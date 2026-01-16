
import sys
import os

# Add current directory to path so we can import modules
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

try:
    from nlp_engine import nlp_engine
    from knowledge_base import knowledge_base
    print("✅ Modules imported successfully")
except ImportError as e:
    print(f"❌ Import Error: {e}")
    sys.exit(1)

def test_intent(text, expected_intent):
    result = nlp_engine.classify_intent(text)
    if result.name == expected_intent:
        print(f"✅ Intent '{expected_intent}' detected correctly for: '{text}' (Conf: {result.confidence})")
        return True
    else:
        print(f"❌ Failed: Expected '{expected_intent}', got '{result.name}' for: '{text}'")
        return False

def test_knowledge(query, expected_keyword):
    answer = knowledge_base.get_answer(query)
    if answer and expected_keyword.lower() in answer.lower():
         print(f"✅ Knowledge retrieval successful for: '{query}'")
         return True
    else:
         print(f"❌ Failed Knowledge: Query '{query}' returned: {answer[:50] if answer else 'None'}")
         return False

print("\n--- Testing New Intents ---")
test_intent("Quiero una cita con la directora", "request_meeting")
test_intent("Necesito reportar un caso de bullying", "report_issue")
test_intent("Cuanto debo de colegiatura", "financial_query")

print("\n--- Testing New Knowledge ---")
test_knowledge("protocolo bullying", "tolerancia cero")
test_knowledge("nota minima aprobar", "60 puntos")
test_knowledge("perdi mi carne", "Q25.00")

print("\n--- Testing Conversational AI (Spanish) ---")
test_intent("Muchas gracias por la ayuda", "small_talk")
test_intent("Eres un tonto e inutil", "insult_handling")
test_intent("Jajaja que risa", "small_talk")
test_intent("Quien eres tu?", "small_talk")

