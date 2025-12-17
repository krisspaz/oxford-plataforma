"""
pytest configuration and fixtures for AI Service tests
"""

import pytest
import sys
import os

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


@pytest.fixture
def sample_schedule():
    """Sample valid schedule for testing"""
    return [
        {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1, "classroom": "101"},
        {"day": 1, "period": 2, "teacherId": 2, "subjectId": 2, "gradeId": 1, "sectionId": 1, "classroom": "102"},
        {"day": 1, "period": 3, "teacherId": 3, "subjectId": 3, "gradeId": 1, "sectionId": 1, "classroom": "103"},
        {"day": 2, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1, "classroom": "101"},
        {"day": 2, "period": 2, "teacherId": 2, "subjectId": 2, "gradeId": 1, "sectionId": 1, "classroom": "102"},
    ]


@pytest.fixture
def sample_teachers():
    """Sample teachers for testing"""
    return [
        {"id": 1, "name": "Prof. García", "subjects": [1, 4]},
        {"id": 2, "name": "Prof. López", "subjects": [2]},
        {"id": 3, "name": "Prof. Martínez", "subjects": [3, 5]},
        {"id": 4, "name": "Prof. Rodríguez", "subjects": [6, 7]},
        {"id": 5, "name": "Prof. Hernández", "subjects": [1, 2]},
    ]


@pytest.fixture
def sample_subjects():
    """Sample subjects for testing"""
    return [
        {"id": 1, "name": "Matemáticas", "hoursPerWeek": 5},
        {"id": 2, "name": "Español", "hoursPerWeek": 5},
        {"id": 3, "name": "Ciencias", "hoursPerWeek": 3},
        {"id": 4, "name": "Inglés", "hoursPerWeek": 4},
        {"id": 5, "name": "Historia", "hoursPerWeek": 2},
        {"id": 6, "name": "Ed. Física", "hoursPerWeek": 2},
        {"id": 7, "name": "Arte", "hoursPerWeek": 2},
    ]


@pytest.fixture
def sample_config():
    """Sample schedule configuration"""
    return {
        "startTime": "07:30",
        "endTime": "14:00",
        "classDuration": 45,
        "includeRecess": True,
        "recessAfterPeriod": 4,
        "recessDuration": 30,
        "gradeId": 1,
        "sectionId": 1,
    }


@pytest.fixture
def nlp_test_cases():
    """Common NLP test cases"""
    return {
        "greetings": [
            "hola",
            "buenos días",
            "buenas tardes",
            "hey",
            "saludos",
        ],
        "help_requests": [
            "ayuda",
            "qué puedo hacer",
            "comandos",
            "opciones",
        ],
        "schedule_generation": [
            "genera horario para 1ro primaria",
            "crea horario de segundo básico",
            "hazme un horario para kinder",
            "necesito horario para 3ro primaria sección A",
        ],
        "time_settings": [
            "horario de 7:30 a 14:00",
            "clases de 8 a 2",
            "empieza a las 7",
        ],
        "duration_settings": [
            "clases de 45 minutos",
            "periodos de 1 hora",
            "duración de 50 min",
        ],
        "teacher_constraints": [
            "el profesor García no puede los lunes",
            "prof López solo en las mañanas",
            "maestra Rodríguez no disponible viernes",
        ],
    }


# Marks for categorizing tests
def pytest_configure(config):
    """Configure custom markers"""
    config.addinivalue_line("markers", "slow: marks tests as slow")
    config.addinivalue_line("markers", "integration: marks integration tests")
    config.addinivalue_line("markers", "unit: marks unit tests")
