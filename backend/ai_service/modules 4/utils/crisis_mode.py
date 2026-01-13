"""
FASE 20: Modo Crisis
====================
Reoptimización inmediata, continuidad operativa
"""

from dataclasses import dataclass
from typing import List, Dict, Optional
from datetime import datetime, timedelta
from enum import Enum
import json
import sqlite3
import logging

logger = logging.getLogger(__name__)


class CrisisType(Enum):
    """Tipos de crisis que el sistema puede manejar"""
    TEACHER_ABSENCE = "teacher_absence"       # Docente no disponible
    ROOM_UNAVAILABLE = "room_unavailable"     # Aula no disponible
    SCHEDULE_CONFLICT = "schedule_conflict"   # Conflicto detectado
    EMERGENCY_EVENT = "emergency_event"       # Evento de emergencia
    MASS_CHANGE = "mass_change"               # Cambio masivo requerido
    SYSTEM_FAILURE = "system_failure"         # Falla de sistema


class CrisisSeverity(Enum):
    """Severidad de la crisis"""
    CRITICAL = "critical"   # Afecta operación inmediata
    HIGH = "high"           # Requiere acción en horas
    MEDIUM = "medium"       # Puede esperar 1 día
    LOW = "low"             # Planificable


@dataclass
class CrisisEvent:
    """Evento de crisis"""
    crisis_id: str
    crisis_type: CrisisType
    severity: CrisisSeverity
    description: str
    affected_entities: List[str]  # IDs de docentes, aulas, grupos afectados
    detected_at: datetime
    resolved: bool = False


@dataclass
class CrisisResolution:
    """Resolución propuesta para una crisis"""
    crisis_id: str
    solution_type: str
    changes_proposed: List[Dict]
    impact_assessment: str
    execution_time: timedelta
    confidence: float
    requires_approval: bool


