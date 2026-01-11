package com.oxford.app.di

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.preferencesDataStore
import com.oxford.shared.data.api.OxfordApiClient
import com.oxford.shared.data.repository.*
import com.oxford.shared.domain.usecase.*

val Context.dataStore: DataStore<Preferences> by preferencesDataStore(name = "oxford_prefs")

interface AppContainer {
    val apiClient: OxfordApiClient
    val authRepository: AuthRepository
    val studentRepository: StudentRepository
    val taskRepository: TaskRepository
    val gradeRepository: GradeRepository
    val attendanceRepository: AttendanceRepository
    val paymentRepository: PaymentRepository
    val notificationRepository: NotificationRepository
    
    // Use Cases
    val loginUseCase: LoginUseCase
    val logoutUseCase: LogoutUseCase
    val getStudentProfileUseCase: GetStudentProfileUseCase
    val getMyTasksUseCase: GetMyTasksUseCase
    val getPendingTasksUseCase: GetPendingTasksUseCase
    val getMyGradesUseCase: GetMyGradesUseCase
    val getGradeAverageUseCase: GetGradeAverageUseCase
    val getMyAttendanceUseCase: GetMyAttendanceUseCase
    val getAttendanceStatsUseCase: GetAttendanceStatsUseCase
    val getMyPaymentsUseCase: GetMyPaymentsUseCase
    val getPendingPaymentsUseCase: GetPendingPaymentsUseCase
    val getPaymentSummaryUseCase: GetPaymentSummaryUseCase
    val getNotificationsUseCase: GetNotificationsUseCase
    val getUnreadCountUseCase: GetUnreadCountUseCase
}

class DefaultAppContainer(private val context: Context) : AppContainer {
    
    private val baseUrl = "https://api.oxford.edu.gt"
    
    override val apiClient: OxfordApiClient by lazy {
        OxfordApiClient(baseUrl)
    }
    
    // Repositories
    override val authRepository: AuthRepository by lazy { AuthRepository(apiClient) }
    override val studentRepository: StudentRepository by lazy { StudentRepository(apiClient) }
    override val taskRepository: TaskRepository by lazy { TaskRepository(apiClient) }
    override val gradeRepository: GradeRepository by lazy { GradeRepository(apiClient) }
    override val attendanceRepository: AttendanceRepository by lazy { AttendanceRepository(apiClient) }
    override val paymentRepository: PaymentRepository by lazy { PaymentRepository(apiClient) }
    override val notificationRepository: NotificationRepository by lazy { NotificationRepository(apiClient) }
    
    // Use Cases
    override val loginUseCase: LoginUseCase by lazy { LoginUseCase(authRepository) }
    override val logoutUseCase: LogoutUseCase by lazy { LogoutUseCase(authRepository) }
    override val getStudentProfileUseCase: GetStudentProfileUseCase by lazy { GetStudentProfileUseCase(studentRepository) }
    override val getMyTasksUseCase: GetMyTasksUseCase by lazy { GetMyTasksUseCase(taskRepository) }
    override val getPendingTasksUseCase: GetPendingTasksUseCase by lazy { GetPendingTasksUseCase(taskRepository) }
    override val getMyGradesUseCase: GetMyGradesUseCase by lazy { GetMyGradesUseCase(gradeRepository) }
    override val getGradeAverageUseCase: GetGradeAverageUseCase by lazy { GetGradeAverageUseCase(gradeRepository) }
    override val getMyAttendanceUseCase: GetMyAttendanceUseCase by lazy { GetMyAttendanceUseCase(attendanceRepository) }
    override val getAttendanceStatsUseCase: GetAttendanceStatsUseCase by lazy { GetAttendanceStatsUseCase(attendanceRepository) }
    override val getMyPaymentsUseCase: GetMyPaymentsUseCase by lazy { GetMyPaymentsUseCase(paymentRepository) }
    override val getPendingPaymentsUseCase: GetPendingPaymentsUseCase by lazy { GetPendingPaymentsUseCase(paymentRepository) }
    override val getPaymentSummaryUseCase: GetPaymentSummaryUseCase by lazy { GetPaymentSummaryUseCase(paymentRepository) }
    override val getNotificationsUseCase: GetNotificationsUseCase by lazy { GetNotificationsUseCase(notificationRepository) }
    override val getUnreadCountUseCase: GetUnreadCountUseCase by lazy { GetUnreadCountUseCase(notificationRepository) }
}
