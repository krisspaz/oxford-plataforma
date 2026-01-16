"""
Knowledge Base for Oxford AI
=============================
Stores institutional documents and FAQs for RAG
"""

from typing import List, Dict, Optional
import json
import os
from pathlib import Path
import logging

logger = logging.getLogger(__name__)


class KnowledgeBase:
    """
    Simple knowledge base for institutional data
    Can be upgraded to vector store (FAISS) later
    """
    
    def __init__(self, data_dir: str = None):
        self.data_dir = data_dir or Path(__file__).parent / "knowledge" / "data"
        self.documents = []
        self.faqs = []
        self.rules = []
        self.load_all()
    
    def load_all(self):
        """Load all knowledge documents"""
        self.load_faqs()
        self.load_rules()
        self.load_holidays()
    
    def load_faqs(self):
        """Load frequently asked questions"""
        # Pre-loaded FAQs for immediate use
        self.faqs = [
            {
                "q": "¿Cuál es el horario del colegio?",
                "a": "El horario escolar es de 7:00 AM a 2:00 PM, de lunes a viernes.",
                "tags": ["horario", "hora", "colegio", "clases"]
            },
            {
                "q": "¿Cuántas horas puede dar un profesor por semana?",
                "a": "El máximo es 40 horas semanales según el reglamento institucional.",
                "tags": ["profesor", "horas", "maximo", "limite"]
            },
            {
                "q": "¿Cómo solicito un cambio de horario?",
                "a": "Debes enviar la solicitud a Coordinación con al menos 3 días de anticipación. Usar el módulo de Solicitudes en el sistema.",
                "tags": ["cambio", "horario", "solicitud"]
            },
            {
                "q": "¿Cuánto dura cada período de clase?",
                "a": "Cada período dura 45 minutos con un receso de 30 minutos después del 4to período.",
                "tags": ["periodo", "clase", "duracion", "receso"]
            },
            {
                "q": "¿Qué hacer si un profesor falta?",
                "a": "Reportar a Coordinación inmediatamente. El sistema buscará un docente sustituto disponible.",
                "tags": ["falta", "ausencia", "sustituto", "profesor"]
            },
            {
                "q": "¿Cuántos períodos consecutivos puede tener un profesor?",
                "a": "Máximo 4 períodos consecutivos. Después debe haber un período libre o receso.",
                "tags": ["consecutivo", "periodos", "profesor", "descanso"]
            },
            {
                "q": "¿Cómo se cargan las notas?",
                "a": "En el módulo Carga de Notas, selecciona tu materia, bimestre y grupo. Ingresa las calificaciones y guarda.",
                "tags": ["notas", "calificaciones", "cargar", "ingresar"]
            },
            {
                "q": "¿Cuándo son los feriados?",
                "a": "Los feriados oficiales son: Año Nuevo, Semana Santa, 1 de Mayo, 30 de Junio, 15 de Septiembre, 20 de Octubre, 1 de Noviembre, 25 de Diciembre.",
                "tags": ["feriado", "festivo", "vacaciones", "asueto"]
            },
            {
                "q": "¿Cómo contacto a los padres de familia?",
                "a": "Usar el módulo de Comunicados en el menú. Puedes enviar mensajes individuales o grupales.",
                "tags": ["padres", "familia", "contactar", "comunicado"]
            },
            {
                "q": "¿Qué hago si pierdo mi carné?",
                "a": "Debes reportarlo inmediatamente a Secretaría y pagar Q25.00 por la reposición.",
                "tags": ["carné", "carne", "perdi", "perdí", "reposición", "identificación"]
            },
            {
                "q": "¿Cómo veo mi horario?",
                "a": "En el menú lateral, selecciona 'Mi Horario'. También puedes preguntarle al Asistente: 'ver mi horario'.",
                "tags": ["horario", "ver", "consultar"]
            },
            {
                "q": "¿Existe un código de vestimenta para padres?",
                "a": "Sí. Para ingresar al colegio se solicita vestimenta decorosa. No se permite el ingreso en ropa de dormir, trajes de baño o ropa extremadamente corta.",
                "tags": ["vestimenta", "padres", "ropa", "ingreso", "normas"]
            },
            {
                "q": "¿Cuál es el protocolo ante un caso de Bullying?",
                "a": "El colegio tiene TOLERANCIA CERO. 1) Reportar al tutor o director. 2) Se investiga en 24h. 3) Se cita a padres. 4) Sanciones desde suspensión hasta expulsión.",
                "tags": ["bullying", "acoso", "molestar", "agresión", "reportar"]
            },
            {
                "q": "¿Puedo llevar celular al colegio?",
                "a": "El uso de celular está prohibido durante las clases salvo autorización expresa del docente para actividad pedagógica. Si se usa sin permiso, será decomisado.",
                "tags": ["celular", "teléfono", "móvil", "prohibido", "traer"]
            },
            {
                "q": "¿Tienen enfermería o servicio médico?",
                "a": "Sí, contamos con enfermería para primeros auxilios. En caso de emergencia mayor, trasladamos al estudiante al centro médico seguro y avisamos a los padres.",
                "tags": ["enfermería", "médico", "salud", "enfermo", "accidente"]
            },
            {
                "q": "¿Qué comida venden en la cafetería?",
                "a": "Ofrecemos refacciones saludables, almuerzos balanceados, frutas y bebidas naturales. No vendemos bebidas energéticas ni comida chatarra en exceso.",
                "tags": ["cafetería", "comida", "almuerzo", "tienda", "venden"]
            },
            {
                "q": "¿Cómo justifico una inasistencia?",
                "a": "Debes enviar una carta firmada por los padres o certificado médico a Coordinación dentro de las 48 horas siguientes a la inasistencia.",
                "tags": ["inasistencia", "faltar", "justificar", "enfermedad", "excusa"]
            },
            {
                "q": "¿Qué pasa si repruebo una materia?",
                "a": "Si repruebas una materia con menos de 60 puntos, tendrás derecho a una (1) recuperación a final de año. Si repruebas más de 3 materias, deberás repetir el grado.",
                "tags": ["reprobar", "perder", "recuperación", "clase", "materia"]
            },
            {
                "q": "¿Cuál es la nota mínima para aprobar?",
                "a": "La nota mínima de aprobación es de 60 puntos sobre 100.",
                "tags": ["nota", "minima", "aprobar", "ganar", "punteo"]
            },
            {
                "q": "¿Cómo solicito una certificación de estudios?",
                "a": "Debes solicitarla en Secretaría. Tiene un costo de Q50.00 y se entrega en 3 días hábiles.",
                "tags": ["certificación", "estudios", "papeles", "documento", "solicitar"]
            },
            {
                "q": "¿Qué hago si pierdo mi carné?",
                "a": "Debes reportarlo inmediatamente a Secretaría y pagar Q25.00 por la reposición.",
                "tags": ["carné", "carne", "perdi", "perdí", "reposición", "identificación"]
            }
        ]
    
    def load_rules(self):
        """Load institutional rules"""
        self.rules = [
            {
                "id": "R001",
                "name": "Máximo de horas semanales",
                "description": "Ningún docente puede exceder 40 horas semanales de clase.",
                "type": "hard",
                "tags": ["docente", "horas", "semana"]
            },
            {
                "id": "R002",
                "name": "Períodos consecutivos",
                "description": "Máximo 4 períodos consecutivos sin descanso.",
                "type": "hard",
                "tags": ["consecutivo", "periodo", "descanso"]
            },
            {
                "id": "R003",
                "name": "Conflicto de aulas",
                "description": "Un aula no puede tener dos clases simultáneas.",
                "type": "hard",
                "tags": ["aula", "conflicto", "clase"]
            },
            {
                "id": "R004",
                "name": "Horario escolar",
                "description": "Las clases deben ser entre 7:00 AM y 2:00 PM.",
                "type": "hard",
                "tags": ["horario", "hora", "clase"]
            },
            {
                "id": "R005",
                "name": "Uso de Plataforma",
                "description": "Todo el personal y alumnos deben utilizar la plataforma Oxford para tareas y comunicados oficiales.",
                "type": "soft",
                "tags": ["plataforma", "sistema", "uso"]
            },
            {
                "id": "R006",
                "name": "Uniforme Completo",
                "description": "Es obligatorio el uso del uniforme completo correspondiente al día. 3 faltas = reporte.",
                "type": "hard",
                "tags": ["uniforme", "ropa", "disciplina"]
            },
            {
                "id": "R007",
                "name": "Puntualidad Docente",
                "description": "El docente debe estar en el aula 5 minutos antes del inicio de la primera clase.",
                "type": "soft",
                "tags": ["puntualidad", "docente", "horario"]
            },
            {
                "id": "R008",
                "name": "Prohibición de Ventas",
                "description": "Está prohibida la venta de productos entre alumnos dentro de las instalaciones.",
                "type": "hard",
                "tags": ["ventas", "negocio", "prohibido"]
            }
        ]
    
    def load_holidays(self):
        """Load holiday dates"""
        self.holidays = {
            "2026-01-01": "Año Nuevo",
            "2026-04-13": "Lunes Santo",
            "2026-04-14": "Martes Santo", 
            "2026-05-01": "Día del Trabajo",
            "2026-06-30": "Día del Ejército",
            "2026-09-15": "Día de la Independencia",
            "2026-10-20": "Día de la Revolución",
            "2026-11-01": "Día de Todos los Santos",
            "2026-12-25": "Navidad",
        }
    
    # =========================================
    # SEARCH FUNCTIONS
    # =========================================
    
    def search_faqs(self, query: str, top_k: int = 3) -> List[Dict]:
        """Simple keyword search in FAQs"""
        query_lower = query.lower()
        scored_faqs = []
        
        # 1. Search Static FAQs
        for faq in self.faqs:
            score = 0
            # Check tags
            for tag in faq["tags"]:
                if tag in query_lower:
                    score += 2
            # Check question
            if any(word in faq["q"].lower() for word in query_lower.split()):
                score += 1
            
            if score > 0:
                scored_faqs.append((score, faq))
                
        # 2. Search Learned Rules (Dynamic Memory)
        try:
            from learning_engine import learning_engine
            active_rules = learning_engine.get_active_rules()
            
            for rule in active_rules:
                # Rule format: id, desc, condition, action, source, priority
                # We treat description as Q and action as A
                r_desc = rule['description'].lower()
                r_action = rule['action']
                score = 0
                
                # Simple containment match
                if any(word in r_desc for word in query_lower.split() if len(word) > 3):
                    score += 3 # Learned rules have high priority
                    
                if score > 0:
                    # Adapt to FAQ format
                    learned_faq = {
                        "q": rule['description'],
                        "a": rule['action'],
                        "tags": ["aprendido", "memoria"]
                    }
                    scored_faqs.append((score, learned_faq))
                    
        except ImportError:
            pass
        except Exception as e:
            logger.error(f"Error reading learned rules: {e}")
        
        # Sort by score descending
        scored_faqs.sort(key=lambda x: x[0], reverse=True)
        
        return [faq for score, faq in scored_faqs[:top_k]]
    
    def get_answer(self, query: str) -> Optional[str]:
        """Get best answer for a query"""
        results = self.search_faqs(query, 1)
        if results:
            return results[0]["a"]
        return None
    
    def search_rules(self, query: str) -> List[Dict]:
        """Search institutional rules"""
        query_lower = query.lower()
        matched = []
        
        for rule in self.rules:
            for tag in rule["tags"]:
                if tag in query_lower:
                    matched.append(rule)
                    break
        
        return matched
    
    def is_holiday(self, date_str: str) -> Optional[str]:
        """Check if date is a holiday"""
        return self.holidays.get(date_str)
    
    def get_holiday_list(self) -> str:
        """Get formatted holiday list"""
        formatted = "📅 **Feriados Oficiales:**\n\n"
        for date, name in sorted(self.holidays.items()):
            formatted += f"• {date}: {name}\n"
        return formatted


# Global instance
knowledge_base = KnowledgeBase()
