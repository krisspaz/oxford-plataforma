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
from flask_cors import CORS

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
    text = request.json.get('text', '')
    res = nlp_engine.process(text)
    
    response = {
        "intent": res.intent.name,
        "confidence": res.intent.confidence,
        "response_text": "Procesado.",
        "entities": {e.type: e.value for e in res.entities},
        "should_generate": False
    }
    
    if response["intent"] == "generate_schedule":
        response["response_text"] = "Generando horario..."
        response["should_generate"] = True
    elif response["intent"] == "greeting":
        import random
        greetings = [
            "¡Hola! 👋 ¡Qué alegría verte! Estoy lista para ayudarte.",
            "¡Saludos! Espero que tengas un día excelente. 🌟",
            "¡Hola! Aquí Oxford AI con toda la energía. 🚀"
        ]
        response["response_text"] = random.choice(greetings)
    elif response["intent"] == "check_homework":
        response["response_text"] = "Consultando tu agenda de tareas..."
        response["action"] = "fetch_tasks" 
    elif response["intent"] == "check_grades":
        response["response_text"] = "Buscando tus calificaciones..."
        response["action"] = "fetch_grades"
    elif response["intent"] == "study_tip":
        import random
        tips = [
           "💡 **Tip de Super Tutor**: Intenta la técnica Pomodoro (25 min estudio, 5 min descanso). ¡Aumentará tu concentración!",
           "📚 **Consejo**: Explícale el tema a alguien más (o a tu mascota). Si lo puedes explicar, lo entiendes.",
           "🧠 **Memoria**: Usa mnemotecnias o canciones para recordar listas difíciles.",
           "💧 **Salud**: ¡Mantente hidratado! Tu cerebro necesita agua para procesar información."
        ]
        response["response_text"] = random.choice(tips)
    elif response["intent"] == "emotional_support":
        import random
        msgs = [
           "🌿 ¡Respira profundo! Eres capaz de todo. Un paso a la vez.",
           "💪 Confía en tu preparación. Lo has hecho bien hasta ahora.",
           "🌟 El esfuerzo de hoy es el éxito de mañana. ¡Tú puedes!",
           "🧘‍♀️ Tómate 5 minutos para desconectar. Todo va a salir bien."
        ]
        response["response_text"] = random.choice(msgs)
    elif response["intent"] == "message_teacher":
        response["response_text"] = "¿A qué profesor te gustaría escribirle? ✉️"
        response["action"] = "init_teacher_chat"
    elif response["intent"] == "report_issue":
        response["response_text"] = "Lamento que tengas un inconveniente. 😔 Abre el formulario para contarme más (es anónimo si deseas)."
        response["action"] = "open_feedback_modal"
    elif response["intent"] == "suggestion":
        response["response_text"] = "¡Nos encanta mejorar! 💡 ¿Cuál es tu idea?"
        response["action"] = "open_feedback_modal"
    elif response["intent"] == "check_task_history":
        response["response_text"] = "Revisando tu historial académico..."
        response["action"] = "fetch_task_history"
    elif response["intent"] == "check_grades":
        response["response_text"] = "Consultando tus calificaciones actuales..."
        response["action"] = "fetch_grades"
    elif response["intent"] == "generate_quiz":
        # Check if subject detected
        entities = nlp.extract_entities(data.get('text', ''))
        subject = entities.get('subject')

        if not subject:
            response["response_text"] = "¡Claro! 🧠 ¿De qué materia quieres el examen? (Matemáticas, Ciencias, Historia...)"
            # We don't trigger 'start_quiz' yet, we wait for user to specify subject in next turn
            # In a real stateful bot, we would set context. For now, simple prompt.
        else:
            # Generate a dynamic quiz based on subject
            # Mock Questions Database
            question_bank = {
                'Matemáticas': [
                    {"id": 1, "question": "¿Cuánto es 8 x 7?", "options": ["54", "56", "58", "62"], "answer": "56"},
                    {"id": 2, "question": "Raíz cuadrada de 144", "options": ["10", "11", "12", "14"], "answer": "12"},
                    {"id": 3, "question": "Si x + 5 = 10, ¿x?", "options": ["2", "5", "8", "10"], "answer": "5"}
                ],
                'Ciencias': [
                    {"id": 1, "question": "¿Símbolo químico del Oro?", "options": ["Ag", "Au", "Fe", "Cu"], "answer": "Au"},
                    {"id": 2, "question": "¿Planeta más grande?", "options": ["Tierra", "Marte", "Júpiter", "Saturno"], "answer": "Júpiter"},
                    {"id": 3, "question": "¿Qué respiran las plantas?", "options": ["Oxígeno", "Dióxido de Carbono", "Nitrógeno", "Helio"], "answer": "Dióxido de Carbono"}
                ],
                'Historia': [
                    {"id": 1, "question": "¿Descubrimiento de América?", "options": ["1492", "1500", "1821", "1945"], "answer": "1492"},
                    {"id": 2, "question": "¿Revolución Francesa?", "options": ["1789", "1810", "1917", "1940"], "answer": "1789"},
                    {"id": 3, "question": "¿Primer presidente de USA?", "options": ["Lincoln", "Washington", "Jefferson", "Kennedy"], "answer": "Washington"}
                ]
            }
            
            # Default to General Knowledge if subject not in bank
            selected_questions = question_bank.get(subject, [
                {"id": 1, "question": "¿Capital de Guatemala?", "options": ["Xela", "Guatemala", "Antigua", "Escuintla"], "answer": "Guatemala"},
                {"id": 2, "question": "¿Colores primarios?", "options": ["Rojo, Verde, Azul", "Amarillo, Azul, Rojo", "Blanco, Negro, Gris"], "answer": "Amarillo, Azul, Rojo"},
                {"id": 3, "question": "¿Animal más rápido?", "options": ["León", "Guepardo", "Águila", "Caballo"], "answer": "Guepardo"}
            ])

            quiz_data = {
                "title": f"Quiz Rápido de {subject}",
                "questions": selected_questions
            }
            import json
            response["response_text"] = f"¡Excelente! Aquí tienes tu prueba de {subject}. 📝"
            response["action"] = "start_quiz"
            response["payload"] = json.dumps(quiz_data)
        
    elif response["intent"] == "unknown":
        response["response_text"] = "No entendí la solicitud. ¿Podrías intentar con 'Ver tareas' o 'Generar horario'?"
    
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

# --- PHASE 4: RISK ANALYSIS ---
risk_analyzer = RiskAnalyzer()

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

if __name__ == "__main__":
    app.run(port=8001, debug=True)
