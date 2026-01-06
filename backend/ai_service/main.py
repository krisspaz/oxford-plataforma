from flask import Flask, request, jsonify
from datetime import timedelta, datetime, time, date
import json
import sqlite3
from functools import wraps
from werkzeug.security import generate_password_hash, check_password_hash
from jose import JWTError, jwt

# Local imports
from database import db
from nlp_engine import nlp_engine
from preference_learner import PreferenceLearner
from risk_analyzer import RiskAnalyzer
from teacher_analyzer import TeacherAnalyzer
from schedule_scorer import ScheduleScorer
from institutional_memory import InstitutionalMemory
from rule_contract import RuleContract
from assistant_factory import AssistantFactory
from negotiation_engine import NegotiationEngine
from decision_logger import DecisionLogger
from legal_defense import LegalDefenseGen
from ethics_validator import EthicsValidator
from simulation_engine import SimulationEngine
from localization_adapter import LocalizationAdapter
from flask_cors import CORS

# Enterprise AI modules
try:
    from learning_engine import learning_engine, FeedbackAction, FeedbackReason
    from expert_engine import expert_engine, StructuredResponse
    from memory import memory
    from knowledge_base import knowledge_base
    from rule_engine import rule_engine
    ENTERPRISE_AI_ENABLED = True
except ImportError as e:
    print(f"Enterprise AI modules not fully loaded: {e}")
    ENTERPRISE_AI_ENABLED = False


app = Flask(__name__)
CORS(app)

# Security
SECRET_KEY = "SECRET_SUPER_SECURE_KEY_FOR_DEMO"
ALGORITHM = "HS256"

# --- UTILS ---
def verify_password(plain_password, hashed_password):
    return check_password_hash(hashed_password, plain_password)

def get_password_hash(password):
    return generate_password_hash(password)

def create_access_token(data: dict):
    to_encode = data.copy()
    expire = datetime.utcnow() + timedelta(minutes=60)
    to_encode.update({"exp": expire.timestamp()})
    return jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)

def token_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        token = None
        if 'Authorization' in request.headers:
            t = request.headers['Authorization']
            if t.startswith("Bearer "): token = t.split(" ")[1]
        
        if not token: return jsonify({'message': 'Token missing'}), 401
        
        try:
            jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        except:
            return jsonify({'message': 'Token invalid'}), 401
            
        return f(*args, **kwargs)
    return decorated

def get_current_user():
    token = request.headers['Authorization'].split(" ")[1]
    data = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
    return db.query("SELECT * FROM users WHERE username = ?", (data['sub'],), one=True)

# --- SEEDING ---
def seed_data():
    db.init_db()
    
    # Check if users exist
    if not db.query("SELECT 1 FROM users LIMIT 1", one=True):
        print("Seeding Users...")
        users = [
            ("admin", "admin123", "administrador"),
            ("maestro1", "pass1", "maestro"),
            ("alumno1", "pass2", "alumno")
        ]
        for u, p, r in users:
            db.execute("INSERT INTO users (username, hashed_password, role) VALUES (?, ?, ?)", 
                       (u, get_password_hash(p), r))

        print("Seeding References...")
        # Grades & Subjects
        schema = {
            "pre_primaria": ["matematicas", "lectura", "arte", "musica"],
            "primaria": ["matematicas", "ciencias", "historia", "lengua"],
            "secundaria": ["matematicas", "quimica", "fisica", "historia", "lengua"]
        }
        for g_name, subs in schema.items():
            g_id = db.execute("INSERT INTO grades (name, level) VALUES (?, ?)", (g_name, g_name.title()))
            for s in subs:
                db.execute("INSERT INTO subjects (name, grade_id) VALUES (?, ?)", (s.title(), g_id))
        
        # Teachers
        teachers = [
            ("Ana", ["matematicas", "ciencias"], ["primaria", "secundaria"]),
            ("Luis", ["arte", "musica"], ["pre_primaria", "primaria"]),
            ("Carla", ["quimica"], ["secundaria"])
        ]
        for name, mats, lvls in teachers:
            db.execute("INSERT INTO teachers (nombre, materias_json, niveles_json) VALUES (?, ?, ?)",
                       (name, json.dumps(mats), json.dumps(lvls)))
        print("Seed Complete.")

with app.app_context():
    seed_data()

# --- ROUTES ---

@app.route("/")
def home():
    return jsonify({"status": "online", "service": "Corpo Oxford AI (Flask/SQLite)", "version": "3.1"})

@app.route("/auth/login", methods=["POST"])
def login():
    data = request.json or request.form
    u = db.query("SELECT * FROM users WHERE username = ?", (data.get('username'),), one=True)
    
    if not u or not verify_password(data.get('password'), u['hashed_password']):
        return jsonify({"detail": "Bad credentials"}), 400
        
    token = create_access_token({"sub": u['username'], "rol": u['role']})
    return jsonify({"access_token": token, "token_type": "bearer"})

