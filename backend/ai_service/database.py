import os
import json
import psycopg2
from psycopg2.extras import RealDictCursor
from datetime import datetime, date, time
from typing import Any, List, Dict, Optional
from werkzeug.security import generate_password_hash
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

class DatabaseManager:
    def __init__(self):
        self.db_url = os.getenv("DATABASE_URL")
        if not self.db_url:
            raise ValueError("DATABASE_URL is not set in environment variables")

    def get_connection(self):
        """Establish a connection to the PostgreSQL database."""
        conn = psycopg2.connect(self.db_url)
        return conn

    def init_db(self):
        """
        In PostgreSQL, we assume the schema is managed by Symfony/Doctrine Migrations.
        This method is kept for compatibility but should logically do nothing 
        or only create AI-specific logs if needed.
        """
        # We generally DON'T want the AI service creating tables in the main DB
        # unless they are strictly for AI internal use (like logs).
        pass

    def seed_data(self):
        """
        Skipping seed data as the main application manages data.
        """
        print("Skipping seed data: Managed by main Symfony application.")

    def query(self, sql: str, params: tuple = (), one: bool = False):
        """Execute a SELECT query."""
        conn = None
        try:
            conn = self.get_connection()
            with conn.cursor(cursor_factory=RealDictCursor) as cursor:
                cursor.execute(sql, params)
                rv = cursor.fetchall()
                
                # Convert RealDictRow to standard dict
                results = [dict(row) for row in rv]
                
                if not results:
                    return None if one else []
                
                return results[0] if one else results
        except Exception as e:
            print(f"Database Query Error: {e}")
            return None if one else []
        finally:
            if conn:
                conn.close()

    def execute(self, sql: str, params: tuple = ()):
        """Execute an INSERT/UPDATE/DELETE query."""
        conn = None
        try:
            conn = self.get_connection()
            with conn.cursor() as cursor:
                cursor.execute(sql, params)
                conn.commit()
                # PostgreSQL specific: return last inserted ID if RETURNING id is used
                if "RETURNING id" in sql.lower():
                    return cursor.fetchone()[0]
                return cursor.rowcount
        except Exception as e:
            print(f"Database Execute Error: {e}")
            if conn:
                conn.rollback()
            return None
        finally:
            if conn:
                conn.close()

    # === AI Specific Logic Adapters ===

    def get_teachers(self):
        return self.query("SELECT * FROM teacher WHERE is_active = true")

    def get_subjects(self):
        return self.query("SELECT * FROM subject")
    
    def get_grades(self):
         return self.query("SELECT * FROM grade WHERE is_active = true")

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
