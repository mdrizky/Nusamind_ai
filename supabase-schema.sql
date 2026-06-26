-- Nusamind AI - Supabase PostgreSQL Schema
-- Generated from Laravel Migrations on 2026-06-24
-- Compatible with Supabase PostgreSQL 15+

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP(0) NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin', 'superadmin')),
    status VARCHAR(20) NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'banned')),
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

-- ============================================================
-- PERSONAL ACCESS TOKENS (Sanctum)
-- ============================================================
CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP(0) NULL,
    expires_at TIMESTAMP(0) NULL,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_personal_access_tokens_tokenable ON personal_access_tokens(tokenable_type, tokenable_id);

-- ============================================================
-- CACHE
-- ============================================================
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- ============================================================
-- JOBS / QUEUE
-- ============================================================
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON jobs(queue);

CREATE TABLE job_batches (
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

CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- CATEGORIES
-- ============================================================
CREATE TABLE categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50) NULL,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

-- Seed categories
INSERT INTO categories (name, icon) VALUES
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
CREATE TABLE businesses (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    category_id BIGINT NULL REFERENCES categories(id) ON DELETE SET NULL,
    business_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    description TEXT NULL,
    logo_path VARCHAR(255) NULL,
    brand_tone VARCHAR(20) NOT NULL DEFAULT 'santai',
    open_hours VARCHAR(255) NULL,
    shipping_info VARCHAR(255) NULL,
    whatsapp_number VARCHAR(20) NULL,
    payment_methods TEXT NULL,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

-- ============================================================
-- PRODUCTS
-- ============================================================
CREATE TABLE products (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
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
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_products_business_id ON products(business_id);

-- ============================================================
-- TRANSACTIONS
-- ============================================================
CREATE TABLE transactions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id BIGINT NULL REFERENCES products(id) ON DELETE SET NULL,
    type VARCHAR(20) NOT NULL CHECK (type IN ('pemasukan', 'pengeluaran')),
    item_name VARCHAR(150) NOT NULL,
    quantity INTEGER NULL,
    amount BIGINT NOT NULL,
    source VARCHAR(20) NOT NULL DEFAULT 'manual',
    raw_input TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_transactions_user_date ON transactions(user_id, transaction_date);

-- ============================================================
-- CONTENT GENERATIONS
-- ============================================================
CREATE TABLE content_generations (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id BIGINT NULL REFERENCES products(id) ON DELETE SET NULL,
    platform VARCHAR(30) NOT NULL,
    content_type VARCHAR(30) NOT NULL,
    image_path VARCHAR(255) NULL,
    image_url VARCHAR(255) NULL,
    caption_text TEXT NULL,
    hashtags VARCHAR(500) NULL,
    style VARCHAR(50) NULL,
    raw_response TEXT NULL,
    is_used BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_content_generations_user ON content_generations(user_id);

-- ============================================================
-- BUSINESS INSIGHTS
-- ============================================================
CREATE TABLE business_insights (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    period_start DATE NULL,
    period_end DATE NULL,
    narrative_text TEXT NULL,
    summary_data TEXT NULL,
    raw_response TEXT NULL,
    created_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_business_insights_user ON business_insights(user_id);

-- ============================================================
-- EXPORT DESCRIPTIONS
-- ============================================================
CREATE TABLE export_descriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id BIGINT NULL REFERENCES products(id) ON DELETE SET NULL,
    target_language VARCHAR(10) NOT NULL,
    original_text TEXT NOT NULL,
    translated_text TEXT NULL,
    created_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_export_descriptions_user ON export_descriptions(user_id);

-- ============================================================
-- AI USAGE LOGS
-- ============================================================
CREATE TABLE ai_usage_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    feature VARCHAR(30) NOT NULL,
    tokens_used INTEGER NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'success',
    created_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_ai_usage_logs_user_date ON ai_usage_logs(user_id, created_at);

-- ============================================================
-- CONTENT REPORTS
-- ============================================================
CREATE TABLE content_reports (
    id BIGSERIAL PRIMARY KEY,
    content_generation_id BIGINT NOT NULL REFERENCES content_generations(id) ON DELETE CASCADE,
    reported_by BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reason VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_resolved BOOLEAN NOT NULL DEFAULT FALSE,
    resolved_at TIMESTAMP(0) NULL,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    type VARCHAR(30) NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    action_url VARCHAR(255) NULL,
    created_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);

-- ============================================================
-- BUSINESS FAQS (NusaReply)
-- ============================================================
CREATE TABLE business_faqs (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(50) NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_business_faqs_business ON business_faqs(business_id);

-- ============================================================
-- CUSTOMER REPLIES (NusaReply)
-- ============================================================
CREATE TABLE customer_replies (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
    customer_message TEXT NOT NULL,
    intent VARCHAR(50) NULL,
    tone VARCHAR(50) NULL,
    generated_reply TEXT NOT NULL,
    is_saved BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_customer_replies_business ON customer_replies(business_id);

-- ============================================================
-- CUSTOMERS (NusaLoyal)
-- ============================================================
CREATE TABLE customers (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    notes TEXT NULL,
    total_orders INTEGER NOT NULL DEFAULT 0,
    total_spent DECIMAL(12, 2) NOT NULL DEFAULT 0,
    last_order_date DATE NULL,
    segment VARCHAR(20) NOT NULL DEFAULT 'new' CHECK (segment IN ('new', 'regular', 'vip')),
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);

CREATE INDEX idx_customers_business ON customers(business_id);

-- ============================================================
-- CAMPAIGN PLANS (NusaCampaign)
-- ============================================================
CREATE TABLE campaign_plans (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
    campaign_name VARCHAR(255) NULL,
    campaign_goal VARCHAR(255) NOT NULL,
    target_product_id BIGINT NULL REFERENCES products(id) ON DELETE SET NULL,
    plan_result TEXT NULL,
    caption TEXT NULL,
    broadcast_message TEXT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_campaign_plans_business ON campaign_plans(business_id);

-- ============================================================
-- STOCK MOVEMENTS (NusaStock)
-- ============================================================
CREATE TABLE stock_movements (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
    product_id BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    movement_type VARCHAR(20) NOT NULL CHECK (movement_type IN ('in', 'out', 'adjustment')),
    quantity INTEGER NOT NULL,
    reason VARCHAR(255) NULL,
    transaction_id BIGINT NULL REFERENCES transactions(id) ON DELETE SET NULL,
    created_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_stock_movements_business ON stock_movements(business_id);
CREATE INDEX idx_stock_movements_product ON stock_movements(product_id);

-- ============================================================
-- HEALTH SCORES (NusaScore)
-- ============================================================
CREATE TABLE health_scores (
    id BIGSERIAL PRIMARY KEY,
    business_id BIGINT NOT NULL REFERENCES businesses(id) ON DELETE CASCADE,
    total_score INTEGER NOT NULL,
    financial_score INTEGER NULL,
    marketing_score INTEGER NULL,
    sales_score INTEGER NULL,
    customer_score INTEGER NULL,
    stock_score INTEGER NULL,
    breakdown_text TEXT NULL,
    recommendations TEXT NULL,
    scored_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_health_scores_business ON health_scores(business_id);

-- ============================================================
-- DEMO DATA (Admin + Demo User)
-- ============================================================
-- Admin: admin@nusamind.test / password
INSERT INTO users (name, email, password, role, status, email_verified_at, created_at, updated_at)
VALUES ('Admin Nusamind', 'admin@nusamind.test', '$2y$12$LJ3m4ys3Lk0TSwHCpNqrVO.8GJuQFYnFfB3jSLYig5F5MfXKLjMCS', 'admin', 'active', NOW(), NOW(), NOW());

-- Demo User: user@nusamind.test / password
INSERT INTO users (name, email, password, role, status, email_verified_at, created_at, updated_at)
VALUES ('Pengguna Demo', 'user@nusamind.test', '$2y$12$LJ3m4ys3Lk0TSwHCpNqrVO.8GJuQFYnFfB3jSLYig5F5MfXKLjMCS', 'user', 'active', NOW(), NOW(), NOW());

-- Demo Business
INSERT INTO businesses (user_id, category_id, business_name, city, description, brand_tone, created_at, updated_at)
VALUES (2, 1, 'Toko Sembako Sejahtera', 'Jakarta', 'Toko sembako lengkap dengan harga terjangkau. Melayani pembelian grosir dan eceran.', 'santai', NOW(), NOW());

-- Demo Products
INSERT INTO products (business_id, name, price, stock, description, cost_estimate, min_stock_alert, unit, created_at, updated_at) VALUES
(1, 'Beras Pandan Wangi 5kg', 75000, 50, 'Beras pandan wangi kualitas premium, pulen dan wangi.', 60000, 10, 'sak', NOW(), NOW()),
(1, 'Minyak Goreng 2L', 32000, 30, 'Minyak goreng kemasan 2 liter, jernih dan sehat.', 27000, 10, 'botol', NOW(), NOW()),
(1, 'Gula Pasir 1kg', 16000, 100, 'Gula pasir putih bersih, cocok untuk minuman dan kue.', 13000, 20, 'kg', NOW(), NOW()),
(1, 'Kopi Kapal Api 20sachet', 22000, 5, 'Kopi hitam favorit dalam kemasan sachet praktis.', 18000, 10, 'renceng', NOW(), NOW()),
(1, 'Telur Ayam 1kg', 28000, 20, 'Telur ayam negeri segar, ukuran besar.', 24000, 10, 'kg', NOW(), NOW());

-- Demo Transactions
INSERT INTO transactions (user_id, product_id, type, item_name, quantity, amount, source, transaction_date, created_at, updated_at) VALUES
(2, 1, 'pemasukan', 'Beras Pandan Wangi 5kg', 2, 150000, 'manual', CURRENT_DATE - 1, NOW(), NOW()),
(2, NULL, 'pengeluaran', 'Beli gas untuk jualan', NULL, 20000, 'manual', CURRENT_DATE - 1, NOW(), NOW()),
(2, 3, 'pemasukan', 'Gula Pasir 1kg', 5, 80000, 'manual', CURRENT_DATE - 2, NOW(), NOW()),
(2, 2, 'pengeluaran', 'Restok Minyak Goreng', 10, 270000, 'manual', CURRENT_DATE - 2, NOW(), NOW()),
(2, 5, 'pemasukan', 'Telur Ayam 1kg', 3, 84000, 'manual', CURRENT_DATE - 3, NOW(), NOW()),
(2, NULL, 'pemasukan', 'Jualan bakso keliling', NULL, 75000, 'manual', CURRENT_DATE - 3, NOW(), NOW()),
(2, NULL, 'pengeluaran', 'Beli es batu', NULL, 10000, 'manual', CURRENT_DATE - 4, NOW(), NOW()),
(2, 1, 'pemasukan', 'Beras Pandan Wangi 5kg', 1, 75000, 'manual', CURRENT_DATE - 4, NOW(), NOW()),
(2, 4, 'pemasukan', 'Kopi Kapal Api 20sachet', 2, 44000, 'manual', CURRENT_DATE - 5, NOW(), NOW());

-- ============================================================
-- ROW LEVEL SECURITY (Supabase-specific)
-- ============================================================
ALTER TABLE businesses ENABLE ROW LEVEL SECURITY;
ALTER TABLE products ENABLE ROW LEVEL SECURITY;
ALTER TABLE transactions ENABLE ROW LEVEL SECURITY;
ALTER TABLE content_generations ENABLE ROW LEVEL SECURITY;
ALTER TABLE business_insights ENABLE ROW LEVEL SECURITY;
ALTER TABLE export_descriptions ENABLE ROW LEVEL SECURITY;
ALTER TABLE ai_usage_logs ENABLE ROW LEVEL SECURITY;
ALTER TABLE content_reports ENABLE ROW LEVEL SECURITY;
ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE business_faqs ENABLE ROW LEVEL SECURITY;
ALTER TABLE customer_replies ENABLE ROW LEVEL SECURITY;
ALTER TABLE customers ENABLE ROW LEVEL SECURITY;
ALTER TABLE campaign_plans ENABLE ROW LEVEL SECURITY;
ALTER TABLE stock_movements ENABLE ROW LEVEL SECURITY;
ALTER TABLE health_scores ENABLE ROW LEVEL SECURITY;

-- Basic RLS policies (user can only see their own data)
CREATE POLICY "Users can view own businesses" ON businesses FOR SELECT USING (user_id = auth::uid());
CREATE POLICY "Users can view own products" ON products FOR SELECT USING (business_id IN (SELECT id FROM businesses WHERE user_id = auth::uid()));
CREATE POLICY "Users can view own transactions" ON transactions FOR SELECT USING (user_id = auth::uid());
-- (Additional policies should be created as needed)
