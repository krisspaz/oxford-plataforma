import random
import numpy as np
from typing import List, Dict, Any, Tuple
from copy import deepcopy
import logging

try:
    from deap import base, creator, tools, algorithms
    DEAP_AVAILABLE = True
except ImportError:
    DEAP_AVAILABLE = False

class GeneticScheduleOptimizer:
    """
    Level 3: Advanced Schedule Generation using Genetic Algorithms (DEAP)
    """
    
    def __init__(self, config: Dict[str, Any]):
        self.config = config
        self.logger = logging.getLogger(__name__)
        self.days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']
        self.slots_per_day = 8 # Default
        
        if DEAP_AVAILABLE:
            self._setup_deap()
            
    def _setup_deap(self):
        """Initialize DEAP Types and Toolbox"""
        # Minimize conflicts (weight -1.0)
        if hasattr(creator, "FitnessMin"):
            del creator.FitnessMin
        if hasattr(creator, "Individual"):
            del creator.Individual
            
        creator.create("FitnessMin", base.Fitness, weights=(-1.0,))
        creator.create("Individual", list, fitness=creator.FitnessMin)
        
        self.toolbox = base.Toolbox()
        
    def generate(
        self, 
        grades: List[str], 
        subjects: List[str], 
        teachers: List[Dict[str, Any]],
        constraints: List[Dict[str, Any]] = [],
        population_size: int = 50,
        generations: int = 30
    ) -> Dict[str, Any]:
        """
        Run the Genetic Algorithm to produce an optimized schedule
        """
        if not DEAP_AVAILABLE:
            return {"error": "DEAP library not installed"}

        self.grades = grades
        self.subjects = subjects
        self.teachers = teachers
        self.constraints = constraints
        
        # 1. Attribute Generator: Random Assignment of (Subject, Teacher) for a slot
        def random_slot():
            subj = random.choice(self.subjects)
            # Find teacher capable of this subject
            capable_teachers = [t for t in self.teachers if subj in t['subjects']]
            teacher = random.choice(capable_teachers)['name'] if capable_teachers else "TBD"
            return (subj, teacher)

        # 2. Individual Generator: A flat list of slots. 
        # Size = Grades * Days * SlotsPerDay
        genome_size = len(grades) * len(self.days) * self.slots_per_day
        
        self.toolbox.register("attr_slot", random_slot)
        self.toolbox.register("individual", tools.initRepeat, creator.Individual, self.toolbox.attr_slot, n=genome_size)
        self.toolbox.register("population", tools.initRepeat, list, self.toolbox.individual)
        
        # 3. Operations
        self.toolbox.register("evaluate", self._evaluate_fitness)
        self.toolbox.register("mate", tools.cxTwoPoint)
        self.toolbox.register("mutate", tools.mutShuffleIndexes, indpb=0.05)
        self.toolbox.register("select", tools.selTournament, tournsize=3)
        
        # 4. Run Evolution
        pop = self.toolbox.population(n=population_size)
        stats = tools.Statistics(lambda ind: ind.fitness.values)
        stats.register("avg", np.mean)
        stats.register("min", np.min)
        
        pop, logbook = algorithms.eaSimple(
            pop, 
            self.toolbox, 
            cxpb=0.7, 
            mutpb=0.2, 
            ngen=generations, 
            stats=stats,
            verbose=False # Keep logs clean
        )
        
        # 5. Decode Best Solution
        best_ind = tools.selBest(pop, 1)[0]
        schedule = self._decode_genome(best_ind)
        
        return {
            "schedule": schedule,
            "conflicts": best_ind.fitness.values[0],
            "generations": generations,
            "log": logbook
        }

    def _evaluate_fitness(self, individual):
        """Calculate fitness (number of conflicts + constraint penalties)"""
        structure = self._decode_genome(individual)
        conflicts = 0.0
        
        # Constraints
        # 1. Teacher Double Booking: Same teacher, Same Day/Slot, Different Grade
        teacher_slots = {} # Key: (Teacher, Day, Slot) -> Counter
        
        for grade_data in structure.values():
            for day, day_slots in grade_data.items():
                for slot_idx, assignment in enumerate(day_slots):
                    teacher = assignment['teacher']
                    if teacher and teacher != "TBD":
                        key = (teacher, day, slot_idx)
                        if key in teacher_slots:
                            conflicts += 10.0 # HARD Constraint
                        teacher_slots[key] = True
                        
                        # 3. User Defined Constraints (Priorities/Avoidances)
                        # Format: {'type': 'avoid_time', 'teacher': 'Juan', 'day': 'Viernes', 'weight': 50}
                        for constraint in self.constraints:
                            ctype = constraint.get('type')
                            weight = constraint.get('weight', 10.0)
                            
                            if ctype == 'avoid_day' and constraint.get('teacher') == teacher:
                                if day == constraint.get('day'):
                                    conflicts += weight
                                    
                            if ctype == 'prefer_day' and constraint.get('teacher') == teacher:
                                if day != constraint.get('day'):
                                     conflicts += (weight * 0.5) # Soft penalty if not on preferred day

        # 2. Subject Distribution (Soft)
        for grade_data in structure.values():
            for day, day_slots in grade_data.items():
                daily_subjects = [s['subject'] for s in day_slots]
                from collections import Counter
                counts = Counter(daily_subjects)
                for subj, count in counts.items():
                    if count > 2:
                        conflicts += 1.0
                        
        return (conflicts,)

    def _decode_genome(self, genome) -> Dict[str, Dict]:
        """Convert linear genome back to structured schedule"""
        schedule = {}
        idx = 0
        for grade in self.grades:
            schedule[grade] = {}
            for day in self.days:
                schedule[grade][day] = []
                for _ in range(self.slots_per_day):
                    subj, teacher = genome[idx]
                    schedule[grade][day].append({
                        "subject": subj,
                        "teacher": teacher
                    })
                    idx += 1
        return schedule

# Global Instance
genetic_optimizer = GeneticScheduleOptimizer({})
