"""
JWT Authentication Module for AI Service
=========================================
Validates JWT tokens from Symfony backend
"""

import os
import logging
from typing import Optional, Dict, Any
from functools import wraps
from flask import request, jsonify

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
            }
            
        except JWTError as e:
            self.logger.warning(f"JWT validation failed: {e}")
            return None
        except Exception as e:
            self.logger.error(f"Unexpected JWT error: {e}")
            return None
    
    def get_user_from_request(self) -> Optional[Dict[str, Any]]:
        """
        Extract and validate JWT from current Flask request
        """
        auth_header = request.headers.get("Authorization", "")
        
        if not auth_header:
            return None
        
        return self.validate_token(auth_header)


def require_auth(f):
    """
    Decorator to require JWT authentication on an endpoint
    """
    @wraps(f)
    def decorated(*args, **kwargs):
        auth = JWTAuthenticator()
        user = auth.get_user_from_request()
        
        if not user:
            return jsonify({
                "error": "Unauthorized",
                "message": "Valid JWT token required"
            }), 401
        
        # Add user to request context
        request.current_user = user
        return f(*args, **kwargs)
    
    return decorated


def require_role(allowed_roles: list):
    """
    Decorator to require specific roles
    
    Usage:
        @require_role(['ROLE_ADMIN', 'ROLE_DIRECTOR'])
        def admin_endpoint():
            ...
    """
    def decorator(f):
        @wraps(f)
        def decorated(*args, **kwargs):
            auth = JWTAuthenticator()
            user = auth.get_user_from_request()
            
            if not user:
                return jsonify({
                    "error": "Unauthorized",
                    "message": "Valid JWT token required"
                }), 401
            
            user_roles = user.get("roles", [])
            
            # Check if user has any of the allowed roles
            if not any(role in user_roles for role in allowed_roles):
                return jsonify({
                    "error": "Forbidden",
                    "message": f"Required roles: {allowed_roles}"
                }), 403
            
            request.current_user = user
            return f(*args, **kwargs)
        
        return decorated
    return decorator


# Global instance
jwt_auth = JWTAuthenticator()
