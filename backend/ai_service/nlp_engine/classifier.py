class IntentClassifier:
    def classify(self, text):
        # Stub for intent classification
        if "nota" in text.lower():
            return "check_grades"
        if "horario" in text.lower():
            return "check_schedule"
        return "unknown"
