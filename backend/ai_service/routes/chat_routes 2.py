"""
Oxford AI Personal Assistant - Enhanced Chat Endpoint
Endpoint especializado para el asistente personal de las apps móviles
"""

from fastapi import APIRouter, HTTPException
from pydantic import BaseModel
from typing import Optional, List
import random
from datetime import datetime

chat_router = APIRouter(prefix="/chat", tags=["Personal Assistant"])

class ChatMessage(BaseModel):
    message: str
    user_id: Optional[str] = None
    role: str = "student"  # student, teacher, admin, parent
    context: Optional[dict] = None

class ChatResponse(BaseModel):
    response: str
    suggestions: List[str] = []
    action: Optional[str] = None
    data: Optional[dict] = None

# Base de conocimiento del asistente
KNOWLEDGE_BASE = {
    "horarios": {
        "keywords": ["horario", "hora", "clase", "cuando", "tiempo", "lunes", "martes", "miercoles", "jueves", "viernes"],
        "responses": [
            "📅 Tu horario del día:\n\n• 7:30 - Matemáticas (Aula 5A)\n• 8:15 - Comunicación (Aula 5A)\n• 9:00 - Ciencias (Lab 2)\n• 9:45 - Recreo\n• 10:15 - Historia (Aula 5A)\n• 11:00 - Inglés (Aula 5A)\n\n¿Necesitas más detalles?",
        ]
    },
    "tareas": {
        "keywords": ["tarea", "deberes", "pendiente", "entregar", "asignacion", "trabajo"],
        "responses": [
            "📝 Tienes 3 tareas pendientes:\n\n1️⃣ **Matemáticas** - Ejercicios p.45\n   📅 Entrega: Mañana\n\n2️⃣ **Comunicación** - Lectura Cap. 5\n   📅 Entrega: En 3 días\n\n3️⃣ **Ciencias** - Proyecto final\n   📅 Entrega: En 1 semana\n\n💡 ¿Quieres que te ayude con alguna?",
        ]
    },
    "notas": {
        "keywords": ["nota", "calificacion", "promedio", "resultado", "examen", "evaluacion", "cuanto saque"],
        "responses": [
            "📊 Tus calificaciones actuales:\n\n• Matemáticas: **88** ✅\n• Comunicación: **92** ⭐\n• Ciencias: **85** ✅\n• Historia: **78** ⚠️\n• Inglés: **90** ⭐\n\n📈 **Promedio General: 86.6**\n\n💪 ¡Vas muy bien! Historia necesita un poco más de atención.",
        ]
    },
    "asistencia": {
        "keywords": ["asistencia", "falta", "ausencia", "llegue tarde", "presente"],
        "responses": [
            "📋 Tu asistencia este bimestre:\n\n✅ Días asistidos: 42\n⚠️ Tardanzas: 2\n❌ Faltas: 1 (justificada)\n\n📊 **Porcentaje: 97.8%**\n\n¡Excelente asistencia! Sigue así 💪",
        ]
    },
    "pagos": {
        "keywords": ["pago", "colegiatura", "mensualidad", "deuda", "cuanto debo", "saldo"],
        "responses": [
            "💰 Estado de cuenta:\n\n✅ Diciembre 2025: Pagado\n✅ Noviembre 2025: Pagado\n❌ Enero 2026: Q 750.00 (Pendiente)\n❌ Febrero 2026: Q 750.00 (Pendiente)\n\n💵 **Total pendiente: Q 1,500.00**\n\n📅 Fecha límite: 15 de cada mes",
        ]
    },
    "ayuda_tarea": {
        "keywords": ["ayuda", "como hago", "no entiendo", "explicame", "dificil", "problema"],
        "responses": [
            "🧠 ¡Claro que te ayudo! Dime:\n\n1️⃣ ¿De qué materia es la tarea?\n2️⃣ ¿Cuál es el tema específico?\n3️⃣ ¿Qué parte no entiendes?\n\nTambién puedo recomendarte recursos de estudio o videos explicativos. 📚",
        ]
    },
    "motivacion": {
        "keywords": ["motivacion", "animo", "triste", "cansado", "aburrido", "estresado", "no puedo"],
        "responses": [
            "💪 ¡Tú puedes!\n\n\"El éxito no es definitivo, el fracaso no es fatal: lo que cuenta es el coraje de continuar.\"\n- Winston Churchill\n\n🌟 Recuerda:\n• Cada día es una nueva oportunidad\n• Los pequeños pasos llevan a grandes logros\n• Descansar también es importante\n\n¿Hay algo específico que te preocupa? Estoy aquí para escucharte. 💚",
            "🌈 ¡Hey! Todos tenemos días difíciles.\n\n✨ Tips para sentirte mejor:\n1. Respira profundo 3 veces\n2. Toma un vaso de agua\n3. Camina 5 minutos\n4. Habla con alguien de confianza\n\n¡Eres capaz de más de lo que crees! 🚀",
        ]
    },
    "consejos_estudio": {
        "keywords": ["consejo", "tip", "estudiar", "aprender", "memoria", "concentrar"],
        "responses": [
            "📚 Tips para estudiar mejor:\n\n🧠 **Técnica Pomodoro:**\n• 25 min de estudio\n• 5 min de descanso\n• Repite 4 veces, luego 15 min de descanso\n\n📝 **Para memorizar:**\n• Lee en voz alta\n• Haz resúmenes cortos\n• Enseña a alguien más\n\n💡 ¿Quieres más tips específicos para alguna materia?",
        ]
    },
    "quien_eres": {
        "keywords": ["quien eres", "que eres", "como te llamas", "tu nombre", "eres humano", "robot"],
        "responses": [
            "🤖 ¡Hola! Soy **Oxford AI**, tu asistente personal académico.\n\n✨ Estoy aquí para:\n• Ayudarte con tus tareas\n• Mostrarte notas y horarios\n• Darte tips de estudio\n• Motivarte cuando lo necesites\n\n🧠 Uso Inteligencia Artificial para entenderte mejor cada día.\n\n¿En qué puedo ayudarte?",
        ]
    },
}

