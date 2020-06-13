import uuid
from flask_sqlalchemy import SQLAlchemy
from flask_migrate import Migrate
from sqlalchemy.dialects.postgresql import UUID

db = SQLAlchemy()
migrate = Migrate()


class Api(db.Model):
    id = db.Column(UUID(as_uuid=True), primary_key=True,
                   default=uuid.uuid4, unique=True, nullable=False)
    name = db.Column(db.String(120), unique=True, nullable=False)
    backend = db.Column(db.String(80), unique=True, nullable=False)
    path = db.Column(db.String(80), unique=True, nullable=False)
