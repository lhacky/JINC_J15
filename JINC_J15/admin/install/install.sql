DROP TABLE IF EXISTS `#__jinc_newsletter`;
CREATE TABLE IF NOT EXISTS `#__jinc_newsletter` (
  `news_id` int(10) unsigned NOT NULL auto_increment,
  `news_name` varchar(64) NOT NULL,
  `news_type` int NOT NULL default '1',
  `news_description` mediumtext,
  `published` tinyint(1) NOT NULL default '0',
  `news_created` timestamp NOT NULL,
  `news_lastsent` timestamp NOT NULL,
  `news_sendername` varchar(255) NOT NULL,
  `news_senderaddr` varchar(255) NOT NULL,
  `news_welcome_subject` varchar(255) default '',
  `news_welcome` mediumtext,
  `news_welcome_created` mediumtext,
  `news_disclaimer` mediumtext,
-- Added from JINC 0.4 for public newsletters
  `news_optin_subject` varchar(255) default '',
  `news_optin` mediumtext,
  `news_optinremove_subject` varchar(255) default '',
  `news_optinremove` mediumtext,
-- Added from JINC 0.5 for template management
  `news_default_template` int NOT NULL default 0,
-- Added from JINC 0.6 for onSubscription newsletter management
  `news_on_subscription` TINYINT(1) NOT NULL default 0,
-- Added from JINC 0.7 
  `news_jcontact_enabled` TINYINT(1) NOT NULL default 0,
  `news_front_theme` varchar(64) NOT NULL default '',
  `news_front_max_msg` int NOT NULL default 0,
  `news_front_type` int NOT NULL default 0,
  `news_attributes` TEXT,
-- Added from JINC 0.8
  `news_captcha` int NOT NULL default 0,
  `news_replyto_name` varchar(255),
  `news_replyto_addr` varchar(255),
  `news_notify` TINYINT(1) NOT NULL default 0,
-- Added from JINC 0.9
  `news_noti_id` int(10) UNSIGNED NOT NULL default 0,
  `news_input_style` int NOT NULL default 1,
  PRIMARY KEY  (`news_id`)
);

DROP TABLE IF EXISTS `#__jinc_message`;
CREATE TABLE IF NOT EXISTS `#__jinc_message` (
  `msg_id` int(10) unsigned NOT NULL auto_increment,
  `msg_news_id` int(10) unsigned NOT NULL,
  `msg_subject` varchar(128) NOT NULL,
  `msg_body` mediumtext NOT NULL,
  `msg_plaintext` tinyint(1) NOT NULL default 0,
  `msg_bulkmail` tinyint(1) NOT NULL default 0,
  `msg_datasent` timestamp NOT NULL default 0,
-- Changed from JINC 0.8
  `msg_attachment` TEXT,
  PRIMARY KEY  (`msg_id`)
);

DROP TABLE IF EXISTS `#__jinc_subscriber`;
CREATE TABLE IF NOT EXISTS `#__jinc_subscriber` (
  `subs_id` int(10) unsigned NOT NULL auto_increment,
  `subs_news_id` int(10) unsigned NOT NULL,
  `subs_user_id` int(10) unsigned NOT NULL,
  `subs_datasub` timestamp NOT NULL,
  PRIMARY KEY  (`subs_id`),
  UNIQUE KEY `subs_news_id` (`subs_news_id`,`subs_user_id`)
);

DROP TABLE IF EXISTS `#__jinc_group`;
CREATE TABLE IF NOT EXISTS `#__jinc_group` (
  `grp_id` int(10) unsigned NOT NULL auto_increment,
  `grp_name` varchar(255) NOT NULL default '',
  `grp_descr` text NOT NULL default '',
  PRIMARY KEY  (`grp_id`)
);

DROP TABLE IF EXISTS `#__jinc_membership`;
CREATE TABLE IF NOT EXISTS `#__jinc_membership` (
  `mem_id` int(10) unsigned NOT NULL auto_increment,
  `mem_grp_id` int(10) unsigned NOT NULL,
  `mem_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`mem_id`),
  UNIQUE KEY (`mem_grp_id`, `mem_user_id`)
);

DROP TABLE IF EXISTS `#__jinc_access`;
CREATE TABLE IF NOT EXISTS `#__jinc_access` (
  `acc_id` int(10) unsigned NOT NULL auto_increment,
  `acc_news_id` int(10) unsigned NOT NULL,
  `acc_grp_id` int(10) unsigned NOT NULL,
  `acc_role` int(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`acc_id`),
  UNIQUE KEY (`acc_news_id`, `acc_grp_id`, `acc_role`)
);

-- Added from JINC 0.4 for public newsletters
DROP TABLE IF EXISTS `#__jinc_public_subscriber`;
CREATE TABLE IF NOT EXISTS `#__jinc_public_subscriber` (
  `pub_id` int(10) unsigned NOT NULL auto_increment,
  `pub_news_id` int(10) unsigned NOT NULL,
  `pub_email` varchar(127) NOT NULL,
  `pub_datasub` timestamp NULL,
  `pub_random` varchar(32),
  PRIMARY KEY  (`pub_id`),
  UNIQUE KEY `news_email_key` (`pub_news_id`,`pub_email`)
);

-- Added from JINC 0.5
DROP TABLE IF EXISTS `#__jinc_template`;
CREATE TABLE IF NOT EXISTS `#__jinc_template` (
  `tem_id` int(10) unsigned NOT NULL auto_increment,
  `tem_name` varchar(128) NOT NULL,
  `tem_subject` varchar(128) NOT NULL,
  `tem_body` mediumtext NOT NULL,
  PRIMARY KEY  (`tem_id`)
);

-- Added from JINC 0.6
DROP TABLE IF EXISTS `#__jinc_stats_event`;
CREATE TABLE IF NOT EXISTS `#__jinc_stats_event` (
  `stat_id` int(10) unsigned NOT NULL auto_increment,
  `stat_type` int NOT NULL default '0',
  `stat_date` timestamp NOT NULL,
  `stat_news_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`stat_id`)
);

-- Added from JINC 0.7
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

-- Added from JINC 0.7
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

-- Added from JINC 0.9
CREATE TABLE IF NOT EXISTS `#__jinc_attribute_name` (
    `news_id` int(10) unsigned NOT NULL,
    `id` int(10) unsigned NOT NULL,
    `value` varchar(255),
    PRIMARY KEY  (`news_id`, `id`)
);

REPLACE INTO `#__jinc_attribute` (attr_name, attr_description, attr_type, attr_name_i18n)
    VALUES ('name', 'Subscriber Name', 0, 'YOUR_NAME');

DROP TABLE IF EXISTS `#__jinc_notice`;
CREATE TABLE IF NOT EXISTS `#__jinc_notice` (
    `noti_id` int(10) unsigned NOT NULL auto_increment,
    `noti_name` varchar(255) NOT NULL,
    `noti_title` varchar(255) NOT NULL,
    `noti_bdesc` varchar(255) NOT NULL default '',
    `noti_conditions` TEXT,
    `noti_type` int unsigned NOT NULL default 0,
    PRIMARY KEY  (`noti_id`)
);
