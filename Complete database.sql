-- ============================================
-- MANGO MART DATABASE - COMPLETE SQL CODE
-- Database: mangomart_db
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS `mangomart_db`;
USE `mangomart_db`;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. PRODUCTS TABLE (Mango Varieties)
-- ============================================
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `variety` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `brix_rating` DECIMAL(3,1) DEFAULT NULL COMMENT 'Sweetness rating',
    `stock_kg` INT NOT NULL DEFAULT 100,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_products_price` (`price`),
    INDEX `idx_products_active` (`is_active`),
    INDEX `idx_products_variety` (`variety`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. CART TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `cart` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity_kg` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    `added_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_cart_user` (`user_id`),
    INDEX `idx_cart_product` (`product_id`),
    UNIQUE KEY `uk_cart_user_product` (`user_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. ORDERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    `payment_method` VARCHAR(50) DEFAULT 'cod',
    `shipping_address` TEXT NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_orders_user` (`user_id`),
    INDEX `idx_orders_status` (`status`),
    INDEX `idx_orders_created` (`created_at`),
    INDEX `idx_orders_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. ORDER ITEMS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `product_name` VARCHAR(100) NOT NULL,
    `quantity_kg` DECIMAL(5,2) NOT NULL,
    `price_per_kg` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_order_items_order` (`order_id`),
    INDEX `idx_order_items_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. REVIEWS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `comment` TEXT DEFAULT NULL,
    `is_approved` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_reviews_product` (`product_id`),
    INDEX `idx_reviews_rating` (`rating`),
    INDEX `idx_reviews_approved` (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. INQUIRIES / CONTACT TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `subject` VARCHAR(200) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('unread', 'read', 'replied') NOT NULL DEFAULT 'unread',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_inquiries_email` (`email`),
    INDEX `idx_inquiries_status` (`status`),
    INDEX `idx_inquiries_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. ADMIN TABLE (Optional)
-- ============================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('super_admin', 'admin', 'manager') NOT NULL DEFAULT 'admin',
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `last_login` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_admins_username` (`username`),
    INDEX `idx_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. COUPONS / DISCOUNTS TABLE (Optional)
-- ============================================
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_type` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    `discount_value` DECIMAL(10,2) NOT NULL,
    `min_order_amount` DECIMAL(10,2) DEFAULT 0,
    `max_discount` DECIMAL(10,2) DEFAULT NULL,
    `valid_from` DATE NOT NULL,
    `valid_until` DATE NOT NULL,
    `usage_limit` INT DEFAULT NULL,
    `used_count` INT NOT NULL DEFAULT 0,
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_coupons_code` (`code`),
    INDEX `idx_coupons_valid` (`valid_from`, `valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert sample users (password: 'password123' hashed)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801712345678', 'Dhaka, Bangladesh'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801812345678', 'Rajshahi, Bangladesh'),
('Mango Lover', 'mango@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801912345678', 'Chapai Nawabganj, Bangladesh');

-- Insert sample products (Mango varieties)
INSERT INTO `products` (`name`, `variety`, `description`, `price`, `image_url`, `brix_rating`, `stock_kg`) VALUES
('Himsagar', 'Himsagar', 'King of aroma, juicy & fiberless mango from Rajshahi. Naturally ripened with perfect sweetness.', 190.00, 'https://images.pexels.com/photos/1092730/pexels-photo-1092730.jpeg', 22.5, 500),
('Langra', 'Langra', 'Sweet tangy golden pulp mango. Traditional variety loved for its unique flavor profile.', 170.00, 'https://images.pexels.com/photos/8152053/pexels-photo-8152053.jpeg', 21.8, 450),
('Amrapali', 'Amrapali', 'Deep orange, rich flavor mango. High pulp content with intense sweetness.', 210.00, 'https://images.pexels.com/photos/568617/pexels-photo-568617.jpeg', 23.0, 400),
('Fazli', 'Fazli', 'Giant size, unique taste mango. Each mango weighs up to 1kg with exceptional flavor.', 250.00, 'https://images.pexels.com/photos/2320594/pexels-photo-2320594.jpeg', 22.0, 300),
('Gopalbhog', 'Gopalbhog', 'Sweet and aromatic, perfect for desserts. Soft texture with honey-like sweetness.', 180.00, 'https://images.pexels.com/photos/4246079/pexels-photo-4246079.jpeg', 21.5, 350),
('Lakshmanbhog', 'Lakshmanbhog', 'Golden yellow color with rich aroma. Medium size with excellent shelf life.', 185.00, 'https://images.pexels.com/photos/5537650/pexels-photo-5537650.jpeg', 21.0, 280);

-- Insert sample reviews
INSERT INTO `reviews` (`user_id`, `product_id`, `rating`, `comment`, `is_approved`) VALUES
(1, 1, 5, 'Absolutely delicious! Best Himsagar I have ever tasted. The sweetness is perfect.', 1),
(2, 2, 4, 'Very tasty Langra mangoes. Delivery was fast and packaging was excellent.', 1),
(3, 3, 5, 'Amrapali is my favorite! So juicy and flavorful. Will order again.', 1),
(1, 4, 5, 'Huge Fazli mangoes! Worth every taka. The taste is unforgettable.', 1),
(2, 1, 5, 'Best mango delivery service in Bangladesh. Fresh and chemical-free.', 1),
(3, 2, 4, 'Good quality mangoes. The sweetness level is just right.', 1);

-- Insert sample orders
INSERT INTO `orders` (`order_number`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_address`, `phone`) VALUES
('MGO-ABC123-20250101', 1, 380.00, 'delivered', 'cod', 'Dhaka, Bangladesh', '+8801712345678'),
('MGO-DEF456-20250105', 2, 420.00, 'shipped', 'cod', 'Rajshahi, Bangladesh', '+8801812345678'),
('MGO-GHI789-20250110', 3, 500.00, 'processing', 'online', 'Chapai Nawabganj, Bangladesh', '+8801912345678');

-- Insert sample order items
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `quantity_kg`, `price_per_kg`, `subtotal`) VALUES
(1, 1, 'Himsagar', 2.00, 190.00, 380.00),
(2, 2, 'Langra', 1.00, 170.00, 170.00),
(2, 3, 'Amrapali', 1.00, 210.00, 210.00),
(2, 4, 'Fazli', 0.50, 250.00, 125.00),
(3, 1, 'Himsagar', 1.00, 190.00, 190.00),
(3, 3, 'Amrapali', 1.00, 210.00, 210.00),
(3, 5, 'Gopalbhog', 0.50, 180.00, 90.00);

-- Insert sample inquiries
INSERT INTO `inquiries` (`name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
('Rahim Khan', 'rahim@example.com', '+8801712345678', 'Bulk Order Inquiry', 'I want to order 100kg of Himsagar mangoes for my shop. Please provide bulk discount.', 'read'),
('Fatema Begum', 'fatema@example.com', '+8801812345678', 'Delivery Question', 'When will you deliver to Chittagong? I want to place a large order.', 'unread');

-- Insert admin user (password: 'admin123')
INSERT INTO `admins` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@mangomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'super_admin');

-- Insert coupon
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_order_amount`, `valid_from`, `valid_until`) VALUES
('WELCOME10', 'percentage', 10.00, 500.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
('MANGO20', 'percentage', 20.00, 1000.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY));

-- ============================================
-- CREATE VIEWS FOR REPORTS
-- ============================================

-- View: Product sales summary
CREATE OR REPLACE VIEW `view_product_sales` AS
SELECT 
    p.id,
    p.name,
    p.variety,
    p.price,
    COALESCE(SUM(oi.quantity_kg), 0) AS total_sold_kg,
    COALESCE(SUM(oi.subtotal), 0) AS total_revenue,
    COUNT(DISTINCT oi.order_id) AS total_orders,
    AVG(r.rating) AS avg_rating
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'
LEFT JOIN reviews r ON p.id = r.product_id AND r.is_approved = 1
GROUP BY p.id;

-- View: Monthly sales report
CREATE OR REPLACE VIEW `view_monthly_sales` AS
SELECT 
    DATE_FORMAT(o.created_at, '%Y-%m') AS month,
    COUNT(DISTINCT o.id) AS total_orders,
    SUM(o.total_amount) AS total_revenue,
    AVG(o.total_amount) AS avg_order_value,
    COUNT(DISTINCT o.user_id) AS unique_customers
FROM orders o
WHERE o.status != 'cancelled'
GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
ORDER BY month DESC;

-- View: Top customers
CREATE OR REPLACE VIEW `view_top_customers` AS
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(o.id) AS total_orders,
    SUM(o.total_amount) AS total_spent,
    AVG(o.total_amount) AS avg_order_value
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE o.status != 'cancelled'
GROUP BY u.id
ORDER BY total_spent DESC
LIMIT 10;

-- ============================================
-- CREATE STORED PROCEDURES
-- ============================================

-- Procedure: Get user order history
DELIMITER //
CREATE PROCEDURE `sp_get_user_orders`(IN p_user_id INT)
BEGIN
    SELECT 
        o.order_number,
        o.total_amount,
        o.status,
        o.created_at,
        COUNT(oi.id) AS items_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = p_user_id
    GROUP BY o.id
    ORDER BY o.created_at DESC;
END //
DELIMITER ;

-- Procedure: Update product stock after order
DELIMITER //
CREATE PROCEDURE `sp_update_stock`(IN p_product_id INT, IN p_quantity_kg DECIMAL(5,2))
BEGIN
    UPDATE products 
    SET stock_kg = stock_kg - p_quantity_kg 
    WHERE id = p_product_id AND stock_kg >= p_quantity_kg;
    
    SELECT ROW_COUNT() AS affected_rows;
END //
DELIMITER ;

-- Procedure: Get dashboard statistics
DELIMITER //
CREATE PROCEDURE `sp_dashboard_stats`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM products WHERE is_active = 1) AS total_products,
        (SELECT COUNT(*) FROM orders WHERE status = 'pending') AS pending_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'shipped') AS shipped_orders,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT SUM(total_amount) FROM orders WHERE status = 'delivered') AS total_revenue,
        (SELECT COUNT(*) FROM inquiries WHERE status = 'unread') AS unread_inquiries,
        (SELECT AVG(rating) FROM reviews WHERE is_approved = 1) AS avg_rating;
