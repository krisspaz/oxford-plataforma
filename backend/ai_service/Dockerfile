FROM python:3.10-alpine

WORKDIR /app

# Install system dependencies
# build-base for compilation, openblas-dev for numpy/pandas optimization
RUN apk add --no-cache \
    build-base \
    openblas-dev

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Run with Uvicorn (Production server)
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
