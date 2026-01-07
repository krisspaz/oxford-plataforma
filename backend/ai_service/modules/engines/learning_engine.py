"""
AI Continuous Learning Engine
==============================
Self-improvement system - learns from every interaction
"""

import sqlite3
import json
from datetime import datetime, timedelta
from typing import List, Dict, Optional, Any
from dataclasses import dataclass, asdict
from enum import Enum
import logging

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


class LearningEngine:
    """
    Continuous learning system that improves from user behavior
    
    RUNS AFTER EACH INTERACTION - NOT VISIBLE TO USER
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
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_tables()
    
    def _init_tables(self):
        """Create learning tables"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        # 1. ai_interactions - Every usage
        c.execute('''
            CREATE TABLE IF NOT EXISTS ai_interactions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT NOT NULL,
                user_role TEXT NOT NULL,
                question TEXT NOT NULL,
                response TEXT NOT NULL,
                intent TEXT,
                context_snapshot TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # 2. ai_feedback - Implicit training
        c.execute('''
            CREATE TABLE IF NOT EXISTS ai_feedback (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                interaction_id INTEGER NOT NULL,
                action TEXT NOT NULL,
                reason TEXT,
                user_correction TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (interaction_id) REFERENCES ai_interactions(id)
            )
        ''')
        
        # 3. ai_improvements - Proposed learnings
        c.execute('''
            CREATE TABLE IF NOT EXISTS ai_improvements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                source_interaction_id INTEGER,
                problem_detected TEXT NOT NULL,
                proposed_solution TEXT NOT NULL,
                improvement_type TEXT NOT NULL,
                priority TEXT NOT NULL,
                confidence REAL,
                requires_approval INTEGER DEFAULT 1,
                status TEXT DEFAULT 'pending',
                approved_by TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                applied_at DATETIME
            )
        ''')
        
        # 4. ai_rules - Live rules (human + AI learned)
        c.execute('''
            CREATE TABLE IF NOT EXISTS ai_rules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                rule_description TEXT NOT NULL,
                rule_condition TEXT,
                rule_action TEXT,
                source TEXT NOT NULL,
                priority TEXT DEFAULT 'medium',
                active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # 5. ai_patterns - Detected patterns
        c.execute('''
            CREATE TABLE IF NOT EXISTS ai_patterns (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                pattern_type TEXT NOT NULL,
                pattern_key TEXT NOT NULL,
                occurrence_count INTEGER DEFAULT 1,
                last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(pattern_type, pattern_key)
            )
        ''')
        
        conn.commit()
        conn.close()
        logger.info("Learning tables initialized")
    
    # =========================================
    # LOGGING INTERACTIONS
    # =========================================
    
    def log_interaction(self, user_id: str, user_role: str, question: str, 
                       response: str, intent: str = None, context: Dict = None) -> int:
        """Log every AI interaction"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO ai_interactions 
            (user_id, user_role, question, response, intent, context_snapshot)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (user_id, user_role, question, response, intent, 
              json.dumps(context) if context else None))
        
        interaction_id = c.lastrowid
        conn.commit()
        conn.close()
        
        return interaction_id
    
    def log_feedback(self, interaction_id: int, action: FeedbackAction, 
                    reason: FeedbackReason = None, user_correction: str = None) -> None:
        """Log user feedback (implicit or explicit)"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO ai_feedback 
            (interaction_id, action, reason, user_correction)
            VALUES (?, ?, ?, ?)
        ''', (interaction_id, action.value, 
              reason.value if reason else None, user_correction))
        
        conn.commit()
        conn.close()
        
        # Trigger learning analysis
        self._analyze_feedback(interaction_id, action, reason)
    
    # =========================================
    # LEARNING ANALYSIS
    # =========================================
    
    def _analyze_feedback(self, interaction_id: int, action: FeedbackAction, 
                         reason: FeedbackReason = None) -> Optional[Improvement]:
        """
        Analyze feedback and propose improvements
        
        This is the CORE learning function
        """
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        # Get interaction details
        c.execute('SELECT * FROM ai_interactions WHERE id = ?', (interaction_id,))
        interaction = c.fetchone()
        
        if not interaction:
            conn.close()
            return None
        
        user_role = interaction[2]  # user_role column
        intent = interaction[5]     # intent column
        
        # Check if user can influence learning
        if not self._can_influence_learning(user_role, action):
            conn.close()
            logger.debug(f"User role {user_role} cannot influence learning")
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
            conn.close()
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
            conn.close()
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
            conn.close()
            return improvement
        
        conn.close()
        return None
    
    def _can_influence_learning(self, user_role: str, action: FeedbackAction) -> bool:
        """
        Anti-malicious training protection
        
        - Students cannot train rules
        - Teachers can only influence preferences
        - Admins validate structural changes
        """
        permissions = self.LEARNING_PERMISSIONS.get(user_role, {})
        
        if not permissions:
            return False
        
        # Rejections from students don't train
        if user_role in ["ROLE_STUDENT", "ROLE_ESTUDIANTE"]:
            return False
        
        # Teachers can only train preferences (alternatives)
        if user_role in ["ROLE_TEACHER", "ROLE_DOCENTE"]:
            return action == FeedbackAction.ALTERNATIVE
        
        return True
    
    def _track_pattern(self, pattern_type: str, pattern_key: str) -> None:
        """Track occurrence of a pattern"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO ai_patterns (pattern_type, pattern_key, occurrence_count, last_seen)
            VALUES (?, ?, 1, ?)
            ON CONFLICT(pattern_type, pattern_key) 
            DO UPDATE SET occurrence_count = occurrence_count + 1, last_seen = ?
        ''', (pattern_type, pattern_key, datetime.now(), datetime.now()))
        
        conn.commit()
        conn.close()
    
    def _get_pattern_count(self, pattern_type: str, pattern_key: str) -> int:
        """Get how many times a pattern has occurred"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT occurrence_count FROM ai_patterns
            WHERE pattern_type = ? AND pattern_key = ?
        ''', (pattern_type, pattern_key))
        
        result = c.fetchone()
        conn.close()
        
        return result[0] if result else 0
    
    def _save_improvement(self, improvement: Improvement) -> int:
        """Save a proposed improvement"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO ai_improvements 
            (source_interaction_id, problem_detected, proposed_solution,
             improvement_type, priority, confidence, requires_approval, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (improvement.source_interaction_id, improvement.problem_detected,
              improvement.proposed_solution, improvement.improvement_type.value,
              improvement.priority.value, improvement.confidence,
              1 if improvement.requires_approval else 0, improvement.status))
        
        improvement_id = c.lastrowid
        conn.commit()
        conn.close()
        
        logger.info(f"Improvement proposed: {improvement.proposed_solution}")
        return improvement_id
    
    # =========================================
    # RULES MANAGEMENT
    # =========================================
    
    def add_rule(self, description: str, condition: str = None, 
                action: str = None, source: str = "human") -> int:
        """Add a new rule to the system"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO ai_rules 
            (rule_description, rule_condition, rule_action, source)
            VALUES (?, ?, ?, ?)
        ''', (description, condition, action, source))
        
        rule_id = c.lastrowid
        conn.commit()
        conn.close()
        
        return rule_id
    
    def get_active_rules(self) -> List[Dict]:
        """Get all active rules"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('SELECT * FROM ai_rules WHERE active = 1')
        rows = c.fetchall()
        conn.close()
        
        return [{
            "id": r[0],
            "description": r[1],
            "condition": r[2],
            "action": r[3],
            "source": r[4],
            "priority": r[5]
        } for r in rows]
    
    # =========================================
    # ADMIN FUNCTIONS
    # =========================================
    
    def get_pending_improvements(self) -> List[Dict]:
        """Get improvements waiting for approval"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT * FROM ai_improvements 
            WHERE status = 'pending' AND requires_approval = 1
            ORDER BY priority DESC, created_at DESC
        ''')
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "id": r[0],
            "problem": r[2],
            "solution": r[3],
            "type": r[4],
            "priority": r[5],
            "confidence": r[6],
            "created_at": r[9]
        } for r in rows]
    
    def approve_improvement(self, improvement_id: int, approved_by: str) -> bool:
        """Approve an improvement for application"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            UPDATE ai_improvements 
            SET status = 'approved', approved_by = ?
            WHERE id = ?
        ''', (approved_by, improvement_id))
        
        conn.commit()
        conn.close()
        
        logger.info(f"Improvement {improvement_id} approved by {approved_by}")
        return True
    
    def get_learning_stats(self) -> Dict:
        """Get statistics about learning"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        stats = {}
        
        c.execute('SELECT COUNT(*) FROM ai_interactions')
        stats["total_interactions"] = c.fetchone()[0]
        
        c.execute('SELECT COUNT(*) FROM ai_feedback')
        stats["total_feedback"] = c.fetchone()[0]
        
        c.execute('SELECT COUNT(*) FROM ai_improvements WHERE status = "pending"')
        stats["pending_improvements"] = c.fetchone()[0]
        
        c.execute('SELECT COUNT(*) FROM ai_improvements WHERE status = "applied"')
        stats["applied_improvements"] = c.fetchone()[0]
        
        c.execute('SELECT COUNT(*) FROM ai_rules WHERE active = 1')
        stats["active_rules"] = c.fetchone()[0]
        
        conn.close()
        return stats


# Global instance
learning_engine = LearningEngine()
