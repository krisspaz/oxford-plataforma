"""
Machine Learning-based Intent Classifier
==========================================
Real ML classifier using scikit-learn for intent classification
and TF-IDF vectorization for text features.
"""

import os
import pickle
import logging
from typing import Dict, List, Tuple, Optional, Any
from pathlib import Path

import numpy as np

# ML imports
try:
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.naive_bayes import MultinomialNB
    from sklearn.linear_model import LogisticRegression
    from sklearn.pipeline import Pipeline
    from sklearn.model_selection import train_test_split
    from sklearn.metrics import classification_report
    ML_AVAILABLE = True
except ImportError:
    ML_AVAILABLE = False
    logging.warning("scikit-learn not available, using fallback classifier")


# Training data for intent classification
TRAINING_DATA = {
    'generate_schedule': [
        "genera horario para primero primaria",
        "genera un horario",
        "crea horario de tercero básico",
        "hazme un horario para kinder",
        "necesito horario para primero primaria",
        "arma el horario de segundo",
        "construye horario 5to primaria",
        "quiero un horario para preparatoria",
        "dame horario de 2do basico",
        "genera el horario semanal",
        "crear horario automatico",
        "hacer horario nuevo",
        "generar horario completo",
        "necesito crear un horario",
        "elabora horario para el grado",
    ],
    'greeting': [
        "hola",
        "buenos dias",
        "buenas tardes",
        "buenas noches",
        "hey",
        "saludos",
        "hola que tal",
        "buenas",
        "hi",
        "hello",
    ],
    'help': [
        "ayuda",
        "que puedo hacer",
        "como funciona",
        "comandos disponibles",
        "opciones",
        "instrucciones",
        "que comandos hay",
        "necesito ayuda",
        "como uso esto",
        "help",
    ],
    'add_constraint': [
        "el profesor garcia no puede los lunes",
        "prof lopez no puede miercoles",
        "la maestra no esta disponible los viernes",
        "profesor martinez solo puede en la mañana",
        "agregar restriccion",
        "el docente no puede",
        "no disponible los jueves",
        "solo puede en la tarde",
        "restriccion de horario",
        "profesor no disponible",
    ],
    'set_time': [
        "el horario es de 7:30 a 14:00",
        "clases de 8 a 2 de la tarde",
        "las clases empiezan a las 7",
        "que las clases terminen a las 13:00",
        "horario de 7 a 1",
        "inicio a las 8",
        "terminar a las 2",
        "de 7:30 a 13:30",
        "hora de entrada 7:00",
        "hora de salida 14:00",
    ],
    'set_duration': [
        "clases de 45 minutos",
        "cada clase de 1 hora",
        "periodos de 50 min",
        "duracion de 40 minutos",
        "que duren 45 minutos",
        "clases de una hora",
        "periodos de 60 minutos",
        "cada periodo de 45",
        "tiempo de clase 50 minutos",
    ],
    'add_recess': [
        "agrega receso",
        "pon recreo",
        "con descanso",
        "incluir receso",
        "agregar recreo despues de la cuarta hora",
        "con recreo de 30 minutos",
        "incluir descanso",
        "poner receso",
    ],
    'remove_recess': [
        "quita el receso",
        "sin recreo",
        "elimina el descanso",
        "no receso",
        "sin descanso",
        "quitar recreo",
        "eliminar receso",
    ],
    'show_status': [
        "mostrar configuracion",
        "ver estado",
        "que tengo configurado",
        "mostrar restricciones",
        "ver configuracion actual",
        "estado actual",
        "mostrar ajustes",
        "ver ajustes",
    ],
    'clear': [
        "limpiar configuracion",
        "borrar todo",
        "reiniciar",
        "reset",
        "eliminar restricciones",
        "empezar de nuevo",
        "comenzar de cero",
        "limpiar restricciones",
    ],
}


