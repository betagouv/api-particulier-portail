import factory
from app.db import Api, db
from gateway.models import ApiKey
from security.hasher import hash_api_key
from datetime import timedelta, datetime


class ApiFactory(factory.alchemy.SQLAlchemyModelFactory):
    class Meta:
        model = Api
        sqlalchemy_session = db.session


class ApiKeyFactory(factory.alchemy.SQLAlchemyModelFactory):
    class Meta:
        model = ApiKey
        sqlalchemy_session = db.session
        exclude = ("key",)

    key = None
    hashed_key = factory.LazyAttribute(lambda o: hash_api_key(o.key))
    active = True
    expires_at = datetime.now() + timedelta(days=365)
