ALTER TABLE `#__jinc_newsletter` ADD COLUMN `news_jcontact_enabled` TINYINT(1) NOT NULL default 0;
ALTER TABLE `#__jinc_newsletter` ADD COLUMN `news_front_theme` varchar(64) NOT NULL default '';
ALTER TABLE `#__jinc_newsletter` ADD COLUMN `news_front_type` int NOT NULL default 0;
ALTER TABLE `#__jinc_newsletter` ADD COLUMN `news_front_max_msg` int NOT NULL default 0;
ALTER TABLE `#__jinc_newsletter` ADD COLUMN `news_attributes` TEXT;

ALTER TABLE `#__jinc_public_subscriber` ADD COLUMN `pub_name` varchar(127) NOT NULL;

DROP TABLE IF EXISTS `#__jinc_process`;
CREATE TABLE IF NOT EXISTS `#__jinc_process` (
  `proc_id` int(10) unsigned NOT NULL auto_increment,
  `proc_msg_id` int(10) unsigned NOT NULL,
  `proc_status` int(2) unsigned NOT NULL default 0,
  `proc_start_time` timestamp NOT NULL default 0,
  `proc_last_update_time` timestamp NOT NULL,
  `proc_last_subscriber_time` timestamp NOT NULL default 0,
  `proc_last_subscriber_id` int(10) unsigned NOT NULL default 0,
  `proc_sent_messages` int(10) unsigned NOT NULL default 0,
  `proc_sent_success` int(10) unsigned NOT NULL default 0,
  `proc_client_id` varchar(32) default '',
  PRIMARY KEY  (`proc_id`)
);

DROP TABLE IF EXISTS `#__jinc_attribute`;
CREATE TABLE IF NOT EXISTS `#__jinc_attribute` (
  `attr_id` int(10) unsigned NOT NULL auto_increment,
  `attr_name` varchar(32) NOT NULL,
  `attr_description` varchar(255) NOT NULL,
  `attr_type` int(10) unsigned NOT NULL default 0,
  `attr_table_name` varchar(64) NOT NULL,
  `attr_name_i18n` varchar(128) NOT NULL,
  PRIMARY KEY  (`attr_id`),
  UNIQUE KEY  (`attr_name`)
);
