from sqlalchemy import Column, Date, DateTime, Float, Integer, String, UniqueConstraint
from sqlalchemy.sql import func

from .db import Base


class Sale(Base):
    __tablename__ = "sales"

    id = Column(Integer, primary_key=True, index=True)
    source = Column(String, nullable=False, index=True)
    row_hash = Column(String, nullable=False, index=True)
    sale_date = Column(Date, nullable=False, index=True)
    week_start = Column(Date, nullable=False, index=True)
    agent_name = Column(String, nullable=False, index=True)
    company_name = Column(String, nullable=False, index=True)
    commission = Column(Float, nullable=False)

    __table_args__ = (UniqueConstraint("source", "row_hash", name="uq_sale_source_rowhash"),)


class WeekClosure(Base):
    __tablename__ = "week_closures"

    id = Column(Integer, primary_key=True, index=True)
    week_start = Column(Date, nullable=False, unique=True, index=True)
    closed_at = Column(DateTime(timezone=True), server_default=func.now(), nullable=False)


class WeekAgentSnapshot(Base):
    __tablename__ = "week_agent_snapshots"

    id = Column(Integer, primary_key=True, index=True)
    week_start = Column(Date, nullable=False, index=True)
    agent_name = Column(String, nullable=False, index=True)
    company_name = Column(String, nullable=False, index=True)
    sales_count = Column(Integer, nullable=False)
    commission_amount = Column(Float, nullable=False)
