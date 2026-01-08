import SwiftUI

@main
struct OxfordApp: App {
    @StateObject private var authManager = AuthManager()

    var body: some Scene {
        WindowGroup {
            if authManager.isAuthenticated {
                MainTabView()
                    .environmentObject(authManager)
            } else {
                LoginView()
                    .environmentObject(authManager)
            }
        }
    }
}

// MARK: - Auth Manager
class AuthManager: ObservableObject {
    @Published var isAuthenticated = false
    @Published var isLoading = false
    @Published var errorMessage: String?
    @Published var token: String?

    private let baseURL = "https://oxford-gateway.onrender.com/api"

    func login(email: String, password: String) {
        isLoading = true
        errorMessage = nil

        guard let url = URL(string: "\(baseURL)/login_check") else {
            errorMessage = "URL inválida"
            isLoading = false
            return
        }

        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")

        let body = ["email": email, "password": password]
        request.httpBody = try? JSONSerialization.data(withJSONObject: body)

        URLSession.shared.dataTask(with: request) { [weak self] data, response, error in
            DispatchQueue.main.async {
                self?.isLoading = false

                if let error = error {
                    self?.errorMessage = "Error de conexión: \(error.localizedDescription)"
                    return
                }

                guard let data = data else {
                    self?.errorMessage = "Sin respuesta del servidor"
                    return
                }

                if let httpResponse = response as? HTTPURLResponse {
                    if httpResponse.statusCode == 401 {
                        self?.errorMessage = "Credenciales incorrectas"
                        return
                    }
                    if httpResponse.statusCode != 200 {
                        self?.errorMessage = "Error del servidor (\(httpResponse.statusCode))"
                        return
                    }
                }

                do {
                    if let json = try JSONSerialization.jsonObject(with: data) as? [String: Any],
                        let token = json["token"] as? String
                    {
                        self?.token = token
                        self?.isAuthenticated = true
                    } else {
                        self?.errorMessage = "Respuesta inválida"
                    }
                } catch {
                    self?.errorMessage = "Error procesando respuesta"
                }
            }
        }.resume()
    }

    func logout() {
        token = nil
        isAuthenticated = false
    }
}

// MARK: - Color Extension
extension Color {
    init(hex: String) {
        let hex = hex.trimmingCharacters(in: CharacterSet.alphanumerics.inverted)
        var int: UInt64 = 0
        Scanner(string: hex).scanHexInt64(&int)
        let a: UInt64
        let r: UInt64
        let g: UInt64
        let b: UInt64
        switch hex.count {
        case 3: (a, r, g, b) = (255, (int >> 8) * 17, (int >> 4 & 0xF) * 17, (int & 0xF) * 17)
        case 6: (a, r, g, b) = (255, int >> 16, int >> 8 & 0xFF, int & 0xFF)
        case 8: (a, r, g, b) = (int >> 24, int >> 16 & 0xFF, int >> 8 & 0xFF, int & 0xFF)
        default: (a, r, g, b) = (255, 0, 0, 0)
        }
        self.init(
            .sRGB, red: Double(r) / 255, green: Double(g) / 255, blue: Double(b) / 255,
            opacity: Double(a) / 255)
    }
}

// MARK: - Login View
struct LoginView: View {
    @EnvironmentObject var authManager: AuthManager
    @State private var email = ""
    @State private var password = ""
    @State private var showPassword = false

