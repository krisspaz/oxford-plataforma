# AI Service Architecture
# =======================
# Module Organization Guide

## Structure

```
ai_service/
├── main.py                    # Flask entry point
├── config.py                  # Configuration
├── modules/
│   ├── __init__.py           # Module exports
│   ├── core/                  # Core infrastructure
│   │   ├── database.py
│   │   ├── cache.py
│   │   ├── models.py
│   │   └── schemas.py
│   ├── engines/               # AI Processing Engines
│   │   ├── nlp_engine.py      # Natural Language Processing
│   │   ├── learning_engine.py # Machine Learning
│   │   ├── expert_engine.py   # Expert System
│   │   ├── rule_engine.py     # Business Rules
│   │   ├── ml_classifier.py   # Classifications
│   │   └── response_generator.py
│   ├── analyzers/             # Data Analysis
│   │   ├── risk_analyzer.py
│   │   ├── teacher_analyzer.py
│   │   ├── cognitive_profiler.py
│   │   ├── conflict_detector.py
│   │   └── economic_impact.py
│   ├── assistants/            # AI Assistants
│   │   ├── assistant_factory.py
│   │   ├── context_manager.py
│   │   ├── knowledge_base.py
│   │   └── memory.py
│   ├── security/              # Security & Governance
│   │   ├── jwt_auth.py
│   │   ├── banking_security.py
│   │   ├── ai_governance.py
│   │   └── ethics_validator.py
│   ├── scheduling/            # Schedule Optimization
│   │   ├── schedule_generator.py
│   │   ├── genetic_scheduler.py
│   │   ├── schedule_scorer.py
│   │   └── pedagogical_criteria.py
│   └── utils/                 # Utilities
│       ├── decision_logger.py
│       ├── crisis_mode.py
│       └── localization_adapter.py
└── routes/                    # API Endpoints
    ├── assistant.py
    ├── schedule.py
    ├── analytics.py
    └── admin.py
```

## Module Categories

### 🧠 Engines (AI Processing)
| Module | Purpose | Lines |
|--------|---------|-------|
| nlp_engine.py | Natural language understanding | 21,937 |
| ml_classifier.py | ML classification models | 25,545 |
| learning_engine.py | Continuous learning | 16,778 |
| expert_engine.py | Expert system rules | 14,407 |
| response_generator.py | Response generation | 15,709 |

### 📊 Analyzers
| Module | Purpose |
|--------|---------|
| risk_analyzer.py | Risk assessment |
| cognitive_profiler.py | Student profiling |
| conflict_detector.py | Schedule conflicts |
| economic_impact.py | Financial analysis |
| teacher_analyzer.py | Teacher performance |

### 🤖 Assistants
| Module | Purpose |
|--------|---------|
| assistant_factory.py | Role-based assistants |
| context_manager.py | Conversation context |
| knowledge_base.py | Institutional knowledge |
| memory.py | Persistent memory |

### 🔒 Security
| Module | Purpose |
|--------|---------|
| jwt_auth.py | JWT authentication |
| banking_security.py | Financial security |
| ai_governance.py | AI decision governance |
| ethics_validator.py | Ethical checks |

### 📅 Scheduling
| Module | Purpose |
|--------|---------|
| schedule_generator.py | Generate schedules |
| genetic_scheduler.py | Genetic algorithm optimization |
| schedule_scorer.py | Score schedules |
| pedagogical_criteria.py | Educational constraints |

## Usage

```python
from modules.engines import nlp_engine, ml_classifier
from modules.analyzers import risk_analyzer
from modules.assistants import assistant_factory
```
