CREATE TABLE mymenus_links (
  id        INT(5) UNSIGNED     NOT NULL AUTO_INCREMENT,
  pid       INT(5) UNSIGNED     NOT NULL DEFAULT '0',
  mid       INT(5) UNSIGNED     NOT NULL DEFAULT '0',
  title     VARCHAR(255)        NOT NULL DEFAULT '',
  alt_title VARCHAR(255)        NOT NULL DEFAULT '',
  visible   TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  link      VARCHAR(255)                 DEFAULT NULL,
  weight    TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
  target    VARCHAR(10)                  DEFAULT NULL,
  groups    TEXT,
  hooks     TEXT,
  image     VARCHAR(255)                 DEFAULT NULL,
  css       VARCHAR(255)                 DEFAULT NULL,
  PRIMARY KEY (id),
  KEY mid (mid)
)
  ENGINE = MyISAM;

CREATE TABLE mymenus_menus (
  id    INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255)    NOT NULL DEFAULT '',
  css   VARCHAR(255)    NOT NULL DEFAULT '',
  PRIMARY KEY (id)
)
  ENGINE = MyISAM;
