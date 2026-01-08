package com.oxford.shared.domain.usecase

import com.oxford.shared.data.repository.*
import com.oxford.shared.domain.entity.*

/**
 * Use Cases - Business logic layer
 */

// ============================================
// AUTH USE CASES
// ============================================

class LoginUseCase(private val authRepository: AuthRepository) {
    suspend operator fun invoke(email: String, password: String): Result<User> {
        if (email.isBlank()) {
            return Result.failure(IllegalArgumentException("Email es requerido"))
        }
        if (password.isBlank()) {
            return Result.failure(IllegalArgumentException("Contraseña es requerida"))
        }
        if (!email.contains("@")) {
            return Result.failure(IllegalArgumentException("Email inválido"))
        }
        return authRepository.login(email, password)
    }
}

class LogoutUseCase(private val authRepository: AuthRepository) {
    operator fun invoke() {
        authRepository.logout()
    }
}

// ============================================
// STUDENT USE CASES
// ============================================

class GetStudentProfileUseCase(private val studentRepository: StudentRepository) {
    suspend operator fun invoke(): Result<Student> {
        return studentRepository.getMyProfile()
    }
}

// ============================================
// TASK USE CASES
// ============================================

class GetMyTasksUseCase(private val taskRepository: TaskRepository) {
    suspend operator fun invoke(): Result<List<Task>> {
        return taskRepository.getMyTasks()
    }
}

class GetPendingTasksUseCase(private val taskRepository: TaskRepository) {
    suspend operator fun invoke(): Result<List<Task>> {
        return taskRepository.getMyTasks().map { tasks ->
            tasks.filter { it.status == TaskStatus.ACTIVE }
                .sortedBy { it.dueDate }
        }
    }
}

class GetOverdueTasksUseCase(private val taskRepository: TaskRepository) {
    suspend operator fun invoke(): Result<List<Task>> {
        return taskRepository.getMyTasks().map { tasks ->
            tasks.filter { it.isOverdue }
        }
    }
}

// ============================================
// GRADE USE CASES
// ============================================

class GetMyGradesUseCase(private val gradeRepository: GradeRepository) {
    suspend operator fun invoke(): Result<List<GradeRecord>> {
        return gradeRepository.getMyGrades()
    }
}

class GetGradeAverageUseCase(private val gradeRepository: GradeRepository) {
    suspend operator fun invoke(): Result<Double> {
        return gradeRepository.getMyGrades().map { grades ->
            if (grades.isEmpty()) 0.0
            else grades.sumOf { it.percentage } / grades.size
        }
    }
}

class GetGradesBySubjectUseCase(private val gradeRepository: GradeRepository) {
    suspend operator fun invoke(): Result<Map<String, List<GradeRecord>>> {
        return gradeRepository.getMyGrades().map { grades ->
            grades.groupBy { it.subject }
        }
    }
}

// ============================================
// ATTENDANCE USE CASES
// ============================================

class GetMyAttendanceUseCase(private val attendanceRepository: AttendanceRepository) {
    suspend operator fun invoke(): Result<List<Attendance>> {
        return attendanceRepository.getMyAttendance()
    }
}

class GetAttendanceStatsUseCase(private val attendanceRepository: AttendanceRepository) {
    suspend operator fun invoke(): Result<AttendanceStats> {
        return attendanceRepository.getMyAttendance().map { records ->
            val total = records.size
            val present = records.count { it.status == AttendanceStatus.PRESENT }
            val absent = records.count { it.status == AttendanceStatus.ABSENT }
            val late = records.count { it.status == AttendanceStatus.LATE }
            val excused = records.count { it.status == AttendanceStatus.EXCUSED }
            
            AttendanceStats(
                total = total,
                present = present,
                absent = absent,
                late = late,
                excused = excused,
                percentage = if (total > 0) (present.toDouble() / total) * 100 else 0.0
            )
        }
    }
}

data class AttendanceStats(
    val total: Int,
    val present: Int,
    val absent: Int,
    val late: Int,
    val excused: Int,
    val percentage: Double
)

// ============================================
// PAYMENT USE CASES
// ============================================

class GetMyPaymentsUseCase(private val paymentRepository: PaymentRepository) {
    suspend operator fun invoke(): Result<List<Payment>> {
        return paymentRepository.getMyPayments()
    }
}

class GetPendingPaymentsUseCase(private val paymentRepository: PaymentRepository) {
    suspend operator fun invoke(): Result<List<Payment>> {
        return paymentRepository.getPendingPayments()
    }
}

class GetPaymentSummaryUseCase(private val paymentRepository: PaymentRepository) {
    suspend operator fun invoke(): Result<PaymentSummary> {
        return paymentRepository.getMyPayments().map { payments ->
            val pending = payments.filter { it.status == PaymentStatus.PENDING }
            val paid = payments.filter { it.status == PaymentStatus.PAID }
            
            PaymentSummary(
                totalPending = pending.sumOf { it.amount },
                totalPaid = paid.sumOf { it.amount },
                pendingCount = pending.size,
                overdueCount = pending.count { it.isOverdue }
            )
        }
    }
}

data class PaymentSummary(
    val totalPending: Double,
    val totalPaid: Double,
    val pendingCount: Int,
    val overdueCount: Int
)

// ============================================
// NOTIFICATION USE CASES
// ============================================

class GetNotificationsUseCase(private val notificationRepository: NotificationRepository) {
    suspend operator fun invoke(): Result<List<Notification>> {
        return notificationRepository.getNotifications()
    }
}

class GetUnreadCountUseCase(private val notificationRepository: NotificationRepository) {
    suspend operator fun invoke(): Result<Int> {
        return notificationRepository.getNotifications().map { notifications ->
            notifications.count { !it.isRead }
        }
    }
}

class MarkNotificationReadUseCase(private val notificationRepository: NotificationRepository) {
    suspend operator fun invoke(id: Int): Result<Unit> {
        return notificationRepository.markAsRead(id)
    }
}
