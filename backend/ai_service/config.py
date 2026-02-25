"""
AI Service Configuration Module
================================
Centralized configuration management for the AI service
"""

import os
from dataclasses import dataclass

@dataclass
class Config:
    """Configuration for AI service"""
    
    # Service settings
    host: str = "0.0.0.0"
    port: int = 8001
    log_level: str = "INFO"
    debug: bool = False
    environment: str = "production"
    
    # Database
    db_name: str = "oxford_db"
    db_user: str = "postgres"
    db_password: str = "password"
    db_host: str = "localhost"

    # Security
    secret_key: str = "dev_secret"
    algorithm: str = "HS256"
    access_token_expire_minutes: int = 30
    github_repository: str = "krisspaz/oxford-plataforma"

    # Genetic Algorithm Config
    population_size: int = 50
    generations: int = 100
    mutation_rate: float = 0.01
    max_teacher_hours_per_day: int = 6
    
    # NLP settings
    fuzzy_match_threshold: float = 0.7
    max_suggestions: int = 5
    
    # Conflict detection
    enable_conflict_detection: bool = True
    
    # Schedule Generation Defaults
    default_class_duration: int = 45 
    default_recess_duration: int = 30 
    max_periods_per_day: int = 8
    default_start_time: str = "07:30"
    default_end_time: str = "14:00"

    @classmethod
    def from_env(cls) -> "Config":
        """Load configuration from environment variables with sane production defaults."""

        env = os.getenv("APP_ENV", "production")

        # Security: SECRET_KEY must be set in production
        secret = os.getenv("SECRET_KEY")
        if env == "production":
            if not secret or secret.strip() in ("dev_secret", "", "CHANGE_ME_SECRET_KEY_64_HEX_CHARS"):
                raise RuntimeError(
                    "AI Service: SECRET_KEY must be set to a secure value in production"
                )
        else:
            # In non-prod we allow a predictable secret for convenience
            secret = secret or "dev_secret"

        db_password = os.getenv("DB_PASSWORD", "password")
        if env == "production" and db_password in ("password", "oxford2026", "CHANGE_ME_DB_PASSWORD"):
            raise RuntimeError(
                "AI Service: DB_PASSWORD must be set to a secure value in production"
            )

        return cls(
            host=os.getenv("AI_SERVICE_HOST", "0.0.0.0"),
            port=int(os.getenv("AI_SERVICE_PORT", "8001")),
            log_level=os.getenv("LOG_LEVEL", "INFO"),
            debug=os.getenv("DEBUG", "false").lower() == "true",
            environment=env,

            db_name=os.getenv("DB_NAME", "oxford_db"),
            db_user=os.getenv("DB_USER", "postgres"),
            db_password=db_password,
            db_host=os.getenv("DB_HOST", "localhost"),

            secret_key=secret,
            algorithm=os.getenv("ALGORITHM", "HS256"),

            population_size=int(os.getenv("POPULATION_SIZE", "50")),
            generations=int(os.getenv("GENERATIONS", "100")),

            default_class_duration=int(os.getenv("CLASS_DURATION", "45")),
            default_start_time=os.getenv("START_TIME", "07:30")
        )

# Global config instance
config = Config.from_env()
