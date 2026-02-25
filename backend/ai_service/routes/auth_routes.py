from fastapi import APIRouter, HTTPException, Depends, Request
from pydantic import BaseModel
from werkzeug.security import generate_password_hash, check_password_hash
from database import db
from jwt_auth import verify_token
import jose.jwt as jwt
from datetime import datetime, timedelta
from config import config

auth_router = APIRouter()

class LoginRequest(BaseModel):
    username: str
    password: str

# --- UTILS ---
def verify_password(plain_password, hashed_password):
    return check_password_hash(hashed_password, plain_password)

def get_password_hash(password):
    return generate_password_hash(password)

def create_access_token(data: dict):
    to_encode = data.copy()
    expire = datetime.utcnow() + timedelta(minutes=60)
    to_encode.update({"exp": expire.timestamp()})
    return jwt.encode(to_encode, config.secret_key, algorithm=config.algorithm)

# Dependency to check auth
async def get_current_user(request: Request):
    auth_header = request.headers.get("Authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Missing Authorization Header")
    
    try:
        token = auth_header.split(" ")[1]
        payload = verify_token(token)
        return payload
    except Exception as e:
        raise HTTPException(status_code=401, detail="Invalid Token")

@auth_router.post("/login")
def login(creds: LoginRequest):
    # mysql-connector-python usa %s como placeholder, no '?'
    u = db.query(
        "SELECT * FROM users WHERE username = %s",
        (creds.username,),
        one=True
    )

    if not u or not verify_password(creds.password, u['hashed_password']):
        raise HTTPException(status_code=400, detail="Bad credentials")

    token = create_access_token({"sub": u['username'], "rol": u['role']})
    return {"access_token": token, "token_type": "bearer"}

@auth_router.get("/me")
def me(current_user: dict = Depends(get_current_user)):
    # current_user is the token payload
    username_from_token = current_user.get("sub") # 'sub' typically holds username/id

    u = db.query(
        "SELECT * FROM users WHERE username = %s",
        (username_from_token,),
        one=True
    )
    
    if u:
        return {"id": u['id'], "username": u['username'], "rol": u['role']}
    else:
        return current_user
