iremport sqlite3
import json
from datetime import datetime, date, time
from typing import Any, List, Dict, Optional

DB_NAME = "schedules.db"

class DatabaseManager:
    def __init__(self, db_name=DB_NAME):
        self.db_name = db_name

    def get_connection(self):
        conn = sqlite3.connect(self.db_name)
        conn.row_factory = sqlite3.Row
        return conn

    def init_db(self):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        # User Table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                hashed_password TEXT NOT NULL,
                role TEXT NOT NULL
            )
        ''')
        
        # Grades
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS grades (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT UNIQUE NOT NULL,
                level TEXT NOT NULL
            )
        ''')
        
        # Subjects
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS subjects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                grade_id INTEGER,
                FOREIGN KEY (grade_id) REFERENCES grades (id)
            )
        ''')
        
        # Teachers
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS teachers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT UNIQUE NOT NULL,
                materias_json TEXT, -- JSON List
                niveles_json TEXT   -- JSON List
            )
        ''')
        
        # Schedules
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS schedules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                fecha DATE,
                dia_semana TEXT NOT NULL,
                hora_inicio TEXT NOT NULL,
                hora_fin TEXT NOT NULL,
                lugar TEXT,
                grade_id INTEGER,
                subject_id INTEGER,
                teacher_id INTEGER,
                participantes_json TEXT,
                FOREIGN KEY (grade_id) REFERENCES grades (id),
                FOREIGN KEY (subject_id) REFERENCES subjects (id),
                FOREIGN KEY (teacher_id) REFERENCES teachers (id)
            )
        ''')
        
        conn.commit()
        conn.close()

    def query(self, sql: str, params: tuple = (), one: bool = False):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(sql, params)
        rv = cursor.fetchall()
        conn.close()
        
        # Convert Rows to Dicts
        if not rv: return None if one else []
        
        results = [dict(row) for row in rv]
        return results[0] if one else results

    def execute(self, sql: str, params: tuple = ()):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(sql, params)
        conn.commit()
        last_id = cursor.lastrowid
        conn.close()
        return last_id

db = DatabaseManager()
