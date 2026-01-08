# Add project specific ProGuard rules here.

# Keep Kotlin Serialization
-keepattributes *Annotation*, InnerClasses
-dontnote kotlinx.serialization.AnnotationsKt

-keepclassmembers class kotlinx.serialization.json.** { *** Companion; }
-keepclasseswithmembers class kotlinx.serialization.json.** {
    kotlinx.serialization.KSerializer serializer(...);
}

-keep,includedescriptorclasses class com.oxford.**$$serializer { *; }
-keepclassmembers class com.oxford.** {
    *** Companion;
}
-keepclasseswithmembers class com.oxford.** {
    kotlinx.serialization.KSerializer serializer(...);
}

# Keep Ktor
-keep class io.ktor.** { *; }
-keep class kotlinx.coroutines.** { *; }
-dontwarn kotlinx.atomicfu.**
-dontwarn io.netty.**
-dontwarn com.typesafe.**
-dontwarn org.slf4j.**

# Keep DTOs
-keep class com.oxford.shared.data.model.** { *; }
-keep class com.oxford.shared.domain.entity.** { *; }

# Keep ViewModels
-keep class com.oxford.app.viewmodel.** { *; }

# Compose
-dontwarn androidx.compose.**

# General
-keepattributes Signature
-keepattributes SourceFile,LineNumberTable
-renamesourcefileattribute SourceFile
