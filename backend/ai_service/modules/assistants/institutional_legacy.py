"""
FASE 21: Legado y Cultura Institucional
========================================
Congelar filosofía, proteger criterio histórico
"""

from dataclasses import dataclass
from typing import List, Dict, Optional
from datetime import datetime
import json
import sqlite3
import hashlib
import logging

logger = logging.getLogger(__name__)


@dataclass
class InstitutionalPrinciple:
    """Principio institucional inmutable"""
    principle_id: str
    name: str
    description: str
    rationale: str  # Por qué existe
    established_date: datetime
    established_by: str
    is_frozen: bool  # No puede ser modificado


@dataclass  
class CulturalDecision:
    """Decisión que forma parte de la cultura institucional"""
    decision_id: str
    context: str
    decision: str
    reasoning: str
    made_by: str
    made_at: datetime
    importance: str  # "foundational", "significant", "routine"


class InstitutionalLegacy:
    """
    Motor de Legado y Cultura Institucional
    
    Funciones:
    - Congelar filosofía institucional
    - Proteger criterio histórico
    - Crear cultura de decisión
    - Independencia de personas clave
    """
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_tables()
        self._init_core_principles()
    
    def _init_tables(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS institutional_principles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                principle_id TEXT UNIQUE,
                name TEXT,
                description TEXT,
                rationale TEXT,
                established_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                established_by TEXT,
                is_frozen INTEGER DEFAULT 0,
                hash_signature TEXT
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS cultural_decisions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                decision_id TEXT UNIQUE,
                context TEXT,
                decision TEXT,
                reasoning TEXT,
                made_by TEXT,
                made_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                importance TEXT DEFAULT 'routine',
                is_archived INTEGER DEFAULT 0
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS legacy_knowledge (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT,
                knowledge_key TEXT,
                knowledge_value TEXT,
                source TEXT,
                verified INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(category, knowledge_key)
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS succession_knowledge (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                role TEXT,
                knowledge_area TEXT,
                critical_info TEXT,
                documented_by TEXT,
                last_verified DATETIME,
                importance TEXT DEFAULT 'high'
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def _init_core_principles(self):
        """Inicializar principios fundamentales"""
        core = [
            InstitutionalPrinciple(
                principle_id="P001",
                name="Bienestar Estudiantil Primero",
                description="Toda decisión de horarios debe priorizar el bienestar y aprendizaje del estudiante sobre conveniencia administrativa.",
                rationale="El propósito de la institución es formar estudiantes, no optimizar operaciones.",
                established_date=datetime.now(),
                established_by="Fundación Institucional",
                is_frozen=True
            ),
            InstitutionalPrinciple(
                principle_id="P002",
                name="Respeto al Docente",
                description="Los docentes no son recursos intercambiables. Su bienestar y desarrollo profesional son prioridad.",
                rationale="Docentes saludables y motivados generan mejor educación.",
                established_date=datetime.now(),
                established_by="Fundación Institucional",
                is_frozen=True
            ),
            InstitutionalPrinciple(
                principle_id="P003",
                name="Transparencia en Decisiones",
                description="Toda decisión algorítmica debe ser explicable y justificable en términos humanos.",
                rationale="La confianza institucional requiere transparencia.",
                established_date=datetime.now(),
                established_by="Fundación Institucional",
                is_frozen=True
            ),
            InstitutionalPrinciple(
                principle_id="P004",
                name="Equidad",
                description="La carga de trabajo debe distribuirse equitativamente, sin favoritismos.",
                rationale="La equidad genera confianza y compromiso.",
                established_date=datetime.now(),
                established_by="Fundación Institucional",
                is_frozen=True
            ),
            InstitutionalPrinciple(
                principle_id="P005",
                name="Continuidad Educativa",
                description="La estabilidad y continuidad del proceso educativo es prioritaria sobre cambios frecuentes.",
                rationale="Los estudiantes aprenden mejor con rutinas estables.",
                established_date=datetime.now(),
                established_by="Fundación Institucional",
                is_frozen=True
            ),
        ]
        
        for principle in core:
            self.register_principle(principle, force=False)
    
    # =========================================
    # PRINCIPLES MANAGEMENT
    # =========================================
    
    def register_principle(self, principle: InstitutionalPrinciple, 
                          force: bool = False) -> bool:
        """Registrar un principio institucional"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        # Verificar si ya existe
        c.execute('SELECT is_frozen FROM institutional_principles WHERE principle_id = ?',
                 (principle.principle_id,))
        existing = c.fetchone()
        
        if existing:
            if existing[0] == 1 and not force:
                logger.warning(f"Principio {principle.principle_id} está congelado")
                conn.close()
                return False
        
        # Generar hash de integridad
        content = f"{principle.name}{principle.description}{principle.rationale}"
        hash_sig = hashlib.sha256(content.encode()).hexdigest()[:16]
        
        c.execute('''
            INSERT OR REPLACE INTO institutional_principles 
            (principle_id, name, description, rationale, established_by, is_frozen, hash_signature)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', (
            principle.principle_id,
            principle.name,
            principle.description,
            principle.rationale,
            principle.established_by,
            1 if principle.is_frozen else 0,
            hash_sig
        ))
        
        conn.commit()
        conn.close()
        return True
    
    def get_principles(self) -> List[Dict]:
        """Obtener todos los principios"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('SELECT * FROM institutional_principles ORDER BY id')
        rows = c.fetchall()
        conn.close()
        
        return [{
            "id": r[1],
            "name": r[2],
            "description": r[3],
            "rationale": r[4],
            "established_date": r[5],
            "established_by": r[6],
            "is_frozen": r[7] == 1
        } for r in rows]
    
    def verify_principle_integrity(self, principle_id: str) -> bool:
        """Verificar que un principio no ha sido alterado"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT name, description, rationale, hash_signature 
            FROM institutional_principles WHERE principle_id = ?
        ''', (principle_id,))
        
        row = c.fetchone()
        conn.close()
        
        if not row:
            return False
        
        content = f"{row[0]}{row[1]}{row[2]}"
        expected_hash = hashlib.sha256(content.encode()).hexdigest()[:16]
        
        return expected_hash == row[3]
    
    # =========================================
    # CULTURAL DECISIONS
    # =========================================
    
    def record_cultural_decision(self, context: str, decision: str, 
                                 reasoning: str, made_by: str,
                                 importance: str = "routine") -> str:
        """Registrar una decisión cultural importante"""
        decision_id = f"CD_{datetime.now().strftime('%Y%m%d%H%M%S')}"
        
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT INTO cultural_decisions 
            (decision_id, context, decision, reasoning, made_by, importance)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (decision_id, context, decision, reasoning, made_by, importance))
        
        conn.commit()
        conn.close()
        
        return decision_id
    
    def get_foundational_decisions(self) -> List[Dict]:
        """Obtener decisiones fundacionales"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT * FROM cultural_decisions 
            WHERE importance = 'foundational' AND is_archived = 0
            ORDER BY made_at DESC
        ''')
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "id": r[1],
            "context": r[2],
            "decision": r[3],
            "reasoning": r[4],
            "made_by": r[5],
            "made_at": r[6]
        } for r in rows]
    
    # =========================================
    # SUCCESSION PLANNING
    # =========================================
    
    def document_succession_knowledge(self, role: str, area: str, 
                                       info: str, documented_by: str):
        """Documentar conocimiento para sucesión"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT OR REPLACE INTO succession_knowledge 
            (role, knowledge_area, critical_info, documented_by, last_verified)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
        ''', (role, area, info, documented_by))
        
        conn.commit()
        conn.close()
    
    def get_succession_guide(self, role: str) -> List[Dict]:
        """Obtener guía de sucesión para un rol"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            SELECT knowledge_area, critical_info, last_verified, importance
            FROM succession_knowledge WHERE role = ?
            ORDER BY importance DESC
        ''', (role,))
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "area": r[0],
            "info": r[1],
            "last_verified": r[2],
            "importance": r[3]
        } for r in rows]
    
    # =========================================
    # LEGACY KNOWLEDGE
    # =========================================
    
    def add_legacy_knowledge(self, category: str, key: str, 
                            value: str, source: str):
        """Agregar conocimiento al legado institucional"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            INSERT OR REPLACE INTO legacy_knowledge 
            (category, knowledge_key, knowledge_value, source)
            VALUES (?, ?, ?, ?)
        ''', (category, key, value, source))
        
        conn.commit()
        conn.close()
    
    def search_legacy(self, category: str = None, query: str = None) -> List[Dict]:
        """Buscar en el conocimiento legado"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        if category and query:
            c.execute('''
                SELECT * FROM legacy_knowledge 
                WHERE category = ? AND 
                (knowledge_key LIKE ? OR knowledge_value LIKE ?)
            ''', (category, f"%{query}%", f"%{query}%"))
        elif category:
            c.execute('SELECT * FROM legacy_knowledge WHERE category = ?', (category,))
        else:
            c.execute('SELECT * FROM legacy_knowledge')
        
        rows = c.fetchall()
        conn.close()
        
        return [{
            "category": r[1],
            "key": r[2],
            "value": r[3],
            "source": r[4],
            "verified": r[5] == 1
        } for r in rows]
    
    def generate_institutional_dna(self) -> Dict:
        """Generar el ADN institucional completo"""
        return {
            "principles": self.get_principles(),
            "foundational_decisions": self.get_foundational_decisions(),
            "generated_at": datetime.now().isoformat(),
            "integrity_verified": all(
                self.verify_principle_integrity(p["id"]) 
                for p in self.get_principles()
            )
        }


# Global instance
institutional_legacy = InstitutionalLegacy()
