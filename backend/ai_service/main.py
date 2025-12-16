"""
Corpo Oxford AI Service - Robust NLP for Schedule Management
============================================================
This module provides intelligent natural language processing for schedule commands.
Uses pattern matching, fuzzy matching, and intent classification for Spanish language.
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
import re
from difflib import SequenceMatcher
from datetime import datetime
import random

app = FastAPI(
    title="Corpo Oxford AI Service",
    description="Intelligent NLP for schedule management",
    version="2.0.0"
)

# Enable CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ============================================================================
# CONSTANTS - School Configuration
# ============================================================================

DAYS = {
    'lunes': 'Lunes',
    'martes': 'Martes',
    'miercoles': 'Miércoles',
    'miércoles': 'Miércoles',
    'jueves': 'Jueves',
    'viernes': 'Viernes',
    'sabado': 'Sábado',
    'sábado': 'Sábado',
    'domingo': 'Domingo'
}

GRADES = {
    'prekinder': 'Pre-Kinder',
    'pre-kinder': 'Pre-Kinder',
    'pre kinder': 'Pre-Kinder',
    'kinder': 'Kinder',
    'preparatoria': 'Preparatoria',
    'prepa': 'Preparatoria',
    '1ro primaria': '1ro Primaria',
    '1° primaria': '1ro Primaria',
    'primero primaria': '1ro Primaria',
    '2do primaria': '2do Primaria',
    '2° primaria': '2do Primaria',
    'segundo primaria': '2do Primaria',
    '3ro primaria': '3ro Primaria',
    '3° primaria': '3ro Primaria',
    'tercero primaria': '3ro Primaria',
    '4to primaria': '4to Primaria',
    '4° primaria': '4to Primaria',
    'cuarto primaria': '4to Primaria',
    '5to primaria': '5to Primaria',
    '5° primaria': '5to Primaria',
    'quinto primaria': '5to Primaria',
    '6to primaria': '6to Primaria',
    '6° primaria': '6to Primaria',
    'sexto primaria': '6to Primaria',
    '1ro basico': '1ro Básico',
    '1ro básico': '1ro Básico',
    '1° basico': '1ro Básico',
    '2do basico': '2do Básico',
    '2do básico': '2do Básico',
    '3ro basico': '3ro Básico',
    '3ro básico': '3ro Básico',
    '4to bachillerato': '4to Bachillerato',
    '5to bachillerato': '5to Bachillerato',
}

SUBJECTS = {
    'matematicas': 'Matemáticas',
    'matemáticas': 'Matemáticas',
    'mate': 'Matemáticas',
    'español': 'Español',
    'espanol': 'Español',
    'lengua': 'Español',
    'ciencias': 'Ciencias',
    'ciencias naturales': 'Ciencias',
    'ingles': 'Inglés',
    'inglés': 'Inglés',
    'english': 'Inglés',
    'historia': 'Historia',
    'estudios sociales': 'Historia',
    'sociales': 'Historia',
    'educacion fisica': 'Ed. Física',
    'educación física': 'Ed. Física',
    'ed fisica': 'Ed. Física',
    'ed. fisica': 'Ed. Física',
    'ed. física': 'Ed. Física',
    'fisica': 'Física',
    'física': 'Física',
    'deportes': 'Ed. Física',
    'arte': 'Arte',
    'artes': 'Arte',
    'dibujo': 'Arte',
    'musica': 'Música',
    'música': 'Música',
    'computacion': 'Computación',
    'computación': 'Computación',
    'informatica': 'Computación',
    'informática': 'Computación',
    'compu': 'Computación',
    'quimica': 'Química',
    'química': 'Química',
    'biologia': 'Biología',
    'biología': 'Biología',
    'geografia': 'Geografía',
    'geografía': 'Geografía',
    'literatura': 'Literatura',
    'lectura': 'Literatura',
    'economia': 'Economía',
    'economía': 'Economía',
    'filosofia': 'Filosofía',
    'filosofía': 'Filosofía',
}

# Intent patterns - what the user wants to do
INTENT_PATTERNS = {
    'generate': [
        r'genera', r'crear', r'crea', r'haz', r'hazme', r'generar',
        r'construye', r'arma', r'elabora', r'haz.*horario'
    ],
    'remove_recess': [
        r'quita.*receso', r'sin receso', r'elimina.*receso', r'no.*receso',
        r'remueve.*receso', r'saca.*receso', r'borra.*receso'
    ],
    'add_recess': [
        r'agrega.*receso', r'pon.*receso', r'con receso', r'añade.*receso',
        r'incluye.*receso', r'quiero receso'
    ],
    'set_time': [
        r'horario de \d', r'de \d+.*a \d+', r'desde.*hasta', r'empieza.*termina',
        r'inicia.*acaba', r'horarios son', r'hora de inicio', r'hora de fin'
    ],
    'set_duration': [
        r'clases.*de.*minutos', r'clases.*de.*hora', r'duracion.*de',
        r'duración.*de', r'cada clase.*min', r'periodos.*de'
    ],
    'group_grades': [
        r'mismo horario', r'misma clase', r'igual.*horario', r'iguales',
        r'comparten.*horario', r'juntos'
    ],
    'day_subjects': [
        r'(lunes|martes|miércoles|miercoles|jueves|viernes).*solo',
        r'solo.*(lunes|martes|miércoles|miercoles|jueves|viernes)',
        r'(lunes|martes|miércoles|miercoles|jueves|viernes).*unicamente',
        r'quiero que.*tenga'
    ],
    'set_days': [
        r'horario.*(?:sabado|sábado|domingo)', r'quiero.*(?:sabado|sábado|domingo)',
        r'incluir.*(?:sabado|sábado|domingo)', r'agregar.*(?:sabado|sábado|domingo)',
        r'(?:sabado|sábado|domingo).*y.*(?:sabado|sábado|domingo)', r'dias.*trabajar'
    ],
    'teacher_restriction': [
        r'prof.*solo.*puede', r'profesor.*no.*puede', r'maestro.*disponible',
        r'prof.*de.*a', r'profesor.*horario', r'profesor.*no.*da',
        r'prof.*no puede', r'profesor\s+\w+\s+no', r'el profesor.*no',
        r'profe.*no.*lunes', r'profe.*no.*martes', r'profe.*no.*miercoles',
        r'profe.*no.*jueves', r'profe.*no.*viernes'
    ],
    'teacher_subject': [
        r'prof.*da', r'profesor.*enseña', r'prof.*imparte',
        r'maestro.*da', r'asigna.*prof'
    ],
    'clear': [
        r'limpiar', r'borrar', r'reiniciar', r'reset', r'eliminar todo',
        r'quitar todo', r'empezar de nuevo'
    ],
    'help': [
        r'ayuda', r'help', r'que puedo', r'qué puedo', r'comandos',
        r'opciones', r'como funciona', r'cómo funciona'
    ],
    'show_config': [
        r'mostrar.*config', r'ver.*config', r'configuracion actual',
        r'que tengo', r'qué tengo', r'restricciones'
    ],
    'greeting': [
        r'^hola$', r'^hi$', r'^hey$', r'^buenos días', r'^buenas tardes',
        r'^buenas noches', r'^saludos', r'^que tal', r'^qué tal', r'^hola!'
    ]
}

# ============================================================================
# MODELS
# ============================================================================

class CommandRequest(BaseModel):
    text: str
    current_config: Optional[Dict[str, Any]] = None

class CommandResponse(BaseModel):
    intent: str
    confidence: float
    entities: Dict[str, Any]
    response_text: str
    config_changes: Optional[Dict[str, Any]] = None
    should_generate: bool = False

class ScheduleRequest(BaseModel):
    config: Dict[str, Any]
    grade_groups: List[List[str]] = []
    day_rules: Dict[str, List[str]] = {}
    teacher_restrictions: Dict[str, Dict[str, str]] = {}

class ScheduleSlot(BaseModel):
    subject: str
    teacher: str

class ScheduleResponse(BaseModel):
    success: bool
    schedule: Dict[str, Dict[str, List[Dict[str, str]]]]
    message: str

# ============================================================================
# NLP UTILITIES
# ============================================================================

def normalize_text(text: str) -> str:
    """Normalize text for comparison - lowercase and remove accents for matching."""
    text = text.lower().strip()
    # Keep accents for entity matching but normalize for intent detection
    return text

def fuzzy_match(text: str, options: Dict[str, str], threshold: float = 0.7) -> Optional[str]:
    """Find best fuzzy match from options dictionary."""
    text = text.lower()
    best_match = None
    best_ratio = 0
    
    for key, value in options.items():
        ratio = SequenceMatcher(None, text, key.lower()).ratio()
        if ratio > best_ratio and ratio >= threshold:
            best_ratio = ratio
            best_match = value
    
    return best_match

def extract_times(text: str) -> List[Dict[str, Any]]:
    """Extract time expressions from text."""
    times = []
    
    # Pattern: 10:30, 10:00, 8:15, etc.
    time_pattern = r'(\d{1,2}):(\d{2})'
    for match in re.finditer(time_pattern, text):
        h, m = int(match.group(1)), int(match.group(2))
        times.append({'hour': h, 'minute': m, 'formatted': f"{h:02d}:{m:02d}"})
    
    # Pattern: 10, 8, 12 (just hours)
    hour_pattern = r'(?<!\d)(\d{1,2})(?:\s*(?:de la\s*)?(?:mañana|tarde|am|pm|hrs?|horas?))?(?!\d|:)'
    for match in re.finditer(hour_pattern, text.lower()):
        h = int(match.group(1))
        if h <= 24 and {'hour': h, 'minute': 0, 'formatted': f"{h:02d}:00"} not in times:
            # Check for am/pm/mañana/tarde
            context = text[max(0, match.start()-10):match.end()+15].lower()
            if 'tarde' in context or 'pm' in context:
                if h < 12:
                    h += 12
            times.append({'hour': h, 'minute': 0, 'formatted': f"{h:02d}:00"})
    
    return times

def extract_duration(text: str) -> Optional[int]:
    """Extract class duration in minutes."""
    text = text.lower()
    
    # Pattern: "1 hora", "2 horas"
    hour_pattern = r'(\d+)\s*horas?'
    match = re.search(hour_pattern, text)
    if match:
        return int(match.group(1)) * 60
    
    # Pattern: "45 minutos", "30 min"
    min_pattern = r'(\d+)\s*(?:minutos?|mins?)'
    match = re.search(min_pattern, text)
    if match:
        return int(match.group(1))
    
    return None

def extract_days(text: str) -> List[str]:
    """Extract day names from text."""
    text = text.lower()
    found = []
    for key, value in DAYS.items():
        if key in text and value not in found:
            found.append(value)
    return found

def extract_subjects(text: str) -> List[str]:
    """Extract subject names from text using fuzzy matching."""
    text = text.lower()
    found = []
    
    # First try exact matches
    for key, value in SUBJECTS.items():
        if key in text and value not in found:
            found.append(value)
    
    # Also try to find words that might be subjects
    words = re.findall(r'\b\w+\b', text)
    for word in words:
        if len(word) > 3:
            match = fuzzy_match(word, SUBJECTS, 0.75)
            if match and match not in found:
                found.append(match)
    
    return found

def extract_grades(text: str) -> List[str]:
    """Extract grade names from text."""
    text = text.lower()
    found = []
    
    # Try exact matches first
    for key, value in GRADES.items():
        if key in text and value not in found:
            found.append(value)
    
    # Pattern for "Xo primaria/basico/bachillerato"
    grade_pattern = r'(\d+)(?:ro|do|to|°)?\s*(primaria|basico|básico|bachillerato)'
    for match in re.finditer(grade_pattern, text):
        num = match.group(1)
        level = match.group(2).replace('básico', 'basico')
        ordinal = {'1': '1ro', '2': '2do', '3': '3ro', '4': '4to', '5': '5to', '6': '6to'}.get(num, f'{num}o')
        key = f"{ordinal} {level}"
        grade = GRADES.get(key)
        if grade and grade not in found:
            found.append(grade)
    
    return found

def extract_teacher(text: str) -> Optional[str]:
    """Extract teacher name from text."""
    # Pattern: "Prof. García", "Profesor López", "Maestro Martinez"
    pattern = r'(?:prof\.?|profesor|maestro|maestra)\s+([a-záéíóúñ]+)'
    match = re.search(pattern, text.lower())
    if match:
        name = match.group(1).capitalize()
        return f"Prof. {name}"
    return None

def classify_intent(text: str) -> tuple:
    """Classify the user's intent from their message with priority ordering."""
    text = normalize_text(text)
    
    # Priority order - check specific intents first
    priority_order = [
        'remove_recess', 'add_recess',  # Very specific
        'set_days',  # Configure which days (sábado, domingo)
        'group_grades',  # Contains "mismo horario"
        'day_subjects',  # Contains day + subjects
        'teacher_restriction', 'teacher_subject',
        'generate', 'clear', 'help', 'show_config', 'greeting',
        'set_duration',  # More specific than set_time
        'set_time'  # Most general - check last
    ]
    
    for intent in priority_order:
        if intent not in INTENT_PATTERNS:
            continue
        patterns = INTENT_PATTERNS[intent]
        for pattern in patterns:
            if re.search(pattern, text):
                return intent, 0.85
    
    # Fallback to scoring system for unknown intents
    best_intent = 'unknown'
    best_score = 0
    
    for intent, patterns in INTENT_PATTERNS.items():
        for pattern in patterns:
            matches = re.findall(pattern, text)
            if matches:
                score = len(matches) + 0.5
                if score > best_score:
                    best_score = score
                    best_intent = intent
    
    confidence = min(best_score / 2, 1.0)
    return best_intent, confidence

