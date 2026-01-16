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
            ],
            'responses_en': [
                "Hello! 👋 I am your Oxford School virtual assistant. How can I help you?",
                "Good morning! I am here to assist with your questions about Oxford School and schedules. What do you need?",
                "Hi! Welcome to the Oxford platform. I can help with schedules, rules, and more. What would you like to do?",
            ],
            'suggestions_en': [
                "Generate schedule for 1st grade",
                "Show available commands",
                "Configure class schedule",
            ]
        },
        'small_talk': {
            'responses': [
                "¡De nada! Es un placer ayudarte. 😊",
                "Soy la Inteligencia Artificial del Colegio Oxford, diseñada para optimizar tu tiempo. 🚀",
                "¡Gracias! Hago lo mejor que puedo. ¿Necesitas crear algún horario?",
                "Me alegra que te guste el sistema. ¿En qué más puedo apoyarte hoy?",
            ],
            'suggestions': [
                "Generar horario",
                "Ver comandos",
            ],
            'responses_en': [
                "You're welcome! It's a pleasure to help. 😊",
                "I am the Oxford School AI, designed to optimize your time. 🚀",
                "Thanks! I do my best. Do you need to create a schedule?",
                "I'm glad you like the system. How else can I support you today?",
            ],
            'suggestions_en': [
                "Generate schedule",
                "Show commands",
            ]
        },
        'insult_handling': {
            'responses': [
                "Entiendo que puedas estar frustrado, pero estoy aquí para ayudarte. ¿Podemos intentar de nuevo?",
                "Por favor, mantengamos una comunicación respetuosa. Estoy para servirte.",
                "Lamento si algo no salió como esperabas. Dime qué necesitas y lo intentaré mejorar.",
            ],
            'suggestions': [
                "Ayuda",
                "Reportar problema",
            ],
            'responses_en': [
                "I understand you might be frustrated, but I'm here to help. Can we try again?",
                "Please, let's keep communication respectful. I am here to serve you.",
                "I'm sorry if something didn't go as expected. Tell me what you need and I'll try to improve.",
            ],
            'suggestions_en': [
                "Help",
                "Report issue",
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
            'suggestions': [],
            'responses_en': [
                """🤖 **Available Commands:**

📅 **Generate Schedules:**
• "Generate schedule for [grade]"
• "Create schedule for 3rd grade section A"

⏰ **Configure Time:**
• "Classes of 45 minutes"
• "Schedule from 7:30 to 14:00"

👨‍🏫 **Teacher Restrictions:**
• "Teacher Garcia cannot on Mondays"
• "Prof. Lopez only in mornings"

☕ **Recess:**
• "Add recess after 4th period"
• "No recess"

📊 **Others:**
• "Show configuration"
• "Validate schedule"
• "Clear restrictions"
""",
            ],
            'suggestions_en': []
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
            ],
            'success_en': [
                "✅ Schedule generated successfully for {grade}!",
                "✅ I have created the schedule for {grade}. Check the details.",
                "✅ Ready. The {grade} schedule is complete.",
            ],
            'processing_en': [
                "⏳ Generating optimal schedule for {grade}...",
                "⏳ Analyzing restrictions and creating schedule...",
                "⏳ Optimizing class distribution...",
            ],
            'missing_grade_en': [
                "Please specify the grade. Example: 'Generate schedule for 1st grade'",
                "For which grade do you want the schedule? (1st grade, 2nd grade, etc.)",
            ],
            'error_en': [
                "❌ Unable to generate schedule. {reason}",
                "❌ There was a problem: {reason}",
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
            ],
            'success_en': [
                "✅ Constraint added: {constraint}",
                "✅ Understood. I have registered that {constraint}",
                "✅ Configuration updated with the restriction.",
            ],
            'clarification_en': [
                "Could you be more specific? Example: 'Teacher Garcia cannot on Mondays'",
                "I didn't understand the restriction. Which teacher and when can't they?",
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
            ],
             'success_en': [
                "✅ Schedule configured: {start} to {end}",
                "✅ Classes will be from {start} to {end}.",
            ],
            'invalid_en': [
                "⚠️ Invalid schedule. Start time must be before end time.",
                "⚠️ Please check the times. Expected format: 7:30 to 14:00",
            ]
        },
        'set_duration': {
            'success': [
                "✅ Duración de clases: {duration} minutos",
                "✅ Cada período durará {duration} minutos.",
            ],
             'success_en': [
                "✅ Class duration: {duration} minutes",
                "✅ Each period will last {duration} minutes.",
            ]
        },
        'add_recess': {
             'success': [
                "✅ Receso agregado después del período {period}",
                "✅ Se incluirá un receso de {duration} minutos.",
            ],
            'success_en': [
                "✅ Recess added after period {period}",
                "✅ A recess of {duration} minutes will be included.",
            ]
        },
        'remove_recess': {
            'success': [
                "✅ Receso eliminado del horario.",
                "✅ El horario ya no incluye receso.",
            ],
            'success_en': [
                "✅ Recess removed from schedule.",
                "✅ The schedule no longer includes recess.",
            ]
        },
        'show_status': {
            'template': """⚙️ **Configuración Actual:**

📅 **Horario:** {start_time} - {end_time}
⏱️ **Duración de clases:** {class_duration} minutos
☕ **Receso:** {recess_status}

👨‍🏫 **Restricciones de profesores:** {restrictions_count}
📝 **Preferencias de materias:** {preferences_count}
""",
            'template_en': """⚙️ **Current Configuration:**

📅 **Schedule:** {start_time} - {end_time}
⏱️ **Class Duration:** {class_duration} minutes
☕ **Recess:** {recess_status}

👨‍🏫 **Teacher Restrictions:** {restrictions_count}
📝 **Subject Preferences:** {preferences_count}
"""
        },
        'clear': {
             'success': [
                "🗑️ Configuración restablecida a valores predeterminados.",
                "🗑️ Todas las restricciones han sido eliminadas.",
            ],
             'success_en': [
                "🗑️ Configuration reset to default.",
                "🗑️ All restrictions have been cleared.",
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
            ],
            'responses_en': [
                "🤔 I didn't understand your message. Could you rephrase it?",
                "🤔 I'm not sure what to do. Type 'help' to see commands.",
                "🤔 Could you be more specific? Try 'generate schedule' or 'help'.",
            ],
            'suggestions_en': [
                "Help",
                "Generate schedule",
                "Show configuration",
            ]
        },
        'validation_success': {
            'responses': [
                "✅ El horario es válido. No se encontraron conflictos.",
                "✅ Validación completa. El horario está libre de errores.",
            ],
            'responses_en': [
                "✅ The schedule is valid. No conflicts found.",
                "✅ Validation complete. The schedule is error-free.",
            ]
        },
        'validation_warning': {
            'template': "⚠️ El horario tiene {count} advertencia(s):\n{warnings}",
            'template_en': "⚠️ The schedule has {count} warning(s):\n{warnings}"
        },
        'validation_error': {
            'template': "❌ El horario tiene {count} conflicto(s):\n{errors}",
            'template_en': "❌ The schedule has {count} conflict(s):\n{errors}"
        },
        'request_meeting': {
            'responses': [
                "📅 He notificado a la dirección. Te contactarán pronto para coordinar la cita.",
                "✅ Solicitud de cita recibida. Secretaría revisará la disponibilidad.",
            ],
            'responses_en': [
                "📅 I have notified the administration. They will contact you soon to coordinate the meeting.",
                "✅ Meeting request received. The secretary will check availability.",
            ]
        },
        'report_issue': {
            'responses': [
                "🚨 Gracias por reportarlo. Hemos alertado al equipo de mantenimiento/disciplina.",
                "⚠️ Reporte registrado con prioridad alta. Tomaremos cartas en el asunto.",
            ],
            'responses_en': [
                "🚨 Thanks for reporting this. We have alerted the maintenance/discipline team.",
                "⚠️ Report registered with high priority. We will take action.",
            ]
        },
        'financial_query': {
            'responses': [
                "💰 Para consultas de saldo exacto, por favor revisa el portal de padres o contacta a contabilidad.",
                "💳 Los pagos se realizan en el banco o vía transferencia. ¿Necesitas los números de cuenta?",
            ],
             'responses_en': [
                "💰 For exact balance inquiries, please check the parent portal or contact accounting.",
                "💳 Payments can be made at the bank or via transfer. Do you need account numbers?",
            ]
        },
        'emotional_support': {
            'responses': [
                "💙 Respira profundo. ¡Tú puedes con esto! Un examen no define tu valor.",
                "🌟 Es normal sentir nervios. Confía en lo que has estudiado. ¡Ánimo!",
            ],
            'responses_en': [
                "💙 Take a deep breath. You got this! An exam doesn't define your worth.",
                "🌟 It's normal to feel nervous. Trust what you've studied. Cheer up!",
            ]
        },
        'study_tip': {
             'responses': [
                "🧠 **Tip de Estudio:** Usa la técnica Pomodoro (25 min estudio, 5 min descanso).",
                "📚 Trata de explicar el tema a alguien más. Si puedes enseñarlo, ¡lo sabes!",
            ],
            'responses_en': [
                "🧠 **Study Tip:** Use the Pomodoro technique (25 min study, 5 min break).",
                "📚 Try to explain the topic to someone else. If you can teach it, you know it!",
            ]
        },
        'generate_quiz': {
             'responses': [
                "📝 ¡Hora del Quiz! ¿Cuál es la capital de Francia?",
                "❓ Pregunta rápida: ¿Cuánto es 8 x 7?",
            ],
             'responses_en': [
                "📝 Quiz Time! What is the capital of France?",
                "❓ Quick question: What is 8 x 7?",
            ]
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
        success: bool = True,
        lang: str = 'es'
    ) -> Dict[str, Any]:
        """
        Generate a response based on intent and context
        
        Args:
            intent: The classified intent
            context: Additional context (config, schedule, etc.)
            entities: Extracted entities
            success: Whether the operation was successful
            lang: Language code ('es' or 'en')
            
        Returns:
            Dict with 'message' and optional 'suggestions'
        """
        context = context or {}
        entities = entities or {}
        
        template_config = self.TEMPLATES.get(intent, self.TEMPLATES['unknown'])
        
        if intent == 'greeting':
            return self._generate_greeting(template_config, lang)
        elif intent == 'help':
            return self._generate_help(template_config, lang)
        elif intent == 'generate_schedule':
            return self._generate_schedule_response(template_config, entities, success, context, lang)
        elif intent == 'add_constraint':
            return self._generate_constraint_response(template_config, entities, success, lang)
        elif intent == 'set_time':
            return self._generate_time_response(template_config, entities, success, lang)
        elif intent == 'set_duration':
            return self._generate_duration_response(template_config, entities, success, lang)
        elif intent in ['add_recess', 'remove_recess']:
            return self._generate_recess_response(intent, template_config, entities, success, lang)
        elif intent == 'show_status':
            return self._generate_status_response(template_config, context, lang)
        elif intent == 'clear':
            return self._generate_clear_response(template_config, lang)
        else:
            return self._generate_unknown_response(template_config, lang)
    
    def _get_responses(self, config: Dict, lang: str, key: str = 'responses') -> List[str]:
        """Helper to get responses in correct language"""
        if lang == 'en' and f"{key}_en" in config:
            return config[f"{key}_en"]
        return config.get(key, [])

    def _get_suggestions(self, config: Dict, lang: str) -> List[str]:
        """Helper to get suggestions in correct language"""
        if lang == 'en' and "suggestions_en" in config:
            return config["suggestions_en"]
        return config.get("suggestions", [])

    def _generate_greeting(self, config: Dict, lang: str = 'es') -> Dict[str, Any]:
        responses = self._get_responses(config, lang)
        return {
            'message': random.choice(responses),
            'suggestions': self._get_suggestions(config, lang),
        }
    
    def _generate_help(self, config: Dict, lang: str = 'es') -> Dict[str, Any]:
        responses = self._get_responses(config, lang)
        return {
            'message': responses[0],
            'suggestions': [],
        }
    
    def _generate_schedule_response(
        self, 
        config: Dict, 
        entities: Dict,
        success: bool,
        context: Dict,
        lang: str = 'es'
    ) -> Dict[str, Any]:
        grade_val = entities.get('grade', {}).get('value', 'grade')
        # Simple translation for grade fallback
        if lang == 'en' and grade_val == 'grade':
             grade_val = 'the selected grade'
        elif grade_val == 'grade':
             grade_val = 'el grado seleccionado'
             
        grade = grade_val
        
        if not entities.get('grade'):
            responses = self._get_responses(config, lang, 'missing_grade')
            suggestions = [
                "Genera horario para 1ro primaria", "Genera horario para 2do básico"
            ] if lang == 'es' else [
                "Generate schedule for 1st grade", "Generate schedule for 2nd grade"
            ]
            return {
                'message': random.choice(responses),
                'suggestions': suggestions
            }
        
        if success:
            responses = self._get_responses(config, lang, 'success')
            message = random.choice(responses).format(grade=grade)
            
            # Add summary if schedule data is available
            if 'schedule' in context:
                schedule = context['schedule']
                conflicts = context.get('conflicts', [])
                message += self._format_schedule_summary(schedule, conflicts, lang)
            
            suggestions = ['Validar horario', 'Exportar PDF', 'Modificar horario'] if lang == 'es' else ['Validate schedule', 'Export PDF', 'Modify schedule']
            return {
                'message': message,
                'suggestions': suggestions,
            }
        else:
            reason = context.get('error', 'Intenta con diferentes restricciones.' if lang == 'es' else 'Try with different restrictions.')
            responses = self._get_responses(config, lang, 'error')
            suggestions = ['Mostrar restricciones', 'Limpiar configuración'] if lang == 'es' else ['Show restrictions', 'Clear config']
            return {
                'message': random.choice(responses).format(reason=reason),
                'suggestions': suggestions
            }
    
    def _generate_constraint_response(
        self, 
        config: Dict, 
        entities: Dict,
        success: bool,
        lang: str = 'es'
    ) -> Dict[str, Any]:
        if success:
            constraint = entities.get('constraint_text', 'la restricción indicada' if lang == 'es' else 'the indicated restriction')
            responses = self._get_responses(config, lang, 'success')
            suggestions = ['Mostrar restricciones', 'Genera horario'] if lang == 'es' else ['Show restrictions', 'Generate schedule']
            return {
                'message': random.choice(responses).format(constraint=constraint),
                'suggestions': suggestions,
            }
        else:
            responses = self._get_responses(config, lang, 'clarification')
            suggestions = ['El profesor X no puede los lunes'] if lang == 'es' else ['Teacher X cannot on Mondays']
            return {
                'message': random.choice(responses),
                'suggestions': suggestions,
            }
    
    def _generate_time_response(
        self, 
        config: Dict,
        entities: Dict,
        success: bool,
        lang: str = 'es'
    ) -> Dict[str, Any]:
        times = entities.get('times', [])
        
        if len(times) >= 2 and success:
            start = times[0].get('value', '07:30')
            end = times[1].get('value', '14:00')
            responses = self._get_responses(config, lang, 'success')
            suggestions = ['Generar horario', 'Configurar duración'] if lang == 'es' else ['Generate schedule', 'Config duration']
            return {
                'message': random.choice(responses).format(start=start, end=end),
                'suggestions': suggestions,
            }
        else:
            responses = self._get_responses(config, lang, 'invalid')
            suggestions = ['Horario de 7:30 a 14:00'] if lang == 'es' else ['Schedule from 7:30 to 14:00']
            return {
                'message': random.choice(responses),
                'suggestions': suggestions,
            }
    
    def _generate_duration_response(
        self,
        config: Dict,
        entities: Dict,
        success: bool,
        lang: str = 'es'
    ) -> Dict[str, Any]:
        duration = entities.get('duration', 45)
        responses = self._get_responses(config, lang, 'success')
        suggestions = ['Generar horario'] if lang == 'es' else ['Generate schedule']
        return {
            'message': random.choice(responses).format(duration=duration),
            'suggestions': suggestions,
        }
    
    def _generate_recess_response(
        self,
        intent: str,
        config: Dict,
        entities: Dict,
        success: bool,
        lang: str = 'es'
    ) -> Dict[str, Any]:
        if intent == 'add_recess':
            period = entities.get('period', 4)
            duration = entities.get('duration', 30)
            responses = self._get_responses(config, lang, 'success')
            return {
                'message': random.choice(responses).format(
                    period=period, 
                    duration=duration
                ),
                'suggestions': ['Generar horario'] if lang == 'es' else ['Generate schedule'],
            }
        else:
            responses = self._get_responses(config, lang, 'success')
            return {
                'message': random.choice(responses),
                'suggestions': ['Generar horario'] if lang == 'es' else ['Generate schedule'],
            }
    
    def _generate_status_response(self, config: Dict, context: Dict, lang: str = 'es') -> Dict[str, Any]:
        cfg = context.get('config', {})
        
        if lang == 'es':
            recess_status = "No incluido"
            if cfg.get('includeRecess', True):
                recess_status = f"Después del período {cfg.get('recessAfterPeriod', 4)} ({cfg.get('recessDuration', 30)} min)"
            template = config['template']
            suggestions = ['Generar horario', 'Agregar restricción']
        else:
            recess_status = "Not included"
            if cfg.get('includeRecess', True):
                 recess_status = f"After period {cfg.get('recessAfterPeriod', 4)} ({cfg.get('recessDuration', 30)} min)"
            template = config.get('template_en', config['template'])
            suggestions = ['Generate schedule', 'Add constraint']
        
        message = template.format(
            start_time=cfg.get('startTime', '07:30'),
            end_time=cfg.get('endTime', '14:00'),
            class_duration=cfg.get('classDuration', 45),
            recess_status=recess_status,
            restrictions_count=len(cfg.get('teacherRestrictions', {})),
            preferences_count=len(cfg.get('subjectPreferences', {})),
        )
        
        return {
            'message': message,
            'suggestions': suggestions,
        }
    
    def _generate_clear_response(self, config: Dict, lang: str = 'es') -> Dict[str, Any]:
        responses = self._get_responses(config, lang, 'success')
        suggestions = ['Generar horario', 'Mostrar configuración'] if lang == 'es' else ['Generate schedule', 'Show config']
        return {
            'message': random.choice(responses),
            'suggestions': suggestions,
        }
    
    def _generate_unknown_response(self, config: Dict, lang: str = 'es') -> Dict[str, Any]:
        responses = self._get_responses(config, lang, 'responses')
        suggestions = self._get_suggestions(config, lang)
        return {
            'message': random.choice(responses),
            'suggestions': suggestions,
        }
    
    def _format_schedule_summary(self, schedule: List, conflicts: List, lang: str = 'es') -> str:
        """Format a summary of the generated schedule"""
        if not schedule:
            return ""
        
        total_classes = len(schedule)
        errors = sum(1 for c in conflicts if c.get('severity') == 'error')
        warnings = sum(1 for c in conflicts if c.get('severity') == 'warning')
        
        if lang == 'es':
            summary = f"\n\n📊 **Resumen:**\n"
            summary += f"• {total_classes} clases programadas\n"
            
            if errors > 0:
                summary += f"• ⚠️ {errors} conflictos encontrados\n"
            if warnings > 0:
                summary += f"• ℹ️ {warnings} advertencias\n"
            if errors == 0 and warnings == 0:
                summary += "• ✅ Sin conflictos\n"
        else:
            summary = f"\n\n📊 **Summary:**\n"
            summary += f"• {total_classes} scheduled classes\n"
            
            if errors > 0:
                summary += f"• ⚠️ {errors} conflicts found\n"
            if warnings > 0:
                summary += f"• ℹ️ {warnings} warnings\n"
            if errors == 0 and warnings == 0:
                summary += "• ✅ No conflicts\n"
        
        return summary
    
    def format_validation_result(self, is_valid: bool, conflicts: List, lang: str = 'es') -> Dict[str, Any]:
        """Format validation result as a response"""
        if is_valid and not conflicts:
            template = self.TEMPLATES['validation_success']
            responses = self._get_responses(template, lang, 'responses')
            suggestions = ['Exportar horario', 'Modificar'] if lang == 'es' else ['Export schedule', 'Modify']
            return {
                'message': random.choice(responses),
                'suggestions': suggestions,
            }
        
        errors = [c for c in conflicts if c.get('severity') == 'error']
        warnings = [c for c in conflicts if c.get('severity') == 'warning']
        
        if errors:
            error_text = "\n".join(f"• {e.get('message', 'Error')}" for e in errors[:5])
            template_conf = self.TEMPLATES['validation_error']
            template = template_conf.get('template_en', template_conf['template']) if lang == 'en' else template_conf['template']
            message = template.format(
                count=len(errors),
                errors=error_text
            )
        else:
            warning_text = "\n".join(f"• {w.get('message', 'Advertencia')}" for w in warnings[:5])
            template_conf = self.TEMPLATES['validation_warning']
            template = template_conf.get('template_en', template_conf['template']) if lang == 'en' else template_conf['template']
            message = template.format(
                count=len(warnings),
                warnings=warning_text
            )
        
        suggestions = ['Regenerar horario', 'Modificar restricciones'] if lang == 'es' else ['Regenerate schedule', 'Modify restrictions']
        return {
            'message': message,
            'suggestions': suggestions,
        }


# Global instance
response_generator = ResponseGenerator()
