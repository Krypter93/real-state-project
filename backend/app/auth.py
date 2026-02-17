from fastapi import Header, HTTPException

ALLOWED_ROLES = {"finance", "manager"}


def require_finance_or_manager(x_role: str = Header(default="", alias="X-Role")):
    if x_role not in ALLOWED_ROLES:
        raise HTTPException(status_code=403, detail="Only finance/manager can access this endpoint")
    return x_role
