package com.nusamind.nusamindai.ui.home

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.nusamind.nusamindai.ui.theme.HijauTosca
import com.nusamind.nusamindai.ui.theme.KuningEmas

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(
    onNavigateToBusiness: () -> Unit,
    onNavigateToProducts: () -> Unit,
    onNavigateToFinance: () -> Unit,
    onNavigateToContent: () -> Unit,
    onNavigateToNotifications: () -> Unit,
    onLogout: () -> Unit,
    viewModel: HomeViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Nusamind AI", fontWeight = FontWeight.Bold) },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = HijauTosca, titleContentColor = androidx.compose.ui.graphics.Color.White),
                actions = {
                    IconButton(onClick = onNavigateToNotifications) {
                        Icon(Icons.Default.Notifications, contentDescription = "Notifikasi", tint = androidx.compose.ui.graphics.Color.White)
                    }
                    IconButton(onClick = {
                        viewModel.logout()
                        onLogout()
                    }) {
                        Icon(Icons.Default.Logout, contentDescription = "Logout", tint = androidx.compose.ui.graphics.Color.White)
                    }
                },
            )
        },
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
        ) {
            if (state.isLoading) {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
                return@Column
            }

            // Saldo Card
            Card(
                modifier = Modifier.fillMaxWidth(),
                colors = CardDefaults.cardColors(containerColor = HijauTosca),
            ) {
                Column(Modifier.padding(20.dp)) {
                    Text("Saldo Hari Ini", color = androidx.compose.ui.graphics.Color.White, style = MaterialTheme.typography.bodyMedium)
                    Spacer(Modifier.height(8.dp))
                    Text(
                                "Rp ${java.text.NumberFormat.getNumberInstance(java.util.Locale("id", "ID")).format(state.summary?.balance ?: 0)}",
                        color = androidx.compose.ui.graphics.Color.White,
                        style = MaterialTheme.typography.headlineLarge,
                        fontWeight = FontWeight.Bold,
                    )
                    Spacer(Modifier.height(12.dp))
                    Row(Modifier.fillMaxWidth()) {
                        Column(Modifier.weight(1f)) {
                            Text("Pemasukan", color = androidx.compose.ui.graphics.Color.White.copy(alpha = 0.8f), style = MaterialTheme.typography.bodySmall)
                            Text("Rp ${java.text.NumberFormat.getNumberInstance(java.util.Locale("id", "ID")).format(state.summary?.totalIncome ?: 0)}",
                                color = androidx.compose.ui.graphics.Color.White, fontWeight = FontWeight.SemiBold)
                        }
                        Column(Modifier.weight(1f)) {
                            Text("Pengeluaran", color = androidx.compose.ui.graphics.Color.White.copy(alpha = 0.8f), style = MaterialTheme.typography.bodySmall)
                            Text("Rp ${java.text.NumberFormat.getNumberInstance(java.util.Locale("id", "ID")).format(state.summary?.totalExpense ?: 0)}",
                                color = androidx.compose.ui.graphics.Color.White, fontWeight = FontWeight.SemiBold)
                        }
                    }
                }
            }

            Spacer(Modifier.height(24.dp))

            // Fitur Grid
            Text("Fitur", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
            Spacer(Modifier.height(12.dp))

            Row(Modifier.fillMaxWidth()) {
                MenuCard(
                    icon = Icons.Default.AccountBalance,
                    title = "Keuangan",
                    desc = "Catat transaksi pakai AI",
                    onClick = onNavigateToFinance,
                    modifier = Modifier.weight(1f),
                )
                Spacer(Modifier.width(12.dp))
                MenuCard(
                    icon = Icons.Default.Image,
                    title = "Konten",
                    desc = "Buat caption pakai AI",
                    onClick = onNavigateToContent,
                    modifier = Modifier.weight(1f),
                )
            }

            Spacer(Modifier.height(12.dp))

            Row(Modifier.fillMaxWidth()) {
                MenuCard(
                    icon = Icons.Default.Business,
                    title = "Usaha",
                    desc = "Profil usaha kamu",
                    onClick = onNavigateToBusiness,
                    modifier = Modifier.weight(1f),
                )
                Spacer(Modifier.width(12.dp))
                MenuCard(
                    icon = Icons.Default.Inventory2,
                    title = "Produk",
                    desc = "Kelola produk",
                    onClick = onNavigateToProducts,
                    modifier = Modifier.weight(1f),
                )
            }

            Spacer(Modifier.height(24.dp))

            // Business Briefing
            if (state.insight != null) {
                Text("Ringkasan Mingguan", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                Spacer(Modifier.height(12.dp))
                Card(modifier = Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = KuningEmas.copy(alpha = 0.15f))) {
                    Column(Modifier.padding(16.dp)) {
                        Text(state.insight!!.narrativeText ?: "Belum ada briefing", style = MaterialTheme.typography.bodyMedium)
                        if (state.insight!!.topProduct != null) {
                            Spacer(Modifier.height(8.dp))
                            Text("Produk terlaris: ${state.insight!!.topProduct}", fontWeight = FontWeight.SemiBold, style = MaterialTheme.typography.bodySmall)
                        }
                    }
                }
            }

            if (state.error != null) {
                Spacer(Modifier.height(16.dp))
                Text(state.error!!, color = MaterialTheme.colorScheme.error)
            }
        }
    }
}

@Composable
fun MenuCard(
    icon: ImageVector,
    title: String,
    desc: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
) {
    Card(onClick = onClick, modifier = modifier, colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface)) {
        Column(Modifier.padding(16.dp)) {
            Icon(icon, contentDescription = null, tint = HijauTosca, modifier = Modifier.size(32.dp))
            Spacer(Modifier.height(8.dp))
            Text(title, fontWeight = FontWeight.SemiBold, style = MaterialTheme.typography.titleMedium)
            Text(desc, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
        }
    }
}
