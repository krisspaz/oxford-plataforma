# AI Service Production Dockerfile
FROM python:3.13-slim AS base

WORKDIR /app

# System dependencies
RUN apt-get update && apt-get install -y \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Production stage
FROM base AS production

# Copy application
COPY . .

# Create non-root user
RUN useradd -m -r appuser && chown -R appuser:appuser /app
USER appuser

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8001/health || exit 1

EXPOSE 8001

# Use uvicorn for production
CMD ["uvicorn", "main_fastapi:app", "--host", "0.0.0.0", "--port", "8001", "--workers", "4"]
