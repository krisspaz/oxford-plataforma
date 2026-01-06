"""
Oxford AI Service - FastAPI Migration
Enterprise-grade with circuit breakers, Pydantic DTOs, OpenAPI
"""

from fastapi import FastAPI, HTTPException, Depends, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel, Field
from typing import Optional, List, Dict, Any
from datetime import datetime
from enum import Enum
import asyncio
import logging
import time
from functools import wraps

# ==========================================
# CONFIGURATION
# ==========================================

app = FastAPI(
    title="Oxford AI Service",
    description="Enterprise AI for scheduling, predictions, and NLP",
    version="2.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
    openapi_url="/openapi.json"
)

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:5173", "http://localhost:3000"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("ai_service")

security = HTTPBearer(auto_error=False)

# ==========================================
# PYDANTIC DTOs
# ==========================================

class CommandRequest(BaseModel):
    """Request DTO for processing commands"""
    command: str = Field(..., min_length=1, max_length=1000, description="Natural language command")
    user_id: Optional[int] = Field(None, description="User ID for context")
    role: Optional[str] = Field("user", description="User role")
    context: Optional[Dict[str, Any]] = Field(default_factory=dict, description="Additional context")

class CommandResponse(BaseModel):
    """Response DTO for command processing"""
    response: str
    intent: str
    confidence: float = Field(..., ge=0, le=1)
    actions: List[Dict[str, Any]] = []
    suggestions: List[str] = []
    processing_time_ms: float

class ScheduleRequest(BaseModel):
    """Request DTO for schedule generation"""
    teachers: List[Dict[str, Any]]
    subjects: List[Dict[str, Any]]
    rooms: List[Dict[str, Any]]
    constraints: Optional[Dict[str, Any]] = None

class ScheduleResponse(BaseModel):
    """Response DTO for schedule generation"""
    schedule: List[Dict[str, Any]]
    conflicts: List[str] = []
    score: float
    warnings: List[str] = []

class FeedbackRequest(BaseModel):
    """Request DTO for learning feedback"""
    query: str
    response: str
    feedback: str = Field(..., pattern="^(positive|negative|neutral)$")
    user_id: Optional[int] = None

class HealthResponse(BaseModel):
    """Response DTO for health check"""
    status: str
    version: str
    uptime: float
    circuit_breaker: str

class RiskAnalysisRequest(BaseModel):
    """Request DTO for risk analysis"""
    student_id: int
    include_predictions: bool = True

class RiskAnalysisResponse(BaseModel):
    """Response DTO for risk analysis"""
    student_id: int
    risk_score: float
    risk_level: str
    factors: List[Dict[str, Any]]
    recommendations: List[str]

# ==========================================
# CIRCUIT BREAKER
# ==========================================

class CircuitState(Enum):
    CLOSED = "closed"
    OPEN = "open"
    HALF_OPEN = "half_open"

class CircuitBreaker:
    """Circuit breaker pattern implementation"""
    
    def __init__(
        self,
        failure_threshold: int = 5,
        recovery_timeout: float = 30.0,
        half_open_max_calls: int = 3
    ):
        self.failure_threshold = failure_threshold
        self.recovery_timeout = recovery_timeout
        self.half_open_max_calls = half_open_max_calls
        
        self.state = CircuitState.CLOSED
        self.failure_count = 0
        self.last_failure_time = 0
        self.half_open_calls = 0
    
    @property
    def is_closed(self) -> bool:
        return self.state == CircuitState.CLOSED
    
    @property
    def is_open(self) -> bool:
        if self.state == CircuitState.OPEN:
            # Check if recovery timeout has passed
            if time.time() - self.last_failure_time > self.recovery_timeout:
                self.state = CircuitState.HALF_OPEN
                self.half_open_calls = 0
                return False
            return True
        return False
    
    def record_success(self):
        """Record successful call"""
        if self.state == CircuitState.HALF_OPEN:
            self.half_open_calls += 1
            if self.half_open_calls >= self.half_open_max_calls:
                self.state = CircuitState.CLOSED
                self.failure_count = 0
                logger.info("Circuit breaker CLOSED")
        else:
            self.failure_count = 0
    
    def record_failure(self):
        """Record failed call"""
        self.failure_count += 1
        self.last_failure_time = time.time()
        
        if self.failure_count >= self.failure_threshold:
            self.state = CircuitState.OPEN
            logger.warning(f"Circuit breaker OPEN after {self.failure_count} failures")

# Global circuit breaker
circuit_breaker = CircuitBreaker()

def with_circuit_breaker(fallback_response: Any = None):
    """Decorator for circuit breaker pattern"""
    def decorator(func):
        @wraps(func)
        async def wrapper(*args, **kwargs):
            if circuit_breaker.is_open:
                logger.warning("Circuit breaker is OPEN, returning fallback")
                if fallback_response is not None:
                    return fallback_response
                raise HTTPException(
                    status_code=503,
                    detail="Service temporarily unavailable"
                )
            
            try:
                result = await func(*args, **kwargs)
                circuit_breaker.record_success()
                return result
            except Exception as e:
                circuit_breaker.record_failure()
                logger.error(f"Circuit breaker recorded failure: {e}")
                raise
        
        return wrapper
    return decorator

# ==========================================
# SERVICE STARTUP
# ==========================================

START_TIME = time.time()

