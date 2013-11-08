--
-- Eresus MySQL 5.x dump
-- --------------------------------------------------------

SET NAMES "UTF8";

CREATE TABLE `sections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `name` varchar(255) NOT NULL default '',
  `title` text NOT NULL,
  `caption` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `hint` text NOT NULL,
  `keywords` text NOT NULL,
  `position` smallint(5) unsigned NOT NULL default 0,
  `active` tinyint(1) NOT NULL default '0',
  `access` tinyint(1) unsigned NOT NULL default 5,
  `visible` tinyint(1) unsigned NOT NULL default 1,
  `template` varchar(255) NOT NULL default '',
  `type` varchar(64) NOT NULL default 'default',
  `content` longtext NOT NULL,
  `options` text NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `name_idx` (`name`),
  KEY `client_idx` (`parent_id`, `active`, `visible`, `position`)
) DEFAULT CHARSET=utf8;

INSERT INTO `sections` VALUES(1,  0, 'main', 'Главная', 'Главная', '', 'Главная страница', '', 0, 1, 5, 1, '', 'default', '<h1>Добро пожаловать!</h1>', 'a:0:{}', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

CREATE TABLE `plugins` (
  `name` varchar(255) NOT NULL,
  `active` bool  NOT NULL default 1,
  `settings` text,
  PRIMARY KEY  (`name`),
  KEY `active` (`active`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `hash` varchar(32) NOT NULL default '',
  `active` tinyint(1) unsigned NOT NULL default 1,
  `access` tinyint(3) unsigned default NULL,
  `name` varchar(255) default NULL,
  `mail` varchar(255) default NULL,
  `profile` text,
  PRIMARY KEY  (`id`),
  KEY `login` (`login`),
  KEY `active` (`active`)
) DEFAULT CHARSET=utf8;

INSERT INTO `accounts` VALUES(1, 'root', '74be16979710d4c4e7c6647856088456', 1, 1, 'Служба поддержки', 'support@example.org', NULL);
INSERT INTO `accounts` VALUES(2, 'admin', '74be16979710d4c4e7c6647856088456', 1, 2, 'Администратор', 'admin@example.org', NULL);
INSERT INTO `accounts` VALUES(3, 'editor', '74be16979710d4c4e7c6647856088456', 1, 3, 'Редактор', 'editor@example.org', NULL);
INSERT INTO `accounts` VALUES(4, 'user', '74be16979710d4c4e7c6647856088456', 1, 4, 'Пользователь', 'user@example.org', NULL);
