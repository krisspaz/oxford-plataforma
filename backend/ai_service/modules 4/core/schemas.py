from pydantic import BaseModel
from typing import List, Optional, Any
from datetime import date, time, datetime

class UserBase(BaseModel):
    username: str
    rol: str

class UserCreate(UserBase):
    password: str



class UserResponse(UserBase):
    id: int
    class Config:
        from_attributes = True

class Token(BaseModel):
    access_token: str
    token_type: str

class ScheduleBase(BaseModel):
    fecha: Optional[date] = None
    dia_semana: str
    hora_inicio: str # accept string "HH:MM"
    hora_fin: str
    lugar: Optional[str] = None
    grado_id: int
    materia_id: int
    teacher_id: int
    participantes: List[str] = []

class ScheduleCreate(ScheduleBase):
    pass

class ScheduleResponse(BaseModel):
    id: int
    fecha: Optional[date]
    dia_semana: str
    hora_inicio: time
    hora_fin: time
    lugar: Optional[str]
    grade_name: str
    subject_name: str
    teacher_name: str
    
    class Config:
        from_attributes = True

class ConflictCheck(BaseModel):
    conflict: bool
    message: str
    details: Optional[Any] = None

class NLPCommand(BaseModel):
    text: str
    
class NLPResponse(BaseModel):
    intent: str
    fecha: str
    hora_inicio: str
    hora_fin: str
    lugar: str
    grado: str
    materia: str
    maestro: str
    participantes: List[str]
    accion: str
    mensaje_usuario: str
