from sqlalchemy import Column, Integer, String, ForeignKey, Date, Time, Boolean, JSON, DateTime
from sqlalchemy.orm import relationship
from database import Base
from datetime import datetime

class User(Base):
    __tablename__ = "users"
    id = Column(Integer, primary_key=True, index=True)
    username = Column(String, unique=True, index=True)
    hashed_password = Column(String)
    role = Column(String) # administrador, maestro, alumno

class Grade(Base):
    __tablename__ = "grades"
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, unique=True) # e.g. "pre_primaria", "primaria"
    level = Column(String) # e.g. "Pre-Primaria"

class Subject(Base):
    __tablename__ = "subjects"
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String)
    grade_id = Column(Integer, ForeignKey("grades.id"))
    
    grade = relationship("Grade")

class Teacher(Base):
    __tablename__ = "teachers"
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String, unique=True)
    materias_json = Column(JSON) # List of strings
    niveles_json = Column(JSON) # List of strings

class Schedule(Base):
    __tablename__ = "schedules"
    id = Column(Integer, primary_key=True, index=True)
    fecha = Column(Date, nullable=True) # Optional for weekly recurring
    dia_semana = Column(String) # Lunes, Martes...
    hora_inicio = Column(Time)
    hora_fin = Column(Time)
    lugar = Column(String, nullable=True)
    
    grade_id = Column(Integer, ForeignKey("grades.id"))
    subject_id = Column(Integer, ForeignKey("subjects.id"))
    teacher_id = Column(Integer, ForeignKey("teachers.id"))
    
    grade = relationship("Grade")
    subject = relationship("Subject")
    teacher = relationship("Teacher")
    
    participantes_json = Column(JSON, default=[])

class ActivityLog(Base):
    __tablename__ = "logs"
    id = Column(Integer, primary_key=True, index=True)
    timestamp = Column(DateTime, default=datetime.utcnow)
    action = Column(String) # CREATE, UPDATE, DELETE, QUERY
    details = Column(String)
    user_id = Column(Integer, nullable=True)
