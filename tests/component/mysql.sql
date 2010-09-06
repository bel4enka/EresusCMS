DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `plugins`;
DROP TABLE IF EXISTS `test`;
DROP TABLE IF EXISTS `test_prefixed`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `owner` int(10) unsigned NOT NULL default 0,
  `title` text NOT NULL,
  `caption` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `hint` text NOT NULL,
  `keywords` text NOT NULL,
  `position` smallint(5) unsigned NOT NULL default 0,
  `active` tinyint(1) NOT NULL default '0',
  `access` tinyint(1) unsigned NOT NULL default 5,
  `visible` tinyint(1) unsigned NOT NULL default 1,
  `template` varchar(64) NOT NULL default '',
  `type` varchar(32) NOT NULL default 'default',
  `content` longtext NOT NULL,
  `options` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `owner` (`owner`),
  KEY `position` (`position`),
  KEY `active` (`active`),
  KEY `access` (`access`),
  KEY `visible` (`visible`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `pages` VALUES(1, 'main', 0, 'Главная', 'Главная', '', 'Главная страница', '', 0, 1, 5, 1, 'default', 'html', 'test', 'a:0:{}', '0000-00-00 00:00:00', '2010-02-06 21:55:31');
INSERT INTO `pages` VALUES(2, 'second', 0, 'Вторая страница', 'Вторая страница', '', '', '', 1, 1, 5, 1, 'default', 'default', '', 'a:2:{s:1:"a";s:1:"b";s:1:"c";s:2:"d5";}', '2010-01-23 11:26:38', '2010-01-23 12:20:28');

CREATE TABLE `plugins` (
  `name` varchar(32) NOT NULL,
  `active` bool  NOT NULL default 1,
  `content` bool  NOT NULL default 0,
  `settings` text,
  `title` varchar(64) default '',
  `version` varchar(16) default '',
  `description` varchar(255) default '',
  PRIMARY KEY  (`name`),
  KEY `active` (`active`),
  KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- INSERT INTO `plugins` VALUES('html', 'client,content,ondemand', 1, 0, 'a:0:{}', 'HTML', '3.00', 'HTML страница');
INSERT INTO `plugins` VALUES('html', 1, 0, 'a:0:{}', 'HTML', '3.00', 'HTML страница');

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(16) NOT NULL default '',
  `hash` varchar(32) NOT NULL default '',
  `active` tinyint(1) unsigned NOT NULL default 1,
  `lastVisit` datetime default NULL,
  `lastLoginTime` int(10) unsigned default NULL,
  `loginErrors` int(10) unsigned default NULL,
  `access` tinyint(3) unsigned default NULL,
  `name` varchar(64) default NULL,
  `mail` varchar(64) default NULL,
  `profile` text,
  PRIMARY KEY  (`id`),
  KEY `login` (`login`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `users` VALUES(1, 'root', '74be16979710d4c4e7c6647856088456', 1, '2010-02-19 12:35:10', 1266572110, 0, 1, 'Служба поддержки', 'support@example.org', NULL);
INSERT INTO `users` VALUES(2, 'admin', '74be16979710d4c4e7c6647856088456', 1, '2010-02-20 12:35:10', 1266572110, 0, 2, 'Администратор', 'admin@example.org', NULL);
INSERT INTO `users` VALUES(3, 'editor', '74be16979710d4c4e7c6647856088456', 1, '2010-02-21 12:35:10', 1266572110, 0, 3, 'Редактор', 'editor@example.org', NULL);

CREATE TABLE `test_prefixed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `test_prefixed` VALUES(1, 'main');
INSERT INTO `test_prefixed` VALUES(2, 'second');