    var body: some View {
        ZStack {
            LinearGradient(
                gradient: Gradient(colors: [
                    Color(hex: "1E3A5F"), Color(hex: "2E5A8F"), Color(hex: "4A7AB8"),
                ]),
                startPoint: .top, endPoint: .bottom
            ).ignoresSafeArea()

            VStack(spacing: 24) {
                Spacer()

                ZStack {
                    RoundedRectangle(cornerRadius: 20).fill(.white).frame(width: 100, height: 100)
                    Text("🎓").font(.system(size: 48))
                }

                Text("Colegio Oxford").font(.system(size: 32, weight: .bold)).foregroundColor(
                    .white)
                Text("Plataforma Educativa").font(.subheadline).foregroundColor(.white.opacity(0.8))

                VStack(spacing: 20) {
                    Text("Iniciar Sesión").font(.title2.bold()).foregroundColor(
                        Color(hex: "1E3A5F"))

                    HStack {
                        Image(systemName: "envelope").foregroundColor(.gray)
                        TextField("Correo Electrónico", text: $email)
                    }
                    .padding().background(Color.gray.opacity(0.1)).cornerRadius(12)

                    HStack {
                        Image(systemName: "lock").foregroundColor(.gray)
                        if showPassword {
                            TextField("Contraseña", text: $password)
                        } else {
                            SecureField("Contraseña", text: $password)
                        }
                        Button(action: { showPassword.toggle() }) {
                            Image(systemName: showPassword ? "eye.slash" : "eye").foregroundColor(
                                .gray)
                        }
                    }
                    .padding().background(Color.gray.opacity(0.1)).cornerRadius(12)

                    if let error = authManager.errorMessage {
                        Text(error).foregroundColor(.red).font(.caption)
                    }

                    Button(action: { authManager.login(email: email, password: password) }) {
                        HStack {
                            if authManager.isLoading {
                                ProgressView().progressViewStyle(
                                    CircularProgressViewStyle(tint: .white))
                            } else {
                                Text("Ingresar").fontWeight(.semibold)
                            }
                        }
                        .frame(maxWidth: .infinity).padding()
                        .background(Color(hex: "1E3A5F")).foregroundColor(.white).cornerRadius(12)
                    }
                    .disabled(email.isEmpty || password.isEmpty || authManager.isLoading)

                    Button("¿Olvidaste tu contraseña?") {}
                        .foregroundColor(Color(hex: "2E5A8F"))
                }
                .padding(24).background(.white).cornerRadius(24).padding(.horizontal, 24)

                Spacer()

                Text("© 2026 Colegio Oxford\nTodos los derechos reservados")
                    .font(.caption).foregroundColor(.white.opacity(0.6)).multilineTextAlignment(
                        .center
                    )
                    .padding(.bottom, 32)
            }
        }
    }
}

// MARK: - Main Tab View
struct MainTabView: View {
    @State private var selectedTab = 0

    var body: some View {
        TabView(selection: $selectedTab) {
            HomeView().tabItem {
                Image(systemName: "house.fill")
                Text("Inicio")
            }.tag(0)
            ChatView().tabItem {
                Image(systemName: "bubble.left.and.bubble.right.fill")
                Text("Asistente")
            }.tag(1)
            TasksListView().tabItem {
                Image(systemName: "checklist")
                Text("Tareas")
            }.tag(2)
            GradesListView().tabItem {
                Image(systemName: "chart.bar.fill")
                Text("Notas")
            }.tag(3)
            ProfileScreen().tabItem {
                Image(systemName: "person.fill")
                Text("Perfil")
            }.tag(4)
        }
        .tint(Color(hex: "1E3A5F"))
    }
}