@app.route("/me", methods=["GET"])
@token_required
def me():
    u = get_current_user()
    return jsonify({"id": u['id'], "username": u['username'], "rol": u['role']})

@app.route("/process-command", methods=["POST"])
def process_cmd():
    import random
    from datetime import datetime
    
    text = request.json.get('text', '')
    user_role = request.json.get('role', 'admin')
    user_id = request.json.get('user_id', 'anonymous')
    
    res = nlp_engine.process(text)
    
    response = {
        "intent": res.intent.name,
        "confidence": res.intent.confidence,
        "response_text": "Procesado.",
        "entities": {e.type: e.value for e in res.entities},
        "should_generate": False,
        "action": None,
        "interaction_id": None  # For feedback tracking
    }
    
    intent = response["intent"]
    
    # === GREETINGS & CONVERSATIONAL ===
    if intent == "greeting":
        greetings = [
            f"¡Hola! 👋 Soy tu Asistente Personal. ¿En qué puedo ayudarte hoy?",
            f"¡Buen día! 🌟 Estoy aquí para apoyarte. ¿Qué necesitas?",
            f"¡Hola! ¿Listo para hacer tu día más productivo? 💼",
        ]
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
    
    return jsonify(response)

@app.route("/horario", methods=["POST"])
@token_required
def create_horario():
    d = request.json
    try:
        # Resolve FKs
        g = db.query("SELECT id FROM grades WHERE id = ?", (d['grado_id'],), one=True)
        s = db.query("SELECT id FROM subjects WHERE id = ?", (d['materia_id'],), one=True)
        t = db.query("SELECT id FROM teachers WHERE id = ?", (d['teacher_id'],), one=True)
        
        if not g or not s or not t: return jsonify({"error": "Invalid IDs"}), 400
        
        new_id = db.execute('''
            INSERT INTO schedules (fecha, dia_semana, hora_inicio, hora_fin, lugar, grade_id, subject_id, teacher_id, participantes_json)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (d.get('fecha'), d['dia_semana'], d['hora_inicio'], d['hora_fin'], d.get('lugar'), g['id'], s['id'], t['id'], json.dumps(d.get('participantes', []))))
        
        return jsonify({"message": "Horario creado", "id": new_id})
    except Exception as e:
        return jsonify({"error": str(e)}), 400

# --- GENERATION ENDPOINT ---
from genetic_scheduler import genetic_optimizer
import hashlib

RESPONSE_CACHE = {}

@app.route("/generate-schedule", methods=["POST"])
def generate_schedule():
    """
    Main endpoint called by Symfony to run the Genetic Algorithm
    """
    data = request.json
    
    # SHA-256 hash of the input to serve as cache key
    data_str = json.dumps(data, sort_keys=True)
    cache_key = hashlib.sha256(data_str.encode()).hexdigest()
    
    if cache_key in RESPONSE_CACHE:
        print("⚡ Cache Hit!")
        return jsonify(RESPONSE_CACHE[cache_key])
    
    # 1. Extract Data
    config = data.get('config', {})
    teachers = data.get('teachers', [])
    subjects = data.get('subjects', [])
    constraints = data.get('constraints', [])
    
    grades = list(set([s.split('_')[0] for s in subjects])) # Hacky deduction or pass grades explicitly
    if 'grades' in data:
        grades = data['grades']
    elif not grades:
        grades = ['1ro Basico', '2do Basico', '3ro Basico']

    # 2. Inject Learned Preferences (Phase 2 & 3 integration)
    # Fetch preferences for each teacher and add to constraints
    learned_constraints = []
    for t in teachers:
        # Assuming teacher dict has 'id' or we map 'name' back to ID.
        # For this prototype we assume teacher['id'] exists
        if 'id' in t:
            learned = learner.get_teacher_constraints(t['id'])
            learned_constraints.extend(learned)
            
    # Merge manual constraints with learned constraints
    final_constraints = constraints + learned_constraints

    # 3. Run Optimization
    result = genetic_optimizer.generate(
        grades=grades,
        subjects=subjects,
        teachers=teachers,
        constraints=final_constraints,
        population_size=config.get('population_size', 50),
        generations=config.get('generations', 30)
    )
    
    # 4. Return Result (includes 'explanation' from Phase 3)
    response_data = {
        "status": "success",
        "schedule": result['schedule'],
        "conflicts": result['conflicts'],
        "explanation": result.get('explanation', []),
        "optimization_score": max(0, 100 - (result['conflicts'] * 5))
    }
    
    # Store in cache
    RESPONSE_CACHE[cache_key] = response_data
    
    return jsonify(response_data)

# --- OLD ENDPOINTS ---
@app.route("/horario", methods=["GET"])
def get_horarios():
    rows = db.query('''
        SELECT s.id, s.dia_semana, s.hora_inicio, s.hora_fin, sub.name as materia, t.nombre as maestro, g.name as grado
        FROM schedules s
        JOIN subjects sub ON s.subject_id = sub.id
        JOIN teachers t ON s.teacher_id = t.id
        JOIN grades g ON s.grade_id = g.id
        LIMIT 50
    ''')
    return jsonify([dict(row) for row in rows])

# --- LEARNING ---
learner = PreferenceLearner()

@app.route("/learn", methods=["POST"])
def learn_preferences():
    """
    Endpoint for Symfony to report manual schedule changes
    Payload: { "teacher_id": 1, "day_from": "Lunes", "day_to": "Martes", "reason": "conflict" }
    """
    data = request.json
    try:
        if 'teacher_id' in data and 'day_from' in data and 'day_to' in data:
            learner.learn_from_move(
                data['teacher_id'], 
                data['day_from'], 
                data['day_to'],
                data.get('reason', 'manual')
            )
            return jsonify({"status": "learned", "message": "Preference updated"})
        return jsonify({"error": "Missing fields"}), 400
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# --- PHASE 5: SIMULATION ---
@app.route("/simulate", methods=["POST"])
def simulate_change():
    """
    Run a 'What-If' scenario.
    Payload: { "base_schedule": {...}, "change": {"teacher": "Juan", "add_subject": "Math"} }
    """
    data = request.json
    # Mocking the simulation result for MVP speed
    return jsonify({
        "status": "simulated",
        "original_score": 85,
        "new_score": 82,
        "impact": "adding this class creates a conflict on Tuesday",
        "recommendation": "Try Wednesday instead"
    })
risk_analyzer = RiskAnalyzer()
teacher_analyzer = TeacherAnalyzer()
schedule_scorer = ScheduleScorer()
memory = InstitutionalMemory()
rule_contract = RuleContract()
assistant_factory = AssistantFactory()
negotiation_engine = NegotiationEngine()
decision_logger = DecisionLogger()
legal_defense = LegalDefenseGen()
ethics_validator = EthicsValidator()
simulation_engine = SimulationEngine()
localization_adapter = LocalizationAdapter()

@app.route("/negotiate", methods=["POST"])
def negotiate_change():
    """
    Endpoint for automatic negotiation.
    """
    data = request.json
    conflict = data.get('conflict', {})
    alternatives = negotiation_engine.propose_alternatives(conflict)
    return jsonify({"alternatives": alternatives})



@app.route("/predict-risk", methods=["POST"])
def predict_risk():
    """
    Analyze student data for academic risk
    """
    data = request.json
    try:
        analysis = risk_analyzer.analyze(data)
        return jsonify(analysis)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/analyze-burnout", methods=["POST"])
def analyze_burnout():
    """
    Analyzes a specific teacher's workload provided in the request body.
    """
    data = request.json
    teacher_id = data.get("teacher_id")
    schedule = data.get("schedule", [])
    result = teacher_analyzer.analyze_workload(teacher_id, schedule)
    return jsonify(result)

@app.route("/institutional-health", methods=["GET"])
def institutional_health():
    """
    Returns the Global ISA (Indice de Salud Académica) score.
    """
    # Mock aggregation of stats
    teacher_stats = {'avg_burnout': 15}
    student_stats = {} 
    result = schedule_scorer.calculate_isa([], teacher_stats, student_stats)
    return jsonify(result)

@app.route("/memory/patterns", methods=["GET"])
def get_memory_patterns():
    """
    Returns learned patterns from institutional history.
    """
    return jsonify(memory.get_learned_patterns())

@app.route("/rules/contract", methods=["GET"])
def get_rule_contract():
    """
    Returns the current Institutional Rule Contract.
    """
    return jsonify(rule_contract.get_contract())

@app.route("/audit/log", methods=["GET"])
def get_audit_log():
    return jsonify({"log": decision_logger.get_audit_trail()})

@app.route("/legal/defense", methods=["POST"])
def get_legal_defense():
    data = request.json
    return jsonify(legal_defense.generate_defense(data.get('id'), data))

@app.route("/ethics/validate", methods=["POST"])
def validate_ethics():
    allowed, msg = ethics_validator.validate_request(request.json)
    return jsonify({"allowed": allowed, "message": msg})

@app.route("/simulation/future", methods=["GET"])
def simulate_future():
    return jsonify(simulation_engine.simulate_future_scenario(3))

@app.route("/context/maturity", methods=["GET"])
def get_maturity():
    return jsonify(localization_adapter.get_maturity_index())

if __name__ == "__main__":
    app.run(port=8001, debug=True)
