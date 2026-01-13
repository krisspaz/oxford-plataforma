import os
from fastapi import FastAPI, Header, HTTPException
from pydantic import BaseModel

app = FastAPI()

class AskPayload(BaseModel):
    prompt: str

def check_key(x_internal_key: str = Header(...)):
    if x_internal_key != os.getenv("AI_INTERNAL_KEY"):
        raise HTTPException(status_code=403, detail="Forbidden")

@app.get("/health")
def health():
    return {"ok": True}

@app.post("/ask")
def ask(data: dict, x_internal_key: str = Header(..., alias="X-INTERNAL-KEY")):
    check_key(x_internal_key)
    
    prompt = data.get("prompt", "")
    
    # RAG Retrieval
    import chromadb
    from sentence_transformers import SentenceTransformer
    
    context_text = "No relevant documents found."
    try:
        chroma_client = chromadb.PersistentClient(path="./chroma_db")
        collection = chroma_client.get_collection(name="knowledge_base")
        model = SentenceTransformer('all-MiniLM-L6-v2')
        
        query_embedding = model.encode([prompt]).tolist()
        results = collection.query(
            query_embeddings=query_embedding,
            n_results=3
        )
        
        if results['documents']:
            context_text = "\n\n".join(results['documents'][0])
    except Exception as e:
        context_text = f"Error retrieving context: {str(e)}"

    # Construct Response (Simulated LLM)
    final_response = f"Here is the information I found in your documents:\n\n{context_text}\n\n(Generated based on: '{prompt}')"
    
    response_data = {"response": final_response, "original_input": data}
    
    # Log to MongoDB
    from database import db
    db.log_interaction(
        input_data=data, 
        output_data=response_data, 
        metadata={"source": "rag_fastapi", "retrieved_context": context_text}
    )
    
    return response_data
