"""
Redis Cache Module for AI Service
==================================
Caches frequent responses to reduce latency
"""

import json
import hashlib
import logging
from typing import Optional, Any
from functools import wraps

# Try to import Redis
try:
    import redis
    REDIS_AVAILABLE = True
except ImportError:
    REDIS_AVAILABLE = False
    logging.warning("redis-py not installed. Caching disabled.")


class AICache:
    """
    Simple caching layer for AI responses
    Uses Redis if available, falls back to in-memory dict
    """
    
    def __init__(self, redis_url: str = "redis://localhost:6379/0", default_ttl: int = 300):
        """
        Initialize cache
        
        Args:
            redis_url: Redis connection URL
            default_ttl: Default time-to-live in seconds (5 min)
        """
        self.default_ttl = default_ttl
        self.logger = logging.getLogger(__name__)
        self._memory_cache = {}  # Fallback cache
        
        if REDIS_AVAILABLE:
            try:
                self.redis_client = redis.from_url(redis_url, decode_responses=True)
                self.redis_client.ping()  # Test connection
                self.use_redis = True
                self.logger.info("Redis cache connected")
            except Exception as e:
                self.logger.warning(f"Redis connection failed: {e}. Using memory cache.")
                self.use_redis = False
                self.redis_client = None
        else:
            self.use_redis = False
            self.redis_client = None
    
    def _make_key(self, prefix: str, data: str) -> str:
        """Generate a cache key from prefix and data"""
        hash_val = hashlib.md5(data.encode()).hexdigest()[:12]
        return f"ai:{prefix}:{hash_val}"
    
    def get(self, key: str) -> Optional[Any]:
        """Get value from cache"""
        try:
            if self.use_redis:
                value = self.redis_client.get(key)
                if value:
                    return json.loads(value)
            else:
                return self._memory_cache.get(key)
        except Exception as e:
            self.logger.error(f"Cache get error: {e}")
        return None
    
    def set(self, key: str, value: Any, ttl: int = None) -> bool:
        """Set value in cache"""
        try:
            ttl = ttl or self.default_ttl
            json_value = json.dumps(value, ensure_ascii=False)
            
            if self.use_redis:
                self.redis_client.setex(key, ttl, json_value)
            else:
                self._memory_cache[key] = value
                # Note: Memory cache doesn't support TTL natively
                # In production, use Redis
            return True
        except Exception as e:
            self.logger.error(f"Cache set error: {e}")
            return False
    
    def delete(self, key: str) -> bool:
        """Delete key from cache"""
        try:
            if self.use_redis:
                self.redis_client.delete(key)
            else:
                self._memory_cache.pop(key, None)
            return True
        except Exception as e:
            self.logger.error(f"Cache delete error: {e}")
            return False
    
    def clear_pattern(self, pattern: str) -> int:
        """Clear all keys matching pattern (Redis only)"""
        if not self.use_redis:
            self._memory_cache.clear()
            return len(self._memory_cache)
        
        try:
            keys = self.redis_client.keys(pattern)
            if keys:
                return self.redis_client.delete(*keys)
        except Exception as e:
            self.logger.error(f"Cache clear error: {e}")
        return 0


def cache_response(prefix: str = "resp", ttl: int = 300):
    """
    Decorator to cache function responses
    
    Usage:
        @cache_response(prefix="nlp", ttl=600)
        def process_command(text):
            ...
    """
    def decorator(f):
        @wraps(f)
        def decorated(*args, **kwargs):
            cache = ai_cache
            
            # Create cache key from function args
            cache_data = f"{f.__name__}:{str(args)}:{str(kwargs)}"
            cache_key = cache._make_key(prefix, cache_data)
            
            # Try to get from cache
            cached = cache.get(cache_key)
            if cached is not None:
                return cached
            
            # Execute function
            result = f(*args, **kwargs)
            
            # Cache result
            cache.set(cache_key, result, ttl)
            
            return result
        return decorated
    return decorator


# Global instance
ai_cache = AICache()
