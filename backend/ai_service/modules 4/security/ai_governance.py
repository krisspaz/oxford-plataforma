"""
FASE 18: Gobierno de IA
=======================
Niveles de autonomía, decidir vs recomendar, control de riesgo
"""

from dataclasses import dataclass
from typing import List, Dict, Optional, Callable
from enum import Enum
from datetime import datetime
import json
import sqlite3
import logging

logger = logging.getLogger(__name__)


class AutonomyLevel(Enum):
    """Niveles de autonomía de la IA"""
    FULL_AUTO = "full_auto"           # Decide y ejecuta solo
    AUTO_WITH_LOG = "auto_with_log"   # Decide, ejecuta, registra
    RECOMMEND = "recommend"            # Solo recomienda
    ASK_FIRST = "ask_first"           # Pregunta antes de actuar
    DISABLED = "disabled"              # No interviene


class RiskLevel(Enum):
    """Nivel de riesgo de una acción"""
    CRITICAL = "critical"    # Puede causar daño irreversible
    HIGH = "high"            # Impacto significativo
    MEDIUM = "medium"        # Impacto moderado
    LOW = "low"              # Impacto mínimo
    NONE = "none"            # Sin riesgo


@dataclass
class GovernanceRule:
    """Regla de gobierno para la IA"""
    action_type: str
    autonomy_level: AutonomyLevel
    requires_approval_from: List[str]  # Roles que pueden aprobar
    risk_level: RiskLevel
    max_daily_auto_actions: int
    explanation: str


@dataclass
class ActionRequest:
    """Solicitud de acción a la IA"""
    action_id: str
    action_type: str
    requested_by: str
    role: str
    parameters: Dict
    timestamp: datetime


@dataclass
class GovernanceDecision:
    """Decisión del sistema de gobierno"""
    action: ActionRequest
    allowed: bool
    autonomy_applied: AutonomyLevel
    requires_human_approval: bool
    approvers_needed: List[str]
    risk_assessment: str
    explanation: str


