ALTER TABLE `blogs` ADD `is_deleted` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_published`;
ALTER TABLE `pages` ADD `is_deleted` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_active`;

UPDATE `pages` SET `is_deleted` = '0'
UPDATE `blogs` SET `is_deleted` = '0'