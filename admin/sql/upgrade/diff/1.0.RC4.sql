ALTER TABLE `#__jvideo_config`
    ADD COLUMN `showSocialButtons` TINYINT(1) NOT NULL DEFAULT '1';

ALTER TABLE `#__jvideo_videos`
    MODIFY `status` enum('waiting for upload','pending','complete','error','cancelled','deleted') NOT NULL default 'waiting for upload';