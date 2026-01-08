package com.oxford.app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

// ============================================
// TASKS SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TasksScreen(
    onNavigateBack: () -> Unit,
    onTaskClick: (Int) -> Unit
) {
    var selectedTab by remember { mutableStateOf(0) }
    val tabs = listOf("Pendientes", "Entregadas", "Todas")
    
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mis Tareas") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        Column(modifier = Modifier.padding(padding)) {
            TabRow(selectedTabIndex = selectedTab) {
                tabs.forEachIndexed { index, title ->
                    Tab(
                        selected = selectedTab == index,
                        onClick = { selectedTab = index },
                        text = { Text(title) }
                    )
                }
            }
            
            LazyColumn(
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(5) { index ->
                    TaskCard(
                        task = TaskItem(
                            "Tarea ${index + 1}",
                            if (index % 2 == 0) "Matemáticas" else "Comunicación",
                            "En ${index + 1} días",
                            if (index % 2 == 0) Color(0xFF4CAF50) else Color(0xFF2196F3)
                        ),
                        onClick = { onTaskClick(index) }
                    )
                }
            }
        }
    }
}

// ============================================
// TASK DETAIL SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TaskDetailScreen(
    taskId: Int,
    onNavigateBack: () -> Unit
) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Detalle de Tarea") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .padding(16.dp)
        ) {
            Text(
                text = "Ejercicios de Matemáticas",
                fontSize = 24.sp,
                fontWeight = FontWeight.Bold
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text("Materia: Matemáticas", color = Color.Gray)
            Text("Fecha de entrega: 10 Enero 2026", color = Color.Gray)
            Text("Puntos: 100", color = Color.Gray)
            
            Spacer(modifier = Modifier.height(24.dp))
            
            Text(
                text = "Descripción",
                fontSize = 18.sp,
                fontWeight = FontWeight.SemiBold
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text("Resolver los ejercicios del 1 al 20 de la página 45 del libro de texto.")
            
            Spacer(modifier = Modifier.weight(1f))
            
            Button(
                onClick = { /* TODO: Submit task */ },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(56.dp),
                shape = RoundedCornerShape(12.dp)
            ) {
                Icon(Icons.Default.Upload, contentDescription = null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Entregar Tarea", fontSize = 16.sp)
            }
        }
    }
}

// ============================================
// GRADES SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GradesScreen(onNavigateBack: () -> Unit) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mis Notas") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        LazyColumn(
            modifier = Modifier.padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            item {
                // Average Card
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFF1E3A5F))
                ) {
                    Column(
                        modifier = Modifier.padding(20.dp),
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Text("Promedio General", color = Color.White.copy(alpha = 0.8f))
                        Text(
                            text = "85.5",
                            fontSize = 48.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color.White
                        )
                        Text("Bimestre Actual", color = Color.White.copy(alpha = 0.8f))
                    }
                }
            }
            
            items(listOf(
                Triple("Matemáticas", 88.0, Color(0xFF4CAF50)),
                Triple("Comunicación", 92.0, Color(0xFF2196F3)),
                Triple("Ciencias", 85.0, Color(0xFF9C27B0)),
                Triple("Historia", 78.0, Color(0xFFFF9800)),
                Triple("Inglés", 90.0, Color(0xFF00BCD4))
            )) { (subject, score, color) ->
                GradeCard(subject = subject, score = score, color = color)
            }
        }
    }
}

@Composable
fun GradeCard(subject: String, score: Double, color: Color) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp)
    ) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(48.dp)
                    .background(color.copy(alpha = 0.1f), RoundedCornerShape(12.dp)),
                contentAlignment = Alignment.Center
            ) {
                Icon(Icons.Default.Book, contentDescription = null, tint = color)
            }
            Spacer(modifier = Modifier.width(12.dp))
            Text(
                text = subject,
                modifier = Modifier.weight(1f),
                fontWeight = FontWeight.Medium
            )
            Text(
                text = score.toString(),
                fontSize = 20.sp,
                fontWeight = FontWeight.Bold,
                color = color
            )
        }
    }
}

