package com.nusamind.nusamindai.ui.content

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import coil.compose.rememberAsyncImagePainter
import com.nusamind.nusamindai.ui.theme.HijauTosca

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ContentScreen(
    onBack: () -> Unit,
    viewModel: ContentViewModel = hiltViewModel(),
) {
    val state by viewModel.state.collectAsState()
    val context = LocalContext.current

    val imagePicker = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent(),
    ) { uri: Uri? -> uri?.let { viewModel.setImage(it) } }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Buat Konten") },
                navigationIcon = { IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, contentDescription = "Back") } },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = HijauTosca, titleContentColor = androidx.compose.ui.graphics.Color.White, navigationIconContentTint = androidx.compose.ui.graphics.Color.White),
            )
        },
    ) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
        ) {
            // Image Picker
            if (state.imageUri != null) {
                Image(
                    painter = rememberAsyncImagePainter(state.imageUri),
                    contentDescription = "Preview",
                    modifier = Modifier.fillMaxWidth().height(200.dp),
                    contentScale = ContentScale.Crop,
                )
                Spacer(Modifier.height(8.dp))
            }

            OutlinedButton(onClick = { imagePicker.launch("image/*") }, modifier = Modifier.fillMaxWidth()) {
                Icon(Icons.Default.AddPhotoAlternate, contentDescription = null)
                Spacer(Modifier.width(8.dp))
                Text(if (state.imageUri != null) "Ganti Foto" else "Pilih Foto")
            }

            Spacer(Modifier.height(16.dp))

            // Style Selector
            Text("Gaya Bahasa:", style = MaterialTheme.typography.titleMedium)
            Spacer(Modifier.height(8.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                FilterChip(selected = state.selectedStyle == "gaul", onClick = { viewModel.setStyle("gaul") }, label = { Text("Gaul") })
                FilterChip(selected = state.selectedStyle == "formal", onClick = { viewModel.setStyle("formal") }, label = { Text("Formal") })
                FilterChip(selected = state.selectedStyle == "hard_selling", onClick = { viewModel.setStyle("hard_selling") }, label = { Text("Hard Selling") })
            }

            Spacer(Modifier.height(24.dp))

            Button(
                onClick = { viewModel.generate(context) },
                modifier = Modifier.fillMaxWidth().height(50.dp),
                enabled = !state.isLoading && state.imageUri != null,
            ) {
                if (state.isLoading) CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary)
                else Text("Generate Konten")
            }

            if (state.error != null) {
                Spacer(Modifier.height(8.dp))
                Text(state.error!!, color = MaterialTheme.colorScheme.error)
            }

            // Result
            if (state.result != null) {
                Spacer(Modifier.height(24.dp))
                Text("Hasil:", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)

                Spacer(Modifier.height(12.dp))

                Card(modifier = Modifier.fillMaxWidth()) {
                    Column(Modifier.padding(16.dp)) {
                        Text("Caption:", fontWeight = FontWeight.SemiBold)
                        Text(state.result!!.caption)
                        Spacer(Modifier.height(12.dp))

                        Text("Hashtags:", fontWeight = FontWeight.SemiBold)
                        Text(state.result!!.hashtags.joinToString(" "))
                        Spacer(Modifier.height(12.dp))

                        Text("Template WA:", fontWeight = FontWeight.SemiBold)
                        Text(state.result!!.whatsappTemplate)
                    }
                }

                Spacer(Modifier.height(16.dp))

                OutlinedButton(onClick = { viewModel.reset() }, modifier = Modifier.fillMaxWidth()) {
                    Text("Buat Lagi")
                }
            }
        }
    }
}
