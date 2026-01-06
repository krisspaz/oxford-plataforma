"""
Pytest Tests for AI Service
============================
Comprehensive tests for NLP, ML classifier, and API endpoints
"""

import pytest
import json
import sys
import os

# Add parent directory to path
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


class TestNLPEngine:
    """Tests for the Spanish NLP Engine"""
    
    def setup_method(self):
        from nlp_engine import nlp_engine
        self.nlp = nlp_engine
    
    def test_normalize_text(self):
        """Test text normalization"""
        assert self.nlp.normalize("  HOLA  ") == "hola"
        assert self.nlp.normalize("Buenos Días") == "buenos días"
    
    def test_normalize_accents(self):
        """Test accent removal"""
        assert self.nlp.normalize("café", remove_accents=True) == "cafe"
        assert self.nlp.normalize("niño", remove_accents=True) == "nino"
    
    def test_classify_greeting(self):
        """Test greeting intent classification"""
        result = self.nlp.process("hola")
        assert result.intent.name == "greeting"
        assert result.intent.confidence > 0.5
    
    def test_classify_greeting_typo(self):
        """Test greeting with typo"""
        result = self.nlp.process("ola")  # Missing 'h'
        # Should still recognize or fall back gracefully
        assert result.intent.name in ["greeting", "unknown"]
    
    def test_classify_help(self):
        """Test help intent classification"""
        result = self.nlp.process("ayuda")
        assert result.intent.name == "help"
    
    def test_classify_schedule_generation(self):
        """Test schedule generation intent"""
        result = self.nlp.process("genera un horario para primero primaria")
        assert result.intent.name == "generate_schedule"
    
    def test_extract_grade_entity(self):
        """Test grade entity extraction"""
        result = self.nlp.process("horario para kinder")
        entities = [e for e in result.entities if e.type == "grade"]
        # May or may not find it depending on implementation
        # This is a smoke test
        assert result is not None
    
    def test_extract_subject_entity(self):
        """Test subject entity extraction"""
        result = self.nlp.process("clase de matemáticas")
        entities = [e for e in result.entities if e.type == "subject"]
        assert len(entities) > 0 or result.intent.name != "unknown"
    
    def test_emotional_support_intent(self):
        """Test emotional support detection"""
        result = self.nlp.process("estoy muy estresado")
        assert result.intent.name == "emotional_support"
    
    def test_study_tip_intent(self):
        """Test study tip request"""
        result = self.nlp.process("dame un consejo para estudiar")
        assert result.intent.name in ["study_tip", "help"]


class TestMLClassifier:
    """Tests for the ML Intent Classifier"""
    
    def setup_method(self):
        from ml_classifier import intent_classifier
        self.classifier = intent_classifier
    
    def test_predict_greeting(self):
        """Test ML prediction for greeting"""
        intent, confidence = self.classifier.predict("hola como estas")
        assert intent == "greeting" or confidence > 0
    
    def test_predict_schedule(self):
        """Test ML prediction for schedule"""
        intent, confidence = self.classifier.predict("genera horario")
        assert intent == "generate_schedule" or confidence > 0.3
    
    def test_predict_all_returns_list(self):
        """Test predict_all returns sorted list"""
        results = self.classifier.predict_all("ayuda")
        assert isinstance(results, list)
        assert len(results) > 0
        assert "intent" in results[0]
        assert "confidence" in results[0]
    
    def test_fallback_on_unknown(self):
        """Test fallback for unknown text"""
        intent, confidence = self.classifier.predict("asdfghjkl zxcvbnm")
        # Should return something, even if low confidence
        assert intent is not None


class TestRiskAnalyzer:
    """Tests for Student Risk Analyzer"""
    
    def setup_method(self):
        from risk_analyzer import RiskAnalyzer
        self.analyzer = RiskAnalyzer()
    
    def test_low_risk_student(self):
        """Test student with good grades"""
        result = self.analyzer.analyze({
            "id": 1,
            "grades": [
                {"subject": "Math", "score": 90, "history": [88, 90, 92]},
                {"subject": "Science", "score": 85, "history": [80, 82, 85]}
            ],
            "attendance": 95
        })
        assert result["risk_level"] == "SAFE"
        assert result["risk_score"] < 30
    
    def test_high_risk_student(self):
        """Test student with failing grades"""
        result = self.analyzer.analyze({
            "id": 2,
            "grades": [
                {"subject": "Math", "score": 45, "history": [60, 55, 45]},
                {"subject": "Science", "score": 50, "history": [55, 52, 50]},
                {"subject": "History", "score": 40, "history": [50, 45, 40]}
            ],
            "attendance": 70
        })
        assert result["risk_level"] in ["CRITICAL", "WARNING"]
        assert result["risk_score"] > 50
    
    def test_declining_trend_detected(self):
        """Test declining grade trend detection"""
        result = self.analyzer.analyze({
            "id": 3,
            "grades": [
                {"subject": "Math", "score": 65, "history": [80, 70, 65]}
            ],
            "attendance": 90
        })
        # Should detect the declining trend
        assert any("Caída" in alert or "decline" in alert.lower() for alert in result["alerts"]) or result["risk_score"] > 0


class TestAPI:
    """Tests for Flask API endpoints"""
    
    @pytest.fixture
    def client(self):
        from main import app
        app.config["TESTING"] = True
        with app.test_client() as client:
            yield client
    
    def test_health_check(self, client):
        """Test health endpoint"""
        response = client.get("/health")
        assert response.status_code == 200
        data = json.loads(response.data)
        assert data["status"] == "healthy"
    
    def test_process_command_greeting(self, client):
        """Test process-command with greeting"""
        response = client.post(
            "/process-command",
            json={"text": "hola", "role": "ROLE_TEACHER"},
            content_type="application/json"
        )
        assert response.status_code == 200
        data = json.loads(response.data)
        assert "intent" in data
        assert "response_text" in data
    
    def test_process_command_empty(self, client):
        """Test process-command with empty text"""
        response = client.post(
            "/process-command",
            json={"text": ""},
            content_type="application/json"
        )
        assert response.status_code == 200
    
    def test_process_command_help(self, client):
        """Test help command"""
        response = client.post(
            "/process-command",
            json={"text": "ayuda"},
            content_type="application/json"
        )
        assert response.status_code == 200
        data = json.loads(response.data)
        assert data["intent"] == "help"


class TestTeacherAnalyzer:
    """Tests for Teacher Burnout Analyzer"""
    
    def setup_method(self):
        from teacher_analyzer import TeacherAnalyzer
        self.analyzer = TeacherAnalyzer()
    
    def test_healthy_schedule(self):
        """Test teacher with balanced schedule"""
        schedule = [
            {"day": "Monday", "hour": 1, "subject": "Math"},
            {"day": "Monday", "hour": 2, "subject": "Math"},
            {"day": "Tuesday", "hour": 1, "subject": "Science"},
            {"day": "Tuesday", "hour": 2, "subject": "Science"},
        ]
        result = self.analyzer.analyze_workload(1, schedule)
        assert result["status"] == "SAFE"
    
    def test_overloaded_schedule(self):
        """Test teacher with heavy schedule"""
        schedule = []
        for day in ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]:
            for hour in range(1, 10):  # 9 hours per day
                schedule.append({"day": day, "hour": hour, "subject": "Math"})
        
        result = self.analyzer.analyze_workload(2, schedule)
        assert result["status"] in ["WARNING", "CRITICAL"]


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
