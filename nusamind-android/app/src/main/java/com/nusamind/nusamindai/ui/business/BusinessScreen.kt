package com.nusamind.nusamindai.ui.business

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
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.nusamind.nusamindai.ui.theme.HijauTosca

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BusinessScreen(
    onBack: () -> Unit,
    viewModel: BusinessViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()
    var name by remember(state.business) { mutableStateOf(state.business?.businessName ?: "") }
    var city by remember(state.business) { mutableStateOf(state.business?.city ?: "") }
    var description by remember(state.business) { mutableStateOf(state.business?.description ?: "") }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Profil Usaha") },
                navigationIcon = { IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, contentDescription = "Back") } },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = HijauTosca, titleContentColor = androidx.compose.ui.graphics.Color.White, navigationIconContentTint = androidx.compose.ui.graphics.Color.White),
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
                Box(Modifier.fillMaxSize()) { CircularProgressIndicator() }
                return@Column
            }

            OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("Nama Usaha") },
                keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next), modifier = Modifier.fillMaxWidth(), singleLine = true)

            Spacer(Modifier.height(16.dp))

            OutlinedTextField(value = city, onValueChange = { city = it }, label = { Text("Kota") },
                keyboardOptions = KeyboardOptions(imeAction = ImeAction.Next), modifier = Modifier.fillMaxWidth(), singleLine = true)

            Spacer(Modifier.height(16.dp))

            OutlinedTextField(value = description, onValueChange = { description = it }, label = { Text("Deskripsi") },
                modifier = Modifier.fillMaxWidth(), minLines = 3)

            Spacer(Modifier.height(8.dp))

            if (state.error != null) {
                Text(state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
                Spacer(Modifier.height(8.dp))
            }

            if (state.successMessage != null) {
                Text(state.successMessage!!, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.bodySmall)
                Spacer(Modifier.height(8.dp))
            }

            Spacer(Modifier.height(16.dp))

            Button(onClick = { viewModel.save(name.trim(), city.trim(), description.trim().ifEmpty { null }) },
                modifier = Modifier.fillMaxWidth().height(50.dp), enabled = !state.isSaving) {
                if (state.isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary)
                else Text("Simpan")
            }
        }
    }
}
