CREATE TABLE IF NOT EXISTS `#__jvideo_config` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`reg_level` int(11) default NULL,
	`show_embeded` tinyint(4) default NULL,
	`min_appr_gid` tinyint(4) default '25',
	`require_admin_vid_appr` tinyint(4) default '0',
	`auto_play` tinyint(4) default NULL,
	`has_ratings` tinyint(4) default NULL,
	`brad_pos` int(11) default NULL,
	`has_png` tinyint(4) default NULL,
	`min_gid` int(11) default NULL,
	`client_guid` varchar(200) default NULL,
	`infino_uname` blob,
	`infino_pass` blob,
	`infino_domain` varchar(100) default NULL,
	`infino_accountId` int(11) default NULL,
	`infino_userId` int(11) default NULL,
	`infino_acctKey` varchar(100) NOT NULL,
	`infino_secretKey` varchar(100) NOT NULL,
    `video_player_height` int(11) default '360',
    `video_player_width` int(11) default '640',
    `aspect_constraint` int(11) default '1',
	`profile_system` VARCHAR( 25 ) NOT NULL DEFAULT 'default',
	`video_system` varchar(25) NOT NULL default 'default',
	`comments_system` varchar(25) NOT NULL default 'default',
	`map_profile_url` varchar(100) default NULL,
	`map_profile_table` varchar(50) default NULL,
	`map_profile_id` VARCHAR( 50 ) default NULL,
	`map_profile_user_id` varchar(50) default NULL,
	`map_profile_avatar` varchar(50) default NULL,
	`map_profile_avatar_prefix` varchar(50) default NULL,
	`proxyEnabled` TINYINT(1) NOT NULL DEFAULT '0',
	`proxyHost` VARCHAR( 100 ) NULL ,
	`proxyPort` INT NULL ,
	`proxyUsername` VARCHAR( 25 ) NULL ,
	`proxyPassword` VARCHAR( 25 ) NULL ,
	`proxyTimeout` INT NULL ,
	`proxyResponseTimeout` INT NULL,
	`cacheThumbnails` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`seoEnabled` TINYINT( 1 ) NOT NULL DEFAULT  '0',
	`seoFileExtension` VARCHAR( 10 ) NULL,
	`version` VARCHAR( 25 ) NOT NULL DEFAULT '0.0.0',
	`lastIncrSync` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
	`lastFullSync` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
	`incrSyncInterval` INT NOT NULL DEFAULT '30',
	`fullSyncInterval` INT NOT NULL DEFAULT '24',
	`thumbFaderEnabled` TINYINT( 1 ) NOT NULL DEFAULT '1',
	`sizeLimit` BIGINT UNSIGNED NOT NULL DEFAULT '0',
	`recordingLimit` INT UNSIGNED NOT NULL DEFAULT '0',
	`maxVideosPerUser` INT UNSIGNED NOT NULL DEFAULT '0',
	`maxDuration` INT UNSIGNED NOT NULL DEFAULT '0',
    `showLinkback` TINYINT( 1 ) NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__jvideos` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int(11) default NULL,
	`admin_approved` int(11) default 0,
	`video_title` varchar(200) default NULL,
	`video_desc` text,
	`tags` varchar(255) default NULL,
	`status` enum('pending','complete','error','cancelled','deleted') NOT NULL default 'pending',
	`transaction_dt` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	`hits` int(11) NOT NULL default 0,
	`infin_vid_id` varchar(50) default NULL,
	`thumb_url` varchar(1000) default NULL,
	`duration` float UNSIGNED NULL,
	`date_added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`published` TINYINT(1) NOT NULL DEFAULT '1',
	`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__jvideo_categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`category_name` varchar(100) default NULL,
	`parent_id` int(11) default NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__jvideos_categories` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`category_name` varchar(100) default NULL,
	`parent_id` int(11) default NULL,
	`breadcrumb` varchar(255) default NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__jvideo_featured` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`v_id` int(11) default NULL,
	`feature_rank` int(11) default '1'
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `#__jvideo_ranking` (
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`v_id` int(11) default NULL,
	`rank` double default NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__jvideo_rating` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`v_id` INT( 11 ) NOT NULL ,
	`user_id` INT ( 11 ) NOT NULL ,
	`rating` INT( 11 ) NOT NULL ,
	UNIQUE KEY `v_id` (`v_id`,`user_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__jvideo_users` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`user_id` INT NOT NULL ,
	`display_name` VARCHAR( 50 ) NULL ,
	`birthdate` DATE NULL ,
	`location` VARCHAR( 50 ) NULL ,
	`description` VARCHAR( 250 ) NULL ,
	`occupation` VARCHAR( 100 ) NULL ,
	`interests` VARCHAR( 250 ) NULL ,
	`website` VARCHAR( 100 ) NULL, 
	`avatar` VARCHAR( 100 ) NULL
) ENGINE = innodb;

CREATE TABLE IF NOT EXISTS `#__jvideo_thumbnails` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`videoID` INT NOT NULL,
	`imageURL` VARCHAR(500) NULL,
	`timeIndex` FLOAT NOT NULL DEFAULT '0',
	`width` INT NOT NULL DEFAULT '0',
	`height` INT NOT NULL DEFAULT '0'
) ENGINE = InnoDB;
