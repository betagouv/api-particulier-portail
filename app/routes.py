from app import app
from app.db import Api
from flask import request, Response
import requests


@app.route('/')
@app.route('/index')
def index():
    apis = Api.query.all()
    return "Hello, croute!"


def proxy(**kwargs):
    api = kwargs['api']
    r = requests.request(request.method, api.backend,
                         params=request.args, stream=True)
    headers = dict(r.raw.headers)

    def generate():
        for chunk in r.raw.stream(decode_content=False):
            yield chunk
    out = Response(generate(), headers=headers)
    out.status_code = r.status_code
    return out


for api in Api.query.all():
    app.add_url_rule("/{}".format(api.path), api.name, proxy, defaults={
        'api': api
    })