# ============================================================================
# COMMAND PROCESSOR
# ============================================================================

def process_command(text: str, current_config: Optional[Dict] = None) -> CommandResponse:
    """Process a natural language command and extract intent + entities."""
    
    intent, confidence = classify_intent(text)
    entities = {}
    config_changes = {}
    response_text = ""
    should_generate = False
    
    # Extract entities based on intent
    if intent == 'generate':
        should_generate = True
        response_text = "🔄 **Generando horario...**"
    
    elif intent == 'remove_recess':
        config_changes['hasRecess'] = False
        response_text = "✅ **Receso eliminado.**\nEl horario será continuo sin interrupciones.\n\nEscribe 'generar' para crear el horario."
    
    elif intent == 'add_recess':
        config_changes['hasRecess'] = True
        response_text = "✅ **Receso agregado.**\n\nEscribe 'generar' para crear el horario."
    
    elif intent == 'set_time':
        times = extract_times(text)
        duration = extract_duration(text)
        
        if len(times) >= 2:
            config_changes['startTime'] = times[0]['formatted']
            config_changes['endTime'] = times[1]['formatted']
            entities['times'] = times
        
        if duration:
            config_changes['classDuration'] = duration
            entities['duration'] = duration
        
        if config_changes:
            parts = []
            if 'startTime' in config_changes:
                parts.append(f"Inicio: {config_changes['startTime']}")
            if 'endTime' in config_changes:
                parts.append(f"Fin: {config_changes['endTime']}")
            if 'classDuration' in config_changes:
                parts.append(f"Duración: {config_changes['classDuration']} min")
            response_text = f"✅ **Configuración actualizada:**\n" + "\n".join(f"• {p}" for p in parts) + "\n\nEscribe 'generar' para crear el horario."
        else:
            response_text = "❓ No entendí la configuración de tiempo.\n\nEjemplos:\n• 'Horario de 8 a 2 de la tarde'\n• 'Clases de 45 minutos'"
    
    elif intent == 'set_duration':
        duration = extract_duration(text)
        if duration:
            config_changes['classDuration'] = duration
            entities['duration'] = duration
            response_text = f"✅ **Duración actualizada:** {duration} minutos por clase.\n\nEscribe 'generar' para crear el horario."
        else:
            response_text = "❓ No entendí la duración.\n\nEjemplos:\n• 'Clases de 1 hora'\n• 'Clases de 45 minutos'"
    
    elif intent == 'group_grades':
        grades = extract_grades(text)
        if len(grades) >= 2:
            entities['grades'] = grades
            config_changes['gradeGroup'] = grades
            response_text = f"✅ **Grados agrupados:**\n{' = '.join(grades)}\n\nTendrán el MISMO horario.\nEscribe 'generar' para crear."
        else:
            response_text = f"❌ Necesito al menos 2 grados. Encontré: {', '.join(grades) if grades else 'ninguno'}\n\nEjemplo: 'Pre-Kinder y Kinder mismo horario'"
    
    elif intent == 'day_subjects':
        days = extract_days(text)
        subjects = extract_subjects(text)
        
        if days and subjects:
            entities['days'] = days
            entities['subjects'] = subjects
            config_changes['dayRule'] = {'days': days, 'subjects': subjects}
            day_str = ', '.join(days)
            subj_str = ', '.join(subjects)
            response_text = f"✅ **Regla para {day_str}:**\nSolo se impartirán: {subj_str}\n\nEscribe 'generar' para aplicar."
        else:
            response_text = f"❓ No entendí la regla.\nDías encontrados: {days}\nMaterias encontradas: {subjects}\n\nEjemplo: 'Los miércoles solo física, arte y música'"
    
    elif intent == 'teacher_restriction':
        teacher = extract_teacher(text)
        times = extract_times(text)
        days = extract_days(text)
        
        # Check if it's a day-based restriction (e.g., "no puede los lunes")
        is_unavailable = bool(re.search(r'no puede|no da|no está|no trabaja', text))
        
        if teacher and days and is_unavailable:
            # Day-based restriction: teacher can't work on specific days
            entities['teacher'] = teacher
            entities['unavailable_days'] = days
            config_changes['teacherDayRestriction'] = {
                'teacher': teacher,
                'unavailableDays': days
            }
            days_str = ', '.join(days)
            response_text = f"✅ **Restricción de {teacher}:**\nNo disponible los días: {days_str}\n\nEscribe 'generar' para aplicar."
        elif teacher and len(times) >= 2:
            # Time-based restriction: teacher only available between certain hours
            entities['teacher'] = teacher
            entities['times'] = times
            config_changes['teacherRestriction'] = {
                'teacher': teacher,
                'start': times[0]['formatted'],
                'end': times[1]['formatted']
            }
            response_text = f"✅ **Restricción de {teacher}:**\nDisponible de {times[0]['formatted']} a {times[1]['formatted']}\n\nEscribe 'generar' para aplicar."
        elif teacher and days:
            # Days specified without "no puede" - assume they CAN work those days
            entities['teacher'] = teacher
            entities['available_days'] = days
            config_changes['teacherDayRestriction'] = {
                'teacher': teacher,
                'availableDays': days
            }
            days_str = ', '.join(days)
            response_text = f"✅ **{teacher} solo trabaja:**\n{days_str}\n\nEscribe 'generar' para aplicar."
        else:
            response_text = "❓ No entendí la restricción del profesor.\n\nEjemplos:\n• 'Prof. García no puede los lunes'\n• 'Prof. López solo puede de 8:00 a 12:00'"
    
    elif intent == 'teacher_subject':
        teacher = extract_teacher(text)
        subjects = extract_subjects(text)
        if teacher and subjects:
            entities['teacher'] = teacher
            entities['subjects'] = subjects
            config_changes['teacherSubject'] = {'teacher': teacher, 'subjects': subjects}
            response_text = f"✅ **{teacher} asignado a:**\n{', '.join(subjects)}\n\nEscribe 'generar' para aplicar."
        else:
            response_text = "❓ No entendí la asignación.\n\nEjemplo: 'Prof. López da Matemáticas y Español'"
    
    elif intent == 'set_days':
        days = extract_days(text)
        if days:
            entities['days'] = days
            config_changes['scheduleDays'] = days
            response_text = f"✅ **Días del horario configurados:**\n{', '.join(days)}\n\nEscribe 'generar' para crear el horario."
        else:
            response_text = "❓ ¿Qué días quieres incluir?\n\nEjemplo: 'Quiero horario sábado y domingo'"
    
    elif intent == 'clear':
        config_changes['clear'] = True
        response_text = "🗑️ **Todo limpio.**\nConfiguración reiniciada a valores por defecto."
    
    elif intent == 'help':
        response_text = """🤖 **Puedo entender:**

⏰ **Horarios:** "De 8 a 2 de la tarde, clases de 45 min"
🚫 **Receso:** "Quita el receso" / "Agrega receso"
📅 **Días específicos:** "Miércoles solo física, arte y música"
📆 **Fin de semana:** "Quiero horario sábado y domingo"
👥 **Agrupar:** "Pre-kinder y Kinder mismo horario"
👨‍🏫 **Profes:** "Prof. García solo puede de 8 a 12"
⚡ **Generar:** "Genera el horario"
🗑️ **Limpiar:** "Reiniciar todo" """
    
    elif intent == 'show_config':
        response_text = "📋 **Configuración actual mostrada arriba.**"
    
    elif intent == 'greeting':
        response_text = """👋 **¡Hola!** Soy tu asistente de horarios.

Algunos comandos que puedo entender:
• "Horario de 8 a 2, clases de 1 hora"
• "Quita el receso"
• "Miércoles solo física, arte y música"
• "Pre-kinder y Kinder mismo horario"
• "Generar horario"

¿En qué te puedo ayudar?"""
    
    else:
        # Try to extract any useful info anyway
        times = extract_times(text)
        duration = extract_duration(text)
        days = extract_days(text)
        subjects = extract_subjects(text)
        grades = extract_grades(text)
        
        if times or duration or days or subjects or grades:
            entities = {
                'times': times,
                'duration': duration,
                'days': days,
                'subjects': subjects,
                'grades': grades
            }
            response_text = f"🤔 Entendí algo pero no estoy seguro de qué hacer:\n\n"
            if times:
                response_text += f"• Tiempos: {[t['formatted'] for t in times]}\n"
            if duration:
                response_text += f"• Duración: {duration} min\n"
            if days:
                response_text += f"• Días: {days}\n"
            if subjects:
                response_text += f"• Materias: {subjects}\n"
            if grades:
                response_text += f"• Grados: {grades}\n"
            response_text += "\n¿Puedes ser más específico?"
        else:
            response_text = "❓ No entendí tu solicitud.\n\nEscribe 'ayuda' para ver los comandos disponibles."
    
    return CommandResponse(
        intent=intent,
        confidence=confidence,
        entities=entities,
        response_text=response_text,
        config_changes=config_changes if config_changes else None,
        should_generate=should_generate
    )

