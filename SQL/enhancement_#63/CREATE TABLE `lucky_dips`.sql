CREATE TABLE `lucky_dips` ( `id` INT NOT NULL AUTO_INCREMENT , `name` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NULL DEFAULT NULL , `list_id` INT NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_520_ci;

ALTER TABLE `lucky_dips` ADD  FOREIGN KEY (`list_id`) REFERENCES `lists`(`list_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `items` ADD `luckydip_id` INT NULL DEFAULT NULL AFTER `packsize_id`;
ALTER TABLE `items` ADD FOREIGN KEY (`luckydip_id`) REFERENCES `lucky_dips`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;