/*
SQLyog Ultimate v8.4 
MySQL - 5.1.50-community : Database - studentskit
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`studentskit` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `studentskit`;

/*Table structure for table `adaptive_payments` */

DROP TABLE IF EXISTS `adaptive_payments`;

CREATE TABLE `adaptive_payments` (
  `adaptive_payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pending_user_lesson_id` int(11) unsigned DEFAULT NULL,
  `user_lesson_id` int(11) unsigned DEFAULT NULL,
  `teacher_lesson_id` int(11) unsigned DEFAULT NULL,
  `subject_id` int(11) unsigned DEFAULT NULL,
  `student_user_id` int(11) unsigned NOT NULL,
  `status` enum('IN_PROCESS','ACTIVE','CANCELED','DEACTIVED','ERROR') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'IN_PROCESS',
  `is_approved` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `max_amount` float NOT NULL,
  `paid_amount` float DEFAULT NULL,
  `is_used` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `preapproval_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valid_thru` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`adaptive_payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `auto_approve_lesson_request` */

DROP TABLE IF EXISTS `auto_approve_lesson_request`;

CREATE TABLE `auto_approve_lesson_request` (
  `teacher_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `live` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `live_range_of_time` text COLLATE utf8_unicode_ci,
  `video` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`teacher_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `file_system` */

DROP TABLE IF EXISTS `file_system`;

CREATE TABLE `file_system` (
  `file_system_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '		',
  `parent_file_system_id` int(11) NOT NULL DEFAULT '0',
  `entity_type` enum('subject','lesson') COLLATE utf8_unicode_ci NOT NULL,
  `entity_id` int(11) NOT NULL,
  `type` enum('file','folder','delete') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size_kb` int(10) unsigned DEFAULT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`file_system_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `forum_access` */

DROP TABLE IF EXISTS `forum_access`;

CREATE TABLE `forum_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_level_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `access_level_id` (`access_level_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Users with certain access';

/*Table structure for table `forum_access_levels` */

DROP TABLE IF EXISTS `forum_access_levels`;

CREATE TABLE `forum_access_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `level` int(11) NOT NULL,
  `isAdmin` tinyint(4) NOT NULL DEFAULT '0',
  `isSuper` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Access levels for users';

/*Table structure for table `forum_forums` */

DROP TABLE IF EXISTS `forum_forums`;

CREATE TABLE `forum_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT '0',
  `access_level_id` int(11) DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `slug` varchar(115) NOT NULL,
  `deep` int(11) DEFAULT '1',
  `path` text,
  `description` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1',
  `orderNo` smallint(6) NOT NULL DEFAULT '0',
  `topic_count` int(11) NOT NULL DEFAULT '0',
  `post_count` int(11) NOT NULL DEFAULT '0',
  `accessRead` smallint(6) NOT NULL DEFAULT '0',
  `accessPost` smallint(6) NOT NULL DEFAULT '1',
  `accessPoll` smallint(6) NOT NULL DEFAULT '1',
  `accessReply` smallint(6) NOT NULL DEFAULT '1',
  `settingPostCount` smallint(6) NOT NULL DEFAULT '1',
  `settingAutoLock` smallint(6) NOT NULL DEFAULT '1',
  `lastTopic_id` int(11) DEFAULT NULL,
  `lastPost_id` int(11) DEFAULT NULL,
  `lastUser_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lastTopic_id` (`lastTopic_id`),
  KEY `lastPost_id` (`lastPost_id`),
  KEY `lastUser_id` (`lastUser_id`),
  KEY `forum_id` (`forum_id`),
  KEY `access_level_id` (`access_level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Forum categories to post topics to';

/*Table structure for table `forum_forums_i18n` */

DROP TABLE IF EXISTS `forum_forums_i18n`;

CREATE TABLE `forum_forums_i18n` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `locale` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `locale` (`locale`),
  KEY `model` (`model`),
  KEY `row_id` (`foreign_key`),
  KEY `field` (`field`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `forum_moderators` */

DROP TABLE IF EXISTS `forum_moderators`;

CREATE TABLE `forum_moderators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Moderators to forums';

/*Table structure for table `forum_poll_options` */

DROP TABLE IF EXISTS `forum_poll_options`;

CREATE TABLE `forum_poll_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) DEFAULT NULL,
  `option` varchar(100) NOT NULL,
  `vote_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options/Questions for a poll';

/*Table structure for table `forum_poll_votes` */

DROP TABLE IF EXISTS `forum_poll_votes`;

CREATE TABLE `forum_poll_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) DEFAULT NULL,
  `poll_option_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`),
  KEY `poll_option_id` (`poll_option_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Votes for polls';

/*Table structure for table `forum_polls` */

DROP TABLE IF EXISTS `forum_polls`;

CREATE TABLE `forum_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Polls attached to topics';

/*Table structure for table `forum_posts` */

DROP TABLE IF EXISTS `forum_posts`;

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `userIP` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `contentHtml` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Posts to topics';

/*Table structure for table `forum_profiles` */

DROP TABLE IF EXISTS `forum_profiles`;

CREATE TABLE `forum_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `signatureHtml` text,
  `locale` varchar(3) NOT NULL DEFAULT 'eng',
  `timezone` varchar(4) NOT NULL DEFAULT '-8',
  `totalPosts` int(10) NOT NULL DEFAULT '0',
  `totalTopics` int(10) NOT NULL DEFAULT '0',
  `currentLogin` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='User profiles';

/*Table structure for table `forum_reported` */

DROP TABLE IF EXISTS `forum_reported`;

CREATE TABLE `forum_reported` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `itemType` smallint(6) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reported topics, posts, users, etc';

/*Table structure for table `forum_settings` */

DROP TABLE IF EXISTS `forum_settings`;

CREATE TABLE `forum_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='Forum settings';

/*Table structure for table `forum_subscriptions` */

DROP TABLE IF EXISTS `forum_subscriptions`;

CREATE TABLE `forum_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='User topic and forum subscriptions.';

/*Table structure for table `forum_topics` */

DROP TABLE IF EXISTS `forum_topics`;

CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `language` char(2) NOT NULL DEFAULT 'en',
  `slug` varchar(110) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `post_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `firstPost_id` int(11) DEFAULT NULL,
  `lastPost_id` int(11) DEFAULT NULL,
  `lastUser_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `firstPost_id` (`firstPost_id`),
  KEY `lastPost_id` (`lastPost_id`),
  KEY `lastUser_id` (`lastUser_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Discussion topics';

/*Table structure for table `i18n` */

DROP TABLE IF EXISTS `i18n`;

CREATE TABLE `i18n` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `locale` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `locale` (`locale`),
  KEY `model` (`model`),
  KEY `row_id` (`foreign_key`),
  KEY `field` (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `images` */

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_source` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_resize` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `message_enum` text COLLATE utf8_unicode_ci NOT NULL,
  `message_params` text COLLATE utf8_unicode_ci,
  `link` text COLLATE utf8_unicode_ci,
  `unread` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`notification_id`),
  KEY `NewIndex1` (`user_id`,`unread`)
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `payment_info` */

DROP TABLE IF EXISTS `payment_info`;

CREATE TABLE `payment_info` (
  `payment_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_ID` int(10) unsigned NOT NULL,
  `last_4_digits` int(11) unsigned NOT NULL,
  `cc_ref` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_info_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `pending_user_lessons` */

DROP TABLE IF EXISTS `pending_user_lessons`;

CREATE TABLE `pending_user_lessons` (
  `pending_user_lesson_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_lesson_id` int(11) unsigned DEFAULT NULL,
  `status` enum('ACTIVE','CANCELED','EXECUTED') COLLATE utf8_unicode_ci DEFAULT 'ACTIVE',
  `action` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teacher_lesson_id` int(11) unsigned DEFAULT NULL,
  `subject_id` int(11) unsigned DEFAULT NULL,
  `teacher_user_id` int(11) unsigned DEFAULT NULL,
  `student_user_id` int(11) unsigned NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `duration_minutes` int(11) unsigned DEFAULT NULL,
  `1_on_1_price` float unsigned DEFAULT NULL,
  `max_students` int(11) unsigned DEFAULT NULL,
  `full_group_total_price` float unsigned DEFAULT NULL,
  `extra` text COLLATE utf8_unicode_ci,
  `reverse_stage` tinyint(1) DEFAULT '0',
  `version` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pending_user_lesson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `student_tests` */

DROP TABLE IF EXISTS `student_tests`;

CREATE TABLE `student_tests` (
  `student_test_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_enable` tinyint(4) DEFAULT '1',
  `entity_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entity_type` enum('subject','lesson') COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `questions` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`student_test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `subject_catalog` */

DROP TABLE IF EXISTS `subject_catalog`;

CREATE TABLE `subject_catalog` (
  `subject_catalog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`subject_catalog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `subject_categories` */

DROP TABLE IF EXISTS `subject_categories`;

CREATE TABLE `subject_categories` (
  `subject_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_subject_category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `path` text COLLATE utf8_unicode_ci,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `forum_id` int(11) DEFAULT NULL,
  `deep` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`subject_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `subject_categories_i18n` */

DROP TABLE IF EXISTS `subject_categories_i18n`;

CREATE TABLE `subject_categories_i18n` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `locale` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `foreign_key` int(10) NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `locale` (`locale`),
  KEY `model` (`model`),
  KEY `row_id` (`foreign_key`),
  KEY `field` (`field`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `subjects` */

DROP TABLE IF EXISTS `subjects`;

CREATE TABLE `subjects` (
  `subject_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `lesson_type` enum('live','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'live',
  `subject_category_id` int(11) DEFAULT NULL,
  `catalog_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `image` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `image_source` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_resize` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_38x38` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_58x58` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_60x60` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_63x63` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_72x72` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_78x78` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_80x80` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_100x100` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_128x95` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_149x182` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_200x210` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_436x214` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_enable` tinyint(4) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `language` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `is_public` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `duration_minutes` int(11) NOT NULL DEFAULT '60',
  `total_lessons` int(11) unsigned NOT NULL DEFAULT '0',
  `students_amount` int(11) NOT NULL DEFAULT '0',
  `raters_amount` int(11) NOT NULL DEFAULT '0',
  `avarage_rating` float NOT NULL DEFAULT '0',
  `1_on_1_price` float unsigned NOT NULL,
  `max_students` int(11) unsigned DEFAULT NULL,
  `full_group_student_price` float unsigned DEFAULT NULL,
  `full_group_total_price` float unsigned DEFAULT NULL,
  `is_locked` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `lock_ends` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `NewIndex1` (`type`,`language`,`is_public`,`created`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `teacher_about_videos` */

DROP TABLE IF EXISTS `teacher_about_videos`;

CREATE TABLE `teacher_about_videos` (
  `teacher_about_video_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_user_id` int(11) unsigned NOT NULL,
  `video_source` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `language` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`teacher_about_video_id`),
  KEY `teacher_user_id_language` (`teacher_user_id`,`language`),
  KEY `teacher_user_id` (`teacher_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `teacher_certificates` */

DROP TABLE IF EXISTS `teacher_certificates`;

CREATE TABLE `teacher_certificates` (
  `teacher_certificate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_user_id` int(11) unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `image` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `image_resize` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_source` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_78x78` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`teacher_certificate_id`),
  KEY `teacher_user_id` (`teacher_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `teacher_lessons` */

DROP TABLE IF EXISTS `teacher_lessons`;

CREATE TABLE `teacher_lessons` (
  `teacher_lesson_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `request_subject_id` int(11) DEFAULT NULL,
  `teacher_user_id` int(11) NOT NULL,
  `student_user_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `subject_category_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `lesson_type` enum('live','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'live',
  `is_public` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `is_deleted` tinyint(2) DEFAULT '0',
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `image` tinyint(2) NOT NULL DEFAULT '0',
  `image_source` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_resize` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_38x38` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_58x58` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_60x60` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_63x63` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_72x72` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_78x78` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_80x80` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_100x100` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_128x95` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_149x182` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_200x210` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_436x214` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `1_on_1_price` float NOT NULL,
  `max_students` int(11) unsigned DEFAULT '1',
  `full_group_student_price` float DEFAULT NULL,
  `full_group_total_price` float DEFAULT NULL,
  `num_of_pending_join_requests` float NOT NULL DEFAULT '0',
  `num_of_students` int(11) NOT NULL DEFAULT '0',
  `num_of_pending_invitations` int(11) NOT NULL DEFAULT '0',
  `notification_status` tinyint(2) NOT NULL DEFAULT '0',
  `payment_status` tinyint(2) NOT NULL DEFAULT '0',
  `rating_status` tinyint(2) NOT NULL DEFAULT '0',
  `is_locked` tinyint(2) NOT NULL DEFAULT '0',
  `lock_ends` datetime DEFAULT NULL,
  PRIMARY KEY (`teacher_lesson_id`),
  KEY `payment` (`lesson_type`,`payment_status`,`datetime`),
  KEY `NewIndex1` (`datetime`,`lesson_type`,`notification_status`,`payment_status`),
  KEY `NewIndex2` (`end_datetime`,`payment_status`,`rating_status`),
  KEY `NewIndex3` (`subject_id`),
  KEY `subject_startdatetime` (`subject_id`,`datetime`),
  KEY `teacher_user_id_start_datetime_end_datetime` (`teacher_user_id`,`datetime`,`end_datetime`,`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `threads` */

DROP TABLE IF EXISTS `threads`;

CREATE TABLE `threads` (
  `thread_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `by_user_id` int(10) unsigned NOT NULL,
  `by_user_type` enum('teacher','student') COLLATE utf8_unicode_ci NOT NULL,
  `by_user_unread_messages` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `to_user_id` int(11) unsigned NOT NULL,
  `to_user_type` enum('teacher','student') COLLATE utf8_unicode_ci NOT NULL,
  `to_user_unread_messages` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `entity_type` enum('subject','lesson') COLLATE utf8_unicode_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `messages` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`thread_id`),
  KEY `by_user_id` (`by_user_id`,`by_user_unread_messages`),
  KEY `to_user_id` (`to_user_id`,`to_user_unread_messages`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `user_lessons` */

DROP TABLE IF EXISTS `user_lessons`;

CREATE TABLE `user_lessons` (
  `user_lesson_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '	',
  `teacher_lesson_id` int(11) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `request_subject_id` int(11) DEFAULT NULL,
  `teacher_user_id` int(11) DEFAULT NULL,
  `student_user_id` int(11) NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `stage` tinyint(4) NOT NULL,
  `subject_category_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `is_public` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `lesson_type` enum('live','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'video',
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `image` tinyint(2) NOT NULL DEFAULT '0',
  `image_source` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_resize` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_38x38` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_58x58` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_60x60` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_63x63` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_72x72` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_78x78` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_80x80` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_100x100` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_128x95` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_149x182` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_200x210` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_436x214` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `1_on_1_price` float unsigned NOT NULL,
  `max_students` int(10) unsigned DEFAULT NULL,
  `full_group_student_price` float unsigned DEFAULT NULL,
  `full_group_total_price` float DEFAULT NULL,
  `rating_by_student` float DEFAULT NULL,
  `comment_by_student` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_image` tinyint(2) NOT NULL DEFAULT '0',
  `rating_by_teacher` float DEFAULT NULL,
  `comment_by_teacher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notification_status` tinyint(2) NOT NULL DEFAULT '0',
  `payment_status` tinyint(2) NOT NULL DEFAULT '0',
  `version` char(36) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_lesson_id`),
  KEY `NewIndex2` (`teacher_lesson_id`),
  KEY `NewIndex1` (`payment_status`,`stage`,`notification_status`,`teacher_lesson_id`),
  KEY `NewIndex3` (`subject_id`),
  KEY `student_user_id_start_datetime_end_datetime` (`student_user_id`,`datetime`,`end_datetime`)
) ENGINE=MyISAM AUTO_INCREMENT=287 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facebook_id` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activation_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(2) DEFAULT '0',
  `first_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` tinyint(2) NOT NULL DEFAULT '0',
  `image_source` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_resize` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_38x38` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_60x60` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_63x63` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_72x72` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_78x78` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_80x80` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_100x100` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_149x182` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_crop_200x210` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `zipcode` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_avarage_rating` float unsigned NOT NULL DEFAULT '0',
  `student_about` text COLLATE utf8_unicode_ci,
  `student_raters_amount` int(10) unsigned NOT NULL DEFAULT '0',
  `student_total_lessons` int(10) unsigned NOT NULL DEFAULT '0',
  `students_total_learning_minutes` int(11) DEFAULT '0',
  `teacher_about` text COLLATE utf8_unicode_ci,
  `teaching_address` text COLLATE utf8_unicode_ci,
  `teacher_zipcode` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teacher_total_teaching_minutes` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_students_amount` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_total_lessons` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_avarage_rating` float unsigned NOT NULL DEFAULT '0',
  `teacher_raters_amount` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_paypal_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'eng',
  `languages_of_records` text COLLATE utf8_unicode_ci,
  `title` enum('Dr.','Mr.') COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(25) COLLATE utf8_unicode_ci DEFAULT 'UTC',
  `currentLogin` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `NewIndex1` (`email`),
  KEY `NewIndex2` (`facebook_id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `watchitoo_lesson_meetings` */

DROP TABLE IF EXISTS `watchitoo_lesson_meetings`;

CREATE TABLE `watchitoo_lesson_meetings` (
  `watchitoo_lesson_meeting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_lesson_id` int(11) unsigned NOT NULL,
  `meeting_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`watchitoo_lesson_meeting_id`),
  KEY `NewIndex1` (`teacher_lesson_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `watchitoo_lesson_users` */

DROP TABLE IF EXISTS `watchitoo_lesson_users`;

CREATE TABLE `watchitoo_lesson_users` (
  `user_id` int(11) unsigned NOT NULL,
  `watchitoo_user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `watchitoo_subject_meetings` */

DROP TABLE IF EXISTS `watchitoo_subject_meetings`;

CREATE TABLE `watchitoo_subject_meetings` (
  `watchitoo_subject_meeting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) unsigned NOT NULL,
  `meeting_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`watchitoo_subject_meeting_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `watchitoo_subject_teachers` */

DROP TABLE IF EXISTS `watchitoo_subject_teachers`;

CREATE TABLE `watchitoo_subject_teachers` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `watchitoo_user_id` int(11) unsigned NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `NewIndex1` (`subject_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
