"""
AI Continuous Learning Engine
==============================
Self-improvement system - learns from every interaction
"""

import json
from datetime import datetime, timedelta
from typing import List, Dict, Optional, Any
from dataclasses import dataclass, asdict
from enum import Enum
import logging
from database import db

logger = logging.getLogger(__name__)


class FeedbackAction(Enum):
    ACCEPTED = "accepted"
    REJECTED = "rejected"
    ALTERNATIVE = "alternative"
    CORRECTED = "corrected"


class FeedbackReason(Enum):
    LOGIC = "logic"
    MISSING_DATA = "missing_data"
    PREFERENCE = "preference"
    UNCLEAR = "unclear"


class ImprovementType(Enum):
    RULE = "rule"
    PRIORITY = "priority"
    UX = "ux"
    VALIDATION = "validation"


class Priority(Enum):
    HIGH = "high"
    MEDIUM = "medium"
    LOW = "low"


@dataclass
class Improvement:
    """Proposed system improvement"""
    source_interaction_id: int
    problem_detected: str
    proposed_solution: str
    improvement_type: ImprovementType
    priority: Priority
    confidence: float  # 0.0 to 1.0
    requires_approval: bool
    status: str = "pending"  # pending, approved, applied, rejected
    approved_by: Optional[str] = None
    applied_at: Optional[datetime] = None


