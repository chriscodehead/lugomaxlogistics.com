-- Lugomax Logistics Database Schema
-- MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS lugomax_db2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lugomax_db2;

-- ============================================================
-- 1. USERS TABLE (Admin/Staff)
-- ============================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff', 'driver') DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB;

-- ============================================================
-- 2. ORDERS TABLE (Tracking System)
-- ============================================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tracking_number VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    pickup_address TEXT NOT NULL,
    delivery_address TEXT NOT NULL,
    package_type VARCHAR(50) NOT NULL,
    package_weight DECIMAL(10,2),
    package_dimensions VARCHAR(50),
    service_type VARCHAR(50) NOT NULL,
    status ENUM('pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
    current_location VARCHAR(255),
    estimated_delivery DATE,
    actual_delivery TIMESTAMP NULL,
    delivery_signature TEXT,
    delivery_photo VARCHAR(255),
    price DECIMAL(10,2),
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    special_instructions TEXT,
    assigned_driver INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_driver) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_tracking (tracking_number),
    INDEX idx_customer_email (customer_email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- 3. ORDER STATUS HISTORY
-- ============================================================
CREATE TABLE order_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    location VARCHAR(255),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    notes TEXT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

-- ============================================================
-- 4. BLOG CATEGORIES
-- ============================================================
CREATE TABLE blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ============================================================
-- 5. BLOG POSTS
-- ============================================================
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_published (published_at),
    FULLTEXT idx_search (title, excerpt, content)
) ENGINE=InnoDB;

-- ============================================================
-- 6. BLOG TAGS
-- ============================================================
CREATE TABLE blog_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE blog_post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 7. SERVICES
-- ============================================================
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    short_description TEXT,
    full_description LONGTEXT,
    features JSON,
    pricing_info TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- ============================================================
-- 8. QUOTES
-- ============================================================
CREATE TABLE quotes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quote_number VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    company_name VARCHAR(100),
    pickup_postcode VARCHAR(20) NOT NULL,
    delivery_postcode VARCHAR(20) NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    package_type VARCHAR(50),
    package_weight DECIMAL(10,2),
    package_dimensions VARCHAR(50),
    delivery_date DATE,
    special_requirements TEXT,
    status ENUM('pending', 'quoted', 'accepted', 'declined', 'converted') DEFAULT 'pending',
    quoted_price DECIMAL(10,2),
    quoted_by INT,
    quoted_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quoted_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_quote_number (quote_number),
    INDEX idx_email (customer_email),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- 9. TESTIMONIALS
-- ============================================================
CREATE TABLE testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(100),
    position VARCHAR(100),
    content TEXT NOT NULL,
    rating INT DEFAULT 5,
    avatar VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_approved (is_approved),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB;

-- ============================================================
-- 10. CONTACT MESSAGES
-- ============================================================
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    replied_by INT,
    replied_at TIMESTAMP NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- 11. JOB APPLICATIONS
-- ============================================================
CREATE TABLE job_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    position VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    location VARCHAR(100),
    resume_file VARCHAR(255),
    cover_letter TEXT,
    experience_years INT,
    availability VARCHAR(50),
    status ENUM('new', 'reviewing', 'interview', 'accepted', 'rejected') DEFAULT 'new',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_position (position),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================
-- 12. SITE SETTINGS
-- ============================================================
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB;

-- ============================================================
-- 13. EMAIL NOTIFICATIONS
-- ============================================================
CREATE TABLE email_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    type VARCHAR(50),
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    related_order_id INT,
    sent_at TIMESTAMP NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (related_order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ============================================================
-- INSERT DEFAULT DATA
-- ============================================================

-- Default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@lugomax.co.uk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Default blog categories
INSERT INTO blog_categories (name, slug, description) VALUES
('Logistics News', 'logistics-news', 'Latest news and updates in logistics industry'),
('Shipping Tips', 'shipping-tips', 'Helpful tips for shipping and delivery'),
('Company Updates', 'company-updates', 'Lugomax company news and announcements'),
('Industry Insights', 'industry-insights', 'Deep dives into logistics trends');

-- Default services
INSERT INTO services (title, slug, icon, short_description, is_active, display_order) VALUES
('General Goods Delivery (UK-wide)', 'general-goods-delivery', 'package', 'Comprehensive nationwide delivery solutions for the safe and timely dispatch of goods across the UK.', TRUE, 1),
('Same-Day & Scheduled Delivery', 'same-day-delivery', 'clock', 'Flexible delivery options including urgent same-day services and convenient scheduled deliveries.', TRUE, 2),
('Business & Commercial Logistics', 'business-logistics', 'building', 'Tailored logistics solutions designed to support businesses of all sizes across the UK.', TRUE, 3);

-- Default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'Lugomax Logistics', 'text', 'Website name'),
('site_email', 'info@lugomax.co.uk', 'text', 'Contact email'),
('site_phone', '+44 20 1234 5678', 'text', 'Contact phone'),
('tracking_prefix', 'LGX', 'text', 'Prefix for tracking numbers'),
('smtp_enabled', '0', 'boolean', 'Enable SMTP for emails'),
('orders_per_page', '20', 'number', 'Orders per page in admin'),
('blog_posts_per_page', '9', 'number', 'Blog posts per page');

-- Sample testimonials
INSERT INTO testimonials (customer_name, company_name, position, content, rating, is_approved, is_featured) VALUES
('Sarah Johnson', 'TechForward', 'Operations Manager', 'Lugomax transformed our delivery operations. Their professionalism and reliability are unmatched.', 5, TRUE, TRUE),
('Michael Brown', 'Swift Supplies', 'E-commerce Owner', 'The same-day delivery service has been a game-changer for our business. Fast, reliable, and always on time.', 5, TRUE, TRUE),
('Emma Thompson', 'Delta Distributors', 'Warehouse Manager', 'Working with Lugomax has been seamless. Their team is responsive and efficient.', 5, TRUE, TRUE);
