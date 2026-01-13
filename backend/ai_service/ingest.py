import os
import chromadb
from minio import Minio
from pypdf import PdfReader
from io import BytesIO
from sentence_transformers import SentenceTransformer

# Configuration
MINIO_ENDPOINT = os.getenv("MINIO_ENDPOINT", "minio:9000")
MINIO_ACCESS_KEY = os.getenv("MINIO_ROOT_USER", "minioadmin")
MINIO_SECRET_KEY = os.getenv("MINIO_ROOT_PASSWORD", "minioadmin")
BUCKET_NAME = "oxford-storage"
CHROMA_DB_PATH = "./chroma_db"

def get_minio_client():
    return Minio(
        MINIO_ENDPOINT,
        access_key=MINIO_ACCESS_KEY,
        secret_key=MINIO_SECRET_KEY,
        secure=False
    )

def ingest_documents():
    print("🚀 Starting Ingestion Process...")
    
    # 1. Connect to Services
    client = get_minio_client()
    chroma_client = chromadb.PersistentClient(path=CHROMA_DB_PATH)
    collection = chroma_client.get_or_create_collection(name="knowledge_base")
    model = SentenceTransformer('all-MiniLM-L6-v2')

    # 2. List PDFs in Bucket
    objects = client.list_objects(BUCKET_NAME, prefix="reports/", recursive=True)
    
    documents = []
    ids = []
    metadatas = []

    for obj in objects:
        filename = obj.object_name.lower()
        
        # Skip unsupported files
        if not (filename.endswith('.pdf') or filename.endswith('.txt')):
            continue
            
        print(f"📄 Processing: {obj.object_name}")
        
        # Download file into memory
        response = client.get_object(BUCKET_NAME, obj.object_name)
        file_bytes = response.read()
        response.close()
        
        # Extract Text based on file type
        text = ""
        if filename.endswith('.pdf'):
            pdf_file = BytesIO(file_bytes)
            reader = PdfReader(pdf_file)
            for page in reader.pages:
                text += page.extract_text() + "\n"
        elif filename.endswith('.txt'):
            text = file_bytes.decode('utf-8', errors='ignore')
        
        # Chunk Text (Simple Strategy: 500 characters overlap)
        chunk_size = 500
        for i in range(0, len(text), chunk_size):
            chunk = text[i:i+chunk_size]
            if len(chunk) < 50: continue # Skip tiny chunks
            
            doc_id = f"{obj.object_name}_{i}"
            documents.append(chunk)
            ids.append(doc_id)
            metadatas.append({"source": obj.object_name})

    # 3. Embed & Store
    if documents:
        print(f"🧠 Generating Embeddings for {len(documents)} chunks...")
        embeddings = model.encode(documents).tolist()
        
        collection.upsert(
            documents=documents,
            embeddings=embeddings,
            metadatas=metadatas,
            ids=ids
        )
        print("✅ Ingestion Complete!")
    else:
        print("⚠️ No documents found to ingest.")

if __name__ == "__main__":
    ingest_documents()
