"""
Rule Engine for Oxford AI
==========================
IF/THEN logic for schedule validation and business rules
"""

from dataclasses import dataclass
from typing import List, Dict, Any, Optional
from datetime import datetime, date
import logging

logger = logging.getLogger(__name__)


@dataclass
class RuleResult:
    """Result of a rule validation"""
    allowed: bool
    reason: str
    alternative: Optional[str] = None
    impact: Optional[str] = None
    
    def to_structured_response(self) -> str:
        """Format as structured response"""
        status = "✅ Permitido" if self.allowed else "❌ No permitido"
        response = f"{status}\n\n📋 **Motivo:** {self.reason}"
        
        if self.alternative:
            response += f"\n\n💡 **Alternativa:** {self.alternative}"
        
        if self.impact:
            response += f"\n\n📊 **Impacto:** {self.impact}"
            
        return response


class RuleEngine:
    """
    Business rules engine for academic scheduling
    Uses hard IF/THEN rules, not AI guessing
    """
    
    # Configuration - can be loaded from database
    RULES = {
        # Teacher limits
        "max_teacher_weekly_hours": 40,
        "max_teacher_daily_hours": 8,
        "max_consecutive_periods": 4,
        "min_break_between_classes": 10,  # minutes
        
        # Schedule limits
        "school_start_hour": 7,
        "school_end_hour": 14,
        "class_duration_minutes": 45,
        "recess_duration_minutes": 30,
        
        # Blocked dates (holidays)
        "blocked_dates": [
            "2026-01-01",  # Año Nuevo
            "2026-04-13",  # Semana Santa (ejemplo)
            "2026-04-14",
            "2026-05-01",  # Día del Trabajo
            "2026-06-30",  # Día del Ejército
            "2026-09-15",  # Independencia
            "2026-10-20",  # Día de la Revolución
            "2026-11-01",  # Día de Todos los Santos
            "2026-12-25",  # Navidad
        ],
    }
    
    def __init__(self, custom_rules: Dict = None):
        if custom_rules:
            self.RULES.update(custom_rules)
    
    # =========================================
    # TEACHER RULES
    # =========================================
    
    def validate_teacher_hours(self, teacher_id: int, current_hours: int, new_hours: int) -> RuleResult:
        """
        Rule: Teacher cannot exceed max weekly hours
        """
        total = current_hours + new_hours
        max_hours = self.RULES["max_teacher_weekly_hours"]
        
        if total > max_hours:
            return RuleResult(
                allowed=False,
                reason=f"El docente tiene {current_hours}h asignadas. Agregar {new_hours}h excede el máximo de {max_hours}h semanales.",
                alternative=f"Disponible: {max_hours - current_hours}h adicionales como máximo.",
                impact="Evita sobrecarga docente y burnout"
            )
        
        return RuleResult(
            allowed=True,
            reason=f"Total: {total}h de {max_hours}h permitidas ({max_hours - total}h disponibles)",
            impact="Ninguno"
        )
    
    def validate_teacher_daily_hours(self, teacher_id: int, schedule_for_day: List[Dict]) -> RuleResult:
        """
        Rule: Teacher cannot exceed max daily hours
        """
        total_hours = len(schedule_for_day) * (self.RULES["class_duration_minutes"] / 60)
        max_daily = self.RULES["max_teacher_daily_hours"]
        
        if total_hours > max_daily:
            return RuleResult(
                allowed=False,
                reason=f"El docente ya tiene {total_hours:.1f}h asignadas este día. Máximo: {max_daily}h.",
                alternative="Distribuir en otro día de la semana.",
                impact="Previene fatiga y mejora calidad de enseñanza"
            )
        
        return RuleResult(allowed=True, reason="Dentro del límite diario")
    
    def validate_consecutive_periods(self, teacher_id: int, periods: List[int]) -> RuleResult:
        """
        Rule: Teacher cannot have more than N consecutive periods without break
        """
        max_consecutive = self.RULES["max_consecutive_periods"]
        
        if len(periods) < 2:
            return RuleResult(allowed=True, reason="Pocos períodos, no aplica")
        
        # Sort and check consecutive
        periods_sorted = sorted(periods)
        consecutive = 1
        max_found = 1
        
        for i in range(1, len(periods_sorted)):
            if periods_sorted[i] == periods_sorted[i-1] + 1:
                consecutive += 1
                max_found = max(max_found, consecutive)
            else:
                consecutive = 1
        
        if max_found > max_consecutive:
            return RuleResult(
                allowed=False,
                reason=f"El docente tiene {max_found} períodos consecutivos. Máximo: {max_consecutive}.",
                alternative="Insertar un período libre entre clases.",
                impact="Recomendación de bienestar docente"
            )
        
        return RuleResult(allowed=True, reason=f"{max_found} períodos consecutivos (máx {max_consecutive})")
    
    # =========================================
    # ROOM/CLASSROOM RULES
    # =========================================
    
    def validate_room_availability(self, room_id: int, day: str, period: int, 
                                   current_schedule: List[Dict]) -> RuleResult:
        """
        Rule: Room cannot be double-booked
        """
        conflicts = [
            s for s in current_schedule 
            if s.get("room_id") == room_id and s.get("day") == day and s.get("period") == period
        ]
        
        if conflicts:
            conflict = conflicts[0]
            return RuleResult(
                allowed=False,
                reason=f"El aula ya está ocupada por: {conflict.get('subject', 'Otra clase')}",
                alternative=f"Sugerir otra aula disponible o cambiar período.",
                impact=f"Conflicto con {conflict.get('teacher_name', 'otro docente')}"
            )
        
        return RuleResult(allowed=True, reason="Aula disponible")
    
    # =========================================
    # DATE RULES
    # =========================================
    
    def validate_date(self, target_date: str) -> RuleResult:
        """
        Rule: Cannot schedule on blocked dates (holidays)
        """
        if target_date in self.RULES["blocked_dates"]:
            return RuleResult(
                allowed=False,
                reason=f"La fecha {target_date} es un día feriado/bloqueado.",
                alternative="Elegir el día hábil más cercano.",
                impact="No hay clases programadas ese día"
            )
        
        return RuleResult(allowed=True, reason="Fecha permitida")
    
    def validate_time_range(self, start_hour: int, end_hour: int) -> RuleResult:
        """
        Rule: Classes must be within school hours
        """
        school_start = self.RULES["school_start_hour"]
        school_end = self.RULES["school_end_hour"]
        
        if start_hour < school_start:
            return RuleResult(
                allowed=False,
                reason=f"Horario inicia antes de las {school_start}:00 (apertura del colegio).",
                alternative=f"Mover a {school_start}:00 o después."
            )
        
        if end_hour > school_end:
            return RuleResult(
                allowed=False,
                reason=f"Horario termina después de las {school_end}:00 (cierre del colegio).",
                alternative=f"Terminar antes de las {school_end}:00."
            )
        
        return RuleResult(allowed=True, reason="Dentro del horario escolar")
    
    # =========================================
    # COMBINED VALIDATION
    # =========================================
    
    def validate_schedule_change(self, 
                                  teacher_id: int,
                                  teacher_current_hours: int,
                                  room_id: int,
                                  day: str,
                                  period: int,
                                  current_schedule: List[Dict]) -> RuleResult:
        """
        Run all validations for a schedule change
        Returns the first failure or success
        """
        
        # Check room
        room_result = self.validate_room_availability(room_id, day, period, current_schedule)
        if not room_result.allowed:
            return room_result
        
        # Check teacher hours
        hours_result = self.validate_teacher_hours(teacher_id, teacher_current_hours, 1)
        if not hours_result.allowed:
            return hours_result
        
        return RuleResult(
            allowed=True,
            reason="Todas las validaciones pasaron correctamente.",
            impact="Ninguno - cambio permitido"
        )


# Global instance
rule_engine = RuleEngine()
