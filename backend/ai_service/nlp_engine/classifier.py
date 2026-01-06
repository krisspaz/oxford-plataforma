class IntentClassifier:
    def classify(self, text):
        text = text.lower().strip()
        
        # Grading Intents
        if any(word in text for word in ["nota", "calificación", "promedio", "puntos", "score"]):
            return "check_grades"
            
        # Schedule Intents
        if any(word in text for word in ["horario", "clase", "toca", "mañana", "lunes", "martes", "miércoles", "jueves", "viernes"]):
            return "check_schedule"
            
        # Finance Intents
        if any(word in text for word in ["pago", "mensualidad", "debo", "saldo", "factura", "recibo"]):
            return "check_finance"
            
        # Administrative Intents
        if any(word in text for word in ["constancia", "certificado", "inscripción", "papeles"]):
            return "request_document"
            
        # Login/Auth Intents
        if any(word in text for word in ["entrar", "login", "contraseña", "clave", "acceso"]):
            return "auth_help"

        return "unknown"
