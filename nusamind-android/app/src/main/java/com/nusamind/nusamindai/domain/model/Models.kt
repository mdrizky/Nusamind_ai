package com.nusamind.nusamindai.domain.model

data class User(
    val id: Int,
    val name: String,
    val email: String,
    val role: String,
    val status: String,
)

data class Business(
    val id: Int?,
    val businessName: String?,
    val categoryId: Int?,
    val city: String?,
    val description: String?,
    val logoPath: String?,
    val categoryName: String?,
)

data class Product(
    val id: Int?,
    val name: String,
    val price: Int,
    val stock: Int?,
    val description: String?,
    val imagePath: String?,
    val businessId: Int?,
)

data class Transaction(
    val id: Int?,
    val type: String,
    val itemName: String,
    val quantity: Int?,
    val amount: Int,
    val productId: Int?,
    val source: String,
    val rawInput: String?,
    val transactionDate: String?,
)

data class TransactionSummary(
    val totalIncome: Int,
    val totalExpense: Int,
    val balance: Int,
)

data class AiExtraction(
    val transactions: List<Transaction>,
)

data class ContentResult(
    val caption: String,
    val hashtags: List<String>,
    val whatsappTemplate: String,
    val contentId: Int,
)

data class BusinessInsight(
    val periodStart: String?,
    val periodEnd: String?,
    val narrativeText: String?,
    val topProduct: String?,
    val lowStockProduct: String?,
)

data class BusinessInsightListResponse(
    val insights: List<BusinessInsight>,
)
