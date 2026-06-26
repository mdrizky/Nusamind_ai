package com.nusamind.nusamindai.data.api.dto

import com.google.gson.annotations.SerializedName

data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    @SerializedName("password_confirmation") val passwordConfirmation: String,
)

data class LoginRequest(
    val email: String,
    val password: String,
)

data class AuthResponse(
    val message: String?,
    val user: UserDto?,
    val token: String?,
)

data class UserDto(
    val id: Int,
    val name: String,
    val email: String,
    val role: String?,
    val status: String?,
    val business: BusinessDto?,
)

data class BusinessDto(
    val id: Int?,
    @SerializedName("business_name") val businessName: String?,
    @SerializedName("category_id") val categoryId: Int?,
    val city: String?,
    val description: String?,
    @SerializedName("logo_path") val logoPath: String?,
    val category: CategoryDto?,
)

data class CategoryDto(
    val id: Int,
    val name: String,
    val icon: String?,
)

data class ProductDto(
    val id: Int?,
    @SerializedName("business_id") val businessId: Int?,
    val name: String?,
    val price: Int?,
    val stock: Int?,
    val description: String?,
    @SerializedName("image_path") val imagePath: String?,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?,
)

data class TransactionDto(
    val id: Int?,
    val type: String?,
    @SerializedName("item_name") val itemName: String?,
    val quantity: Int?,
    val amount: Int?,
    @SerializedName("product_id") val productId: Int?,
    val source: String?,
    @SerializedName("raw_input") val rawInput: String?,
    @SerializedName("transaction_date") val transactionDate: String?,
)

data class TransactionBatchRequest(
    val transactions: List<TransactionItemRequest>,
)

data class TransactionItemRequest(
    val type: String,
    @SerializedName("item_name") val itemName: String,
    val quantity: Int?,
    val amount: Int,
    @SerializedName("product_id") val productId: Int?,
    val source: String = "manual",
    @SerializedName("raw_input") val rawInput: String? = null,
)

data class TransactionBatchResponse(
    val message: String?,
    val count: Int?,
)

data class TransactionListResponse(
    val transactions: List<TransactionDto>?,
    val summary: TransactionSummary?,
)

data class TransactionSummary(
    @SerializedName("total_income") val totalIncome: Int,
    @SerializedName("total_expense") val totalExpense: Int,
    val balance: Int,
)

data class AiExtractRequest(
    @SerializedName("input_text") val inputText: String,
)

data class AiExtractResponse(
    val transactions: List<TransactionDto>?,
    val note: String?,
)

data class ContentGenerateResponse(
    @SerializedName("caption_result") val captionResult: String?,
    @SerializedName("hashtags_result") val hashtagsResult: List<String>?,
    @SerializedName("whatsapp_template_result") val whatsappTemplateResult: String?,
    @SerializedName("content_id") val contentId: Int?,
)

data class ContentReportRequest(
    @SerializedName("content_generation_id") val contentGenerationId: Int,
    val reason: String,
)

data class BusinessInsightDto(
    val id: Int?,
    @SerializedName("period_start") val periodStart: String?,
    @SerializedName("period_end") val periodEnd: String?,
    @SerializedName("narrative_text") val narrativeText: String?,
    @SerializedName("top_product") val topProduct: String?,
    @SerializedName("low_stock_product") val lowStockProduct: String?,
)

data class BusinessInsightResponse(
    val message: String?,
    val insight: BusinessInsightDto?,
)

data class BusinessResponse(
    val message: String?,
    val business: BusinessDto?,
)

data class ProductResponse(
    val message: String?,
    val product: ProductDto?,
)

data class ProductListResponse(
    val products: List<ProductDto>?,
)

data class TransactionDetailResponse(
    val message: String?,
    val transaction: TransactionDto?,
)

data class TranslateRequest(
    @SerializedName("product_id") val productId: Int,
    @SerializedName("target_language") val targetLanguage: String,
)

data class TranslateResponse(
    @SerializedName("original_text") val originalText: String?,
    @SerializedName("translated_text") val translatedText: String?,
    @SerializedName("target_language") val targetLanguage: String?,
)

data class ErrorResponse(
    val message: String?,
    val errors: Map<String, List<String>>?,
)
