from fastapi import APIRouter
from pydantic import BaseModel
import random
from datetime import datetime
from services_container import nlp_engine, assistant_factory, schedule_scorer

core_router = APIRouter()

class CommandRequest(BaseModel):
    text: str = ""
    role: str = "admin"

@core_router.get("/")
def home():
    return {"status": "online", "service": "Corpo Oxford AI (FastAPI/Refactored)", "version": "3.3"}

@core_router.post("/process-command")
def process_cmd(req: CommandRequest):
    text = req.text
    user_role = req.role
    
    res = nlp_engine.process(text)
    
    response = {
        "intent": res.intent.name,
        "confidence": res.intent.confidence,
        "response_text": "Procesado.",
        "entities": {e.type: e.value for e in res.entities},
        "should_generate": False,
        "action": None,
        "interaction_id": None
    }
    
    intent = response["intent"]
    
    # === GREETINGS & CONVERSATIONAL ===
    if intent == "greeting":
        response["response_text"] = assistant_factory.get_response("greeting", user_role, {"isa": "95.0"})
    
    elif intent == "how_are_you":
        responses = [
            "¡Estoy funcionando al 100%! 🤖 Todos los sistemas operativos. ¿Y tú? ¿En qué puedo apoyarte?",
            "¡Excelente! Procesando a toda velocidad. ☕ ¿Qué te gustaría hacer hoy?",
            "¡Muy bien, gracias por preguntar! 💚 Aquí estoy para lo que necesites.",
        ]
        response["response_text"] = random.choice(responses)
    
    elif intent == "thanks":
        responses = [
            "¡De nada! 🙏 Es un placer ayudarte.",
            "¡Para servirte! Si necesitas algo más, aquí estaré. 😊",
            "¡Con gusto! Recuerda que siempre puedes contar conmigo. ✨",
        ]
        response["response_text"] = random.choice(responses)
    
    elif intent == "goodbye":
        responses = [
            "¡Hasta pronto! 👋 Que tengas un excelente día.",
            "¡Nos vemos! Recuerda que aquí estaré cuando me necesites. 🌟",
            "¡Cuídate mucho! ¡Éxito en todo! 💪",
        ]
        response["response_text"] = random.choice(responses)
    
    # === HELP & ABOUT ===
    elif intent == "help":
        response["response_text"] = """🧠 **Soy tu Asistente Personal Académico.** Puedo ayudarte con:

📊 **Consultas Académicas:**
• "Ver mis materias" - Lista tus asignaturas
• "Ver mis estudiantes" - Lista de alumnos
• "Cargar notas" - Ir a gestión de calificaciones

📝 **Para Estudiantes:**
• "Mis notas" - Ver calificaciones
• "Tareas pendientes" - Ver deberes
• "Mi asistencia" - Registro de faltas

🗓️ **Horarios:**
• "Ver mi horario" - Tu horario semanal
• "Generar horarios" - Crear con IA (Admin)

💡 **Bienestar:**
• "Dame un consejo" - Tips de estudio
• "Necesito motivación" - Apoyo emocional
• "Estoy estresado" - Ayuda

📊 **Analítica:**
• "Estudiantes en riesgo" - Dashboard de alertas
• "Salud institucional" - Métricas ISA

¿Qué te gustaría hacer?"""
    
    elif intent == "about_ai":
        response["response_text"] = """🤖 **Soy el Asistente Personal de Oxford Sistema**

• **Nombre:** Asistente Oxford AI
• **Versión:** 2.0 Beta (Enterprise)
• **Creador:** Equipo de Desarrollo Oxford Sistema
• **Tecnología:** Machine Learning + NLP en Español
• **Capacidades:** Generación de horarios, análisis de riesgo, aprendizaje continuo

Fui diseñado para hacer tu vida académica más fácil. 🎓"""
    
    # === SCHEDULE ===
    elif intent == "generate_schedule":
        response["response_text"] = "🚀 Iniciando el generador de horarios con IA...\n\nEsta función usa algoritmos genéticos para optimizar la distribución."
        response["should_generate"] = True
        response["action"] = "start_generation"
    
    elif intent == "view_schedule":
        response["response_text"] = "📅 Consultando tu horario actual..."
        response["action"] = "fetch_schedule"
    
    # === ACADEMIC - TEACHER ===
    elif intent == "my_subjects":
        response["response_text"] = "📚 Consultando tus materias asignadas..."
        response["action"] = "fetch_subjects"
    
    elif intent == "my_students":
        response["response_text"] = "👨‍🎓 Consultando la lista de tus estudiantes..."
        response["action"] = "fetch_students"
    
    elif intent == "student_grades":
        response["response_text"] = "📊 Consultando las calificaciones del grupo..."
        response["action"] = "fetch_group_grades"
    
    elif intent == "load_grades":
        response["response_text"] = "📝 Te dirijo al módulo de **Carga de Notas**.\n\nAhí puedes registrar calificaciones por materia y bimestre."
        response["action"] = "navigate_grades"
    
    # === ACADEMIC - STUDENT ===
    elif intent == "check_homework":
        response["response_text"] = "📋 Consultando tu agenda de tareas pendientes..."
        response["action"] = "fetch_homework"
    
    elif intent == "check_grades":
        response["response_text"] = "📊 Buscando tus calificaciones actuales..."
        response["action"] = "fetch_grades"
    
    elif intent == "check_attendance":
        response["response_text"] = "📅 Consultando tu registro de asistencia..."
        response["action"] = "fetch_attendance"
    
    # === EMOTIONAL SUPPORT ===
    elif intent == "emotional_support":
        msgs = [
            "🌿 ¡Respira profundo! Eres capaz de todo. Un paso a la vez.\n\n💡 Tip: Tómate 5 minutos, cierra los ojos y respira lento.",
            "💪 Sé que las cosas pueden sentirse abrumadoras, pero lo estás haciendo bien.\n\n🧘 Recuerda: está bien pedir ayuda cuando la necesites.",
            "🌟 El esfuerzo de hoy construye el éxito de mañana. ¡Tú puedes!\n\n💚 Tu bienestar es importante. Si te sientes sobrecargado, habla con alguien de confianza.",
        ]
        response["response_text"] = random.choice(msgs)
    
    elif intent == "motivation":
        msgs = [
            "💪 **¡Tú puedes!**\n\n\"El éxito no es la clave de la felicidad. La felicidad es la clave del éxito.\" - Albert Schweitzer",
            "🌟 **Cada día es una nueva oportunidad.**\n\n\"No te compares con otros. Compárate con quien eras ayer.\"",
            "🚀 **El único límite eres tú mismo.**\n\n\"La disciplina es el puente entre las metas y los logros.\" - Jim Rohn",
            "🔥 **¡No te rindas!**\n\n\"El fracaso es el condimento que le da sabor al éxito.\" - Truman Capote",
        ]
        response["response_text"] = random.choice(msgs)
    
    elif intent == "study_tip":
        tips = [
            "💡 **Técnica Pomodoro:** 25 min de estudio, 5 min de descanso. ¡Aumenta la concentración!",
            "📚 **Método Feynman:** Explica el tema como si lo enseñaras a un niño. Si puedes, lo entiendes.",
            "🧠 **Repaso Espaciado:** Repasa el material 1 día, 3 días, 1 semana y 1 mes después.",
            "📝 **Mapas Mentales:** Dibuja conexiones entre conceptos. Tu cerebro recuerda mejor lo visual.",
            "🎧 **Música Instrumental:** Música sin letra ayuda a concentrarse. Prueba lo-fi o música clásica.",
            "💧 **Hidratación:** Tu cerebro es 75% agua. Mantente hidratado para pensar mejor.",
        ]
        response["response_text"] = random.choice(tips)
    
    # === RISK & ANALYTICS ===
    elif intent == "check_risk":
        response["response_text"] = "🔍 Analizando base de datos de estudiantes para detectar riesgos académicos..."
        response["action"] = "show_risk_dashboard"
    
    elif intent == "teacher_burnout":
        response["response_text"] = "📊 Analizando tu carga de trabajo y distribución de horarios..."
        response["action"] = "analyze_burnout"
    
    elif intent == "institutional_health":
        result = schedule_scorer.calculate_isa([], {'avg_burnout': 15}, {})
        response["response_text"] = f"""📊 **Índice de Salud Académica (ISA)**

🏆 **Puntuación Global:** {result['isa_score']}/100 ({result['level']})

**Desglose:**
• 👨‍🏫 Bienestar Docente: {result['breakdown']['teacher_wellness']}%
• 👨‍🎓 Balance Estudiantil: {result['breakdown']['student_balance']}%
• 🏢 Eficiencia: {result['breakdown']['efficiency']}%
• 📈 Estabilidad: {result['breakdown']['stability']}%

{result['trend']}"""
    
    # === ADMINISTRATIVE ===
    elif intent == "report_issue":
        response["response_text"] = "😔 Lamento que tengas un inconveniente.\n\n📝 Por favor describe el problema y lo reportaremos al equipo de soporte."
        response["action"] = "open_feedback_modal"
    
    elif intent == "suggestion":
        response["response_text"] = "💡 ¡Nos encanta mejorar! ¿Cuál es tu sugerencia?"
        response["action"] = "open_feedback_modal"
    
    # === FUN & MISC ===
    elif intent == "joke":
        jokes = [
            "😂 ¿Por qué los programadores prefieren el frío?\n\nPorque odian los bugs... ¡y el calor los atrae!",
            "🤣 ¿Cuál es el colmo de un matemático?\n\n¡Tener problemas personales!",
            "😄 ¿Qué le dice una iguana a su hermana gemela?\n\n¡Somos iguanitas!",
            "😆 ¿Por qué el libro de matemáticas estaba triste?\n\n¡Porque tenía demasiados problemas!",
        ]
        response["response_text"] = random.choice(jokes)
    
    elif intent == "time":
        now = datetime.now()
        response["response_text"] = f"🕐 Son las **{now.strftime('%H:%M')}** del **{now.strftime('%d de %B de %Y')}**."
    
    elif intent == "random_fact":
        facts = [
            "🧠 **¿Sabías que?** El cerebro humano puede almacenar aproximadamente 2.5 petabytes de información.",
            "🌍 **Dato Curioso:** Guatemala tiene 37 volcanes, 3 de ellos activos.",
            "📚 **Fun Fact:** La palabra 'escuela' viene del griego 'scholé' que significa 'tiempo libre'.",
            "🦋 **Increíble:** Las mariposas pueden ver colores que los humanos no pueden percibir.",
        ]
        response["response_text"] = random.choice(facts)
    
    elif intent == "weather":
        response["response_text"] = "☁️ No tengo acceso directo al clima, pero puedes consultar **weather.com** o tu app del teléfono.\n\n💡 Tip: ¡Siempre lleva sombrilla en época de lluvias!"
    
    # === UNKNOWN / FALLBACK ===
    elif intent == "unknown" or response["confidence"] < 0.4:
        fallbacks = [
            f"🤔 No estoy seguro de entender \"{text[:50]}...\".\n\nIntenta preguntarme sobre:\n• Tus materias\n• Tus horarios\n• Tips de estudio\n\nO escribe **\"ayuda\"** para ver todo lo que puedo hacer.",
            "Hmm, no encontré una respuesta para eso. 🔍\n\n¿Podrías reformularlo? O escribe **\"ayuda\"** para ver opciones.",
        ]
        response["response_text"] = random.choice(fallbacks)
    
    return response