# Respuestas por defecto
DEFAULT_RESPONSES = [
    "🤔 Hmm, no estoy seguro de entender. ¿Puedes reformular tu pregunta?\n\nPuedo ayudarte con:\n• Tareas y notas\n• Horarios\n• Pagos\n• Tips de estudio\n\n¿Qué te gustaría saber?",
    "💭 Interesante pregunta. Déjame pensar...\n\nMientras tanto, dime si necesitas algo de:\n📚 Académico | 💰 Pagos | 📅 Horarios | 💪 Motivación",
]

GREETINGS = {
    "keywords": ["hola", "buenos dias", "buenas tardes", "buenas noches", "hey", "que tal", "saludos"],
    "responses": [
        "¡Hola! 👋 ¿Cómo estás?\n\nSoy tu asistente personal Oxford. ¿En qué puedo ayudarte hoy?",
        "¡Hey! 🌟 Qué gusto verte por aquí.\n\n¿Necesitas ayuda con tareas, notas, o algo más?",
        "¡Buenas! 😊 Aquí estoy para lo que necesites.\n\n¿Qué te gustaría hacer hoy?",
    ]
}

FAREWELLS = {
    "keywords": ["adios", "chao", "hasta luego", "bye", "nos vemos", "me voy"],
    "responses": [
        "¡Hasta pronto! 👋 Que te vaya excelente. ¡Éxito en todo!",
        "¡Nos vemos! 🌟 Recuerda que siempre estaré aquí cuando me necesites.",
        "¡Cuídate mucho! 💚 ¡Hasta la próxima!",
    ]
}

THANKS = {
    "keywords": ["gracias", "muchas gracias", "te lo agradezco", "thanks"],
    "responses": [
        "¡De nada! 🙏 Es un placer ayudarte. ¿Algo más?",
        "¡Para eso estoy! 😊 Si necesitas algo más, me dices.",
        "¡Con gusto! ✨ Cuenta conmigo siempre.",
    ]
}

