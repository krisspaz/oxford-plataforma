"""
FASE 19: Impacto Económico
==========================
Medición de costos ocultos, ahorro cuantificado
"""

from dataclasses import dataclass
from typing import List, Dict, Optional
from datetime import datetime, timedelta
import sqlite3
import json
import logging

logger = logging.getLogger(__name__)


@dataclass
class CostItem:
    """Item de costo identificado"""
    category: str
    description: str
    estimated_cost: float  # En moneda local
    frequency: str  # "daily", "weekly", "monthly", "yearly"
    is_hidden: bool  # Costo oculto vs visible


@dataclass
class SavingsReport:
    """Reporte de ahorros generados"""
    period: str
    total_saved: float
    categories: Dict[str, float]
    highlights: List[str]
    roi_percentage: float


class EconomicImpact:
    """
    Motor de análisis de impacto económico
    
    Mide:
    - Costos ocultos
    - Horas perdidas
    - Desgaste humano cuantificado
    - Ahorro real del sistema
    """
    
    # Costos base (ajustar por país/institución)
    COST_CONFIG = {
        "teacher_hourly_rate": 50.00,    # GTQ por hora
        "admin_hourly_rate": 40.00,
        "substitute_rate": 80.00,        # Más caro por urgencia
        "overtime_multiplier": 1.5,
        "meeting_cost_per_hour": 200.00,  # Costo de reunión (varios participantes)
        "printing_cost_per_page": 0.50,
        "turnover_cost_multiplier": 3,    # Meses de salario por rotación
    }
    
    def __init__(self, db_path: str = "schedules.db"):
        self.db_path = db_path
        self._init_tables()
    
    def _init_tables(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS cost_registry (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT,
                description TEXT,
                amount REAL,
                frequency TEXT,
                is_hidden INTEGER,
                recorded_by TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS savings_registry (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category TEXT,
                description TEXT,
                amount_saved REAL,
                method TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        c.execute('''
            CREATE TABLE IF NOT EXISTS time_tracking (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                activity_type TEXT,
                hours_before REAL,
                hours_after REAL,
                hours_saved REAL,
                period TEXT,
                recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        conn.commit()
        conn.close()
    
    # =========================================
    # COST IDENTIFICATION
    # =========================================
    
    def identify_hidden_costs(self, schedule_data: Dict = None) -> List[CostItem]:
        """Identificar costos ocultos en operación actual"""
        hidden_costs = []
        
        # Costo de horas extras por mala planificación
        hidden_costs.append(CostItem(
            category="overtime",
            description="Horas extras por replanificación de último momento",
            estimated_cost=self.COST_CONFIG["teacher_hourly_rate"] * 
                          self.COST_CONFIG["overtime_multiplier"] * 5,  # 5 horas/semana estimadas
            frequency="weekly",
            is_hidden=True
        ))
        
        # Costo de sustitutos por burnout
        hidden_costs.append(CostItem(
            category="substitutes",
            description="Maestros sustitutos por ausencias relacionadas a estrés",
            estimated_cost=self.COST_CONFIG["substitute_rate"] * 4,  # 4 horas/semana
            frequency="weekly",
            is_hidden=True
        ))
        
        # Costo de reuniones de coordinación
        hidden_costs.append(CostItem(
            category="meetings",
            description="Reuniones excesivas por falta de información centralizada",
            estimated_cost=self.COST_CONFIG["meeting_cost_per_hour"] * 2,  # 2 horas/semana
            frequency="weekly",
            is_hidden=True
        ))
        
        # Costo de errores manuales
        hidden_costs.append(CostItem(
            category="manual_errors",
            description="Corrección de errores en horarios manuales",
            estimated_cost=self.COST_CONFIG["admin_hourly_rate"] * 3,
            frequency="weekly",
            is_hidden=True
        ))
        
        # Costo de rotación de personal
        hidden_costs.append(CostItem(
            category="turnover",
            description="Rotación de personal por condiciones laborales",
            estimated_cost=self.COST_CONFIG["teacher_hourly_rate"] * 160 * 
                          self.COST_CONFIG["turnover_cost_multiplier"],  # 3 meses salario
            frequency="yearly",
            is_hidden=True
        ))
        
        return hidden_costs
    
    def calculate_total_hidden_cost(self, period: str = "monthly") -> float:
        """Calcular costo oculto total para un período"""
        costs = self.identify_hidden_costs()
        total = 0.0
        
        for cost in costs:
            if cost.frequency == "daily":
                if period == "monthly":
                    total += cost.estimated_cost * 22  # días laborales
                elif period == "yearly":
                    total += cost.estimated_cost * 260
            elif cost.frequency == "weekly":
                if period == "monthly":
                    total += cost.estimated_cost * 4
                elif period == "yearly":
                    total += cost.estimated_cost * 52
            elif cost.frequency == "monthly":
                if period == "yearly":
                    total += cost.estimated_cost * 12
                else:
                    total += cost.estimated_cost
            elif cost.frequency == "yearly":
                if period == "monthly":
                    total += cost.estimated_cost / 12
                else:
                    total += cost.estimated_cost
        
        return total
    
    # =========================================
    # SAVINGS CALCULATION
    # =========================================
    
    def calculate_ai_savings(self, usage_stats: Dict = None) -> SavingsReport:
        """Calcular ahorros generados por el sistema de IA"""
        categories = {}
        highlights = []
        
        # Ahorro en tiempo de planificación
        planning_hours_saved = 10  # Estimado por semana
        planning_savings = planning_hours_saved * self.COST_CONFIG["admin_hourly_rate"] * 4
        categories["planning"] = planning_savings
        highlights.append(f"Ahorro de {planning_hours_saved}h semanales en planificación")
        
        # Ahorro en reducción de conflictos
        conflict_reduction = 0.8  # 80% menos conflictos
        conflict_savings = self.COST_CONFIG["meeting_cost_per_hour"] * 4 * conflict_reduction * 4
        categories["conflicts"] = conflict_savings
        highlights.append(f"Reducción del {conflict_reduction*100:.0f}% en conflictos de horario")
        
        # Ahorro en prevención de burnout
        burnout_reduction = 0.3  # 30% menos ausencias por estrés
        burnout_savings = self.COST_CONFIG["substitute_rate"] * 16 * burnout_reduction * 4
        categories["burnout"] = burnout_savings
        highlights.append(f"Reducción del {burnout_reduction*100:.0f}% en ausencias por estrés")
        
        # Ahorro en errores evitados
        error_reduction = 0.9  # 90% menos errores
        error_savings = self.COST_CONFIG["admin_hourly_rate"] * 12 * error_reduction
        categories["errors"] = error_savings
        
        total = sum(categories.values())
        
        # Calcular ROI
        system_cost = 500  # Costo mensual estimado del sistema
        roi = ((total - system_cost) / system_cost) * 100 if system_cost > 0 else 0
        
        return SavingsReport(
            period="monthly",
            total_saved=total,
            categories=categories,
            highlights=highlights,
            roi_percentage=roi
        )
    
    def generate_executive_report(self) -> str:
        """Generar reporte ejecutivo de impacto económico"""
        hidden_costs = self.calculate_total_hidden_cost("monthly")
        savings = self.calculate_ai_savings()
        
        report = f"""
📊 **REPORTE DE IMPACTO ECONÓMICO**
===================================

💰 **Costos Ocultos Identificados (mensual):**
   GTQ {hidden_costs:,.2f}

💚 **Ahorros Generados por IA (mensual):**
   GTQ {savings.total_saved:,.2f}

📈 **ROI del Sistema:**
   {savings.roi_percentage:.1f}%

**Desglose de Ahorros:**
"""
        for category, amount in savings.categories.items():
            report += f"   • {category.title()}: GTQ {amount:,.2f}\n"
        
        report += f"""
**Highlights:**
"""
        for highlight in savings.highlights:
            report += f"   ✅ {highlight}\n"
        
        report += f"""
**Proyección Anual:**
   Ahorro estimado: GTQ {savings.total_saved * 12:,.2f}
"""
        
        return report
    
    def track_time_saved(self, activity: str, hours_before: float, 
                        hours_after: float, period: str = "monthly"):
        """Registrar tiempo ahorrado en una actividad"""
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        hours_saved = hours_before - hours_after
        
        c.execute('''
            INSERT INTO time_tracking 
            (activity_type, hours_before, hours_after, hours_saved, period)
            VALUES (?, ?, ?, ?, ?)
        ''', (activity, hours_before, hours_after, hours_saved, period))
        
        conn.commit()
        conn.close()
        
        return hours_saved


# Global instance
economic_impact = EconomicImpact()
