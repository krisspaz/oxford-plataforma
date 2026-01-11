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
                "q": "¿Cómo veo mi horario?",
                "a": "En el menú lateral, selecciona 'Mi Horario'. También puedes preguntarle al Asistente: 'ver mi horario'.",
                "tags": ["horario", "ver", "consultar", "mi"]
            },
            {
                "q": "¿Cuál es el uniforme oficial del Colegio Oxford?",
                "a": "El uniforme de diario consta de polo blanca con el logo del colegio y pantalón azul marino. El uniforme de educación física es pants y chumpa institucional con playera gris. Zapatos negros escolares para diario y tenis blancos para física.",
                "tags": ["uniforme", "ropa", "vestimenta", "pants", "física"]
            },
            {
                "q": "¿Cuáles son los requisitos de inscripción o admisión?",
                "a": "Para inscribirse se requiere: Certificado de nacimiento (RENAP), certificados de estudios de años anteriores, carta de buena conducta, solvencia de pago del colegio anterior y DPI de los padres encargados.",
                "tags": ["inscripción", "admisión", "requisitos", "papelería", "documentos"]
            },
            {
                "q": "¿Dónde y cómo puedo realizar los pagos de mensualidad?",
                "a": "Los pagos pueden realizarse en el Banco Industrial (Convenio 3452) o Banrural. También aceptamos transferencias y pagos con tarjeta en secretaría. Recuerda enviar tu boleta por WhatsApp al departamento de contabilidad.",
                "tags": ["pago", "mensualidad", "banco", "tarjeta", "costo"]
            },
            {
                "q": "¿Cuál es el número de teléfono o contacto del colegio?",
                "a": "Puedes contactarnos al PBX: 2411-5555 o al WhatsApp de Coordinación: 5555-1234. Nuestro correo es info@colegiooxford.edu.gt.",
                "tags": ["contacto", "teléfono", "whatsapp", "correo", "llamar"]
            },
            {
                "q": "¿Tienen bus escolar?",
                "a": "Sí, contamos con servicio de bus para diferentes zonas. Debes solicitar la ruta en secretaría al momento de inscribirte para cotizar el precio según tu ubicación.",
                "tags": ["bus", "transporte", "ruta", "microbus"]
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
