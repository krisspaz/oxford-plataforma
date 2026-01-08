from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from config import config
from database import db
import uvicorn

# Import Routes
from routes.auth_routes import auth_router
from routes.core_routes import core_router
from routes.schedule_routes import schedule_router
from routes.analytics_routes import analytics_router
from routes.chat_routes import chat_router

def create_app():
    app = FastAPI(
        title="Corpo Oxford AI Service",
        version="3.4",
        description="AI Core for Academic Scheduling, Analytics & Personal Assistant"
    )
    
    # CORS Configuration
    app.add_middleware(
        CORSMiddleware,
        allow_origins=["*"], # Tighten this in production
        allow_credentials=True,
        allow_methods=["*"],
        allow_headers=["*"],
    )
    
    # Register Routers
    app.include_router(auth_router, prefix="/auth", tags=["Authentication"])
    app.include_router(core_router, tags=["Core Commands"])
    app.include_router(schedule_router, tags=["Scheduling"])
    app.include_router(analytics_router, prefix="/analytics", tags=["Analytics"])
    app.include_router(chat_router, tags=["Personal Assistant"])
    
    # Events
    @app.on_event("startup")
    async def startup_event():
        print("🚀 AI Service Starting Up...")
        db.seed_data()
        
    return app

app = create_app()

if __name__ == "__main__":
    print(f"🚀 AI Service running on port {config.port} (Env: {config.log_level})")
    uvicorn.run("main:app", host=config.host, port=config.port, reload=config.debug)
