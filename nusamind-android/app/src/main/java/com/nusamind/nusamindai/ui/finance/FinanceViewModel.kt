package com.nusamind.nusamindai.ui.finance

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusamind.nusamindai.domain.model.Transaction
import com.nusamind.nusamindai.domain.model.TransactionSummary
import com.nusamind.nusamindai.domain.usecase.FinanceUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class FinanceUiState(
    val isLoading: Boolean = false,
    val extracted: List<Transaction> = emptyList(),
    val transactions: List<Transaction> = emptyList(),
    val summary: TransactionSummary = TransactionSummary(0, 0, 0),
    val isExtracting: Boolean = false,
    val isSaving: Boolean = false,
    val error: String? = null,
    val successMessage: String? = null,
    val activeTab: Int = 0,
)

@HiltViewModel
class FinanceViewModel @Inject constructor(
    private val financeUseCase: FinanceUseCase,
) : ViewModel() {

    private val _state = MutableStateFlow(FinanceUiState())
    val state: StateFlow<FinanceUiState> = _state

    init {
        loadTransactions()
    }

    fun loadTransactions(filter: String? = "today") {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            financeUseCase.getTransactions(filter, null).fold(
                onSuccess = { (tx, summary) ->
                    _state.value = _state.value.copy(isLoading = false, transactions = tx, summary = summary)
                },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) },
            )
        }
    }

    fun extract(inputText: String) {
        viewModelScope.launch {
            _state.value = _state.value.copy(isExtracting = true, error = null)
            financeUseCase.extractTransactions(inputText).fold(
                onSuccess = { _state.value = _state.value.copy(isExtracting = false, extracted = it) },
                onFailure = { _state.value = _state.value.copy(isExtracting = false, error = it.message) },
            )
        }
    }

    fun confirmSave() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isSaving = true)
            val items = state.value.extracted.map { tx ->
                com.nusamind.nusamindai.data.api.dto.TransactionItemRequest(
                    type = tx.type,
                    itemName = tx.itemName,
                    quantity = tx.quantity,
                    amount = tx.amount,
                    productId = tx.productId,
                    source = "ai_text",
                )
            }
            financeUseCase.storeTransactions(items).fold(
                onSuccess = {
                    _state.value = _state.value.copy(isSaving = false, extracted = emptyList(), successMessage = "Transaksi tersimpan!")
                    loadTransactions()
                },
                onFailure = { _state.value = _state.value.copy(isSaving = false, error = it.message) },
            )
        }
    }

    fun setTab(tab: Int) {
        _state.value = _state.value.copy(activeTab = tab)
    }
}