# ============================================================================
# SCHEDULE GENERATOR
# ============================================================================

DEFAULT_SUBJECTS = ['Matemáticas', 'Español', 'Ciencias', 'Inglés', 'Historia', 
                    'Ed. Física', 'Arte', 'Música', 'Computación']

DEFAULT_TEACHERS = [
    {'name': 'Prof. García', 'subjects': ['Matemáticas', 'Física']},
    {'name': 'Prof. López', 'subjects': ['Español', 'Literatura']},
    {'name': 'Prof. Martínez', 'subjects': ['Ciencias', 'Química']},
    {'name': 'Prof. Smith', 'subjects': ['Inglés']},
    {'name': 'Prof. Hernández', 'subjects': ['Historia', 'Ed. Física']},
    {'name': 'Prof. Rodríguez', 'subjects': ['Arte', 'Música']},
    {'name': 'Prof. Flores', 'subjects': ['Computación', 'Matemáticas']},
]

DEFAULT_GRADES = [
    'Pre-Kinder', 'Kinder', 'Preparatoria',
    '1ro Primaria', '2do Primaria', '3ro Primaria', '4to Primaria', '5to Primaria', '6to Primaria',
    '1ro Básico', '2do Básico', '3ro Básico',
    '4to Bachillerato', '5to Bachillerato'
]