class CrisisMode:
    """
    Motor de Modo Crisis
    
    Funciones:
    - Detectar crisis automáticamente
    - Reoptimización inmediata
    - Priorizar estabilidad
    - Continuidad operativa
    """
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self.active_crisis = None
        self._init_tables()
    
    def _init_tables(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS crisis_events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                crisis_id TEXT UNIQUE,
                crisis_type TEXT,
                severity TEXT,
                description TEXT,
                affected_entities TEXT,
                detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                resolved INTEGER DEFAULT 0,
                resolved_at DATETIME,
                resolution_method TEXT
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS crisis_playbook (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                crisis_type TEXT,
                step_order INTEGER,
                action TEXT,
                responsible_role TEXT,
                max_time_minutes INTEGER
            )
        ''')
        
        # Insertar playbook predefinido
        self._init_playbook(c)
        
        conn.commit()
        conn.close()
    
    def _init_playbook(self, cursor):
        """Inicializar playbook de crisis"""
        playbook = [
            # Ausencia de docente
            ("teacher_absence", 1, "Identificar clases afectadas", "ROLE_COORDINATION", 5),
            ("teacher_absence", 2, "Buscar sustituto disponible", "ROLE_COORDINATION", 10),
            ("teacher_absence", 3, "Reasignar aulas si necesario", "ROLE_COORDINATION", 5),
            ("teacher_absence", 4, "Notificar a grupos afectados", "ROLE_SECRETARY", 5),
            ("teacher_absence", 5, "Actualizar horario temporal", "ROLE_COORDINATION", 5),
            
            # Aula no disponible
            ("room_unavailable", 1, "Identificar clases afectadas", "ROLE_COORDINATION", 5),
            ("room_unavailable", 2, "Buscar aula alternativa", "ROLE_COORDINATION", 10),
            ("room_unavailable", 3, "Validar capacidad y equipamiento", "ROLE_COORDINATION", 5),
            ("room_unavailable", 4, "Notificar cambio", "ROLE_SECRETARY", 5),
            
            # Emergencia general
            ("emergency_event", 1, "Activar protocolo de seguridad", "ROLE_DIRECTOR", 1),
            ("emergency_event", 2, "Suspender actividades si necesario", "ROLE_DIRECTOR", 5),
            ("emergency_event", 3, "Comunicar a comunidad", "ROLE_DIRECTOR", 10),
            ("emergency_event", 4, "Planificar recuperación", "ROLE_COORDINATION", 30),
        ]
        
        cursor.execute('SELECT COUNT(*) FROM crisis_playbook')
        if cursor.fetchone()[0] == 0:
            cursor.executemany('''
                INSERT INTO crisis_playbook 
                (crisis_type, step_order, action, responsible_role, max_time_minutes)
                VALUES (?, ?, ?, ?, ?)
            ''', playbook)
    
    # =========================================
    # CRISIS DETECTION
    # =========================================
    
    def detect_crisis(self, event_type: str, data: Dict) -> Optional[CrisisEvent]:
        """Detectar y registrar una crisis"""
        crisis_type = CrisisType(event_type) if event_type in [c.value for c in CrisisType] else CrisisType.SCHEDULE_CONFLICT
        
        # Determinar severidad
        severity = self._assess_severity(crisis_type, data)
        
        crisis_id = f"CRISIS_{datetime.now().strftime('%Y%m%d%H%M%S')}"
        
        crisis = CrisisEvent(
            crisis_id=crisis_id,
            crisis_type=crisis_type,
            severity=severity,
            description=data.get("description", "Crisis detectada"),
            affected_entities=data.get("affected", []),
            detected_at=datetime.now()
        )
        
        # Registrar
        self._save_crisis(crisis)
        self.active_crisis = crisis
        
        logger.warning(f"🚨 CRISIS DETECTADA: {crisis_id} - {crisis_type.value} ({severity.value})")
        
        return crisis
    
    def _assess_severity(self, crisis_type: CrisisType, data: Dict) -> CrisisSeverity:
        """Evaluar severidad de la crisis"""
        affected_count = len(data.get("affected", []))
        is_immediate = data.get("immediate", False)
        
        if crisis_type == CrisisType.EMERGENCY_EVENT:
            return CrisisSeverity.CRITICAL
        
        if is_immediate and affected_count > 5:
            return CrisisSeverity.CRITICAL
        elif is_immediate or affected_count > 3:
            return CrisisSeverity.HIGH
        elif affected_count > 1:
            return CrisisSeverity.MEDIUM
        
        return CrisisSeverity.LOW
    
    def _save_crisis(self, crisis: CrisisEvent):
        """Guardar crisis en BD"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO crisis_events 
            (crisis_id, crisis_type, severity, description, affected_entities)
            VALUES (?, ?, ?, ?, ?)
        ''', (
            crisis.crisis_id,
            crisis.crisis_type.value,
            crisis.severity.value,
            crisis.description,
            json.dumps(crisis.affected_entities)
        ))
        
        conn.commit()
        conn.close()
    
    # =========================================
    # CRISIS RESOLUTION
    # =========================================
    
    def generate_resolution(self, crisis: CrisisEvent, 
                            current_schedule: List[Dict]) -> CrisisResolution:
        """Generar resolución para una crisis"""
        changes = []
        
        if crisis.crisis_type == CrisisType.TEACHER_ABSENCE:
            changes = self._resolve_teacher_absence(crisis, current_schedule)
        elif crisis.crisis_type == CrisisType.ROOM_UNAVAILABLE:
            changes = self._resolve_room_unavailable(crisis, current_schedule)
        elif crisis.crisis_type == CrisisType.SCHEDULE_CONFLICT:
            changes = self._resolve_conflict(crisis, current_schedule)
        
        # Evaluar impacto
        impact = self._assess_resolution_impact(changes)
        
        return CrisisResolution(
            crisis_id=crisis.crisis_id,
            solution_type="automatic_reoptimization",
            changes_proposed=changes,
            impact_assessment=impact,
            execution_time=timedelta(minutes=len(changes) * 2),
            confidence=0.8 if len(changes) <= 3 else 0.6,
            requires_approval=crisis.severity in [CrisisSeverity.CRITICAL, CrisisSeverity.HIGH]
        )
    
    def _resolve_teacher_absence(self, crisis: CrisisEvent, 
                                  schedule: List[Dict]) -> List[Dict]:
        """Resolver ausencia de docente"""
        changes = []
        teacher_id = crisis.affected_entities[0] if crisis.affected_entities else None
        
        if not teacher_id:
            return []
        
        # Encontrar clases afectadas
        affected_blocks = [b for b in schedule if str(b.get("teacher_id")) == str(teacher_id)]
        
        for block in affected_blocks:
            # Opción 1: Buscar sustituto
            changes.append({
                "type": "substitute",
                "original": block,
                "action": "assign_substitute",
                "priority": "high"
            })
        
        return changes
    
    def _resolve_room_unavailable(self, crisis: CrisisEvent, 
                                   schedule: List[Dict]) -> List[Dict]:
        """Resolver aula no disponible"""
        changes = []
        room_id = crisis.affected_entities[0] if crisis.affected_entities else None
        
        if not room_id:
            return []
        
        # Encontrar clases afectadas
        affected_blocks = [b for b in schedule if str(b.get("room_id")) == str(room_id)]
        
        for block in affected_blocks:
            changes.append({
                "type": "reassign_room",
                "original": block,
                "action": "find_alternative_room",
                "priority": "high"
            })
        
        return changes
    
    def _resolve_conflict(self, crisis: CrisisEvent, 
                          schedule: List[Dict]) -> List[Dict]:
        """Resolver conflicto de horario"""
        return [{
            "type": "conflict_resolution",
            "action": "reschedule_one_party",
            "priority": "medium"
        }]
    
    def _assess_resolution_impact(self, changes: List[Dict]) -> str:
        """Evaluar impacto de los cambios propuestos"""
        if not changes:
            return "Sin cambios necesarios"
        
        if len(changes) == 1:
            return "Impacto mínimo - solo 1 cambio requerido"
        elif len(changes) <= 3:
            return f"Impacto bajo - {len(changes)} cambios requeridos"
        elif len(changes) <= 5:
            return f"Impacto moderado - {len(changes)} cambios afectan múltiples clases"
        else:
            return f"Impacto alto - {len(changes)} cambios requieren revisión cuidadosa"
    
    # =========================================
    # PLAYBOOK EXECUTION
    # =========================================
    
    def get_playbook(self, crisis_type: str) -> List[Dict]:
        """Obtener playbook para un tipo de crisis"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT step_order, action, responsible_role, max_time_minutes
            FROM crisis_playbook
            WHERE crisis_type = ?
            ORDER BY step_order
        ''', (crisis_type,))
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "step": r[0],
            "action": r[1],
            "responsible": r[2],
            "max_time": r[3]
        } for r in rows]
    
    def resolve_crisis(self, crisis_id: str, method: str = "automatic"):
        """Marcar crisis como resuelta"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            UPDATE crisis_events 
            SET resolved = 1, resolved_at = CURRENT_TIMESTAMP, resolution_method = ?
            WHERE crisis_id = ?
        ''', (method, crisis_id))
        
        conn.commit()
        conn.close()
        
        self.active_crisis = None
        logger.info(f"✅ Crisis {crisis_id} resuelta con método: {method}")
    
    def get_active_crises(self) -> List[Dict]:
        """Obtener crisis activas"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT * FROM crisis_events WHERE resolved = 0
            ORDER BY detected_at DESC
        ''')
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "crisis_id": r[1],
            "type": r[2],
            "severity": r[3],
            "description": r[4],
            "affected": json.loads(r[5]) if r[5] else [],
            "detected_at": r[6]
        } for r in rows]


# Global instance
crisis_mode = CrisisMode()
