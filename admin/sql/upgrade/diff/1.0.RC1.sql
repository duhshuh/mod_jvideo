ALTER TABLE `#__jvideo_config`
    ADD COLUMN `anonDurLimit` INT( 10 ) NOT NULL DEFAULT '0',
    ADD COLUMN `enforceAnonDurLimit` TINYINT( 1 ) NOT NULL DEFAULT '0',
    ADD COLUMN `blockAnonViewers` TINYINT( 1 ) NOT NULL DEFAULT '0',
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
    ADD COLUMN `showSocialButtons` TINYINT(1) NOT NULL DEFAULT '1';

ALTER TABLE `#__jvideo_videos`
    MODIFY `status` enum('waiting for upload','pending','complete','error','cancelled','deleted') NOT NULL default 'waiting for upload';