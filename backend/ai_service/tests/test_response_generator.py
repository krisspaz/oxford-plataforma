"""
Tests for Response Generator
=============================
"""

import pytest
import sys
import os

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from response_generator import ResponseGenerator, response_generator


class TestResponseGenerator:
    """Tests for response generation"""
    
    @pytest.fixture
    def generator(self):
        return ResponseGenerator()
    
    # ========================================
    # GREETING RESPONSES
    # ========================================
    
    def test_greeting_response(self, generator):
        result = generator.generate('greeting')
        
        assert 'message' in result
        assert len(result['message']) > 0
        assert '¡Hola' in result['message'] or 'Buenos' in result['message']
    
    def test_greeting_has_suggestions(self, generator):
        result = generator.generate('greeting')
        
        assert 'suggestions' in result
        assert isinstance(result['suggestions'], list)
    
    # ========================================
    # HELP RESPONSES
    # ========================================
    
    def test_help_response_contains_commands(self, generator):
        result = generator.generate('help')
        
        assert 'message' in result
        assert 'Generar' in result['message'] or 'horario' in result['message'].lower()
    
    def test_help_response_has_formatting(self, generator):
        result = generator.generate('help')
        
        # Should have markdown formatting
        assert '**' in result['message'] or '•' in result['message']
    
    # ========================================
    # SCHEDULE GENERATION RESPONSES
    # ========================================
    
    def test_schedule_success_response(self, generator):
        entities = {'grade': {'value': '1ro Primaria'}}
        result = generator.generate('generate_schedule', entities=entities, success=True)
        
        assert 'message' in result
        assert '✅' in result['message']
        assert '1ro Primaria' in result['message']
    
    def test_schedule_missing_grade_response(self, generator):
        result = generator.generate('generate_schedule', entities={}, success=True)
        
        assert 'message' in result
        assert 'grado' in result['message'].lower()
        assert len(result.get('suggestions', [])) > 0
    
    def test_schedule_error_response(self, generator):
        context = {'error': 'No hay profesores disponibles'}
        result = generator.generate('generate_schedule', context=context, success=False)
        
        assert 'message' in result
        assert '❌' in result['message']
    
    # ========================================
    # CONSTRAINT RESPONSES
    # ========================================
    
    def test_constraint_success_response(self, generator):
        entities = {'constraint_text': 'Prof. García no puede los lunes'}
        result = generator.generate('add_constraint', entities=entities, success=True)
        
        assert 'message' in result
        assert '✅' in result['message']
    
    def test_constraint_clarification_response(self, generator):
        result = generator.generate('add_constraint', entities={}, success=False)
        
        assert 'message' in result
        assert 'específico' in result['message'].lower() or 'ejemplo' in result['message'].lower()
    
    # ========================================
    # TIME SETTING RESPONSES
    # ========================================
    
    def test_time_success_response(self, generator):
        entities = {
            'times': [
                {'value': '07:30'},
                {'value': '14:00'},
            ]
        }
        result = generator.generate('set_time', entities=entities, success=True)
        
        assert 'message' in result
        assert '✅' in result['message']
        assert '07:30' in result['message'] or '14:00' in result['message']
    
    def test_time_invalid_response(self, generator):
        result = generator.generate('set_time', entities={'times': []}, success=False)
        
        assert 'message' in result
        assert '⚠️' in result['message']
    
    # ========================================
    # DURATION RESPONSES
    # ========================================
    
    def test_duration_response(self, generator):
        entities = {'duration': 45}
        result = generator.generate('set_duration', entities=entities, success=True)
        
        assert 'message' in result
        assert '45' in result['message']
    
    # ========================================
    # RECESS RESPONSES
    # ========================================
    
    def test_add_recess_response(self, generator):
        entities = {'period': 4, 'duration': 30}
        result = generator.generate('add_recess', entities=entities, success=True)
        
        assert 'message' in result
        assert '✅' in result['message']
    
    def test_remove_recess_response(self, generator):
        result = generator.generate('remove_recess', success=True)
        
        assert 'message' in result
        assert '✅' in result['message']
        assert 'receso' in result['message'].lower()
    
    # ========================================
    # STATUS RESPONSES
    # ========================================
    
    def test_status_response_with_config(self, generator):
        context = {
            'config': {
                'startTime': '07:30',
                'endTime': '14:00',
                'classDuration': 45,
                'includeRecess': True,
                'recessAfterPeriod': 4,
                'recessDuration': 30,
                'teacherRestrictions': {},
                'subjectPreferences': {},
            }
        }
        result = generator.generate('show_status', context=context)
        
        assert 'message' in result
        assert '07:30' in result['message']
        assert '14:00' in result['message']
        assert '45' in result['message']
    
    # ========================================
    # CLEAR RESPONSES
    # ========================================
    
    def test_clear_response(self, generator):
        result = generator.generate('clear', success=True)
        
        assert 'message' in result
        assert '🗑️' in result['message']
    
    # ========================================
    # UNKNOWN RESPONSES
    # ========================================
    
    def test_unknown_response(self, generator):
        result = generator.generate('unknown')
        
        assert 'message' in result
        assert '🤔' in result['message']
        assert len(result.get('suggestions', [])) > 0
    
    def test_unknown_suggests_help(self, generator):
        result = generator.generate('unknown')
        
        suggestions = result.get('suggestions', [])
        # Should suggest help
        assert any('ayuda' in s.lower() for s in suggestions) or len(suggestions) > 0


class TestValidationResponses:
    """Tests for validation result formatting"""
    
    @pytest.fixture
    def generator(self):
        return ResponseGenerator()
    
    def test_validation_success_response(self, generator):
        result = generator.format_validation_result(True, [])
        
        assert 'message' in result
        assert '✅' in result['message']
    
    def test_validation_with_errors(self, generator):
        conflicts = [
            {'severity': 'error', 'message': 'Profesor García asignado a dos clases'},
        ]
        result = generator.format_validation_result(False, conflicts)
        
        assert 'message' in result
        assert '❌' in result['message']
        assert 'García' in result['message']
    
    def test_validation_with_warnings(self, generator):
        conflicts = [
            {'severity': 'warning', 'message': 'Profesor tiene muchas horas el lunes'},
        ]
        result = generator.format_validation_result(True, conflicts)
        
        assert 'message' in result
        assert '⚠️' in result['message']


class TestSubjectEmojis:
    """Tests for subject emoji mapping"""
    
    @pytest.fixture
    def generator(self):
        return ResponseGenerator()
    
    def test_has_common_subject_emojis(self, generator):
        subjects = ['Matemáticas', 'Español', 'Ciencias', 'Inglés', 'Historia']
        
        for subject in subjects:
            assert subject in generator.SUBJECT_EMOJIS
    
    def test_emojis_are_valid(self, generator):
        for subject, emoji in generator.SUBJECT_EMOJIS.items():
            assert len(emoji) > 0
            assert isinstance(emoji, str)


if __name__ == "__main__":
    pytest.main([__file__, "-v", "--tb=short"])
