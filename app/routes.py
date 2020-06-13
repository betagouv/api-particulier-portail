from app import app
from app.db import Api
from flask import request, Response
import requests


def proxy(**kwargs):
    api = kwargs['api']
    r = requests.request(request.method, api.backend,
                         params=request.args)
    headers = dict(r.raw.headers)
    if 'Transfer-Encoding' in headers:
        del headers['Transfer-Encoding']
    if 'Content-Encoding' in headers:
        del headers['Content-Encoding']
    print(headers)

    out = Response(r.text, headers=headers)
    out.status_code = r.status_code
    return out


for api in Api.query.all():
    app.add_url_rule("/{}".format(api.path), api.name, proxy, defaults={
        'api': api
    })
