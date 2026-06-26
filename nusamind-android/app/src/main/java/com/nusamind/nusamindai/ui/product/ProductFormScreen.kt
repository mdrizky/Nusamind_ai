package com.nusamind.nusamindai.ui.product

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.ImeAction
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.nusamind.nusamindai.ui.theme.HijauTosca

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProductFormScreen(
    onBack: () -> Unit,
    viewModel: ProductViewModel = hiltViewModel(),
) {
    val state by viewModel.formState.collectAsState()
    var name by remember { mutableStateOf(state.product?.name ?: "") }
    var price by remember { mutableStateOf(state.product?.price?.toString() ?: "") }
    var stock by remember { mutableStateOf(state.product?.stock?.toString() ?: "") }
    var description by remember { mutableStateOf(state.product?.description ?: "") }

    LaunchedEffect(state.success) { if (state.success) onBack() }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(if (state.product?.id != null) "Edit Produk" else "Tambah Produk") },
                navigationIcon = { IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, contentDescription = "Back") } },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = HijauTosca, titleContentColor = androidx.compose.ui.graphics.Color.White, navigationIconContentTint = androidx.compose.ui.graphics.Color.White),
            )
        },
    ) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
        ) {
            OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("Nama Produk") },
                keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next), modifier = Modifier.fillMaxWidth(), singleLine = true)

            Spacer(Modifier.height(16.dp))

            OutlinedTextField(value = price, onValueChange = { price = it }, label = { Text("Harga (Rp)") },
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number, imeAction = ImeAction.Next),
                modifier = Modifier.fillMaxWidth(), singleLine = true)

            Spacer(Modifier.height(16.dp))

            OutlinedTextField(value = stock, onValueChange = { stock = it }, label = { Text("Stok (opsional)") },
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number, imeAction = ImeAction.Next),
                modifier = Modifier.fillMaxWidth(), singleLine = true)

            Spacer(Modifier.height(16.dp))

            OutlinedTextField(value = description, onValueChange = { description = it }, label = { Text("Deskripsi (opsional)") },
                modifier = Modifier.fillMaxWidth(), minLines = 3)

            if (state.error != null) {
                Spacer(Modifier.height(8.dp))
                Text(state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
            }

            Spacer(Modifier.height(24.dp))

            Button(onClick = { viewModel.saveProduct(state.product?.id, name.trim(), price.trim(), stock.trim(), description.trim().ifEmpty { null }) },
                modifier = Modifier.fillMaxWidth().height(50.dp), enabled = !state.isSaving) {
                if (state.isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary)
                else Text("Simpan")
            }
        }
    }
}
