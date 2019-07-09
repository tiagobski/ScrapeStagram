-- Collations
ALTER TABLE `accounts` CHANGE `biography_accounts` `biography_accounts` VARCHAR(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `posts` CHANGE `description_posts` `description_posts` VARCHAR(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

-- Default 'undefined' PK values
INSERT INTO `locations` (`id_locations`, `external_id_locations`, `slug_locations`, `name_locations`) VALUES (NULL, '0', 'undefined', 'Undefined');