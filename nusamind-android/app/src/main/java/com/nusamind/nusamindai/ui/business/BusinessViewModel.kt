package com.nusamind.nusamindai.ui.business

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusamind.nusamindai.domain.model.Business
import com.nusamind.nusamindai.domain.usecase.BusinessUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class BusinessUiState(
    val isLoading: Boolean = true,
    val business: Business? = null,
    val isSaving: Boolean = false,
    val error: String? = null,
    val successMessage: String? = null,
)

@HiltViewModel
class BusinessViewModel @Inject constructor(
    private val businessUseCase: BusinessUseCase,
) : ViewModel() {

    private val _state = MutableStateFlow(BusinessUiState())
    val state: StateFlow<BusinessUiState> = _state

    init {
        load()
    }

    fun load() {
        viewModelScope.launch {
            _state.value = BusinessUiState(isLoading = true)
            val result = businessUseCase.getMyBusiness()
            _state.value = BusinessUiState(isLoading = false, business = result.getOrNull())
        }
    }

    fun save(name: String, city: String, description: String?) {
        viewModelScope.launch {
            _state.value = _state.value.copy(isSaving = true, error = null, successMessage = null)
            val result = if (_state.value.business?.id != null) {
                businessUseCase.updateBusiness(name, city, description)
            } else {
                businessUseCase.createBusiness(name, 1, city, description)
            }
            result.fold(
                onSuccess = {
                    _state.value = _state.value.copy(isSaving = false, business = it, successMessage = "Profil usaha tersimpan")
                },
                onFailure = { _state.value = _state.value.copy(isSaving = false, error = it.message) },
            )
        }
    }
}
