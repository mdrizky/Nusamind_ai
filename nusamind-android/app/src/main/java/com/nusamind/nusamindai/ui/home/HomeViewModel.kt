package com.nusamind.nusamindai.ui.home

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusamind.nusamindai.domain.model.Business
import com.nusamind.nusamindai.domain.model.BusinessInsight
import com.nusamind.nusamindai.domain.model.TransactionSummary
import com.nusamind.nusamindai.domain.usecase.AuthUseCase
import com.nusamind.nusamindai.domain.usecase.BusinessUseCase
import com.nusamind.nusamindai.domain.usecase.FinanceUseCase
import com.nusamind.nusamindai.domain.usecase.InsightUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class HomeUiState(
    val isLoading: Boolean = true,
    val business: Business? = null,
    val insight: BusinessInsight? = null,
    val summary: TransactionSummary? = null,
    val userName: String = "",
    val error: String? = null,
)

@HiltViewModel
class HomeViewModel @Inject constructor(
    private val authUseCase: AuthUseCase,
    private val businessUseCase: BusinessUseCase,
    private val financeUseCase: FinanceUseCase,
    private val insightUseCase: InsightUseCase,
) : ViewModel() {

    private val _state = MutableStateFlow(HomeUiState())
    val state: StateFlow<HomeUiState> = _state

    init {
        loadHome()
    }

    fun loadHome() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            try {
                val business = businessUseCase.getMyBusiness().getOrNull()
                val insight = insightUseCase.getLatestInsight().getOrNull()
                val txResult = financeUseCase.getTransactions("today", null).getOrNull()
                _state.value = HomeUiState(
                    isLoading = false,
                    business = business,
                    insight = insight,
                    summary = txResult?.second,
                )
            } catch (e: Exception) {
                _state.value = HomeUiState(isLoading = false, error = e.message)
            }
        }
    }

    fun logout() {
        viewModelScope.launch { authUseCase.logout() }
    }
}