// MARK: - Home View
struct HomeView: View {
    let userName = "Carlos Martínez"
    let grade = "5to Primaria - Sección A"

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 20) {
                    HStack {
                        VStack(alignment: .leading, spacing: 4) {
                            Text("¡Hola!").foregroundColor(.white.opacity(0.8))
                            Text(userName).font(.title2.bold()).foregroundColor(.white)
                            Text(grade).font(.subheadline).foregroundColor(.white.opacity(0.8))
                        }
                        Spacer()
                        ZStack {
                            Circle().fill(.white.opacity(0.2)).frame(width: 60, height: 60)
                            Text("CM").font(.title3.bold()).foregroundColor(.white)
                        }
                    }
                    .padding(20)
                    .background(
                        LinearGradient(
                            colors: [Color(hex: "1E3A5F"), Color(hex: "4A7AB8")],
                            startPoint: .leading, endPoint: .trailing)
                    )
                    .cornerRadius(20).padding(.horizontal)

                    VStack(alignment: .leading, spacing: 12) {
                        Text("Acceso Rápido").font(.headline).padding(.horizontal)
                        HStack(spacing: 12) {
                            QuickBtn(icon: "checklist", title: "Tareas", badge: "5", color: .green)
                            QuickBtn(icon: "chart.bar", title: "Notas", color: .blue)
                            QuickBtn(icon: "calendar", title: "Asistencia", color: .purple)
                            QuickBtn(icon: "creditcard", title: "Pagos", badge: "2", color: .orange)
                        }.padding(.horizontal)
                    }

                    VStack(alignment: .leading, spacing: 12) {
                        HStack {
                            Text("Tareas Pendientes").font(.headline)
                            Spacer()
                            Button("Ver todo") {}.foregroundColor(Color(hex: "4A7AB8"))
                        }.padding(.horizontal)

                        VStack(spacing: 10) {
                            TaskCard(
                                title: "Ejercicios de Matemáticas", subject: "Matemáticas",
                                due: "Mañana", color: .green)
                            TaskCard(
                                title: "Lectura Cap. 5", subject: "Comunicación", due: "En 3 días",
                                color: .blue)
                            TaskCard(
                                title: "Proyecto de Ciencias", subject: "Ciencias",
                                due: "En 1 semana", color: .purple)
                        }.padding(.horizontal)
                    }
                }.padding(.vertical)
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Oxford")
        }
    }
}

struct QuickBtn: View {
    let icon: String
    let title: String
    var badge: String? = nil
    let color: Color

    var body: some View {
        VStack(spacing: 8) {
            ZStack(alignment: .topTrailing) {
                ZStack {
                    RoundedRectangle(cornerRadius: 12).fill(color.opacity(0.1)).frame(
                        width: 48, height: 48)
                    Image(systemName: icon).foregroundColor(color)
                }
                if let badge = badge {
                    Text(badge).font(.caption2.bold()).foregroundColor(.white).padding(4)
                        .background(.red).clipShape(Circle()).offset(x: 4, y: -4)
                }
            }
            Text(title).font(.caption).foregroundColor(Color(hex: "1E3A5F"))
        }
        .frame(maxWidth: .infinity).padding(.vertical, 12)
        .background(.white).cornerRadius(16).shadow(color: .black.opacity(0.05), radius: 10)
    }
}

struct TaskCard: View {
    let title: String
    let subject: String
    let due: String
    let color: Color

    var body: some View {
        HStack(spacing: 12) {
            ZStack {
                RoundedRectangle(cornerRadius: 12).fill(color.opacity(0.1)).frame(
                    width: 48, height: 48)
                Image(systemName: "doc.text").foregroundColor(color)
            }
            VStack(alignment: .leading, spacing: 2) {
                Text(title).font(.subheadline.weight(.medium))
                Text(subject).font(.caption).foregroundColor(.gray)
            }
            Spacer()
            VStack(alignment: .trailing) {
                Image(systemName: "clock").font(.caption).foregroundColor(.gray)
                Text(due).font(.caption).foregroundColor(.gray)
            }
        }
        .padding().background(.white).cornerRadius(16).shadow(
            color: .black.opacity(0.05), radius: 10)
    }
}

// MARK: - Tasks List View
struct TasksListView: View {
    @State private var selectedTab = 0

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                Picker("Estado", selection: $selectedTab) {
                    Text("Pendientes").tag(0)
                    Text("Entregadas").tag(1)
                    Text("Todas").tag(2)
                }.pickerStyle(.segmented).padding()

                ScrollView {
                    VStack(spacing: 12) {
                        ForEach(0..<5) { i in
                            TaskCard(
                                title: "Tarea \(i+1)",
                                subject: i % 2 == 0 ? "Matemáticas" : "Comunicación",
                                due: "En \(i+1) días", color: i % 2 == 0 ? .green : .blue)
                        }
                    }.padding()
                }
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Mis Tareas")
        }
    }
}

