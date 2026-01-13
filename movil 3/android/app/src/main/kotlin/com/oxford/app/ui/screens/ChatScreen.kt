package com.oxford.app.ui.screens

import androidx.compose.animation.*
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowUpward
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.SmartToy
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import kotlinx.coroutines.launch
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONObject
import java.io.IOException
import java.util.UUID

data class ChatMessage(
    val id: String = UUID.randomUUID().toString(),
    val text: String,
    val isUser: Boolean
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ChatScreen(onNavigateBack: () -> Unit) {
    var messages by remember { mutableStateOf(listOf(
        ChatMessage(text = "¡Hola! 👋 Soy tu asistente personal Oxford.\n\n¿En qué puedo ayudarte hoy?", isUser = false)
    )) }
    var inputText by remember { mutableStateOf("") }
    var isLoading by remember { mutableStateOf(false) }
    val listState = rememberLazyListState()
    val coroutineScope = rememberCoroutineScope()
    
    // API Configuration
    // Use 10.0.2.2 for localhost access from Android Emulator
    val aiUrl = "http://10.0.2.2:8000/ai/chat/message" 
    val client = OkHttpClient()

    fun addBotMessage(text: String) {
        messages = messages + ChatMessage(text = text, isUser = false)
    }

    fun getLocalResponse(text: String): String {
        val lower = text.lowercase()
        return when {
            "tarea" in lower -> "📝 Tienes 3 tareas pendientes:\n\n1️⃣ Matemáticas - Ejercicios p.45 (Mañana)\n2️⃣ Comunicación - Lectura Cap. 5 (3 días)\n3️⃣ Ciencias - Proyecto final (1 semana)\n\n¿Quieres ayuda con alguna?"
            "nota" in lower || "promedio" in lower -> "📊 Tus notas:\n\n• Matemáticas: 88 ✅\n• Comunicación: 92 ⭐\n• Ciencias: 85 ✅\n• Historia: 78 ⚠️\n• Inglés: 90 ⭐\n\n📈 Promedio: 86.6"
            "horario" in lower -> "📅 Tu horario hoy:\n\n• 7:30 - Matemáticas\n• 8:15 - Comunicación\n• 9:00 - Ciencias\n• 10:15 - Historia\n• 11:00 - Inglés"
            "consejo" in lower || "tip" in lower -> "💡 Tips de estudio:\n\n🧠 Técnica Pomodoro:\n• 25 min estudio + 5 min descanso\n\n📝 Para memorizar:\n• Lee en voz alta\n• Haz resúmenes cortos\n• Enseña a alguien"
            "hola" in lower || "buenos" in lower -> "¡Hola! 👋 ¿En qué puedo ayudarte hoy?\n\nPuedo ayudarte con:\n• Tareas 📝\n• Notas 📊\n• Horarios 📅\n• Tips de estudio 💡"
            else -> "🤔 Puedo ayudarte con:\n\n• Ver tus tareas\n• Consultar notas\n• Ver tu horario\n• Tips de estudio\n\n¿Qué te gustaría hacer?"
        }
    }

    fun sendMessage(text: String) {
        if (text.isBlank()) return
        
        messages = messages + ChatMessage(text = text, isUser = true)
        inputText = ""
        isLoading = true

        coroutineScope.launch {
            // Scroll to bottom
            listState.animateScrollToItem(messages.size - 1)
            
            // Should be done in a real ViewModel with Dispatchers.IO, but keeping it simple here
            Thread {
                try {
                    val jsonBody = JSONObject().apply {
                        put("message", text)
                        put("role", "student")
                    }
                    
                    val request = Request.Builder()
                        .url(aiUrl)
                        .post(jsonBody.toString().toRequestBody("application/json".toMediaType()))
                        .build()

                    client.newCall(request).execute().use { response ->
                        val respBody = response.body?.string()
                        if (response.isSuccessful && respBody != null) {
                            val json = JSONObject(respBody)
                            val botResponse = json.getString("response")
                            launchAndAddMessage(botResponse, ::addBotMessage)
                        } else {
                             launchAndAddMessage(getLocalResponse(text), ::addBotMessage)
                        }
                    }
                } catch (e: Exception) {
                    launchAndAddMessage(getLocalResponse(text), ::addBotMessage)
                } finally {
                    isLoading = false
                }
            }.start()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Asistente Oxford") },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(Icons.Filled.ArrowBack, contentDescription = "Atrás")
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = MaterialTheme.colorScheme.primary,
                    titleContentColor = MaterialTheme.colorScheme.onPrimary,
                    navigationIconContentColor = MaterialTheme.colorScheme.onPrimary,
                    actionIconContentColor = MaterialTheme.colorScheme.onPrimary
                )
            )
        },
        containerColor = MaterialTheme.colorScheme.background
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
        ) {
            // Messages List
            LazyColumn(
                state = listState,
                modifier = Modifier
                    .weight(1f)
                    .padding(horizontal = 16.dp),
                contentPadding = PaddingValues(vertical = 16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(messages) { message ->
                    ChatBubble(message)
                }
                if (isLoading) {
                    item {
                        Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.padding(8.dp)) {
                            CircularProgressIndicator(modifier = Modifier.size(20.dp), strokeWidth = 2.dp)
                            Spacer(modifier = Modifier.width(8.dp))
                            Text("Pensando...", color = MaterialTheme.colorScheme.onBackground.copy(alpha=0.6f), fontSize = 12.sp)
                        }
                    }
                }
            }

            // Suggestions Chips
            LazyRow(
                contentPadding = PaddingValues(horizontal = 16.dp, vertical = 8.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp),
                modifier = Modifier.background(Color.Transparent)
            ) {
                val suggestions = listOf(
                    "📝 Mis tareas" to "Ver mis tareas pendientes",
                    "📊 Mis notas" to "Cuáles son mis notas",
                    "📅 Mi horario" to "Ver mi horario",
                    "💡 Tips" to "Dame consejos para estudiar"
                )
                items(suggestions) { (label, query) ->
                    SuggestionChip(
                        onClick = { sendMessage(query) },
                        label = { Text(label, color = MaterialTheme.colorScheme.primary) },
                        colors = SuggestionChipDefaults.suggestionChipColors(
                            containerColor = MaterialTheme.colorScheme.surface
                        ),
                        border = SuggestionChipDefaults.suggestionChipBorder(
                            borderColor = MaterialTheme.colorScheme.primary.copy(alpha = 0.2f)
                        )
                    )
                }
            }

            // Input Area
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(MaterialTheme.colorScheme.surface)
                    .padding(12.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                TextField(
                    value = inputText,
                    onValueChange = { inputText = it },
                    placeholder = { Text("Escribe un mensaje...") },
                    modifier = Modifier
                        .weight(1f)
                        .clip(RoundedCornerShape(24.dp))
                        .background(MaterialTheme.colorScheme.surfaceVariant), // Better for input background
                    colors = TextFieldDefaults.colors(
                        focusedIndicatorColor = Color.Transparent,
                        unfocusedIndicatorColor = Color.Transparent,
                        disabledIndicatorColor = Color.Transparent,
                        focusedContainerColor = MaterialTheme.colorScheme.surfaceVariant,
                        unfocusedContainerColor = MaterialTheme.colorScheme.surfaceVariant
                    ),
                    maxLines = 3,
                    keyboardOptions = KeyboardOptions(imeAction = ImeAction.Send),
                    keyboardActions = KeyboardActions(onSend = { sendMessage(inputText) })
                )
                
                Spacer(modifier = Modifier.width(8.dp))
                
                IconButton(
                    onClick = { sendMessage(inputText) },
                    enabled = inputText.isNotBlank() && !isLoading,
                    modifier = Modifier
                        .size(48.dp)
                        .background(
                            if(inputText.isNotBlank()) MaterialTheme.colorScheme.primary else Color.Gray, 
                            CircleShape
                        )
                ) {
                    Icon(
                        Icons.Filled.ArrowUpward,
                        contentDescription = "Enviar",
                        tint = Color.White
                    )
                }
            }
        }
    }
}

