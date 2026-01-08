package com.oxford.shared.domain.entity

/**
 * Domain entities - Clean architecture core
 */

data class User(
    val id: Int,
    val email: String,
    val firstName: String,
    val lastName: String,
    val roles: List<UserRole>,
    val profileImageUrl: String? = null
) {
    val fullName: String get() = "$firstName $lastName"
    val isStudent: Boolean get() = roles.contains(UserRole.STUDENT)
    val isTeacher: Boolean get() = roles.contains(UserRole.TEACHER)
    val isParent: Boolean get() = roles.contains(UserRole.PARENT)
    val isAdmin: Boolean get() = roles.contains(UserRole.ADMIN)
}

enum class UserRole {
    STUDENT, TEACHER, PARENT, ADMIN, SECRETARY, COORDINATOR, DIRECTOR
}

data class Student(
    val id: Int,
    val firstName: String,
    val lastName: String,
    val studentCode: String,
    val email: String?,
    val grade: Grade?,
    val section: Section?,
    val photoUrl: String? = null,
    val isActive: Boolean = true
) {
    val fullName: String get() = "$firstName $lastName"
}

data class Grade(
    val id: Int,
    val name: String,
    val level: String? = null
)

data class Section(
    val id: Int,
    val name: String
)

data class Task(
    val id: Int,
    val title: String,
    val description: String?,
    val dueDate: String,
    val type: TaskType,
    val status: TaskStatus,
    val points: Int,
    val subject: String?,
    val teacher: String?,
    val mySubmission: TaskSubmission? = null
) {
    val isOverdue: Boolean get() = status == TaskStatus.ACTIVE && dueDate < currentDate()
    
    private fun currentDate(): String {
        // Simplified - in real app use kotlinx-datetime
        return "2026-01-08"
    }
}

enum class TaskType {
    HOMEWORK, EXAM, PROJECT, QUIZ, ACTIVITY
}

enum class TaskStatus {
    ACTIVE, COMPLETED, CANCELLED
}

data class TaskSubmission(
    val id: Int,
    val status: String,
    val score: Double?,
    val submittedAt: String,
    val isLate: Boolean
)

data class GradeRecord(
    val id: Int,
    val subject: String,
    val bimester: String,
    val score: Double,
    val maxScore: Double = 100.0,
    val percentage: Double = score / maxScore * 100,
    val comments: String? = null
) {
    val letterGrade: String get() = when {
        percentage >= 90 -> "A"
        percentage >= 80 -> "B"
        percentage >= 70 -> "C"
        percentage >= 60 -> "D"
        else -> "F"
    }
    
    val isPassing: Boolean get() = percentage >= 60
}

data class Attendance(
    val id: Int,
    val date: String,
    val status: AttendanceStatus,
    val subject: String? = null
)

enum class AttendanceStatus {
    PRESENT, ABSENT, LATE, EXCUSED
}

data class Payment(
    val id: Int,
    val amount: Double,
    val concept: String,
    val status: PaymentStatus,
    val dueDate: String,
    val paidAt: String? = null
) {
    val isPending: Boolean get() = status == PaymentStatus.PENDING
    val isOverdue: Boolean get() = isPending && dueDate < "2026-01-08"
}

enum class PaymentStatus {
    PENDING, PAID, PARTIAL, CANCELLED
}

data class Notification(
    val id: Int,
    val title: String,
    val message: String,
    val type: NotificationType,
    val isRead: Boolean,
    val createdAt: String
)

enum class NotificationType {
    GENERAL, TASK, GRADE, PAYMENT, ATTENDANCE, ANNOUNCEMENT
}
