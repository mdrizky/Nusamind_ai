package com.nusamind.nusamindai.ui.finance

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.nusamind.nusamindai.ui.theme.HijauTosca
import com.nusamind.nusamindai.ui.theme.KuningEmas
import java.text.NumberFormat
import java.util.Locale

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun FinanceScreen(
    onBack: () -> Unit,
    viewModel: FinanceViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()
    var inputText by remember { mutableStateOf("") }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Pencatatan Keuangan") },
                navigationIcon = { IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, contentDescription = "Back") } },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = HijauTosca, titleContentColor = androidx.compose.ui.graphics.Color.White, navigationIconContentTint = androidx.compose.ui.graphics.Color.White),
            )
        },
    ) { padding ->
        Column(Modifier.fillMaxSize().padding(padding)) {
            // Tab Row
            TabRow(selectedTabIndex = state.activeTab) {
                Tab(selected = state.activeTab == 0, onClick = { viewModel.setTab(0) }, text = { Text("Catat") })
                Tab(selected = state.activeTab == 1, onClick = { viewModel.setTab(1) }, text = { Text("Riwayat") })
            }

            when (state.activeTab) {
                0 -> TabInput(
                    inputText = inputText,
                    onInputChange = { inputText = it },
                    onExtract = { viewModel.extract(inputText.trim()) },
                    onConfirm = { viewModel.confirmSave() },
                    state = state,
                )
                1 -> TabHistory(state = state)
            }
        }
    }
}

@Composable
private fun TabInput(
    inputText: String,
    onInputChange: (String) -> Unit,
    onExtract: () -> Unit,
    onConfirm: () -> Unit,
    state: FinanceUiState,
) {
    Column(Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(16.dp)) {
        Text("Ceritakan transaksi hari ini:", style = MaterialTheme.typography.bodyLarge)
        Spacer(Modifier.height(8.dp))

        OutlinedTextField(
            value = inputText,
            onValueChange = onInputChange,
            modifier = Modifier.fillMaxWidth(),
            minLines = 4,
            placeholder = { Text("Contoh: Hari ini laku 5 porsi ayam geprek total 75 ribu, terus beli minyak goreng 20 ribu") },
            keyboardOptions = KeyboardOptions(imeAction = ImeAction.Done),
        )

        Spacer(Modifier.height(12.dp))

        Button(
            onClick = onExtract,
            modifier = Modifier.fillMaxWidth().height(50.dp),
            enabled = !state.isExtracting && inputText.trim().length >= 3,
        ) {
            if (state.isExtracting) CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary)
            else Text("Ekstrak dengan AI")
        }

        if (state.error != null) {
            Spacer(Modifier.height(8.dp))
            Text(state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
        }

        if (state.extracted.isNotEmpty()) {
            Spacer(Modifier.height(24.dp))
            Text("Hasil Ekstraksi:", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)

            state.extracted.forEach { tx ->
                Spacer(Modifier.height(8.dp))
                Card(modifier = Modifier.fillMaxWidth()) {
                    Row(Modifier.padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            if (tx.type == "pemasukan") Icons.Default.TrendingUp else Icons.Default.TrendingDown,
                            contentDescription = null,
                            tint = if (tx.type == "pemasukan") HijauTosca else MaterialTheme.colorScheme.error,
                        )
                        Spacer(Modifier.width(12.dp))
                        Column(Modifier.weight(1f)) {
                            Text(tx.itemName, fontWeight = FontWeight.SemiBold)
                            if (tx.quantity != null) Text("${tx.quantity} x ${NumberFormat.getNumberInstance(Locale("id", "ID")).format(tx.amount / tx.quantity)}", style = MaterialTheme.typography.bodySmall)
                        }
                        Text("Rp ${NumberFormat.getNumberInstance(Locale("id", "ID")).format(tx.amount)}", fontWeight = FontWeight.Bold)
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            Button(onClick = onConfirm, modifier = Modifier.fillMaxWidth().height(50.dp), enabled = !state.isSaving,
                colors = ButtonDefaults.buttonColors(containerColor = KuningEmas)) {
                if (state.isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary)
                else Text("Simpan Semua Transaksi")
            }
        }

        if (state.successMessage != null) {
            Spacer(Modifier.height(8.dp))
            Text(state.successMessage!!, color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.SemiBold)
        }
    }
}

@Composable
private fun TabHistory(state: FinanceUiState) {
    Column(Modifier.fillMaxSize().padding(16.dp)) {
        // Summary Card
        Card(modifier = Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = HijauTosca)) {
            Column(Modifier.padding(16.dp)) {
                Text("Ringkasan Hari Ini", color = androidx.compose.ui.graphics.Color.White)
                Spacer(Modifier.height(4.dp))
                Text("Pemasukan: Rp ${NumberFormat.getNumberInstance(Locale("id", "ID")).format(state.summary.totalIncome)}",
                    color = androidx.compose.ui.graphics.Color.White)
                Text("Pengeluaran: Rp ${NumberFormat.getNumberInstance(Locale("id", "ID")).format(state.summary.totalExpense)}",
                    color = androidx.compose.ui.graphics.Color.White)
                Text("Saldo: Rp ${NumberFormat.getNumberInstance(Locale("id", "ID")).format(state.summary.balance)}",
                    color = androidx.compose.ui.graphics.Color.White, fontWeight = FontWeight.Bold)
            }
        }

        Spacer(Modifier.height(16.dp))

        if (state.isLoading) {
            Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) { CircularProgressIndicator() }
        } else if (state.transactions.isEmpty()) {
            Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Icon(Icons.Default.ReceiptLong, contentDescription = null, modifier = Modifier.size(48.dp), tint = MaterialTheme.colorScheme.onSurfaceVariant)
                    Spacer(Modifier.height(8.dp))
                    Text("Belum ada transaksi hari ini", color = MaterialTheme.colorScheme.onSurfaceVariant)
                }
            }
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                items(state.transactions) { tx ->
                    Card(modifier = Modifier.fillMaxWidth()) {
                        Row(Modifier.padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                            Column(Modifier.weight(1f)) {
                                Text(tx.itemName, fontWeight = FontWeight.SemiBold)
                                Text(tx.type.replaceFirstChar { it.uppercase() }, style = MaterialTheme.typography.bodySmall)
                            }
                            Text("Rp ${NumberFormat.getNumberInstance(Locale("id", "ID")).format(tx.amount)}",
                                fontWeight = FontWeight.Bold, color = if (tx.type == "pemasukan") HijauTosca else MaterialTheme.colorScheme.error)
                        }
                    }
                }
            }
        }
    }
}
