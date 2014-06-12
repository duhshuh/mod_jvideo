ALTER TABLE `#__jvideos`
    RENAME TO `#__jvideo_videos`,
    MODIFY `status` enum('waiting for upload','pending','complete','error','cancelled','deleted') NOT NULL default 'waiting for upload',
    ENGINE = MyISAM;

ALTER TABLE `#__jvideo_categories`
	CHANGE COLUMN `category_name` `name` varchar(100) NOT NULL,
    ADD COLUMN `nestLeft` int(11) NULL,
    ADD COLUMN `nestRight` int(11) NULL,
    ADD COLUMN `active` tinyint(1) NOT NULL DEFAULT 1,
    ENGINE = MyISAM;

ALTER TABLE `#__jvideos_categories`
    RENAME TO `#__jvideo_videos_categories`,
    DROP PRIMARY KEY,
    DROP COLUMN `id`,
    ADD PRIMARY KEY (`video_id`, `category_id`),
    ENGINE = MyISAM;

ALTER TABLE `#__jvideo_config`
    ADD COLUMN `installStatus` ENUM('step1', 'step2', 'step3', 'step4', 'step5', 'complete') NOT NULL DEFAULT 'step1',
    ADD COLUMN `anonDurLimit` INT(10) NOT NULL DEFAULT '0',
    ADD COLUMN `enforceAnonDurLimit` TINYINT(1) NOT NULL DEFAULT '0',
    ADD COLUMN `blockAnonViewers` TINYINT(1) NOT NULL DEFAULT '0',
    ADD COLUMN `showInfoBox` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showAuthor` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showJoinDate` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showVideoCount` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showDateAdded` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showCategories` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showTags` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showLinkToVideo` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showViews` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showDescription` TINYINT(1) NOT NULL DEFAULT '1',
    ADD COLUMN `showSocialButtons` TINYINT(1) NOT NULL DEFAULT '1',
    ENGINE = MyISAM;

ALTER TABLE `#__jvideo_featured` ENGINE = MyISAM;
ALTER TABLE `#__jvideo_ranking` ENGINE = MyISAM;
ALTER TABLE `#__jvideo_rating` ENGINE = MyISAM;
ALTER TABLE `#__jvideo_thumbnails` ENGINE = MyISAM;
ALTER TABLE `#__jvideo_users` ENGINE = MyISAM;

DROP TABLE IF EXISTS `#__jvideo_upgrade_categories_lookup`;
CREATE TABLE `#__jvideo_upgrade_categories_lookup` (
    `oldCategoryId` INT NOT NULL,
    `newCategoryId` INT NOT NULL
);