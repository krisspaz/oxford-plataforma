import os
import json
import mysql.connector
from datetime import datetime, date, time
from typing import Any, List, Dict, Optional
from werkzeug.security import generate_password_hash
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

class DatabaseManager:
    def __init__(self):
        # We can reuse existing env vars or look for MYSQL specific ones
        # docker-compose uses MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE
        # Config code likely reads DB_USER, etc.
        # Let's support both or rely on the container variables passed.
        
        self.db_host = os.getenv("MYSQL_HOST") or os.getenv("DB_HOST", "database")
        self.db_user = os.getenv("MYSQL_USER") or os.getenv("DB_USER", "oxford_user")
        self.db_password = os.getenv("MYSQL_PASSWORD") or os.getenv("DB_PASSWORD", "oxford2026")
        self.db_name = os.getenv("MYSQL_DATABASE") or os.getenv("DB_NAME", "oxford_db")
        self.db_port = int(os.getenv("MYSQL_PORT") or os.getenv("DB_PORT", "3306"))

    def get_connection(self):
        """Establish a connection to the MySQL database."""
        return mysql.connector.connect(
            host=self.db_host,
            user=self.db_user,
            password=self.db_password,
            database=self.db_name,
            port=self.db_port
        )

    def init_db(self):
        """
        Create necessary tables for AI Service in MySQL (replacing Mongo/SQLite).
        """
        queries = [
            # 1. ai_interactions
            """
            CREATE TABLE IF NOT EXISTS ai_interactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(255) NOT NULL,
                user_role VARCHAR(50) NOT NULL,
                question TEXT,
                response TEXT,
                intent VARCHAR(100),
                context_snapshot JSON,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                input_data JSON, -- Legacy compatibility
                output_data JSON, -- Legacy compatibility
                metadata JSON -- Legacy compatibility
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            """,
            # 2. ai_feedback
            """
            CREATE TABLE IF NOT EXISTS ai_feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                interaction_id INT NOT NULL,
                action VARCHAR(50) NOT NULL,
                reason VARCHAR(50),
                user_correction TEXT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (interaction_id) REFERENCES ai_interactions(id) ON DELETE CASCADE
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            """,
            # 3. ai_improvements
            """
            CREATE TABLE IF NOT EXISTS ai_improvements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                source_interaction_id INT,
                problem_detected TEXT NOT NULL,
                proposed_solution TEXT NOT NULL,
                improvement_type VARCHAR(50) NOT NULL,
                priority VARCHAR(50) NOT NULL,
                confidence FLOAT,
                requires_approval BOOLEAN DEFAULT TRUE,
                status VARCHAR(50) DEFAULT 'pending',
                approved_by VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                applied_at DATETIME
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            """,
            # 4. ai_rules
            """
            CREATE TABLE IF NOT EXISTS ai_rules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                rule_description TEXT NOT NULL,
                rule_condition TEXT,
                rule_action TEXT,
                source VARCHAR(255) NOT NULL,
                priority VARCHAR(50) DEFAULT 'medium',
                active BOOLEAN DEFAULT TRUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            """,
            # 5. ai_patterns
            """
            CREATE TABLE IF NOT EXISTS ai_patterns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pattern_type VARCHAR(50) NOT NULL,
                pattern_key VARCHAR(255) NOT NULL,
                occurrence_count INT DEFAULT 1,
                last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_pattern (pattern_type, pattern_key)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            """
        ]
        
        for q in queries:
            self.execute(q)
            
        print("AI Service: Verified all MySQL tables.")

    def seed_data(self):
        """skip"""
        pass

    def query(self, sql: str, params: tuple = (), one: bool = False):
        """Execute a SELECT query."""
        conn = None
        try:
            conn = self.get_connection()
            # MySQL Connector uses dictionary=True for dict cursor
            with conn.cursor(dictionary=True) as cursor:
                cursor.execute(sql, params)
                rv = cursor.fetchall()
                
                if not rv:
                    return None if one else []
                
                return rv[0] if one else rv
        except Exception as e:
            print(f"Database Query Error: {e}")
            return None if one else []
        finally:
            if conn and conn.is_connected():
                conn.close()

    def execute(self, sql: str, params: tuple = ()):
        """Execute an INSERT/UPDATE/DELETE query."""
        conn = None
        try:
            conn = self.get_connection()
            with conn.cursor() as cursor:
                cursor.execute(sql, params)
                conn.commit()
                # Return lastrowid for INSERT
                if "insert" in sql.lower():
                    return cursor.lastrowid
                return cursor.rowcount
        except Exception as e:
            print(f"Database Execute Error: {e}")
            return None
        finally:
            if conn and conn.is_connected():
                conn.close()

    # === AI Specific Logic Adapters ===

    def get_teachers(self):
        return self.query("SELECT * FROM teacher WHERE is_active = 1") # MySQL uses 1/0 for bool

    def get_subjects(self):
        return self.query("SELECT * FROM subject")
    
    def get_grades(self):
         return self.query("SELECT * FROM grade WHERE is_active = 1")

    # === MongoDB Integration -> Migrated to MySQL ===
    
    def log_interaction(self, input_data: dict, output_data: dict, metadata: dict = None):
        """Logs AI interaction to MySQL table 'ai_interactions'"""
        try:
            sql = """
            INSERT INTO ai_interactions (timestamp, input_data, output_data, metadata)
            VALUES (%s, %s, %s, %s)
            """
            
            # Serialize JSON
            inp_json = json.dumps(input_data)
            out_json = json.dumps(output_data)
            meta_json = json.dumps(metadata or {})
            
            timestamp = datetime.now()
            
            # Param tuple
            params = (timestamp, inp_json, out_json, meta_json)
            
            inserted_id = self.execute(sql, params)
            return str(inserted_id)
        except Exception as e:
            print(f"Logging Error: {e}")
            return None

db = DatabaseManager()
# Auto-init logs table on module load (or call explicit in main)
# db.init_db() 
