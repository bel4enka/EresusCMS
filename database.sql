-- --------------------------------------------------------
--
-- Структура таблицы `pages`
--

CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `owner` int(10) unsigned NOT NULL default '0',
  `title` text default NULL,
  `caption` varchar(64) default NULL,
  `description` text default NULL,
  `hint` text default NULL,
  `keywords` text default NULL,
  `position` smallint(5) unsigned default NULL,
  `active` tinyint(1) NOT NULL default '0',
  `access` tinyint(1) unsigned default NULL,
  `visible` tinyint(1) unsigned default NULL,
  `template` varchar(64) default NULL,
  `type` varchar(32) NOT NULL default 'default',
  `content` longtext,
  `options` text,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `owner` (`owner`),
  KEY `position` (`position`),
  KEY `active` (`active`),
  KEY `access` (`access`),
  KEY `visible` (`visible`),
  KEY `created` (`created`),
  KEY `updated` (`updated`)
) TYPE=MyISAM COMMENT='Структура и страницы сайта';

--
-- Дамп данных таблицы `pages`
--

INSERT INTO `pages` VALUES (1, 'main', 0, 'Главная', 'Главная', '', 'Главная страница', '', 0, 1, 5, 1, '', 'default', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `plugins`
--

CREATE TABLE `plugins` (
  `name` varchar(32) NOT NULL default '',
  `type` set('client','admin','content','ondemand') default NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `position` int(10) unsigned default NULL,
  `settings` text,
  `title` varchar(64) default NULL,
  `version` varchar(16) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`name`),
  KEY `active` (`active`),
  KEY `position` (`position`),
  KEY `type` (`type`)
) TYPE=MyISAM COMMENT='Модули расширения';

--
-- Дамп данных таблицы `plugins`
--


-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(16) NOT NULL default '',
  `hash` char(32) NOT NULL default '',
  `active` tinyint(1) unsigned NOT NULL default '1',
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
) TYPE=MyISAM PACK_KEYS=0 COMMENT='Пользователи';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` VALUES (1, 'root', 'd41d8cd98f00b204e9800998ecf8427e', 1, '0000-00-00 00:00:00', 0, 0, 1, 'Служба поддержки', 'support@procreat.ru', NULL);