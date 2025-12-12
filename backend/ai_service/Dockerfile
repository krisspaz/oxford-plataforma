FROM python:3.10-slim

WORKDIR /app

# Install system dependencies (if any needed for scipy/numpy)
RUN apt-get update && apt-get install -y build-essential && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Run with Uvicorn (Production server)
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