// Helper to bridge non-main thread to main thread
fun launchAndAddMessage(text: String, addMsg: (String) -> Unit) {
    // In valid Compose/ViewModel architecture this is handled differently.
    // Since we are inside composable with remembered scope, we rely on the side-effect update.
    // However, calling state update from background thread is not allowed. 
    // We actually need to dispatch.
    // For this simple implementation, we assumed `coroutineScope.launch` handles it,
    // but OKHttp callback is on background thread. We must switch context.
    // Correct way is implemented inside the launch block above.
    // Wait, the above code uses Thread, so we need a global handler or similar.
    // Actually, let's fix the implementation to use Viewmodel properly or main looper.
    // Simplified fix: Since accessing state must be on main thread.
    android.os.Handler(android.os.Looper.getMainLooper()).post {
        addMsg(text)
    }
}

@Composable
fun ChatBubble(message: ChatMessage) {
    Column(
        modifier = Modifier.fillMaxWidth(),
        horizontalAlignment = if (message.isUser) Alignment.End else Alignment.Start
    ) {
        Box(
            modifier = Modifier
                .widthIn(max = 280.dp)
                .clip(
                    RoundedCornerShape(
                        topStart = 16.dp,
                        topEnd = 16.dp,
                        bottomStart = if (message.isUser) 16.dp else 0.dp,
                        bottomEnd = if (message.isUser) 0.dp else 16.dp
                    )
                )
                .background(
                    if (message.isUser) MaterialTheme.colorScheme.primary 
                    else MaterialTheme.colorScheme.surfaceVariant
                )
                .padding(12.dp)
        ) {
            Text(
                text = message.text,
                color = if (message.isUser) MaterialTheme.colorScheme.onPrimary 
                       else MaterialTheme.colorScheme.onSurfaceVariant,
                fontSize = 15.sp
            )
        }
    }
}
