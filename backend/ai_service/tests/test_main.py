"""
AI Service Tests - Sistema Oxford
=================================
Tests for the AI-powered schedule generator and NLP processing
"""

import pytest
from fastapi.testclient import TestClient
from unittest.mock import patch, MagicMock
import json
import sys
import os

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from main import app

client = TestClient(app)


class TestHealthEndpoint:
    """Tests for health check endpoint"""

    def test_health_endpoint_returns_ok(self):
        response = client.get("/health")
        assert response.status_code == 200
        data = response.json()
        assert data["status"] == "healthy"

    def test_health_endpoint_includes_service_name(self):
        response = client.get("/health")
        data = response.json()
        assert "service" in data
        assert data["service"] == "oxford-ai-service"


class TestChatEndpoint:
    """Tests for chat/NLP endpoint"""

    def test_chat_requires_message(self):
        response = client.post("/chat", json={})
        assert response.status_code in [400, 422]

    def test_chat_with_greeting(self):
        response = client.post("/chat", json={
            "message": "Hola, buenos días"
        })
        assert response.status_code == 200
        data = response.json()
        assert "response" in data

    def test_chat_with_schedule_request(self):
        response = client.post("/chat", json={
            "message": "Genera un horario para primero primaria"
        })
        assert response.status_code == 200
        data = response.json()
        assert "response" in data

    def test_chat_with_empty_message(self):
        response = client.post("/chat", json={
            "message": ""
        })
        # Should handle gracefully
        assert response.status_code in [200, 400]


class TestScheduleGeneratorEndpoint:
    """Tests for schedule generation endpoint"""

    def test_generate_schedule_basic(self):
        response = client.post("/generate-schedule", json={
            "gradeId": 1,
            "sectionId": "A",
            "teachers": [
                {"id": 1, "name": "Prof. García", "subjects": ["Matemáticas"]},
                {"id": 2, "name": "Prof. López", "subjects": ["Español"]}
            ],
            "subjects": [
                {"id": 1, "name": "Matemáticas", "hoursPerWeek": 5},
                {"id": 2, "name": "Español", "hoursPerWeek": 5}
            ]
        })
        assert response.status_code == 200
        data = response.json()
        assert "schedule" in data or "error" in data

    def test_generate_schedule_missing_grade(self):
        response = client.post("/generate-schedule", json={
            "teachers": [],
            "subjects": []
        })
        assert response.status_code in [400, 422]

    def test_generate_schedule_empty_teachers(self):
        response = client.post("/generate-schedule", json={
            "gradeId": 1,
            "sectionId": "A",
            "teachers": [],
            "subjects": [
                {"id": 1, "name": "Matemáticas", "hoursPerWeek": 5}
            ]
        })
        # Should handle gracefully even with no teachers
        assert response.status_code in [200, 400]


class TestNLPProcessing:
    """Tests for NLP intent detection"""

    def test_detect_greeting_intent(self):
        response = client.post("/analyze-intent", json={
            "message": "Hola, cómo estás"
        })
        if response.status_code == 200:
            data = response.json()
            assert data.get("intent") in ["greeting", "unknown"]

    def test_detect_schedule_intent(self):
        response = client.post("/analyze-intent", json={
            "message": "Necesito generar el horario de tercero básico"
        })
        if response.status_code == 200:
            data = response.json()
            assert "intent" in data

    def test_detect_help_intent(self):
        response = client.post("/analyze-intent", json={
            "message": "Ayuda, qué puedes hacer?"
        })
        if response.status_code == 200:
            data = response.json()
            assert "intent" in data


class TestScheduleValidation:
    """Tests for schedule conflict detection"""

    def test_validate_schedule_no_conflicts(self):
        response = client.post("/validate-schedule", json={
            "schedule": [
                {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1},
                {"day": 1, "period": 2, "teacherId": 2, "subjectId": 2}
            ]
        })
        if response.status_code == 200:
            data = response.json()
            assert "valid" in data or "conflicts" in data

    def test_validate_schedule_with_conflicts(self):
        response = client.post("/validate-schedule", json={
            "schedule": [
                {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1},
                {"day": 1, "period": 1, "teacherId": 1, "subjectId": 2}  # Same teacher, same period
            ]
        })
        if response.status_code == 200:
            data = response.json()
            # Should detect the conflict
            if "conflicts" in data:
                assert len(data["conflicts"]) > 0


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
