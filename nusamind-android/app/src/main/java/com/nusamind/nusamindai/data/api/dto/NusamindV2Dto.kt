package com.nusamind.nusamindai.data.api.dto

import com.google.gson.annotations.SerializedName

// NusaReply
data class ReplyRequest(
    @SerializedName("customer_message") val customerMessage: String,
    val intent: String? = null,
    val tone: String? = null,
)

data class ReplyResponse(
    val data: ReplyData?,
)

data class ReplyData(
    val reply: String?,
    @SerializedName("intent_detected") val intentDetected: String?,
)

data class CustomerReplyListResponse(
    val data: List<CustomerReply>?,
)

data class CustomerReply(
    val id: Int,
    @SerializedName("customer_message") val customerMessage: String,
    val intent: String?,
    val tone: String?,
    @SerializedName("generated_reply") val generatedReply: String,
    @SerializedName("is_saved") val isSaved: Boolean,
    @SerializedName("created_at") val createdAt: String?,
)

data class FaqRequest(
    val question: String,
    val answer: String,
    val category: String? = null,
)

data class FaqListResponse(
    val data: List<Faq>?,
)

data class Faq(
    val id: Int,
    val question: String,
    val answer: String,
    val category: String?,
)

// NusaStock
data class StockAnalysisResponse(
    val data: List<StockRecommendation>?,
)

data class StockRecommendation(
    @SerializedName("product_name") val productName: String,
    val status: String,
    @SerializedName("current_stock") val currentStock: Int,
    @SerializedName("recommended_restock") val recommendedRestock: Int,
    val reason: String,
)

data class StockMovementListResponse(
    val data: List<StockMovementItem>?,
)

data class StockMovementItem(
    val id: Int,
    @SerializedName("product_id") val productId: Int,
    @SerializedName("movement_type") val movementType: String,
    val quantity: Int,
    val reason: String?,
    @SerializedName("created_at") val createdAt: String?,
)

data class StockMovementRequest(
    @SerializedName("product_id") val productId: Int,
    @SerializedName("movement_type") val movementType: String,
    val quantity: Int,
    val reason: String? = null,
)

// NusaCampaign
data class CampaignPlanListResponse(
    val data: List<CampaignPlan>?,
)

data class CampaignPlan(
    val id: Int,
    @SerializedName("campaign_name") val campaignName: String?,
    @SerializedName("campaign_goal") val campaignGoal: String,
    @SerializedName("is_active") val isActive: Boolean,
    @SerializedName("created_at") val createdAt: String?,
)

data class CampaignRequest(
    @SerializedName("campaign_goal") val campaignGoal: String,
    @SerializedName("target_product_id") val targetProductId: Int?,
)

data class CampaignResponse(
    val data: CampaignPlan?,
)

// NusaLoyal
data class CustomerListResponse(
    val data: List<Customer>?,
)

data class Customer(
    val id: Int,
    val name: String,
    val phone: String?,
    val address: String?,
    val segment: String?,
    @SerializedName("total_orders") val totalOrders: Int?,
    @SerializedName("total_spent") val totalSpent: Double?,
)

data class CustomerRequest(
    val name: String,
    val phone: String? = null,
    val address: String? = null,
    val notes: String? = null,
)

data class FollowUpRequest(
    @SerializedName("customer_id") val customerId: Int,
)

data class FollowUpResponse(
    val data: FollowUpData?,
)

data class FollowUpData(
    @SerializedName("follow_up_message") val followUpMessage: String?,
    val subject: String?,
    @SerializedName("segment_note") val segmentNote: String?,
    @SerializedName("next_action") val nextAction: String?,
)

// NusaPrice
data class PriceRequest(
    @SerializedName("product_id") val productId: Int,
    @SerializedName("competitor_price") val competitorPrice: Double? = null,
)

data class PriceResponse(
    val data: PriceData?,
)

data class PriceData(
    @SerializedName("product_name") val productName: String?,
    @SerializedName("current_price") val currentPrice: Double?,
    @SerializedName("recommended_price") val recommendedPrice: Double?,
    @SerializedName("min_price") val minPrice: Double?,
    @SerializedName("max_price") val maxPrice: Double?,
    val reasoning: String?,
)

// NusaCatalog
data class CatalogRequest(
    @SerializedName("product_id") val productId: Int,
)

data class CatalogResponse(
    val data: CatalogData?,
)

data class CatalogData(
    @SerializedName("optimized_name") val optimizedName: String?,
    @SerializedName("optimized_description") val optimizedDescription: String?,
    val keywords: List<String>?,
)

// NusaScore
data class HealthScoreResponse(
    val data: HealthScore?,
)

data class HealthScoreListResponse(
    val data: List<HealthScore>?,
)

data class HealthScore(
    val id: Int,
    @SerializedName("total_score") val totalScore: Int,
    @SerializedName("financial_score") val financialScore: Int?,
    @SerializedName("marketing_score") val marketingScore: Int?,
    @SerializedName("sales_score") val salesScore: Int?,
    @SerializedName("customer_score") val customerScore: Int?,
    @SerializedName("stock_score") val stockScore: Int?,
    @SerializedName("breakdown_text") val breakdownText: String?,
    val recommendations: List<String>?,
    @SerializedName("scored_at") val scoredAt: String?,
)

// NusaCoach
data class CoachRequest(
    val message: String,
)

data class CoachResponse(
    val data: CoachData?,
)

data class CoachData(
    val reply: String?,
    val suggestions: List<String>?,
)

// Base
data class BaseResponse(
    val message: String?,
)
