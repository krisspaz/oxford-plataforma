from datetime import datetime
from typing import Dict, Any

class DecisionLogger:
    def __init__(self, log_file="ai_decisions.log"):
        self.log_file = log_file

    def log_decision(self, actor: str, action: str, reason: str, impact: str):
        """
        Point 18: IA con responsabilidad firmada.
        Point 7: Modo auditoría y trazabilidad.
        """
        entry = f"[{datetime.now()}] ACTOR:{actor} ACTION:{action} REASON:{reason} IMPACT:{impact}\n"
        with open(self.log_file, "a") as f:
            f.write(entry)

    def get_audit_trail(self) -> str:
        try:
            with open(self.log_file, "r") as f:
                return f.read()
        except:
            return "No audit trail found."

    def analyze_hidden_costs(self, decision: Dict) -> float:
        """
        Point 23: Medición de costo oculto.
        Calculates assumed cost of a decision (stress, overtime, friction).
        """
        base_cost = 100
        if decision.get('overtime', False): base_cost += 500
        if decision.get('teacher_conflict', False): base_cost += 1000
        return base_cost
