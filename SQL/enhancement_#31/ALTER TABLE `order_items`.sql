ALTER TABLE `order_items` ADD `checked` BOOLEAN NOT NULL DEFAULT FALSE AFTER `quantity`;

## USE CORRECT ORDER ID
UPDATE `order_items` SET `checked`=1 WHERE `order_id`<{ID};