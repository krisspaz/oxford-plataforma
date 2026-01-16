
import sys
import os
import json
from unittest.mock import MagicMock

# Add path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

# Mock dependencies if environment issues arise, but let's try direct import
try:
    from main_fastapi import ask
    from learning_engine import learning_engine
    print("✅ Validated imports")
except ImportError as e:
    print(f"❌ Import Error: {e}")
    sys.exit(1)

# 1. Test Teaching (!aprende)
print("\n--- 1. Testing Training Command (!aprende) ---")
train_payload = {
    "prompt": "!aprende Cual es la clave del wifi? | Oxford_Secure_2026",
    "user_id": "test_admin",
    "user_role": "ROLE_ADMIN"
}
# Mock header
os.environ["AI_INTERNAL_KEY"] = "corposistemas_secret_key"
header_key = "corposistemas_secret_key"

try:
    response = ask(train_payload, x_internal_key=header_key)
    print(f"Response: {response['response']}")
    if "Entendido" in response['response'] and "Oxford_Secure_2026" in response['response']:
        print("✅ Training successful")
    else:
        print("❌ Training failed response")
except Exception as e:
    print(f"❌ Training Exception: {e}")

# 2. Test Recall
print("\n--- 2. Testing Recall (Dynamic Memory) ---")
ask_payload = {
    "prompt": "Cual es la clave del wifi?",
    "user_id": "student_1",
    "user_role": "ROLE_STUDENT"
}

try:
    response = ask(ask_payload, x_internal_key=header_key)
    print(f"Response: {response['response']}")
    if "Oxford_Secure_2026" in response['response']:
        print("✅ Recall successful (Learned Rule Applied)")
    else:
        print("❌ Recall failed - AI did not use learned rule")
except Exception as e:
    print(f"❌ Recall Exception: {e}")

# 3. Test Learning Log
print("\n--- 3. Verifying Interaction Log ---")
stats = learning_engine.get_learning_stats()
print(f"Total Interactions: {stats['total_interactions']}")
print(f"Active Rules: {stats['active_rules']}")

if stats['total_interactions'] >= 2 and stats['active_rules'] >= 1:
     print("✅ Logging verified")
else:
     print("❌ Logging metrics incorrect")
