ALTER TABLE `customers` ADD `is_subscribed` TINYINT(1) NULL DEFAULT '1' AFTER `country`, ADD `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_subscribed`;


ALTER TABLE `gallery_images`
ADD COLUMN `image_width` INT UNSIGNED NULL AFTER `image_path`,
ADD COLUMN `image_height` INT UNSIGNED NULL AFTER `image_width`;