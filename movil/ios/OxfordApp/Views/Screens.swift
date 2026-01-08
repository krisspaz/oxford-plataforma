import SwiftUI

// MARK: - Tasks View
struct TasksView: View {
    @State private var selectedTab = 0
    let tabs = ["Pendientes", "Entregadas", "Todas"]

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                // Tab Picker
                Picker("Estado", selection: $selectedTab) {
                    ForEach(0..<tabs.count, id: \.self) { index in
                        Text(tabs[index]).tag(index)
                    }
                }
                .pickerStyle(.segmented)
                .padding()

                // Task List
                ScrollView {
                    VStack(spacing: 12) {
                        ForEach(0..<5) { index in
                            NavigationLink(destination: TaskDetailView(taskId: index)) {
                                TaskRowCard(
                                    title: "Tarea \(index + 1)",
                                    subject: index % 2 == 0 ? "Matemáticas" : "Comunicación",
                                    dueDate: "En \(index + 1) días",
                                    color: index % 2 == 0 ? .green : .blue
                                )
                            }
                            .buttonStyle(.plain)
                        }
                    }
                    .padding()
                }
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Mis Tareas")
        }
    }
}

// MARK: - Task Detail View
struct TaskDetailView: View {
    let taskId: Int

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 20) {
                // Header
                VStack(alignment: .leading, spacing: 8) {
                    Text("Ejercicios de Matemáticas")
                        .font(.title.bold())

                    HStack(spacing: 16) {
                        Label("Matemáticas", systemImage: "book")
                        Label("100 pts", systemImage: "star")
                    }
                    .foregroundColor(.gray)

                    HStack {
                        Image(systemName: "calendar")
                        Text("Fecha de entrega: 10 Enero 2026")
                    }
                    .foregroundColor(.orange)
                }

                Divider()

                // Description
                VStack(alignment: .leading, spacing: 8) {
                    Text("Descripción")
                        .font(.headline)
                    Text(
                        "Resolver los ejercicios del 1 al 20 de la página 45 del libro de texto. Mostrar todo el procedimiento."
                    )
                    .foregroundColor(.secondary)
                }

                Divider()

                // Attachments
                VStack(alignment: .leading, spacing: 8) {
                    Text("Archivos Adjuntos")
                        .font(.headline)

                    HStack {
                        Image(systemName: "doc.fill")
                            .foregroundColor(.blue)
                        Text("ejercicios_p45.pdf")
                        Spacer()
                        Button(action: {}) {
                            Image(systemName: "arrow.down.circle")
                        }
                    }
                    .padding()
                    .background(Color.gray.opacity(0.1))
                    .cornerRadius(12)
                }

                Spacer()
            }
            .padding()
        }
        .navigationTitle("Detalle")
        .navigationBarTitleDisplayMode(.inline)
        .safeAreaInset(edge: .bottom) {
            Button(action: {}) {
                Label("Entregar Tarea", systemImage: "arrow.up.circle.fill")
                    .frame(maxWidth: .infinity)
                    .padding()
                    .background(Color(hex: "1E3A5F"))
                    .foregroundColor(.white)
                    .cornerRadius(12)
            }
            .padding()
            .background(.ultraThinMaterial)
        }
    }
}

// MARK: - Grades View
struct GradesView: View {
    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 16) {
                    // Average Card
                    VStack(spacing: 8) {
                        Text("Promedio General")
                            .foregroundColor(.white.opacity(0.8))
                        Text("85.5")
                            .font(.system(size: 48, weight: .bold))
                            .foregroundColor(.white)
                        Text("Bimestre Actual")
                            .foregroundColor(.white.opacity(0.8))
                    }
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 30)
                    .background(Color(hex: "1E3A5F"))
                    .cornerRadius(20)
                    .padding(.horizontal)

                    // Subject Grades
                    VStack(spacing: 12) {
                        GradeRow(subject: "Matemáticas", score: 88, color: .green)
                        GradeRow(subject: "Comunicación", score: 92, color: .blue)
                        GradeRow(subject: "Ciencias", score: 85, color: .purple)
                        GradeRow(subject: "Historia", score: 78, color: .orange)
                        GradeRow(subject: "Inglés", score: 90, color: .cyan)
                    }
                    .padding(.horizontal)
                }
                .padding(.vertical)
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
                RoundedRectangle(cornerRadius: 12)
                    .fill(color.opacity(0.1))
                    .frame(width: 48, height: 48)
                Image(systemName: "book.fill")
                    .foregroundColor(color)
            }

            Text(subject)
                .font(.subheadline.weight(.medium))

            Spacer()

            Text(String(format: "%.0f", score))
                .font(.title2.bold())
                .foregroundColor(color)
        }
        .padding()
        .background(.white)
        .cornerRadius(16)
    }
}

