
import sys
import os
import json

# Add path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

# Auth
os.environ["AI_INTERNAL_KEY"] = "corposistemas_secret_key"
header_key = "corposistemas_secret_key"

try:
    from main_fastapi import ask
    print("✅ Logic imports success")
except ImportError as e:
    print(f"❌ Import Error: {e}")
    sys.exit(1)

def test_prompt(prompt, expected_lang, expected_keyword):
    print(f"\nTesting: '{prompt}'")
    payload = {
        "prompt": prompt,
        "user_id": "test_bi",
        "user_role": "ROLE_ADMIN"
    }
    try:
        response = ask(payload, x_internal_key=header_key)
        res_text = response['response']
        detected_lang = response.get('lang', 'unknown')
        
        print(f"  Detected Lang: {detected_lang}")
        print(f"  Response: {res_text}")
        
        if detected_lang != expected_lang:
            print(f"  ❌ Wrong language detected. Expected {expected_lang}, got {detected_lang}")
            return False
            
        if expected_keyword.lower() not in res_text.lower():
             print(f"  ❌ Response missing expected keyword '{expected_keyword}'")
             return False
             
        print("  ✅ Success")
        return True
    except Exception as e:
        import traceback
        traceback.print_exc()
        print(f"  ❌ Exception: {e}")
        return False

# Test Cases
tests = [
    ("Hola, como estas?", "es", "Hola"),
    ("Hello, who are you?", "en", "Oxford"),
    ("Genera horario para 1ro primaria", "es", "exitosamente"),
    ("Generate schedule for 1st grade", "en", "successfully"),
    ("Ayuda por favor", "es", "Comandos"),
    ("Help me please", "en", "Commands")
]

passed = 0
for t in tests:
    if test_prompt(t[0], t[1], t[2]):
        passed += 1

print(f"\nPassed {passed}/{len(tests)} tests.")
if passed == len(tests):
    sys.exit(0)
else:
    sys.exit(1)
