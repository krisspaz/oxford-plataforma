"""
JWT Authentication Module for AI Service
=========================================
Validates JWT tokens from Symfony backend (FastAPI version)
"""

import os
import logging
from typing import Optional, Dict, Any

# JWT handling
try:
    from jose import jwt, JWTError
    JWT_AVAILABLE = True
except ImportError:
    JWT_AVAILABLE = False
    logging.warning("python-jose not installed. JWT validation disabled.")

class JWTAuthenticator:
    """
    JWT Token validator that works with Symfony's LexikJWTAuthenticationBundle
    """
    
    def __init__(self, secret_key: str = None, algorithm: str = "HS256"):
        """
        Initialize JWT authenticator
        
        Args:
            secret_key: JWT secret (same as Symfony's JWT_SECRET_KEY)
            algorithm: JWT algorithm (default HS256)
        """
        self.secret_key = secret_key or os.getenv("JWT_SECRET_KEY", "oxford_dev_secret_key_change_in_production")
        self.algorithm = algorithm
        self.logger = logging.getLogger(__name__)
    
    def validate_token(self, token: str) -> Optional[Dict[str, Any]]:
        """
        Validate a JWT token and return the payload
        
        Returns:
            Dict with user info if valid, None if invalid
        """
        if not JWT_AVAILABLE:
            self.logger.warning("JWT library not available, skipping validation")
            return {"user_id": "dev", "roles": ["ROLE_ADMIN"], "email": "dev@localhost"}
        
        try:
            # Remove 'Bearer ' prefix if present
            if token.startswith("Bearer "):
                token = token[7:]
            
            payload = jwt.decode(
                token,
                self.secret_key,
                algorithms=[self.algorithm]
            )
            
            return {
                "user_id": payload.get("id") or payload.get("sub"),
                "email": payload.get("email") or payload.get("username"),
                "roles": payload.get("roles", []),
                "teacher_id": payload.get("teacherId"),
                "student_id": payload.get("studentId"),
                # Keep original fields too just in case
                "sub": payload.get("sub"),
                "username": payload.get("username")
            }
            
        except JWTError as e:
            self.logger.warning(f"JWT validation failed: {e}")
            raise e
        except Exception as e:
            self.logger.error(f"Unexpected JWT error: {e}")
            raise e

# Global instance
jwt_auth = JWTAuthenticator()

def verify_token(token: str):
    """
    Standalone function for FastAPI dependencies
    """
    return jwt_auth.validate_token(token)
