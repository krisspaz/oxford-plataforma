"""
Conversation Memory for Oxford AI
=================================
Short-term and long-term memory for contextual responses
"""

import json
import sqlite3
from datetime import datetime, timedelta
from typing import List, Dict, Any, Optional
import logging

logger = logging.getLogger(__name__)


class ConversationMemory:
    """
    Manages conversation context for natural dialogue
    """
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_db()
        self._short_term = {}  # In-memory for current session
    
    def _init_db(self):
        """Create tables for long-term memory"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        # Conversation history
        c.execute('''
            CREATE TABLE IF NOT EXISTS conversation_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT,
                role TEXT,
                message TEXT,
                response TEXT,
                intent TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # User decisions/preferences (long-term)
        c.execute('''
            CREATE TABLE IF NOT EXISTS user_decisions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT,
                decision_type TEXT,
                decision_key TEXT,
                decision_value TEXT,
                context TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(user_id, decision_type, decision_key)
            )
        ''')
        
        conn.commit()
        conn.close()
    
    # =========================================
    # SHORT-TERM MEMORY (Current conversation)
    # =========================================
    
    def add_to_conversation(self, user_id: str, role: str, message: str, 
                           response: str, intent: str) -> None:
        """Add a message exchange to memory"""
        # Short-term (in-memory)
        if user_id not in self._short_term:
            self._short_term[user_id] = []
        
        self._short_term[user_id].append({
            "message": message,
            "response": response,
            "intent": intent,
            "timestamp": datetime.now().isoformat()
        })
        
        # Keep only last 10 messages in short-term
        if len(self._short_term[user_id]) > 10:
            self._short_term[user_id] = self._short_term[user_id][-10:]
        
        # Long-term (database) - async in production
        try:
            conn = sqlite3.connect(self.db_path)
            c = conn.cursor()
            c.execute('''
                INSERT INTO conversation_history (user_id, role, message, response, intent)
                VALUES (?, ?, ?, ?, ?)
            ''', (user_id, role, message, response, intent))
            conn.commit()
            conn.close()
        except Exception as e:
            logger.error(f"Error saving conversation: {e}")
    
    def get_recent_context(self, user_id: str, limit: int = 5) -> List[Dict]:
        """Get recent conversation for context"""
        if user_id in self._short_term:
            return self._short_term[user_id][-limit:]
        return []
    
    def get_context_summary(self, user_id: str) -> str:
        """Get a text summary of recent context for AI"""
        recent = self.get_recent_context(user_id, 3)
        if not recent:
            return ""
        
        summary = "Contexto reciente:\n"
        for msg in recent:
            summary += f"- Usuario dijo: \"{msg['message'][:50]}...\" (Intent: {msg['intent']})\n"
        
        return summary
    
    # =========================================
    # LONG-TERM MEMORY (Decisions/Preferences)
    # =========================================
    
    def save_decision(self, user_id: str, decision_type: str, 
                     key: str, value: str, context: str = "") -> bool:
        """
        Save a user decision for future reference
        
        Example:
            save_decision("user_1", "teacher_preference", "Juan_viernes", "no_disponible", 
                         "El usuario mencionó que Juan no puede los viernes")
        """
        try:
            conn = sqlite3.connect(self.db_path)
            c = conn.cursor()
            c.execute('''
                INSERT OR REPLACE INTO user_decisions 
                (user_id, decision_type, decision_key, decision_value, context)
                VALUES (?, ?, ?, ?, ?)
            ''', (user_id, decision_type, key, value, context))
            conn.commit()
            conn.close()
            
            logger.info(f"Saved decision: {decision_type}/{key}={value}")
            return True
        except Exception as e:
            logger.error(f"Error saving decision: {e}")
            return False
    
    def get_decision(self, user_id: str, decision_type: str, key: str) -> Optional[Dict]:
        """Retrieve a saved decision"""
        try:
            conn = sqlite3.connect(self.db_path)
            c = conn.cursor()
            c.execute('''
                SELECT decision_value, context, timestamp FROM user_decisions
                WHERE user_id = ? AND decision_type = ? AND decision_key = ?
            ''', (user_id, decision_type, key))
            row = c.fetchone()
            conn.close()
            
            if row:
                return {
                    "value": row[0],
                    "context": row[1],
                    "timestamp": row[2]
                }
        except Exception as e:
            logger.error(f"Error getting decision: {e}")
        return None
    
    def get_all_decisions(self, user_id: str, decision_type: str = None) -> List[Dict]:
        """Get all decisions for a user, optionally filtered by type"""
        try:
            conn = sqlite3.connect(self.db_path)
            c = conn.cursor()
            
            if decision_type:
                c.execute('''
                    SELECT decision_key, decision_value, context, timestamp 
                    FROM user_decisions
                    WHERE user_id = ? AND decision_type = ?
                    ORDER BY timestamp DESC
                ''', (user_id, decision_type))
            else:
                c.execute('''
                    SELECT decision_type, decision_key, decision_value, context, timestamp 
                    FROM user_decisions
                    WHERE user_id = ?
                    ORDER BY timestamp DESC
                ''', (user_id,))
            
            rows = c.fetchall()
            conn.close()
            
            if decision_type:
                return [{"key": r[0], "value": r[1], "context": r[2], "timestamp": r[3]} for r in rows]
            else:
                return [{"type": r[0], "key": r[1], "value": r[2], "context": r[3], "timestamp": r[4]} for r in rows]
                
        except Exception as e:
            logger.error(f"Error getting decisions: {e}")
        return []
    
    def check_if_mentioned(self, user_id: str, topic: str) -> Optional[str]:
        """
        Check if user has mentioned something before
        
        Example:
            check_if_mentioned("user_1", "Juan_viernes") 
            -> "no_disponible" or None
        """
        # Check short-term first
        if user_id in self._short_term:
            for msg in self._short_term[user_id]:
                if topic.lower() in msg.get("message", "").lower():
                    return msg.get("message")
        
        # Check long-term decisions
        decision = self.get_decision(user_id, "mentioned", topic)
        if decision:
            return decision["value"]
        
        return None


# Global instance
memory = ConversationMemory()
