"""
AI Response Generator
======================
Generates natural language responses in Spanish based on intent and context
"""

from typing import Dict, List, Any, Optional
import random


class ResponseGenerator:
    """Generates contextual responses in Spanish"""
    
    # Response templates by intent
    TEMPLATES = {
        'greeting': {
            'responses': [
                "¡Hola! 👋 Soy tu asistente virtual del Colegio Oxford. ¿En qué puedo ayudarte?",
                "¡Buenos días! Estoy aquí para ayudarte con tus dudas sobre el Colegio Oxford y tus horarios. ¿Qué necesitas?",
                "¡Hola! Bienvenido a la plataforma Oxford. Puedo ayudarte con horarios, reglamentos y más. ¿Qué deseas hacer?",
            ],
            'suggestions': [
                "Genera horario para 1ro primaria",
                "Mostrar comandos disponibles",
                "Configurar horario de clases",
            ]
        },
        'help': {
            'responses': [
                """🤖 **Comandos Disponibles:**

📅 **Generar Horarios:**
• "Genera horario para [grado]"
• "Crea horario de 3ro primaria sección A"

⏰ **Configurar Tiempo:**
• "Clases de 45 minutos"
• "Horario de 7:30 a 14:00"

👨‍🏫 **Restricciones de Profesores:**
• "El profesor García no puede los lunes"
• "Prof. López solo en las mañanas"

☕ **Receso:**
• "Agrega receso después de la 4ta hora"
• "Sin receso"

📊 **Otros:**
• "Mostrar configuración"
• "Validar horario"
• "Limpiar restricciones"
""",
            ],
            'suggestions': []
        },
        'generate_schedule': {
            'success': [
                "✅ ¡Horario generado exitosamente para {grade}!",
                "✅ He creado el horario para {grade}. Revisa los detalles.",
                "✅ Listo. El horario de {grade} está completo.",
            ],
            'processing': [
                "⏳ Generando horario óptimo para {grade}...",
                "⏳ Analizando restricciones y creando horario...",
                "⏳ Optimizando distribución de clases...",
            ],
            'missing_grade': [
                "Por favor, especifica el grado. Ejemplo: 'Genera horario para 1ro primaria'",
                "¿Para qué grado deseas el horario? (1ro primaria, 2do básico, etc.)",
            ],
            'error': [
                "❌ No fue posible generar el horario. {reason}",
                "❌ Hubo un problema: {reason}",
            ]
        },
        'add_constraint': {
            'success': [
                "✅ Restricción agregada: {constraint}",
                "✅ Entendido. He registrado que {constraint}",
                "✅ Configuración actualizada con la restricción.",
            ],
            'clarification': [
                "¿Podrías ser más específico? Ejemplo: 'El profesor García no puede los lunes'",
                "No entendí la restricción. ¿Cuál es el profesor y cuándo no puede?",
            ]
        },
        'set_time': {
            'success': [
                "✅ Horario configurado: {start} a {end}",
                "✅ Las clases serán de {start} a {end}.",
            ],
            'invalid': [
                "⚠️ El horario no es válido. La hora de inicio debe ser antes que la de fin.",
                "⚠️ Por favor, verifica las horas. Formato esperado: 7:30 a 14:00",
            ]
        },
        'set_duration': {
            'success': [
                "✅ Duración de clases: {duration} minutos",
                "✅ Cada período durará {duration} minutos.",
            ]
        },
        'add_recess': {
            'success': [
                "✅ Receso agregado después del período {period}",
                "✅ Se incluirá un receso de {duration} minutos.",
            ]
        },
        'remove_recess': {
            'success': [
                "✅ Receso eliminado del horario.",
                "✅ El horario ya no incluye receso.",
            ]
        },
        'show_status': {
            'template': """⚙️ **Configuración Actual:**

📅 **Horario:** {start_time} - {end_time}
⏱️ **Duración de clases:** {class_duration} minutos
☕ **Receso:** {recess_status}

👨‍🏫 **Restricciones de profesores:** {restrictions_count}
📝 **Preferencias de materias:** {preferences_count}
"""
        },
        'clear': {
            'success': [
                "🗑️ Configuración restablecida a valores predeterminados.",
                "🗑️ Todas las restricciones han sido eliminadas.",
            ]
        },
        'unknown': {
            'responses': [
                "🤔 No entendí tu mensaje. ¿Podrías reformularlo?",
                "🤔 No estoy seguro de qué hacer. Escribe 'ayuda' para ver los comandos.",
                "🤔 ¿Podrías ser más específico? Intenta con 'genera horario' o 'ayuda'.",
            ],
            'suggestions': [
                "Ayuda",
                "Genera horario",
                "Mostrar configuración",
            ]
        },
        'validation_success': {
            'responses': [
                "✅ El horario es válido. No se encontraron conflictos.",
                "✅ Validación completa. El horario está libre de errores.",
            ]
        },
        'validation_warning': {
            'template': "⚠️ El horario tiene {count} advertencia(s):\n{warnings}"
        },
        'validation_error': {
            'template': "❌ El horario tiene {count} conflicto(s):\n{errors}"
        }
    }
    
    # Emoji mapping for subjects
    SUBJECT_EMOJIS = {
        'Matemáticas': '🔢',
        'Español': '📚',
        'Ciencias': '🔬',
        'Inglés': '🇬🇧',
        'Historia': '🏛️',
        'Ed. Física': '⚽',
        'Arte': '🎨',
        'Música': '🎵',
        'Computación': '💻',
        'Química': '⚗️',
        'Biología': '🧬',
        'Geografía': '🌍',
        'Literatura': '📖',
        'Filosofía': '🤔',
    }
    
    def __init__(self):
        pass
    
    def generate(
        self,
        intent: str,
        context: Dict[str, Any] = None,
        entities: Dict[str, Any] = None,
        success: bool = True
    ) -> Dict[str, Any]:
        """
        Generate a response based on intent and context
        
        Args:
            intent: The classified intent
            context: Additional context (config, schedule, etc.)
            entities: Extracted entities
            success: Whether the operation was successful
            
        Returns:
            Dict with 'message' and optional 'suggestions'
        """
        context = context or {}
        entities = entities or {}
        
        template_config = self.TEMPLATES.get(intent, self.TEMPLATES['unknown'])
        
        if intent == 'greeting':
            return self._generate_greeting(template_config)
        elif intent == 'help':
            return self._generate_help(template_config)
        elif intent == 'generate_schedule':
            return self._generate_schedule_response(template_config, entities, success, context)
        elif intent == 'add_constraint':
            return self._generate_constraint_response(template_config, entities, success)
        elif intent == 'set_time':
            return self._generate_time_response(template_config, entities, success)
        elif intent == 'set_duration':
            return self._generate_duration_response(template_config, entities, success)
        elif intent in ['add_recess', 'remove_recess']:
            return self._generate_recess_response(intent, template_config, entities, success)
        elif intent == 'show_status':
            return self._generate_status_response(template_config, context)
        elif intent == 'clear':
            return self._generate_clear_response(template_config)
        else:
            return self._generate_unknown_response(template_config)
    
    def _generate_greeting(self, config: Dict) -> Dict[str, Any]:
        return {
            'message': random.choice(config['responses']),
            'suggestions': config.get('suggestions', []),
        }
    
    def _generate_help(self, config: Dict) -> Dict[str, Any]:
        return {
            'message': config['responses'][0],
            'suggestions': [],
        }
    
    def _generate_schedule_response(
        self, 
        config: Dict, 
        entities: Dict,
        success: bool,
        context: Dict
    ) -> Dict[str, Any]:
        grade = entities.get('grade', {}).get('value', 'el grado seleccionado')
        
        if not entities.get('grade'):
            return {
                'message': random.choice(config['missing_grade']),
                'suggestions': [
                    "Genera horario para 1ro primaria",
                    "Genera horario para 2do básico",
                ],
            }
        
        if success:
            message = random.choice(config['success']).format(grade=grade)
            
            # Add summary if schedule data is available
            if 'schedule' in context:
                schedule = context['schedule']
                conflicts = context.get('conflicts', [])
                message += self._format_schedule_summary(schedule, conflicts)
            
            return {
                'message': message,
                'suggestions': ['Validar horario', 'Exportar PDF', 'Modificar horario'],
            }
        else:
            reason = context.get('error', 'Intenta con diferentes restricciones.')
            return {
                'message': random.choice(config['error']).format(reason=reason),
                'suggestions': ['Mostrar restricciones', 'Limpiar configuración'],
            }
    
    def _generate_constraint_response(
        self, 
        config: Dict, 
        entities: Dict,
        success: bool
    ) -> Dict[str, Any]:
        if success:
            constraint = entities.get('constraint_text', 'la restricción indicada')
            return {
                'message': random.choice(config['success']).format(constraint=constraint),
                'suggestions': ['Mostrar restricciones', 'Genera horario'],
            }
        else:
            return {
                'message': random.choice(config['clarification']),
                'suggestions': ['El profesor X no puede los lunes'],
            }
    
    def _generate_time_response(
        self, 
        config: Dict,
        entities: Dict,
        success: bool
    ) -> Dict[str, Any]:
        times = entities.get('times', [])
        
        if len(times) >= 2 and success:
            start = times[0].get('value', '07:30')
            end = times[1].get('value', '14:00')
            return {
                'message': random.choice(config['success']).format(start=start, end=end),
                'suggestions': ['Generar horario', 'Configurar duración'],
            }
        else:
            return {
                'message': random.choice(config['invalid']),
                'suggestions': ['Horario de 7:30 a 14:00'],
            }
    
    def _generate_duration_response(
        self,
        config: Dict,
        entities: Dict,
        success: bool
    ) -> Dict[str, Any]:
        duration = entities.get('duration', 45)
        return {
            'message': random.choice(config['success']).format(duration=duration),
            'suggestions': ['Generar horario'],
        }
    
    def _generate_recess_response(
        self,
        intent: str,
        config: Dict,
        entities: Dict,
        success: bool
    ) -> Dict[str, Any]:
        if intent == 'add_recess':
            period = entities.get('period', 4)
            duration = entities.get('duration', 30)
            return {
                'message': random.choice(config['success']).format(
                    period=period, 
                    duration=duration
                ),
                'suggestions': ['Generar horario'],
            }
        else:
            return {
                'message': random.choice(config['success']),
                'suggestions': ['Generar horario'],
            }
    
    def _generate_status_response(self, config: Dict, context: Dict) -> Dict[str, Any]:
        cfg = context.get('config', {})
        
        recess_status = "No incluido"
        if cfg.get('includeRecess', True):
            recess_status = f"Después del período {cfg.get('recessAfterPeriod', 4)} ({cfg.get('recessDuration', 30)} min)"
        
        message = config['template'].format(
            start_time=cfg.get('startTime', '07:30'),
            end_time=cfg.get('endTime', '14:00'),
            class_duration=cfg.get('classDuration', 45),
            recess_status=recess_status,
            restrictions_count=len(cfg.get('teacherRestrictions', {})),
            preferences_count=len(cfg.get('subjectPreferences', {})),
        )
        
        return {
            'message': message,
            'suggestions': ['Generar horario', 'Agregar restricción'],
        }
    
    def _generate_clear_response(self, config: Dict) -> Dict[str, Any]:
        return {
            'message': random.choice(config['success']),
            'suggestions': ['Generar horario', 'Mostrar configuración'],
        }
    
    def _generate_unknown_response(self, config: Dict) -> Dict[str, Any]:
        return {
            'message': random.choice(config['responses']),
            'suggestions': config.get('suggestions', []),
        }
    
    def _format_schedule_summary(self, schedule: List, conflicts: List) -> str:
        """Format a summary of the generated schedule"""
        if not schedule:
            return ""
        
        total_classes = len(schedule)
        errors = sum(1 for c in conflicts if c.get('severity') == 'error')
        warnings = sum(1 for c in conflicts if c.get('severity') == 'warning')
        
        summary = f"\n\n📊 **Resumen:**\n"
        summary += f"• {total_classes} clases programadas\n"
        
        if errors > 0:
            summary += f"• ⚠️ {errors} conflictos encontrados\n"
        if warnings > 0:
            summary += f"• ℹ️ {warnings} advertencias\n"
        if errors == 0 and warnings == 0:
            summary += "• ✅ Sin conflictos\n"
        
        return summary
    
    def format_validation_result(self, is_valid: bool, conflicts: List) -> Dict[str, Any]:
        """Format validation result as a response"""
        if is_valid and not conflicts:
            return {
                'message': random.choice(self.TEMPLATES['validation_success']['responses']),
                'suggestions': ['Exportar horario', 'Modificar'],
            }
        
        errors = [c for c in conflicts if c.get('severity') == 'error']
        warnings = [c for c in conflicts if c.get('severity') == 'warning']
        
        if errors:
            error_text = "\n".join(f"• {e.get('message', 'Error')}" for e in errors[:5])
            message = self.TEMPLATES['validation_error']['template'].format(
                count=len(errors),
                errors=error_text
            )
        else:
            warning_text = "\n".join(f"• {w.get('message', 'Advertencia')}" for w in warnings[:5])
            message = self.TEMPLATES['validation_warning']['template'].format(
                count=len(warnings),
                warnings=warning_text
            )
        
        return {
            'message': message,
            'suggestions': ['Regenerar horario', 'Modificar restricciones'],
        }


# Global instance
response_generator = ResponseGenerator()