END //
DELIMITER ;

-- ============================================
-- CREATE TRIGGERS
-- ============================================

-- Trigger: Update stock when order is placed
DELIMITER //
CREATE TRIGGER `trg_before_insert_order_item`
BEFORE INSERT ON `order_items`
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    
    SELECT stock_kg INTO current_stock FROM products WHERE id = NEW.product_id;
    
    IF current_stock < NEW.quantity_kg THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insufficient stock for this product';
    END IF;
    
    UPDATE products SET stock_kg = stock_kg - NEW.quantity_kg WHERE id = NEW.product_id;
END //
DELIMITER ;

-- Trigger: Auto-generate order number
DELIMITER //
CREATE TRIGGER `trg_before_insert_order`
BEFORE INSERT ON `orders`
FOR EACH ROW
BEGIN
    IF NEW.order_number IS NULL OR NEW.order_number = '' THEN
        SET NEW.order_number = CONCAT('MGO-', UPPER(SUBSTRING(MD5(RAND()), 1, 8)), '-', DATE_FORMAT(NOW(), '%Y%m%d'));
    END IF;
END //
DELIMITER ;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

-- Additional indexes for better query performance
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_order_items_order_product ON order_items(order_id, product_id);
CREATE INDEX idx_reviews_product_rating ON reviews(product_id, rating);
CREATE INDEX idx_products_price_brix ON products(price, brix_rating);

