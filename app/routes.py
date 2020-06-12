from app import app
from app.db import Api


@app.route('/')
@app.route('/index')
def index():
    apis = Api.query.all()
    return "Hello, croute!"
