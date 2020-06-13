from app import app
from app.db import Api
from flask import request, Response
import requests


def proxy(url=None, **kwargs):
    api = kwargs['api']
    if url:
        url = "{}/{}".format(api.backend, url)
    else:
        url = api.backend

    r = requests.request(request.method, url, params=request.args)

    headers = dict(r.raw.headers)
    if 'Transfer-Encoding' in headers:
        del headers['Transfer-Encoding']
    if 'Content-Encoding' in headers:
        del headers['Content-Encoding']

    out = Response(r.text, headers=headers)
    out.status_code = r.status_code
    return out


def build_routes():
    for api in Api.query.all():
        app.add_url_rule("/{}/<path:url>".format(api.path), api.name, proxy, defaults={
            'api': api
        })
        app.add_url_rule("/{}/".format(api.path), "{} - Root with slash".format(api.name), proxy, defaults={
            'api': api
        })
        app.add_url_rule("/{}".format(api.path), "{} - Root".format(api.name), proxy, defaults={
            'api': api
        })


try:
    build_routes()
except Exception as err:
    print(err)
