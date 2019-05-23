ALTER TABLE `items` DROP `selected`, DROP `total_qty`, DROP `last_ordered`;
ALTER TABLE `items` ADD `primary_dept` INT NULL DEFAULT NULL AFTER `link`;
ALTER TABLE `items` ADD FOREIGN KEY (`list_id`) REFERENCES `lists`(`list_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `items` ADD FOREIGN KEY (`primary_dept`) REFERENCES `departments`(`dept_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;