def generate_time_slots(config: Dict) -> List[Dict]:
    """Generate time slots based on configuration."""
    slots = []
    
    def parse_time(t: str) -> int:
        parts = t.split(':')
        return int(parts[0]) * 60 + (int(parts[1]) if len(parts) > 1 else 0)
    
    def format_time(mins: int) -> str:
        return f"{mins // 60}:{mins % 60:02d}"
    
    start = parse_time(config.get('startTime', '07:30'))
    end = parse_time(config.get('endTime', '13:00'))
    duration = config.get('classDuration', 45)
    has_recess = config.get('hasRecess', True)
    recess_start = parse_time(config.get('recessStart', '10:10'))
    recess_end = parse_time(config.get('recessEnd', '10:50'))
    
    current = start
    while current < end:
        # Check for recess
        if has_recess and current >= recess_start and current < recess_end:
            slots.append({
                'label': f"{format_time(recess_start)} - {format_time(recess_end)}",
                'isRecess': True
            })
            current = recess_end
            continue
        
        slot_end = current + duration
        if has_recess and current < recess_start and slot_end > recess_start:
            slot_end = recess_start
        if slot_end > end:
            slot_end = end
        
        slots.append({
            'label': f"{format_time(current)} - {format_time(slot_end)}",
            'isRecess': False
        })
        current = slot_end
    
    return slots

