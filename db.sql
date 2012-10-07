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
  KEY `NewIndex1` (`payment_status`,`stage`,`notification_status`,`teacher_lesson_id`)
) ENGINE=MyISAM AUTO_INCREMENT=236 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `user_lessons` */

insert  into `user_lessons`(`user_lesson_id`,`teacher_lesson_id`,`subject_id`,`request_subject_id`,`teacher_user_id`,`student_user_id`,`datetime`,`end_datetime`,`stage`,`subject_category_id`,`forum_id`,`is_public`,`lesson_type`,`name`,`description`,`language`,`duration_minutes`,`1_on_1_price`,`max_students`,`full_group_student_price`,`full_group_total_price`,`rating_by_student`,`comment_by_student`,`student_image`,`rating_by_teacher`,`comment_by_teacher`,`notification_status`,`payment_status`,`version`) values (235,55,20,NULL,4,13,'2012-11-05 20:08:00','2012-11-05 21:08:00',7,NULL,NULL,1,'live','Live test','my live test\r\nmy live test\r\nmy live test\r\nmy live test\r\n','eng',60,60,2,50,100,NULL,NULL,0,NULL,NULL,0,1,'506f3eb9-7e28-495d-9f42-16b4c0a80014');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
