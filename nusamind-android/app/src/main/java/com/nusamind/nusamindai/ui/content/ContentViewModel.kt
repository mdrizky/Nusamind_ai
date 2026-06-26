package com.nusamind.nusamindai.ui.content

import android.content.Context
import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusamind.nusamindai.domain.model.ContentResult
import com.nusamind.nusamindai.domain.usecase.ContentUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class ContentUiState(
    val isLoading: Boolean = false,
    val imageUri: Uri? = null,
    val selectedStyle: String = "gaul",
    val selectedProductId: Int? = null,
    val result: ContentResult? = null,
    val error: String? = null,
)

@HiltViewModel
class ContentViewModel @Inject constructor(
    private val contentUseCase: ContentUseCase,
) : ViewModel() {

    private val _state = MutableStateFlow(ContentUiState())
    val state: StateFlow<ContentUiState> = _state

    fun setImage(uri: Uri) { _state.value = _state.value.copy(imageUri = uri) }

    fun setStyle(style: String) { _state.value = _state.value.copy(selectedStyle = style) }

    fun generate(context: Context) {
        val uri = _state.value.imageUri ?: return
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            try {
                val inputStream = context.contentResolver.openInputStream(uri)
                val bytes = inputStream?.readBytes() ?: throw Exception("Gagal baca gambar")
                inputStream.close()
                val fileName = "upload_${System.currentTimeMillis()}.jpg"
                contentUseCase.generateContent(bytes, fileName, _state.value.selectedStyle, _state.value.selectedProductId).fold(
                    onSuccess = { _state.value = _state.value.copy(isLoading = false, result = it) },
                    onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) },
                )
            } catch (e: Exception) {
                _state.value = _state.value.copy(isLoading = false, error = e.message)
            }
        }
    }

    fun reset() {
        _state.value = ContentUiState()
    }
}
