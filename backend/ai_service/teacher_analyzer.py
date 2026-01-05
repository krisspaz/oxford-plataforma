from typing import List, Dict, Any

class TeacherAnalyzer:
    def __init__(self):
        self.MAX_DAILY_HOURS = 8
        self.MAX_CONSECUTIVE_HOURS = 5
        self.MAX_WEEKLY_HOURS = 40

    def analyze_workload(self, teacher_id: int, schedule_data: List[Dict[str, Any]]) -> Dict[str, Any]:
        """
        Analyzes a teacher's schedule to predict burnout and identify issues.
        schedule_data expected format: [{'day': 'Monday', 'hour': 1, 'subject': 'Math'}, ...]
        """
        daily_load = {}
        consecutive_blocks = 0
        max_consecutive = 0
        total_hours = 0
        gaps = 0
        
        # Sort by day/hour
        # Simulating data structure processing
        # In a real scenario, we'd parse the schedule list thoroughly
        
        # Mock logic for demonstration/MVP of the algorithm
        risk_score = 0
        alerts = []
        positive_factors = []

        # 1. Calculate Daily Load
        # Group by day
        days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
        for day in days:
            daily_hours = len([s for s in schedule_data if s.get('day') == day])
            daily_load[day] = daily_hours
            total_hours += daily_hours
            
            if daily_hours > self.MAX_DAILY_HOURS:
                risk_score += 10
                alerts.append(f"Carga excesiva el {day} ({daily_hours} horas)")

        # 2. Check Consecutive Blocks (Simplified)
        # Assuming sorted input
        current_streak = 0
        for i in range(len(schedule_data) - 1):
            if schedule_data[i].get('day') == schedule_data[i+1].get('day'):
                # Assuming hour is integer
                if schedule_data[i+1].get('hour') == schedule_data[i].get('hour') + 1:
                    current_streak += 1
                else:
                    current_streak = 0
                    gaps += 1
            else:
                current_streak = 0
            
            max_consecutive = max(max_consecutive, current_streak)

        if max_consecutive > self.MAX_CONSECUTIVE_HOURS:
            risk_score += 15
            alerts.append(f"Bloques consecutivos peligrosos: {max_consecutive} horas seguidas sin descanso.")

        # 3. Global Assessment
        if total_hours > self.MAX_WEEKLY_HOURS:
            risk_score += 20
            alerts.append(f"Carga semanal crítica: {total_hours} horas (Máx rec: {self.MAX_WEEKLY_HOURS})")

        # 4. Gaps (Horarios huecos)
        if gaps > 5:
             risk_score += 5
             alerts.append(f"Horario ineficiente: {gaps} períodos libres intermedios (huecos).")

        # Normalize Risk
        risk_score = min(100, risk_score)
        
        status = "SAFE"
        if risk_score > 70:
            status = "CRITICAL"
        elif risk_score > 30:
            status = "WARNING"

        return {
            "teacher_id": teacher_id,
            "burnout_risk_score": risk_score,
            "status": status,
            "metrics": {
                "total_hours": total_hours,
                "max_consecutive": max_consecutive,
                "gaps": gaps
            },
            "alerts": alerts,
            "recommendations": self._generate_recommendations(alerts)
        }

    def _generate_recommendations(self, alerts: List[str]) -> List[str]:
        recs = []
        for alert in alerts:
            if "Carga excesiva" in alert:
                recs.append("Redistribuir carga a Jueves o Viernes si es posible.")
            if "Bloques consecutivos" in alert:
                recs.append("Insertar receso obligatorio después de la 3era hora.")
            if "semanal crítica" in alert:
                recs.append("Considere contratar un auxiliar o dividir cursos.")
            if "ineficiente" in alert:
                recs.append("Compactar horario para permitir salida temprana.")
        
        if not recs:
            recs.append("Horario saludable. Mantener así.")
            
        return recs
