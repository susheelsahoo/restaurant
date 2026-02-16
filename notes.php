ALTER TABLE `reservations` CHANGE `status` `status` ENUM('pending','confirmed','declined','complete','in-house') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending';
CREATE TABLE reservation_statuses (
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(50) UNIQUE,
label VARCHAR(255),
color VARCHAR(20) DEFAULT 'secondary',
sort_order INT DEFAULT 0,
is_active TINYINT(1) DEFAULT 1,
created_at TIMESTAMP NULL DEFAULT NULL,
updated_at TIMESTAMP NULL DEFAULT NULL
);

ALTER TABLE reservations
ADD COLUMN status_id BIGINT UNSIGNED NULL AFTER id;

ALTER TABLE reservations
ADD CONSTRAINT fk_reservations_status_id
FOREIGN KEY (status_id)
REFERENCES reservation_statuses(id)
ON DELETE RESTRICT;
INSERT INTO reservation_statuses
(name, label, color, sort_order, is_active, created_at, updated_at)
VALUES
('pending', 'Pending', 'warning', 1, 1, NOW(), NOW()),
('confirmed', 'Confirmed', 'success', 2, 1, NOW(), NOW()),
('declined', 'Declined', 'danger', 3, 1, NOW(), NOW()),
('in-house', 'In-House', 'info', 4, 1, NOW(), NOW()),
('complete', 'Complete', 'primary', 5, 1, NOW(), NOW());

UPDATE reservations r
JOIN reservation_statuses rs
ON rs.name = r.status
SET r.status_id = rs.id
WHERE r.status IS NOT NULL;
ALTER TABLE reservations DROP COLUMN status;