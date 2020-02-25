CREATE TABLE IF NOT EXISTS `pack_sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `short_name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `pack_sizes` (`name`, `short_name`) VALUES ('Item', 'It');

ALTER TABLE `items` ADD `packsize_id` INT NOT NULL DEFAULT '1' AFTER `mute_perm`;
ALTER TABLE `items` ADD FOREIGN KEY (`packsize_id`) REFERENCES `pack_sizes`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;