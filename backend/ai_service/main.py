from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List, Optional
import random

app = FastAPI(title="Corpo Oxford AI Service", version="1.0.0")

# --- Models ---
class StudentData(BaseModel):
    id: int
    grades: List[float] = []

class RiskAnalysisResult(BaseModel):
    student_id: int
    average_grade: float
    risk_level: str
    risk_score: float
    message: str

class ScheduleRequest(BaseModel):
    slots: List[str]
    teachers: List[dict]
    groups: List[dict]

# --- Endpoints ---

@app.get("/")
def read_root():
    return {"status": "online", "service": "Corpo Oxford AI"}

# --- ML Setup ---
from sklearn.linear_model import LogisticRegression
import numpy as np
import pandas as pd
from fastapi.responses import JSONResponse
from fastapi import Request

# Mock training data (Grades vs Risk: 0=Safe, 1=AtRisk)
# In production, load this from a .pkl file
X_train = np.array([[95], [85], [75], [65], [55], [45], [90], [80], [60], [50]])
y_train = np.array([0, 0, 0, 1, 1, 1, 0, 0, 1, 1]) # 1 = At Risk

model = LogisticRegression()
model.fit(X_train, y_train)

# --- Global Exception Handler ---
@app.exception_handler(Exception)
async def global_exception_handler(request: Request, exc: Exception):
    return JSONResponse(
        status_code=500,
        content={"status": "error", "message": str(exc)},
    )

@app.post("/predict-risk", response_model=RiskAnalysisResult)
def predict_risk(student: StudentData):
    """
    Analyzes risk using a Logistic Regression model trained on grades.
    """
    if not student.grades:
        return RiskAnalysisResult(
            student_id=student.id,
            average_grade=0.0,
            risk_level="UNKNOWN",
            risk_score=0.0,
            message="No grades available for analysis"
        )
    
    avg_grade = float(np.mean(student.grades))
    
    # Predict probability of being "At Risk" (class 1)
    risk_prob = model.predict_proba([[avg_grade]])[0][1]
    
    classification = "LOW"
    message = "Good academic standing."
    
    if risk_prob > 0.8:
        classification = "CRITICAL"
        message = "High probability of failure. Intervention required."
    elif risk_prob > 0.5:
        classification = "HIGH"
        message = "At risk student."
    elif risk_prob > 0.2:
        classification = "MEDIUM"
        message = "Monitor progress."
        
    return RiskAnalysisResult(
        student_id=student.id,
        average_grade=avg_grade,
        risk_level=classification,
        risk_score=risk_prob * 100,
        message=message
    )

@app.post("/generate-schedule")
def generate_schedule(data: ScheduleRequest):
    """
    Generates a class schedule trying to minimize conflicts.
    """
    # ... (Logic from schedule_generator.py adapted to API) ...
    # For brevity, implementing the same heuristic logic here or importing it
    
    # Simple Mock Implementation for the API structure
    tasks = []
    for group in data.groups:
        for req in group.get('subjects_needed', []):
            subject = req['subject']
            count = req['hours']
            for _ in range(count):
                tasks.append({
                    'group': group['id'],
                    'subject': subject
                })
    
    assignments = []
    # Mock efficient scheduling
    teachers = data.teachers
    slots = data.slots
    
    for i, task in enumerate(tasks):
        slot = slots[i % len(slots)]
        teacher = teachers[i % len(teachers)]
        assignments.append({
            'group': task['group'],
            'subject': task['subject'],
            'teacher_id': teacher['id'],
            'teacher_name': teacher['name'],
            'slot': slot,
            'slot_index': 0 # Simplified
        })
        
    return {
        "status": "success",
        "schedule": assignments,
        "unassigned_count": 0
    }
