package com.oxford.app.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.oxford.app.OxfordApplication
import com.oxford.shared.domain.entity.*
import com.oxford.shared.domain.usecase.*
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

// ============================================
// UI STATE CLASSES
// ============================================

sealed class UiState<out T> {
    object Loading : UiState<Nothing>()
    data class Success<T>(val data: T) : UiState<T>()
    data class Error(val message: String) : UiState<Nothing>()
}

data class AuthState(
    val isAuthenticated: Boolean = false,
    val isLoading: Boolean = false,
    val user: User? = null,
    val error: String? = null
)

data class HomeState(
    val student: Student? = null,
    val pendingTasks: List<Task> = emptyList(),
    val unreadNotifications: Int = 0,
    val isLoading: Boolean = true,
    val error: String? = null
)

data class TasksState(
    val tasks: List<Task> = emptyList(),
    val selectedTask: Task? = null,
    val isLoading: Boolean = true,
    val error: String? = null
)

data class GradesState(
    val grades: List<GradeRecord> = emptyList(),
    val average: Double = 0.0,
    val gradesBySubject: Map<String, List<GradeRecord>> = emptyMap(),
    val isLoading: Boolean = true,
    val error: String? = null
)

data class AttendanceState(
    val records: List<Attendance> = emptyList(),
    val stats: AttendanceStats? = null,
    val isLoading: Boolean = true,
    val error: String? = null
)

data class PaymentsState(
    val payments: List<Payment> = emptyList(),
    val summary: PaymentSummary? = null,
    val isLoading: Boolean = true,
    val error: String? = null
)

// ============================================
// AUTH VIEW MODEL
// ============================================

class AuthViewModel(
    private val loginUseCase: LoginUseCase,
    private val logoutUseCase: LogoutUseCase
) : ViewModel() {
    
    private val _state = MutableStateFlow(AuthState())
    val state: StateFlow<AuthState> = _state.asStateFlow()
    
    fun login(email: String, password: String) {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            
            loginUseCase(email, password)
                .onSuccess { user ->
                    _state.value = AuthState(
                        isAuthenticated = true,
                        isLoading = false,
                        user = user
                    )
                }
                .onFailure { error ->
                    _state.value = _state.value.copy(
                        isLoading = false,
                        error = error.message ?: "Error de autenticación"
                    )
                }
        }
    }
    
    fun logout() {
        logoutUseCase()
        _state.value = AuthState()
    }
    
    fun clearError() {
        _state.value = _state.value.copy(error = null)
    }
}

// ============================================
// HOME VIEW MODEL
// ============================================

class HomeViewModel(
    private val getStudentProfileUseCase: GetStudentProfileUseCase,
    private val getPendingTasksUseCase: GetPendingTasksUseCase,
    private val getUnreadCountUseCase: GetUnreadCountUseCase
) : ViewModel() {
    
    private val _state = MutableStateFlow(HomeState())
    val state: StateFlow<HomeState> = _state.asStateFlow()
    
    init {
        loadData()
    }
    
    fun loadData() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            
            // Load student profile
            getStudentProfileUseCase().onSuccess { student ->
                _state.value = _state.value.copy(student = student)
            }
            
            // Load pending tasks
            getPendingTasksUseCase().onSuccess { tasks ->
                _state.value = _state.value.copy(pendingTasks = tasks.take(5))
            }
            
            // Load unread notifications count
            getUnreadCountUseCase().onSuccess { count ->
                _state.value = _state.value.copy(unreadNotifications = count)
            }
            
            _state.value = _state.value.copy(isLoading = false)
        }
    }
    
    fun refresh() = loadData()
}

// ============================================
// TASKS VIEW MODEL
// ============================================

class TasksViewModel(
    private val getMyTasksUseCase: GetMyTasksUseCase
) : ViewModel() {
    
    private val _state = MutableStateFlow(TasksState())
    val state: StateFlow<TasksState> = _state.asStateFlow()
    
    init {
        loadTasks()
    }
    
    fun loadTasks() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            
            getMyTasksUseCase()
                .onSuccess { tasks ->
                    _state.value = TasksState(
                        tasks = tasks,
                        isLoading = false
                    )
                }
                .onFailure { error ->
                    _state.value = _state.value.copy(
                        isLoading = false,
                        error = error.message
                    )
                }
        }
    }
    
    fun selectTask(task: Task) {
        _state.value = _state.value.copy(selectedTask = task)
    }
    
    fun refresh() = loadTasks()
}

// ============================================
// GRADES VIEW MODEL
// ============================================

class GradesViewModel(
    private val getMyGradesUseCase: GetMyGradesUseCase,
    private val getGradeAverageUseCase: GetGradeAverageUseCase,
    private val getGradesBySubjectUseCase: GetGradesBySubjectUseCase
) : ViewModel() {
    
    private val _state = MutableStateFlow(GradesState())
    val state: StateFlow<GradesState> = _state.asStateFlow()
    
    init {
        loadGrades()
    }
    
    fun loadGrades() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            
            getMyGradesUseCase().onSuccess { grades ->
                _state.value = _state.value.copy(grades = grades)
            }
            
            getGradeAverageUseCase().onSuccess { average ->
                _state.value = _state.value.copy(average = average)
            }
            
            getGradesBySubjectUseCase().onSuccess { gradesBySubject ->
                _state.value = _state.value.copy(
                    gradesBySubject = gradesBySubject,
                    isLoading = false
                )
            }
        }
    }
    
    fun refresh() = loadGrades()
}

// ============================================
// PAYMENTS VIEW MODEL
// ============================================

class PaymentsViewModel(
    private val getMyPaymentsUseCase: GetMyPaymentsUseCase,
    private val getPaymentSummaryUseCase: GetPaymentSummaryUseCase
) : ViewModel() {
    
    private val _state = MutableStateFlow(PaymentsState())
    val state: StateFlow<PaymentsState> = _state.asStateFlow()
    
    init {
        loadPayments()
    }
    
    fun loadPayments() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            
            getMyPaymentsUseCase().onSuccess { payments ->
                _state.value = _state.value.copy(payments = payments)
            }
            
            getPaymentSummaryUseCase().onSuccess { summary ->
                _state.value = _state.value.copy(
                    summary = summary,
                    isLoading = false
                )
            }
        }
    }
    
    fun refresh() = loadPayments()
}

// ============================================
// VIEW MODEL FACTORIES
// ============================================

class AuthViewModelFactory : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        val container = OxfordApplication.instance.container
        return AuthViewModel(
            container.loginUseCase,
            container.logoutUseCase
        ) as T
    }
}

class HomeViewModelFactory : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        val container = OxfordApplication.instance.container
        return HomeViewModel(
            container.getStudentProfileUseCase,
            container.getPendingTasksUseCase,
            container.getUnreadCountUseCase
        ) as T
    }
}

class TasksViewModelFactory : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        val container = OxfordApplication.instance.container
        return TasksViewModel(container.getMyTasksUseCase) as T
    }
}

class GradesViewModelFactory : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        val container = OxfordApplication.instance.container
        return GradesViewModel(
            container.getMyGradesUseCase,
            container.getGradeAverageUseCase,
            GetGradesBySubjectUseCase(container.gradeRepository)
        ) as T
    }
}

class PaymentsViewModelFactory : ViewModelProvider.Factory {
    @Suppress("UNCHECKED_CAST")
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        val container = OxfordApplication.instance.container
        return PaymentsViewModel(
            container.getMyPaymentsUseCase,
            container.getPaymentSummaryUseCase
        ) as T
    }
}
