from fastapi import APIRouter, HTTPException
from pydantic import BaseModel
from typing import Dict, Any, List, Optional
from services_container import (
    negotiation_engine, risk_analyzer, teacher_analyzer, schedule_scorer, 
    memory, rule_contract, decision_logger, legal_defense, ethics_validator,
    simulation_engine, localization_adapter
)

analytics_router = APIRouter()

class NegotiateRequest(BaseModel):
    conflict: Dict[str, Any]

class RiskRequest(BaseModel):
    student_id: Optional[int] = None
    scores: Optional[List[int]] = None
    attendance: Optional[float] = None

class BurnoutRequest(BaseModel):
    teacher_id: int
    schedule: List[Any]

class LegalDefenseRequest(BaseModel):
    id: int
    context: Dict[str, Any]

class ValidateEthicsRequest(BaseModel):
    action: str
    params: Dict[str, Any]

class SimulateChangeRequest(BaseModel):
    base_schedule: Dict[str, Any]
    change: Dict[str, Any]

@analytics_router.post("/negotiate")
def negotiate_change(req: NegotiateRequest):
    alternatives = negotiation_engine.propose_alternatives(req.conflict)
    return {"alternatives": alternatives}

@analytics_router.post("/predict-risk")
def predict_risk(req: Dict[str, Any]): # Accepting raw dict for flexibility for now
    try:
        analysis = risk_analyzer.analyze(req)
        return analysis
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@analytics_router.post("/analyze-burnout")
def analyze_burnout(req: BurnoutRequest):
    result = teacher_analyzer.analyze_workload(req.teacher_id, req.schedule)
    return result

@analytics_router.get("/institutional-health")
def institutional_health():
    # Mock aggregation of stats
    teacher_stats = {'avg_burnout': 15}
    student_stats = {} 
    result = schedule_scorer.calculate_isa([], teacher_stats, student_stats)
    return result

@analytics_router.get("/memory/patterns")
def get_memory_patterns():
    return memory.get_learned_patterns()

@analytics_router.get("/rules/contract")
def get_rule_contract():
    return rule_contract.get_contract()

@analytics_router.get("/audit/log")
def get_audit_log():
    return {"log": decision_logger.get_audit_trail()}

@analytics_router.post("/legal/defense")
def get_legal_defense(req: LegalDefenseRequest):
    return legal_defense.generate_defense(req.id, req.context)

@analytics_router.post("/ethics/validate")
def validate_ethics(req: ValidateEthicsRequest):
    allowed, msg = ethics_validator.validate_request(req.dict())
    return {"allowed": allowed, "message": msg}

@analytics_router.get("/simulation/future")
def simulate_future():
    return simulation_engine.simulate_future_scenario(3)

@analytics_router.get("/context/maturity")
def get_maturity():
    return localization_adapter.get_maturity_index()

@analytics_router.post("/simulate")
def simulate_change(req: SimulateChangeRequest):
    """
    Run a 'What-If' scenario.
    """
    # Mocking the simulation result for MVP speed
    return {
        "status": "simulated",
        "original_score": 85,
        "new_score": 82,
        "impact": "adding this class creates a conflict on Tuesday",
        "recommendation": "Try Wednesday instead"
    }
