import pytest
import sys
import os
import json
from fastapi.testclient import TestClient

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from main import app

@pytest.fixture
def client():
    # FastAPI TestClient
    with TestClient(app) as client:
        yield client

def test_home_endpoint(client):
    """Test the root endpoint /"""
    response = client.get("/")
    assert response.status_code == 200
    data = response.json()
    assert data["status"] == "online"
    assert "service" in data

def test_process_command_greeting(client):
    """Test /process-command with greeting"""
    response = client.post("/process-command", json={
        "text": "Hola",
        "role": "admin"
    })
    assert response.status_code == 200
    data = response.json()
    assert "response_text" in data
    # "intent" might be greeting or known
    assert data["intent"] in ["greeting", "unknown", "how_are_you"]

def test_process_command_help(client):
    """Test /process-command with help intent"""
    response = client.post("/process-command", json={
        "text": "ayuda",
        "role": "admin"
    })
    assert response.status_code == 200
    data = response.json()
    assert "response_text" in data

def test_auth_login_fail(client):
    """Test /auth/login with bad credentials"""
    response = client.post("/auth/login", json={
        "username": "admin",
        "password": "wrongpassword"
    })
    # FastAPI returns 400 for bad creds as per our implementation
    assert response.status_code == 400