// MARK: - Grades List View
struct GradesListView: View {
    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 16) {
                    VStack(spacing: 8) {
                        Text("Promedio General").foregroundColor(.white.opacity(0.8))
                        Text("85.5").font(.system(size: 48, weight: .bold)).foregroundColor(.white)
                        Text("Bimestre Actual").foregroundColor(.white.opacity(0.8))
                    }
                    .frame(maxWidth: .infinity).padding(.vertical, 30)
                    .background(Color(hex: "1E3A5F")).cornerRadius(20).padding(.horizontal)

                    VStack(spacing: 12) {
                        GradeRow(subject: "Matemáticas", score: 88, color: .green)
                        GradeRow(subject: "Comunicación", score: 92, color: .blue)
                        GradeRow(subject: "Ciencias", score: 85, color: .purple)
                        GradeRow(subject: "Historia", score: 78, color: .orange)
                        GradeRow(subject: "Inglés", score: 90, color: .cyan)
                    }.padding(.horizontal)
                }.padding(.vertical)
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Mis Notas")
        }
    }
}

struct GradeRow: View {
    let subject: String
    let score: Double
    let color: Color

    var body: some View {
        HStack {
            ZStack {
                RoundedRectangle(cornerRadius: 12).fill(color.opacity(0.1)).frame(
                    width: 48, height: 48)
                Image(systemName: "book.fill").foregroundColor(color)
            }
            Text(subject).font(.subheadline.weight(.medium))
            Spacer()
            Text(String(format: "%.0f", score)).font(.title2.bold()).foregroundColor(color)
        }.padding().background(.white).cornerRadius(16)
    }
}

// MARK: - Payments List View
struct PaymentsListView: View {
    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 16) {
                    VStack(spacing: 8) {
                        Text("Saldo Pendiente").foregroundColor(.white.opacity(0.8))
                        Text("Q 1,500.00").font(.system(size: 36, weight: .bold)).foregroundColor(
                            .white)
                        Text("2 pagos pendientes").foregroundColor(.white.opacity(0.8))
                    }
                    .frame(maxWidth: .infinity).padding(.vertical, 24)
                    .background(Color.orange).cornerRadius(20).padding(.horizontal)

                    VStack(spacing: 12) {
                        PayRow(concept: "Colegiatura Enero", amount: "Q 750.00", paid: false)
                        PayRow(concept: "Colegiatura Febrero", amount: "Q 750.00", paid: false)
                        PayRow(concept: "Colegiatura Diciembre", amount: "Q 750.00", paid: true)
                        PayRow(concept: "Colegiatura Noviembre", amount: "Q 750.00", paid: true)
                    }.padding(.horizontal)
                }.padding(.vertical)
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Pagos")
        }
    }
}

struct PayRow: View {
    let concept: String
    let amount: String
    let paid: Bool

    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(concept).font(.subheadline.weight(.medium))
                Text(amount).foregroundColor(.gray)
            }
            Spacer()
            Text(paid ? "Pagado" : "Pendiente")
                .font(.caption.weight(.medium))
                .padding(.horizontal, 12).padding(.vertical, 6)
                .background(paid ? Color.green.opacity(0.1) : Color.orange.opacity(0.1))
                .foregroundColor(paid ? .green : .orange).cornerRadius(20)
        }.padding().background(.white).cornerRadius(16)
    }
}

