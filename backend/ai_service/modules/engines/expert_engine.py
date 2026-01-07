"""
Expert AI Reasoning Engine for Oxford
======================================
Central brain - NOT a chatbot
"""

from dataclasses import dataclass, field
from typing import List, Dict, Optional, Any
from enum import Enum
import logging

logger = logging.getLogger(__name__)


class UserRole(Enum):
    ADMIN = "ROLE_ADMIN"
    SUPER_ADMIN = "ROLE_SUPER_ADMIN"
    DIRECTOR = "ROLE_DIRECTOR"
    COORDINATION = "ROLE_COORDINATION"
    TEACHER = "ROLE_TEACHER"
    DOCENTE = "ROLE_DOCENTE"
    SECRETARY = "ROLE_SECRETARY"
    STUDENT = "ROLE_STUDENT"
    UNKNOWN = "UNKNOWN"


class Priority(Enum):
    ALTA = "ALTA"
    MEDIA = "MEDIA"
    BAJA = "BAJA"


@dataclass
class StructuredResponse:
    """
    Formato obligatorio de respuesta experta
    """
    resultado: str  # ✅ Permitido / ❌ No permitido / ℹ️ Informativo
    motivo: str
    reglas_aplicadas: List[str] = field(default_factory=list)
    riesgos_detectados: List[str] = field(default_factory=list)
    alternativas: List[str] = field(default_factory=list)
    recomendacion_final: str = ""
    mejoras_detectadas: List[Dict] = field(default_factory=list)
    datos_faltantes: List[str] = field(default_factory=list)
    
    def to_markdown(self) -> str:
        """Format as markdown string"""
        md = f"**Resultado:** {self.resultado}\n\n"
        md += f"**Motivo:** {self.motivo}\n\n"
        
        if self.reglas_aplicadas:
            md += "**Reglas aplicadas:**\n"
            for regla in self.reglas_aplicadas:
                md += f"• {regla}\n"
            md += "\n"
        
        if self.riesgos_detectados:
            md += "⚠️ **Riesgos detectados:**\n"
            for riesgo in self.riesgos_detectados:
                md += f"• {riesgo}\n"
            md += "\n"
        
        if self.alternativas:
            md += "💡 **Alternativas propuestas:**\n"
            for alt in self.alternativas:
                md += f"• {alt}\n"
            md += "\n"
        
        if self.recomendacion_final:
            md += f"📌 **Recomendación final:** {self.recomendacion_final}\n"
        
        if self.datos_faltantes:
            md += "\n❓ **Datos necesarios para respuesta completa:**\n"
            for dato in self.datos_faltantes:
                md += f"• {dato}\n"
        
        return md
    
    def to_dict(self) -> Dict:
        return {
            "resultado": self.resultado,
            "motivo": self.motivo,
            "reglas_aplicadas": self.reglas_aplicadas,
            "riesgos_detectados": self.riesgos_detectados,
            "alternativas": self.alternativas,
            "recomendacion_final": self.recomendacion_final,
            "datos_faltantes": self.datos_faltantes,
        }