class MLIntentClassifier:
    """
    Machine Learning-based intent classifier using TF-IDF + Logistic Regression
    """
    
    MODEL_PATH = Path(__file__).parent / "models" / "intent_classifier.pkl"
    
    def __init__(self):
        self.logger = logging.getLogger(__name__)
        self.pipeline = None
        self.label_encoder = {}
        self.label_decoder = {}
        self._is_trained = False
        
        # Try to load existing model
        if self.MODEL_PATH.exists():
            self._load_model()
        else:
            # Train on first use
            self._train()
    
    def _train(self):
        """Train the classifier on training data"""
        if not ML_AVAILABLE:
            self.logger.warning("ML not available, using fallback")
            return
        
        # Prepare training data
        texts = []
        labels = []
        
        for intent, examples in TRAINING_DATA.items():
            for text in examples:
                texts.append(text.lower())
                labels.append(intent)
        
        # Create label encoder
        unique_labels = list(set(labels))
        self.label_encoder = {label: i for i, label in enumerate(unique_labels)}
        self.label_decoder = {i: label for label, i in self.label_encoder.items()}
        
        # Encode labels
        y = np.array([self.label_encoder[label] for label in labels])
        
        # Create pipeline with TF-IDF + Logistic Regression
        self.pipeline = Pipeline([
            ('tfidf', TfidfVectorizer(
                ngram_range=(1, 2),
                max_features=1000,
                min_df=1,
                sublinear_tf=True
            )),
            ('clf', LogisticRegression(
                max_iter=1000,
                C=10,
                class_weight='balanced',
                random_state=42
            ))
        ])
        
        # Train
        self.pipeline.fit(texts, y)
        self._is_trained = True
        
        # Save model
        self._save_model()
        
        self.logger.info(f"Classifier trained with {len(texts)} examples")
    
    def _save_model(self):
        """Save trained model to disk"""
        try:
            self.MODEL_PATH.parent.mkdir(parents=True, exist_ok=True)
            with open(self.MODEL_PATH, 'wb') as f:
                pickle.dump({
                    'pipeline': self.pipeline,
                    'label_encoder': self.label_encoder,
                    'label_decoder': self.label_decoder,
                }, f)
            self.logger.info(f"Model saved to {self.MODEL_PATH}")
        except Exception as e:
            self.logger.error(f"Failed to save model: {e}")
    
    def _load_model(self):
        """Load trained model from disk"""
        try:
            with open(self.MODEL_PATH, 'rb') as f:
                data = pickle.load(f)
                self.pipeline = data['pipeline']
                self.label_encoder = data['label_encoder']
                self.label_decoder = data['label_decoder']
                self._is_trained = True
            self.logger.info("Model loaded from disk")
        except Exception as e:
            self.logger.error(f"Failed to load model: {e}")
            self._train()
    
    def predict(self, text: str) -> Tuple[str, float]:
        """
        Predict intent from text
        
        Returns:
            Tuple of (intent_name, confidence)
        """
        if not self._is_trained or not ML_AVAILABLE:
            return self._fallback_predict(text)
        
        text = text.lower().strip()
        
        # Get prediction and probability
        prediction = self.pipeline.predict([text])[0]
        probabilities = self.pipeline.predict_proba([text])[0]
        
        intent = self.label_decoder[prediction]
        confidence = float(probabilities.max())
        
        return intent, confidence
    
    def predict_all(self, text: str) -> List[Dict[str, Any]]:
        """
        Get all intents with their probabilities
        """
        if not self._is_trained or not ML_AVAILABLE:
            intent, conf = self._fallback_predict(text)
            return [{'intent': intent, 'confidence': conf}]
        
        text = text.lower().strip()
        probabilities = self.pipeline.predict_proba([text])[0]
        
        results = []
        for idx, prob in enumerate(probabilities):
            results.append({
                'intent': self.label_decoder[idx],
                'confidence': float(prob)
            })
        
        return sorted(results, key=lambda x: x['confidence'], reverse=True)
    
    def _fallback_predict(self, text: str) -> Tuple[str, float]:
        """Simple keyword-based fallback when ML is not available"""
        text = text.lower()
        
        keywords = {
            'generate_schedule': ['genera', 'horario', 'crear', 'crea'],
            'greeting': ['hola', 'buenos', 'saludos', 'hey'],
            'help': ['ayuda', 'help', 'comando', 'opcion'],
            'add_constraint': ['profesor', 'no puede', 'restriccion'],
            'set_time': ['7:', '8:', 'hora', 'empieza', 'termina'],
            'set_duration': ['minutos', 'duracion', 'hora'],
            'add_recess': ['receso', 'recreo', 'descanso', 'agrega'],
            'remove_recess': ['quita', 'sin'],
            'show_status': ['mostrar', 'ver', 'configuracion'],
            'clear': ['limpiar', 'borrar', 'reiniciar', 'reset'],
        }
        
        for intent, kws in keywords.items():
            if any(kw in text for kw in kws):
                return intent, 0.6
        
        return 'unknown', 0.0
    
    def retrain(self, additional_data: Dict[str, List[str]] = None):
        """Retrain the model with additional data"""
        if additional_data:
            for intent, examples in additional_data.items():
                if intent in TRAINING_DATA:
                    TRAINING_DATA[intent].extend(examples)
                else:
                    TRAINING_DATA[intent] = examples
        
        self._train()


