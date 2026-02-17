import csv
import io
import os
from datetime import date

from fastapi import Depends, FastAPI, HTTPException
from fastapi.responses import StreamingResponse
from sqlalchemy import func
from sqlalchemy.orm import Session

from .auth import require_finance_or_manager
from .db import Base, engine, get_db
from .models import Sale, WeekAgentSnapshot, WeekClosure
from .sync import run_csv_sync

Base.metadata.create_all(bind=engine)

app = FastAPI(title="Daily Ops MVP")


def _sources() -> list[str]:
    raw = os.getenv("CSV_SOURCES", "")
    return [s.strip() for s in raw.split(",") if s.strip()]


@app.post("/ops/daily-run", dependencies=[Depends(require_finance_or_manager)])
def daily_run(db: Session = Depends(get_db)):
    results = []
    for source in _sources():
        try:
            results.append(run_csv_sync(source, db))
        except Exception as exc:
            results.append(
                {
                    "source": source,
                    "processed_new_rows": 0,
                    "inserted_sales": 0,
                    "duplicates_marked": 0,
                    "skipped_rows": 0,
                    "last_row_synced": 0,
                    "error": str(exc),
                }
            )
    return {"sources": results}


@app.post("/weeks/{weekStart}/close", dependencies=[Depends(require_finance_or_manager)])
def close_week(weekStart: date, db: Session = Depends(get_db)):
    existing = db.query(WeekClosure).filter(WeekClosure.week_start == weekStart).first()
    if existing:
        raise HTTPException(status_code=409, detail=f"Week {weekStart.isoformat()} is already closed")

    db.add(WeekClosure(week_start=weekStart))

    aggregates = (
        db.query(
            Sale.agent_name,
            Sale.company_name,
            func.count(Sale.id).label("sales_count"),
            func.coalesce(func.sum(Sale.commission), 0.0).label("commission_amount"),
        )
        .filter(Sale.week_start == weekStart)
        .group_by(Sale.agent_name, Sale.company_name)
        .all()
    )

    db.query(WeekAgentSnapshot).filter(WeekAgentSnapshot.week_start == weekStart).delete()
    for row in aggregates:
        db.add(
            WeekAgentSnapshot(
                week_start=weekStart,
                agent_name=row.agent_name,
                company_name=row.company_name,
                sales_count=row.sales_count,
                commission_amount=float(row.commission_amount or 0),
            )
        )

    db.commit()
    return {"weekStart": weekStart.isoformat(), "closed": True, "snapshots": len(aggregates)}


@app.get("/weeks/{weekStart}/export", dependencies=[Depends(require_finance_or_manager)])
def export_week(weekStart: date, db: Session = Depends(get_db)):
    snapshots = (
        db.query(WeekAgentSnapshot)
        .filter(WeekAgentSnapshot.week_start == weekStart)
        .order_by(WeekAgentSnapshot.agent_name.asc())
        .all()
    )

    output = io.StringIO()
    writer = csv.writer(output)
    writer.writerow(["weekStart", "agentName", "companyName", "salesCount", "commission"])

    for snapshot in snapshots:
        writer.writerow(
            [
                snapshot.week_start.isoformat(),
                snapshot.agent_name,
                snapshot.company_name,
                snapshot.sales_count,
                snapshot.commission_amount,
            ]
        )

    output.seek(0)
    return StreamingResponse(
        iter([output.getvalue()]),
        media_type="text/csv",
        headers={"Content-Disposition": f'attachment; filename="week-{weekStart.isoformat()}.csv"'},
    )
