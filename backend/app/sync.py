import csv
import hashlib
import json
from datetime import date, datetime, timedelta
from pathlib import Path

from sqlalchemy.orm import Session

from .models import Sale


def _week_start(dt: date) -> date:
    return dt - timedelta(days=dt.weekday())


def _row_hash(row: dict) -> str:
    normalized = json.dumps(row, sort_keys=True, default=str)
    return hashlib.sha256(normalized.encode("utf-8")).hexdigest()


def sync_sales_from_csv(source: str, db: Session) -> dict:
    summary = {
        "source": source,
        "processed_new_rows": 0,
        "inserted_sales": 0,
        "duplicates_marked": 0,
        "skipped_rows": 0,
        "last_row_synced": 0,
    }

    path = Path(source)
    if not path.exists():
        raise FileNotFoundError(f"CSV source not found: {source}")

    with path.open("r", encoding="utf-8", newline="") as csv_file:
        reader = csv.DictReader(csv_file)
        for row_number, row in enumerate(reader, start=1):
            summary["last_row_synced"] = row_number

            try:
                sale_date = datetime.strptime((row.get("date") or "").strip(), "%Y-%m-%d").date()
                agent_name = (row.get("agentName") or "").strip()
                company_name = (row.get("companyName") or "").strip()
                commission = float(row.get("commission") or 0)
                if not agent_name or not company_name:
                    raise ValueError("agentName/companyName are required")
            except Exception:
                summary["skipped_rows"] += 1
                continue

            row_hash = _row_hash(row)
            duplicate = db.query(Sale).filter(Sale.source == source, Sale.row_hash == row_hash).first()
            if duplicate:
                summary["duplicates_marked"] += 1
                continue

            summary["processed_new_rows"] += 1
            db.add(
                Sale(
                    source=source,
                    row_hash=row_hash,
                    sale_date=sale_date,
                    week_start=_week_start(sale_date),
                    agent_name=agent_name,
                    company_name=company_name,
                    commission=commission,
                )
            )
            summary["inserted_sales"] += 1

    db.commit()
    return summary


def run_csv_sync(source: str, db: Session) -> dict:
    return sync_sales_from_csv(source, db)
