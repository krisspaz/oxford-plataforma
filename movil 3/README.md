# Oxford Mobile Apps

Aplicaciones móviles para el sistema Oxford usando **Kotlin Multiplatform (KMP)**.

## Estructura

```
movil/
├── kotlin/           # Código compartido (KMP)
│   ├── src/
│   │   ├── commonMain/kotlin/com/oxford/shared/
│   │   │   ├── data/
│   │   │   │   ├── api/          # API Client
│   │   │   │   ├── model/        # DTOs
│   │   │   │   └── repository/   # Repositories
│   │   │   └── domain/
│   │   │       ├── entity/       # Domain Models
│   │   │       └── usecase/      # Business Logic
│   │   ├── androidMain/          # Android-specific
│   │   └── iosMain/              # iOS-specific
│   └── build.gradle.kts
│
├── android/          # App Android (Jetpack Compose)
│   └── app/
│       └── src/main/kotlin/com/oxford/app/
│           ├── MainActivity.kt
│           ├── navigation/
│           └── ui/
│               ├── screens/
│               └── theme/
│
└── ios/              # App iOS (SwiftUI)
    └── OxfordApp/
        ├── OxfordApp.swift
        ├── Services/
        └── Views/
```

## Características

### Android (Jetpack Compose)
- ✅ Login con diseño Oxford
- ✅ Dashboard con accesos rápidos
- ✅ Tareas y detalle
- ✅ Notas por materia
- ✅ Historial de asistencia
- ✅ Pagos pendientes
- ✅ Perfil y configuración
- ✅ Notificaciones
- ✅ Horario semanal

### iOS (SwiftUI)
- ✅ Login con gradiente Oxford
- ✅ Tab navigation
- ✅ Home con resumen
- ✅ Lista de tareas
- ✅ Notas con promedio
- ✅ Pagos
- ✅ Perfil con logout

## API Endpoints

Las apps se conectan al backend Symfony:

| Endpoint | Descripción |
|----------|-------------|
| POST `/api/login_check` | Autenticación |
| GET `/api/students/me` | Perfil estudiante |
| GET `/api/tasks/my-tasks` | Tareas del estudiante |
| GET `/api/grades/my-grades` | Notas |
| GET `/api/attendance/my-attendance` | Asistencia |
| GET `/api/payments/my-payments` | Pagos |
| GET `/api/notifications` | Notificaciones |

## Build

### Android
```bash
cd movil/android
./gradlew assembleDebug
```

### iOS
```bash
cd movil/ios
open OxfordApp.xcodeproj
# Build desde Xcode
```

## Requisitos

- Android: SDK 24+ (Android 7.0+)
- iOS: iOS 16.0+
- Kotlin: 1.9.22
- Gradle: 8.0+
- Xcode: 15.0+
