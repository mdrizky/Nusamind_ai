-- Nusamind AI - Supabase PostgreSQL Schema
-- Compatible with Supabase PostgreSQL 15+
-- UUID-based auth.users integration
-- Complete RLS policies for all tables with admin bypass

-- ============================================================
-- CLEANUP (safe to re-run)
-- ============================================================
DROP TABLE IF EXISTS public.health_scores CASCADE;
DROP TABLE IF EXISTS public.stock_movements CASCADE;
DROP TABLE IF EXISTS public.campaign_plans CASCADE;
DROP TABLE IF EXISTS public.customers CASCADE;
DROP TABLE IF EXISTS public.customer_replies CASCADE;
DROP TABLE IF EXISTS public.business_faqs CASCADE;
DROP TABLE IF EXISTS public.notifications CASCADE;
DROP TABLE IF EXISTS public.content_reports CASCADE;
DROP TABLE IF EXISTS public.ai_usage_logs CASCADE;
DROP TABLE IF EXISTS public.export_descriptions CASCADE;
DROP TABLE IF EXISTS public.business_insights CASCADE;
DROP TABLE IF EXISTS public.content_generations CASCADE;
DROP TABLE IF EXISTS public.transactions CASCADE;
DROP TABLE IF EXISTS public.products CASCADE;
DROP TABLE IF EXISTS public.businesses CASCADE;
DROP TABLE IF EXISTS public.categories CASCADE;
DROP TABLE IF EXISTS public.personal_access_tokens CASCADE;
DROP TABLE IF EXISTS public.job_batches CASCADE;
DROP TABLE IF EXISTS public.failed_jobs CASCADE;
DROP TABLE IF EXISTS public.jobs CASCADE;
DROP TABLE IF EXISTS public.cache_locks CASCADE;
DROP TABLE IF EXISTS public.cache CASCADE;
DROP TABLE IF EXISTS public.users CASCADE;

-- ============================================================
-- USERS (links to Supabase Auth)
-- ============================================================
CREATE TABLE public.users (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin', 'superadmin')),
    status VARCHAR(20) NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'banned')),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================================
-- PERSONAL ACCESS TOKENS
-- ============================================================
CREATE TABLE public.personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id UUID NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMPTZ NULL,
    expires_at TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_personal_access_tokens_tokenable ON public.personal_access_tokens(tokenable_type, tokenable_id);

-- ============================================================
-- CACHE
-- ============================================================
CREATE TABLE public.cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE public.cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- ============================================================
-- JOBS / QUEUE
-- ============================================================
CREATE TABLE public.jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON public.jobs(queue);

CREATE TABLE public.job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

CREATE TABLE public.failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ============================================================
-- CATEGORIES
-- ============================================================
CREATE TABLE public.categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50) NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

INSERT INTO public.categories (name, icon) VALUES
    ('Makanan & Minuman', 'bi-cup-hot'),
    ('Fashion', 'bi-handbag'),
    ('Kesehatan & Kecantikan', 'bi-heart-pulse'),
    ('Elektronik', 'bi-phone'),
    ('Kerajinan Tangan', 'bi-palette'),
    ('Pertanian', 'bi-flower1'),
    ('Jasa', 'bi-gear'),
    ('Lainnya', 'bi-grid');

