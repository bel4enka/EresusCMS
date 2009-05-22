-- Eresus 2 PostgreSQL 8.x dump
--
-- $Id$
-- --------------------------------------------------------

CREATE TABLE pages (
  id SERIAL NOT NULL,
  name VARCHAR(32) NOT NULL DEFAULT '',
  owner INTEGER NOT NULL DEFAULT 0,
  title TEXT NOT NULL,
  caption VARCHAR(64) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  hint TEXT NOT NULL,
  keywords TEXT NOT NULL,
  position SMALLINT NOT NULL DEFAULT 0,
  active BOOLEAN NOT NULL DEFAULT FALSE,
  access SMALLINT NOT NULL DEFAULT 5,
  visible BOOLEAN NOT NULL DEFAULT TRUE,
  template VARCHAR(64) NOT NULL DEFAULT '',
  type VARCHAR(32) NOT NULL DEFAULT 'DEFAULT',
  content TEXT,
  options TEXT,
  created TIMESTAMP,
  updated TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE INDEX name ON pages (name);
CREATE INDEX owner ON pages (owner);
CREATE INDEX position ON pages (position);
CREATE INDEX active ON pages (active);
CREATE INDEX access ON pages (access);
CREATE INDEX visible ON pages (visible);
CREATE INDEX created ON pages (created);
CREATE INDEX updated ON pages (updated);

INSERT INTO pages VALUES(1, 'main', 0, 'Главная', 'Главная', '', 'Главная страница', '', 0, TRUE, 5, TRUE, '', 'default', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

CREATE TABLE plugins (
  name VARCHAR(32) NOT NULL DEFAULT '',
  type set('client','admin','content','ondemand') DEFAULT NULL,
  active boolean DEFAULT TRUE,
  position INTEGER DEFAULT 0,
  settings text,
  title VARCHAR(64) DEFAULT '',
  version VARCHAR(16) DEFAULT '',
  description VARCHAR(255) DEFAULT '',
  PRIMARY KEY (name)
);

CREATE INDEX active ON plugins (active);
CREATE INDEX position ON plugins (position);
CREATE INDEX type ON plugins (type);

-- --------------------------------------------------------

CREATE TABLE users (
  id SERIAL,
  login VARCHAR(16) NOT NULL DEFAULT '',
  hash VARCHAR(32) NOT NULL DEFAULT '',
  active boolean DEFAULT TRUE,
  lastVisit datetime DEFAULT NULL,
  lastLoginTime INTEGER DEFAULT NULL,
  loginErrors INTEGER DEFAULT NULL,
  access tinyint(3) DEFAULT NULL,
  name VARCHAR(64) DEFAULT NULL,
  mail VARCHAR(64) DEFAULT NULL,
  profile text,
  PRIMARY KEY  (id),
  KEY login (login),
  KEY active (active)
);

INSERT INTO users VALUES(1, 'root', '74be16979710d4c4e7c6647856088456', 1, '0000-00-00 00:00:00', 0, 0, 1, 'Служба поддержки', 'support@example.org', NULL);
