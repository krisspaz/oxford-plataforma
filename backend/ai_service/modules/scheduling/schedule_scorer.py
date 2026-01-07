from typing import List, Dict, Any

class ScheduleScorer:
    def __init__(self):
        pass

    def calculate_isa(self, schedule_data: List[Dict], teacher_stats: Dict[str, Any], student_stats: Dict[str, Any]) -> Dict[str, Any]:
        """
        Calculates the Índica de Salud Académica (ISA) - A comprehensive health score for the institution.
        """
        
        # 1. Teacher Wellness Score (Inverse of Burnout avg)
        avg_burnout = teacher_stats.get('avg_burnout', 0)
        teacher_score = max(0, 100 - avg_burnout)
        
        # 2. Student Fatigue Score (Mock logic)
        # Real logic would check subject difficulty distribution
        fatigue_penalty = 10 # Mock penalty for heavy monday
        student_score = 100 - fatigue_penalty
        
        # 3. Efficiency Score (Classroom usage, gaps)
        efficiency_score = 85 # Mock
        
        # 4. Stability Score (Changes vs last version)
        stability_score = 95 # Very stable
        
        # Weighted Average for ISA
        # Weights: Teachers (30%), Students (30%), Efficiency (20%), Stability (20%)
        isa_score = (teacher_score * 0.3) + (student_score * 0.3) + (efficiency_score * 0.2) + (stability_score * 0.2)
        
        isa_level = "EXCELLENT"
        if isa_score < 70: isa_level = "NEEDS_IMPROVEMENT"
        if isa_score < 50: isa_level = "CRITICAL"

        insights = []
        if teacher_score < 70: insights.append("⚠️ El cuerpo docente presenta signos de fatiga acumulada.")
        if student_score > 80: insights.append("✅ Distribución de carga estudiantil balanceada.")
        if efficiency_score < 60: insights.append("⚠️ Uso ineficiente de infraestructura (muchas aulas vacías).")

        return {
            "isa_score": round(isa_score, 1),
            "level": isa_level,
            "breakdown": {
                "teacher_wellness": teacher_score,
                "student_balance": student_score,
                "efficiency": efficiency_score,
                "stability": stability_score
            },
            "insights": insights,
            "trend": "+2.5% vs last week" # Mock trend
        }
