Right now when request is approve with multiple category then it will create multiple PO for each category. 
I want to create only one PO with sub PO like 
main PO: PO-2026-0107
- sub PO 1: PO-2026-0107-A
- sub PO 2: PO-2026-0107-B
- sub PO 3: PO-2026-0107-C
for html design you can review this html:/Applications/XAMPP/xamppfiles/htdocs/restaurant/public/supplier_order_dispatch_list.html

If you want smoother UX:

Show “New version available” popup
Auto reload after SW update

If you want, next I can:
✅ Add background sync (offline form submit)
✅ Add push notifications (Laravel + Firebase)
✅ Make it enterprise-level PWA


CREATE TABLE `purchase_order_templates` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `department_id` BIGINT UNSIGNED NULL,
    `priority` ENUM('normal', 'urgent', 'low') NOT NULL DEFAULT 'normal',
    `status` ENUM('active', 'draft', 'archived') NOT NULL DEFAULT 'active',
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_order_template_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `template_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NULL,
    `item_name` VARCHAR(255) NOT NULL,
    `category_name` VARCHAR(255) NULL,
    `default_quantity` DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    `unit` VARCHAR(50) NOT NULL,
    `note` TEXT NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `purchase_order_template_items_template_id_index` (`template_id`),
    KEY `purchase_order_template_items_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `purchase_order_template_items`
ADD CONSTRAINT `poti_template_id_fk`
FOREIGN KEY (`template_id`) REFERENCES `purchase_order_templates`(`id`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `purchase_order_template_items`
ADD CONSTRAINT `poti_product_id_fk`
FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `purchase_order_templates`
ADD CONSTRAINT `pot_department_id_fk`
FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`)
ON DELETE SET NULL ON UPDATE CASCADE;


CREATE TABLE product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL,
    monthly_budget INT(11) NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE products
    ADD COLUMN category_id INT NULL AFTER sku,
    ADD CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES product_categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

ALTER TABLE products
    DROP COLUMN category;


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    sku VARCHAR(50) UNIQUE,
    category_id INT NULL,
    unit VARCHAR(20),
    barcode VARCHAR(50),
    status ENUM('active','inactive'),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES product_categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);




CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    sku VARCHAR(50) UNIQUE,
    category VARCHAR(100),
    unit VARCHAR(20),
    estimated_price DECIMAL(10, 2) NULL,
    barcode VARCHAR(50),
    status ENUM('active','inactive')
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(50),
    status ENUM('active','review')
);
CREATE TABLE product_suppliers (
    product_id INT,
    supplier_id INT,
    PRIMARY KEY (product_id, supplier_id)
);
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_no VARCHAR(50) UNIQUE,
    user_id INT,
    department_id INT,
    priority ENUM('low','normal','urgent'),
    status ENUM('submitted','approved','rejected','ordered', 'returned'),
    needed_by DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS category_suppliers (
    category_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY category_suppliers_unique (category_id, supplier_id),
    KEY category_suppliers_supplier_idx (supplier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO category_suppliers (category_id, supplier_id)
SELECT DISTINCT p.category_id, ps.supplier_id
FROM products p
INNER JOIN product_suppliers ps ON ps.product_id = p.id
WHERE p.category_id IS NOT NULL
  AND ps.supplier_id IS NOT NULL;


CREATE TABLE request_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT,
    product_id INT,
    quantity DECIMAL(10,2),
    supplier_id INT,
    notes TEXT
);
CREATE TABLE approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT,
    approver_id INT,
    status ENUM('pending','approved','rejected','returned'),
    comment TEXT,
    updated_at TIMESTAMP
);

CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50) UNIQUE,

    parent_po_id BIGINT UNSIGNED NULL,
    po_suffix VARCHAR(10) NULL,

    request_id INT,
    supplier_id INT,
    buyer_id INT,

    status ENUM('draft','sent','confirmed','partial','completed','delayed'),

    order_date DATE,
    expected_delivery DATE,

    INDEX purchase_orders_parent_po_id_idx (parent_po_id)
);

CREATE TABLE po_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT,
    product_id INT,
    quantity DECIMAL(10,2),
    received_qty DECIMAL(10,2) DEFAULT 0,
    unit_price DECIMAL(10,2)
);
CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    delivery_no VARCHAR(50),
    po_id INT,
    status ENUM('expected','partial','completed','delayed'),
    received_by INT,
    delivery_date DATETIME
);

SELECT r.request_no, u.name, p.name AS product, ri.quantity
FROM requests r
JOIN users u ON r.user_id = u.id
JOIN request_items ri ON ri.request_id = r.id
JOIN products p ON p.id = ri.product_id;

SELECT r.request_no, a.status, u.name AS approver
FROM approvals a
JOIN requests r ON r.id = a.request_id
JOIN users u ON u.id = a.approver_id
WHERE a.status = 'pending';

SELECT po.po_number, s.name AS supplier, p.name AS product, pi.quantity
FROM purchase_orders po
JOIN suppliers s ON s.id = po.supplier_id
JOIN po_items pi ON pi.po_id = po.id
JOIN products p ON p.id = pi.product_id;

SELECT d.delivery_no, po.po_number, d.status
FROM deliveries d
JOIN purchase_orders po ON po.id = d.po_id;

SELECT p.name, COUNT(*) as total
FROM request_items ri
JOIN products p ON p.id = ri.product_id
GROUP BY p.id
ORDER BY total DESC
LIMIT 5;

CREATE INDEX idx_request_user ON requests(user_id);
CREATE INDEX idx_po_supplier ON purchase_orders(supplier_id);

ALTER TABLE requests 
ADD COLUMN manager_comment TEXT NULL AFTER status,
ADD COLUMN admin_comment TEXT NULL AFTER manager_comment;


ALTER TABLE products
CONVERT TO CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;


ALTER DATABASE tifliszo
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;


php artisan migrate --path=database/migrations/2026_05_04_000002_add_department_id_to_users_table.php
php artisan db:seed --class=RolesPermissionsSeeder
php artisan optimize:clear


