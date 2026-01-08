import SwiftUI

struct MainTabView: View {
    @State private var selectedTab = 0

    var body: some View {
        TabView(selection: $selectedTab) {
            HomeView()
                .tabItem {
                    Image(systemName: "house.fill")
                    Text("Inicio")
                }
                .tag(0)

            TasksView()
                .tabItem {
                    Image(systemName: "checklist")
                    Text("Tareas")
                }
                .tag(1)

            GradesView()
                .tabItem {
                    Image(systemName: "chart.bar.fill")
                    Text("Notas")
                }
                .tag(2)

            PaymentsView()
                .tabItem {
                    Image(systemName: "creditcard.fill")
                    Text("Pagos")
                }
                .tag(3)

            ProfileView()
                .tabItem {
                    Image(systemName: "person.fill")
                    Text("Perfil")
                }
                .tag(4)
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
                    // Welcome Card
                    HStack {
                        VStack(alignment: .leading, spacing: 4) {
                            Text("¡Hola!")
                                .foregroundColor(.white.opacity(0.8))
                            Text(userName)
                                .font(.title2.bold())
                                .foregroundColor(.white)
                            Text(grade)
                                .font(.subheadline)
                                .foregroundColor(.white.opacity(0.8))
                        }

                        Spacer()

                        ZStack {
                            Circle()
                                .fill(.white.opacity(0.2))
                                .frame(width: 60, height: 60)
                            Text("CM")
                                .font(.title3.bold())
                                .foregroundColor(.white)
                        }
                    }
                    .padding(20)
                    .background(
                        LinearGradient(
                            colors: [Color(hex: "1E3A5F"), Color(hex: "4A7AB8")],
                            startPoint: .leading,
                            endPoint: .trailing
                        )
                    )
                    .cornerRadius(20)
                    .padding(.horizontal)

                    // Quick Actions
                    VStack(alignment: .leading, spacing: 12) {
                        Text("Acceso Rápido")
                            .font(.headline)
                            .padding(.horizontal)

                        HStack(spacing: 12) {
                            QuickActionButton(
                                icon: "checklist", title: "Tareas", badge: "5", color: .green)
                            QuickActionButton(icon: "chart.bar", title: "Notas", color: .blue)
                            QuickActionButton(icon: "calendar", title: "Asistencia", color: .purple)
                            QuickActionButton(
                                icon: "creditcard", title: "Pagos", badge: "2", color: .orange)
                        }
                        .padding(.horizontal)
                    }

                    // Pending Tasks
                    VStack(alignment: .leading, spacing: 12) {
                        HStack {
                            Text("Tareas Pendientes")
                                .font(.headline)
                            Spacer()
                            Button("Ver todo") {}
                                .foregroundColor(Color(hex: "4A7AB8"))
                        }
                        .padding(.horizontal)

                        VStack(spacing: 10) {
                            TaskRowCard(
                                title: "Ejercicios de Matemáticas", subject: "Matemáticas",
                                dueDate: "Mañana", color: .green)
                            TaskRowCard(
                                title: "Lectura Cap. 5", subject: "Comunicación",
                                dueDate: "En 3 días", color: .blue)
                            TaskRowCard(
                                title: "Proyecto de Ciencias", subject: "Ciencias",
                                dueDate: "En 1 semana", color: .purple)
                        }
                        .padding(.horizontal)
                    }

                    // Schedule
                    VStack(alignment: .leading, spacing: 12) {
                        Text("Horario de Hoy")
                            .font(.headline)
                            .padding(.horizontal)

                        VStack(spacing: 8) {
                            ScheduleRow(time: "7:30", subject: "Matemáticas", room: "Aula 5A")
                            ScheduleRow(time: "8:15", subject: "Comunicación", room: "Aula 5A")
                            ScheduleRow(time: "9:00", subject: "Ciencias", room: "Lab 2")
                        }
                        .padding(.horizontal)
                    }
                }
                .padding(.vertical)
            }
            .background(Color(hex: "F5F7FA"))
            .navigationTitle("Oxford")
            .toolbar {
                ToolbarItem(placement: .navigationBarTrailing) {
                    Button(action: {}) {
                        ZStack(alignment: .topTrailing) {
                            Image(systemName: "bell")
                            Circle()
                                .fill(.red)
                                .frame(width: 8, height: 8)
                        }
                    }
                }
            }
        }
    }
}

// MARK: - Quick Action Button
struct QuickActionButton: View {
    let icon: String
    let title: String
    var badge: String? = nil
    let color: Color

    var body: some View {
        VStack(spacing: 8) {
            ZStack(alignment: .topTrailing) {
                ZStack {
                    RoundedRectangle(cornerRadius: 12)
                        .fill(color.opacity(0.1))
                        .frame(width: 48, height: 48)
                    Image(systemName: icon)
                        .foregroundColor(color)
                }

                if let badge = badge {
                    Text(badge)
                        .font(.caption2.bold())
                        .foregroundColor(.white)
                        .padding(4)
                        .background(.red)
                        .clipShape(Circle())
                        .offset(x: 4, y: -4)
                }
            }

            Text(title)
                .font(.caption)
                .foregroundColor(Color(hex: "1E3A5F"))
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, 12)
        .background(.white)
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.05), radius: 10)
    }
}

// MARK: - Task Row Card
struct TaskRowCard: View {
    let title: String
    let subject: String
    let dueDate: String
    let color: Color

    var body: some View {
        HStack(spacing: 12) {
            ZStack {
                RoundedRectangle(cornerRadius: 12)
                    .fill(color.opacity(0.1))
                    .frame(width: 48, height: 48)
                Image(systemName: "doc.text")
                    .foregroundColor(color)
            }

            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(.subheadline.weight(.medium))
                Text(subject)
                    .font(.caption)
                    .foregroundColor(.gray)
            }

            Spacer()

            VStack(alignment: .trailing) {
                Image(systemName: "clock")
                    .font(.caption)
                    .foregroundColor(.gray)
                Text(dueDate)
                    .font(.caption)
                    .foregroundColor(.gray)
            }
        }
        .padding()
        .background(.white)
        .cornerRadius(16)
        .shadow(color: .black.opacity(0.05), radius: 10)
    }
}

// MARK: - Schedule Row
struct ScheduleRow: View {
    let time: String
    let subject: String
    let room: String

    var body: some View {
        HStack {
            Text(time)
                .font(.subheadline.weight(.medium))
                .foregroundColor(Color(hex: "1E3A5F"))
                .frame(width: 50, alignment: .leading)

            VStack(alignment: .leading) {
                Text(subject)
                    .font(.subheadline.weight(.medium))
                Text(room)
                    .font(.caption)
                    .foregroundColor(.gray)
            }

            Spacer()
        }
        .padding()
        .background(.white)
        .cornerRadius(12)
    }
}

#Preview {
    MainTabView()
        .environmentObject(AuthManager())
}
