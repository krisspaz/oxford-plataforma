"""
FASE 5: Criterio Pedagógico Real
================================
Optimización por aprendizaje, no solo tiempo
"""

from dataclasses import dataclass
from typing import List, Dict, Optional
from enum import Enum
import logging

logger = logging.getLogger(__name__)


class SubjectCategory(Enum):
    HEAVY = "heavy"          # Matemáticas, Física, Química
    MEDIUM = "medium"        # Historia, Lengua, Ciencias
    LIGHT = "light"          # Arte, Música, Educación Física
    PRACTICAL = "practical"  # Laboratorios, Talleres


class AgeGroup(Enum):
    PRESCHOOL = "preschool"      # 4-6 años
    ELEMENTARY = "elementary"    # 7-12 años
    MIDDLE = "middle"            # 13-15 años
    HIGH = "high"                # 16-18 años


@dataclass
class CognitiveLoadConfig:
    """Configuración de carga cognitiva por edad"""
    max_heavy_consecutive: int
    max_heavy_per_day: int
    optimal_attention_minutes: int
    break_frequency_periods: int
    best_heavy_period: int  # 1-based, typically morning


# Configuración científica basada en investigación educativa
COGNITIVE_LOAD_BY_AGE = {
    AgeGroup.PRESCHOOL: CognitiveLoadConfig(
        max_heavy_consecutive=1,
        max_heavy_per_day=2,
        optimal_attention_minutes=15,
        break_frequency_periods=2,
        best_heavy_period=2  # Después de activación inicial
    ),
    AgeGroup.ELEMENTARY: CognitiveLoadConfig(
        max_heavy_consecutive=2,
        max_heavy_per_day=3,
        optimal_attention_minutes=25,
        break_frequency_periods=3,
        best_heavy_period=2
    ),
    AgeGroup.MIDDLE: CognitiveLoadConfig(
        max_heavy_consecutive=2,
        max_heavy_per_day=4,
        optimal_attention_minutes=35,
        break_frequency_periods=4,
        best_heavy_period=2
    ),
    AgeGroup.HIGH: CognitiveLoadConfig(
        max_heavy_consecutive=3,
        max_heavy_per_day=5,
        optimal_attention_minutes=45,
        break_frequency_periods=4,
        best_heavy_period=1  # Pueden empezar fuerte
    ),
}


# Clasificación de materias por carga cognitiva
SUBJECT_CLASSIFICATION = {
    # HEAVY
    "matematicas": SubjectCategory.HEAVY,
    "matemáticas": SubjectCategory.HEAVY,
    "fisica": SubjectCategory.HEAVY,
    "física": SubjectCategory.HEAVY,
    "quimica": SubjectCategory.HEAVY,
    "química": SubjectCategory.HEAVY,
    "calculo": SubjectCategory.HEAVY,
    "algebra": SubjectCategory.HEAVY,
    
    # MEDIUM
    "historia": SubjectCategory.MEDIUM,
    "lengua": SubjectCategory.MEDIUM,
    "español": SubjectCategory.MEDIUM,
    "literatura": SubjectCategory.MEDIUM,
    "ciencias": SubjectCategory.MEDIUM,
    "biologia": SubjectCategory.MEDIUM,
    "biología": SubjectCategory.MEDIUM,
    "geografia": SubjectCategory.MEDIUM,
    "geografía": SubjectCategory.MEDIUM,
    "ingles": SubjectCategory.MEDIUM,
    "inglés": SubjectCategory.MEDIUM,
    
    # LIGHT
    "arte": SubjectCategory.LIGHT,
    "musica": SubjectCategory.LIGHT,
    "música": SubjectCategory.LIGHT,
    "educacion fisica": SubjectCategory.LIGHT,
    "educación física": SubjectCategory.LIGHT,
    "deportes": SubjectCategory.LIGHT,
    "dibujo": SubjectCategory.LIGHT,
    
    # PRACTICAL
    "laboratorio": SubjectCategory.PRACTICAL,
    "taller": SubjectCategory.PRACTICAL,
    "computacion": SubjectCategory.PRACTICAL,
    "computación": SubjectCategory.PRACTICAL,
    "informatica": SubjectCategory.PRACTICAL,
    "informática": SubjectCategory.PRACTICAL,
}


@dataclass
class PedagogicalValidation:
    """Resultado de validación pedagógica"""
    is_valid: bool
    score: float  # 0-100
    issues: List[str]
    recommendations: List[str]
    justification: str