class ExpertReasoningEngine:
    """
    Cerebro central del sistema - NO un chatbot
    
    Principios:
    - No inventar información
    - Si faltan datos, decirlo explícitamente
    - Lógica y reglas sobre respuestas genéricas
    - Justificar siempre
    """
    
    def __init__(self):
        self.logger = logging.getLogger(__name__)
        
        # Import dependencies
        try:
            from rule_engine import rule_engine
            from knowledge_base import knowledge_base
            from memory import memory
            self.rules = rule_engine
            self.knowledge = knowledge_base
            self.memory = memory
        except ImportError as e:
            self.logger.warning(f"Some modules not available: {e}")
            self.rules = None
            self.knowledge = None
            self.memory = None
    
    def analyze_request(self, 
                        query: str, 
                        user_role: str, 
                        user_id: str = None,
                        context: Dict = None) -> StructuredResponse:
        """
        Main analysis function following expert reasoning
        
        Steps:
        1. Identify user type and permissions
        2. Identify required data
        3. Identify applicable rules
        4. Search for inconsistencies/conflicts
        5. Propose improvements
        6. If incomplete, request only necessary data
        """
        context = context or {}
        
        # Step 1: Validate role and permissions
        role = self._parse_role(user_role)
        permission_check = self._check_permissions(role, query)
        if not permission_check["allowed"]:
            return StructuredResponse(
                resultado="❌ Acción no permitida",
                motivo=permission_check["reason"],
                reglas_aplicadas=["Validación de permisos por rol"],
                recomendacion_final="Contacta al administrador si necesitas acceso."
            )
        
        # Step 2: Analyze query type
        query_analysis = self._analyze_query(query.lower())
        
        # Step 3: Handle based on query type
        if query_analysis["type"] == "schedule_change":
            return self._handle_schedule_change(query, context, role)
        elif query_analysis["type"] == "info_request":
            return self._handle_info_request(query, context, role)
        elif query_analysis["type"] == "validation":
            return self._handle_validation(query, context, role)
        elif query_analysis["type"] == "report":
            return self._handle_report_request(query, context, role)
        else:
            # Check knowledge base
            return self._handle_general_query(query, context, role)
    
    def _parse_role(self, role_str: str) -> UserRole:
        """Parse role string to enum"""
        for role in UserRole:
            if role.value == role_str:
                return role
        return UserRole.UNKNOWN
    
    def _check_permissions(self, role: UserRole, query: str) -> Dict:
        """Check if role can perform the requested action"""
        query_lower = query.lower()
        
        # Admin/Super Admin can do everything
        if role in [UserRole.ADMIN, UserRole.SUPER_ADMIN]:
            return {"allowed": True, "reason": ""}
        
        # Director can do most things except system config
        if role in [UserRole.DIRECTOR, UserRole.COORDINATION]:
            if "configurar sistema" in query_lower or "eliminar usuario" in query_lower:
                return {"allowed": False, "reason": "Solo administradores pueden configurar el sistema"}
            return {"allowed": True, "reason": ""}
        
        # Teachers have limited access
        if role in [UserRole.TEACHER, UserRole.DOCENTE]:
            admin_actions = ["crear horario", "eliminar", "generar horario global", "modificar reglamento"]
            for action in admin_actions:
                if action in query_lower:
                    return {
                        "allowed": False, 
                        "reason": f"Tu rol de Docente no permite: {action}. Contacta a Coordinación."
                    }
            return {"allowed": True, "reason": ""}
        
        # Students have very limited access
        if role == UserRole.STUDENT:
            allowed_actions = ["ver mi", "mis notas", "mi horario", "tareas", "ayuda"]
            if not any(action in query_lower for action in allowed_actions):
                return {
                    "allowed": False,
                    "reason": "Tu rol de Estudiante solo permite consultar tus datos personales."
                }
            return {"allowed": True, "reason": ""}
        
        return {"allowed": True, "reason": ""}
    
    def _analyze_query(self, query: str) -> Dict:
        """Classify the query type"""
        schedule_keywords = ["cambiar horario", "mover clase", "modificar horario", "asignar periodo"]
        validation_keywords = ["puedo", "es posible", "se puede", "permitido"]
        report_keywords = ["reporte", "estadística", "métricas", "análisis"]
        
        for kw in schedule_keywords:
            if kw in query:
                return {"type": "schedule_change", "keyword": kw}
        
        for kw in validation_keywords:
            if kw in query:
                return {"type": "validation", "keyword": kw}
        
        for kw in report_keywords:
            if kw in query:
                return {"type": "report", "keyword": kw}
        
        return {"type": "info_request", "keyword": None}
    
    def _handle_schedule_change(self, query: str, context: Dict, role: UserRole) -> StructuredResponse:
        """Handle schedule modification requests with rule validation"""
        
        # Check what data we have
        datos_faltantes = []
        if "teacher_id" not in context:
            datos_faltantes.append("ID del docente afectado")
        if "day" not in context:
            datos_faltantes.append("Día de la semana")
        if "period" not in context:
            datos_faltantes.append("Período/hora a modificar")
        
        if datos_faltantes:
            return StructuredResponse(
                resultado="⏸️ Información incompleta",
                motivo="No puedo procesar el cambio sin datos específicos.",
                datos_faltantes=datos_faltantes,
                recomendacion_final="Proporciona los datos faltantes para continuar."
            )
        
        # Validate with rule engine
        reglas = ["R001: Máximo 40h semanales por docente", "R002: Máximo 4 períodos consecutivos"]
        riesgos = []
        alternativas = []
        
        # Simulate rule checking
        teacher_hours = context.get("teacher_current_hours", 0)
        if teacher_hours >= 38:
            riesgos.append(f"Docente cerca del límite ({teacher_hours}/40h)")
            alternativas.append("Distribuir carga con otro docente")
        
        return StructuredResponse(
            resultado="✅ Cambio viable (pendiente validación completa)",
            motivo="Los datos proporcionados son válidos. Se requiere confirmar disponibilidad en sistema.",
            reglas_aplicadas=reglas,
            riesgos_detectados=riesgos,
            alternativas=alternativas,
            recomendacion_final="Ejecutar cambio en módulo de Horarios para validación final."
        )
    
    def _handle_info_request(self, query: str, context: Dict, role: UserRole) -> StructuredResponse:
        """Handle information requests using knowledge base"""
        
        # Search in knowledge base
        if self.knowledge:
            answer = self.knowledge.get_answer(query)
            if answer:
                return StructuredResponse(
                    resultado="ℹ️ Información encontrada",
                    motivo=answer,
                    reglas_aplicadas=["Búsqueda en base de conocimiento"],
                    recomendacion_final="Si necesitas más detalles, especifica tu consulta."
                )
        
        return StructuredResponse(
            resultado="❓ Información no disponible",
            motivo="No encontré información específica sobre esta consulta en la base de conocimiento.",
            datos_faltantes=["Documentación institucional relacionada"],
            mejoras_detectadas=[{
                "area": "Base de conocimiento",
                "descripcion": f"Agregar información sobre: {query[:50]}",
                "prioridad": "MEDIA"
            }],
            recomendacion_final="Consulta con Coordinación o revisa los documentos institucionales."
        )
    
    def _handle_validation(self, query: str, context: Dict, role: UserRole) -> StructuredResponse:
        """Handle 'can I do X?' type questions"""
        
        # Check knowledge base for FAQs
        if self.knowledge:
            answer = self.knowledge.get_answer(query)
            if answer:
                return StructuredResponse(
                    resultado="ℹ️ Consulta el reglamento",
                    motivo=answer,
                    recomendacion_final="Si aún tienes dudas, consulta con Coordinación."
                )
        
        return StructuredResponse(
            resultado="⚠️ Validación manual requerida",
            motivo="Esta acción requiere verificación con las reglas institucionales específicas.",
            reglas_aplicadas=["Consulta de permisos"],
            recomendacion_final="Envía solicitud a Coordinación para aprobación."
        )
    
    def _handle_report_request(self, query: str, context: Dict, role: UserRole) -> StructuredResponse:
        """Handle report/analytics requests"""
        
        if role not in [UserRole.ADMIN, UserRole.SUPER_ADMIN, UserRole.DIRECTOR, UserRole.COORDINATION]:
            return StructuredResponse(
                resultado="❌ Acceso denegado",
                motivo="Los reportes institucionales solo están disponibles para Dirección y Administración.",
                reglas_aplicadas=["Control de acceso por rol"],
                recomendacion_final="Solicita el reporte a tu coordinador."
            )
        
        return StructuredResponse(
            resultado="📊 Generando reporte",
            motivo="Accede al módulo de Reportes para ver datos actualizados.",
            recomendacion_final="Usa el menú lateral > Reportes para información detallada."
        )
    
    def _handle_general_query(self, query: str, context: Dict, role: UserRole) -> StructuredResponse:
        """Handle general queries"""
        
        # Try knowledge base
        if self.knowledge:
            faqs = self.knowledge.search_faqs(query, top_k=1)
            if faqs:
                return StructuredResponse(
                    resultado="ℹ️ Información",
                    motivo=faqs[0]["a"],
                    recomendacion_final="¿Necesitas algo más?"
                )
        
        return StructuredResponse(
            resultado="❓ No tengo información suficiente",
            motivo="Esta consulta no está en mi base de conocimiento actual.",
            datos_faltantes=["Contexto adicional sobre la consulta"],
            mejoras_detectadas=[{
                "area": "Cobertura de conocimiento",
                "descripcion": f"Consulta no cubierta: {query[:100]}",
                "prioridad": "BAJA"
            }],
            recomendacion_final="Reformula la pregunta o consulta directamente con Coordinación."
        )


# Global instance
expert_engine = ExpertReasoningEngine()
