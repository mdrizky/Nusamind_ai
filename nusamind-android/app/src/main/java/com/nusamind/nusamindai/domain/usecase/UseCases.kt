package com.nusamind.nusamindai.domain.usecase

import com.nusamind.nusamindai.data.api.dto.*
import com.nusamind.nusamindai.data.repository.NusamindRepository
import com.nusamind.nusamindai.domain.model.*
import javax.inject.Inject

class AuthUseCase @Inject constructor(private val repo: NusamindRepository) {
    suspend fun register(name: String, email: String, password: String, passwordConfirmation: String) =
        repo.register(RegisterRequest(name, email, password, passwordConfirmation))

    suspend fun login(email: String, password: String) =
        repo.login(LoginRequest(email, password))

    suspend fun logout() = repo.logout()
}

class BusinessUseCase @Inject constructor(private val repo: NusamindRepository) {
    suspend fun getMyBusiness() = repo.getMyBusiness()
    suspend fun createBusiness(name: String, categoryId: Int, city: String, desc: String?) =
        repo.createBusiness(name, categoryId, city, desc)
    suspend fun updateBusiness(name: String?, city: String?, desc: String?) =
        repo.updateBusiness(name, city, desc)
}

class ProductUseCase @Inject constructor(private val repo: NusamindRepository) {
    suspend fun getProducts() = repo.getProducts()
    suspend fun createProduct(name: String, price: Int, stock: Int?, desc: String?) =
        repo.createProduct(name, price, stock, desc)
    suspend fun updateProduct(id: Int, name: String?, price: Int?, stock: Int?, desc: String?) =
        repo.updateProduct(id, name, price, stock, desc)
    suspend fun deleteProduct(id: Int) = repo.deleteProduct(id)
}

class FinanceUseCase @Inject constructor(private val repo: NusamindRepository) {
    suspend fun extractTransactions(inputText: String) = repo.extractTransactions(inputText)
    suspend fun storeTransactions(items: List<TransactionItemRequest>) =
        repo.storeTransactions(items)
    suspend fun getTransactions(filter: String?, type: String?) =
        repo.getTransactions(filter, type)
}

class ContentUseCase @Inject constructor(private val repo: NusamindRepository) {
    suspend fun generateContent(imageBytes: ByteArray, fileName: String, style: String, productId: Int?) =
        repo.generateContent(imageBytes, fileName, style, productId)
}

class InsightUseCase @Inject constructor(private val repo: NusamindRepository) {
    suspend fun getLatestInsight() = repo.getLatestInsight()
}
