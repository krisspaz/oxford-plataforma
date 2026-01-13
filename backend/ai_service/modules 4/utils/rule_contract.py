from typing import List, Dict, Any

class RuleContract:
    def __init__(self):
        # Point 21: Gobernanza clara del sistema
        self.hard_rules = [
            {"id": "MAX_STUDENTS", "value": 35, "desc": "No más de 35 alumnos por aula"},
            {"id": "NO_OBSOLETE_LABS", "value": True, "desc": "No asignar laboratorios sin equipo"}
        ]
        self.soft_rules = [
            {"id": "PREFER_MORNING_MATH", "value": True, "desc": "Matemáticas preferiblemente en la mañana"},
            {"id": "TEACHER_GAP_LIMIT", "value": 2, "desc": "Máximo 2 horas libres entre clases"}
        ]
        # Point 38: Modo legado (Intocables)
        self.legacy_rules = [
            {"id": "FRIDAY_EARLY_EXIT", "value": "13:00", "desc": "Salida viernes a las 13:00 (Tradición 20 años)"}
        ]

    def get_contract(self) -> Dict[str, List]:
        return {
            "hard_rules": self.hard_rules,
            "soft_rules": self.soft_rules,
            "legacy_rules": self.legacy_rules
        }

    def validate_action(self, action_type: str, params: Dict) -> Dict:
        """
        Validates an action against the contract.
        Point 8: IA que sabe decir NO.
        """
        violations = []
        
        # Mock validation logic
        if action_type == "assign_room" and params.get("students", 0) > 35:
            violations.append("Violación de Regla Dura: MAX_STUDENTS (Límite 35)")
            
        if action_type == "schedule_slot" and params.get("day") == "Friday" and params.get("hour") > 13:
             violations.append("Violación de Legado: FRIDAY_EARLY_EXIT")

        if violations:
            return {"allowed": False, "violations": violations}
        
        return {"allowed": True, "violations": []}

    def update_rule(self, rule_type: str, rule_id: str, new_value: Any):
        """
        Point 22: Conversión de experiencia en reglas.
        Allows evolving the rules over time.
        """
        # Logic to update rules would go here
        pass
