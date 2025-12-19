from typing import Dict, List, Optional, Any
from dataclasses import dataclass, field
from datetime import datetime

@dataclass
class Conversationturn:
    role: str  # 'user' or 'system'
    content: str
    timestamp: datetime = field(default_factory=datetime.now)
    intent: Optional[str] = None
    sentiment: float = 0.0

@dataclass
class SessionState:
    history: List[Conversationturn] = field(default_factory=list)
    config: Dict[str, Any] = field(default_factory=dict)
    pending_confirmation: Optional[Dict[str, Any]] = None
    last_interaction: datetime = field(default_factory=datetime.now)
    metadata: Dict[str, Any] = field(default_factory=dict)

class ContextManager:
    """
    Manages conversational context, memory, and session state.
    """
    def __init__(self):
        self.sessions: Dict[str, SessionState] = {}
        
    def get_session(self, session_id: str) -> SessionState:
        if session_id not in self.sessions:
            self.sessions[session_id] = SessionState()
        
        session = self.sessions[session_id]
        session.last_interaction = datetime.now()
        return session

    def add_user_message(self, session_id: str, content: str, sentiment: float = 0.0):
        session = self.get_session(session_id)
        session.history.append(Conversationturn(
            role='user', 
            content=content, 
            sentiment=sentiment
        ))
        
        # Keep only last 10 turns for short-term memory
        if len(session.history) > 20: 
            session.history = session.history[-20:]

    def add_system_message(self, session_id: str, content: str, intent: str = None):
        session = self.get_session(session_id)
        session.history.append(Conversationturn(
            role='system', 
            content=content,
            intent=intent
        ))

    def update_config(self, session_id: str, changes: Dict[str, Any]):
        """Update the accumulated configuration for this session"""
        session = self.get_session(session_id)
        session.config.update(changes)

    def get_context_summary(self, session_id: str) -> str:
        """Get a text summary of the current session context"""
        session = self.get_session(session_id)
        if not session.history:
            return ""
        
        # Analyze last few messages to determine topic
        return f"Last topic: {session.history[-1].intent or 'Unknown'}"

    def set_pending_confirmation(self, session_id: str, action: Dict[str, Any]):
        session = self.get_session(session_id)
        session.pending_confirmation = action

    def get_pending_confirmation(self, session_id: str) -> Optional[Dict[str, Any]]:
        return self.get_session(session_id).pending_confirmation

    def clear_pending_confirmation(self, session_id: str):
        session = self.get_session(session_id)
        session.pending_confirmation = None

    def clear_session(self, session_id: str):
        if session_id in self.sessions:
            # We keep the object but reset contents to preserve reference if needed, 
            # or just delete. Deleting is safer for "Reset".
            del self.sessions[session_id]

# Global instance
context_manager = ContextManager()
