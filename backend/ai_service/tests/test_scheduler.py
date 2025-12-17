"""
Tests for Schedule Generator and Conflict Detection
====================================================
"""

import pytest
import sys
import os

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from conflict_detector import ScheduleValidator, Conflict, ConflictType
from schedule_generator import ScheduleGenerator, GenerationResult


class TestScheduleValidator:
    """Tests for conflict detection"""
    
    @pytest.fixture
    def validator(self):
        return ScheduleValidator()
    
    # ========================================
    # TEACHER DOUBLE BOOKING
    # ========================================
    
    def test_detect_teacher_double_booking(self, validator):
        """Teacher assigned to two classes at the same time"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1},
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 2, "gradeId": 2},  # Conflict!
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        assert not is_valid
        assert any(c.type == ConflictType.TEACHER_DOUBLE_BOOKING for c in conflicts)
    
    def test_no_conflict_different_periods(self, validator):
        """Same teacher, different periods - no conflict"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1},
            {"day": 1, "period": 2, "teacherId": 1, "subjectId": 2, "gradeId": 2},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        teacher_conflicts = [c for c in conflicts if c.type == ConflictType.TEACHER_DOUBLE_BOOKING]
        assert len(teacher_conflicts) == 0
    
    def test_no_conflict_different_days(self, validator):
        """Same teacher, same period, different days - no conflict"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1},
            {"day": 2, "period": 1, "teacherId": 1, "subjectId": 2, "gradeId": 2},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        teacher_conflicts = [c for c in conflicts if c.type == ConflictType.TEACHER_DOUBLE_BOOKING]
        assert len(teacher_conflicts) == 0
    
    # ========================================
    # ROOM DOUBLE BOOKING
    # ========================================
    
    def test_detect_room_double_booking(self, validator):
        """Two classes in same room at same time"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "classroom": "101"},
            {"day": 1, "period": 1, "teacherId": 2, "subjectId": 2, "gradeId": 2, "classroom": "101"},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        assert not is_valid
        assert any(c.type == ConflictType.ROOM_DOUBLE_BOOKING for c in conflicts)
    
    def test_no_room_conflict_different_rooms(self, validator):
        """Different rooms - no conflict"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "classroom": "101"},
            {"day": 1, "period": 1, "teacherId": 2, "subjectId": 2, "gradeId": 2, "classroom": "102"},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        room_conflicts = [c for c in conflicts if c.type == ConflictType.ROOM_DOUBLE_BOOKING]
        assert len(room_conflicts) == 0
    
    # ========================================
    # GRADE DOUBLE BOOKING
    # ========================================
    
    def test_detect_grade_double_booking(self, validator):
        """Same grade has two classes at same time"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1},
            {"day": 1, "period": 1, "teacherId": 2, "subjectId": 2, "gradeId": 1, "sectionId": 1},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        assert not is_valid
        assert any(c.type == ConflictType.GRADE_DOUBLE_BOOKING for c in conflicts)
    
    # ========================================
    # TEACHER OVERLOAD
    # ========================================
    
    def test_detect_teacher_overload(self, validator):
        """Teacher with too many hours in one day"""
        # 8 periods for same teacher = overload (max is 6)
        schedule = [
            {"day": 1, "period": i, "teacherId": 1, "subjectId": 1, "gradeId": i}
            for i in range(1, 9)
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        overload_conflicts = [c for c in conflicts if c.type == ConflictType.TEACHER_OVERLOAD]
        assert len(overload_conflicts) > 0
        assert overload_conflicts[0].severity == "warning"
    
    # ========================================
    # CONSECUTIVE SAME SUBJECT
    # ========================================
    
    def test_detect_consecutive_same_subject(self, validator):
        """Same subject for 3+ consecutive periods"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1},
            {"day": 1, "period": 2, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1},
            {"day": 1, "period": 3, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        consecutive_conflicts = [c for c in conflicts if c.type == ConflictType.CONSECUTIVE_SAME_SUBJECT]
        assert len(consecutive_conflicts) > 0
    
    # ========================================
    # VALID SCHEDULE
    # ========================================
    
    def test_valid_schedule(self, validator):
        """A properly constructed schedule should have no errors"""
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1, "classroom": "101"},
            {"day": 1, "period": 2, "teacherId": 2, "subjectId": 2, "gradeId": 1, "sectionId": 1, "classroom": "102"},
            {"day": 1, "period": 3, "teacherId": 1, "subjectId": 1, "gradeId": 2, "sectionId": 1, "classroom": "101"},
            {"day": 2, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1, "sectionId": 1, "classroom": "101"},
        ]
        
        is_valid, conflicts = validator.validate(schedule)
        
        errors = [c for c in conflicts if c.severity == "error"]
        assert len(errors) == 0


class TestScheduleGenerator:
    """Tests for schedule generation"""
    
    @pytest.fixture
    def generator(self):
        return ScheduleGenerator()
    
    @pytest.fixture
    def sample_subjects(self):
        return [
            {"id": 1, "name": "Matemáticas", "hoursPerWeek": 5},
            {"id": 2, "name": "Español", "hoursPerWeek": 5},
            {"id": 3, "name": "Ciencias", "hoursPerWeek": 3},
        ]
    
    @pytest.fixture
    def sample_teachers(self):
        return [
            {"id": 1, "name": "García", "subjects": [1]},  # Teaches Math
            {"id": 2, "name": "López", "subjects": [2]},   # Teaches Spanish
            {"id": 3, "name": "Martínez", "subjects": [3]},  # Teaches Science
        ]
    
    def test_generate_simple_schedule(self, generator, sample_subjects, sample_teachers):
        """Generate a basic schedule"""
        result = generator.generate(
            subjects=sample_subjects,
            teachers=sample_teachers,
            grade_id=1,
            section_id=1
        )
        
        assert isinstance(result, GenerationResult)
        assert result.attempts > 0
    
    def test_generate_with_fixed_slots(self, generator, sample_subjects, sample_teachers):
        """Generate schedule with fixed slots"""
        constraints = {
            "fixed_slots": [
                {"day": 1, "period": 1, "subjectId": 1, "teacherId": 1}
            ]
        }
        
        result = generator.generate(
            subjects=sample_subjects,
            teachers=sample_teachers,
            grade_id=1,
            constraints=constraints
        )
        
        if result.success:
            # Check fixed slot is present
            fixed_found = any(
                s.get("day") == 1 and s.get("period") == 1 and s.get("subjectId") == 1
                for s in result.schedule
            )
            assert fixed_found
    
    def test_generate_with_teacher_unavailable(self, generator, sample_subjects, sample_teachers):
        """Generate schedule with teacher unavailability"""
        constraints = {
            "teacher_unavailable": {
                1: [(1, 1), (1, 2), (1, 3)]  # Teacher 1 unavailable on Monday morning
            }
        }
        
        result = generator.generate(
            subjects=sample_subjects,
            teachers=sample_teachers,
            grade_id=1,
            constraints=constraints
        )
        
        # Should still generate (may use different slots or different teachers)
        assert result.attempts > 0
    
    def test_generation_result_has_conflicts_list(self, generator, sample_subjects, sample_teachers):
        """Result should include conflicts list"""
        result = generator.generate(
            subjects=sample_subjects,
            teachers=sample_teachers,
            grade_id=1
        )
        
        assert hasattr(result, 'conflicts')
        assert isinstance(result.conflicts, list)
    
    def test_generation_with_no_teachers(self, generator, sample_subjects):
        """Handle case with no teachers"""
        result = generator.generate(
            subjects=sample_subjects,
            teachers=[],
            grade_id=1
        )
        
        # Should fail gracefully or return empty schedule
        assert result.attempts > 0


class TestValidatorSerialization:
    """Test conflict serialization"""
    
    def test_conflict_to_dict(self):
        """Conflicts should serialize to dict properly"""
        validator = ScheduleValidator()
        
        schedule = [
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 1, "gradeId": 1},
            {"day": 1, "period": 1, "teacherId": 1, "subjectId": 2, "gradeId": 2},
        ]
        
        _, conflicts = validator.validate(schedule)
        serialized = validator.to_dict_list(conflicts)
        
        assert isinstance(serialized, list)
        if serialized:
            assert 'type' in serialized[0]
            assert 'severity' in serialized[0]
            assert 'message' in serialized[0]


if __name__ == "__main__":
    pytest.main([__file__, "-v", "--tb=short"])
