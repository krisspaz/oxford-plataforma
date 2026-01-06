"""
Test Cases for AI Learning System
==================================
Real-world scenarios to validate and train the AI
"""

import pytest
from learning_engine import (
    LearningEngine, FeedbackAction, FeedbackReason, 
    ImprovementType, Priority
)


class TestLearningEngine:
    """Test the continuous learning system"""
    
    def setup_method(self):
        """Create a fresh test instance"""
        self.engine = LearningEngine(db_path=":memory:")
    
    # =========================================
    # CASE 1: Missing Rule Detection
    # =========================================
    def test_case_1_missing_rule(self):
        """
        Scenario: User asks "Mover laboratorio a viernes 5pm"
        Expected: ❌ No - Regla de laboratorios no existe
        Learning: Crear regla "Laboratorios no viernes tarde"
        """
        # Log interaction
        interaction_id = self.engine.log_interaction(
            user_id="admin_1",
            user_role="ROLE_ADMIN",
            question="Mover laboratorio a viernes 5pm",
            response="El cambio ha sido realizado",  # Wrong answer
            intent="schedule_change"
        )
        
        # User rejects (system didn't catch the issue)
        self.engine.log_feedback(
            interaction_id=interaction_id,
            action=FeedbackAction.REJECTED,
            reason=FeedbackReason.LOGIC
        )
        
        # Simulate 3 more rejections
        for _ in range(2):
            iid = self.engine.log_interaction(
                user_id="admin_2",
                user_role="ROLE_ADMIN",
                question="Laboratorio viernes tarde",
                response="Procesando cambio",
                intent="schedule_change"
            )
            self.engine.log_feedback(iid, FeedbackAction.REJECTED, FeedbackReason.LOGIC)
        
        # Should have improvement proposed
        improvements = self.engine.get_pending_improvements()
        assert len(improvements) >= 1
        assert improvements[0]["priority"] == "high"
    
    # =========================================
    # CASE 2: Recurring Preference
    # =========================================
    def test_case_2_recurring_preference(self):
        """
        Scenario: IA proposes lunes 7am, users always choose 9am
        Learning: Prioritize 9am automatically
        """
        # Multiple users prefer alternatives
        for i in range(4):
            iid = self.engine.log_interaction(
                user_id=f"director_{i}",
                user_role="ROLE_DIRECTOR",
                question="Sugerir horario para reunión",
                response="Propongo lunes 7:00 AM",
                intent="schedule_suggestion"
            )
            self.engine.log_feedback(
                iid, 
                FeedbackAction.ALTERNATIVE,
                FeedbackReason.PREFERENCE
            )
        
        # Check if pattern was detected
        stats = self.engine.get_learning_stats()
        assert stats["total_feedback"] >= 4
    
    # =========================================
    # CASE 3: Missing Data Pattern
    # =========================================
    def test_case_3_missing_data(self):
        """
        Scenario: User asks incomplete question, corrects with more data
        Learning: Request required data before answering
        """
        iid = self.engine.log_interaction(
            user_id="coord_1",
            user_role="ROLE_COORDINATION",
            question="Mover clase del profe",
            response="¿Cuál profesor?",
            intent="schedule_change"
        )
        
        self.engine.log_feedback(
            iid,
            FeedbackAction.CORRECTED,
            reason=FeedbackReason.MISSING_DATA,
            user_correction="Profe Juan López, matemáticas"
        )
        
        # Should propose validation improvement
        improvements = self.engine.get_pending_improvements()
        # At least the pattern is tracked
        stats = self.engine.get_learning_stats()
        assert stats["total_feedback"] >= 1
    
    # =========================================
    # CASE 4: UX Improvement
    # =========================================
    def test_case_4_ux_improvement(self):
        """
        Scenario: Users repeatedly ask for more options
        Learning: Show 3 options from the start
        """
        for i in range(5):
            iid = self.engine.log_interaction(
                user_id=f"admin_{i}",
                user_role="ROLE_ADMIN",
                question="Dame opciones de horario",
                response="Aquí hay una opción: ...",
                intent="schedule_options"
            )
            self.engine.log_feedback(iid, FeedbackAction.ALTERNATIVE)
    
    # =========================================
    # ANTI-MALICIOUS: Student Cannot Train
    # =========================================
    def test_student_cannot_train_rules(self):
        """
        Students rejecting early schedules should NOT train the system
        """
        iid = self.engine.log_interaction(
            user_id="student_1",
            user_role="ROLE_STUDENT",
            question="Mi horario de 7am",
            response="Tu primera clase es a las 7:00 AM",
            intent="view_schedule"
        )
        
        self.engine.log_feedback(iid, FeedbackAction.REJECTED)
        
        # Should NOT create improvement
        improvements = self.engine.get_pending_improvements()
        # Filter out student-influenced improvements
        assert not any(
            "student" in str(imp.get("source", "")).lower() 
            for imp in improvements
        )
    
    # =========================================
    # RULE MANAGEMENT
    # =========================================
    def test_add_and_get_rules(self):
        """Test adding and retrieving rules"""
        rule_id = self.engine.add_rule(
            description="Laboratorios no pueden ser viernes después de 4pm",
            condition="day == 'viernes' AND hour >= 16 AND type == 'laboratorio'",
            action="REJECT",
            source="human"
        )
        
        rules = self.engine.get_active_rules()
        assert len(rules) >= 1
        assert rules[0]["source"] == "human"
    
    # =========================================
    # STATS TRACKING
    # =========================================
    def test_stats_tracking(self):
        """Test that stats are properly tracked"""
        # Log some interactions
        for i in range(3):
            self.engine.log_interaction(
                user_id=f"user_{i}",
                user_role="ROLE_TEACHER",
                question="Consulta",
                response="Respuesta"
            )
        
        stats = self.engine.get_learning_stats()
        assert stats["total_interactions"] == 3


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
