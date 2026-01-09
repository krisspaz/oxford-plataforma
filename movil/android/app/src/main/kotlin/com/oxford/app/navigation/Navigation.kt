package com.oxford.app.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import com.oxford.app.ui.screens.*

sealed class Screen(val route: String) {
    object Splash : Screen("splash")
    object Login : Screen("login")
    object Home : Screen("home")
    object Tasks : Screen("tasks")
    object TaskDetail : Screen("tasks/{taskId}") {
        fun createRoute(taskId: Int) = "tasks/$taskId"
    }
    object Grades : Screen("grades")
    object Attendance : Screen("attendance")
    object Payments : Screen("payments")
    object Profile : Screen("profile")
    object Notifications : Screen("notifications")
    object Schedule : Screen("schedule")
    object Chat : Screen("chat")
}

@Composable
fun OxfordNavHost(navController: NavHostController) {
    NavHost(
        navController = navController,
        startDestination = Screen.Login.route
    ) {
        composable(Screen.Login.route) {
            LoginScreen(
                onLoginSuccess = {
                    navController.navigate(Screen.Home.route) {
                        popUpTo(Screen.Login.route) { inclusive = true }
                    }
                }
            )
        }
        
        composable(Screen.Home.route) {
            HomeScreen(
                onNavigateToTasks = { navController.navigate(Screen.Tasks.route) },
                onNavigateToGrades = { navController.navigate(Screen.Grades.route) },
                onNavigateToAttendance = { navController.navigate(Screen.Attendance.route) },
                onNavigateToPayments = { navController.navigate(Screen.Payments.route) },
                onNavigateToProfile = { navController.navigate(Screen.Profile.route) },
                onNavigateToNotifications = { navController.navigate(Screen.Notifications.route) },
                onNavigateToSchedule = { navController.navigate(Screen.Schedule.route) },
                onNavigateToChat = { navController.navigate(Screen.Chat.route) }
            )
        }
        
        composable(Screen.Tasks.route) {
            TasksScreen(
                onNavigateBack = { navController.popBackStack() },
                onTaskClick = { taskId ->
                    navController.navigate(Screen.TaskDetail.createRoute(taskId))
                }
            )
        }
        
        composable(Screen.TaskDetail.route) { backStackEntry ->
            val taskId = backStackEntry.arguments?.getString("taskId")?.toIntOrNull() ?: 0
            TaskDetailScreen(
                taskId = taskId,
                onNavigateBack = { navController.popBackStack() }
            )
        }
        
        composable(Screen.Grades.route) {
            GradesScreen(onNavigateBack = { navController.popBackStack() })
        }
        
        composable(Screen.Attendance.route) {
            AttendanceScreen(onNavigateBack = { navController.popBackStack() })
        }
        
        composable(Screen.Payments.route) {
            PaymentsScreen(onNavigateBack = { navController.popBackStack() })
        }
        
        composable(Screen.Profile.route) {
            ProfileScreen(
                onNavigateBack = { navController.popBackStack() },
                onLogout = {
                    navController.navigate(Screen.Login.route) {
                        popUpTo(Screen.Home.route) { inclusive = true }
                    }
                }
            )
        }
        
        composable(Screen.Notifications.route) {
            NotificationsScreen(onNavigateBack = { navController.popBackStack() })
        }
        
        composable(Screen.Schedule.route) {
            ScheduleScreen(onNavigateBack = { navController.popBackStack() })
        }

        composable(Screen.Chat.route) {
            ChatScreen(onNavigateBack = { navController.popBackStack() })
        }
    }
}