// MARK: - Profile Screen
struct ProfileScreen: View {
    @EnvironmentObject var authManager: AuthManager

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 24) {
                    VStack(spacing: 12) {
                        ZStack {
                            Circle().fill(Color(hex: "1E3A5F")).frame(width: 100, height: 100)
                            Text("CM").font(.largeTitle.bold()).foregroundColor(.white)
                        }
                        Text("Carlos Martínez").font(.title2.bold())
                        Text("carlos.martinez@oxford.edu").foregroundColor(.gray)
                        Text("5to Primaria - Sección A").foregroundColor(.gray)
                    }.padding(.top)

                    VStack(spacing: 8) {
                        ProfileItem(icon: "person", title: "Editar Perfil")
                        ProfileItem(icon: "lock", title: "Cambiar Contraseña")
                        ProfileItem(icon: "bell", title: "Notificaciones")
                        ProfileItem(icon: "questionmark.circle", title: "Ayuda")
                    }.padding(.horizontal)

                    Spacer(minLength: 40)

                    Button(action: { authManager.logout() }) {
                        HStack {
                            Image(systemName: "rectangle.portrait.and.arrow.right")
                            Text("Cerrar Sesión")
                        }
                        .frame(maxWidth: .infinity).padding()
                        .background(Color.red.opacity(0.1)).foregroundColor(.red).cornerRadius(12)
                    }.padding(.horizontal)
                }
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Mi Perfil")
        }
    }
}

struct ProfileItem: View {
    let icon: String
    let title: String

    var body: some View {
        HStack {
            Image(systemName: icon).foregroundColor(Color(hex: "1E3A5F")).frame(width: 24)
            Text(title)
            Spacer()
            Image(systemName: "chevron.right").foregroundColor(.gray)
        }.padding().background(.white).cornerRadius(12)
    }
}

// MARK: - AI Chat View
struct ChatView: View {
    @State private var messages: [ChatMessage] = [
        ChatMessage(
            id: UUID(),
            text: "¡Hola! 👋 Soy tu asistente personal Oxford.\n\n¿En qué puedo ayudarte hoy?",
            isUser: false)
    ]
    @State private var inputText = ""
    @State private var isLoading = false

    private let aiURL = "https://oxford-gateway.onrender.com/ai/chat/message"

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                // Messages
                ScrollViewReader { proxy in
                    ScrollView {
                        LazyVStack(spacing: 12) {
                            ForEach(messages) { message in
                                ChatBubble(message: message)
                            }
                            if isLoading {
                                HStack {
                                    ProgressView()
                                    Text("Pensando...")
                                        .foregroundColor(.gray)
                                }
                                .padding()
                            }
                        }
                        .padding()
                    }
                    .onChange(of: messages.count) { _ in
                        if let last = messages.last {
                            withAnimation {
                                proxy.scrollTo(last.id, anchor: .bottom)
                            }
                        }
                    }
                }

                // Quick Suggestions
                ScrollView(.horizontal, showsIndicators: false) {
                    HStack(spacing: 8) {
                        QuickChip(text: "📝 Mis tareas") { sendMessage("Ver mis tareas pendientes") }
                        QuickChip(text: "📊 Mis notas") { sendMessage("Cuáles son mis notas") }
                        QuickChip(text: "📅 Mi horario") { sendMessage("Ver mi horario") }
                        QuickChip(text: "💡 Tips") { sendMessage("Dame consejos para estudiar") }
                    }
                    .padding(.horizontal)
                }
                .padding(.vertical, 8)
                .background(Color.gray.opacity(0.05))

