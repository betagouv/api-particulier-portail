import factory
from app.db import Api, db


class ApiFactory(factory.alchemy.SQLAlchemyModelFactory):
    class Meta:
        model = Api
        sqlalchemy_session = db.session
