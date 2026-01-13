"""
Services Container
==================
Centralizes the initialization of all AI and business logic services.
"""

from database import db
from nlp_engine import nlp_engine
from preference_learner import PreferenceLearner
from risk_analyzer import RiskAnalyzer
from teacher_analyzer import TeacherAnalyzer
from schedule_scorer import ScheduleScorer
from institutional_memory import InstitutionalMemory
from rule_contract import RuleContract
from assistant_factory import AssistantFactory
from negotiation_engine import NegotiationEngine
from decision_logger import DecisionLogger
from legal_defense import LegalDefenseGen
from ethics_validator import EthicsValidator
from simulation_engine import SimulationEngine
from localization_adapter import LocalizationAdapter

# Initialize Singletons
learner = PreferenceLearner()
risk_analyzer = RiskAnalyzer()
teacher_analyzer = TeacherAnalyzer()
schedule_scorer = ScheduleScorer()
memory = InstitutionalMemory()
rule_contract = RuleContract()
assistant_factory = AssistantFactory()
negotiation_engine = NegotiationEngine()
decision_logger = DecisionLogger()
legal_defense = LegalDefenseGen()
ethics_validator = EthicsValidator()
simulation_engine = SimulationEngine()
localization_adapter = LocalizationAdapter()

# Enterprise AI modules (Optional load)
try:
    from learning_engine import learning_engine
    from expert_engine import expert_engine
    # from memory import memory # overlap with institutional_memory? keeping both if distinct
    from knowledge_base import knowledge_base
    from rule_engine import rule_engine
    ENTERPRISE_AI_ENABLED = True
except ImportError as e:
    print(f"Enterprise AI modules not fully loaded: {e}")
    ENTERPRISE_AI_ENABLED = False