# Import AI modules
try:
    from expert_engine import ExpertEngine
    from nlp_engine import NLPEngine
    from memory import InstitutionalMemory
    from knowledge_base import KnowledgeBase
    from rule_engine import RuleEngine
    from schedule_generator import ScheduleGenerator
    from schedule_scorer import ScheduleScorer
    from learning_engine import LearningEngine
    
    expert = ExpertEngine()
    nlp = NLPEngine()
    memory = InstitutionalMemory()
    knowledge = KnowledgeBase()
    rules = RuleEngine()
    scheduler = ScheduleGenerator()
    scorer = ScheduleScorer()
    learning = LearningEngine()
    
    MODULES_LOADED = True
except ImportError as e:
    logger.warning(f"Some AI modules not loaded: {e}")
    MODULES_LOADED = False

# ==========================================
# API ENDPOINTS
# ==========================================

@app.get("/health", response_model=HealthResponse, tags=["System"])
async def health_check():
    """Health check endpoint"""
    return HealthResponse(
        status="healthy" if MODULES_LOADED else "degraded",
        version="2.0.0",
        uptime=time.time() - START_TIME,
        circuit_breaker=circuit_breaker.state.value
    )

@app.post("/process-command", response_model=CommandResponse, tags=["AI"])
@with_circuit_breaker(fallback_response=None)
async def process_command(request: CommandRequest):
    """Process natural language command"""
    start_time = time.time()
    
    try:
        if MODULES_LOADED:
            # Process with NLP
            intent = nlp.classify_intent(request.command)
            entities = nlp.extract_entities(request.command)
            
            # Get expert response
            response = expert.process(
                command=request.command,
                intent=intent,
                entities=entities,
                role=request.role,
                context=request.context
            )
            
            # Record for learning
            memory.record_interaction(
                user_id=request.user_id,
                command=request.command,
                response=response.get("text", ""),
                intent=intent
            )
            
            return CommandResponse(
                response=response.get("text", "Procesado."),
                intent=intent.get("name", "unknown"),
                confidence=intent.get("confidence", 0.5),
                actions=response.get("actions", []),
                suggestions=response.get("suggestions", []),
                processing_time_ms=(time.time() - start_time) * 1000
            )
        else:
            # Fallback response
            return CommandResponse(
                response="El servicio de IA está en modo degradado. Por favor intenta más tarde.",
                intent="fallback",
                confidence=0.0,
                actions=[],
                suggestions=[],
                processing_time_ms=(time.time() - start_time) * 1000
            )
    
    except Exception as e:
        logger.error(f"Error processing command: {e}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/generate-schedule", response_model=ScheduleResponse, tags=["Scheduling"])
@with_circuit_breaker()
async def generate_schedule(request: ScheduleRequest):
    """Generate optimized schedule"""
    try:
        if not MODULES_LOADED:
            raise HTTPException(status_code=503, detail="Scheduler not available")
        
        schedule = scheduler.generate(
            teachers=request.teachers,
            subjects=request.subjects,
            rooms=request.rooms,
            constraints=request.constraints
        )
        
        score = scorer.evaluate(schedule)
        conflicts = scheduler.detect_conflicts(schedule)
        
        return ScheduleResponse(
            schedule=schedule,
            conflicts=conflicts,
            score=score,
            warnings=[]
        )
    
    except Exception as e:
        logger.error(f"Error generating schedule: {e}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/learn", tags=["Learning"])
async def learn_from_feedback(
    request: FeedbackRequest,
    background_tasks: BackgroundTasks
):
    """Process feedback for continuous learning"""
    try:
        # Process in background to not block response
        background_tasks.add_task(
            process_feedback_async,
            request.query,
            request.response,
            request.feedback,
            request.user_id
        )
        
        return {"status": "feedback_received", "message": "Gracias por tu feedback"}
    
    except Exception as e:
        logger.error(f"Error processing feedback: {e}")
        raise HTTPException(status_code=500, detail=str(e))

async def process_feedback_async(query: str, response: str, feedback: str, user_id: int):
    """Background task for processing feedback"""
    if MODULES_LOADED:
        learning.record_feedback(
            query=query,
            response=response,
            feedback=feedback,
            user_id=user_id
        )

@app.post("/analyze-risk", response_model=RiskAnalysisResponse, tags=["Analytics"])
@with_circuit_breaker()
async def analyze_risk(request: RiskAnalysisRequest):
    """Analyze student dropout risk"""
    try:
        # Simulated risk analysis
        risk_score = 0.35
        
        return RiskAnalysisResponse(
            student_id=request.student_id,
            risk_score=risk_score,
            risk_level="medium" if risk_score > 0.3 else "low",
            factors=[
                {"factor": "attendance", "impact": 0.15},
                {"factor": "grades", "impact": 0.10},
                {"factor": "participation", "impact": 0.10}
            ],
            recommendations=[
                "Agendar tutoría semanal",
                "Contactar a familia",
                "Monitorear asistencia"
            ]
        )
    
    except Exception as e:
        logger.error(f"Error analyzing risk: {e}")
        raise HTTPException(status_code=500, detail=str(e))

# ==========================================
# ERROR HANDLERS
# ==========================================

@app.exception_handler(Exception)
async def global_exception_handler(request, exc):
    logger.error(f"Global exception: {exc}")
    return {
        "error": "internal_error",
        "message": "An unexpected error occurred",
        "detail": str(exc) if app.debug else None
    }

# ==========================================
# RUN
# ==========================================

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "main_fastapi:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        workers=4
    )
