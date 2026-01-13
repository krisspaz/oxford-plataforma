package com.oxford.shared.data.model

import kotlinx.serialization.Serializable

@Serializable
data class LoginRequest(
    val email: String,
    val password: String
)

@Serializable
data class LoginResponse(
    val token: String,
    val refreshToken: String? = null,
    val user: UserDto? = null
)

@Serializable
data class UserDto(
    val id: Int,
    val email: String,
    val firstName: String? = null,
    val lastName: String? = null,
    val roles: List<String> = emptyList()
)

@Serializable
data class StudentDto(
    val id: Int,
    val firstName: String,
    val lastName: String,
    val studentCode: String,
    val email: String? = null,
    val gradeId: Int? = null,
    val gradeName: String? = null,
    val sectionId: Int? = null,
    val sectionName: String? = null,
    val isActive: Boolean = true
)

@Serializable
data class TaskDto(
    val id: Int,
    val title: String,
    val description: String? = null,
    val dueDate: String,
    val type: String,
    val status: String,
    val points: Int,
    val subjectName: String? = null,
    val teacherName: String? = null
)

@Serializable
data class GradeRecordDto(
    val id: Int,
    val subjectName: String,
    val bimesterName: String,
    val score: Double,
    val maxScore: Double = 100.0,
    val comments: String? = null
)

@Serializable
data class AttendanceDto(
    val id: Int,
    val date: String,
    val status: String, // PRESENT, ABSENT, LATE, EXCUSED
    val subjectName: String? = null
)

@Serializable
data class PaymentDto(
    val id: Int,
    val amount: Double,
    val concept: String,
    val status: String,
    val dueDate: String,
    val paidAt: String? = null
)

@Serializable
data class NotificationDto(
    val id: Int,
    val title: String,
    val message: String,
    val type: String,
    val isRead: Boolean = false,
    val createdAt: String
)

@Serializable
data class ApiResponse<T>(
    val success: Boolean,
    val data: T? = null,
    val message: String? = null,
    val error: String? = null
)
