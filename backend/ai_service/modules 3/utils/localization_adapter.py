from typing import Dict

class LocalizationAdapter:
    def __init__(self, region="LATAM"):
        self.region = region

    def adapt_rules(self, rules: Dict) -> Dict:
        """
        Point 15: IA entrenada con datos locales (LATAM).
        Adapts generic rules to local cultural/legal context.
        """
        if self.region == "LATAM":
            # Example: Late arrivals due to traffic are common
            rules['strict_punctuality_penalty'] = "LOW" 
            # Example: Special holidays
            rules['holidays'] = ["Semana Santa", "Dia del Maestro"]
        return rules
    
    def get_maturity_index(self) -> Dict:
        """
        Point 34: Índice de Madurez Institucional (IMI).
        """
        return {
            "imi_score": 88,
            "level": "Established",
            "strengths": ["Clear Hierarchy", "Digital Adoption"]
        }