// ============================================
// ATTENDANCE SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AttendanceScreen(onNavigateBack: () -> Unit) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mi Asistencia") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .padding(16.dp)
        ) {
            // Stats Row
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                AttendanceStatCard("95%", "Asistencia", Color(0xFF4CAF50), Modifier.weight(1f))
                AttendanceStatCard("3", "Tardanzas", Color(0xFFFF9800), Modifier.weight(1f))
                AttendanceStatCard("2", "Faltas", Color(0xFFF44336), Modifier.weight(1f))
            }
            
            Spacer(modifier = Modifier.height(24.dp))
            
            Text(
                text = "Historial Reciente",
                fontSize = 18.sp,
                fontWeight = FontWeight.SemiBold
            )
            
            Spacer(modifier = Modifier.height(12.dp))
            
            LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                items(10) { index ->
                    val status = when (index % 4) {
                        0 -> "Presente" to Color(0xFF4CAF50)
                        1 -> "Presente" to Color(0xFF4CAF50)
                        2 -> "Tardanza" to Color(0xFFFF9800)
                        else -> "Presente" to Color(0xFF4CAF50)
                    }
                    AttendanceRow(
                        date = "${8 - index} Enero 2026",
                        status = status.first,
                        color = status.second
                    )
                }
            }
        }
    }
}

@Composable
fun AttendanceStatCard(value: String, label: String, color: Color, modifier: Modifier) {
    Card(modifier = modifier, colors = CardDefaults.cardColors(containerColor = color)) {
        Column(
            modifier = Modifier.padding(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(value, fontSize = 28.sp, fontWeight = FontWeight.Bold, color = Color.White)
            Text(label, fontSize = 12.sp, color = Color.White.copy(alpha = 0.8f))
        }
    }
}

@Composable
fun AttendanceRow(date: String, status: String, color: Color) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(date, modifier = Modifier.weight(1f))
            Surface(
                shape = RoundedCornerShape(20.dp),
                color = color.copy(alpha = 0.1f)
            ) {
                Text(
                    status,
                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp),
                    color = color,
                    fontWeight = FontWeight.Medium
                )
            }
        }
    }
}

// ============================================
// PAYMENTS SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PaymentsScreen(onNavigateBack: () -> Unit) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Pagos") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        LazyColumn(
            modifier = Modifier.padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            item {
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFFFF9800))
                ) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Text("Saldo Pendiente", color = Color.White.copy(alpha = 0.8f))
                        Text(
                            "Q 1,500.00",
                            fontSize = 36.sp,
                            fontWeight = FontWeight.Bold,
                            color = Color.White
                        )
                        Text("2 pagos pendientes", color = Color.White.copy(alpha = 0.8f))
                    }
                }
            }
            
            items(listOf(
                Triple("Colegiatura Enero", "Q 750.00", "Pendiente"),
                Triple("Colegiatura Febrero", "Q 750.00", "Pendiente"),
                Triple("Colegiatura Diciembre", "Q 750.00", "Pagado"),
                Triple("Colegiatura Noviembre", "Q 750.00", "Pagado")
            )) { (concept, amount, status) ->
                PaymentRow(concept = concept, amount = amount, status = status)
            }
        }
    }
}

@Composable
fun PaymentRow(concept: String, amount: String, status: String) {
    val color = if (status == "Pagado") Color(0xFF4CAF50) else Color(0xFFFF9800)
    Card(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(concept, fontWeight = FontWeight.Medium)
                Text(amount, color = Color.Gray)
            }
            Surface(
                shape = RoundedCornerShape(20.dp),
                color = color.copy(alpha = 0.1f)
            ) {
                Text(
                    status,
                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 4.dp),
                    color = color,
                    fontWeight = FontWeight.Medium
                )
            }
        }
    }
}