class LearningEngine:
    """
    Continuous learning system that improves from user behavior
    
    RUNS AFTER EACH INTERACTION - NOT VISIBLE TO USER
    USES MySQL VIA database.py
    """
    
    # Minimum patterns needed before learning
    MIN_PATTERN_COUNT = 3
    
    # Roles that can influence learning
    LEARNING_PERMISSIONS = {
        "ROLE_SUPER_ADMIN": {"rules": True, "priority": True, "ux": True, "validation": True},
        "ROLE_ADMIN": {"rules": True, "priority": True, "ux": True, "validation": True},
        "ROLE_DIRECTOR": {"rules": True, "priority": True, "ux": False, "validation": False},
        "ROLE_COORDINATION": {"rules": False, "priority": True, "ux": False, "validation": False},
        "ROLE_TEACHER": {"rules": False, "priority": False, "ux": False, "validation": False},
        "ROLE_DOCENTE": {"rules": False, "priority": False, "ux": False, "validation": False},
        "ROLE_STUDENT": {"rules": False, "priority": False, "ux": False, "validation": False},
    }
    
    def __init__(self):
        # Database initialization is handled centrally in main / app startup
        pass
    
    # =========================================
    # LOGGING INTERACTIONS
    # =========================================
    
    def log_interaction(self, user_id: str, user_role: str, question: str, 
                       response: str, intent: str = None, context: Dict = None, metadata: Dict = None) -> int:
        """Log every AI interaction"""
        
        # Merge metadata into context if present
        if metadata:
            context = context or {}
            context.update(metadata)
        
        sql = '''
            INSERT INTO ai_interactions 
            (user_id, user_role, question, response, intent, context_snapshot)
            VALUES (%s, %s, %s, %s, %s, %s)
        '''
        params = (user_id, user_role, question, response, intent, 
              json.dumps(context) if context else None)
        
        interaction_id = db.execute(sql, params)
        return interaction_id
    
    def log_feedback(self, interaction_id: int, action: FeedbackAction, 
                    reason: FeedbackReason = None, user_correction: str = None) -> None:
        """Log user feedback (implicit or explicit)"""
        sql = '''
            INSERT INTO ai_feedback 
            (interaction_id, action, reason, user_correction)
            VALUES (%s, %s, %s, %s)
        '''
        params = (interaction_id, action.value, 
              reason.value if reason else None, user_correction)
        
        db.execute(sql, params)
        
        # Trigger learning analysis
        self._analyze_feedback(interaction_id, action, reason)
    
    # =========================================
    # LEARNING ANALYSIS
    # =========================================
    
    def _analyze_feedback(self, interaction_id: int, action: FeedbackAction, 
                         reason: FeedbackReason = None) -> Optional[Improvement]:
        """
        Analyze feedback and propose improvements
        """
        # Get interaction details
        interaction = db.query('SELECT * FROM ai_interactions WHERE id = %s', (interaction_id,), one=True)
        
        if not interaction:
            return None
        
        # Depending on dict cursor or tuple 
        # database.py uses dictionary=True so we allow that
        user_role = interaction.get('user_role')
        intent = interaction.get('intent')
        
        # Check if user can influence learning
        if not self._can_influence_learning(user_role, action):
            return None
        
        # Track pattern
        pattern_key = f"{intent}:{action.value}"
        self._track_pattern("feedback", pattern_key)
        
        # Check if pattern is significant
        pattern_count = self._get_pattern_count("feedback", pattern_key)
        
        if action == FeedbackAction.REJECTED and pattern_count >= self.MIN_PATTERN_COUNT:
            # Significant rejection pattern detected
            improvement = Improvement(
                source_interaction_id=interaction_id,
                problem_detected=f"Respuesta para intent '{intent}' rechazada {pattern_count} veces",
                proposed_solution=f"Revisar y mejorar respuesta para: {intent}",
                improvement_type=ImprovementType.RULE,
                priority=Priority.HIGH,
                confidence=min(pattern_count * 0.2, 0.9),
                requires_approval=True
            )
            self._save_improvement(improvement)
            return improvement
        
        if action == FeedbackAction.ALTERNATIVE and pattern_count >= self.MIN_PATTERN_COUNT:
            # Users consistently prefer alternatives
            improvement = Improvement(
                source_interaction_id=interaction_id,
                problem_detected=f"Usuarios prefieren alternativa para: {intent}",
                proposed_solution="Priorizar alternativas en respuestas futuras",
                improvement_type=ImprovementType.PRIORITY,
                priority=Priority.MEDIUM,
                confidence=min(pattern_count * 0.15, 0.8),
                requires_approval=False
            )
            self._save_improvement(improvement)
            return improvement
        
        if action == FeedbackAction.CORRECTED and reason == FeedbackReason.MISSING_DATA:
            # Missing data pattern
            improvement = Improvement(
                source_interaction_id=interaction_id,
                problem_detected="Falta de datos para responder completamente",
                proposed_solution=f"Solicitar datos adicionales antes de responder sobre: {intent}",
                improvement_type=ImprovementType.VALIDATION,
                priority=Priority.MEDIUM,
                confidence=0.6,
                requires_approval=True
            )
            self._save_improvement(improvement)
            return improvement
        
        return None
    
    def _can_influence_learning(self, user_role: str, action: FeedbackAction) -> bool:
        """
        Anti-malicious training protection
        """
        permissions = self.LEARNING_PERMISSIONS.get(user_role, {})
        
        if not permissions:
            return False
        
        # Rejections from students don't train
        if user_role in ["ROLE_STUDENT", "ROLE_ALUMNO"]: # Adjusted to localized role
            return False
        
        # Teachers can only train preferences (alternatives)
        if user_role in ["ROLE_TEACHER", "ROLE_DOCENTE"]:
            return action == FeedbackAction.ALTERNATIVE
        
        return True
    
    def _track_pattern(self, pattern_type: str, pattern_key: str) -> None:
        """Track occurrence of a pattern"""
        # MySQL ON DUPLICATE KEY UPDATE syntax
        sql = '''
            INSERT INTO ai_patterns (pattern_type, pattern_key, occurrence_count, last_seen)
            VALUES (%s, %s, 1, NOW())
            ON DUPLICATE KEY UPDATE occurrence_count = occurrence_count + 1, last_seen = NOW()
        '''
        db.execute(sql, (pattern_type, pattern_key))
    
    def _get_pattern_count(self, pattern_type: str, pattern_key: str) -> int:
        """Get how many times a pattern has occurred"""
        result = db.query('''
            SELECT occurrence_count FROM ai_patterns
            WHERE pattern_type = %s AND pattern_key = %s
        ''', (pattern_type, pattern_key), one=True)
        
        return result['occurrence_count'] if result else 0
    
    def _save_improvement(self, improvement: Improvement) -> int:
        """Save a proposed improvement"""
        sql = '''
            INSERT INTO ai_improvements 
            (source_interaction_id, problem_detected, proposed_solution,
             improvement_type, priority, confidence, requires_approval, status)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        '''
        params = (improvement.source_interaction_id, improvement.problem_detected,
              improvement.proposed_solution, improvement.improvement_type.value,
              improvement.priority.value, improvement.confidence,
              1 if improvement.requires_approval else 0, improvement.status)
        
        improvement_id = db.execute(sql, params)
        
        logger.info(f"Improvement proposed: {improvement.proposed_solution}")
        return improvement_id
    
    # =========================================
    # RULES MANAGEMENT
    # =========================================
    
    def add_rule(self, description: str, condition: str = None, 
                action: str = None, source: str = "human") -> int:
        """Add a new rule to the system"""
        sql = '''
            INSERT INTO ai_rules 
            (rule_description, rule_condition, rule_action, source)
            VALUES (%s, %s, %s, %s)
        '''
        return db.execute(sql, (description, condition, action, source))
    
    def get_active_rules(self) -> List[Dict]:
        """Get all active rules"""
        rows = db.query('SELECT * FROM ai_rules WHERE active = 1')
        
        return [{
            "id": r['id'],
            "description": r['rule_description'],
            "condition": r['rule_condition'],
            "action": r['rule_action'],
            "source": r['source'],
            "priority": r['priority']
        } for r in rows] # Assumes rows is list of dicts
    
    # =========================================
    # ADMIN FUNCTIONS
    # =========================================
    
    def get_pending_improvements(self) -> List[Dict]:
        """Get improvements waiting for approval"""
        rows = db.query('''
            SELECT * FROM ai_improvements 
            WHERE status = 'pending' AND requires_approval = 1
            ORDER BY priority DESC, created_at DESC
        ''')
        
        return [{
            "id": r['id'],
            "problem": r['problem_detected'],
            "solution": r['proposed_solution'],
            "type": r['improvement_type'],
            "priority": r['priority'],
            "confidence": r['confidence'],
            "created_at": r['created_at']
        } for r in rows]
    
    def approve_improvement(self, improvement_id: int, approved_by: str) -> bool:
        """Approve an improvement for application"""
        db.execute('''
            UPDATE ai_improvements 
            SET status = 'approved', approved_by = %s
            WHERE id = %s
        ''', (approved_by, improvement_id))
        
        logger.info(f"Improvement {improvement_id} approved by {approved_by}")
        return True
    
    def get_learning_stats(self) -> Dict:
        """Get statistics about learning"""
        stats = {}
        
        res = db.query('SELECT COUNT(*) as c FROM ai_interactions', one=True)
        stats["total_interactions"] = res['c'] if res else 0
        
        res = db.query('SELECT COUNT(*) as c FROM ai_feedback', one=True)
        stats["total_feedback"] = res['c'] if res else 0
        
        res = db.query('SELECT COUNT(*) as c FROM ai_improvements WHERE status = "pending"', one=True)
        stats["pending_improvements"] = res['c'] if res else 0
        
        res = db.query('SELECT COUNT(*) as c FROM ai_improvements WHERE status = "applied"', one=True)
        stats["applied_improvements"] = res['c'] if res else 0
        
        res = db.query('SELECT COUNT(*) as c FROM ai_rules WHERE active = 1', one=True)
        stats["active_rules"] = res['c'] if res else 0
        
        return stats


# Global instance
learning_engine = LearningEngine()
