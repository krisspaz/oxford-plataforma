from fastapi import APIRouter, HTTPException, Depends, Request
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
import json
import hashlib
from database import db
# Reuse the get_current_user dependency from auth_routes or move it to a shared module.
# For simplicity, let's redefine or import if we create a dependencies.py
# I'll rely on a local helper for now to avoid circular imports if I don't move it.
from jose import jwt
from config import config
from services_container import learner
from genetic_scheduler import genetic_optimizer

schedule_router = APIRouter()

RESPONSE_CACHE = {}

# Dependency (duplicated for speed, should be shared)
def verify_token_simple(token):
    try:
        return jwt.decode(token, config.secret_key, algorithms=[config.algorithm])
    except:
        raise HTTPException(status_code=401, detail="Invalid Token")

async def require_auth(request: Request):
    auth_header = request.headers.get("Authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Missing Auth Header")
    token = auth_header.split(" ")[1]
    return verify_token_simple(token)

class ScheduleCreateRequest(BaseModel):
    grado_id: int
    materia_id: int
    teacher_id: int
    dia_semana: int
    hora_inicio: str
    hora_fin: str
    fecha: Optional[str] = None
    lugar: Optional[str] = None
    participantes: List[Any] = []

class GenerateScheduleRequest(BaseModel):
    config: Dict[str, Any] = {}
    teachers: List[Dict[str, Any]] = []
    subjects: List[str] = []
    constraints: List[Dict[str, Any]] = []
    grades: Optional[List[str]] = None

class LearnRequest(BaseModel):
    teacher_id: int
    day_from: str
    day_to: str
    reason: str = "manual"

@schedule_router.post("/horario")
def create_horario(d: ScheduleCreateRequest, user=Depends(require_auth)):
    try:
        # Resolve FKs
        g = db.query("SELECT id FROM grades WHERE id = ?", (d.grado_id,), one=True)
        s = db.query("SELECT id FROM subjects WHERE id = ?", (d.materia_id,), one=True)
        t = db.query("SELECT id FROM teachers WHERE id = ?", (d.teacher_id,), one=True)
        
        if not g or not s or not t: 
            raise HTTPException(status_code=400, detail="Invalid IDs")
        
        new_id = db.execute('''
            INSERT INTO schedules (fecha, dia_semana, hora_inicio, hora_fin, lugar, grade_id, subject_id, teacher_id, participantes_json)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (d.fecha, d.dia_semana, d.hora_inicio, d.hora_fin, d.lugar, g['id'], s['id'], t['id'], json.dumps(d.participantes)))
        
        return {"message": "Horario creado", "id": new_id}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@schedule_router.post("/generate-schedule")
def generate_schedule(req: GenerateScheduleRequest):
    """
    Main endpoint called by Symfony to run the Genetic Algorithm
    """
    # Convert Pydantic model to dict for hashing/processing
    data = req.dict()
    
    # SHA-256 hash of the input to serve as cache key
    data_str = json.dumps(data, sort_keys=True)
    cache_key = hashlib.sha256(data_str.encode()).hexdigest()
    
    if cache_key in RESPONSE_CACHE:
        print("⚡ Cache Hit!")
        return RESPONSE_CACHE[cache_key]
    
    # 1. Extract Data
    config = req.config
    teachers = req.teachers
    subjects = req.subjects
    constraints = req.constraints
    grades = req.grades

    if not grades:
        grades = list(set([s.split('_')[0] for s in subjects])) 
        if not grades:
            grades = ['1ro Basico', '2do Basico', '3ro Basico']

    # 2. Inject Learned Preferences (Phase 2 & 3 integration)
    learned_constraints = []
    for t in teachers:
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
    
    return response_data

@schedule_router.get("/horario")
def get_horarios():
    rows = db.query('''
        SELECT s.id, s.dia_semana, s.hora_inicio, s.hora_fin, sub.name as materia, t.nombre as maestro, g.name as grado
        FROM schedules s
        JOIN subjects sub ON s.subject_id = sub.id
        JOIN teachers t ON s.teacher_id = t.id
        JOIN grades g ON s.grade_id = g.id
        LIMIT 50
    ''')
    return [dict(row) for row in rows]

@schedule_router.post("/learn")
def learn_preferences(req: LearnRequest):
    """
    Endpoint for Symfony to report manual schedule changes
    """
    try:
        learner.learn_from_move(
            req.teacher_id, 
            req.day_from, 
            req.day_to,
            req.reason
        )
        return {"status": "learned", "message": "Preference updated"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
