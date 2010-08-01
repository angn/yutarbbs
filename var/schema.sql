-- MySQL dump 10.11
--
-- Host: localhost    Database: holies
-- ------------------------------------------------------
-- Server version	5.0.45-Debian_1ubuntu3.1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `fid` tinyint(3) unsigned NOT NULL,
  `cid` tinyint(3) unsigned NOT NULL auto_increment,
  `label` char(31) NOT NULL,
  PRIMARY KEY  (`fid`,`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `emoticons`
--

DROP TABLE IF EXISTS `emoticons`;
CREATE TABLE `emoticons` (
  `name` char(15) NOT NULL,
  `filename` char(63) NOT NULL,
  PRIMARY KEY  (`name`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `mid` smallint(5) unsigned NOT NULL auto_increment,
  `tid` int(10) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`mid`),
  KEY `tid` (`tid`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=18509 DEFAULT CHARSET=utf8;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
  `tid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `registry`
--

DROP TABLE IF EXISTS `registry`;
CREATE TABLE `registry` (
  `id` enum('') NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `cid` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
CREATE TABLE `threads` (
  `tid` int(10) unsigned NOT NULL auto_increment,
  `fid` tinyint(3) unsigned NOT NULL,
  `cid` tinyint(3) unsigned NOT NULL,
  `uid` smallint(5) unsigned NOT NULL,
  `subject` tinytext NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `hits` smallint(5) unsigned NOT NULL,
  `attachment` varchar(255) NOT NULL,
  PRIMARY KEY  (`tid`),
  KEY `created_at` (`created_at`),
  KEY `fid` (`fid`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=4227 DEFAULT CHARSET=utf8;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` smallint(5) unsigned NOT NULL auto_increment,
  `userid` char(31) NOT NULL,
  `passwd` char(40) character set latin1 collate latin1_bin NOT NULL,
  `name` char(4) NOT NULL,
  `year` year(2) NOT NULL,
  `phone` char(31) character set latin1 collate latin1_bin NOT NULL,
  `email` char(63) character set latin1 collate latin1_bin NOT NULL,
  `msn` char(63) character set latin1 collate latin1_bin NOT NULL,
  `website` char(31) character set latin1 collate latin1_bin NOT NULL,
  `remark` char(255) NOT NULL,
  `rss` char(63) character set latin1 collate latin1_bin NOT NULL,
  `updated_on` date NOT NULL,
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-04-10  6:38:17
