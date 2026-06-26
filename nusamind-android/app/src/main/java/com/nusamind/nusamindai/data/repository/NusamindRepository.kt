package com.nusamind.nusamindai.data.repository

import com.nusamind.nusamindai.data.api.ApiService
import com.nusamind.nusamindai.data.api.dto.*
import com.nusamind.nusamindai.data.local.TokenManager
import com.nusamind.nusamindai.domain.model.*
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.toRequestBody
import javax.inject.Inject

class NusamindRepository @Inject constructor(
    private val api: ApiService,
    private val tokenManager: TokenManager,
) {
    private val mediaType = "multipart/form-data".toMediaTypeOrNull()

    suspend fun register(request: RegisterRequest): Result<User> {
        val response = api.register(request)
        if (response.isSuccessful) {
            val body = response.body() ?: return Result.failure(Exception("Empty response"))
            tokenManager.saveToken(body.token ?: return Result.failure(Exception("No token")))
            val u = body.user ?: return Result.failure(Exception("No user"))
            return Result.success(User(u.id, u.name, u.email, u.role ?: "user", u.status ?: "active"))
        }
        return Result.failure(Exception(response.errorBody()?.string() ?: "Register failed"))
    }

    suspend fun login(request: LoginRequest): Result<User> {
        val response = api.login(request)
        if (response.isSuccessful) {
            val body = response.body() ?: return Result.failure(Exception("Empty response"))
            tokenManager.saveToken(body.token ?: return Result.failure(Exception("No token")))
            val u = body.user ?: return Result.failure(Exception("No user"))
            return Result.success(User(u.id, u.name, u.email, u.role ?: "user", u.status ?: "active"))
        }
        val errorBody = response.errorBody()?.string() ?: "Login failed"
        return Result.failure(Exception(errorBody))
    }

    suspend fun logout(): Result<Unit> {
        api.logout()
        tokenManager.clearToken()
        return Result.success(Unit)
    }

    suspend fun getMyBusiness(): Result<Business?> {
        val response = api.getMyBusiness()
        if (response.isSuccessful) {
            val b = response.body()?.business
            return Result.success(b?.let {
                Business(it.id, it.businessName, it.categoryId, it.city, it.description, it.logoPath, it.category?.name)
            })
        }
        return Result.success(null)
    }

    suspend fun createBusiness(name: String, categoryId: Int, city: String, desc: String?): Result<Business> {
        val map = mutableMapOf<String, Any>("business_name" to name, "category_id" to categoryId, "city" to city)
        desc?.let { map["description"] = it }
        val response = api.createBusiness(map)
        if (response.isSuccessful) {
            val b = response.body()?.business ?: return Result.failure(Exception("No business"))
            return Result.success(Business(b.id, b.businessName, b.categoryId, b.city, b.description, b.logoPath, b.category?.name))
        }
        return Result.failure(Exception(response.errorBody()?.string() ?: "Failed"))
    }

    suspend fun updateBusiness(name: String?, city: String?, desc: String?): Result<Business> {
        val map = mutableMapOf<String, Any>()
        name?.let { map["business_name"] = it }
        city?.let { map["city"] = it }
        desc?.let { map["description"] = it }
        val response = api.updateBusiness(map)
        if (response.isSuccessful) {
            val b = response.body()?.business ?: return Result.failure(Exception("No business"))
            return Result.success(Business(b.id, b.businessName, b.categoryId, b.city, b.description, b.logoPath, b.category?.name))
        }
        return Result.failure(Exception(response.errorBody()?.string() ?: "Failed"))
    }

    suspend fun getProducts(): Result<List<Product>> {
        val response = api.getProducts()
        if (response.isSuccessful) {
            val list = response.body()?.products?.map {
                Product(it.id, it.name ?: "", it.price ?: 0, it.stock, it.description, it.imagePath)
            } ?: emptyList()
            return Result.success(list)
        }
        return Result.failure(Exception("Failed to load products"))
    }

    suspend fun createProduct(name: String, price: Int, stock: Int?, desc: String?): Result<Product> {
        val map = mutableMapOf<String, Any>("name" to name, "price" to price)
        stock?.let { map["stock"] = it }
        desc?.let { map["description"] = it }
        val response = api.createProduct(map)
        if (response.isSuccessful) {
            val p = response.body()?.product ?: return Result.failure(Exception("No product"))
            return Result.success(Product(p.id, p.name ?: "", p.price ?: 0, p.stock, p.description, p.imagePath))
        }
        return Result.failure(Exception(response.errorBody()?.string() ?: "Failed"))
    }

    suspend fun updateProduct(id: Int, name: String?, price: Int?, stock: Int?, desc: String?): Result<Product> {
        val map = mutableMapOf<String, Any>()
        name?.let { map["name"] = it }
        price?.let { map["price"] = it }
        stock?.let { map["stock"] = it }
        desc?.let { map["description"] = it }
        val response = api.updateProduct(id, map)
        if (response.isSuccessful) {
            val p = response.body()?.product ?: return Result.failure(Exception("No product"))
            return Result.success(Product(p.id, p.name ?: "", p.price ?: 0, p.stock, p.description, p.imagePath))
        }
        return Result.failure(Exception(response.errorBody()?.string() ?: "Failed"))
    }

    suspend fun deleteProduct(id: Int): Result<Unit> {
        val response = api.deleteProduct(id)
        return if (response.isSuccessful) Result.success(Unit)
        else Result.failure(Exception("Failed to delete"))
    }

    suspend fun extractTransactions(inputText: String): Result<List<Transaction>> {
        val response = api.extractTransactions(AiExtractRequest(inputText))
        if (response.isSuccessful) {
            val list = response.body()?.transactions?.map {
                Transaction(it.id, it.type ?: "", it.itemName ?: "", it.quantity, it.amount ?: 0, it.productId, it.source ?: "", it.rawInput, it.transactionDate)
            } ?: emptyList()
            return Result.success(list)
        }
        val msg = response.errorBody()?.string() ?: "Gagal memproses"
        return Result.failure(Exception(msg))
    }

    suspend fun storeTransactions(items: List<TransactionItemRequest>): Result<Int> {
        val response = api.storeTransactions(TransactionBatchRequest(items))
        return if (response.isSuccessful) Result.success(response.body()?.count ?: 0)
        else Result.failure(Exception("Gagal menyimpan transaksi"))
    }

    suspend fun getTransactions(filter: String?, type: String?): Result<Pair<List<Transaction>, TransactionSummary>> {
        val response = api.getTransactions(filter, type)
        if (response.isSuccessful) {
            val body = response.body() ?: return Result.failure(Exception("Empty"))
            val list = body.transactions?.map {
                Transaction(it.id, it.type ?: "", it.itemName ?: "", it.quantity, it.amount ?: 0, it.productId, it.source ?: "", it.rawInput, it.transactionDate)
            } ?: emptyList()
            val summary = body.summary?.let { TransactionSummary(it.totalIncome, it.totalExpense, it.balance) }
                ?: TransactionSummary(0, 0, 0)
            return Result.success(Pair(list, summary))
        }
        return Result.failure(Exception("Failed to load"))
    }

    suspend fun generateContent(imageBytes: ByteArray, fileName: String, style: String, productId: Int?): Result<ContentResult> {
        val body = imageBytes.toRequestBody(mediaType)
        val part = MultipartBody.Part.createFormData("image", fileName, body)
        val styleBody = style.toRequestBody(mediaType)
        val productIdBody = productId?.toString()?.toRequestBody(mediaType)
        val response = api.generateContent(part, styleBody, productIdBody)
        if (response.isSuccessful) {
            val r = response.body() ?: return Result.failure(Exception("Empty"))
            return Result.success(ContentResult(r.captionResult ?: "", r.hashtagsResult ?: emptyList(), r.whatsappTemplateResult ?: "", r.contentId ?: 0))
        }
        return Result.failure(Exception(response.errorBody()?.string() ?: "Gagal generate"))
    }

    suspend fun getLatestInsight(): Result<BusinessInsight?> {
        val response = api.getLatestInsight()
        if (response.isSuccessful) {
            val i = response.body()?.insight
            return Result.success(i?.let {
                BusinessInsight(it.periodStart, it.periodEnd, it.narrativeText, it.topProduct, it.lowStockProduct)
            })
        }
        return Result.success(null)
    }
}
