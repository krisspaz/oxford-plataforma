"""
Enhanced NLP Engine for Spanish Language Processing
=====================================================
Robust natural language understanding for school schedule management
with advanced pattern matching, intent classification, and entity extraction.
"""

import re
from typing import Dict, List, Optional, Tuple, Any
from dataclasses import dataclass, field
from difflib import SequenceMatcher
import math


@dataclass
class Intent:
    """Represents a classified intent with confidence"""
    name: str
    confidence: float
    matched_patterns: List[str] = field(default_factory=list)


@dataclass
class Entity:
    """Represents an extracted entity"""
    type: str
    value: Any
    raw_text: str
    start: int
    end: int


@dataclass
class NLPResult:
    """Complete NLP processing result"""
    intent: Intent
    entities: List[Entity]
    normalized_text: str
    suggestions: List[str]


class SpanishNLPEngine:
    """
    Advanced NLP Engine for Spanish language processing
    Optimized for educational schedule management domain
    """
    
    # Spanish stopwords for text normalization
    STOPWORDS = {
        'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas',
        'de', 'del', 'al', 'a', 'en', 'con', 'por', 'para',
        'que', 'y', 'o', 'pero', 'si', 'no', 'como', 'más',
        'este', 'esta', 'estos', 'estas', 'ese', 'esa',
        'mi', 'tu', 'su', 'me', 'te', 'se', 'le', 'lo',
        'muy', 'también', 'ya', 'ahora', 'aquí', 'allí'
    }
    
    # Character normalization for accent handling
    ACCENT_MAP = {
        'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u',
        'ü': 'u', 'ñ': 'n'
    }
    
    # Intent patterns with weighted scoring
    INTENT_PATTERNS = {
        'generate_schedule': {
            'weight': 1.0,
            'patterns': [
                r'\b(genera|generar|crear|crea|haz|hazme|construye|arma|elabora)\b.*\b(horario|agenda|calendario)\b',
                r'\b(horario|agenda)\b.*\b(para|de|del)\b',
                r'\b(necesito|quiero|dame)\b.*\b(horario)\b',
                r'^genera\b',
                r'^crea\b.*\bhorario',
            ],
            'keywords': ['genera', 'crear', 'horario', 'agenda', 'armar'],
        },
        'modify_schedule': {
            'weight': 0.9,
            'patterns': [
                r'\b(modifica|modificar|cambia|cambiar|edita|editar|ajusta|ajustar)\b.*\b(horario|clase|periodo)\b',
                r'\b(mueve|mover|pon|poner)\b.*\b(clase|materia)\b',
            ],
            'keywords': ['modificar', 'cambiar', 'editar', 'mover', 'ajustar'],
        },
        'add_constraint': {
            'weight': 0.85,
            'patterns': [
                r'\b(prof|profesor|maestro|maestra)\b.*\b(no puede|no.*disponible|solo puede)\b',
                r'\b(restriccion|restricción)\b',
                r'\b(no puede|no quiero|evitar)\b.*\b(lunes|martes|miércoles|jueves|viernes)\b',
            ],
            'keywords': ['restriccion', 'no puede', 'solo puede', 'disponible'],
        },
        'set_time': {
            'weight': 0.8,
            'patterns': [
                r'\b(horario|clases)\b.*\b(de|desde)\b.*\d+.*\b(a|hasta)\b.*\d+',
                r'\b(empieza|empezar|inicia|iniciar)\b.*\d+',
                r'\b(termina|terminar|acaba|acabar)\b.*\d+',
                r'\b\d{1,2}:\d{2}\b.*\b(a|hasta)\b.*\b\d{1,2}:\d{2}\b',
            ],
            'keywords': ['empieza', 'termina', 'hora', 'inicio', 'fin'],
        },
        'set_duration': {
            'weight': 0.75,
            'patterns': [
                r'\b(clases|periodos)\b.*\b(de|duran)\b.*\b(\d+)\b.*\b(minutos|min|hora)\b',
                r'\b(duracion|duración)\b.*\b(\d+)\b',
            ],
            'keywords': ['duracion', 'minutos', 'horas', 'tiempo'],
        },
        'remove_recess': {
            'weight': 0.7,
            'patterns': [
                r'\b(quita|quitar|elimina|eliminar|sin|saca|sacar)\b.*\b(receso|recreo|descanso)\b',
            ],
            'keywords': ['quitar', 'eliminar', 'sin', 'receso', 'recreo'],
        },
        'add_recess': {
            'weight': 0.7,
            'patterns': [
                r'\b(agrega|agregar|pon|poner|con|añade|añadir)\b.*\b(receso|recreo|descanso)\b',
            ],
            'keywords': ['agregar', 'añadir', 'con', 'receso', 'recreo'],
        },
        'help': {
            'weight': 0.6,
            'patterns': [
                r'^ayuda$',
                r'\b(ayuda|help|auxilio)\b',
                r'\b(que puedo|qué puedo|como funciona|cómo funciona)\b',
                r'\b(comandos|opciones|instrucciones)\b',
            ],
            'keywords': ['ayuda', 'help', 'comandos', 'opciones'],
        },
        'greeting': {
            'weight': 0.5,
            'patterns': [
                r'^(hola|hi|hey|buenos dias|buenos días|buenas tardes|buenas noches|saludos)[\s!.]*$',
                r'^(hola|hey)[\s,]+',
            ],
            'keywords': ['hola', 'buenos', 'saludos'],
        },
        'show_status': {
            'weight': 0.65,
            'patterns': [
                r'\b(muestra|mostrar|ver|dame)\b.*\b(configuracion|config|estado|actual)\b',
                r'\b(que tengo|qué tengo|restricciones actuales)\b',
            ],
            'keywords': ['mostrar', 'ver', 'configuracion', 'estado'],
        },
        'clear': {
            'weight': 0.6,
            'patterns': [
                r'\b(limpiar|borrar|reiniciar|reset|eliminar todo|quitar todo)\b',
                r'\b(empezar de nuevo|comenzar de cero)\b',
            ],
            'keywords': ['limpiar', 'borrar', 'reiniciar', 'reset'],
        },
        'assign_teacher': {
            'weight': 0.85,
            'patterns': [
                r'\b(prof|profesor|profe|maestro)\b.*\b(da|enseña|imparte|tiene)\b.*\b(matematicas|español|ciencias|ingles)\b',
                r'\b(asigna|asignar)\b.*\b(prof|profesor)\b',
            ],
            'keywords': ['profesor', 'asignar', 'enseña', 'imparte'],
        },
    }
    
    # Entity extractors configuration
    ENTITY_PATTERNS = {
        'grade': {
            'patterns': [
                (r'\b(pre-?kinder|prekinder)\b', 'Pre-Kinder'),
                (r'\b(kinder)\b', 'Kinder'),
                (r'\b(preparatoria|prepa)\b', 'Preparatoria'),
                (r'\b(1ro?|primero?)\s*(primaria)\b', '1ro Primaria'),
                (r'\b(2do?|segundo?)\s*(primaria)\b', '2do Primaria'),
                (r'\b(3ro?|tercero?)\s*(primaria)\b', '3ro Primaria'),
                (r'\b(4to?|cuarto?)\s*(primaria)\b', '4to Primaria'),
                (r'\b(5to?|quinto?)\s*(primaria)\b', '5to Primaria'),
                (r'\b(6to?|sexto?)\s*(primaria)\b', '6to Primaria'),
                (r'\b(1ro?|primero?)\s*(bas?i?co?|básico?)\b', '1ro Básico'),
                (r'\b(2do?|segundo?)\s*(bas?i?co?|básico?)\b', '2do Básico'),
                (r'\b(3ro?|tercero?)\s*(bas?i?co?|básico?)\b', '3ro Básico'),
                (r'\b(4to?|cuarto?)\s*(bachillerato)\b', '4to Bachillerato'),
                (r'\b(5to?|quinto?)\s*(bachillerato)\b', '5to Bachillerato'),
            ]
        },
        'subject': {
            'patterns': [
                (r'\b(matem[aá]ticas?|mate)\b', 'Matemáticas'),
                (r'\b(espa[ñn]ol|lengua)\b', 'Español'),
                (r'\b(ciencias?\s*naturales?|ciencias?)\b', 'Ciencias'),
                (r'\b(ingl[eé]s|english)\b', 'Inglés'),
                (r'\b(historia|estudios?\s*sociales?|sociales?)\b', 'Historia'),
                (r'\b(educaci[oó]n?\s*f[ií]sica|ed\.?\s*f[ií]sica|deportes?)\b', 'Ed. Física'),
                (r'\b(artes?|dibujo)\b', 'Arte'),
                (r'\b(m[uú]sica)\b', 'Música'),
                (r'\b(computaci[oó]n|inform[aá]tica|compu)\b', 'Computación'),
                (r'\b(qu[ií]mica)\b', 'Química'),
                (r'\b(biolog[ií]a)\b', 'Biología'),
                (r'\b(geograf[ií]a)\b', 'Geografía'),
                (r'\b(literatura|lectura)\b', 'Literatura'),
                (r'\b(econom[ií]a)\b', 'Economía'),
                (r'\b(filosof[ií]a)\b', 'Filosofía'),
            ]
        },
        'day': {
            'patterns': [
                (r'\b(lunes)\b', 'Lunes'),
                (r'\b(martes)\b', 'Martes'),
                (r'\b(mi[eé]rcoles)\b', 'Miércoles'),
                (r'\b(jueves)\b', 'Jueves'),
                (r'\b(viernes)\b', 'Viernes'),
                (r'\b(s[aá]bado)\b', 'Sábado'),
                (r'\b(domingo)\b', 'Domingo'),
            ]
        },
        'time': {
            'patterns': [
                (r'\b(\d{1,2}):(\d{2})\b', 'time_hhmm'),
                (r'\b(\d{1,2})\s*(am|pm|hrs?|horas?|de la mañana|de la tarde)\b', 'time_h'),
            ]
        },
        'section': {
            'patterns': [
                (r'\bsecci[oó]n\s*([A-Za-z])\b', 'section'),
                (r'\b([A-Z])\s*$', 'section_letter'),
            ]
        },
    }
    
    def __init__(self, fuzzy_threshold: float = 0.7):
        self.fuzzy_threshold = fuzzy_threshold
        self._compile_patterns()
    
    def _compile_patterns(self):
        """Pre-compile regex patterns for performance"""
        self._compiled_intents = {}
        for intent, config in self.INTENT_PATTERNS.items():
            self._compiled_intents[intent] = {
                'patterns': [re.compile(p, re.IGNORECASE) for p in config['patterns']],
                'weight': config['weight'],
                'keywords': config['keywords'],
            }
        
        self._compiled_entities = {}
        for entity_type, config in self.ENTITY_PATTERNS.items():
            self._compiled_entities[entity_type] = [
                (re.compile(p, re.IGNORECASE), v)
                for p, v in config['patterns']
            ]
    
    def normalize(self, text: str, remove_accents: bool = False) -> str:
        """Normalize text for processing"""
        text = text.strip().lower()
        
        if remove_accents:
            for accented, plain in self.ACCENT_MAP.items():
                text = text.replace(accented, plain)
        
        # Normalize multiple spaces
        text = re.sub(r'\s+', ' ', text)
        
        return text
    
    def remove_stopwords(self, text: str) -> str:
        """Remove Spanish stopwords"""
        words = text.split()
        return ' '.join(w for w in words if w.lower() not in self.STOPWORDS)
    
    def classify_intent(self, text: str) -> Intent:
        """
        Classify the intent of the input text using pattern matching
        and keyword-based confidence scoring
        """
        normalized = self.normalize(text)
        normalized_no_accent = self.normalize(text, remove_accents=True)
        
        scores = {}
        matched = {}
        
        for intent, config in self._compiled_intents.items():
            score = 0.0
            patterns_matched = []
            
            # Pattern matching (high weight)
            for pattern in config['patterns']:
                if pattern.search(normalized) or pattern.search(normalized_no_accent):
                    score += 0.5
                    patterns_matched.append(pattern.pattern)
            
            # Keyword presence (medium weight)
            keywords_found = 0
            for keyword in config['keywords']:
                if keyword in normalized or keyword in normalized_no_accent:
                    keywords_found += 1
            
            if config['keywords']:
                keyword_score = (keywords_found / len(config['keywords'])) * 0.3
                score += keyword_score
            
            # Apply intent weight
            score *= config['weight']
            
            # Normalize to 0-1 range
            score = min(score, 1.0)
            
            if score > 0:
                scores[intent] = score
                matched[intent] = patterns_matched
        
        if not scores:
            return Intent(name='unknown', confidence=0.0, matched_patterns=[])
        
        # Get best intent
        best_intent = max(scores, key=scores.get)
        confidence = scores[best_intent]
        
        # Apply softmax-like normalization for more realistic confidence
        if len(scores) > 1:
            total = sum(math.exp(s * 2) for s in scores.values())
            confidence = math.exp(confidence * 2) / total
        
        return Intent(
            name=best_intent,
            confidence=round(confidence, 3),
            matched_patterns=matched.get(best_intent, [])
        )
    
    def extract_entities(self, text: str) -> List[Entity]:
        """Extract all entities from text"""
        entities = []
        normalized = self.normalize(text)
        
        for entity_type, patterns in self._compiled_entities.items():
            for pattern, value_template in patterns:
                for match in pattern.finditer(normalized):
                    # Handle time specially
                    if entity_type == 'time':
                        if value_template == 'time_hhmm':
                            value = f"{match.group(1)}:{match.group(2)}"
                        else:
                            hour = int(match.group(1))
                            period = match.group(2).lower() if len(match.groups()) > 1 else ''
                            if 'pm' in period or 'tarde' in period:
                                if hour < 12:
                                    hour += 12
                            value = f"{hour:02d}:00"
                    elif entity_type == 'section':
                        value = match.group(1).upper()
                    else:
                        value = value_template
                    
                    entities.append(Entity(
                        type=entity_type,
                        value=value,
                        raw_text=match.group(0),
                        start=match.start(),
                        end=match.end()
                    ))
        
        # Remove duplicates keeping first occurrence
        seen = set()
        unique_entities = []
        for e in entities:
            key = (e.type, e.value)
            if key not in seen:
                seen.add(key)
                unique_entities.append(e)
        
        return unique_entities
    
    def get_suggestions(self, intent: Intent, entities: List[Entity]) -> List[str]:
        """Generate helpful suggestions based on context"""
        suggestions = []
        
        if intent.name == 'unknown' or intent.confidence < 0.3:
            suggestions = [
                "Intenta: 'Genera horario para 1ro primaria'",
                "Intenta: 'El profesor García no puede los lunes'",
                "Intenta: 'Clases de 45 minutos'",
                "Escribe 'ayuda' para ver todos los comandos",
            ]
        elif intent.name == 'generate_schedule' and not any(e.type == 'grade' for e in entities):
            suggestions = [
                "Especifica el grado: '1ro primaria', '2do básico', etc.",
                "Ejemplo: 'Genera horario para 3ro primaria sección A'",
            ]
        elif intent.name == 'assign_teacher' and not any(e.type == 'subject' for e in entities):
            suggestions = [
                "Especifica la materia: 'Matemáticas', 'Español', etc.",
            ]
        
        return suggestions
    
    def process(self, text: str) -> NLPResult:
        """
        Complete NLP processing pipeline
        Returns intent, entities, and suggestions
        """
        normalized = self.normalize(text)
        intent = self.classify_intent(text)
        entities = self.extract_entities(text)
        suggestions = self.get_suggestions(intent, entities)
        
        return NLPResult(
            intent=intent,
            entities=entities,
            normalized_text=normalized,
            suggestions=suggestions
        )
    
    def fuzzy_match(self, text: str, options: Dict[str, str]) -> Optional[Tuple[str, float]]:
        """Find best fuzzy match from options"""
        normalized = self.normalize(text, remove_accents=True)
        
        best_match = None
        best_ratio = 0.0
        
        for key, value in options.items():
            norm_key = self.normalize(key, remove_accents=True)
            ratio = SequenceMatcher(None, normalized, norm_key).ratio()
            
            if ratio > best_ratio and ratio >= self.fuzzy_threshold:
                best_ratio = ratio
                best_match = value
        
        return (best_match, best_ratio) if best_match else None


# Create global instance
nlp_engine = SpanishNLPEngine()
