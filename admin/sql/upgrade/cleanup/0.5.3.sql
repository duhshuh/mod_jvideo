ALTER TABLE `#__jvideo_categories` DROP COLUMN `parent_id`, DROP COLUMN `breadcrumb`;
DROP TABLE IF EXISTS `#__jvideo_upgrade_categories_lookup`;