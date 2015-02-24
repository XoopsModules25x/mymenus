CREATE TABLE mymenus_config (
  `id`        SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `skin_id`   SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `name`      VARCHAR(25)          NOT NULL DEFAULT '',
  `title`     VARCHAR(255)         NOT NULL DEFAULT '',
  `value`     TEXT,
  `desc`      VARCHAR(255)         NOT NULL DEFAULT '',
  `formtype`  VARCHAR(15)          NOT NULL DEFAULT '',
  `valuetype` VARCHAR(10)          NOT NULL DEFAULT '',
  `corder`    SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order` (`order`)
)
  ENGINE = MyISAM;

CREATE TABLE mymenus_configoption (
  `id`        MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `config_id` SMALLINT(5) UNSIGNED  NOT NULL DEFAULT '0',
  `name`      VARCHAR(255)          NOT NULL DEFAULT '',
  `value`     VARCHAR(255)          NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `config_id` (`config_id`)
)
  ENGINE = MyISAM;
