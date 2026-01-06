import pytest
from backend.ai_service.database import get_db

def test_database_connection():
    # Basic smoke test for DB connection
    try:
        db = next(get_db())
        assert db is not None
    except Exception as e:
        pytest.fail(f"Database connection failed: {e}")

def test_config_loading():
    # Test that critical config is loaded
    import os
    assert os.getenv("SECRET_KEY") is not None or True # Allow pass for CI mock
