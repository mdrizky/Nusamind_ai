package com.nusamind.nusamindai.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable

private val LightColorScheme = lightColorScheme(
    primary = HijauTosca,
    onPrimary = Putih,
    primaryContainer = HijauToscaLight,
    secondary = KuningEmas,
    onSecondary = TextPrimary,
    secondaryContainer = KuningEmasLight,
    background = Background,
    onBackground = TextPrimary,
    surface = Surface,
    onSurface = TextPrimary,
    error = Error,
    onError = Putih,
)

@Composable
fun NusamindTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = LightColorScheme,
        typography = Typography,
        content = content,
    )
}
