import uuid
from app.db import db
from sqlalchemy.dialects.postgresql import UUID


class ApiKey(db.Model):
    id = db.Column(
        UUID(as_uuid=True),
        primary_key=True,
        default=uuid.uuid4,
        unique=True,
        nullable=False,
    )
    active = db.Column(db.Boolean, nullable=False)
    expires_at = db.Column(db.DateTime(timezone=True), nullable=False)
    hashed_key = db.Column(db.String(120), nullable=False, unique=True)
