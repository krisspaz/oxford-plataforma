import SwiftUI

// MARK: - Reusable UI Components for iOS

// Loading View
struct LoadingView: View {
    var message = "Cargando..."

    var body: some View {
        VStack(spacing: 16) {
            ProgressView()
                .scaleEffect(1.5)
            Text(message)
                .foregroundColor(.gray)
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(Color(hex: "F5F7FA"))
    }
}

// Error View
struct ErrorView: View {
    let message: String
    let onRetry: () -> Void

    var body: some View {
        VStack(spacing: 16) {
            Image(systemName: "exclamationmark.triangle.fill")
                .font(.system(size: 48))
                .foregroundColor(.red)

            Text("¡Oops!")
                .font(.title2.bold())

            Text(message)
                .foregroundColor(.gray)
                .multilineTextAlignment(.center)
                .padding(.horizontal, 32)

            Button(action: onRetry) {
                Label("Reintentar", systemImage: "arrow.clockwise")
                    .padding(.horizontal, 24)
                    .padding(.vertical, 12)
                    .background(Color(hex: "1E3A5F"))
                    .foregroundColor(.white)
                    .cornerRadius(12)
            }
        }
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(Color(hex: "F5F7FA"))
    }
}

// Empty State View
struct EmptyStateView: View {
    let icon: String
    let title: String
    let message: String
    var actionLabel: String? = nil
    var onAction: (() -> Void)? = nil

    var body: some View {
        VStack(spacing: 20) {
            ZStack {
                Circle()
                    .fill(Color(hex: "1E3A5F").opacity(0.1))
                    .frame(width: 80, height: 80)

                Image(systemName: icon)
                    .font(.system(size: 32))
                    .foregroundColor(Color(hex: "1E3A5F"))
            }

            Text(title)
                .font(.title3.bold())

            Text(message)
                .foregroundColor(.gray)
                .multilineTextAlignment(.center)

            if let actionLabel = actionLabel, let onAction = onAction {
                Button(actionLabel, action: onAction)
                    .foregroundColor(Color(hex: "1E3A5F"))
            }
        }
        .padding(32)
    }
}

// Stat Card
struct StatCardView: View {
    let value: String
    let label: String
    let icon: String
    let color: Color

    var body: some View {
        VStack(spacing: 8) {
            Image(systemName: icon)
                .foregroundColor(.white.opacity(0.8))

            Text(value)
                .font(.title.bold())
                .foregroundColor(.white)

            Text(label)
                .font(.caption)
                .foregroundColor(.white.opacity(0.8))
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, 16)
        .background(color)
        .cornerRadius(16)
    }
}

// Oxford Card
struct OxfordCard<Content: View>: View {
    let content: () -> Content

    init(@ViewBuilder content: @escaping () -> Content) {
        self.content = content
    }

    var body: some View {
        content()
            .padding()
            .background(.white)
            .cornerRadius(16)
            .shadow(color: .black.opacity(0.05), radius: 10)
    }
}

// Gradient Header
struct GradientHeader: View {
    let title: String
    let subtitle: String?
    let initials: String

    init(title: String, subtitle: String? = nil, initials: String = "") {
        self.title = title
        self.subtitle = subtitle
        self.initials = initials
    }

    var body: some View {
        HStack {
            VStack(alignment: .leading, spacing: 4) {
                Text("¡Hola!")
                    .foregroundColor(.white.opacity(0.8))
                Text(title)
                    .font(.title2.bold())
                    .foregroundColor(.white)
                if let subtitle = subtitle {
                    Text(subtitle)
                        .font(.subheadline)
                        .foregroundColor(.white.opacity(0.8))
                }
            }

            Spacer()

            if !initials.isEmpty {
                ZStack {
                    Circle()
                        .fill(.white.opacity(0.2))
                        .frame(width: 60, height: 60)
                    Text(initials)
                        .font(.title3.bold())
                        .foregroundColor(.white)
                }
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
    }
}

// Badge
struct BadgeView: View {
    let count: Int

    var body: some View {
        if count > 0 {
            Text("\(count)")
                .font(.caption2.bold())
                .foregroundColor(.white)
                .padding(5)
                .background(.red)
                .clipShape(Circle())
        }
    }
}

// Success Toast
struct SuccessToast: View {
    let message: String
    @Binding var isPresented: Bool

    var body: some View {
        if isPresented {
            HStack(spacing: 12) {
                Image(systemName: "checkmark.circle.fill")
                    .foregroundColor(.green)
                Text(message)
            }
            .padding()
            .background(.white)
            .cornerRadius(12)
            .shadow(radius: 10)
            .transition(.move(edge: .top).combined(with: .opacity))
            .onAppear {
                DispatchQueue.main.asyncAfter(deadline: .now() + 2) {
                    withAnimation {
                        isPresented = false
                    }
                }
            }
        }
    }
}

// MARK: - View Modifiers

struct LoadingModifier: ViewModifier {
    let isLoading: Bool

    func body(content: Content) -> some View {
        ZStack {
            content
                .disabled(isLoading)
                .blur(radius: isLoading ? 2 : 0)

            if isLoading {
                Color.black.opacity(0.3)
                    .ignoresSafeArea()

                ProgressView()
                    .scaleEffect(1.5)
                    .padding(24)
                    .background(.white)
                    .cornerRadius(12)
            }
        }
    }
}

extension View {
    func loading(_ isLoading: Bool) -> some View {
        modifier(LoadingModifier(isLoading: isLoading))
    }

    func oxfordCard() -> some View {
        self
            .padding()
            .background(.white)
            .cornerRadius(16)
            .shadow(color: .black.opacity(0.05), radius: 10)
    }
}

#Preview {
    VStack {
        LoadingView()
    }
}
