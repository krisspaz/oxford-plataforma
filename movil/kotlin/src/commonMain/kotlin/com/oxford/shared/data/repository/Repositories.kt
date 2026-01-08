package com.oxford.shared.data.repository

import com.oxford.shared.data.api.OxfordApiClient
import com.oxford.shared.data.model.*
import com.oxford.shared.domain.entity.*

/**
 * Auth Repository - Handles authentication
 */
class AuthRepository(private val api: OxfordApiClient) {
    
    suspend fun login(email: String, password: String): Result<User> {
        val result = api.post<LoginRequest, LoginResponse>(
            "/api/login_check",
            LoginRequest(email, password)
        )
        
        return result.map { response ->
            api.setAuthToken(response.token)
            response.user?.toDomain() ?: User(
                id = 0,
                email = email,
                firstName = "",
                lastName = "",
                roles = emptyList()
            )
        }
    }
    
    fun logout() {
        api.clearAuthToken()
    }
    
    private fun UserDto.toDomain() = User(
        id = id,
        email = email,
        firstName = firstName ?: "",
        lastName = lastName ?: "",
        roles = roles.mapNotNull { role ->
            when (role) {
                "ROLE_STUDENT" -> UserRole.STUDENT
                "ROLE_TEACHER" -> UserRole.TEACHER
                "ROLE_PARENT" -> UserRole.PARENT
                "ROLE_ADMIN" -> UserRole.ADMIN
                "ROLE_SECRETARY" -> UserRole.SECRETARY
                "ROLE_COORDINATION" -> UserRole.COORDINATOR
                "ROLE_DIRECTOR" -> UserRole.DIRECTOR
                else -> null
            }
        }
    )
}

/**
 * Student Repository - Student data operations
 */
class StudentRepository(private val api: OxfordApiClient) {
    
    suspend fun getMyProfile(): Result<Student> {
        return api.get<StudentDto>("/api/students/me").map { it.toDomain() }
    }
    
    suspend fun getStudentById(id: Int): Result<Student> {
        return api.get<StudentDto>("/api/students/$id").map { it.toDomain() }
    }
    
    private fun StudentDto.toDomain() = Student(
        id = id,
        firstName = firstName,
        lastName = lastName,
        studentCode = studentCode,
        email = email,
        grade = gradeId?.let { Grade(it, gradeName ?: "") },
        section = sectionId?.let { Section(it, sectionName ?: "") },
        isActive = isActive
    )
}

/**
 * Task Repository - Task operations
 */
class TaskRepository(private val api: OxfordApiClient) {
    
    suspend fun getMyTasks(): Result<List<Task>> {
        return api.get<List<TaskDto>>("/api/tasks/my-tasks").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    suspend fun getTaskById(id: Int): Result<Task> {
        return api.get<TaskDto>("/api/tasks/$id").map { it.toDomain() }
    }
    
    private fun TaskDto.toDomain() = Task(
        id = id,
        title = title,
        description = description,
        dueDate = dueDate,
        type = TaskType.valueOf(type.uppercase()),
        status = TaskStatus.valueOf(status.uppercase()),
        points = points,
        subject = subjectName,
        teacher = teacherName
    )
}

/**
 * Grade Repository - Grade records
 */
class GradeRepository(private val api: OxfordApiClient) {
    
    suspend fun getMyGrades(): Result<List<GradeRecord>> {
        return api.get<List<GradeRecordDto>>("/api/grades/my-grades").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    suspend fun getGradesByBimester(bimesterId: Int): Result<List<GradeRecord>> {
        return api.get<List<GradeRecordDto>>("/api/grades?bimesterId=$bimesterId").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    private fun GradeRecordDto.toDomain() = GradeRecord(
        id = id,
        subject = subjectName,
        bimester = bimesterName,
        score = score,
        maxScore = maxScore,
        comments = comments
    )
}

/**
 * Attendance Repository
 */
class AttendanceRepository(private val api: OxfordApiClient) {
    
    suspend fun getMyAttendance(): Result<List<Attendance>> {
        return api.get<List<AttendanceDto>>("/api/attendance/my-attendance").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    private fun AttendanceDto.toDomain() = Attendance(
        id = id,
        date = date,
        status = AttendanceStatus.valueOf(status.uppercase()),
        subject = subjectName
    )
}

/**
 * Payment Repository
 */
class PaymentRepository(private val api: OxfordApiClient) {
    
    suspend fun getMyPayments(): Result<List<Payment>> {
        return api.get<List<PaymentDto>>("/api/payments/my-payments").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    suspend fun getPendingPayments(): Result<List<Payment>> {
        return api.get<List<PaymentDto>>("/api/payments/pending").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    private fun PaymentDto.toDomain() = Payment(
        id = id,
        amount = amount,
        concept = concept,
        status = PaymentStatus.valueOf(status.uppercase()),
        dueDate = dueDate,
        paidAt = paidAt
    )
}

/**
 * Notification Repository
 */
class NotificationRepository(private val api: OxfordApiClient) {
    
    suspend fun getNotifications(): Result<List<Notification>> {
        return api.get<List<NotificationDto>>("/api/notifications").map { list ->
            list.map { it.toDomain() }
        }
    }
    
    suspend fun markAsRead(id: Int): Result<Unit> {
        return api.post<Unit, Unit>("/api/notifications/$id/read", Unit)
    }
    
    private fun NotificationDto.toDomain() = Notification(
        id = id,
        title = title,
        message = message,
        type = try { NotificationType.valueOf(type.uppercase()) } catch (e: Exception) { NotificationType.GENERAL },
        isRead = isRead,
        createdAt = createdAt
    )
}