                // Input
                HStack(spacing: 12) {
                    TextField("Escribe un mensaje...", text: $inputText)
                        .padding(12)
                        .background(Color.gray.opacity(0.1))
                        .cornerRadius(20)

                    Button(action: { sendMessage(inputText) }) {
                        Image(systemName: "arrow.up.circle.fill")
                            .font(.system(size: 36))
                            .foregroundColor(inputText.isEmpty ? .gray : Color(hex: "1E3A5F"))
                    }
                    .disabled(inputText.isEmpty || isLoading)
                }
                .padding()
                .background(.white)
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Asistente Oxford")
            .navigationBarTitleDisplayMode(.inline)
        }
    }

    func sendMessage(_ text: String) {
        guard !text.isEmpty else { return }

        let userMessage = ChatMessage(id: UUID(), text: text, isUser: true)
        messages.append(userMessage)
        inputText = ""
        isLoading = true

        // Call AI API
        guard let url = URL(string: aiURL) else {
            addBotMessage("Error de conexión")
            return
        }

        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")

        let body = ["message": text, "role": "student"]
        request.httpBody = try? JSONSerialization.data(withJSONObject: body)

        URLSession.shared.dataTask(with: request) { data, response, error in
            DispatchQueue.main.async {
                isLoading = false

                if let error = error {
                    addBotMessage("Error: \(error.localizedDescription)")
                    return
                }

                guard let data = data else {
                    addBotMessage("Sin respuesta del servidor")
                    return
                }

                do {
                    if let json = try JSONSerialization.jsonObject(with: data) as? [String: Any],
                        let response = json["response"] as? String
                    {
                        addBotMessage(response)
                    } else {
                        // Fallback local response
                        addBotMessage(getLocalResponse(for: text))
                    }
                } catch {
                    addBotMessage(getLocalResponse(for: text))
                }
            }
        }.resume()
    }

    func addBotMessage(_ text: String) {
        let botMessage = ChatMessage(id: UUID(), text: text, isUser: false)
        messages.append(botMessage)
    }

    func getLocalResponse(for text: String) -> String {
        let lower = text.lowercased()

        if lower.contains("tarea") {
            return
                "📝 Tienes 3 tareas pendientes:\n\n1️⃣ Matemáticas - Ejercicios p.45 (Mañana)\n2️⃣ Comunicación - Lectura Cap. 5 (3 días)\n3️⃣ Ciencias - Proyecto final (1 semana)\n\n¿Quieres ayuda con alguna?"
        }
        if lower.contains("nota") || lower.contains("promedio") {
            return
                "📊 Tus notas:\n\n• Matemáticas: 88 ✅\n• Comunicación: 92 ⭐\n• Ciencias: 85 ✅\n• Historia: 78 ⚠️\n• Inglés: 90 ⭐\n\n📈 Promedio: 86.6"
        }
        if lower.contains("horario") {
            return
                "📅 Tu horario hoy:\n\n• 7:30 - Matemáticas\n• 8:15 - Comunicación\n• 9:00 - Ciencias\n• 10:15 - Historia\n• 11:00 - Inglés"
        }
        if lower.contains("consejo") || lower.contains("tip") {
            return
                "💡 Tips de estudio:\n\n🧠 Técnica Pomodoro:\n• 25 min estudio + 5 min descanso\n\n📝 Para memorizar:\n• Lee en voz alta\n• Haz resúmenes cortos\n• Enseña a alguien"
        }
        if lower.contains("hola") || lower.contains("buenos") {
            return
                "¡Hola! 👋 ¿En qué puedo ayudarte hoy?\n\nPuedo ayudarte con:\n• Tareas 📝\n• Notas 📊\n• Horarios 📅\n• Tips de estudio 💡"
        }

        return
            "🤔 Puedo ayudarte con:\n\n• Ver tus tareas\n• Consultar notas\n• Ver tu horario\n• Tips de estudio\n\n¿Qué te gustaría hacer?"
    }
}

struct ChatMessage: Identifiable {
    let id: UUID
    let text: String
    let isUser: Bool
}

struct ChatBubble: View {
    let message: ChatMessage

    var body: some View {
        HStack {
            if message.isUser { Spacer() }

            Text(message.text)
                .padding(12)
                .background(message.isUser ? Color(hex: "1E3A5F") : .white)
                .foregroundColor(message.isUser ? .white : .primary)
                .cornerRadius(16)
                .shadow(color: .black.opacity(0.05), radius: 5)

            if !message.isUser { Spacer() }
        }
    }
}

struct QuickChip: View {
    let text: String
    let action: () -> Void

    var body: some View {
        Button(action: action) {
            Text(text)
                .font(.caption)
                .padding(.horizontal, 12)
                .padding(.vertical, 8)
                .background(.white)
                .cornerRadius(16)
                .shadow(color: .black.opacity(0.05), radius: 3)
        }
        .foregroundColor(Color(hex: "1E3A5F"))
    }
}
