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

/*Table structure for table `auto_approve_lesson_request` */

DROP TABLE IF EXISTS `auto_approve_lesson_request`;

CREATE TABLE `auto_approve_lesson_request` (
  `teacher_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `live` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `live_range_of_time` text COLLATE utf8_unicode_ci,
  `video` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`teacher_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `auto_approve_lesson_request` */

insert  into `auto_approve_lesson_request`(`teacher_user_id`,`live`,`live_range_of_time`,`video`) values (4,0,NULL,0);

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

/*Data for the table `file_system` */

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

/*Data for the table `forum_access` */

insert  into `forum_access`(`id`,`access_level_id`,`user_id`,`created`,`modified`) values (1,4,4,'2012-07-26 05:43:24','2012-07-26 05:43:24');

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

/*Data for the table `forum_access_levels` */

insert  into `forum_access_levels`(`id`,`title`,`level`,`isAdmin`,`isSuper`) values (1,'Member',1,0,0),(2,'Moderator',4,0,0),(3,'Super Moderator',7,0,1),(4,'Administrator',10,1,1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Forum categories to post topics to';

/*Data for the table `forum_forums` */

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

/*Data for the table `forum_moderators` */

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

/*Data for the table `forum_poll_options` */

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

/*Data for the table `forum_poll_votes` */

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

/*Data for the table `forum_polls` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Posts to topics';

/*Data for the table `forum_posts` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User profiles';

/*Data for the table `forum_profiles` */

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

/*Data for the table `forum_reported` */

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

/*Data for the table `forum_settings` */

insert  into `forum_settings`(`id`,`key`,`value`,`created`,`modified`) values (1,'site_name','CakePHP Forum Plugin',NULL,'2012-07-26 05:42:10'),(2,'site_email','noreply@studentskit.com',NULL,'2012-07-26 05:42:10'),(3,'site_main_url','http://studentskit/',NULL,'2012-07-26 05:42:10'),(4,'topics_per_page','20',NULL,'2012-07-26 05:42:10'),(5,'topics_per_hour','3',NULL,'2012-07-26 05:42:10'),(6,'topic_flood_interval','300',NULL,'2012-07-26 05:42:10'),(7,'topic_pages_till_truncate','10',NULL,'2012-07-26 05:42:10'),(8,'posts_per_page','15',NULL,'2012-07-26 05:42:10'),(9,'posts_per_hour','15',NULL,'2012-07-26 05:42:10'),(10,'posts_till_hot_topic','35',NULL,'2012-07-26 05:42:10'),(11,'post_flood_interval','60',NULL,'2012-07-26 05:42:10'),(12,'days_till_autolock','21',NULL,'2012-07-26 05:42:10'),(13,'whos_online_interval','15',NULL,'2012-07-26 05:42:10'),(14,'security_question','What framework does this plugin run on?',NULL,'2012-07-26 05:42:10'),(15,'security_answer','cakephp',NULL,'2012-07-26 05:42:10'),(16,'enable_quick_reply','1',NULL,'2012-07-26 05:42:10'),(17,'enable_gravatar','1',NULL,'2012-07-26 05:42:10'),(18,'censored_words','',NULL,'2012-07-26 05:42:10'),(19,'default_locale','eng',NULL,'2012-07-26 05:42:10'),(20,'default_timezone','-8',NULL,'2012-07-26 05:42:10'),(21,'title_separator',' - ',NULL,'2012-07-26 05:42:10'),(22,'enable_topic_subscriptions','1',NULL,'2012-07-26 05:42:10'),(23,'enable_forum_subscriptions','1',NULL,'2012-07-26 05:42:10'),(24,'auto_subscribe_self','1',NULL,'2012-07-26 05:42:10');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User topic and forum subscriptions.';

/*Data for the table `forum_subscriptions` */

/*Table structure for table `forum_topics` */

DROP TABLE IF EXISTS `forum_topics`;

CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Discussion topics';

/*Data for the table `forum_topics` */

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `message_enum` text COLLATE utf8_unicode_ci NOT NULL,
  `message_params` text COLLATE utf8_unicode_ci,
  `link` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `notifications` */

insert  into `notifications`(`notification_id`,`user_id`,`message`,`message_enum`,`message_params`,`link`) values (1,4,'Sivan Yamin requesting \"video subject 1\" on ','student.booking.request.sent','{\"message_enum\":\"student.booking.request.sent\",\"params\":{\"teacher_user_id\":\"4\",\"student_user_id\":\"5\",\"datetime\":null,\"name\":\"video subject 1\",\"user_lesson_id\":\"1\"}}','[\"\\/\"]'),(2,5,'Eldad Yamin accepted you\'r request for \"video subject 1\" on ','teacher.booking.request.accepted','{\"message_enum\":\"teacher.booking.request.accepted\",\"params\":{\"user_lesson_id\":\"1\",\"teacher_user_id\":\"4\",\"student_user_id\":\"5\",\"datetime\":null,\"name\":\"video subject 1\"}}','[\"\\/\"]');

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

/*Data for the table `payment_info` */

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

/*Data for the table `student_tests` */

/*Table structure for table `subject_catalog` */

DROP TABLE IF EXISTS `subject_catalog`;

CREATE TABLE `subject_catalog` (
  `subject_catalog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`subject_catalog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `subject_catalog` */

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `subject_categories` */

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
  `is_enable` tinyint(4) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
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
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `subjects` */

insert  into `subjects`(`subject_id`,`user_id`,`type`,`lesson_type`,`subject_category_id`,`catalog_id`,`forum_id`,`name`,`image`,`is_enable`,`description`,`language`,`is_public`,`duration_minutes`,`total_lessons`,`students_amount`,`raters_amount`,`avarage_rating`,`1_on_1_price`,`max_students`,`full_group_student_price`,`full_group_total_price`,`created`,`modified`) values (1,4,1,'video',NULL,NULL,NULL,'video subject 1',0,1,'video subject 1\r\nvideo subject 1\r\nvideo subject 1\r\nvideo subject 1','he',1,60,0,0,0,0,0,1,NULL,NULL,'2012-08-02 14:29:44','2012-08-02 15:43:18'),(2,5,2,'video',NULL,NULL,NULL,'video request',0,1,'video request\r\nvideo request\r\nvideo request\r\nvideo request','he',1,60,0,0,0,0,1,1,NULL,NULL,'2012-08-04 20:54:22','2012-08-04 20:54:22'),(3,4,1,'live',NULL,NULL,NULL,'live lesson',0,1,'live lesson\r\nlive lesson\r\nlive lesson\r\nlive lesson','he',1,60,0,0,0,0,0,1,NULL,NULL,'2012-08-05 04:33:57','2012-08-05 04:33:57');

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
  `subject_type` tinyint(2) NOT NULL DEFAULT '1',
  `lesson_type` enum('live','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'live',
  `is_public` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `is_deleted` tinyint(2) DEFAULT '0',
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `1_on_1_price` float NOT NULL,
  `max_students` int(11) unsigned DEFAULT '1',
  `full_group_student_price` float DEFAULT NULL,
  `full_group_total_price` float DEFAULT NULL,
  `num_of_pending_join_requests` float NOT NULL DEFAULT '0',
  `num_of_students` int(11) NOT NULL DEFAULT '0',
  `num_of_pending_invitations` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`teacher_lesson_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `teacher_lessons` */

insert  into `teacher_lessons`(`teacher_lesson_id`,`subject_id`,`request_subject_id`,`teacher_user_id`,`student_user_id`,`datetime`,`end_datetime`,`subject_category_id`,`forum_id`,`subject_type`,`lesson_type`,`is_public`,`is_deleted`,`name`,`description`,`language`,`duration_minutes`,`1_on_1_price`,`max_students`,`full_group_student_price`,`full_group_total_price`,`num_of_pending_join_requests`,`num_of_students`,`num_of_pending_invitations`) values (1,1,NULL,4,5,'2012-08-05 05:34:44','2012-08-07 05:34:44',NULL,NULL,1,'video',1,0,'video subject 1','video subject 1\r\nvideo subject 1\r\nvideo subject 1\r\nvideo subject 1','he',60,0,1,NULL,NULL,0,1,0);

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
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `threads` */

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
  `subject_type` tinyint(2) NOT NULL DEFAULT '1',
  `lesson_type` enum('live','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'video',
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
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
  PRIMARY KEY (`user_lesson_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `user_lessons` */

insert  into `user_lessons`(`user_lesson_id`,`teacher_lesson_id`,`subject_id`,`request_subject_id`,`teacher_user_id`,`student_user_id`,`datetime`,`end_datetime`,`stage`,`subject_category_id`,`forum_id`,`is_public`,`subject_type`,`lesson_type`,`name`,`description`,`language`,`duration_minutes`,`1_on_1_price`,`max_students`,`full_group_student_price`,`full_group_total_price`,`rating_by_student`,`comment_by_student`,`student_image`,`rating_by_teacher`,`comment_by_teacher`) values (1,1,1,NULL,4,5,'2012-08-05 05:34:44','2012-08-07 05:34:44',7,NULL,NULL,1,1,'video','video subject 1','video subject 1\r\nvideo subject 1\r\nvideo subject 1\r\nvideo subject 1','he',60,0,1,NULL,NULL,NULL,NULL,0,NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activation_code` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(2) DEFAULT '0',
  `first_name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` tinyint(2) NOT NULL DEFAULT '0',
  `dob` date DEFAULT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `zipcode` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_avarage_rating` float unsigned NOT NULL DEFAULT '0',
  `student_raters_amount` int(10) unsigned NOT NULL DEFAULT '0',
  `student_total_lessons` int(10) unsigned NOT NULL DEFAULT '0',
  `teacher_about` text COLLATE utf8_unicode_ci,
  `teaching_address` text COLLATE utf8_unicode_ci,
  `teacher_zipcode` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teacher_total_teaching_minutes` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_students_amount` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_total_lessons` int(11) unsigned NOT NULL DEFAULT '0',
  `teacher_avarage_rating` float unsigned NOT NULL DEFAULT '0',
  `teacher_raters_amount` int(11) unsigned NOT NULL DEFAULT '0',
  `language` text COLLATE utf8_unicode_ci,
  `title` enum('Dr.','Mr.') COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(3) COLLATE utf8_unicode_ci DEFAULT 'eng',
  `timezone` varchar(4) COLLATE utf8_unicode_ci DEFAULT '0',
  `currentLogin` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`user_id`,`email`,`password`,`password_reset`,`activation_code`,`active`,`first_name`,`last_name`,`image`,`dob`,`phone`,`address`,`zipcode`,`student_avarage_rating`,`student_raters_amount`,`student_total_lessons`,`teacher_about`,`teaching_address`,`teacher_zipcode`,`teacher_total_teaching_minutes`,`teacher_students_amount`,`teacher_total_lessons`,`teacher_avarage_rating`,`teacher_raters_amount`,`language`,`title`,`locale`,`timezone`,`currentLogin`,`lastLogin`,`created`) values (4,'eldad87@gmail.com','93bcca70e6e28f23c82fb55c48d88a1bab4bba0d',NULL,NULL,1,'Eldad','Yamin',0,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,0,0,0,0,NULL,NULL,'eng','+2','2012-08-05 05:27:49','2012-08-05 01:22:27','2012-07-19 22:50:32'),(5,'sivaneshokol@gmail.com','93bcca70e6e28f23c82fb55c48d88a1bab4bba0d',NULL,NULL,1,'Sivan','Yamin',0,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,0,0,0,0,NULL,NULL,NULL,NULL,'2012-08-05 01:59:18','2012-08-04 20:50:30',NULL),(6,'test@gmail.com','93bcca70e6e28f23c82fb55c48d88a1bab4bba0d',NULL,'bba6d5957eff80ee1aa2f0e85f75f9979b4823c2',2,'nana','banana',0,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'nanan@gmail.com','93bcca70e6e28f23c82fb55c48d88a1bab4bba0d',NULL,'6d09a6fbb100b57426819a982cd1d65f35f2ab04',0,'eldad','yamin',0,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'nanan2@gmail.com','93bcca70e6e28f23c82fb55c48d88a1bab4bba0d',NULL,'c0631ff1d127d0af5c855e3c698fac887e6de6e4',0,'eldad','yamin',0,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'nanan3@gmail.com','93bcca70e6e28f23c82fb55c48d88a1bab4bba0d',NULL,'e4154b450ab649927690083bd87d4398458fde11',0,'eldad','yamin',0,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
