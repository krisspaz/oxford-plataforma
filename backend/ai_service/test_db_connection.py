from database import db
import sys

try:
    print("Testing connection to PostgreSQL...")
    grades = db.get_grades()
    print(f"Successfully connected! Found {len(grades)} grades.")
    for g in grades:
        print(f"- {g['name']}")
    sys.exit(0)
except Exception as e:
    print(f"Connection failed: {e}")
    sys.exit(1)
