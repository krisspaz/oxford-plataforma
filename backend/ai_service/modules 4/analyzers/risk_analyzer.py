from typing import List, Dict, Any

class RiskAnalyzer:
    def __init__(self):
        pass

    def analyze(self, student_data: Dict[str, Any]) -> Dict[str, Any]:
        """
        Analyze student performance to detect risks.
        Input format:
        {
            "id": 123,
            "grades": [
                {"subject": "Matemáticas", "score": 65, "history": [80, 70, 65]},
                {"subject": "Ciencias", "score": 90, "history": [90, 92, 90]}
            ],
            "attendance": 85 # percentage
        }
        """
        risk_score = 0
        alerts = []
        recommendations = []

        grades = student_data.get('grades', [])
        attendance = student_data.get('attendance', 100)

        # 1. Attendance Analysis
        if attendance < 75:
            risk_score += 40
            alerts.append("⚠️ Asistencia crítica (<75%). Riesgo de reprobación por inasistencia.")
            recommendations.append("Programar reunión con padres para discutir asistencia.")
        elif attendance < 85:
            risk_score += 15
            alerts.append("⚠️ Asistencia baja (<85%).")

        # 2. Grade Analysis
        failing_count = 0
        for g in grades:
            subject = g.get('subject', 'Unknown')
            score = g.get('score', 0)
            history = g.get('history', [])

            # Failing Check
            if score < 60:
                failing_count += 1
                alerts.append(f"❌ Reprobando {subject} ({score}).")
                risk_score += 20
            elif score < 70:
                alerts.append(f"⚠️ Rendimiento bajo en {subject} ({score}).")
                risk_score += 10

            # Trend Analysis (Declining)
            if len(history) >= 2:
                if history[-1] < history[-2] - 10:
                    alerts.append(f"📉 Caída significativa en {subject} (De {history[-2]} a {history[-1]}).")
                    risk_score += 10

        if failing_count >= 3:
            risk_score += 30
            alerts.append("🚨 Riesgo Académico Alto: Reprobando 3+ materias.")
        
        # Cap risk score
        risk_score = min(100, risk_score)

        # Categorize
        if risk_score > 70:
            level = "CRITICAL"
        elif risk_score > 30:
            level = "WARNING"
        else:
            level = "SAFE"

        return {
            "student_id": student_data.get('id'),
            "risk_score": risk_score,
            "risk_level": level,
            "alerts": alerts,
            "recommendations": recommendations
        }
