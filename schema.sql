CREATE TABLE `messages` (
  `mid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`mid`),
  KEY `tid` (`tid`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 MAX_ROWS=1000000;

CREATE TABLE `threads` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `uid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `subject` tinytext NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attachment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`tid`),
  KEY `created_at` (`created_at`),
  KEY `fid` (`fid`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 MAX_ROWS=100000;

CREATE TABLE `users` (
  `uid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `userid` char(31) NOT NULL DEFAULT '',
  `passwd` char(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `name` char(12) NOT NULL DEFAULT '',
  `year` year(2) NOT NULL DEFAULT '00',
  `phone` char(31) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` char(63) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `remark` char(255) NOT NULL DEFAULT '',
  `updated_on` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 MAX_ROWS=200;
