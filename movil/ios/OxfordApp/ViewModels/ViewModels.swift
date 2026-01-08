import Combine
import SwiftUI

// MARK: - View Models for iOS

@MainActor
class HomeViewModel: ObservableObject {
    @Published var student: StudentModel?
    @Published var pendingTasks: [TaskItemModel] = []
    @Published var unreadCount: Int = 0
    @Published var isLoading = true
    @Published var error: String?

    private let api = OxfordAPIClient.shared

    init() {
        Swift.Task { await loadData() }
    }

    func loadData() async {
        isLoading = true
        error = nil

        do {
            // In production, these would be real API calls
            // Simulating data for demo
            try await Swift.Task.sleep(nanoseconds: 500_000_000)

            pendingTasks = [
                TaskItemModel(
                    id: 1, title: "Ejercicios de Matemáticas", subject: "Matemáticas",
                    dueDate: "Mañana", color: .green),
                TaskItemModel(
                    id: 2, title: "Lectura Cap. 5", subject: "Comunicación", dueDate: "En 3 días",
                    color: .blue),
                TaskItemModel(
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

struct TaskItemModel: Identifiable {
    let id: Int
    let title: String
    let subject: String
    let dueDate: String
    let color: Color
}

@MainActor
class TasksViewModel: ObservableObject {
    @Published var tasks: [TaskItemModel] = []
    @Published var selectedTab: Int = 0
    @Published var isLoading = true
    @Published var error: String?

    init() {
        Swift.Task { await loadTasks() }
    }

    func loadTasks() async {
        isLoading = true

        try? await Swift.Task.sleep(nanoseconds: 300_000_000)

        tasks = [
            TaskItemModel(
                id: 1, title: "Ejercicios p.45", subject: "Matemáticas", dueDate: "Mañana",
                color: .green),
            TaskItemModel(
                id: 2, title: "Lectura Cap. 5", subject: "Comunicación", dueDate: "En 3 días",
                color: .blue),
            TaskItemModel(
                id: 3, title: "Proyecto Ciencias", subject: "Ciencias", dueDate: "En 1 semana",
                color: .purple),
            TaskItemModel(
                id: 4, title: "Ensayo Historia", subject: "Historia", dueDate: "En 2 días",
                color: .orange),
            TaskItemModel(
                id: 5, title: "Vocabulario", subject: "Inglés", dueDate: "En 4 días", color: .cyan),
        ]
        isLoading = false
    }
}

@MainActor
class GradesViewModel: ObservableObject {
    @Published var grades: [GradeItemModel] = []
    @Published var average: Double = 0
    @Published var isLoading = true

    init() {
        Swift.Task { await loadGrades() }
    }

    func loadGrades() async {
        isLoading = true

        try? await Swift.Task.sleep(nanoseconds: 300_000_000)

        grades = [
            GradeItemModel(id: 1, subject: "Matemáticas", score: 88, color: .green),
            GradeItemModel(id: 2, subject: "Comunicación", score: 92, color: .blue),
            GradeItemModel(id: 3, subject: "Ciencias", score: 85, color: .purple),
            GradeItemModel(id: 4, subject: "Historia", score: 78, color: .orange),
            GradeItemModel(id: 5, subject: "Inglés", score: 90, color: .cyan),
        ]
        average = grades.map(\.score).reduce(0, +) / Double(grades.count)
        isLoading = false
    }
}

struct GradeItemModel: Identifiable {
    let id: Int
    let subject: String
    let score: Double
    let color: Color
}

@MainActor
class PaymentsViewModel: ObservableObject {
    @Published var payments: [PaymentItemModel] = []
    @Published var totalPending: Double = 0
    @Published var pendingCount: Int = 0
    @Published var isLoading = true

    init() {
        Swift.Task { await loadPayments() }
    }

    func loadPayments() async {
        isLoading = true

        try? await Swift.Task.sleep(nanoseconds: 300_000_000)

        payments = [
            PaymentItemModel(id: 1, concept: "Colegiatura Enero", amount: 750, status: .pending),
            PaymentItemModel(id: 2, concept: "Colegiatura Febrero", amount: 750, status: .pending),
            PaymentItemModel(id: 3, concept: "Colegiatura Diciembre", amount: 750, status: .paid),
            PaymentItemModel(id: 4, concept: "Colegiatura Noviembre", amount: 750, status: .paid),
        ]

        let pending = payments.filter { $0.status == .pending }
        totalPending = pending.map(\.amount).reduce(0, +)
        pendingCount = pending.count
        isLoading = false
    }
}

struct PaymentItemModel: Identifiable {
    let id: Int
    let concept: String
    let amount: Double
    let status: PaymentStatus

    var amountFormatted: String {
        "Q \(String(format: "%.2f", amount))"
    }
}

enum PaymentStatus {
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
struct StudentModel: Identifiable {
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
