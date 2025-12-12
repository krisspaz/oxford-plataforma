
import sys
import json
# import pandas as pd # Uncomment when installed
# from sklearn.linear_model import LinearRegression # Uncomment when installed
import random

def predict_risk(student_data):
    """
    Mock AI function to predict academic risk.
    In a real scenario, this would load a trained model (e.g. .pkl file)
    and run predictions based on grades, attendance, etc.
    """
    
    # Mock logic:
    # If average grade is below 65, HIGH risk.
    # If average grade is below 75, MEDIUM risk.
    # Else LOW risk.
    
    grades = student_data.get('grades', [])
    if not grades:
        return {"risk_level": "UNKNOWN", "score": 0.0, "message": "No grades data available"}
    
    avg_grade = sum(grades) / len(grades)
    
    risk_level = "LOW"
    message = "Good academic standing."
    
    if avg_grade < 60:
        risk_level = "CRITICAL"
        message = "High probability of failing. Immediate intervention required."
    elif avg_grade < 70:
        risk_level = "HIGH"
        message = "At risk. Needs support."
    elif avg_grade < 80:
        risk_level = "MEDIUM"
        message = "Monitoring recommended."
        
    return {
        "student_id": student_data.get('id'),
        "average_grade": avg_grade,
        "risk_level": risk_level,
        "risk_score": 100 - avg_grade, # Higher score = higher risk
        "message": message
    }

if __name__ == "__main__":
    # Expecting JSON input from stdin or argument
    try:
        if len(sys.argv) > 1:
            input_json = sys.argv[1]
            data = json.loads(input_json)
            result = predict_risk(data)
            print(json.dumps(result))
        else:
            print(json.dumps({"error": "No input data provided"}))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
