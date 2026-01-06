"""
FASE 6: Perfiles Cognitivos
===========================
Personalización real de horarios por perfil
"""

from dataclasses import dataclass, field
from typing import List, Dict, Optional
from datetime import datetime
import json
import sqlite3
import logging

logger = logging.getLogger(__name__)


@dataclass
class TeacherProfile:
    """Perfil cognitivo/desgaste de docente"""
    teacher_id: int
    name: str
    fatigue_score: float       # 0-100 (100 = muy fatigado)
    burnout_risk: float        # 0-100
    preferred_periods: List[int]
    avoided_days: List[str]
    max_consecutive: int
    teaching_style: str        # "lecture", "interactive", "practical"
    strengths: List[str]
    concerns: List[str]


@dataclass  
class GroupProfile:
    """Perfil cognitivo de un grupo/sección"""
    group_id: int
    grade: str
    section: str
    avg_attention_span: int    # minutos
    learning_pace: str         # "fast", "normal", "slow"
    best_time_of_day: str      # "morning", "midday", "afternoon"
    challenging_subjects: List[str]
    strong_subjects: List[str]
    special_needs: List[str]
    behavior_pattern: str      # "calm", "active", "mixed"


@dataclass
class ScheduleAdaptation:
    """Adaptación sugerida para un horario"""
    original: Dict
    adapted: Dict
    reason: str
    expected_improvement: float  # %
    confidence: float


