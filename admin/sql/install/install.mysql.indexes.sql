ALTER TABLE `#__jvideo_videos` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `#__jvideo_videos` ADD INDEX `idx_status` (`status`);
ALTER TABLE `#__jvideo_videos` ADD INDEX `idx_published` (`published`);
ALTER TABLE `#__jvideo_videos` ADD INDEX `idx_publish_up_publish_down` (`publish_up`, `publish_down`);
ALTER TABLE `#__jvideo_thumbnails` ADD INDEX `idx_videoID_width_height` ( `videoID` , `width` , `height` ); 
ALTER TABLE `#__jvideo_videos_categories` ADD INDEX `idx_video_id` (`video_id`);
ALTER TABLE `#__jvideo_videos_categories` ADD INDEX `idx_category_id` (`category_id`);
