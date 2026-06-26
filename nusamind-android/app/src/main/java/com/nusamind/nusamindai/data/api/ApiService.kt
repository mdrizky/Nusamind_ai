package com.nusamind.nusamindai.data.api

import com.nusamind.nusamindai.data.api.dto.*
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    @POST("auth/register")
    suspend fun register(@Body request: RegisterRequest): Response<AuthResponse>

    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>

    @POST("auth/logout")
    suspend fun logout(): Response<Map<String, String>>

    @GET("auth/me")
    suspend fun me(): Response<AuthResponse>

    @POST("business")
    suspend fun createBusiness(@Body request: Map<String, @JvmSuppressWildcards Any>): Response<BusinessResponse>

    @GET("business/me")
    suspend fun getMyBusiness(): Response<BusinessResponse>

    @PUT("business/me")
    suspend fun updateBusiness(@Body request: Map<String, @JvmSuppressWildcards Any>): Response<BusinessResponse>

    @GET("products")
    suspend fun getProducts(): Response<ProductListResponse>

    @POST("products")
    suspend fun createProduct(@Body request: Map<String, @JvmSuppressWildcards Any>): Response<ProductResponse>

    @GET("products/{id}")
    suspend fun getProduct(@Path("id") id: Int): Response<ProductResponse>

    @PUT("products/{id}")
    suspend fun updateProduct(@Path("id") id: Int, @Body request: Map<String, @JvmSuppressWildcards Any>): Response<ProductResponse>

    @DELETE("products/{id}")
    suspend fun deleteProduct(@Path("id") id: Int): Response<Map<String, String>>

    @POST("ai/finance/extract")
    suspend fun extractTransactions(@Body request: AiExtractRequest): Response<AiExtractResponse>

    @POST("transactions")
    suspend fun storeTransactions(@Body request: TransactionBatchRequest): Response<TransactionBatchResponse>

    @GET("transactions")
    suspend fun getTransactions(
        @Query("filter") filter: String? = null,
        @Query("type") type: String? = null,
    ): Response<TransactionListResponse>

    @GET("transactions/{id}")
    suspend fun getTransaction(@Path("id") id: Int): Response<TransactionDetailResponse>

    @PUT("transactions/{id}")
    suspend fun updateTransaction(
        @Path("id") id: Int,
        @Body request: Map<String, @JvmSuppressWildcards Any>
    ): Response<TransactionDetailResponse>

    @DELETE("transactions/{id}")
    suspend fun deleteTransaction(@Path("id") id: Int): Response<Map<String, String>>

    @Multipart
    @POST("ai/content/generate")
    suspend fun generateContent(
        @Part image: MultipartBody.Part,
        @Part("style") style: RequestBody,
        @Part("product_id") productId: RequestBody?,
    ): Response<ContentGenerateResponse>

    @POST("ai/content/{id}/regenerate")
    suspend fun regenerateContent(@Path("id") id: Int): Response<ContentGenerateResponse>

    @POST("content-reports")
    suspend fun reportContent(@Body request: ContentReportRequest): Response<Map<String, String>>

    @GET("content-generations")
    suspend fun getContentHistory(): Response<Map<String, List<ContentGenerateResponse>>>

    @POST("ai/export/translate")
    suspend fun translateProduct(@Body request: TranslateRequest): Response<TranslateResponse>

    @GET("business-insights/latest")
    suspend fun getLatestInsight(): Response<BusinessInsightResponse>

    @GET("business-insights/history")
    suspend fun getInsightHistory(): Response<Map<String, List<BusinessInsightDto>>>

    // NusaReply
    @POST("ai/reply/generate")
    suspend fun generateReply(@Body request: ReplyRequest): Response<ReplyResponse>

    @GET("customer-replies")
    suspend fun getCustomerReplies(): Response<CustomerReplyListResponse>

    @POST("customer-replies/{id}/save")
    suspend fun saveCustomerReply(@Path("id") id: Int): Response<BaseResponse>

    @GET("faqs")
    suspend fun getFaqs(): Response<FaqListResponse>

    @POST("faqs")
    suspend fun createFaq(@Body request: FaqRequest): Response<BaseResponse>

    @DELETE("faqs/{id}")
    suspend fun deleteFaq(@Path("id") id: Int): Response<BaseResponse>

    // NusaStock
    @POST("ai/stock/analyze")
    suspend fun analyzeStock(): Response<StockAnalysisResponse>

    @GET("stock-movements")
    suspend fun getStockMovements(): Response<StockMovementListResponse>

    @POST("stock-movements")
    suspend fun createStockMovement(@Body request: StockMovementRequest): Response<BaseResponse>

    // NusaCampaign
    @GET("campaign-plans")
    suspend fun getCampaignPlans(): Response<CampaignPlanListResponse>

    @POST("ai/campaign/generate")
    suspend fun generateCampaign(@Body request: CampaignRequest): Response<CampaignResponse>

    @DELETE("campaign-plans/{id}")
    suspend fun deleteCampaignPlan(@Path("id") id: Int): Response<BaseResponse>

    // NusaLoyal
    @GET("customers")
    suspend fun getCustomers(): Response<CustomerListResponse>

    @POST("customers")
    suspend fun createCustomer(@Body request: CustomerRequest): Response<BaseResponse>

    @PUT("customers/{id}")
    suspend fun updateCustomer(@Path("id") id: Int, @Body request: CustomerRequest): Response<BaseResponse>

    @DELETE("customers/{id}")
    suspend fun deleteCustomer(@Path("id") id: Int): Response<BaseResponse>

    @POST("ai/loyal/follow-up")
    suspend fun generateFollowUp(@Body request: FollowUpRequest): Response<FollowUpResponse>

    // NusaPrice
    @POST("ai/price/recommend")
    suspend fun recommendPrice(@Body request: PriceRequest): Response<PriceResponse>

    // NusaCatalog
    @POST("ai/catalog/enhance")
    suspend fun enhanceCatalog(@Body request: CatalogRequest): Response<CatalogResponse>

    // NusaScore
    @GET("health-scores/latest")
    suspend fun getLatestHealthScore(): Response<HealthScoreResponse>

    @GET("health-scores/history")
    suspend fun getHealthScoreHistory(): Response<HealthScoreListResponse>

    // NusaCoach
    @POST("ai/coach/chat")
    suspend fun coachChat(@Body request: CoachRequest): Response<CoachResponse>
}
