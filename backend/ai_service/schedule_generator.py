"""
Optimized Schedule Generator
=============================
Generates schedules using constraint satisfaction with backtracking
"""

from typing import List, Dict, Any, Optional, Tuple
from dataclasses import dataclass
import random
from copy import deepcopy

from conflict_detector import ScheduleValidator, Conflict


@dataclass
class ScheduleEntry:
    """A single schedule entry"""
    day: int
    period: int
    teacher_id: Optional[int]
    subject_id: int
    grade_id: int
    section_id: Optional[int]
    classroom: Optional[str]


@dataclass
class GenerationResult:
    """Result of schedule generation"""
    success: bool
    schedule: List[Dict]
    conflicts: List[Dict]
    message: str
    attempts: int


class ScheduleGenerator:
    """Generates optimal schedules using constraint satisfaction"""
    
    def __init__(
        self,
        days: List[int] = None,
        periods_per_day: int = 8,
        include_recess: bool = True,
        recess_after_period: int = 4
    ):
        self.days = days or [1, 2, 3, 4, 5]  # Mon-Fri
        self.periods_per_day = periods_per_day
        self.include_recess = include_recess
        self.recess_after_period = recess_after_period
        self.validator = ScheduleValidator()
        self.max_attempts = 100
    
    def generate(
        self,
        subjects: List[Dict],
        teachers: List[Dict],
        grade_id: int,
        section_id: Optional[int] = None,
        constraints: Optional[Dict] = None
    ) -> GenerationResult:
        """
        Generate an optimal schedule
        
        Args:
            subjects: List of subjects with {id, name, hoursPerWeek}
            teachers: List of teachers with {id, name, subjects: [subjectIds]}
            grade_id: Grade to generate schedule for
            section_id: Optional section
            constraints: Optional additional constraints:
                - teacher_unavailable: {teacherId: [(day, period), ...]}
                - preferred_slots: {subjectId: [(day, period), ...]}
                - fixed_slots: [{day, period, subjectId, teacherId}]
        
        Returns:
            GenerationResult with schedule and any conflicts
        """
        constraints = constraints or {}
        
        # Build subject-teacher mapping
        subject_teachers = self._build_subject_teachers(subjects, teachers)
        
        # Calculate required slots
        required_slots = []
        for subject in subjects:
            hours = subject.get("hoursPerWeek", 1)
            for _ in range(hours):
                required_slots.append(subject["id"])
        
        # Get available slots
        available_slots = self._get_available_slots(constraints)
        
        # Generate schedule
        for attempt in range(self.max_attempts):
            schedule = self._try_generate(
                required_slots=required_slots.copy(),
                available_slots=available_slots.copy(),
                subject_teachers=subject_teachers,
                grade_id=grade_id,
                section_id=section_id,
                constraints=constraints
            )
            
            if schedule:
                is_valid, conflicts = self.validator.validate(schedule)
                
                if is_valid:
                    return GenerationResult(
                        success=True,
                        schedule=schedule,
                        conflicts=self.validator.to_dict_list(conflicts),
                        message="Horario generado exitosamente",
                        attempts=attempt + 1
                    )
                
                # If only warnings, accept the schedule
                if not any(c.severity == "error" for c in conflicts):
                    return GenerationResult(
                        success=True,
                        schedule=schedule,
                        conflicts=self.validator.to_dict_list(conflicts),
                        message="Horario generado con advertencias",
                        attempts=attempt + 1
                    )
        
        # Failed after max attempts
        return GenerationResult(
            success=False,
            schedule=[],
            conflicts=[],
            message=f"No se pudo generar un horario válido después de {self.max_attempts} intentos",
            attempts=self.max_attempts
        )
    
    def _try_generate(
        self,
        required_slots: List[int],
        available_slots: List[Tuple[int, int]],
        subject_teachers: Dict[int, List[int]],
        grade_id: int,
        section_id: Optional[int],
        constraints: Dict
    ) -> Optional[List[Dict]]:
        """Attempt to generate a schedule"""
        schedule = []
        random.shuffle(required_slots)
        random.shuffle(available_slots)
        
        # Apply fixed slots first
        fixed_slots = constraints.get("fixed_slots", [])
        for fixed in fixed_slots:
            schedule.append({
                "day": fixed["day"],
                "period": fixed["period"],
                "teacherId": fixed.get("teacherId"),
                "subjectId": fixed["subjectId"],
                "gradeId": grade_id,
                "sectionId": section_id,
            })
            
            # Remove from available
            slot = (fixed["day"], fixed["period"])
            if slot in available_slots:
                available_slots.remove(slot)
            
            # Remove subject from required if present
            if fixed["subjectId"] in required_slots:
                required_slots.remove(fixed["subjectId"])
        
        # Fill remaining slots
        teacher_assignments = {}  # Track teacher assignments per slot
        
        for subject_id in required_slots:
            if not available_slots:
                return None  # No more slots
            
            # Get preferred slots for this subject
            preferred = constraints.get("preferred_slots", {}).get(subject_id, [])
            
            # Try preferred slots first
            slot_found = False
            for slot in preferred:
                if slot in available_slots:
                    teacher_id = self._assign_teacher(
                        subject_id, slot, subject_teachers, 
                        teacher_assignments, constraints
                    )
                    if teacher_id is not None:
                        schedule.append({
                            "day": slot[0],
                            "period": slot[1],
                            "teacherId": teacher_id,
                            "subjectId": subject_id,
                            "gradeId": grade_id,
                            "sectionId": section_id,
                        })
                        available_slots.remove(slot)
                        self._record_teacher_assignment(teacher_assignments, teacher_id, slot)
                        slot_found = True
                        break
            
            if not slot_found:
                # Try any available slot
                for slot in available_slots.copy():
                    teacher_id = self._assign_teacher(
                        subject_id, slot, subject_teachers,
                        teacher_assignments, constraints
                    )
                    if teacher_id is not None:
                        schedule.append({
                            "day": slot[0],
                            "period": slot[1],
                            "teacherId": teacher_id,
                            "subjectId": subject_id,
                            "gradeId": grade_id,
                            "sectionId": section_id,
                        })
                        available_slots.remove(slot)
                        self._record_teacher_assignment(teacher_assignments, teacher_id, slot)
                        slot_found = True
                        break
            
            if not slot_found:
                return None  # Couldn't place subject
        
        return schedule if schedule else None
    
    def _get_available_slots(self, constraints: Dict) -> List[Tuple[int, int]]:
        """Get list of available (day, period) slots"""
        slots = []
        for day in self.days:
            for period in range(1, self.periods_per_day + 1):
                # Skip recess period
                if self.include_recess and period == self.recess_after_period + 1:
                    continue
                slots.append((day, period))
        return slots
    
    def _build_subject_teachers(
        self, 
        subjects: List[Dict], 
        teachers: List[Dict]
    ) -> Dict[int, List[int]]:
        """Build mapping of subject ID to list of teacher IDs who can teach it"""
        mapping = {}
        for subject in subjects:
            subject_id = subject["id"]
            mapping[subject_id] = []
            
            for teacher in teachers:
                teacher_subjects = teacher.get("subjects", [])
                if subject_id in teacher_subjects or subject["name"] in teacher_subjects:
                    mapping[subject_id].append(teacher["id"])
        
        return mapping
    
    def _assign_teacher(
        self,
        subject_id: int,
        slot: Tuple[int, int],
        subject_teachers: Dict[int, List[int]],
        teacher_assignments: Dict,
        constraints: Dict
    ) -> Optional[int]:
        """Find an available teacher for this subject and slot"""
        available_teachers = subject_teachers.get(subject_id, [])
        
        if not available_teachers:
            return None  # No teachers for this subject
        
        unavailable = constraints.get("teacher_unavailable", {})
        
        random.shuffle(available_teachers)
        
        for teacher_id in available_teachers:
            # Check if teacher is unavailable
            teacher_unavail = unavailable.get(teacher_id, [])
            if slot in teacher_unavail:
                continue
            
            # Check if teacher already assigned to this slot
            if slot in teacher_assignments:
                if teacher_id in teacher_assignments[slot]:
                    continue
            
            return teacher_id
        
        # No available teacher found, return first one anyway (will create conflict)
        return available_teachers[0] if available_teachers else None
    
    def _record_teacher_assignment(
        self,
        assignments: Dict,
        teacher_id: int,
        slot: Tuple[int, int]
    ):
        """Record that a teacher is assigned to a slot"""
        if slot not in assignments:
            assignments[slot] = set()
        assignments[slot].add(teacher_id)


# Global generator instance
generator = ScheduleGenerator()
