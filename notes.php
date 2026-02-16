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
ALTER TABLE reservations DROP COLUMN status;
ALTER TABLE reservations
ADD CONSTRAINT fk_reservations_status_id
FOREIGN KEY (status_id)
REFERENCES reservation_statuses(id)
ON DELETE RESTRICT;