class StudentRiskPredictor:
    """
    ML-based predictor for student academic risk
    Uses simple logistic regression on grade history
    """
    
    def __init__(self):
        self.model = None
        self.is_trained = False
        
        if ML_AVAILABLE:
            self._train_dummy_model()
    
    def _train_dummy_model(self):
        """Train a simple model with synthetic data"""
        # Generate synthetic training data
        np.random.seed(42)
        n_samples = 500
        
        # Features: [avg_grade, grade_trend, attendance_rate, assignments_completed]
        X = np.random.randn(n_samples, 4)
        X[:, 0] = np.random.uniform(40, 100, n_samples)  # avg grade
        X[:, 1] = np.random.uniform(-20, 20, n_samples)  # trend
        X[:, 2] = np.random.uniform(0.5, 1.0, n_samples)  # attendance
        X[:, 3] = np.random.uniform(0.3, 1.0, n_samples)  # assignments
        
        # Labels: at_risk if low grades, negative trend, low attendance
        y = ((X[:, 0] < 65) | (X[:, 1] < -10) | (X[:, 2] < 0.7)).astype(int)
        
        self.model = LogisticRegression(random_state=42)
        self.model.fit(X, y)
        self.is_trained = True
    
    def predict_risk(self, grades: List[float], attendance_rate: float = 0.9, 
                     assignments_completed: float = 0.8) -> Dict[str, Any]:
        """
        Predict if a student is at academic risk
        
        Args:
            grades: List of recent grades (0-100)
            attendance_rate: Attendance rate (0-1)
            assignments_completed: Completion rate (0-1)
            
        Returns:
            Dict with risk assessment
        """
        if not grades:
            return {
                'at_risk': False,
                'risk_score': 0.0,
                'confidence': 0.0,
                'factors': [],
            }
        
        avg_grade = np.mean(grades)
        grade_trend = grades[-1] - grades[0] if len(grades) > 1 else 0
        
        if not ML_AVAILABLE or not self.is_trained:
            # Simple rule-based fallback
            risk_score = 0.0
            factors = []
            
            if avg_grade < 65:
                risk_score += 0.4
                factors.append('Promedio bajo')
            if grade_trend < -10:
                risk_score += 0.3
                factors.append('Tendencia negativa')
            if attendance_rate < 0.7:
                risk_score += 0.2
                factors.append('Baja asistencia')
            if assignments_completed < 0.5:
                risk_score += 0.1
                factors.append('Tareas incompletas')
            
            return {
                'at_risk': risk_score > 0.4,
                'risk_score': min(risk_score, 1.0),
                'confidence': 0.6,
                'factors': factors,
            }
        
        # ML prediction
        features = np.array([[avg_grade, grade_trend, attendance_rate, assignments_completed]])
        prediction = self.model.predict(features)[0]
        probability = self.model.predict_proba(features)[0]
        
        risk_score = float(probability[1])  # Probability of being at risk
        
        factors = []
        if avg_grade < 65:
            factors.append('Promedio bajo')
        if grade_trend < -10:
            factors.append('Tendencia negativa')
        if attendance_rate < 0.7:
            factors.append('Baja asistencia')
        if assignments_completed < 0.5:
            factors.append('Tareas incompletas')
        
        return {
            'at_risk': bool(prediction),
            'risk_score': risk_score,
            'confidence': float(max(probability)),
            'factors': factors,
            'average_grade': avg_grade,
            'trend': grade_trend,
        }


# Global instances
intent_classifier = MLIntentClassifier()
risk_predictor = StudentRiskPredictor()
