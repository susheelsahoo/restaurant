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
    status ENUM('submitted','approved','rejected','ordered'),
    needed_by DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
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
    request_id INT,
    supplier_id INT,
    buyer_id INT,
    status ENUM('draft','sent','confirmed','partial','completed','delayed'),
    order_date DATE,
    expected_delivery DATE
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