import sqlite3
import json
from collections import defaultdict

class PreferenceLearner:
    def __init__(self, db_path="schedules.db"):
        self.db_path = db_path
        self._init_db()
        
    def _init_db(self):
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        # Table to store learned weights
        # entity_type: 'teacher', 'grade'
        # entity_id: ID
        # preference_type: 'day_time', 'avoid_day'
        # value: 'Monday', '08:00', etc.
        # weight: float (positive = preferred, negative = avoided)
        c.execute('''
            CREATE TABLE IF NOT EXISTS learned_preferences (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_type TEXT,
                entity_id INTEGER,
                preference_type TEXT,
                value TEXT,
                weight REAL DEFAULT 0.0,
                confidence REAL DEFAULT 0.1,
                last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(entity_type, entity_id, preference_type, value)
            )
        ''')
        # Table to log historical changes for auditing
        c.execute('''
            CREATE TABLE IF NOT EXISTS change_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                description TEXT,
                original_json TEXT,
                new_json TEXT,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        conn.commit()
        conn.close()

    def learn_from_move(self, teacher_id, day_from, day_to, reason="manual_move"):
        """
        User moved a class from day_from to day_to.
        Inference: 
        - Likes day_to (+ weight)
        - Dislikes day_from (- weight)
        """
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        # 1. Penny for your thoughts: Dislike the old day
        self._update_weight(c, 'teacher', teacher_id, 'day_preference', day_from, -5.0)
        
        # 2. Reward the new day
        self._update_weight(c, 'teacher', teacher_id, 'day_preference', day_to, 5.0)
        
        # Log it
        c.execute("INSERT INTO change_history (description, original_json, new_json) VALUES (?, ?, ?)",
                  (f"Teacher {teacher_id} moved from {day_from} to {day_to}", 
                   json.dumps({'day': day_from}), 
                   json.dumps({'day': day_to})))
        
        conn.commit()
        conn.close()
        return True

    def _update_weight(self, cursor, etype, eid, ptype, val, delta):
        # Check if exists
        cursor.execute('''
            SELECT weight, confidence FROM learned_preferences 
            WHERE entity_type=? AND entity_id=? AND preference_type=? AND value=?
        ''', (etype, eid, ptype, val))
        row = cursor.fetchone()
        
        if row:
            new_weight = row[0] + delta
            # Cap confidence at 1.0, increment by 0.1 per observation
            new_conf = min(1.0, row[1] + 0.1) 
            cursor.execute('''
                UPDATE learned_preferences 
                SET weight=?, confidence=?, last_updated=CURRENT_TIMESTAMP 
                WHERE entity_type=? AND entity_id=? AND preference_type=? AND value=?
            ''', (new_weight, new_conf, etype, eid, ptype, val))
        else:
            cursor.execute('''
                INSERT INTO learned_preferences (entity_type, entity_id, preference_type, value, weight, confidence) 
                VALUES (?, ?, ?, ?, ?, ?)
            ''', (etype, eid, ptype, val, delta, 0.2))

    def get_teacher_constraints(self, teacher_id):
        """
        Return list of constraints suitable for GeneticScheduler based on learned weights
        """
        conn = sqlite3.connect(self.db_path)
        c = conn.cursor()
        
        constraints = []
        c.execute('''
            SELECT value, weight, confidence FROM learned_preferences 
            WHERE entity_type='teacher' AND entity_id=? AND preference_type='day_preference'
        ''', (teacher_id,))
        
        for row in c.fetchall():
            day, weight, conf = row
            # Only consider if confidence is sufficient (e.g., > 0.3) or weight is significant
            if abs(weight) > 10:
                if weight < 0:
                    constraints.append({
                        'type': 'avoid_day',
                        'teacher': str(teacher_id), # Scheduler expects name? careful with ID vs Name mapping. 
                        # Ideally Scheduler should work with IDs now or we need a Mapper.
                        # For Phase 2, let's pass the 'value' which is the Day Name (e.g. 'Monday')
                        'day': day,
                        'weight': abs(weight)
                    })
                else:
                    constraints.append({
                        'type': 'prefer_day',
                        'teacher': str(teacher_id),
                        'day': day,
                        'weight': weight
                    })
                    
        conn.close()
        return constraints
