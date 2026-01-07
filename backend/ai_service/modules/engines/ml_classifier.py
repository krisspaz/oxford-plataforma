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


# Training data for intent classification - ENTERPRISE VERSION (500+ examples)
# Includes: Guatemalan colloquialisms, common typos, formal/informal variations
TRAINING_DATA = {
    # === GREETINGS & CONVERSATIONAL (50+ examples) ===
    'greeting': [
        # Formal
        "hola", "buenos dias", "buenas tardes", "buenas noches", "saludos cordiales",
        "muy buenos dias", "muy buenas tardes", "estimado", "buen dia",
        # Informal Guatemala
        "que onda", "que tal", "que hay", "que pasa", "que hubo", "qiubo",
        "hola que tal", "hola como estas", "hey", "hi", "hello", "buenas",
        "que onda vos", "hola mano", "hola profe", "hola maestro", "hola seño",
        # Typos comunes
        "ola", "oal", "hoal", "hol", "buens dias", "benos dias", "vuenas tardes",
        "beunos dias", "buenso dias", "holaa", "holaaaa", "qe onda", "ke onda",
        # Emojis/expresiones
        "hola!", "hola!!", "buenos días ☀️", "buenas 👋", "hey!", "hi there",
        # Variations
        "feliz dia", "lindo dia", "bonito dia", "excelente dia",
    ],
    'how_are_you': [
        "como estas", "como te encuentras", "que tal estas", "como te va",
        "todo bien", "estas bien", "como andas", "que tal te va", "que tal",
        "como vas", "como te sientes", "que ondas", "todo en orden",
        # Guatemala informal
        "que onda como andas", "como te trata la vida", "todo tranqui",
        # Typos
        "como estas?", "cmo estas", "como sta", "ke tal", "q tal",
    ],
    'thanks': [
        "gracias", "muchas gracias", "te lo agradezco", "thanks", "mil gracias",
        "muy amable", "perfecto gracias", "excelente gracias", "genial gracias",
        "super gracias", "gracias totales", "thank you", "de verdad gracias",
        "te agradezco mucho", "grax", "grs", "thnx", "tenkiu", "10kiu",
        "muy agradecido", "agradecida", "se agradece", "gracias por todo",
    ],
    'goodbye': [
        "adios", "hasta luego", "nos vemos", "bye", "chao", "hasta mañana",
        "que te vaya bien", "hasta pronto", "me despido", "bye bye", "chau",
        "nos vidrios", "chaito", "adiosito", "cuidense", "cuidate", "suerte",
        "que les vaya bien", "buen fin de semana", "buenas noches",
        # Typos
        "adio", "asta luego", "chaooo", "byeee", "hasat luego",
    ],
    
    # === HELP & ABOUT (40+ examples) ===
    'help': [
        "ayuda", "que puedo hacer", "como funciona", "comandos disponibles",
        "opciones", "instrucciones", "que comandos hay", "necesito ayuda",
        "como uso esto", "help", "que sabes hacer", "para que sirves",
        "cuales son tus funciones", "que puedes hacer por mi", "menu",
        "dame opciones", "que haces", "funcionalidades", "caracteristicas",
        "explicame", "tutorial", "guia", "manual", "info", "informacion",
        # Questions
        "como te uso", "que cosas puedo preguntarte", "que me puedes ayudar",
        # Typos
        "ayuad", "aiuda", "haelp", "ayda", "comados", "opcoines",
    ],
    'about_ai': [
        "quien eres", "que eres", "como te llamas", "eres una ia",
        "eres un robot", "eres inteligencia artificial", "quien te creo",
        "de donde vienes", "cuantos años tienes", "eres real", "eres humano",
        "que tipo de ia eres", "nombre", "tu nombre", "como es tu nombre",
        "eres chatgpt", "eres openai", "quien te hizo", "equipo creador",
        "version", "que version eres", "sobre ti", "presentate",
    ],
    
    # === SCHEDULE GENERATION (45+ examples) ===
    'generate_schedule': [
        "genera horario para primero primaria", "genera un horario",
        "crea horario de tercero basico", "hazme un horario para kinder",
        "necesito horario para primero primaria", "arma el horario de segundo",
        "construye horario 5to primaria", "quiero un horario para preparatoria",
        "dame horario de 2do basico", "genera el horario semanal",
        "crear horario automatico", "hacer horario nuevo", "nuevo horario",
        "generar horario completo", "necesito crear un horario",
        "elabora horario para el grado", "optimiza los horarios",
        # Variations by grade
        "horario para prekinder", "horario de sexto", "horario bachillerato",
        "horario primaria", "horario secundaria", "horario para el grado",
        # Actions
        "optimizar horario", "reorganizar horario", "recalcular horarios",
        "distribuir clases", "asignar periodos", "planificar semana",
        # Typos
        "genra horario", "genera horairo", "crera horario", "haz horario",
        "ocrario nuevo", "genera orario", "hr nuevo",
    ],
    'view_schedule': [
        "ver horario", "mostrar horario", "mi horario", "horario actual",
        "ver mi horario", "cual es mi horario", "horario de hoy",
        "horario de la semana", "que clases tengo hoy", "mis clases hoy",
        "que tengo mañana", "horario del lunes", "horario del martes",
        "ver calendario", "agenda de clases", "proximas clases",
        # Typos
        "ver orario", "mi orairo", "horari de hoy", "q clases tengo",
    ],
    
    # === CONSTRAINTS & CONFIG (35+ examples) ===
    'add_constraint': [
        "el profesor garcia no puede los lunes", "prof lopez no puede miercoles",
        "la maestra no esta disponible los viernes", "agregar restriccion",
        "el docente no puede", "no disponible los jueves",
        "solo puede en la tarde", "restriccion de horario",
        "profesor martinez solo manana", "no asignar los viernes",
        "evitar primera hora", "bloquear ultimo periodo",
        "no puede antes de las 10", "solo despues del almuerzo",
        "restriccion profesor", "añadir restriccion", "nueva restriccion",
    ],
    'set_time': [
        "el horario es de 7:30 a 14:00", "clases de 8 a 2 de la tarde",
        "las clases empiezan a las 7", "que las clases terminen a las 13:00",
        "horario de 7 a 1", "inicio a las 8", "terminar a las 2",
        "entrada 7:30", "salida 14:00", "de 7:00 a 13:00", "7am a 2pm",
        "hora de entrada", "hora de salida", "jornada de 7 a 2",
    ],
    'set_duration': [
        "clases de 45 minutos", "cada clase de 1 hora", "periodos de 50 min",
        "duracion de 40 minutos", "que duren 45 minutos", "periodos 35 min",
        "cada periodo 50 minutos", "clase de 60 min", "45 min por clase",
    ],
    'add_recess': [
        "agrega receso", "pon recreo", "con descanso", "incluir receso",
        "agregar recreo despues de la cuarta hora", "recreo de 30 min",
        "descanso a las 10", "receso de 20 minutos", "break",
    ],
    'remove_recess': [
        "quita el receso", "sin recreo", "elimina el descanso", "no receso",
        "sin descanso", "eliminar break", "quitar recreo del horario",
    ],
    'show_status': [
        "mostrar configuracion", "ver estado", "que tengo configurado",
        "mostrar restricciones", "ver configuracion actual", "config actual",
        "ver ajustes", "revisar configuracion", "estado del sistema",
    ],
    'clear': [
        "limpiar configuracion", "borrar todo", "reiniciar", "reset",
        "eliminar restricciones", "empezar de nuevo", "desde cero",
        "limpiar todo", "resetear", "borrar configuracion",
    ],
    
    # === ACADEMIC - TEACHER (50+ examples) ===
    'my_subjects': [
        "mis materias", "que materias tengo", "cuales son mis asignaturas",
        "que cursos doy", "mis clases", "que enseno", "materias asignadas",
        "ver mis materias", "lista de materias", "que grados tengo",
        "mis cursos", "que imparto", "asignaturas a mi cargo",
        "ver mis asignaturas", "cuantas materias tengo", "mis grupos",
        "que secciones tengo", "grados asignados", "mis grados",
        # Typos
        "mis mterias", "que mterias tengo", "mis clses", "mis materas",
    ],
    'my_students': [
        "mis estudiantes", "mis alumnos", "cuantos estudiantes tengo",
        "lista de alumnos", "ver mis estudiantes", "quienes son mis alumnos",
        "listado de estudiantes", "estudiantes del grupo", "mis patojos",
        "los del salon", "integrantes del curso", "cantidad de alumnos",
        "ver alumnos", "nombres de estudiantes", "roster de clase",
    ],
    'student_grades': [
        "notas de estudiantes", "calificaciones del grupo", "promedio del grupo",
        "ver notas", "revisar calificaciones", "como van las notas",
        "estudiantes reprobando", "quienes van mal", "quienes van bien",
        "notas parciales", "notas del bimestre", "reporte de notas",
        "quienes tienen bajas notas", "alumnos con problemas",
    ],
    'load_grades': [
        "cargar notas", "subir calificaciones", "registrar notas",
        "ingresar calificaciones", "poner notas", "agregar notas",
        "ingresar notas", "registrar calificaciones", "subir notas",
        "cargar calificaciones", "editar notas", "modificar notas",
    ],
    
    # === ACADEMIC - STUDENT (45+ examples) ===
    'check_homework': [
        "tareas pendientes", "que tareas tengo", "deberes para manana",
        "trabajos pendientes", "hay tarea", "tengo tarea",
        "que hay de tarea", "deberes de hoy", "asignaciones pendientes",
        "proximas tareas", "tareas para entregar", "entregas pendientes",
        "me dejaron tarea", "tareas del dia", "homework",
        # Guatemala
        "tareas pa entregar", "que dejaron de tarea", "hay deber",
        # Typos
        "taraes", "tares pendientes", "taereas", "deberes",
    ],
    'check_grades': [
        "mis notas", "mis calificaciones", "como voy", "mi promedio",
        "cuanto saque", "ver mis notas", "boleta de calificaciones",
        "notas del bimestre", "mi boletin", "historial de notas",
        "en que voy", "como me fue", "resultado del examen",
        "nota del parcial", "cuanto tengo", "promedio actual",
        # Typos
        "mis ntas", "mis calificaiones", "mo promedio", "mi notas",
    ],
    'check_attendance': [
        "mi asistencia", "cuantas faltas tengo", "he faltado mucho",
        "registro de asistencia", "dias faltados", "ausencias",
        "cuantos dias he faltado", "record de asistencia",
        "llegadas tarde", "retardos", "justificaciones",
    ],
    
    # === EMOTIONAL SUPPORT (40+ examples) ===
    'emotional_support': [
        "estoy estresado", "tengo ansiedad", "me siento mal",
        "no puedo mas", "estoy cansado", "necesito descansar",
        "tengo mucha presion", "me siento agotado", "estoy abrumado",
        "me duele la cabeza", "estoy quemado", "burnout",
        "no aguanto", "estoy frustrado", "me siento triste",
        "estoy preocupado", "tengo miedo", "estoy nervioso",
        "no se que hacer", "me siento perdido", "estoy bloqueado",
        # Questions seeking help
        "que hago si estoy estresado", "como manejo la ansiedad",
        "tips para el estres", "ayuda con ansiedad",
    ],
    'motivation': [
        "dame motivacion", "necesito animo", "motivame",
        "estoy desmotivado", "no tengo ganas", "me falta motivacion",
        "echame porras", "dame animos", "necesito inspiracion",
        "porque seguir", "vale la pena", "no le veo sentido",
        "motivacion para estudiar", "animame", "levantame el animo",
    ],
    'study_tip': [
        "dame un consejo", "como estudio mejor", "tips de estudio",
        "consejos para aprender", "como memorizar", "tecnicas de estudio",
        "como concentrarme", "metodos de estudio", "habitos de estudio",
        "como prepararme para examen", "tips para parcial", "estudiar mejor",
        "como organizo mi tiempo", "productividad", "como ser mejor estudiante",
        "estrategias de aprendizaje", "aprender mas rapido",
    ],
    
    # === RISK & ANALYTICS (30+ examples) ===
    'check_risk': [
        "estudiantes en riesgo", "analizar riesgos", "ver alertas",
        "quienes estan mal", "detectar problemas", "alumnos reprobando",
        "riesgo academico", "dashboard de riesgo", "alertas academicas",
        "estudiantes con problemas", "quienes necesitan ayuda",
        "identificar riesgo", "monitorear estudiantes", "alerta temprana",
    ],
    'teacher_burnout': [
        "mi carga de trabajo", "estoy sobrecargado", "revision de carga",
        "analizar mi horario", "tengo muchas clases", "carga docente",
        "cuantas horas trabajo", "mi horario esta pesado",
        "necesito menos clases", "redistribuir carga",
    ],
    'institutional_health': [
        "salud institucional", "como va el colegio", "indice isa",
        "estado general", "metricas del colegio", "kpis educativos",
        "indicadores", "rendimiento institucional", "estadisticas",
    ],
    
    # === ADMINISTRATIVE (25+ examples) ===
    'contact_parent': [
        "contactar padres", "llamar a papa", "enviar mensaje a padres",
        "notificar a familia", "comunicado a padres", "avisar encargados",
        "mensaje a papas", "email a padres", "citar a padres",
    ],
    'report_issue': [
        "reportar problema", "tengo un problema", "algo no funciona",
        "error en el sistema", "necesito soporte", "fallo",
        "bug", "no carga", "esta malo", "no jala", "no sirve",
        "problema tecnico", "ayuda tecnica", "reportar bug",
    ],
    'suggestion': [
        "tengo una sugerencia", "quiero sugerir algo", "propongo",
        "idea de mejora", "feedback", "comentario", "opinion",
        "mejoras", "recomendacion", "podrian agregar",
    ],
    
    # === FUN & MISC (35+ examples) ===
    'joke': [
        "cuentame un chiste", "dime algo gracioso", "hazme reir",
        "tienes chistes", "algo divertido", "contame un chiste",
        "un chiste", "chiste malo", "chiste bueno", "cuentame uno",
        "otro chiste", "mas chistes", "joke", "funny",
    ],
    'weather': [
        "como esta el clima", "va a llover", "que tiempo hace",
        "clima de hoy", "pronostico", "hace calor", "hace frio",
        "temperatura", "clima manana", "lleva sombrilla",
    ],
    'time': [
        "que hora es", "hora actual", "que dia es hoy", "fecha de hoy",
        "que fecha es", "en que dia estamos", "hora", "la hora",
        "dame la hora", "que hora tienes", "what time",
    ],
    'random_fact': [
        "dato curioso", "sabias que", "dime algo interesante",
        "cuentame algo", "fun fact", "curiosidad", "dato random",
        "algo interesante", "informacion curiosa", "sabias",
    ],
    
    # === QUIZ & EVALUATION (20+ examples) ===
    'generate_quiz': [
        "hazme un quiz", "examen de practica", "preguntame",
        "quiz de matematicas", "test de ciencias", "evaluame",
        "dame preguntas", "practica examen", "simulacro",
        "pon a prueba", "trivia", "preguntas de repaso",
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
