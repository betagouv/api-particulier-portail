from app.routes import build_routes
from app.db import db
from sqlalchemy import event
from app.cache import cache
from gateway.api_key_repository import get_active_by_hashed_key


def test_two_calls(test_api, client, test_api_key_value):
    build_routes()
    cache.delete_memoized(get_active_by_hashed_key)
    query_count = 0

    def catch_queries(*args):
        nonlocal query_count
        query_count += 1

    event.listen(db.engine, "before_cursor_execute", catch_queries)

    response = client.get(
        "/{}/pokemon/ditto".format(test_api.path),
        headers={"X-Api-Key": test_api_key_value},
    )
    assert response.status_code == 200
    assert query_count == 1
    client.get(
        "/{}/pokemon/pikachu".format(test_api.path),
        headers={"X-Api-Key": test_api_key_value},
    )
    assert query_count == 1