// MARK: - Payments View
struct PaymentsView: View {
    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 16) {
                    // Balance Card
                    VStack(spacing: 8) {
                        Text("Saldo Pendiente")
                            .foregroundColor(.white.opacity(0.8))
                        Text("Q 1,500.00")
                            .font(.system(size: 36, weight: .bold))
                            .foregroundColor(.white)
                        Text("2 pagos pendientes")
                            .foregroundColor(.white.opacity(0.8))
                    }
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 24)
                    .background(Color.orange)
                    .cornerRadius(20)
                    .padding(.horizontal)

                    // Payment List
                    VStack(spacing: 12) {
                        PaymentRow(
                            concept: "Colegiatura Enero", amount: "Q 750.00", status: "Pendiente")
                        PaymentRow(
                            concept: "Colegiatura Febrero", amount: "Q 750.00", status: "Pendiente")
                        PaymentRow(
                            concept: "Colegiatura Diciembre", amount: "Q 750.00", status: "Pagado")
                        PaymentRow(
                            concept: "Colegiatura Noviembre", amount: "Q 750.00", status: "Pagado")
                    }
                    .padding(.horizontal)
                }
                .padding(.vertical)
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Pagos")
        }
    }
}

struct PaymentRow: View {
    let concept: String
    let amount: String
    let status: String

    var isPaid: Bool { status == "Pagado" }

    var body: some View {
        HStack {
            VStack(alignment: .leading) {
                Text(concept)
                    .font(.subheadline.weight(.medium))
                Text(amount)
                    .foregroundColor(.gray)
            }

            Spacer()

            Text(status)
                .font(.caption.weight(.medium))
                .padding(.horizontal, 12)
                .padding(.vertical, 6)
                .background(isPaid ? Color.green.opacity(0.1) : Color.orange.opacity(0.1))
                .foregroundColor(isPaid ? .green : .orange)
                .cornerRadius(20)
        }
        .padding()
        .background(.white)
        .cornerRadius(16)
    }
}

// MARK: - Profile View
struct ProfileView: View {
    @EnvironmentObject var authManager: AuthManager

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 24) {
                    // Avatar
                    VStack(spacing: 12) {
                        ZStack {
                            Circle()
                                .fill(Color(hex: "1E3A5F"))
                                .frame(width: 100, height: 100)
                            Text("CM")
                                .font(.largeTitle.bold())
                                .foregroundColor(.white)
                        }

                        Text("Carlos Martínez")
                            .font(.title2.bold())
                        Text("carlos.martinez@oxford.edu")
                            .foregroundColor(.gray)
                        Text("5to Primaria - Sección A")
                            .foregroundColor(.gray)
                    }
                    .padding(.top)

                    // Menu Items
                    VStack(spacing: 8) {
                        ProfileMenuItem(icon: "person", title: "Editar Perfil")
                        ProfileMenuItem(icon: "lock", title: "Cambiar Contraseña")
                        ProfileMenuItem(icon: "bell", title: "Notificaciones")
                        ProfileMenuItem(icon: "questionmark.circle", title: "Ayuda")
                    }
                    .padding(.horizontal)

                    Spacer(minLength: 40)

                    // Logout Button
                    Button(action: { authManager.logout() }) {
                        HStack {
                            Image(systemName: "rectangle.portrait.and.arrow.right")
                            Text("Cerrar Sesión")
                        }
                        .frame(maxWidth: .infinity)
                        .padding()
                        .background(Color.red.opacity(0.1))
                        .foregroundColor(.red)
                        .cornerRadius(12)
                    }
                    .padding(.horizontal)
                }
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Mi Perfil")
        }
    }
}

struct ProfileMenuItem: View {
    let icon: String
    let title: String

    var body: some View {
        HStack {
            Image(systemName: icon)
                .foregroundColor(Color(hex: "1E3A5F"))
                .frame(width: 24)
            Text(title)
            Spacer()
            Image(systemName: "chevron.right")
                .foregroundColor(.gray)
        }
        .padding()
        .background(.white)
        .cornerRadius(12)
    }
}

#Preview {
    TasksView()
}
