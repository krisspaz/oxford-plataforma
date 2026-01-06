"""
AI Service Configuration Module
================================
Centralized configuration management for the AI service
"""

import os
from dataclasses import dataclass
from typing import Optional

@dataclass
class AIServiceConfig:
    """Configuration for AI service"""
    
    # Service settings
    host: str = "0.0.0.0"
    port: int = 8001
    log_level: str = "INFO"
    debug: bool = False
    
    # Schedule generation
    default_class_duration: int = 45  # minutes
    default_recess_duration: int = 30  # minutes
    max_periods_per_day: int = 8
    default_start_time: str = "07:30"
    default_end_time: str = "14:00"
    
    # NLP settings
    fuzzy_match_threshold: float = 0.7
    max_suggestions: int = 5
    
    # Conflict detection
    enable_conflict_detection: bool = True
    max_teacher_hours_per_day: int = 6
    
    # Security
    secret_key: str
    algorithm: str

    @classmethod
    def from_env(cls) -> "AIServiceConfig":
        """Load configuration from environment variables"""
        
        # Security Check
        secret = os.getenv("SECRET_KEY")
        algo = os.getenv("ALGORITHM", "HS256")
        env_mode = os.getenv("FLASK_ENV", "production") # Default to prod for safety

        if not secret or secret == "CHANGEME_IN_PROD":
            if env_mode == "production":
                raise ValueError("CRITICAL: SECRET_KEY is missing or insecure in PRODUCTION environment.")
            else:
                # Dev fallback only
                print("⚠️ WARNING: Using insecure default SECRET_KEY for development.")
                secret = "dev_secret_key_change_me"

        return cls(
            host=os.getenv("AI_SERVICE_HOST", "0.0.0.0"),
            port=int(os.getenv("AI_SERVICE_PORT", "8001")),
            log_level=os.getenv("LOG_LEVEL", "INFO"),
            debug=os.getenv("DEBUG", "false").lower() == "true",
            default_class_duration=int(os.getenv("CLASS_DURATION", "45")),
            default_recess_duration=int(os.getenv("RECESS_DURATION", "30")),
            max_periods_per_day=int(os.getenv("MAX_PERIODS", "8")),
            default_start_time=os.getenv("START_TIME", "07:30"),
            default_end_time=os.getenv("END_TIME", "14:00"),
            fuzzy_match_threshold=float(os.getenv("FUZZY_THRESHOLD", "0.7")),
            max_suggestions=int(os.getenv("MAX_SUGGESTIONS", "5")),
            enable_conflict_detection=os.getenv("ENABLE_CONFLICT_DETECTION", "true").lower() == "true",
            max_teacher_hours_per_day=int(os.getenv("MAX_TEACHER_HOURS", "6")),
            secret_key=secret,
            algorithm=algo,
        )

# Global config instance
config = AIServiceConfig.from_env()
