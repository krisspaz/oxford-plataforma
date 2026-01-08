import Combine
import SwiftUI

// MARK: - View Models for iOS

@MainActor
class HomeViewModel: ObservableObject {
    @Published var student: Student?
    @Published var pendingTasks: [TaskItem] = []
    @Published var unreadCount: Int = 0
    @Published var isLoading = true
    @Published var error: String?

    private let api = OxfordAPIClient.shared

    init() {
        Task { await loadData() }
    }

    func loadData() async {
        isLoading = true
        error = nil

        do {
            // In production, these would be real API calls
            // Simulating data for demo
            try await Task.sleep(nanoseconds: 500_000_000)

            pendingTasks = [
                TaskItem(
                    id: 1, title: "Ejercicios de Matemáticas", subject: "Matemáticas",
                    dueDate: "Mañana", color: .green),
                TaskItem(
                    id: 2, title: "Lectura Cap. 5", subject: "Comunicación", dueDate: "En 3 días",
                    color: .blue),
                TaskItem(
                    id: 3, title: "Proyecto de Ciencias", subject: "Ciencias",
                    dueDate: "En 1 semana", color: .purple),
            ]
            unreadCount = 3
            isLoading = false
        } catch {
            self.error = error.localizedDescription
            isLoading = false
        }
    }

    func refresh() async {
        await loadData()
    }
}

struct TaskItem: Identifiable {
    let id: Int
    let title: String
    let subject: String
    let dueDate: String
    let color: Color
}

@MainActor
class TasksViewModel: ObservableObject {
    @Published var tasks: [TaskItem] = []
    @Published var selectedTab: Int = 0
    @Published var isLoading = true
    @Published var error: String?

    init() {
        Task { await loadTasks() }
    }

    func loadTasks() async {
        isLoading = true

        try? await Task.sleep(nanoseconds: 300_000_000)

        tasks = [
            TaskItem(
                id: 1, title: "Ejercicios p.45", subject: "Matemáticas", dueDate: "Mañana",
                color: .green),
            TaskItem(
                id: 2, title: "Lectura Cap. 5", subject: "Comunicación", dueDate: "En 3 días",
                color: .blue),
            TaskItem(
                id: 3, title: "Proyecto Ciencias", subject: "Ciencias", dueDate: "En 1 semana",
                color: .purple),
            TaskItem(
                id: 4, title: "Ensayo Historia", subject: "Historia", dueDate: "En 2 días",
                color: .orange),
            TaskItem(
                id: 5, title: "Vocabulario", subject: "Inglés", dueDate: "En 4 días", color: .cyan),
        ]
        isLoading = false
    }
}

@MainActor
class GradesViewModel: ObservableObject {
    @Published var grades: [GradeItem] = []
    @Published var average: Double = 0
    @Published var isLoading = true

    init() {
        Task { await loadGrades() }
    }

    func loadGrades() async {
        isLoading = true

        try? await Task.sleep(nanoseconds: 300_000_000)

        grades = [
            GradeItem(id: 1, subject: "Matemáticas", score: 88, color: .green),
            GradeItem(id: 2, subject: "Comunicación", score: 92, color: .blue),
            GradeItem(id: 3, subject: "Ciencias", score: 85, color: .purple),
            GradeItem(id: 4, subject: "Historia", score: 78, color: .orange),
            GradeItem(id: 5, subject: "Inglés", score: 90, color: .cyan),
        ]
        average = grades.map(\.score).reduce(0, +) / Double(grades.count)
        isLoading = false
    }
}

struct GradeItem: Identifiable {
    let id: Int
    let subject: String
    let score: Double
    let color: Color
}

@MainActor
class PaymentsViewModel: ObservableObject {
    @Published var payments: [PaymentItem] = []
    @Published var totalPending: Double = 0
    @Published var pendingCount: Int = 0
    @Published var isLoading = true

    init() {
        Task { await loadPayments() }
    }

    func loadPayments() async {
        isLoading = true

        try? await Task.sleep(nanoseconds: 300_000_000)

        payments = [
            PaymentItem(id: 1, concept: "Colegiatura Enero", amount: 750, status: .pending),
            PaymentItem(id: 2, concept: "Colegiatura Febrero", amount: 750, status: .pending),
            PaymentItem(id: 3, concept: "Colegiatura Diciembre", amount: 750, status: .paid),
            PaymentItem(id: 4, concept: "Colegiatura Noviembre", amount: 750, status: .paid),
        ]

        let pending = payments.filter { $0.status == .pending }
        totalPending = pending.map(\.amount).reduce(0, +)
        pendingCount = pending.count
        isLoading = false
    }
}

struct PaymentItem: Identifiable {
    let id: Int
    let concept: String
    let amount: Double
    let status: PaymentItemStatus

    var amountFormatted: String {
        "Q \(String(format: "%.2f", amount))"
    }
}

enum PaymentItemStatus {
    case pending, paid

    var text: String {
        switch self {
        case .pending: return "Pendiente"
        case .paid: return "Pagado"
        }
    }

    var color: Color {
        switch self {
        case .pending: return .orange
        case .paid: return .green
        }
    }
}

// MARK: - Student Model for iOS
struct Student: Identifiable {
    let id: Int
    let firstName: String
    let lastName: String
    let grade: String
    let section: String

    var fullName: String { "\(firstName) \(lastName)" }
    var gradeSection: String { "\(grade) - \(section)" }
    var initials: String {
        let f = firstName.first.map(String.init) ?? ""
        let l = lastName.first.map(String.init) ?? ""
        return "\(f)\(l)"
    }
}
