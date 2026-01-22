# Helper to extract ID using grep/sed (fallback for missing python)
get_id() {
    echo "$1" | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2
}

# 1. LOGIN
echo "---------------------------------------------------"
echo "1. LOGGING IN as admin@oxford.edu..."
LOGIN_RESP=$(curl -s -X POST "$BASE_URL/login_check" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@oxford.edu", "password":"admin123"}')

# Debug response
if [[ "$LOGIN_RESP" != *"token"* ]]; then
    echo "❌ LOGIN FAILED. Raw Response: $LOGIN_RESP"
    exit 1
fi

TOKEN=$(echo "$LOGIN_RESP" | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ TOKEN PARSE FAILED. Response: $LOGIN_RESP"
    exit 1
fi
echo "✅ LOGIN SUCCESS. Token obtained."
AUTH_HEADER="Authorization: Bearer $TOKEN"

# 2. CREATE ACADEMIC LEVEL
echo "---------------------------------------------------"
echo "2. CREATING ACADEMIC LEVEL..."
LEVEL_DATA="{\"name\":\"Diversificado Demo $(date +%s)\", \"code\":\"DIV-$(date +%s)\"}"
LEVEL_RESP=$(curl -s -X POST "$BASE_URL/academic_levels" \
  -H "$AUTH_HEADER" -H "Content-Type: application/json" -d "$LEVEL_DATA")
LEVEL_ID=$(get_id "$LEVEL_RESP")
echo "   -> Level ID: $LEVEL_ID"
if [ -z "$LEVEL_ID" ]; then 
    echo "❌ CRITICAL: Level create failed: $LEVEL_RESP"
    exit 1
fi

# 3. CREATE GRADE
echo "---------------------------------------------------"
echo "3. CREATING GRADE..."
GRADE_DATA="{\"name\":\"Cuarto Bachillerato Demo $(date +%s)\", \"code\":\"4BA-$(date +%s)\", \"sortOrder\":10, \"levelId\":$LEVEL_ID}"
GRADE_RESP=$(curl -s -X POST "$BASE_URL/grades" \
  -H "$AUTH_HEADER" -H "Content-Type: application/json" -d "$GRADE_DATA")
GRADE_ID=$(get_id "$GRADE_RESP")
echo "   -> Grade ID: $GRADE_ID"
if [ -z "$GRADE_ID" ]; then 
    echo "❌ CRITICAL: Grade create failed: $GRADE_RESP"
    exit 1
fi

# 4. CREATE TEACHER
echo "---------------------------------------------------"
echo "4. CREATING TEACHER..."
TEACHER_EMAIL="teacher.demo.$(date +%s)@oxford.edu"
TEACHER_DATA="{\"firstName\":\"Profe\", \"lastName\":\"Demo\", \"email\":\"$TEACHER_EMAIL\", \"password\":\"teach123\", \"dpi\":\"$(date +%s)\", \"address\":\"Ciudad\"}"
TEACHER_RESP=$(curl -s -X POST "$BASE_URL/teachers" \
  -H "$AUTH_HEADER" -H "Content-Type: application/json" -d "$TEACHER_DATA")
TEACHER_ID=$(get_id "$TEACHER_RESP")
echo "   -> Teacher ID: $TEACHER_ID"
if [ -z "$TEACHER_ID" ]; then 
    echo "❌ CRITICAL: Teacher create failed: $TEACHER_RESP"
    exit 1
fi

# 5. CREATE SUBJECT
echo "---------------------------------------------------"
echo "5. CREATING SUBJECT..."
SUBJECT_DATA="{\"name\":\"Física Fundamental $(date +%s)\", \"code\":\"FIS-$(date +%s)\", \"hoursWeek\":5, \"active\":true}"
SUBJECT_RESP=$(curl -s -X POST "$BASE_URL/subjects" \
  -H "$AUTH_HEADER" -H "Content-Type: application/json" -d "$SUBJECT_DATA")
SUBJECT_ID=$(get_id "$SUBJECT_RESP")
echo "   -> Subject ID: $SUBJECT_ID"
if [ -z "$SUBJECT_ID" ]; then 
    echo "❌ CRITICAL: Subject create failed: $SUBJECT_RESP"
    exit 1
fi

# 6. ASSIGN SUBJECT
echo "---------------------------------------------------"
echo "6. ASSIGNING SUBJECT..."
ASSIGN_DATA="{\"subjectId\":$SUBJECT_ID, \"teacherId\":$TEACHER_ID, \"gradeId\":$GRADE_ID, \"hoursPerWeek\":4}"
ASSIGN_RESP=$(curl -s -w "\nHTTP_CODE:%{http_code}" -X POST "$BASE_URL/subjects/assign" \
  -H "$AUTH_HEADER" -H "Content-Type: application/json" -d "$ASSIGN_DATA")
if [[ "$ASSIGN_RESP" != *"HTTP_CODE:201"* ]]; then
     echo "❌ CRITICAL: Assignment failed: $ASSIGN_RESP"
     exit 1
else
     echo "   ✅ Assignment SUCCESS"
fi

# 7. ENROLL STUDENT
echo "---------------------------------------------------"
echo "7. ENROLLING STUDENT..."
STUDENT_DATA="{\"firstName\":\"Alumno\", \"lastName\":\"Demo\", \"birthDate\":\"2010-01-01\", \"gender\":\"M\"}"
ENROLL_BODY="{\"student\":$STUDENT_DATA, \"enrollment\":{\"grade\":$GRADE_ID, \"section\":\"A\", \"package\":1}}"
ENROLL_RESP=$(curl -s -w "\nHTTP_CODE:%{http_code}" -X POST "$BASE_URL/enrollments" \
  -H "$AUTH_HEADER" -H "Content-Type: application/json" -d "$ENROLL_BODY")
if [[ "$ENROLL_RESP" != *"HTTP_CODE:201"* ]]; then
     echo "❌ CRITICAL: Enrollment failed: $ENROLL_RESP"
     exit 1
else
     echo "   ✅ Student Enrolled SUCCESS"
fi
