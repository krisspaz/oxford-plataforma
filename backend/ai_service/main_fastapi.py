import os
from fastapi import FastAPI, Header, HTTPException
from pydantic import BaseModel

app = FastAPI()

class AskPayload(BaseModel):
    prompt: str

def check_key(x_internal_key: str = Header(...)):
    if x_internal_key != os.getenv("AI_INTERNAL_KEY"):
        raise HTTPException(status_code=403, detail="Forbidden")

@app.on_event("startup")
def on_startup():
    from database import db
    try:
        db.init_db()
        print("✅ Tables initialized in MySQL")
    except Exception as e:
        print(f"❌ Error initializing DB: {e}")

@app.get("/health")
def health():
    return {"ok": True}

@app.post("/ask")
def ask(data: dict, x_internal_key: str = Header(..., alias="X-INTERNAL-KEY")):
    check_key(x_internal_key)
    
    prompt = data.get("prompt", "")
    user_id = data.get("user_id", "unknown_user")
    user_role = data.get("user_role", "ROLE_STUDENT")
    
    # Imports
    from nlp_engine import nlp_engine
    from knowledge_base import knowledge_base
    from response_generator import response_generator
    from learning_engine import learning_engine
    from risk_analyzer import RiskAnalyzer
    
    # === 1. CHECK FOR TEACHING COMMAND (!aprende) ===
    if prompt.lower().startswith("!aprende"):
        try:
            # Format: !aprende Q | A
            _, content = prompt.split("!aprende", 1)
            parts = content.split("|")
            if len(parts) == 2:
                q = parts[0].strip()
                a = parts[1].strip()
                
                # Store as rule
                rule_id = learning_engine.add_rule(
                    description=q,
                    condition="query_match",
                    action=a,
                    source=f"user instruction ({user_role})"
                )
                
                response_text = f"✅ ¡Entendido! He aprendido que cuando pregunten '**{q}**', debo responder '**{a}**'."
                
                # Log this training event
                learning_engine.log_interaction(user_id, user_role, prompt, response_text, "training")
                
                return {"response": response_text, "original_input": data}
            else:
                return {"response": "⚠️ Formato incorrecto. Usa: !aprende Pregunta | Respuesta", "original_input": data}
        except Exception as e:
            return {"response": f"❌ Error al aprender: {str(e)}", "original_input": data}

    # === 2. STANDARD NLP PROCESSING ===
    # A. Detect Language
    lang = nlp_engine.detect_language(prompt)
    
    # B. Intent Classification
    nlp_result = nlp_engine.process(prompt)
    intent = nlp_result.intent.name
    entities = nlp_result.entities
    
    # C. Knowledge Base Retrieval (Static + Learned)
    kb_context = {}
    kb_answer = knowledge_base.get_answer(prompt)
    
    # Priority Logic: KB only overrides NLP if confidence is low
    if kb_answer and nlp_result.intent.confidence < 0.8:
        response_text = kb_answer
        intent = "knowledge_retrieval"
    else:
        # D. Generate Response based on Intent
        context = {"config": {"startTime": "07:30", "endTime": "13:30"}} # Dummy context for now
        response_obj = response_generator.generate(
            intent=intent, 
            context=context, 
            entities=entities, 
            success=True, 
            lang=lang
        )
        response_text = response_obj['message']

    # === 3. LOG INTERACTION (SELF-LEARNING) ===
    learning_engine.log_interaction(
        user_id=user_id,
        user_role=user_role,
        question=prompt,
        response=response_text,
        intent=intent,
        metadata={"lang": lang}
    )
    
    return {"response": response_text, "intent": intent, "lang": lang, "original_input": data}

@app.post("/risk-analysis")
def risk_analysis(data: dict, x_internal_key: str = Header(..., alias="X-INTERNAL-KEY")):
    check_key(x_internal_key)

    analyzer = RiskAnalyzer()
    
    # Analyze
    result = analyzer.analyze(data)
    
    return result
