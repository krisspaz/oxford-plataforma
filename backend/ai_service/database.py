import sqlite3
import json
from datetime import datetime, date, time
from typing import Any, List, Dict, Optional
from werkzeug.security import generate_password_hash

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

    def seed_data(self):
        """Seed initial data if database is empty"""
        self.init_db()
        
        # Check if users exist
        if not self.query("SELECT 1 FROM users LIMIT 1", one=True):
            print("Seeding Users...")
            users = [
                ("admin", "admin123", "administrador"),
                ("maestro1", "pass1", "maestro"),
                ("alumno1", "pass2", "alumno")
            ]
            for u, p, r in users:
                self.execute("INSERT INTO users (username, hashed_password, role) VALUES (?, ?, ?)", 
                           (u, generate_password_hash(p), r))

            print("Seeding References...")
            # Grades & Subjects
            schema = {
                "pre_primaria": ["matematicas", "lectura", "arte", "musica"],
                "primaria": ["matematicas", "ciencias", "historia", "lengua"],
                "secundaria": ["matematicas", "quimica", "fisica", "historia", "lengua"]
            }
            for g_name, subs in schema.items():
                g_id = self.execute("INSERT INTO grades (name, level) VALUES (?, ?)", (g_name, g_name.title()))
                for s in subs:
                    self.execute("INSERT INTO subjects (name, grade_id) VALUES (?, ?)", (s.title(), g_id))
            
            # Teachers
            teachers = [
                ("Ana", ["matematicas", "ciencias"], ["primaria", "secundaria"]),
                ("Luis", ["arte", "musica"], ["pre_primaria", "primaria"]),
                ("Carla", ["quimica"], ["secundaria"])
            ]
            for name, mats, lvls in teachers:
                self.execute("INSERT INTO teachers (nombre, materias_json, niveles_json) VALUES (?, ?, ?)",
                           (name, json.dumps(mats), json.dumps(lvls)))
            print("Seed Complete.")

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

    # ... (existing methods remain)

    def execute(self, sql: str, params: tuple = ()):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(sql, params)
        conn.commit()
        last_id = cursor.lastrowid
        conn.close()
        return last_id

    # === MongoDB Integration ===
    def get_mongo_client(self):
        """Returns a PyMongo client"""
        from pymongo import MongoClient
        import os
        # Mongo Connection String from Env or Default
        mongo_uri = os.getenv("MONGO_URI", "mongodb://root:password@mongodb:27017/")
        return MongoClient(mongo_uri)

    def log_interaction(self, input_data: dict, output_data: dict, metadata: dict = None):
        """Logs AI interaction to MongoDB"""
        try:
            client = self.get_mongo_client()
            db = client["oxford_ai_logs"]
            collection = db["interactions"]
            
            log_entry = {
                "timestamp": datetime.now(),
                "input": input_data,
                "output": output_data,
                "metadata": metadata or {}
            }
            
            result = collection.insert_one(log_entry)
            client.close()
            return str(result.inserted_id)
        except Exception as e:
            print(f"MongoDB Error: {e}")
            return None

db = DatabaseManager()