class PedagogicalCriteria:
    """
    Motor de criterio pedagógico real
    
    Basado en:
    - Teoría de carga cognitiva (Sweller)
    - Curvas de atención
    - Alternar teoría y práctica
    """
    
    def __init__(self):
        self.load_config = COGNITIVE_LOAD_BY_AGE
        self.subject_category = SUBJECT_CLASSIFICATION
    
    def get_age_group(self, grade_level: str) -> AgeGroup:
        """Determinar grupo de edad por nivel"""
        grade_lower = grade_level.lower()
        
        if "pre" in grade_lower or "kinder" in grade_lower:
            return AgeGroup.PRESCHOOL
        elif "primaria" in grade_lower or "elementary" in grade_lower:
            return AgeGroup.ELEMENTARY
        elif "secundaria" in grade_lower or "middle" in grade_lower or "basico" in grade_lower:
            return AgeGroup.MIDDLE
        else:
            return AgeGroup.HIGH
    
    def get_subject_category(self, subject_name: str) -> SubjectCategory:
        """Clasificar materia por carga cognitiva"""
        subject_lower = subject_name.lower().strip()
        return self.subject_category.get(subject_lower, SubjectCategory.MEDIUM)
    
    def validate_schedule(self, schedule: List[Dict], grade_level: str) -> PedagogicalValidation:
        """
        Validar horario completo según criterios pedagógicos
        
        Args:
            schedule: Lista de bloques [{subject, period, day}]
            grade_level: Nivel académico
        
        Returns:
            PedagogicalValidation con resultado
        """
        age_group = self.get_age_group(grade_level)
        config = self.load_config[age_group]
        
        issues = []
        recommendations = []
        score = 100.0
        
        # Agrupar por día
        by_day = {}
        for block in schedule:
            day = block.get("day", "unknown")
            if day not in by_day:
                by_day[day] = []
            by_day[day].append(block)
        
        # Validar cada día
        for day, day_schedule in by_day.items():
            # Ordenar por período
            day_schedule.sort(key=lambda x: x.get("period", 0))
            
            # 1. Contar materias pesadas
            heavy_count = sum(
                1 for b in day_schedule 
                if self.get_subject_category(b.get("subject", "")) == SubjectCategory.HEAVY
            )
            
            if heavy_count > config.max_heavy_per_day:
                issues.append(f"{day}: {heavy_count} materias pesadas (máx: {config.max_heavy_per_day})")
                score -= 10
            
            # 2. Verificar materias pesadas consecutivas
            consecutive_heavy = 0
            for block in day_schedule:
                if self.get_subject_category(block.get("subject", "")) == SubjectCategory.HEAVY:
                    consecutive_heavy += 1
                    if consecutive_heavy > config.max_heavy_consecutive:
                        issues.append(f"{day}: {consecutive_heavy} materias pesadas consecutivas")
                        score -= 15
                else:
                    consecutive_heavy = 0
            
            # 3. Verificar alternancia teoría-práctica
            categories = [self.get_subject_category(b.get("subject", "")) for b in day_schedule]
            if len(categories) >= 4:
                # Debe haber variación
                unique_categories = set(categories)
                if len(unique_categories) < 2:
                    issues.append(f"{day}: Falta variación en tipos de materia")
                    score -= 5
            
            # 4. Materia pesada en horario óptimo
            if day_schedule:
                first_heavy_period = None
                for block in day_schedule:
                    if self.get_subject_category(block.get("subject", "")) == SubjectCategory.HEAVY:
                        first_heavy_period = block.get("period", 0)
                        break
                
                if first_heavy_period and first_heavy_period > 4:
                    recommendations.append(f"{day}: Mover materias pesadas a la mañana")
        
        # Generar justificación
        if score >= 90:
            justification = "✅ El horario cumple con criterios pedagógicos óptimos para el desarrollo cognitivo."
        elif score >= 70:
            justification = "⚠️ El horario es aceptable pero tiene áreas de mejora pedagógica."
        else:
            justification = "❌ El horario presenta problemas significativos para el aprendizaje efectivo."
        
        return PedagogicalValidation(
            is_valid=score >= 60,
            score=max(0, score),
            issues=issues,
            recommendations=recommendations,
            justification=justification
        )
    
    def generate_justification(self, subject: str, period: int, grade_level: str) -> str:
        """Generar justificación pedagógica para una asignación"""
        age_group = self.get_age_group(grade_level)
        config = self.load_config[age_group]
        category = self.get_subject_category(subject)
        
        justifications = []
        
        if category == SubjectCategory.HEAVY:
            if period <= 2:
                justifications.append(f"✅ {subject} asignada en período óptimo de atención ({period})")
            else:
                justifications.append(f"⚠️ {subject} idealmente debería estar en períodos 1-2")
        
        if category == SubjectCategory.PRACTICAL:
            justifications.append(f"✅ {subject} proporciona balance práctico al día")
        
        return " | ".join(justifications) if justifications else "Sin observaciones"
    
    def suggest_optimal_order(self, subjects: List[str], grade_level: str) -> List[str]:
        """Sugerir orden óptimo de materias para un día"""
        age_group = self.get_age_group(grade_level)
        
        # Clasificar materias
        heavy = [s for s in subjects if self.get_subject_category(s) == SubjectCategory.HEAVY]
        medium = [s for s in subjects if self.get_subject_category(s) == SubjectCategory.MEDIUM]
        light = [s for s in subjects if self.get_subject_category(s) == SubjectCategory.LIGHT]
        practical = [s for s in subjects if self.get_subject_category(s) == SubjectCategory.PRACTICAL]
        
        # Orden óptimo: Light → Heavy → Medium → Practical → Light
        optimal = []
        
        # Activación inicial (excepto HIGH que pueden empezar fuerte)
        if age_group != AgeGroup.HIGH and light:
            optimal.append(light.pop(0))
        
        # Materias pesadas cuando hay más atención
        optimal.extend(heavy)
        
        # Alternar con medium
        optimal.extend(medium)
        
        # Prácticas después del receso
        optimal.extend(practical)
        
        # Light al final
        optimal.extend(light)
        
        return optimal


# Global instance
pedagogical_criteria = PedagogicalCriteria()
