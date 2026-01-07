from typing import Dict, Tuple

class EthicsValidator:
    def __init__(self):
        pass

    def validate_request(self, request_data: Dict) -> Tuple[bool, str]:
        """
        Point 8: IA que sabe decir NO.
        Point 20: IA con ética operacional.
        """
        # Example: Don't allow firing a teacher via AI command
        if "despedir" in request_data.get('command', '').lower():
            return False, "❌ I cannot execute this. Point 8: Ethical Guardrail activated. Human HR review required."
        
        # Example: Don't overload the same teacher always
        if request_data.get('target_teacher') == "Teacher_A" and request_data.get('current_load') > 40:
             return False, "❌ Refused. Point 20: Unfair distribution of labor detected."

        return True, "Approved"
