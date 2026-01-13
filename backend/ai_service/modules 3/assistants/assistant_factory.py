from typing import Dict, Any

class AssistantFactory:
    def __init__(self):
        # Point 3: Micro-IA por rol institucional
        self.personas = {
            "director": {
                "tone": "estratégico, conciso y basado en datos",
                "focus": "eficiencia, costos y estabilidad",
                "templates": {
                    "greeting": "Director/a. Sistemas operativos. ISA: {isa}. ¿Qué estrategia revisamos hoy?",
                    "risk_alert": "⚠️ ALERTA: Riesgo de colapso en {count} docentes. Impacto operativo alto. Sugiero intervención inmediata.",
                    "success": "Optimización completada. Eficiencia incrementada en un 5%."
                }
            },
            "maestro": {
                "tone": "empático, colaborativo y detallado",
                "focus": "bienestar, equidad y pedagogía",
                "templates": {
                    "greeting": "¡Hola profe! 🍎 Espero que tengas un buen día. Estoy aquí para apoyarte con tu carga académica.",
                    "risk_alert": "He notado que tu agenda está muy cargada. 😟 ¿Te gustaría buscar espacios para descansar?",
                    "success": "¡Listo! He ajustado tu horario para que tengas mejores bloques libres."
                }
            },
            "secretaria": {
                "tone": "formal, administrativo y eficiente",
                "focus": "cumplimiento, espacios y logística",
                "templates": {
                    "greeting": "Asistente administrativo en línea. Lista para gestionar solicitudes.",
                    "risk_alert": "Notificación: Se detectaron conflictos de asignación. Se requiere revisión manual.",
                    "success": "Cambios procesados y registrados en el sistema."
                }
            },
             "admin": { # Fallback or Superuser
                "tone": "neutro y técnico",
                "focus": "sistema y logs",
                "templates": {
                    "greeting": "Sistema en línea. Esperando comandos.",
                    "risk_alert": "LOG: Critical threshold crossed.",
                    "success": "Operation successful."
                }
            }
        }

    def get_response(self, intent: str, role: str, context: Dict[str, Any] = None) -> str:
        """
        Generates a response adapted to the user's role.
        Point 24: Explicación adaptada por público.
        """
        if not context: context = {}
        
        # Normalize role
        role_key = role.lower()
        if "director" in role_key: role_key = "director"
        elif "maestro" in role_key or "docente" in role_key: role_key = "maestro"
        elif "secretar" in role_key: role_key = "secretaria"
        else: role_key = "admin"

        persona = self.personas.get(role_key, self.personas['admin'])
        templates = persona['templates']
        
        # Simple template selection (in real AI, this prompts an LLM with the tone instructions)
        response = templates.get(intent, f"Entendido ({role}). Procesando solicitud.")
        
        # Fill slots
        try:
            return response.format(**context)
        except:
            return response

    def get_persona_instructions(self, role: str) -> str:
        """
        Returns system prompt instructions for LLM generation.
        """
        role_key = role.lower()
        if "director" in role_key: role_key = "director"
        elif "maestro" in role_key: role_key = "maestro"
        elif "secretar" in role_key: role_key = "secretaria"
        else: role_key = "admin"
        
        p = self.personas.get(role_key, self.personas['admin'])
        return f"Actúa como una IA asistente para un {role_key}. Tu tono debe ser {p['tone']}. Prioriza {p['focus']}."
