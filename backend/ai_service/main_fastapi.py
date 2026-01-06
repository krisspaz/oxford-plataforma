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
    # Mock response for now or call actual AI logic
    return {"response": "IA funcionando", "input": data}
