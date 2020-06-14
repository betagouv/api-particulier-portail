import os
from flask import Flask
from app.settings import ProdConfig
from app.routes import build_routes
from app.db import db, migrate
from app.cache import cache


def create_app(config_object=ProdConfig):
    """An application factory, as explained here:
    http://flask.pocoo.org/docs/patterns/appfactories/.
    :param config_object: The configuration object to use.
    """
    app = Flask(__name__)
    app.url_map.strict_slashes = False
    app.config.from_object(config_object)
    register_extensions(app)
    return app


def register_extensions(app):
    """Register Flask extensions."""
    db.init_app(app)
    migrate.init_app(app, db)
    cache.init_app(app)
    with app.app_context():
        if app.config["MIGRATE_ON_BOOT"]:
            db.create_all()
        build_routes()