def find_intent(message: str) -> tuple[str, list]:
    """Encuentra la intención del mensaje"""
    message_lower = message.lower()
    
    # Saludos
    for keyword in GREETINGS["keywords"]:
        if keyword in message_lower:
            return "greeting", random.choice(GREETINGS["responses"])
    
    # Despedidas
    for keyword in FAREWELLS["keywords"]:
        if keyword in message_lower:
            return "farewell", random.choice(FAREWELLS["responses"])
    
    # Agradecimientos
    for keyword in THANKS["keywords"]:
        if keyword in message_lower:
            return "thanks", random.choice(THANKS["responses"])
    
    # Buscar en knowledge base
    best_match = None
    best_score = 0
    
    for intent, data in KNOWLEDGE_BASE.items():
        score = sum(1 for kw in data["keywords"] if kw in message_lower)
        if score > best_score:
            best_score = score
            best_match = intent
    
    if best_match and best_score > 0:
        return best_match, random.choice(KNOWLEDGE_BASE[best_match]["responses"])
    
    return "unknown", random.choice(DEFAULT_RESPONSES)

def get_suggestions(intent: str, role: str) -> List[str]:
    """Genera sugerencias basadas en la intención"""
    base_suggestions = {
        "greeting": ["Ver mis tareas", "¿Cuál es mi promedio?", "Ver mi horario"],
        "tareas": ["Ayúdame con matemáticas", "Ver mis notas", "Tips de estudio"],
        "notas": ["Cómo puedo mejorar", "Ver tareas pendientes", "Mi asistencia"],
        "horarios": ["Ver tareas", "Ver notas", "¿Tengo pagos pendientes?"],
        "pagos": ["Ver mis notas", "Ver mi horario", "Necesito ayuda"],
        "asistencia": ["Ver mis notas", "Ver tareas", "Ver pagos"],
        "motivacion": ["Tips de estudio", "Ver mis notas", "Ayuda con tarea"],
        "unknown": ["Ver mis tareas", "Mi promedio", "Mis pagos", "Mi horario"],
    }
    return base_suggestions.get(intent, base_suggestions["unknown"])

@chat_router.post("/message", response_model=ChatResponse)
async def chat_message(msg: ChatMessage):
    """
    Endpoint principal del asistente personal para las apps móviles.
    Procesa mensajes de chat y genera respuestas contextuales.
    """
    if not msg.message.strip():
        raise HTTPException(status_code=400, detail="Mensaje vacío")
    
    # Procesar mensaje
    intent, response = find_intent(msg.message)
    
    # Generar sugerencias
    suggestions = get_suggestions(intent, msg.role)
    
    # Determinar si hay acción asociada
    action = None
    data = None
    
    if intent == "tareas":
        action = "navigate_tasks"
    elif intent == "notas":
        action = "navigate_grades"
    elif intent == "horarios":
        action = "navigate_schedule"
    elif intent == "pagos":
        action = "navigate_payments"
    elif intent == "asistencia":
        action = "navigate_attendance"
    
    return ChatResponse(
        response=response,
        suggestions=suggestions,
        action=action,
        data=data
    )

@chat_router.get("/suggestions")
async def get_quick_suggestions(role: str = "student"):
    """Retorna sugerencias rápidas para el usuario"""
    
    if role == "student":
        return {
            "suggestions": [
                {"text": "📝 Ver mis tareas", "action": "message", "value": "Ver mis tareas pendientes"},
                {"text": "📊 Mis notas", "action": "message", "value": "Cuáles son mis notas"},
                {"text": "📅 Mi horario", "action": "message", "value": "Ver mi horario del día"},
                {"text": "💰 Mis pagos", "action": "message", "value": "Tengo pagos pendientes"},
                {"text": "📋 Mi asistencia", "action": "message", "value": "Cómo va mi asistencia"},
                {"text": "💡 Tips de estudio", "action": "message", "value": "Dame consejos para estudiar"},
            ]
        }
    elif role == "teacher":
        return {
            "suggestions": [
                {"text": "📋 Ver mis clases", "action": "message", "value": "Ver mis clases del día"},
                {"text": "📝 Cargar notas", "action": "message", "value": "Necesito cargar notas"},
                {"text": "👥 Mis estudiantes", "action": "message", "value": "Ver mis estudiantes"},
                {"text": "⚠️ Estudiantes en riesgo", "action": "message", "value": "Estudiantes con bajo rendimiento"},
                {"text": "📅 Mi horario", "action": "message", "value": "Ver mi horario"},
            ]
        }
    else:
        return {
            "suggestions": [
                {"text": "❓ ¿Qué puedo hacer?", "action": "message", "value": "Ayuda"},
            ]
        }

@chat_router.get("/health")
async def chat_health():
    return {"status": "ok", "service": "Oxford AI Chat", "timestamp": datetime.now().isoformat()}