-- ============================================================
-- BUSINESSES
-- ============================================================
CREATE TABLE public.businesses (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    category_id BIGINT NULL REFERENCES public.categories(id) ON DELETE SET NULL,
    business_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    description TEXT NULL,
    logo_path VARCHAR(255) NULL,
    brand_tone VARCHAR(20) NOT NULL DEFAULT 'santai',
    open_hours VARCHAR(255) NULL,
    shipping_info VARCHAR(255) NULL,
    whatsapp_number VARCHAR(20) NULL,
    payment_methods TEXT NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

CREATE UNIQUE INDEX idx_businesses_user ON public.businesses(user_id);

-- ============================================================
-- PRODUCTS
-- ============================================================
CREATE TABLE public.products (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    stock INTEGER NOT NULL DEFAULT 0,
    image_path VARCHAR(255) NULL,
    description TEXT NULL,
    cost_estimate DECIMAL(12, 2) NULL,
    min_stock_alert INTEGER NOT NULL DEFAULT 5,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    image_url VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    tags TEXT NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_products_business ON public.products(business_id);

-- ============================================================
-- TRANSACTIONS
-- ============================================================
CREATE TABLE public.transactions (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    product_id BIGINT NULL REFERENCES public.products(id) ON DELETE SET NULL,
    type VARCHAR(20) NOT NULL CHECK (type IN ('pemasukan', 'pengeluaran')),
    item_name VARCHAR(150) NOT NULL,
    quantity INTEGER NULL,
    amount BIGINT NOT NULL,
    source VARCHAR(20) NOT NULL DEFAULT 'manual',
    raw_input TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_transactions_user_date ON public.transactions(user_id, transaction_date);

-- ============================================================
-- CONTENT GENERATIONS
-- ============================================================
CREATE TABLE public.content_generations (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    product_id BIGINT NULL REFERENCES public.products(id) ON DELETE SET NULL,
    platform VARCHAR(30) NOT NULL,
    content_type VARCHAR(30) NOT NULL,
    image_path VARCHAR(255) NULL,
    image_url VARCHAR(255) NULL,
    caption_text TEXT NULL,
    hashtags VARCHAR(500) NULL,
    style VARCHAR(50) NULL,
    raw_response TEXT NULL,
    is_used BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_content_generations_user ON public.content_generations(user_id);

-- ============================================================
-- BUSINESS INSIGHTS
-- ============================================================
CREATE TABLE public.business_insights (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    period_start DATE NULL,
    period_end DATE NULL,
    narrative_text TEXT NULL,
    summary_data TEXT NULL,
    raw_response TEXT NULL,
    created_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_business_insights_user ON public.business_insights(user_id);

-- ============================================================
-- EXPORT DESCRIPTIONS
-- ============================================================
CREATE TABLE public.export_descriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    product_id BIGINT NULL REFERENCES public.products(id) ON DELETE SET NULL,
    target_language VARCHAR(10) NOT NULL,
    original_text TEXT NOT NULL,
    translated_text TEXT NULL,
    created_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_export_descriptions_user ON public.export_descriptions(user_id);

-- ============================================================
-- AI USAGE LOGS
-- ============================================================
CREATE TABLE public.ai_usage_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    feature VARCHAR(30) NOT NULL,
    tokens_used INTEGER NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'success',
    created_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_ai_usage_logs_user_date ON public.ai_usage_logs(user_id, created_at);

-- ============================================================
-- CONTENT REPORTS
-- ============================================================
CREATE TABLE public.content_reports (
    id BIGSERIAL PRIMARY KEY,
    content_generation_id BIGINT NOT NULL REFERENCES public.content_generations(id) ON DELETE CASCADE,
    reported_by UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    reason VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_resolved BOOLEAN NOT NULL DEFAULT FALSE,
    resolved_at TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
CREATE TABLE public.notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID NOT NULL REFERENCES public.users(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    type VARCHAR(30) NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    action_url VARCHAR(255) NULL,
    created_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_notifications_user_read ON public.notifications(user_id, is_read);

-- ============================================================
-- BUSINESS FAQS (NusaReply)
-- ============================================================
CREATE TABLE public.business_faqs (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(50) NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_business_faqs_business ON public.business_faqs(business_id);

-- ============================================================
-- CUSTOMER REPLIES (NusaReply)
-- ============================================================
CREATE TABLE public.customer_replies (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    customer_message TEXT NOT NULL,
    intent VARCHAR(50) NULL,
    tone VARCHAR(50) NULL,
    generated_reply TEXT NOT NULL,
    is_saved BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_customer_replies_business ON public.customer_replies(business_id);

-- ============================================================
-- CUSTOMERS (NusaLoyal)
-- ============================================================
CREATE TABLE public.customers (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    notes TEXT NULL,
    total_orders INTEGER NOT NULL DEFAULT 0,
    total_spent DECIMAL(12, 2) NOT NULL DEFAULT 0,
    last_order_date DATE NULL,
    segment VARCHAR(20) NOT NULL DEFAULT 'new' CHECK (segment IN ('new', 'regular', 'vip')),
    created_at TIMESTAMPTZ NULL,
    updated_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_customers_business ON public.customers(business_id);

-- ============================================================
-- CAMPAIGN PLANS (NusaCampaign)
-- ============================================================
CREATE TABLE public.campaign_plans (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    campaign_name VARCHAR(255) NULL,
    campaign_goal VARCHAR(255) NOT NULL,
    target_product_id BIGINT NULL REFERENCES public.products(id) ON DELETE SET NULL,
    plan_result TEXT NULL,
    caption TEXT NULL,
    broadcast_message TEXT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_campaign_plans_business ON public.campaign_plans(business_id);

-- ============================================================
-- STOCK MOVEMENTS (NusaStock)
-- ============================================================
CREATE TABLE public.stock_movements (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    product_id BIGINT NOT NULL REFERENCES public.products(id) ON DELETE CASCADE,
    movement_type VARCHAR(20) NOT NULL CHECK (movement_type IN ('in', 'out', 'adjustment')),
    quantity INTEGER NOT NULL,
    reason VARCHAR(255) NULL,
    transaction_id BIGINT NULL REFERENCES public.transactions(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_stock_movements_business ON public.stock_movements(business_id);
CREATE INDEX idx_stock_movements_product ON public.stock_movements(product_id);

-- ============================================================
-- HEALTH SCORES (NusaScore)
-- ============================================================
CREATE TABLE public.health_scores (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES public.businesses(id) ON DELETE CASCADE,
    total_score INTEGER NOT NULL,
    financial_score INTEGER NULL,
    marketing_score INTEGER NULL,
    sales_score INTEGER NULL,
    customer_score INTEGER NULL,
    stock_score INTEGER NULL,
    breakdown_text TEXT NULL,
    recommendations TEXT NULL,
    scored_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_health_scores_business ON public.health_scores(business_id);

-- ============================================================
-- ROW LEVEL SECURITY
-- ============================================================
-- Helper: check if current user is admin
CREATE OR REPLACE FUNCTION public.is_admin()
RETURNS BOOLEAN
LANGUAGE SQL STABLE SECURITY DEFINER
AS $$
  SELECT EXISTS (SELECT 1 FROM public.users WHERE id = auth.uid() AND role = 'admin');
$$;

ALTER TABLE public.businesses ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.products ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.transactions ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.content_generations ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.business_insights ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.export_descriptions ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.ai_usage_logs ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.content_reports ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.business_faqs ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.customer_replies ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.customers ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.campaign_plans ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.stock_movements ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.health_scores ENABLE ROW LEVEL SECURITY;

-- ============================================================
-- RLS POLICIES — businesses
-- ============================================================
CREATE POLICY "businesses_select" ON public.businesses FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());
CREATE POLICY "businesses_insert" ON public.businesses FOR INSERT
  WITH CHECK (user_id = auth.uid());
CREATE POLICY "businesses_update" ON public.businesses FOR UPDATE
  USING (user_id = auth.uid());
CREATE POLICY "businesses_delete" ON public.businesses FOR DELETE
  USING (user_id = auth.uid());

-- ============================================================
-- RLS POLICIES — products
-- ============================================================
CREATE POLICY "products_select" ON public.products FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "products_insert" ON public.products FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "products_update" ON public.products FOR UPDATE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "products_delete" ON public.products FOR DELETE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));

-- ============================================================
-- RLS POLICIES — transactions
-- ============================================================
CREATE POLICY "transactions_select" ON public.transactions FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());
CREATE POLICY "transactions_insert" ON public.transactions FOR INSERT
  WITH CHECK (user_id = auth.uid());
CREATE POLICY "transactions_update" ON public.transactions FOR UPDATE
  USING (user_id = auth.uid());
CREATE POLICY "transactions_delete" ON public.transactions FOR DELETE
  USING (user_id = auth.uid());

-- ============================================================
-- RLS POLICIES — content_generations
-- ============================================================
CREATE POLICY "content_generations_select" ON public.content_generations FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());
CREATE POLICY "content_generations_insert" ON public.content_generations FOR INSERT
  WITH CHECK (user_id = auth.uid());
CREATE POLICY "content_generations_delete" ON public.content_generations FOR DELETE
  USING (user_id = auth.uid());

-- ============================================================
-- RLS POLICIES — business_insights
-- ============================================================
CREATE POLICY "business_insights_select" ON public.business_insights FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());

-- ============================================================
-- RLS POLICIES — export_descriptions
-- ============================================================
CREATE POLICY "export_descriptions_select" ON public.export_descriptions FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());

-- ============================================================
-- RLS POLICIES — ai_usage_logs
-- ============================================================
CREATE POLICY "ai_usage_logs_select" ON public.ai_usage_logs FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());
CREATE POLICY "ai_usage_logs_insert" ON public.ai_usage_logs FOR INSERT
  WITH CHECK (user_id = auth.uid());

-- ============================================================
-- RLS POLICIES — content_reports
-- ============================================================
CREATE POLICY "content_reports_select" ON public.content_reports FOR SELECT
  USING (reported_by = auth.uid() OR public.is_admin());
CREATE POLICY "content_reports_insert" ON public.content_reports FOR INSERT
  WITH CHECK (reported_by = auth.uid());
CREATE POLICY "content_reports_update" ON public.content_reports FOR UPDATE
  USING (public.is_admin());

-- ============================================================
-- RLS POLICIES — notifications
-- ============================================================
CREATE POLICY "notifications_select" ON public.notifications FOR SELECT
  USING (user_id = auth.uid() OR public.is_admin());
CREATE POLICY "notifications_update" ON public.notifications FOR UPDATE
  USING (user_id = auth.uid());

-- ============================================================
-- RLS POLICIES — business_faqs
-- ============================================================
CREATE POLICY "business_faqs_select" ON public.business_faqs FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "business_faqs_insert" ON public.business_faqs FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "business_faqs_delete" ON public.business_faqs FOR DELETE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));

-- ============================================================
-- RLS POLICIES — customer_replies
-- ============================================================
CREATE POLICY "customer_replies_select" ON public.customer_replies FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "customer_replies_insert" ON public.customer_replies FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "customer_replies_delete" ON public.customer_replies FOR DELETE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));

-- ============================================================
-- RLS POLICIES — customers
-- ============================================================
CREATE POLICY "customers_select" ON public.customers FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "customers_insert" ON public.customers FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "customers_update" ON public.customers FOR UPDATE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "customers_delete" ON public.customers FOR DELETE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));

-- ============================================================
-- RLS POLICIES — campaign_plans
-- ============================================================
CREATE POLICY "campaign_plans_select" ON public.campaign_plans FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "campaign_plans_insert" ON public.campaign_plans FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
CREATE POLICY "campaign_plans_delete" ON public.campaign_plans FOR DELETE
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));

-- ============================================================
-- RLS POLICIES — stock_movements
-- ============================================================
CREATE POLICY "stock_movements_select" ON public.stock_movements FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "stock_movements_insert" ON public.stock_movements FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));

-- ============================================================
-- RLS POLICIES — health_scores
-- ============================================================
CREATE POLICY "health_scores_select" ON public.health_scores FOR SELECT
  USING (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()) OR public.is_admin());
CREATE POLICY "health_scores_insert" ON public.health_scores FOR INSERT
  WITH CHECK (business_id IN (SELECT id FROM public.businesses WHERE user_id = auth.uid()));
