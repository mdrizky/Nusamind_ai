package com.nusamind.nusamindai.ui.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusamind.nusamindai.domain.usecase.AuthUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class AuthUiState(
    val isLoading: Boolean = false,
    val error: String? = null,
    val isSuccess: Boolean = false,
)

@HiltViewModel
class AuthViewModel @Inject constructor(
    private val authUseCase: AuthUseCase,
) : ViewModel() {

    private val _state = MutableStateFlow(AuthUiState())
    val state: StateFlow<AuthUiState> = _state

    fun register(name: String, email: String, password: String, passwordConfirmation: String) {
        viewModelScope.launch {
            _state.value = AuthUiState(isLoading = true)
            val result = authUseCase.register(name, email, password, passwordConfirmation)
            result.fold(
                onSuccess = { _state.value = AuthUiState(isSuccess = true) },
                onFailure = { _state.value = AuthUiState(error = it.message) },
            )
        }
    }

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _state.value = AuthUiState(isLoading = true)
            val result = authUseCase.login(email, password)
            result.fold(
                onSuccess = { _state.value = AuthUiState(isSuccess = true) },
                onFailure = { _state.value = AuthUiState(error = it.message) },
            )
        }
    }
}
