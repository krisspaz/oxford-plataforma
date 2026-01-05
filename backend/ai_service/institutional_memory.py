import json
import os
from datetime import datetime
from typing import List, Dict, Any

class InstitutionalMemory:
    def __init__(self, db_path="memory.json"):
        self.db_path = db_path
        self.history = self._load_memory()
        self.patterns = []

    def _load_memory(self) -> List[Dict]:
        if os.path.exists(self.db_path):
            try:
                with open(self.db_path, 'r') as f:
                    return json.load(f)
            except:
                return []
        return []

    def _save_memory(self):
        with open(self.db_path, 'w') as f:
            json.dump(self.history, f, indent=4)

    def remember_decision(self, decision_type: str, details: Dict, user: str, reason: str = ""):
        """
        Stores a significant decision (e.g., manual schedule change, policy override).
        Point 2: Aprende del historial.
        """
        entry = {
            "timestamp": datetime.now().isoformat(),
            "type": decision_type,
            "details": details,
            "user": user,
            "reason": reason
        }
        self.history.append(entry)
        self._save_memory()
        self._analyze_patterns() # Re-analyze patterns on update

    def _analyze_patterns(self):
        """
        Detects frequent exceptions or changes.
        Point 26: IA que sobrevive al caos (Detects patterns over time).
        """
        # Simple heuristic: count occurrences of identical changes
        counts = {}
        for entry in self.history:
            if entry['type'] == 'manual_schedule_change':
                # Create a signature key
                key = f"{entry['details'].get('teacher_id')}-{entry['details'].get('day')}-{entry['details'].get('hour')}"
                counts[key] = counts.get(key, 0) + 1
        
        self.patterns = []
        for key, count in counts.items():
            if count >= 3: # If happened 3+ times, it's a pattern
                self.patterns.append({
                    "pattern": key,
                    "confidence": "HIGH",
                    "description": f"Repeated change detected: {key} (occurred {count} times). Suggesting rule update."
                })

    def get_learned_patterns(self) -> List[Dict]:
        """
        Returns patterns the AI has learned from human behavior.
        """
        if not self.patterns:
            self._analyze_patterns()
        return self.patterns

    def check_coherence(self, proposed_decision: Dict) -> Dict:
        """
        Point 32: IA que mide coherencia institucional.
        Checks if a proposal contradicts historical DNA.
        """
        score = 100
        warnings = []
        
        # Mock logic: Check if similar decision was rejected in the past
        # In full version, this would use vector similarity
        
        return {
            "coherence_score": score,
            "warnings": warnings,
            "aligned_with_dna": score > 80
        }
