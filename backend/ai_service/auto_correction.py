"""
FASE 10: Auto-Corrección
========================
Reoptimización mínima y prevención de efecto dominó
"""

from dataclasses import dataclass
from typing import List, Dict, Optional, Tuple
from datetime import datetime
import json
import sqlite3
import logging

logger = logging.getLogger(__name__)


@dataclass
class ManualChange:
    """Registro de cambio manual detectado"""
    change_id: int
    timestamp: datetime
    user_id: str
    original_block: Dict
    new_block: Dict
    reason: str
    impact_level: str  # "low", "medium", "high"


@dataclass
class CorrectionProposal:
    """Propuesta de corrección automática"""
    affected_blocks: List[Dict]
    proposed_changes: List[Dict]
    expected_improvement: float
    cascade_risk: float  # 0-1, riesgo de efecto dominó
    should_apply: bool
    explanation: str


class AutoCorrectionEngine:
    """
    Motor de auto-corrección inteligente
    
    Funciones:
    - Detectar cambios manuales
    - Reoptimizar minimizando impacto
    - Prevenir efecto dominó
    - Undo inteligente
    """
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_tables()
    
    def _init_tables(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS manual_changes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT,
                original_block TEXT,
                new_block TEXT,
                reason TEXT,
                impact_level TEXT DEFAULT 'low',
                detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                corrected INTEGER DEFAULT 0,
                correction_applied TEXT
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS schedule_snapshots (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                snapshot_name TEXT,
                schedule_data TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                is_stable INTEGER DEFAULT 0
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS undo_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                action_type TEXT,
                before_state TEXT,
                after_state TEXT,
                user_id TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                undone INTEGER DEFAULT 0
            )
        ''')
        
        conn.commit()
        conn.close()
    
    # =========================================
    # CHANGE DETECTION
    # =========================================
    
    def detect_manual_change(self, original: Dict, new: Dict, 
                             user_id: str, reason: str = "") -> ManualChange:
        """Registrar y analizar un cambio manual"""
        # Calcular nivel de impacto
        impact = self._calculate_impact(original, new)
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO manual_changes 
            (user_id, original_block, new_block, reason, impact_level)
            VALUES (?, ?, ?, ?, ?)
        ''', (user_id, json.dumps(original), json.dumps(new), reason, impact))
        
        change_id = c.lastrowid
        conn.commit()
        conn.close()
        
        return ManualChange(
            change_id=change_id,
            timestamp=datetime.now(),
            user_id=user_id,
            original_block=original,
            new_block=new,
            reason=reason,
            impact_level=impact
        )
    
    def _calculate_impact(self, original: Dict, new: Dict) -> str:
        """Calcular nivel de impacto de un cambio"""
        changes = 0
        
        # Cambio de día = alto impacto
        if original.get("day") != new.get("day"):
            changes += 3
        
        # Cambio de período = impacto medio
        if original.get("period") != new.get("period"):
            changes += 2
        
        # Cambio de aula = bajo impacto
        if original.get("room") != new.get("room"):
            changes += 1
        
        # Cambio de docente = alto impacto
        if original.get("teacher_id") != new.get("teacher_id"):
            changes += 3
        
        if changes >= 5:
            return "high"
        elif changes >= 3:
            return "medium"
        return "low"
    
    # =========================================
    # AUTO-CORRECTION
    # =========================================
    
    def analyze_for_correction(self, current_schedule: List[Dict]) -> CorrectionProposal:
        """
        Analizar horario actual y proponer correcciones mínimas
        """
        issues = []
        proposed = []
        cascade_risk = 0
        
        # Detectar conflictos
        conflicts = self._find_conflicts(current_schedule)
        
        if not conflicts:
            return CorrectionProposal(
                affected_blocks=[],
                proposed_changes=[],
                expected_improvement=0,
                cascade_risk=0,
                should_apply=False,
                explanation="No se detectaron conflictos que requieran corrección."
            )
        
        # Para cada conflicto, encontrar corrección mínima
        for conflict in conflicts:
            correction = self._find_minimal_correction(conflict, current_schedule)
            if correction:
                proposed.append(correction)
                # Evaluar riesgo de cascada
                cascade_risk += correction.get("cascade_risk", 0.1)
        
        cascade_risk = min(cascade_risk, 1.0)
        
        return CorrectionProposal(
            affected_blocks=[c["block"] for c in conflicts],
            proposed_changes=proposed,
            expected_improvement=len(conflicts) * 10,
            cascade_risk=cascade_risk,
            should_apply=cascade_risk < 0.5,  # Solo aplicar si bajo riesgo
            explanation=f"Detectados {len(conflicts)} conflictos. Riesgo de cascada: {cascade_risk:.0%}"
        )
    
    def _find_conflicts(self, schedule: List[Dict]) -> List[Dict]:
        """Encontrar conflictos en el horario"""
        conflicts = []
        
        # Agrupar por día y período
        by_slot = {}
        for block in schedule:
            key = f"{block.get('day')}_{block.get('period')}"
            if key not in by_slot:
                by_slot[key] = []
            by_slot[key].append(block)
        
        # Detectar duplicados
        for key, blocks in by_slot.items():
            # Mismo aula
            rooms = [b.get("room") for b in blocks if b.get("room")]
            if len(rooms) != len(set(rooms)):
                conflicts.append({
                    "type": "room_conflict",
                    "slot": key,
                    "block": blocks[0]
                })
            
            # Mismo docente
            teachers = [b.get("teacher_id") for b in blocks if b.get("teacher_id")]
            if len(teachers) != len(set(teachers)):
                conflicts.append({
                    "type": "teacher_conflict",
                    "slot": key,
                    "block": blocks[0]
                })
        
        return conflicts
    
    def _find_minimal_correction(self, conflict: Dict, 
                                  schedule: List[Dict]) -> Optional[Dict]:
        """Encontrar la corrección mínima para un conflicto"""
        block = conflict["block"]
        conflict_type = conflict["type"]
        
        # Buscar slot disponible más cercano
        current_period = block.get("period", 1)
        current_day = block.get("day", "lunes")
        
        # Intentar mover a período adyacente
        for offset in [1, -1, 2, -2]:
            new_period = current_period + offset
            if 1 <= new_period <= 8:
                if self._is_slot_available(current_day, new_period, schedule):
                    return {
                        "original": block,
                        "new_period": new_period,
                        "new_day": current_day,
                        "cascade_risk": 0.1 * abs(offset)
                    }
        
        return None
    
    def _is_slot_available(self, day: str, period: int, 
                          schedule: List[Dict]) -> bool:
        """Verificar si un slot está disponible"""
        for block in schedule:
            if block.get("day") == day and block.get("period") == period:
                return False
        return True
    
    # =========================================
    # SNAPSHOTS & UNDO
    # =========================================
    
    def save_snapshot(self, schedule: List[Dict], name: str, 
                     is_stable: bool = False) -> int:
        """Guardar snapshot del horario"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO schedule_snapshots (snapshot_name, schedule_data, is_stable)
            VALUES (?, ?, ?)
        ''', (name, json.dumps(schedule), 1 if is_stable else 0))
        
        snapshot_id = c.lastrowid
        conn.commit()
        conn.close()
        
        return snapshot_id
    
    def get_stable_snapshot(self) -> Optional[List[Dict]]:
        """Obtener último snapshot estable"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT schedule_data FROM schedule_snapshots 
            WHERE is_stable = 1 
            ORDER BY created_at DESC LIMIT 1
        ''')
        
        row = c.fetchone()
        conn.close()
        
        if row:
            return json.loads(row[0])
        return None
    
    def smart_undo(self, steps: int = 1) -> Dict:
        """Undo inteligente - deshace cambios minimizando impacto"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT id, before_state FROM undo_history 
            WHERE undone = 0 
            ORDER BY created_at DESC LIMIT ?
        ''', (steps,))
        
        rows = c.fetchall()
        
        if not rows:
            conn.close()
            return {"success": False, "message": "No hay cambios para deshacer"}
        
        # Marcar como deshecho
        for row in rows:
            c.execute('UPDATE undo_history SET undone = 1 WHERE id = ?', (row[0],))
        
        conn.commit()
        conn.close()
        
        # Retornar estado anterior
        return {
            "success": True,
            "restored_state": json.loads(rows[-1][1]),
            "steps_undone": len(rows)
        }


# Global instance
auto_correction = AutoCorrectionEngine()
