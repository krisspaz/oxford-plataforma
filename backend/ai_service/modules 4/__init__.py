# AI Service Module Organization
# ===============================
# Exports all modules in organized structure

from .core import (
    config,
    database,
    cache,
    models,
    schemas,
)

from .engines import (
    nlp_engine,
    learning_engine,
    expert_engine,
    rule_engine,
    schedule_generator,
    genetic_scheduler,
    ml_classifier,
    response_generator,
    simulation_engine,
    negotiation_engine,
)

from .analyzers import (
    risk_analyzer,
    teacher_analyzer,
    cognitive_profiler,
    conflict_detector,
    economic_impact,
)

from .assistants import (
    assistant_factory,
    context_manager,
    knowledge_base,
    memory,
    institutional_memory,
    preference_learner,
    institutional_legacy,
)

from .security import (
    jwt_auth,
    banking_security,
    ai_governance,
    ethics_validator,
    legal_defense,
)

from .scheduling import (
    schedule_scorer,
    pedagogical_criteria,
    auto_correction,
)

from .utils import (
    decision_logger,
    crisis_mode,
    localization_adapter,
    rule_contract,
    services_container,
)

__all__ = [
    # Core
    'config', 'database', 'cache', 'models', 'schemas',
    # Engines
    'nlp_engine', 'learning_engine', 'expert_engine', 'rule_engine',
    'schedule_generator', 'genetic_scheduler', 'ml_classifier',
    'response_generator', 'simulation_engine', 'negotiation_engine',
    # Analyzers
    'risk_analyzer', 'teacher_analyzer', 'cognitive_profiler',
    'conflict_detector', 'economic_impact',
    # Assistants
    'assistant_factory', 'context_manager', 'knowledge_base',
    'memory', 'institutional_memory', 'preference_learner', 'institutional_legacy',
    # Security
    'jwt_auth', 'banking_security', 'ai_governance', 'ethics_validator', 'legal_defense',
    # Scheduling
    'schedule_scorer', 'pedagogical_criteria', 'auto_correction',
    # Utils
    'decision_logger', 'crisis_mode', 'localization_adapter', 
    'rule_contract', 'services_container',
]