// ============================================
// PROFILE SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProfileScreen(
    onNavigateBack: () -> Unit,
    onLogout: () -> Unit
) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mi Perfil") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .padding(padding)
                .padding(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            // Avatar
            Box(
                modifier = Modifier
                    .size(100.dp)
                    .background(Color(0xFF1E3A5F), shape = RoundedCornerShape(50.dp)),
                contentAlignment = Alignment.Center
            ) {
                Text("CM", fontSize = 36.sp, fontWeight = FontWeight.Bold, color = Color.White)
            }
            
            Spacer(modifier = Modifier.height(16.dp))
            
            Text("Carlos Martínez", fontSize = 24.sp, fontWeight = FontWeight.Bold)
            Text("carlos.martinez@oxford.edu", color = Color.Gray)
            Text("5to Primaria - Sección A", color = Color.Gray)
            
            Spacer(modifier = Modifier.height(32.dp))
            
            ProfileMenuItem(Icons.Default.Person, "Editar Perfil") { }
            ProfileMenuItem(Icons.Default.Lock, "Cambiar Contraseña") { }
            ProfileMenuItem(Icons.Default.Notifications, "Notificaciones") { }
            ProfileMenuItem(Icons.Default.Help, "Ayuda") { }
            
            Spacer(modifier = Modifier.weight(1f))
            
            OutlinedButton(
                onClick = onLogout,
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.outlinedButtonColors(contentColor = Color.Red)
            ) {
                Icon(Icons.Default.Logout, contentDescription = null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Cerrar Sesión")
            }
        }
    }
}

@Composable
fun ProfileMenuItem(icon: androidx.compose.ui.graphics.vector.ImageVector, title: String, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 4.dp),
        onClick = onClick
    ) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(icon, contentDescription = null, tint = Color(0xFF1E3A5F))
            Spacer(modifier = Modifier.width(12.dp))
            Text(title, modifier = Modifier.weight(1f))
            Icon(Icons.Default.ChevronRight, contentDescription = null, tint = Color.Gray)
        }
    }
}

// ============================================
// NOTIFICATIONS SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun NotificationsScreen(onNavigateBack: () -> Unit) {
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Notificaciones") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        LazyColumn(
            modifier = Modifier.padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            items(5) { index ->
                NotificationCard(
                    title = "Nueva tarea asignada",
                    message = "Se ha asignado una nueva tarea en Matemáticas",
                    time = "Hace ${index + 1}h",
                    isRead = index > 1
                )
            }
        }
    }
}

@Composable
fun NotificationCard(title: String, message: String, time: String, isRead: Boolean) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(
            containerColor = if (isRead) Color.White else Color(0xFFE3F2FD)
        )
    ) {
        Row(modifier = Modifier.padding(16.dp)) {
            Icon(
                Icons.Default.Notifications,
                contentDescription = null,
                tint = Color(0xFF1E3A5F)
            )
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(title, fontWeight = FontWeight.SemiBold)
                Text(message, fontSize = 14.sp, color = Color.Gray)
            }
            Text(time, fontSize = 12.sp, color = Color.Gray)
        }
    }
}

// ============================================
// SCHEDULE SCREEN
// ============================================
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ScheduleScreen(onNavigateBack: () -> Unit) {
    var selectedDay by remember { mutableStateOf(0) }
    val days = listOf("Lun", "Mar", "Mié", "Jue", "Vie")
    
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Mi Horario") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Default.ArrowBack, "Volver")
                    }
                }
            )
        }
    ) { padding ->
        Column(modifier = Modifier.padding(padding)) {
            // Day selector
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                horizontalArrangement = Arrangement.SpaceEvenly
            ) {
                days.forEachIndexed { index, day ->
                    FilterChip(
                        selected = selectedDay == index,
                        onClick = { selectedDay = index },
                        label = { Text(day) }
                    )
                }
            }
            
            LazyColumn(
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                items(listOf(
                    Triple("7:30 - 8:15", "Matemáticas", "Aula 5A"),
                    Triple("8:15 - 9:00", "Comunicación", "Aula 5A"),
                    Triple("9:00 - 9:45", "Ciencias", "Lab 2"),
                    Triple("10:00 - 10:45", "Historia", "Aula 5A"),
                    Triple("10:45 - 11:30", "Inglés", "Aula 3B"),
                    Triple("11:30 - 12:15", "Ed. Física", "Cancha")
                )) { (time, subject, room) ->
                    ScheduleCard(time = time, subject = subject, room = room)
                }
            }
        }
    }
}

@Composable
fun ScheduleCard(time: String, subject: String, room: String) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                time,
                color = Color(0xFF1E3A5F),
                fontWeight = FontWeight.Medium,
                modifier = Modifier.width(100.dp)
            )
            Column(modifier = Modifier.weight(1f)) {
                Text(subject, fontWeight = FontWeight.SemiBold)
                Text(room, fontSize = 14.sp, color = Color.Gray)
            }
        }
    }
}
