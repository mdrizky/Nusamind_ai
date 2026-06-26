-- =========================================================
-- 05 - DATABASE SCHEMA: Nusamind AI
-- Siap dikonversi jadi migration Laravel (php artisan make:migration)
-- Engine: MySQL 8 / MariaDB
-- =========================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user',
    status ENUM('active','suspended') NOT NULL DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE businesses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    business_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    description TEXT NULL,
    logo_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_businesses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_businesses_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    price BIGINT UNSIGNED NOT NULL DEFAULT 0,
    stock INT NULL,
    image_path VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_products_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    type ENUM('pemasukan','pengeluaran') NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity INT NULL,
    amount BIGINT NOT NULL,
    source ENUM('ai_text','ai_voice','manual') NOT NULL DEFAULT 'manual',
    raw_input TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_transactions_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_transactions_user_date (user_id, transaction_date)
);

CREATE TABLE content_generations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    image_path VARCHAR(255) NOT NULL,
    style ENUM('formal','gaul','hard_selling') NOT NULL,
    caption_result TEXT NULL,
    hashtags_result JSON NULL,
    whatsapp_template_result TEXT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_content_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_content_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE business_insights (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    narrative_text TEXT NOT NULL,
    top_product VARCHAR(150) NULL,
    low_stock_product VARCHAR(150) NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_insights_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE export_descriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    target_language ENUM('en','zh') NOT NULL,
    original_text TEXT NOT NULL,
    translated_text TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_export_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_export_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE ai_usage_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    feature ENUM('finance','content','briefing','export') NOT NULL,
    tokens_used INT NULL,
    status ENUM('success','failed','timeout') NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_ai_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_ai_logs_user_date (user_id, created_at)
);

CREATE TABLE content_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_generation_id BIGINT UNSIGNED NOT NULL,
    reported_by BIGINT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending','reviewed','removed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_reports_content FOREIGN KEY (content_generation_id) REFERENCES content_generations(id) ON DELETE CASCADE,
    CONSTRAINT fk_reports_user FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================================================
-- SEEDER DASAR (contoh data kategori awal)
-- =========================================================
INSERT INTO categories (name, icon, created_at, updated_at) VALUES
('Makanan & Minuman', 'utensils', NOW(), NOW()),
('Fashion', 'shirt', NOW(), NOW()),
('Kerajinan Tangan', 'scissors', NOW(), NOW()),
('Jasa', 'briefcase', NOW(), NOW()),
('Pertanian & Sembako', 'leaf', NOW(), NOW());

-- =========================================================
-- CATATAN UNTUK LARAVEL MIGRATION:
-- - Gunakan $table->foreignId('user_id')->constrained()->onDelete('cascade');
-- - Gunakan $table->json('hashtags_result')->nullable(); untuk kolom JSON
-- - Gunakan php artisan make:model <Nama> -mfs untuk generate Model + Migration + Factory + Seeder sekaligus
-- =========================================================
