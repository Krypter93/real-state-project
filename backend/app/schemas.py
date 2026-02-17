from pydantic import BaseModel


class SourceSyncSummary(BaseModel):
    source: str
    processed_new_rows: int = 0
    inserted_sales: int = 0
    duplicates_marked: int = 0
    skipped_rows: int = 0
    last_row_synced: int = 0
    error: str | None = None