-- Full-text indexes for search
ALTER TABLE products ADD FULLTEXT INDEX ft_products_search(name, variety, description);
ALTER TABLE inquiries ADD FULLTEXT INDEX ft_inquiries_search(name, email, subject, message);

-- ============================================
-- SAMPLE QUERIES FOR TESTING
-- ============================================

-- Get best selling products
-- SELECT name, variety, total_sold_kg, total_revenue FROM view_product_sales ORDER BY total_sold_kg DESC LIMIT 5;

-- Get recent orders with user details
-- SELECT o.order_number, u.name, o.total_amount, o.status, o.created_at 
-- FROM orders o JOIN users u ON o.user_id = u.id 
-- ORDER BY o.created_at DESC LIMIT 10;

-- Search products
-- SELECT * FROM products WHERE MATCH(name, variety, description) AGAINST('sweet mango' IN NATURAL LANGUAGE MODE);

-- Get inventory alert (low stock)
-- SELECT name, variety, stock_kg FROM products WHERE stock_kg < 50 ORDER BY stock_kg ASC;

-- ============================================
-- USER PERMISSIONS (Optional)
-- ============================================

-- Create application user (recommended for production)
-- CREATE USER IF NOT EXISTS 'mangomart_app'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON mangomart_db.* TO 'mangomart_app'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- END OF SQL SCRIPT
-- ============================================