def generate_schedule(request: ScheduleRequest) -> ScheduleResponse:
    """Generate a complete schedule based on configuration and rules."""
    config = request.config
    grade_groups = request.grade_groups
    day_rules = request.day_rules
    
    days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']
    slots = generate_time_slots(config)
    
    # Build copy map for grouped grades
    copy_from = {}
    for group in grade_groups:
        if len(group) >= 2:
            primary = group[0]
            for secondary in group[1:]:
                copy_from[secondary] = primary
    
    schedule = {}
    
    for grade in DEFAULT_GRADES:
        if grade in copy_from:
            continue  # Will copy later
        
        schedule[grade] = {}
        for day in days:
            day_subjects = day_rules.get(day, DEFAULT_SUBJECTS)
            schedule[grade][day] = []
            
            for slot in slots:
                if slot['isRecess']:
                    schedule[grade][day].append({'subject': 'RECESO', 'teacher': ''})
                else:
                    teacher = random.choice(DEFAULT_TEACHERS)
                    subject = random.choice(day_subjects if day_subjects else DEFAULT_SUBJECTS)
                    schedule[grade][day].append({
                        'subject': subject,
                        'teacher': teacher['name']
                    })
    
    # Copy schedules for grouped grades
    for target, source in copy_from.items():
        if source in schedule:
            import json
            schedule[target] = json.loads(json.dumps(schedule[source]))
    
    groups_applied = len(grade_groups)
    rules_applied = len(day_rules)
    
    return ScheduleResponse(
        success=True,
        schedule=schedule,
        message=f"Horario generado con {groups_applied} grupos y {rules_applied} reglas de día"
    )

# ============================================================================
# API ENDPOINTS
# ============================================================================

@app.get("/")
def read_root():
    return {
        "status": "online",
        "service": "Corpo Oxford AI - NLP Schedule Assistant",
        "version": "2.0.0"
    }

@app.get("/health")
def health_check():
    return {"status": "healthy"}

@app.post("/process-command", response_model=CommandResponse)
def api_process_command(request: CommandRequest):
    """Process a natural language command."""
    return process_command(request.text, request.current_config)

@app.post("/generate-schedule", response_model=ScheduleResponse)
def api_generate_schedule(request: ScheduleRequest):
    """Generate a schedule based on configuration."""
    return generate_schedule(request)

# For testing
@app.get("/test-nlp/{text}")
def test_nlp(text: str):
    """Test endpoint to see NLP extraction."""
    return {
        "input": text,
        "intent": classify_intent(text),
        "times": extract_times(text),
        "duration": extract_duration(text),
        "days": extract_days(text),
        "subjects": extract_subjects(text),
        "grades": extract_grades(text),
        "teacher": extract_teacher(text)
    }