class AIGovernance:
    """
    Sistema de gobierno de IA
    
    Controla:
    - Qué puede hacer la IA sola
    - Qué requiere aprobación
    - Niveles de riesgo
    - Límites diarios
    """
    
    # Reglas predefinidas de gobierno
    DEFAULT_RULES: Dict[str, GovernanceRule] = {
        # Acciones de consulta - Full Auto
        "view_schedule": GovernanceRule(
            action_type="view_schedule",
            autonomy_level=AutonomyLevel.FULL_AUTO,
            requires_approval_from=[],
            risk_level=RiskLevel.NONE,
            max_daily_auto_actions=1000,
            explanation="Consultas son seguras"
        ),
        "view_data": GovernanceRule(
            action_type="view_data",
            autonomy_level=AutonomyLevel.FULL_AUTO,
            requires_approval_from=[],
            risk_level=RiskLevel.NONE,
            max_daily_auto_actions=1000,
            explanation="Lectura de datos es segura"
        ),
        
        # Recomendaciones - Auto con log
        "suggest_schedule": GovernanceRule(
            action_type="suggest_schedule",
            autonomy_level=AutonomyLevel.AUTO_WITH_LOG,
            requires_approval_from=[],
            risk_level=RiskLevel.LOW,
            max_daily_auto_actions=100,
            explanation="Sugerencias no afectan datos"
        ),
        "risk_alert": GovernanceRule(
            action_type="risk_alert",
            autonomy_level=AutonomyLevel.AUTO_WITH_LOG,
            requires_approval_from=[],
            risk_level=RiskLevel.LOW,
            max_daily_auto_actions=50,
            explanation="Alertas informativas"
        ),
        
        # Modificaciones menores - Preguntar
        "modify_schedule_minor": GovernanceRule(
            action_type="modify_schedule_minor",
            autonomy_level=AutonomyLevel.ASK_FIRST,
            requires_approval_from=["ROLE_TEACHER", "ROLE_COORDINATION"],
            risk_level=RiskLevel.MEDIUM,
            max_daily_auto_actions=10,
            explanation="Cambios menores requieren confirmación"
        ),
        
        # Modificaciones mayores - Solo recomendar
        "modify_schedule_major": GovernanceRule(
            action_type="modify_schedule_major",
            autonomy_level=AutonomyLevel.RECOMMEND,
            requires_approval_from=["ROLE_DIRECTOR", "ROLE_ADMIN"],
            risk_level=RiskLevel.HIGH,
            max_daily_auto_actions=5,
            explanation="Cambios mayores solo se recomiendan"
        ),
        
        # Acciones críticas - Deshabilitado
        "delete_data": GovernanceRule(
            action_type="delete_data",
            autonomy_level=AutonomyLevel.DISABLED,
            requires_approval_from=["ROLE_SUPER_ADMIN"],
            risk_level=RiskLevel.CRITICAL,
            max_daily_auto_actions=0,
            explanation="Eliminación de datos prohibida para IA"
        ),
        "modify_rules": GovernanceRule(
            action_type="modify_rules",
            autonomy_level=AutonomyLevel.DISABLED,
            requires_approval_from=["ROLE_SUPER_ADMIN"],
            risk_level=RiskLevel.CRITICAL,
            max_daily_auto_actions=0,
            explanation="IA no puede modificar sus propias reglas"
        ),
    }
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self.rules = self.DEFAULT_RULES.copy()
        self._init_tables()
    
    def _init_tables(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS governance_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                action_type TEXT,
                action_id TEXT,
                requested_by TEXT,
                role TEXT,
                autonomy_applied TEXT,
                allowed INTEGER,
                requires_approval INTEGER,
                approvers TEXT,
                risk_level TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS pending_approvals (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                action_id TEXT UNIQUE,
                action_type TEXT,
                action_data TEXT,
                requested_by TEXT,
                approvers_needed TEXT,
                approved_by TEXT,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                resolved_at DATETIME
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS daily_action_counts (
                date TEXT,
                action_type TEXT,
                count INTEGER DEFAULT 0,
                PRIMARY KEY (date, action_type)
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def evaluate_action(self, request: ActionRequest) -> GovernanceDecision:
        """
        Evaluar si una acción debe ejecutarse
        
        Esta es la función central del gobierno de IA
        """
        rule = self.rules.get(request.action_type)
        
        if not rule:
            # Acción desconocida - solo recomendar
            rule = GovernanceRule(
                action_type=request.action_type,
                autonomy_level=AutonomyLevel.RECOMMEND,
                requires_approval_from=["ROLE_ADMIN"],
                risk_level=RiskLevel.MEDIUM,
                max_daily_auto_actions=5,
                explanation="Acción no reconocida - requiere supervisión"
            )
        
        # Verificar límite diario
        daily_count = self._get_daily_count(request.action_type)
        if daily_count >= rule.max_daily_auto_actions:
            return GovernanceDecision(
                action=request,
                allowed=False,
                autonomy_applied=AutonomyLevel.DISABLED,
                requires_human_approval=True,
                approvers_needed=rule.requires_approval_from,
                risk_assessment=f"Límite diario alcanzado ({daily_count}/{rule.max_daily_auto_actions})",
                explanation="Se ha excedido el límite diario de acciones automáticas"
            )
        
        # Determinar si se permite
        allowed = rule.autonomy_level not in [AutonomyLevel.DISABLED]
        requires_approval = rule.autonomy_level in [AutonomyLevel.ASK_FIRST, AutonomyLevel.RECOMMEND]
        
        # Verificar si el rol tiene permiso
        if requires_approval:
            if request.role not in rule.requires_approval_from:
                if rule.autonomy_level == AutonomyLevel.ASK_FIRST:
                    requires_approval = True  # Sigue requiriéndola
                else:
                    allowed = True  # Puede ver la recomendación
        
        decision = GovernanceDecision(
            action=request,
            allowed=allowed,
            autonomy_applied=rule.autonomy_level,
            requires_human_approval=requires_approval,
            approvers_needed=rule.requires_approval_from if requires_approval else [],
            risk_assessment=f"Nivel de riesgo: {rule.risk_level.value}",
            explanation=rule.explanation
        )
        
        # Registrar en log
        self._log_decision(decision)
        
        return decision
    
    def _get_daily_count(self, action_type: str) -> int:
        """Obtener conteo diario de acciones"""
        today = datetime.now().strftime("%Y-%m-%d")
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT count FROM daily_action_counts 
            WHERE date = ? AND action_type = ?
        ''', (today, action_type))
        
        row = c.fetchone()
        conn.close()
        
        return row[0] if row else 0
    
    def _increment_daily_count(self, action_type: str):
        """Incrementar conteo diario"""
        today = datetime.now().strftime("%Y-%m-%d")
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO daily_action_counts (date, action_type, count)
            VALUES (?, ?, 1)
            ON CONFLICT(date, action_type) DO UPDATE SET count = count + 1
        ''', (today, action_type))
        
        conn.commit()
        conn.close()
    
    def _log_decision(self, decision: GovernanceDecision):
        """Registrar decisión en log"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO governance_log 
            (action_type, action_id, requested_by, role, autonomy_applied,
             allowed, requires_approval, approvers, risk_level)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            decision.action.action_type,
            decision.action.action_id,
            decision.action.requested_by,
            decision.action.role,
            decision.autonomy_applied.value,
            1 if decision.allowed else 0,
            1 if decision.requires_human_approval else 0,
            json.dumps(decision.approvers_needed),
            decision.risk_assessment
        ))
        
        conn.commit()
        conn.close()
    
    def request_approval(self, decision: GovernanceDecision) -> str:
        """Crear solicitud de aprobación"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT OR REPLACE INTO pending_approvals 
            (action_id, action_type, action_data, requested_by, approvers_needed)
            VALUES (?, ?, ?, ?, ?)
        ''', (
            decision.action.action_id,
            decision.action.action_type,
            json.dumps(decision.action.parameters),
            decision.action.requested_by,
            json.dumps(decision.approvers_needed)
        ))
        
        conn.commit()
        conn.close()
        
        return decision.action.action_id
    
    def approve_action(self, action_id: str, approved_by: str) -> bool:
        """Aprobar una acción pendiente"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            UPDATE pending_approvals 
            SET status = 'approved', approved_by = ?, resolved_at = CURRENT_TIMESTAMP
            WHERE action_id = ? AND status = 'pending'
        ''', (approved_by, action_id))
        
        success = c.rowcount > 0
        conn.commit()
        conn.close()
        
        return success
    
    def get_pending_approvals(self) -> List[Dict]:
        """Obtener aprobaciones pendientes"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT * FROM pending_approvals WHERE status = 'pending'
            ORDER BY created_at DESC
        ''')
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "action_id": r[1],
            "action_type": r[2],
            "action_data": json.loads(r[3]),
            "requested_by": r[4],
            "approvers_needed": json.loads(r[5]),
            "created_at": r[8]
        } for r in rows]


# Global instance
ai_governance = AIGovernance()
