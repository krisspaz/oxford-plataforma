package com.oxford.app.ui.theme

import android.os.Build
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext

// Oxford Colors
val OxfordBlue = Color(0xFF1E3A5F)
val OxfordBlueLight = Color(0xFF4A7AB8)
val OxfordBlueDark = Color(0xFF0D1F33)
val OxfordGold = Color(0xFFD4AF37)
val OxfordRed = Color(0xFFC41E3A)

private val LightColorScheme = lightColorScheme(
    primary = OxfordBlue,
    onPrimary = Color.White,
    primaryContainer = OxfordBlueLight,
    onPrimaryContainer = Color.White,
    secondary = OxfordGold,
    onSecondary = Color.Black,
    tertiary = OxfordRed,
    background = Color(0xFFF5F7FA),
    surface = Color.White,
    onSurface = Color(0xFF1E1E1E)
)

private val DarkColorScheme = darkColorScheme(
    primary = OxfordBlueLight,
    onPrimary = Color.White,
    primaryContainer = OxfordBlue,
    onPrimaryContainer = Color.White,
    secondary = OxfordGold,
    onSecondary = Color.Black,
    tertiary = OxfordRed,
    background = Color(0xFF121212),
    surface = Color(0xFF1E1E1E),
    onSurface = Color.White
)

@Composable
fun OxfordTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    dynamicColor: Boolean = false,
    content: @Composable () -> Unit
) {
    val colorScheme = when {
        dynamicColor && Build.VERSION.SDK_INT >= Build.VERSION_CODES.S -> {
            val context = LocalContext.current
            if (darkTheme) dynamicDarkColorScheme(context)
            else dynamicLightColorScheme(context)
        }
        darkTheme -> DarkColorScheme
        else -> LightColorScheme
    }

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography,
        content = content
    )
}

val Typography = Typography()
