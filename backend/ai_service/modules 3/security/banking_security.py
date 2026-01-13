"""
FASE 2: Seguridad Nivel Banca (Complemento)
===========================================
MFA, permisos temporales, detección de anomalías
"""

from dataclasses import dataclass
from typing import List, Dict, Optional, Callable
from datetime import datetime, timedelta
import json
import sqlite3
import hashlib
import secrets
import logging

logger = logging.getLogger(__name__)


@dataclass
class MFAChallenge:
    """Desafío de autenticación multifactor"""
    challenge_id: str
    user_id: str
    method: str  # "totp", "email", "sms"
    code: str
    created_at: datetime
    expires_at: datetime
    verified: bool = False


@dataclass
class TemporaryPermission:
    """Permiso temporal"""
    permission_id: str
    user_id: str
    permission: str
    granted_by: str
    reason: str
    created_at: datetime
    expires_at: datetime
    revoked: bool = False


@dataclass
class AnomalyAlert:
    """Alerta de anomalía detectada"""
    alert_id: str
    user_id: str
    action: str
    severity: str
    description: str
    detected_at: datetime
    resolved: bool = False


class BankingGradeSecurity:
    """
    Seguridad de nivel bancario
    
    Componentes:
    - MFA (Multi-Factor Authentication)
    - Permisos temporales
    - Detección de anomalías
    - DLP (Data Loss Prevention)
    """
    
    # Acciones que requieren MFA
    MFA_REQUIRED_ACTIONS = [
        "delete_data",
        "modify_rules",
        "export_all_data",
        "modify_permissions",
        "change_password",
        "access_sensitive_reports",
    ]
    
    # Patrones de anomalía
    ANOMALY_PATTERNS = {
        "excessive_queries": {"threshold": 100, "window_minutes": 5},
        "unusual_hours": {"start": 22, "end": 6},
        "bulk_export": {"threshold": 1000},
        "failed_logins": {"threshold": 5, "window_minutes": 10},
    }
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_tables()
    
    def _init_tables(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS mfa_challenges (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                challenge_id TEXT UNIQUE,
                user_id TEXT,
                method TEXT,
                code_hash TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                expires_at DATETIME,
                verified INTEGER DEFAULT 0
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS temporary_permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                permission_id TEXT UNIQUE,
                user_id TEXT,
                permission TEXT,
                granted_by TEXT,
                reason TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                expires_at DATETIME,
                revoked INTEGER DEFAULT 0
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS anomaly_alerts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                alert_id TEXT UNIQUE,
                user_id TEXT,
                action TEXT,
                severity TEXT,
                description TEXT,
                raw_data TEXT,
                detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                resolved INTEGER DEFAULT 0,
                resolved_by TEXT,
                resolved_at DATETIME
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS action_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT,
                action TEXT,
                details TEXT,
                ip_address TEXT,
                user_agent TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS dlp_rules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                rule_name TEXT,
                pattern TEXT,
                action TEXT,
                severity TEXT,
                active INTEGER DEFAULT 1
            )
        ''')
        
        conn.commit()
        conn.close()
    
    # =========================================
    # MFA (Multi-Factor Authentication)
    # =========================================
    
    def requires_mfa(self, action: str) -> bool:
        """Verificar si una acción requiere MFA"""
        return action in self.MFA_REQUIRED_ACTIONS
    
    def create_mfa_challenge(self, user_id: str, method: str = "email") -> MFAChallenge:
        """Crear desafío MFA"""
        code = ''.join([str(secrets.randbelow(10)) for _ in range(6)])
        challenge_id = f"MFA_{secrets.token_hex(8)}"
        expires_at = datetime.now() + timedelta(minutes=5)
        
        # Hash del código
        code_hash = hashlib.sha256(code.encode()).hexdigest()
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO mfa_challenges 
            (challenge_id, user_id, method, code_hash, expires_at)
            VALUES (?, ?, ?, ?, ?)
        ''', (challenge_id, user_id, method, code_hash, expires_at))
        
        conn.commit()
        conn.close()
        
        logger.info(f"MFA challenge created for user {user_id}")
        
        return MFAChallenge(
            challenge_id=challenge_id,
            user_id=user_id,
            method=method,
            code=code,  # En producción: enviar por email/sms
            created_at=datetime.now(),
            expires_at=expires_at
        )
    
    def verify_mfa(self, challenge_id: str, code: str) -> bool:
        """Verificar código MFA"""
        code_hash = hashlib.sha256(code.encode()).hexdigest()
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT id FROM mfa_challenges 
            WHERE challenge_id = ? AND code_hash = ? 
            AND verified = 0 AND expires_at > ?
        ''', (challenge_id, code_hash, datetime.now()))
        
        result = c.fetchone()
        
        if result:
            c.execute('''
                UPDATE mfa_challenges SET verified = 1 WHERE challenge_id = ?
            ''', (challenge_id,))
            conn.commit()
        
        conn.close()
        return result is not None
    
    # =========================================
    # TEMPORARY PERMISSIONS
    # =========================================
    
    def grant_temporary_permission(self, user_id: str, permission: str,
                                   granted_by: str, reason: str,
                                   duration_hours: int = 24) -> TemporaryPermission:
        """Otorgar permiso temporal"""
        permission_id = f"TEMP_{secrets.token_hex(8)}"
        expires_at = datetime.now() + timedelta(hours=duration_hours)
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO temporary_permissions 
            (permission_id, user_id, permission, granted_by, reason, expires_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (permission_id, user_id, permission, granted_by, reason, expires_at))
        
        conn.commit()
        conn.close()
        
        logger.info(f"Temporary permission granted: {permission} to {user_id} by {granted_by}")
        
        return TemporaryPermission(
            permission_id=permission_id,
            user_id=user_id,
            permission=permission,
            granted_by=granted_by,
            reason=reason,
            created_at=datetime.now(),
            expires_at=expires_at
        )
    
    def check_temporary_permission(self, user_id: str, permission: str) -> bool:
        """Verificar si usuario tiene permiso temporal activo"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT 1 FROM temporary_permissions 
            WHERE user_id = ? AND permission = ? 
            AND expires_at > ? AND revoked = 0
        ''', (user_id, permission, datetime.now()))
        
        result = c.fetchone()
        conn.close()
        
        return result is not None
    
    def revoke_temporary_permission(self, permission_id: str) -> bool:
        """Revocar permiso temporal"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            UPDATE temporary_permissions SET revoked = 1 WHERE permission_id = ?
        ''', (permission_id,))
        
        success = c.rowcount > 0
        conn.commit()
        conn.close()
        
        return success
    
    # =========================================
    # ANOMALY DETECTION
    # =========================================
    
    def log_action(self, user_id: str, action: str, details: Dict = None,
                  ip_address: str = None, user_agent: str = None):
        """Registrar acción para análisis de anomalías"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO action_log 
            (user_id, action, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ''', (user_id, action, json.dumps(details) if details else None,
              ip_address, user_agent))
        
        conn.commit()
        conn.close()
        
        # Analizar anomalías
        self._check_for_anomalies(user_id, action)
    
    def _check_for_anomalies(self, user_id: str, action: str):
        """Verificar patrones de anomalía"""
        anomalies = []
        
        # Verificar hora inusual
        hour = datetime.now().hour
        if hour >= self.ANOMALY_PATTERNS["unusual_hours"]["start"] or \
           hour <= self.ANOMALY_PATTERNS["unusual_hours"]["end"]:
            anomalies.append({
                "type": "unusual_hours",
                "severity": "medium",
                "description": f"Acceso a hora inusual ({hour}:00)"
            })
        
        # Verificar consultas excesivas
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        window_start = datetime.now() - timedelta(minutes=5)
        c.execute('''
            SELECT COUNT(*) FROM action_log 
            WHERE user_id = ? AND timestamp > ?
        ''', (user_id, window_start))
        
        query_count = c.fetchone()[0]
        conn.close()
        
        if query_count > self.ANOMALY_PATTERNS["excessive_queries"]["threshold"]:
            anomalies.append({
                "type": "excessive_queries",
                "severity": "high",
                "description": f"{query_count} acciones en 5 minutos"
            })
        
        # Registrar anomalías encontradas
        for anomaly in anomalies:
            self._create_anomaly_alert(user_id, action, anomaly)
    
    def _create_anomaly_alert(self, user_id: str, action: str, anomaly: Dict):
        """Crear alerta de anomalía"""
        alert_id = f"ALERT_{secrets.token_hex(8)}"
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO anomaly_alerts 
            (alert_id, user_id, action, severity, description, raw_data)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (alert_id, user_id, action, anomaly["severity"],
              anomaly["description"], json.dumps(anomaly)))
        
        conn.commit()
        conn.close()
        
        logger.warning(f"🚨 Anomaly detected: {anomaly['description']} for user {user_id}")
    
    def get_unresolved_alerts(self) -> List[Dict]:
        """Obtener alertas no resueltas"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT * FROM anomaly_alerts WHERE resolved = 0
            ORDER BY detected_at DESC
        ''')
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "alert_id": r[1],
            "user_id": r[2],
            "action": r[3],
            "severity": r[4],
            "description": r[5],
            "detected_at": r[7]
        } for r in rows]
    
    # =========================================
    # DLP (Data Loss Prevention)
    # =========================================
    
    def check_dlp(self, data: str, action: str = "export") -> Dict:
        """Verificar reglas DLP"""
        violations = []
        
        # Patrones sensibles
        patterns = [
            ("email", r"[\w\.-]+@[\w\.-]+\.\w+"),
            ("phone", r"\d{8,}"),
            ("password", r"password|contraseña|clave"),
        ]
        
        import re
        for pattern_name, pattern in patterns:
            if re.search(pattern, data, re.IGNORECASE):
                violations.append({
                    "pattern": pattern_name,
                    "action": "block" if action == "export" else "warn"
                })
        
        return {
            "allowed": len(violations) == 0,
            "violations": violations
        }


# Global instance
banking_security = BankingGradeSecurity()
