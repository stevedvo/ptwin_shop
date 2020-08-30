CREATE TABLE `meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `meal_items` ( `id` INT NOT NULL AUTO_INCREMENT , `meal_id` INT NOT NULL , `item_id` INT NOT NULL , `quantity` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_520_ci;

ALTER TABLE `meal_items` ADD FOREIGN KEY (`item_id`) REFERENCES `items`(`item_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `meal_items` ADD FOREIGN KEY (`meal_id`) REFERENCES `meals`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `meal_plan_days` ( `id` INT NOT NULL AUTO_INCREMENT , `date` DATE NOT NULL , `meal_id` INT NOT NULL , `order_item_status` ENUM('0','10') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_520_ci;

ALTER TABLE `meal_plan_days` ADD FOREIGN KEY (`meal_id`) REFERENCES `meals`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;