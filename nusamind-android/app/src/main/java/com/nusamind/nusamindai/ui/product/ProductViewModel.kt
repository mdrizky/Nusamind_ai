package com.nusamind.nusamindai.ui.product

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.nusamind.nusamindai.domain.model.Product
import com.nusamind.nusamindai.domain.usecase.ProductUseCase
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class ProductListState(
    val isLoading: Boolean = true,
    val products: List<Product> = emptyList(),
    val error: String? = null,
)

data class ProductFormState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val product: Product? = null,
    val error: String? = null,
    val success: Boolean = false,
)

@HiltViewModel
class ProductViewModel @Inject constructor(
    private val productUseCase: ProductUseCase,
) : ViewModel() {

    private val _listState = MutableStateFlow(ProductListState())
    val listState: StateFlow<ProductListState> = _listState

    private val _formState = MutableStateFlow(ProductFormState())
    val formState: StateFlow<ProductFormState> = _formState

    fun loadProducts() {
        viewModelScope.launch {
            _listState.value = ProductListState(isLoading = true)
            productUseCase.getProducts().fold(
                onSuccess = { _listState.value = ProductListState(isLoading = false, products = it) },
                onFailure = { _listState.value = ProductListState(isLoading = false, error = it.message) },
            )
        }
    }

    fun loadProduct(id: Int) {
        viewModelScope.launch {
            _formState.value = ProductFormState(isLoading = true)
            val products = productUseCase.getProducts().getOrNull() ?: emptyList()
            val product = products.find { it.id == id }
            _formState.value = ProductFormState(product = product)
        }
    }

    fun saveProduct(id: Int?, name: String, price: String, stock: String, description: String?) {
        viewModelScope.launch {
            _formState.value = _formState.value.copy(isSaving = true, error = null)
            val priceInt = price.toIntOrNull() ?: run {
                _formState.value = _formState.value.copy(isSaving = false, error = "Harga harus angka")
                return@launch
            }
            val stockInt = stock.toIntOrNull()

            val result = if (id != null && id > 0) {
                productUseCase.updateProduct(id, name, priceInt, stockInt, description)
            } else {
                productUseCase.createProduct(name, priceInt, stockInt, description)
            }
            result.fold(
                onSuccess = { _formState.value = _formState.value.copy(isSaving = false, success = true) },
                onFailure = { _formState.value = _formState.value.copy(isSaving = false, error = it.message) },
            )
        }
    }

    fun deleteProduct(id: Int) {
        viewModelScope.launch {
            productUseCase.deleteProduct(id)
            loadProducts()
        }
    }
}
