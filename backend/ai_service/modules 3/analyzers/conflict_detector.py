"""
Schedule Conflict Detection Module
===================================
Validates generated schedules for conflicts and constraint violations
"""

from typing import List, Dict, Any, Tuple
from dataclasses import dataclass
from enum import Enum


class ConflictType(Enum):
    TEACHER_DOUBLE_BOOKING = "teacher_double_booking"
    ROOM_DOUBLE_BOOKING = "room_double_booking"
    GRADE_DOUBLE_BOOKING = "grade_double_booking"
    TEACHER_OVERLOAD = "teacher_overload"
    SUBJECT_DISTRIBUTION = "subject_distribution"
    CONSECUTIVE_SAME_SUBJECT = "consecutive_same_subject"


@dataclass
class Conflict:
    """Represents a schedule conflict"""
    type: ConflictType
    severity: str  # "error", "warning", "info"
    message: str
    day: int
    period: int
    details: Dict[str, Any]


class ScheduleValidator:
    """Validates schedules for conflicts and constraint violations"""
    
    def __init__(self, max_teacher_hours: int = 6, max_consecutive_same: int = 2):
        self.max_teacher_hours = max_teacher_hours
        self.max_consecutive_same = max_consecutive_same
    
    def validate(self, schedule: List[Dict]) -> Tuple[bool, List[Conflict]]:
        """
        Validate a complete schedule
        
        Args:
            schedule: List of schedule entries, each with:
                - day (1-5)
                - period (1-8)
                - teacherId
                - subjectId
                - gradeId
                - sectionId (optional)
                - classroom (optional)
        
        Returns:
            Tuple of (is_valid, list of conflicts)
        """
        conflicts = []
        
        conflicts.extend(self._check_teacher_double_booking(schedule))
        conflicts.extend(self._check_room_double_booking(schedule))
        conflicts.extend(self._check_grade_double_booking(schedule))
        conflicts.extend(self._check_teacher_overload(schedule))
        conflicts.extend(self._check_consecutive_same_subject(schedule))
        conflicts.extend(self._check_subject_distribution(schedule))
        
        is_valid = not any(c.severity == "error" for c in conflicts)
        return is_valid, conflicts
    
    def _check_teacher_double_booking(self, schedule: List[Dict]) -> List[Conflict]:
        """Check if a teacher is assigned to multiple classes at the same time"""
        conflicts = []
        teacher_slots = {}  # (day, period) -> [entries]
        
        for entry in schedule:
            key = (entry.get("day"), entry.get("period"), entry.get("teacherId"))
            if key[2]:  # Only if teacher is specified
                slot_key = (entry.get("day"), entry.get("period"))
                if slot_key not in teacher_slots:
                    teacher_slots[slot_key] = {}
                
                teacher_id = entry.get("teacherId")
                if teacher_id not in teacher_slots[slot_key]:
                    teacher_slots[slot_key][teacher_id] = []
                teacher_slots[slot_key][teacher_id].append(entry)
        
        for (day, period), teachers in teacher_slots.items():
            for teacher_id, entries in teachers.items():
                if len(entries) > 1:
                    conflicts.append(Conflict(
                        type=ConflictType.TEACHER_DOUBLE_BOOKING,
                        severity="error",
                        message=f"Profesor {teacher_id} asignado a múltiples clases el día {day}, período {period}",
                        day=day,
                        period=period,
                        details={"teacherId": teacher_id, "entries": entries}
                    ))
        
        return conflicts
    
    def _check_room_double_booking(self, schedule: List[Dict]) -> List[Conflict]:
        """Check if a classroom is used by multiple classes at the same time"""
        conflicts = []
        room_slots = {}
        
        for entry in schedule:
            classroom = entry.get("classroom")
            if classroom:
                key = (entry.get("day"), entry.get("period"), classroom)
                if key not in room_slots:
                    room_slots[key] = []
                room_slots[key].append(entry)
        
        for (day, period, room), entries in room_slots.items():
            if len(entries) > 1:
                conflicts.append(Conflict(
                    type=ConflictType.ROOM_DOUBLE_BOOKING,
                    severity="error",
                    message=f"Aula '{room}' ocupada por múltiples clases el día {day}, período {period}",
                    day=day,
                    period=period,
                    details={"classroom": room, "entries": entries}
                ))
        
        return conflicts
    
    def _check_grade_double_booking(self, schedule: List[Dict]) -> List[Conflict]:
        """Check if a grade/section has multiple classes at the same time"""
        conflicts = []
        grade_slots = {}
        
        for entry in schedule:
            key = (
                entry.get("day"), 
                entry.get("period"), 
                entry.get("gradeId"),
                entry.get("sectionId")
            )
            if key not in grade_slots:
                grade_slots[key] = []
            grade_slots[key].append(entry)
        
        for (day, period, grade, section), entries in grade_slots.items():
            if len(entries) > 1:
                conflicts.append(Conflict(
                    type=ConflictType.GRADE_DOUBLE_BOOKING,
                    severity="error",
                    message=f"Grado {grade} sección {section} tiene múltiples clases el día {day}, período {period}",
                    day=day,
                    period=period,
                    details={"gradeId": grade, "sectionId": section, "entries": entries}
                ))
        
        return conflicts
    
    def _check_teacher_overload(self, schedule: List[Dict]) -> List[Conflict]:
        """Check if a teacher has too many hours in a day"""
        conflicts = []
        teacher_daily_hours = {}
        
        for entry in schedule:
            teacher_id = entry.get("teacherId")
            day = entry.get("day")
            if teacher_id:
                key = (teacher_id, day)
                teacher_daily_hours[key] = teacher_daily_hours.get(key, 0) + 1
        
        for (teacher_id, day), hours in teacher_daily_hours.items():
            if hours > self.max_teacher_hours:
                conflicts.append(Conflict(
                    type=ConflictType.TEACHER_OVERLOAD,
                    severity="warning",
                    message=f"Profesor {teacher_id} tiene {hours} horas el día {day} (máximo: {self.max_teacher_hours})",
                    day=day,
                    period=0,
                    details={"teacherId": teacher_id, "hours": hours}
                ))
        
        return conflicts
    
    def _check_consecutive_same_subject(self, schedule: List[Dict]) -> List[Conflict]:
        """Check for too many consecutive periods of the same subject"""
        conflicts = []
        
        # Group by grade/section and day
        grade_day_schedule = {}
        for entry in schedule:
            key = (entry.get("gradeId"), entry.get("sectionId"), entry.get("day"))
            if key not in grade_day_schedule:
                grade_day_schedule[key] = []
            grade_day_schedule[key].append(entry)
        
        for (grade, section, day), entries in grade_day_schedule.items():
            # Sort by period
            sorted_entries = sorted(entries, key=lambda x: x.get("period", 0))
            
            consecutive_count = 1
            prev_subject = None
            
            for entry in sorted_entries:
                subject = entry.get("subjectId")
                if subject == prev_subject:
                    consecutive_count += 1
                    if consecutive_count > self.max_consecutive_same:
                        conflicts.append(Conflict(
                            type=ConflictType.CONSECUTIVE_SAME_SUBJECT,
                            severity="warning",
                            message=f"Grado {grade} tiene {consecutive_count} períodos consecutivos de la misma materia el día {day}",
                            day=day,
                            period=entry.get("period", 0),
                            details={"gradeId": grade, "subjectId": subject, "count": consecutive_count}
                        ))
                else:
                    consecutive_count = 1
                prev_subject = subject
        
        return conflicts
    
    def _check_subject_distribution(self, schedule: List[Dict]) -> List[Conflict]:
        """Check if subjects are well distributed across the week"""
        conflicts = []
        
        # Group by grade/section and subject
        subject_days = {}
        for entry in schedule:
            key = (entry.get("gradeId"), entry.get("sectionId"), entry.get("subjectId"))
            day = entry.get("day")
            if key not in subject_days:
                subject_days[key] = set()
            subject_days[key].add(day)
        
        for (grade, section, subject), days in subject_days.items():
            hours = len(days)
            if hours >= 3 and len(days) < 3:
                conflicts.append(Conflict(
                    type=ConflictType.SUBJECT_DISTRIBUTION,
                    severity="info",
                    message=f"Materia {subject} del grado {grade} podría distribuirse mejor en la semana",
                    day=0,
                    period=0,
                    details={"gradeId": grade, "subjectId": subject, "days": list(days)}
                ))
        
        return conflicts
    
    def to_dict_list(self, conflicts: List[Conflict]) -> List[Dict]:
        """Convert conflicts to dictionary format for JSON response"""
        return [
            {
                "type": c.type.value,
                "severity": c.severity,
                "message": c.message,
                "day": c.day,
                "period": c.period,
                "details": c.details
            }
            for c in conflicts
        ]


# Global validator instance
validator = ScheduleValidator()