class CognitiveProfiler:
    """
    Motor de perfiles cognitivos para personalización real
    """
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_tables()
    
    def _init_tables(self):
        """Crear tablas para perfiles"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS teacher_profiles (
                teacher_id INTEGER PRIMARY KEY,
                name TEXT,
                fatigue_score REAL DEFAULT 0,
                burnout_risk REAL DEFAULT 0,
                preferred_periods TEXT,
                avoided_days TEXT,
                max_consecutive INTEGER DEFAULT 4,
                teaching_style TEXT DEFAULT 'interactive',
                strengths TEXT,
                concerns TEXT,
                last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS group_profiles (
                group_id INTEGER PRIMARY KEY,
                grade TEXT,
                section TEXT,
                avg_attention_span INTEGER DEFAULT 30,
                learning_pace TEXT DEFAULT 'normal',
                best_time TEXT DEFAULT 'morning',
                challenging_subjects TEXT,
                strong_subjects TEXT,
                special_needs TEXT,
                behavior_pattern TEXT DEFAULT 'mixed',
                last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS profile_adaptations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                profile_type TEXT,
                profile_id INTEGER,
                adaptation_type TEXT,
                original_value TEXT,
                adapted_value TEXT,
                reason TEXT,
                applied INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        conn.commit()
        conn.close()
    
    # =========================================
    # TEACHER PROFILES
    # =========================================
    
    def update_teacher_profile(self, teacher_id: int, **kwargs) -> bool:
        """Actualizar perfil de docente"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        # Check if exists
        c.execute('SELECT 1 FROM teacher_profiles WHERE teacher_id = ?', (teacher_id,))
        exists = c.fetchone()
        
        if exists:
            updates = []
            values = []
            for key, value in kwargs.items():
                if isinstance(value, (list, dict)):
                    value = json.dumps(value)
                updates.append(f"{key} = ?")
                values.append(value)
            
            values.append(teacher_id)
            c.execute(f'''
                UPDATE teacher_profiles 
                SET {", ".join(updates)}, last_updated = CURRENT_TIMESTAMP
                WHERE teacher_id = ?
            ''', values)
        else:
            columns = ['teacher_id'] + list(kwargs.keys())
            values = [teacher_id]
            for v in kwargs.values():
                if isinstance(v, (list, dict)):
                    v = json.dumps(v)
                values.append(v)
            
            placeholders = ', '.join(['?' for _ in values])
            c.execute(f'''
                INSERT INTO teacher_profiles ({", ".join(columns)})
                VALUES ({placeholders})
            ''', values)
        
        conn.commit()
        conn.close()
        return True
    
    def get_teacher_profile(self, teacher_id: int) -> Optional[TeacherProfile]:
        """Obtener perfil de docente"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('SELECT * FROM teacher_profiles WHERE teacher_id = ?', (teacher_id,))
        row = c.fetchone()
        conn.close()
        
        if not row:
            return None
        
        return TeacherProfile(
            teacher_id=row[0],
            name=row[1] or "",
            fatigue_score=row[2] or 0,
            burnout_risk=row[3] or 0,
            preferred_periods=json.loads(row[4]) if row[4] else [],
            avoided_days=json.loads(row[5]) if row[5] else [],
            max_consecutive=row[6] or 4,
            teaching_style=row[7] or "interactive",
            strengths=json.loads(row[8]) if row[8] else [],
            concerns=json.loads(row[9]) if row[9] else []
        )
    
    def calculate_teacher_fatigue(self, teacher_id: int, 
                                   weekly_hours: int,
                                   consecutive_periods: int,
                                   groups_count: int) -> float:
        """Calcular fatiga docente basado en carga"""
        base_fatigue = 0
        
        # Horas semanales (40 = agotamiento)
        base_fatigue += min(weekly_hours / 40 * 40, 40)
        
        # Períodos consecutivos
        if consecutive_periods > 4:
            base_fatigue += (consecutive_periods - 4) * 10
        
        # Cantidad de grupos (más grupos = más preparación)
        base_fatigue += min(groups_count * 5, 20)
        
        return min(base_fatigue, 100)
    
    # =========================================
    # GROUP PROFILES
    # =========================================
    
    def update_group_profile(self, group_id: int, **kwargs) -> bool:
        """Actualizar perfil de grupo"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('SELECT 1 FROM group_profiles WHERE group_id = ?', (group_id,))
        exists = c.fetchone()
        
        if exists:
            updates = []
            values = []
            for key, value in kwargs.items():
                if isinstance(value, (list, dict)):
                    value = json.dumps(value)
                updates.append(f"{key} = ?")
                values.append(value)
            
            values.append(group_id)
            c.execute(f'''
                UPDATE group_profiles 
                SET {", ".join(updates)}, last_updated = CURRENT_TIMESTAMP
                WHERE group_id = ?
            ''', values)
        else:
            columns = ['group_id'] + list(kwargs.keys())
            values = [group_id]
            for v in kwargs.values():
                if isinstance(v, (list, dict)):
                    v = json.dumps(v)
                values.append(v)
            
            placeholders = ', '.join(['?' for _ in values])
            c.execute(f'''
                INSERT INTO group_profiles ({", ".join(columns)})
                VALUES ({placeholders})
            ''', values)
        
        conn.commit()
        conn.close()
        return True
    
    def get_group_profile(self, group_id: int) -> Optional[GroupProfile]:
        """Obtener perfil de grupo"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('SELECT * FROM group_profiles WHERE group_id = ?', (group_id,))
        row = c.fetchone()
        conn.close()
        
        if not row:
            return None
        
        return GroupProfile(
            group_id=row[0],
            grade=row[1] or "",
            section=row[2] or "",
            avg_attention_span=row[3] or 30,
            learning_pace=row[4] or "normal",
            best_time_of_day=row[5] or "morning",
            challenging_subjects=json.loads(row[6]) if row[6] else [],
            strong_subjects=json.loads(row[7]) if row[7] else [],
            special_needs=json.loads(row[8]) if row[8] else [],
            behavior_pattern=row[9] or "mixed"
        )
    
    # =========================================
    # PERSONALIZATION
    # =========================================
    
    def adapt_schedule_for_teacher(self, schedule: List[Dict], 
                                    teacher_id: int) -> ScheduleAdaptation:
        """Adaptar horario según perfil de docente"""
        profile = self.get_teacher_profile(teacher_id)
        
        if not profile:
            return ScheduleAdaptation(
                original={"schedule": schedule},
                adapted={"schedule": schedule},
                reason="Sin perfil disponible",
                expected_improvement=0,
                confidence=0.5
            )
        
        adapted = schedule.copy()
        reasons = []
        
        # Evitar días no preferidos
        if profile.avoided_days:
            for block in adapted:
                if block.get("day") in profile.avoided_days:
                    reasons.append(f"Se identificó clase en día evitado ({block.get('day')})")
        
        # Ajustar a períodos preferidos
        if profile.preferred_periods:
            for block in adapted:
                if block.get("period") not in profile.preferred_periods:
                    reasons.append(f"Período {block.get('period')} no es preferido")
        
        improvement = 10 if reasons else 0
        
        return ScheduleAdaptation(
            original={"schedule": schedule},
            adapted={"schedule": adapted},
            reason=" | ".join(reasons) if reasons else "Horario compatible con perfil",
            expected_improvement=improvement,
            confidence=0.7
        )
    
    def predict_adaptation_success(self, schedule: List[Dict], 
                                    group_id: int) -> Dict:
        """Predecir qué tan bien se adaptará un grupo a un horario"""
        profile = self.get_group_profile(group_id)
        
        if not profile:
            return {
                "predicted_success": 0.7,
                "confidence": 0.5,
                "factors": ["Sin perfil de grupo"]
            }
        
        factors = []
        success = 0.8
        
        # Verificar horario vs mejor momento
        if profile.best_time_of_day == "morning":
            factors.append("Grupo rinde mejor en la mañana")
        
        # Ritmo de aprendizaje
        if profile.learning_pace == "slow":
            factors.append("Grupo requiere más tiempo por tema")
            success -= 0.1
        
        return {
            "predicted_success": success,
            "confidence": 0.75,
            "factors": factors
        }


# Global instance
cognitive_profiler = CognitiveProfiler()
