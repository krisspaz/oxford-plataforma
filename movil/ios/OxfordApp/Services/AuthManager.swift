import Combine
import SwiftUI

// MARK: - Auth Manager
class AuthManager: ObservableObject {
    @Published var isAuthenticated = false
    @Published var currentUser: User?
    @Published var isLoading = false
    @Published var errorMessage: String?

    private let apiClient = OxfordAPIClient.shared

    func login(email: String, password: String) async {
        await MainActor.run { isLoading = true }

        do {
            let response = try await apiClient.login(email: email, password: password)
            await MainActor.run {
                self.currentUser = response.user
                self.isAuthenticated = true
                self.isLoading = false
            }
        } catch {
            await MainActor.run {
                self.errorMessage = error.localizedDescription
                self.isLoading = false
            }
        }
    }

    func logout() {
        apiClient.clearToken()
        isAuthenticated = false
        currentUser = nil
    }
}

// MARK: - API Client
class OxfordAPIClient {
    static let shared = OxfordAPIClient()

    private let baseURL = "https://api.oxford.edu.gt"
    private var authToken: String?

    private init() {}

    func setToken(_ token: String) {
        authToken = token
    }

    func clearToken() {
        authToken = nil
    }

    func login(email: String, password: String) async throws -> LoginResponse {
        let url = URL(string: "\(baseURL)/api/login_check")!
        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")

        let body = ["email": email, "password": password]
        request.httpBody = try JSONEncoder().encode(body)

        let (data, _) = try await URLSession.shared.data(for: request)
        let response = try JSONDecoder().decode(LoginResponse.self, from: data)

        setToken(response.token)
        return response
    }

    func get<T: Decodable>(_ endpoint: String) async throws -> T {
        let url = URL(string: "\(baseURL)\(endpoint)")!
        var request = URLRequest(url: url)

        if let token = authToken {
            request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }

        let (data, _) = try await URLSession.shared.data(for: request)
        return try JSONDecoder().decode(T.self, from: data)
    }
}

// MARK: - Models
struct User: Codable, Identifiable {
    let id: Int
    let email: String
    let firstName: String?
    let lastName: String?
    let roles: [String]

    var fullName: String {
        "\(firstName ?? "") \(lastName ?? "")".trimmingCharacters(in: .whitespaces)
    }
}

struct LoginResponse: Codable {
    let token: String
    let refreshToken: String?
    let user: User?
}

struct Task: Codable, Identifiable {
    let id: Int
    let title: String
    let description: String?
    let dueDate: String
    let type: String
    let status: String
    let points: Int
    let subjectName: String?
    let teacherName: String?
}

struct GradeRecord: Codable, Identifiable {
    let id: Int
    let subjectName: String
    let bimesterName: String
    let score: Double
    let maxScore: Double

    var percentage: Double { (score / maxScore) * 100 }
}

struct AttendanceRecord: Codable, Identifiable {
    let id: Int
    let date: String
    let status: String
    let subjectName: String?
}

struct Payment: Codable, Identifiable {
    let id: Int
    let amount: Double
    let concept: String
    let status: String
    let dueDate: String
    let paidAt: String?
}

struct Notification: Codable, Identifiable {
    let id: Int
    let title: String
    let message: String
    let type: String
    let isRead: Bool
    let createdAt: String
}
