# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.5.42)
# Database: evzdelavani2
# Generation Time: 2016-06-24 08:40:45 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `address`;

CREATE TABLE `address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(128) NOT NULL DEFAULT '',
  `street` varchar(128) NOT NULL DEFAULT '',
  `registry_number` int(11) unsigned NOT NULL,
  `house_number` int(11) unsigned NOT NULL,
  `zip` int(8) NOT NULL,
  `note` text NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `company_id` int(11) unsigned NOT NULL,
  `type` char(12) NOT NULL DEFAULT '',
  `country_key` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibfk_address_2` (`company_id`),
  KEY `ibfk_address_3` (`country_key`),
  CONSTRAINT `ibfk_address_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_address_3` FOREIGN KEY (`country_key`) REFERENCES `country` (`key`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table advice
# ------------------------------------------------------------

DROP TABLE IF EXISTS `advice`;

CREATE TABLE `advice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `content` text,
  `header` varchar(128) DEFAULT NULL,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `valid` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `position` int(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `ibfk_advice_1` (`company_id`),
  KEY `ibfk_advice_2` (`user_id`),
  CONSTRAINT `ibfk_advice_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_advice_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `personality_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `perex` text NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(128) NOT NULL DEFAULT '',
  `published_at` date NOT NULL,
  `seokey` varchar(128) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibu_article_1` (`seokey`),
  KEY `ibfk_article_1` (`personality_id`),
  KEY `ibfk_article_2` (`user_id`),
  CONSTRAINT `ibfk_article_1` FOREIGN KEY (`personality_id`) REFERENCES `personality` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ibfk_article_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `seokey` varchar(128) NOT NULL DEFAULT '',
  `position` tinyint(1) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table company
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company`;

CREATE TABLE `company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `seokey` varchar(128) NOT NULL DEFAULT '',
  `ic` int(128) NOT NULL,
  `dic` varchar(128) DEFAULT NULL,
  `description` text NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `user_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `dealer_id` int(11) unsigned DEFAULT NULL,
  `logo` varchar(255) NOT NULL DEFAULT '',
  `partner` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web` varchar(255) NOT NULL DEFAULT '',
  `import_url` varchar(255) NOT NULL DEFAULT '',
  `import_at` datetime DEFAULT NULL,
  `top` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `potencial` int(2) NOT NULL DEFAULT '99',
  `type` int(2) NOT NULL DEFAULT '5',
  `status` int(2) NOT NULL DEFAULT '1',
  `bank_account` varchar(255) NOT NULL DEFAULT '',
  `notice` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_comapny_1` (`user_id`),
  KEY `ibfk_company_2` (`dealer_id`),
  CONSTRAINT `ibfk_comapny_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ibfk_company_2` FOREIGN KEY (`dealer_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table company_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_category`;

CREATE TABLE `company_category` (
  `copmany_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`copmany_id`,`category_id`),
  KEY `ibfk_company_category_2` (`category_id`),
  CONSTRAINT `ibfk_company_category_1` FOREIGN KEY (`copmany_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_company_category_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table company_focus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_focus`;

CREATE TABLE `company_focus` (
  `company_id` int(11) unsigned NOT NULL,
  `focus_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`company_id`,`focus_id`),
  KEY `ibfk_company_focus_2` (`focus_id`),
  CONSTRAINT `ibfk_company_focus_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_company_focus_2` FOREIGN KEY (`focus_id`) REFERENCES `focus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table company_image
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_image`;

CREATE TABLE `company_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `company_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_company_image_1` (`company_id`),
  CONSTRAINT `ibfk_company_image_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table company_video
# ------------------------------------------------------------

DROP TABLE IF EXISTS `company_video`;

CREATE TABLE `company_video` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `company_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_company_video_1` (`company_id`),
  CONSTRAINT `ibfk_company_video_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table contact
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contact`;

CREATE TABLE `contact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ex_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `type` char(128) NOT NULL DEFAULT '',
  `company_id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `phone` varchar(22) NOT NULL DEFAULT '',
  `function` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibfk_contact_1` (`company_id`),
  CONSTRAINT `ibfk_contact_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;



# Dump of table country
# ------------------------------------------------------------

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `key` char(2) NOT NULL DEFAULT '',
  `country` varchar(128) NOT NULL DEFAULT '',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;

INSERT INTO `country` (`key`, `country`, `default`)
VALUES
	('AD','Andorra',0),
	('AE','Spojené arabské emiráty',0),
	('AG','Antigua a Barbuda',0),
	('AI','Anguilla',0),
	('AL','Albánie',0),
	('AM','Arménie',0),
	('AO','Angola',0),
	('AQ','Antarktida',0),
	('AR','Argentina',0),
	('AS','Americká Samoa',0),
	('AT','Rakousko',0),
	('AU','Austrálie',0),
	('AW','Aruba',0),
	('AX','Ålandy',0),
	('AZ','Ázerbájdžán',0),
	('BA','Bosna a Hercegovina',0),
	('BB','Barbados',0),
	('BD','Bangladéš',0),
	('BE','Belgie',0),
	('BF','Burkina Faso',0),
	('BG','Bulharsko',0),
	('BH','Bahrajn',0),
	('BI','Burundi',0),
	('BJ','Benin',0),
	('BL','Svatý Bartoloměj',0),
	('BM','Bermudy',0),
	('BN','Brunej',0),
	('BO','Bolívie',0),
	('BQ','Bonaire, Svatý Eustach a',0),
	('BR','Brazílie',0),
	('BS','Bahamy',0),
	('BT','Bhútán',0),
	('BV','Bouvetův ostrov',0),
	('BW','Botswana',0),
	('BY','Bělorusko',0),
	('BZ','Belize',0),
	('CA','Kanada',0),
	('CC','Kokosové ostrovy',0),
	('CD','Demokratická republika K',0),
	('CF','Středoafrická republika',0),
	('CG','Kongo',0),
	('CH','Švýcarsko',0),
	('CI','Pobřeží slonoviny',0),
	('CK','Cookovy ostrovy',0),
	('CL','Chile',0),
	('CM','Kamerun',0),
	('CN','Čína',0),
	('CO','Kolumbie',0),
	('CR','Kostarika',0),
	('CU','Kuba',0),
	('CV','Kapverdy',0),
	('CW','Curaçao',0),
	('CX','Vánoční ostrov',0),
	('CY','Kypr',0),
	('CZ','Česká republika',1),
	('DE','Německo',0),
	('DJ','Džibutsko',0),
	('DK','Dánsko',0),
	('DM','Dominika',0),
	('DO','Dominikánská republika',0),
	('DZ','Alžírsko',0),
	('EC','Ekvádor',0),
	('EE','Estonsko',0),
	('EG','Egypt',0),
	('EH','Západní Sahara',0),
	('ER','Eritrea',0),
	('ES','Španělsko',0),
	('ET','Etiopie',0),
	('FI','Finsko',0),
	('FJ','Fidži',0),
	('FK','Falklandy (Malvíny)',0),
	('FM','Mikronésie',0),
	('FO','Faerské ostrovy',0),
	('FR','Francie',0),
	('GA','Gabon',0),
	('GB','Spojené království',0),
	('GD','Grenada',0),
	('GE','Gruzie',0),
	('GF','Francouzská Guyana',0),
	('GG','Guernsey',0),
	('GH','Ghana',0),
	('GI','Gibraltar',0),
	('GL','Grónsko',0),
	('GM','Gambie',0),
	('GN','Guinea',0),
	('GP','Guadeloupe',0),
	('GQ','Rovníková Guinea',0),
	('GR','Řecko',0),
	('GS','Jižní Georgie a Jižní Sa',0),
	('GT','Guatemala',0),
	('GU','Guam',0),
	('GW','Guinea-Bissau',0),
	('GY','Guyana',0),
	('HK','Hongkong',0),
	('HM','Heardův ostrov a McDonal',0),
	('HN','Honduras',0),
	('HR','Chorvatsko',0),
	('HT','Haiti',0),
	('HU','Maďarsko',0),
	('ID','Indonésie',0),
	('IE','Irsko',0),
	('IL','Izrael',0),
	('IM','Ostrov Man',0),
	('IN','Indie',0),
	('IO','Britské indickooceánské ',0),
	('IQ','Irák',0),
	('IR','Írán',0),
	('IS','Island',0),
	('IT','Itálie',0),
	('JE','Jersey',0),
	('JM','Jamajka',0),
	('JO','Jordánsko',0),
	('JP','Japonsko',0),
	('KE','Keňa',0),
	('KG','Kyrgyzstán',0),
	('KH','Kambodža',0),
	('KI','Kiribati',0),
	('KM','Komory',0),
	('KN','Svatý Kryštof a Nevis',0),
	('KP','Severní Korea',0),
	('KR','Jižní Korea',0),
	('KW','Kuvajt',0),
	('KY','Kajmanské ostrovy',0),
	('KZ','Kazachstán',0),
	('LA','Laos',0),
	('LB','Libanon',0),
	('LC','Svatá Lucie',0),
	('LI','Lichtenštejnsko',0),
	('LK','Šrí Lanka',0),
	('LR','Libérie',0),
	('LS','Lesotho',0),
	('LT','Litva',0),
	('LU','Lucembursko',0),
	('LV','Lotyšsko',0),
	('LY','Libye',0),
	('MA','Maroko',0),
	('MC','Monako',0),
	('MD','Moldavsko',0),
	('ME','Černá Hora',0),
	('MF','Svatý Martin (francouzsk',0),
	('MG','Madagaskar',0),
	('MH','Marshallovy ostrovy',0),
	('MK','Makedonie',0),
	('ML','Mali',0),
	('MM','Myanmar',0),
	('MN','Mongolsko',0),
	('MO','Macao',0),
	('MP','Severní Mariany',0),
	('MQ','Martinik',0),
	('MR','Mauritánie',0),
	('MS','Montserrat',0),
	('MT','Malta',0),
	('MU','Mauricius',0),
	('MV','Maledivy',0),
	('MW','Malawi',0),
	('MX','Mexiko',0),
	('MY','Malajsie',0),
	('MZ','Mosambik',0),
	('NA','Namibie',0),
	('NC','Nová Kaledonie',0),
	('NE','Niger',0),
	('NF','Norfolk',0),
	('NG','Nigérie',0),
	('NI','Nikaragua',0),
	('NL','Nizozemsko',0),
	('NO','Norsko',0),
	('NP','Nepál',0),
	('NR','Nauru',0),
	('NU','Niue',0),
	('NZ','Nový Zéland',0),
	('OM','Omán',0),
	('PA','Panama',0),
	('PE','Peru',0),
	('PF','Francouzská Polynésie',0),
	('PG','Papua-Nová Guinea',0),
	('PH','Filipíny',0),
	('PK','Pákistán',0),
	('PL','Polsko',0),
	('PM','Saint-Pierre a Miquelon',0),
	('PN','Pitcairnovy ostrovy',0),
	('PR','Portoriko',0),
	('PS','Palestinská autonomie',0),
	('PT','Portugalsko',0),
	('PW','Palau',0),
	('PY','Paraguay',0),
	('QA','Katar',0),
	('RE','Réunion',0),
	('RO','Rumunsko',0),
	('RS','Srbsko',0),
	('RU','Rusko',0),
	('RW','Rwanda',0),
	('SA','Saúdská Arábie',0),
	('SB','Šalamounovy ostrovy',0),
	('SC','Seychely',0),
	('SD','Súdán',0),
	('SE','Švédsko',0),
	('SG','Singapur',0),
	('SH','Svatá Helena, Ascension ',0),
	('SI','Slovinsko',0),
	('SJ','Špicberky a Jan Mayen',0),
	('SK','Slovensko',0),
	('SL','Sierra Leone',0),
	('SM','San Marino',0),
	('SN','Senegal',0),
	('SO','Somálsko',0),
	('SR','Surinam',0),
	('SS','Jižní Súdán',0),
	('ST','Svatý Tomáš a Princův os',0),
	('SV','Salvador',0),
	('SX','Svatý Martin (nizozemská',0),
	('SY','Sýrie',0),
	('SZ','Svazijsko',0),
	('TC','Turks a Caicos',0),
	('TD','Čad',0),
	('TF','Francouzská jižní a anta',0),
	('TG','Togo',0),
	('TH','Thajsko',0),
	('TJ','Tádžikistán',0),
	('TK','Tokelau',0),
	('TL','Východní Timor',0),
	('TM','Turkmenistán',0),
	('TN','Tunisko',0),
	('TO','Tonga',0),
	('TR','Turecko',0),
	('TT','Trinidad a Tobago',0),
	('TV','Tuvalu',0),
	('TW','Tchaj-wan',0),
	('TZ','Tanzanie',0),
	('UA','Ukrajina',0),
	('UG','Uganda',0),
	('UM','Menší odlehlé ostrovy US',0),
	('US','Spojené státy americké',0),
	('UY','Uruguay',0),
	('UZ','Uzbekistán',0),
	('VA','Vatikán',0),
	('VC','Svatý Vincenc a Grenadin',0),
	('VE','Venezuela',0),
	('VG','Britské Panenské ostrovy',0),
	('VI','Americké Panenské ostrov',0),
	('VN','Vietnam',0),
	('VU','Vanuatu',0),
	('WF','Wallis a Futuna',0),
	('WS','Samoa',0),
	('YE','Jemen',0),
	('YT','Mayotte',0),
	('ZA','Jihoafrická republika',0),
	('ZM','Zambie',0),
	('ZW','Zimbabwe',0);

/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table course
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course`;

CREATE TABLE `course` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` char(128) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `retraining` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `seokey` char(128) NOT NULL DEFAULT '',
  `company_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibu_course_1` (`seokey`),
  UNIQUE KEY `ibu_course_2` (`external_id`,`company_id`),
  KEY `ibfk_course_1` (`company_id`),
  KEY `ibfk_course_2` (`language_id`),
  CONSTRAINT `ibfk_course_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_course_2` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table course_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course_comment`;

CREATE TABLE `course_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `rating` tinyint(1) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_course_comment` (`course_id`),
  CONSTRAINT `ibfk_course_comment` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table course_focus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course_focus`;

CREATE TABLE `course_focus` (
  `course_id` int(11) unsigned NOT NULL,
  `focus_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`course_id`,`focus_id`),
  KEY `ibfk_course_focus_2` (`focus_id`),
  CONSTRAINT `ibfk_course_focus_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_course_focus_2` FOREIGN KEY (`focus_id`) REFERENCES `focus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table course_image
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course_image`;

CREATE TABLE `course_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `img` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table course_keyword
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course_keyword`;

CREATE TABLE `course_keyword` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(128) NOT NULL DEFAULT '',
  `course_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_course_keyword_1` (`course_id`),
  CONSTRAINT `ibfk_course_keyword_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table course_level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course_level`;

CREATE TABLE `course_level` (
  `course_id` int(11) unsigned NOT NULL,
  `level_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`course_id`,`level_id`),
  KEY `ibfk_course_level_2` (`level_id`),
  CONSTRAINT `ibfk_course_level_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_course_level_2` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table course_video
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course_video`;

CREATE TABLE `course_video` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `video` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table currency
# ------------------------------------------------------------

DROP TABLE IF EXISTS `currency`;

CREATE TABLE `currency` (
  `currency` char(3) NOT NULL DEFAULT '',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;

INSERT INTO `currency` (`currency`, `default`)
VALUES
	('CZK',1),
	('EUR',0);

/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `file`;

CREATE TABLE `file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(24) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_file_1` (`user_id`),
  CONSTRAINT `ibfk_file_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table flag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flag`;

CREATE TABLE `flag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `flag` WRITE;
/*!40000 ALTER TABLE `flag` DISABLE KEYS */;

INSERT INTO `flag` (`id`, `name`)
VALUES
	(1,'online'),
	(2,'custom');

/*!40000 ALTER TABLE `flag` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table focus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `focus`;

CREATE TABLE `focus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `seokey` varchar(128) NOT NULL DEFAULT '',
  `position` tinyint(1) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `category_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_focus_1` (`category_id`),
  CONSTRAINT `ibfk_focus_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table import
# ------------------------------------------------------------

DROP TABLE IF EXISTS `import`;

CREATE TABLE `import` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) unsigned NOT NULL,
  `import_date` datetime NOT NULL,
  `log` text NOT NULL,
  `exec_note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibfk_import_1` (`company_id`),
  CONSTRAINT `ibfk_import_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table language
# ------------------------------------------------------------

DROP TABLE IF EXISTS `language`;

CREATE TABLE `language` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` char(3) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;

INSERT INTO `language` (`id`, `code`, `name`, `default`)
VALUES
	(1,'CZ','Čeština',1),
	(2,'EN','Angličtina',0);

/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table level
# ------------------------------------------------------------

DROP TABLE IF EXISTS `level`;

CREATE TABLE `level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `level` WRITE;
/*!40000 ALTER TABLE `level` DISABLE KEYS */;

INSERT INTO `level` (`id`, `name`)
VALUES
	(1,'začátečníci'),
	(2,'středně pokročilí'),
	(3,'odborníci');

/*!40000 ALTER TABLE `level` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table note
# ------------------------------------------------------------

DROP TABLE IF EXISTS `note`;

CREATE TABLE `note` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `contact_at` datetime DEFAULT NULL,
  `next_contact_at` datetime DEFAULT NULL,
  `note` text NOT NULL,
  `contact_note` text NOT NULL,
  `contact_id` int(11) unsigned DEFAULT NULL,
  `done` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibfk_note_1` (`company_id`),
  KEY `ibfk_note_2` (`contact_id`),
  KEY `ibfk_note_3` (`user_id`),
  CONSTRAINT `ibfk_note_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  CONSTRAINT `ibfk_note_2` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `ibfk_note_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `billing_info` text NOT NULL,
  `member_count` int(16) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime NOT NULL,
  `course_name` varchar(255) NOT NULL DEFAULT '',
  `company_id` int(11) unsigned NOT NULL,
  `sent_to` varchar(255) NOT NULL DEFAULT '',
  `term_from` date DEFAULT NULL,
  `term_to` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_order_1` (`term_id`),
  KEY `ibfk_order_2` (`company_id`),
  CONSTRAINT `ibfk_order_1` FOREIGN KEY (`term_id`) REFERENCES `term` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ibfk_order_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table partner
# ------------------------------------------------------------

DROP TABLE IF EXISTS `partner`;

CREATE TABLE `partner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `position` int(8) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table permission
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `resource` varchar(128) DEFAULT NULL,
  `privilege` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_permission_1` (`role_id`),
  CONSTRAINT `ibfk_permission_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;

INSERT INTO `permission` (`id`, `role_id`, `resource`, `privilege`)
VALUES
	(1,4,'Admin',NULL),
	(2,2,'Zone',NULL);

/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table personality
# ------------------------------------------------------------

DROP TABLE IF EXISTS `personality`;

CREATE TABLE `personality` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(128) NOT NULL DEFAULT '',
  `surname` varchar(128) NOT NULL DEFAULT '',
  `seokey` varchar(128) NOT NULL DEFAULT '',
  `degrees_before` varchar(128) NOT NULL DEFAULT '',
  `degrees_after` varchar(128) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `image` varchar(128) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibu_personality` (`seokey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table question
# ------------------------------------------------------------

DROP TABLE IF EXISTS `question`;

CREATE TABLE `question` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `question` text NOT NULL,
  `created_at` datetime NOT NULL,
  `course_name` varchar(255) NOT NULL DEFAULT '',
  `sent_to` varchar(255) NOT NULL DEFAULT '',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(24) NOT NULL DEFAULT '',
  `role_id` int(11) unsigned DEFAULT NULL,
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ibfk_role_1` (`role_id`),
  CONSTRAINT `ibfk_role_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;

INSERT INTO `role` (`id`, `role`, `role_id`, `default`)
VALUES
	(1,'guest',NULL,1),
	(2,'member',1,1),
	(3,'dealer',2,1),
	(4,'admin',2,1);

/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table static_page
# ------------------------------------------------------------

DROP TABLE IF EXISTS `static_page`;

CREATE TABLE `static_page` (
  `id` char(11) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ibfk_static_page_1` (`user_id`),
  CONSTRAINT `ibfk_static_page_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `static_page` WRITE;
/*!40000 ALTER TABLE `static_page` DISABLE KEYS */;

INSERT INTO `static_page` (`id`, `title`, `content`, `user_id`, `updated_at`)
VALUES
	('kodex','Názorový kodex','<p>Přid&aacute;n&iacute;m n&aacute;zoru - př&iacute;spěvku se zavazujete dodržovat tento N&aacute;zorov&yacute; kodex:</p>\n<p>&nbsp;</p>\n<ul>\n<li>Př&iacute;spěvek nesm&iacute; obsahovat prokazatelně protipr&aacute;vn&iacute; obsah (nav&aacute;děn&iacute; k tř&iacute;dn&iacute;, n&aacute;božensk&eacute; nebo n&aacute;rodnostn&iacute; nesn&aacute;&scaron;enlivosti apod.).</li>\n<li>Př&iacute;spěvek neobsahuje spam (nevyž&aacute;dan&eacute; reklamn&iacute; sdělen&iacute; a odkazy).</li>\n<li>Př&iacute;spěvek neobsahuje vulgarity.</li>\n</ul>\n<p>&nbsp;</p>\n<p>V př&iacute;padě pochybnost&iacute; o vhodnosti př&iacute;spěvku rozhoduje n&aacute;zor redakce evzdelavani.cz, př&iacute;spěvky neodpov&iacute;daj&iacute;c&iacute; kodexu mohou b&yacute;t vymaz&aacute;ny.</p>\n<p><br /><br /></p>\n<h2>Podm&iacute;nky už&iacute;v&aacute;n&iacute;</h2>\n<p>&nbsp;</p>\n<p>Copyright (c) 2010, evzdelavani.cz, s.r.o. V&scaron;echna pr&aacute;va vyhrazena.</p>\n<p>&nbsp;</p>\n<p>Nakl&aacute;d&aacute;n&iacute; s obsahem serveru evzdelavani.cz, kter&yacute; je chr&aacute;něn autorsk&yacute;m pr&aacute;vem, se ř&iacute;d&iacute; z&aacute;konem č. 121/2000 Sb., autorsk&yacute; z&aacute;kon, v &uacute;činn&eacute;m zněn&iacute;. Chcete-li kter&yacute;koliv zveřejněn&yacute; materi&aacute;l převz&iacute;t dle &sect;34 (1) c), př&iacute;padně jej d&aacute;le &scaron;&iacute;řit jin&yacute;m způsobem, vyž&aacute;dejte si pros&iacute;m předem souhlas provozovatele; bez jeho souhlasu nen&iacute; převzet&iacute; dovoleno.</p>\n<p>&nbsp;</p>\n<p>Služby evzdelavani.cz jsou uživatelům poskytov&aacute;ny bezplatně a provozovatel jeho provoz financuje t&eacute;měř v&yacute;lučně z prodeje inzerce a reklamy. Blokov&aacute;n&iacute; stahov&aacute;n&iacute; či zobrazovan&iacute; reklamy proto považuje přinejmen&scaron;&iacute;m za projev neslu&scaron;n&eacute;ho chov&aacute;n&iacute;.</p>\n<p>&nbsp;</p>\n<p>Společnost evzdelavani.cz, s.r.o., zaručuje v&scaron;em uživatelům serveru ochranu jejich osobn&iacute;ch &uacute;dajů. Nesb&iacute;r&aacute; ž&aacute;dn&eacute; osobn&iacute; &uacute;daje, kter&eacute; j&iacute; uživatel&eacute; sami dobrovolně neposkytnou (zejm&eacute;na při vkl&aacute;d&aacute;n&iacute; n&aacute;zorů k čl&aacute;nkům), a neshromažďuje ž&aacute;dn&eacute; citliv&eacute; &uacute;daje. Dobrovoln&yacute;m poskytnut&iacute;m osobn&iacute;ch &uacute;dajů d&aacute;vaj&iacute; uivatel&eacute; souhlas k tomu, aby společnost evzdelavani.cz, s.r.o., s poskytnut&yacute;mi &uacute;daji nakl&aacute;dala v rozsahu nezbytn&eacute;m k ř&aacute;dn&eacute;mu provozov&aacute;n&iacute; internetov&yacute;ch str&aacute;nek, včetně zpř&iacute;stupňov&aacute;n&iacute; jejich obsahu veřejnosti nebo jeho poskytov&aacute;n&iacute; jin&yacute;m osob&aacute;m a spr&aacute;vy a archivace čl&aacute;nků a n&aacute;zorů k nim, a to na celou dobu takov&eacute; činnosti.</p>\n<p>&nbsp;</p>\n<div>S&nbsp;ohledem na z&aacute;kon č. 480/2004 Sb. o někter&yacute;ch služb&aacute;ch informačn&iacute;ch společnost&iacute; registrovan&yacute; uživatel souhlas&iacute; se zas&iacute;l&aacute;n&iacute;m aktu&aacute;ln&iacute;ch obchodn&iacute;ch informac&iacute; společnosti <strong>evzděl&aacute;v&aacute;n&iacute;.cz, s.r.o.</strong></div>\n<div>Obsahem jsou aktu&aacute;ln&iacute; informace z oboru vzděl&aacute;v&aacute;n&iacute; formou newsletteru nebo mailingu.</div>\n<div>Zas&iacute;l&aacute;n&iacute; informačn&iacute;ch e-mailů&nbsp;je možn&eacute;&nbsp;kdykoliv ukončit zasl&aacute;n&iacute;m e-mailu.</div>',1,'2016-06-18 18:16:01'),
	('kontakty','Kontakty','<h2><strong class=\"left\">evzdelavani.cz, s.r.o.</strong></h2>\n<p><strong class=\"left\">S&iacute;dlo společnosti:&nbsp;</strong></p>\n<p>Mezi &scaron;kolami 2477/25, Stodůlky, 158 00 Praha 5</p>\n<p>Registrace: C 251319 veden&aacute; u Městsk&eacute;ho soudu v Praze</p>\n<p><strong class=\"left\">IČO:&nbsp;<strong>04641591</strong></strong></p>\n<h3>&nbsp;</h3>\n<h4>Pro inzerci volejte / pi&scaron;te</h4>\n<p>Ing. Petra Dost&aacute;lov&aacute;</p>\n<ul>\n<li>email:&nbsp;info@evzdelavani.cz</li>\n<li>tel.:&nbsp;+420 602 545 853</li>\n</ul>\n<p>&nbsp;</p>\n<p>&nbsp;</p>\n<h4>Heldesk / podpora XML</h4>\n<ul>\n<li>email:&nbsp;<a href=\"mailto:helpdesk@evzdelavani.cz\">helpdesk@evzdelavani.cz</a></li>\n</ul>',1,'2016-06-18 18:29:17'),
	('podminky','SMLUVNÍ PODMÍNKY PRO VKLÁDÁNÍ INZERCE DO DATABÁZE SERVERU evzdelavani.cz','<p>(d&aacute;le jen \"Podm&iacute;nky\")<br /><br /><br /></p>\n<h2 style=\"margin-bottom: 10px; font-weight: 500;\">1. Obecn&aacute; ustanoven&iacute;</h2>\n<p>1. Společnost evzděl&aacute;v&aacute;n&iacute;.cz, a. s., se s&iacute;dlem na adrese Praha 8, Na &Scaron;utce 591/13a, PSČ 182 00, Praha 8, IČ: 28981472, zapsan&aacute; v obchodn&iacute;m rejstř&iacute;ku veden&eacute;m Městsk&yacute;m soudem v Praze, odd&iacute;l C vložka 157610 (d&aacute;le jen &bdquo;Provozovatel&ldquo;), je provozovatelem internetov&eacute;ho serveru evzdelavani.cz (d&aacute;le i jako &bdquo;Server&ldquo;) , dostupn&eacute;ho na adrese (URL) http://www.evzdelavani.cz (d&aacute;le jen &bdquo;Služba&ldquo;).<br /><br />2. Provozovatel je opr&aacute;vněn poskytovat př&iacute;stup k uživatelsk&eacute;mu rozhran&iacute; Služby (d&aacute;le jen &bdquo;Z&oacute;na&ldquo;) slouž&iacute;c&iacute;mu pro vložen&iacute; a editaci jednotliv&yacute;ch inzer&aacute;tů a reklamy vzděl&aacute;vac&iacute;ch produktů.<br /><br />3. Objednatelem placen&eacute; inzerce (d&aacute;le jen &bdquo;Objednatel&ldquo;) je fyzick&aacute; či pr&aacute;vnick&aacute; osoba objedn&aacute;vaj&iacute;c&iacute; př&iacute;stup do Z&oacute;ny, jejichž prostřednictv&iacute;m lze zad&aacute;vat jednotlivou inzerci a reklamu na str&aacute;nk&aacute;ch Služby.<br /><br />4. Služba podl&eacute;h&aacute; dle rozhodnut&iacute; Provozovatele &uacute;hradě, a to dle Cen&iacute;ku služeb um&iacute;stěn&eacute;ho na adrese (URL) http://www.evzdelavani.cz/cenik-sluzeb.<br /><br />5. Objednatel souhlas&iacute; se zas&iacute;l&aacute;n&iacute;m novinek na e-mail, kter&yacute; uv&aacute;d&iacute; v registraci.<br /><br /></p>\n<h2 style=\"margin-bottom: 10px; font-weight: 500;\">2. Rozsah a obsah předmětu plněn&iacute;</h2>\n<p>1. Př&iacute;stup do Z&oacute;ny je umožněn na z&aacute;kladě registrace. Registrace je zdarma, zveřejněn&iacute; informac&iacute; o společnosti, lektorovi, inzerce či zakoupen&iacute; jin&eacute;ho reklamn&iacute;ho prostoru podl&eacute;haj&iacute; &uacute;hradě dle Cen&iacute;ku služeb.<br /><br />2. Smluvn&iacute; vztah vznik&aacute; &uacute;hradou pro forma faktury za objednan&eacute; služby. Objednan&eacute; služby se plat&iacute; dopředu na 3 měs&iacute;ce, nebo rok dle Cen&iacute;ku služeb. Smluvn&iacute; vztah vznik&aacute; na dobu neurčitou a lze jej vypovědět pouze p&iacute;semnou formou. Odstoupen&iacute; se ř&iacute;d&iacute; v&yacute;pověďn&iacute; lhůtou 3 měs&iacute;ce. V&yacute;pověď mus&iacute; b&yacute;t učiněna p&iacute;semně a poč&iacute;n&aacute; běžet dnem doručen&iacute; provozovateli.<br /><br />3. Objednatel je opr&aacute;vněn svou registraci kdykoliv zru&scaron;it, a to prostřednictv&iacute;m ž&aacute;dosti odeslan&eacute; formou e-mailov&eacute; zpr&aacute;vy adresovan&eacute; na adresu info@evzdelavani.cz. Do hlavičky (&bdquo;subjekt&ldquo;) takov&eacute;hoto e-mailu uvede &bdquo;Ž&aacute;dost o zru&scaron;en&iacute; registrace&ldquo;. Zru&scaron;en&iacute;m registrace nevznik&aacute; pr&aacute;vo na vr&aacute;cen&iacute; poplatku dle Cen&iacute;ku služeb a finančn&iacute; vyrovn&aacute;n&iacute; se ř&iacute;d&iacute; dle pravidel v&yacute;povědn&iacute; lhůty.<br /><br />4. Inzerce zveřejňovan&aacute; Objednatelem prostřednictv&iacute;m Z&oacute;ny je zpř&iacute;stupněna v&scaron;em uživatelům celosvětov&eacute; poč&iacute;tačov&eacute; s&iacute;tě Internet, a to prostřednictv&iacute;m serveru evzdelavani.cz, jakož i jin&yacute;ch určen&yacute;ch internetov&yacute;ch serverů Provozovatele či jeho smluvn&iacute;ch partnerů.<br /><br />5. Pro př&iacute;stup k Z&oacute;ně slouž&iacute; login (e-mail) a heslo. Objednatel nen&iacute; opr&aacute;vněn tyto &uacute;daje zpř&iacute;stupnit třet&iacute;m osob&aacute;m či se pokou&scaron;et o neopr&aacute;vněn&yacute; př&iacute;stup a neopr&aacute;vněnou manipulaci s daty na Službě. V př&iacute;padě takov&eacute;hoto jedn&aacute;n&iacute; Objednatele je Objednatel plně odpovědn&yacute; za př&iacute;padn&eacute; zneužit&iacute; Z&oacute;ny a/nebo za zneužit&iacute; sv&eacute;ho uživatelsk&eacute;ho jm&eacute;na a/nebo hesla.<br /><br />6. Ve&scaron;ker&aacute; data zad&aacute;v&aacute; Objednatel do Z&oacute;ny na vlastn&iacute; &uacute;čet a n&aacute;klady v souladu s pokyny Provozovatele (zejm&eacute;na n&iacute;že specifikovan&yacute;mi &bdquo;Pravidly inzerce&ldquo;) a nastavenou technickou konfigurac&iacute;. Objednatel nesm&iacute; zejm&eacute;na ohrozit technickou funkčnost Z&oacute;ny. Objednatel je opr&aacute;vněn v r&aacute;mci Z&oacute;ny zad&aacute;vat, upravovat a jinak editovat vlastn&iacute; nab&iacute;dku kurzů, tj. kurzů, kter&eacute; s&aacute;m realizuje, nebo kurzů, kter&eacute; je opr&aacute;vněn nab&iacute;zet na z&aacute;kladě uzavřen&yacute;ch zprostředkovatelsk&yacute;ch a/nebo podobn&yacute;ch smluv s př&iacute;slu&scaron;n&yacute;mi vlastn&iacute;ky dan&yacute;ch kurzů. Objednatel nen&iacute; předev&scaron;&iacute;m opr&aacute;vněn automatick&yacute;mi prostředky stahovat jin&eacute; nab&iacute;dky kurzů než jeho vlastn&iacute;.<br /><br />7. Za obsah a pravdivost uv&aacute;děn&yacute;ch &uacute;dajů je zodpovědn&yacute; v&yacute;lučně Objednatel.<br /><br />8. Objednatel se zavazuje nezveřejňovat ž&aacute;dn&eacute; &uacute;daje, jejichž obsah je v rozporu s pr&aacute;vn&iacute;m ř&aacute;dem Česk&eacute; republiky.<br /><br />D&aacute;le se zavazuje, že nebude Provozovatele činit zodpovědn&yacute;m za jak&eacute;koliv pr&aacute;vn&iacute; n&aacute;roky třet&iacute;ch stran, kter&eacute; vzniknou na z&aacute;kladě zveřejněn&iacute; Objednatelem zadan&yacute;ch dat prostřednictv&iacute;m Z&oacute;ny, a př&iacute;padn&eacute; n&aacute;roky, jež Provozovateli vzniknou z titulu takov&eacute;hoto jedn&aacute;n&iacute; třet&iacute;ch osob v př&iacute;činn&eacute; souvislosti s jedn&aacute;n&iacute;m Objednatele, od&scaron;kodn&iacute;. 9. Provozovatel si vyhrazuje pr&aacute;vo odm&iacute;tnout zveřejnit či smazat jak&eacute;koliv &uacute;daje, pokud:</p>\n<ol>\n<li>jsou v rozporu s pr&aacute;vn&iacute;m ř&aacute;dem Česk&eacute; republiky,</li>\n<li>jsou v rozporu s dobr&yacute;mi mravy, př&iacute;padně ohrožuj&iacute; veřejn&yacute; poř&aacute;dek,</li>\n<li>sv&yacute;m obsahem odporuj&iacute; z&aacute;jmům Provozovatele či jsou v rozporu s těmito Podm&iacute;nkami či n&iacute;že uveden&yacute;mi Pravidly inzerce.</li>\n</ol>\n<p>10. Provozovatel nen&iacute; povinen archivovat inzerci v Z&oacute;ně.<br /><br />11. Objednatel stvrzen&iacute;m těchto Podm&iacute;nek souhlas&iacute; s t&iacute;m, že je Provozovatel opr&aacute;vněn zas&iacute;lat Objednateli informačn&iacute; materi&aacute;ly t&yacute;kaj&iacute;c&iacute; se provozu Služby nebo služeb souvisej&iacute;c&iacute;ch, jakož i o produktech třet&iacute;ch osob.<br /><br /></p>\n<h2 style=\"margin-bottom: 10px; font-weight: 500;\">3. Finančn&iacute; podm&iacute;nky</h2>\n<p>1. Provozovatel je opr&aacute;vněn požadovat platbu předem.<br /><br />2. Provozovatel vystav&iacute; Objednateli za každou objedn&aacute;vku proforma fakturu. Tuto za&scaron;le e-mailem na kontakt zadan&yacute; Objednatelem.<br /><br />3. Vložen&aacute; inzerce je poprv&eacute; zveřejněna až dnem &uacute;hrady &uacute;platy za registraci, kter&yacute;m je den přips&aacute;n&iacute; fakturovan&eacute; č&aacute;stky na &uacute;čet Provozovatele. T&iacute;mto dnem současně započ&iacute;n&aacute; běh stanoven&eacute;ho obdob&iacute; objedn&aacute;vky.<br /><br />4. Provozovatel se zavazuje Objednateli vystavit fakturu-daňov&yacute; doklad do 15 dnů po přips&aacute;n&iacute; fakturovan&eacute; č&aacute;stky, na &uacute;čet Provozovatele.<br /><br />5. Provozovatel je opr&aacute;vněn nezveřejnit inzerci Objednatele v př&iacute;padě ne&uacute;plně uhrazen&yacute;ch plateb či při nemožnosti identifikace platby (neuveden&iacute; variabiln&iacute;ho symbolu). Provozovatel je opr&aacute;vněn k pozastaven&iacute; či odm&iacute;tnut&iacute; objedn&aacute;vky v př&iacute;padě, že eviduje dlužn&eacute; č&aacute;stky Objednatele po splatnosti i na jin&yacute;ch služb&aacute;ch či produktech poskytovan&yacute;ch Provozovatelem.<br /><br />6. Provozovatel si vyhrazuje pr&aacute;vo na jednostrannou změnu cen Služby. Tyto změny sděl&iacute; Provozovatel Objednateli emailovou zpr&aacute;vou s měs&iacute;čn&iacute;m předstihem před &uacute;činnost&iacute; takov&yacute;chto změn. V př&iacute;padě změn produktov&eacute;ho portfolia, k němuž si Provozovatel vyhrazuje pr&aacute;vo, je v&scaron;ak povinen Objednateli vr&aacute;tit již přijat&eacute; &uacute;hrady za nerealizovan&eacute; služby, př&iacute;padně nab&iacute;dnout adekv&aacute;tn&iacute; protiplněn&iacute;.<br /><br /></p>\n<h2 style=\"margin-bottom: 10px; font-weight: 500;\">4. Reklamace</h2>\n<p>1. V př&iacute;padě pochyben&iacute; na straně Provozovatele je Objednatel v r&aacute;mci reklamačn&iacute;ho ř&iacute;zen&iacute; opr&aacute;vněn požadovat přiměřenou n&aacute;hradu. Objednatel je opr&aacute;vněn reklamaci učinit p&iacute;semně, autorizovan&yacute;m e-mailem nebo faxovou zpr&aacute;vou.<br /><br />2. Lhůta pro uplatněn&iacute; reklamace je 14 dn&iacute; ode dne, kdy Objednatel zjistil nebo mohl zjistit pochyben&iacute; Provozovatele.<br /><br />3. Pochyben&iacute;m na straně Provozovatele se rozum&iacute; nefunkčnost Služby, kter&aacute; se t&yacute;k&aacute; potvrzen&eacute; objedn&aacute;vky Objednatele, a to po dobu del&scaron;&iacute; než 6 hodin bez přeru&scaron;en&iacute; v průběhu kalend&aacute;řn&iacute;ho dne.<br /><br />4. Za pochyben&iacute; na straně Provozovatele se nepovažuj&iacute; zejm&eacute;na v&yacute;kyvy v n&aacute;v&scaron;těvnosti jednotliv&yacute;ch serverů Provozovatele.<br /><br />5. N&aacute;mitky vůči vystaven&yacute;m faktur&aacute;m, kter&eacute; by měly za n&aacute;sledek omezen&iacute; pr&aacute;va na vznik pohled&aacute;vky Provozovatele vůči Objednateli, je povinen Objednatel uplatnit u Provozovatele p&iacute;semně a do 7 dnů po doručen&iacute; faktury.<br /><br /></p>\n<h2 style=\"margin-bottom: 10px; font-weight: 500;\">5. Z&aacute;věrečn&aacute; ustanoven&iacute;</h2>\n<p>1. Ot&aacute;zky těmito Smluvn&iacute;mi podm&iacute;nkami neupraven&eacute; se podpůrně ř&iacute;d&iacute; př&iacute;slu&scaron;nou pr&aacute;vn&iacute; &uacute;pravou, a to zejm&eacute;na z&aacute;konem č. 513/1991 Sb., obchodn&iacute;m z&aacute;kon&iacute;kem, ve zněn&iacute; pozděj&scaron;&iacute;ch předpisů.<br /><br />2. Jak&eacute;koliv užit&iacute; obsahu Serveru, popř. jeho č&aacute;st&iacute;, Objednatelem jin&yacute;m způsobem než pro &uacute;čely, k nimž je Server určen, zejm&eacute;na jak&eacute;koliv &scaron;&iacute;řen&iacute; obsahu Služby, je zak&aacute;z&aacute;no. Jsou zak&aacute;z&aacute;ny jak&eacute;koliv z&aacute;sahy do technick&eacute;ho nebo věcn&eacute;ho obsahu Služby. Pr&aacute;vo zhotovit z&aacute;ložn&iacute; kopie materi&aacute;lů obsažen&yacute;ch v Serveru pro osobn&iacute; potřebu v souladu se v&scaron;eobecně z&aacute;vazn&yacute;mi pr&aacute;vn&iacute;mi předpisy nen&iacute; t&iacute;mto ustanoven&iacute;m dotčeno. Jak&eacute;koliv jin&eacute; reprodukce nebo &uacute;pravy prov&aacute;děn&eacute; jak&yacute;mkoliv mechanick&yacute;m nebo elektronick&yacute;m způsobem bez předchoz&iacute;ho p&iacute;semn&eacute;ho souhlasu Provozovatele jsou zak&aacute;z&aacute;ny.<br /><br />3. Dojde-li ke změně jak&yacute;chkoliv fakturačn&iacute;ch &uacute;dajů a kontaktn&iacute;ch osob jak u Objednatele, tak u Provozovatele, jsou smluvn&iacute; strany povinny se o tom vz&aacute;jemně neprodleně informovat. 4. Ve&scaron;ker&eacute; p&iacute;semnosti zas&iacute;lan&eacute; po&scaron;tou se považuj&iacute; za doručen&eacute; 5. dnem po jejich prokazateln&eacute;m odesl&aacute;n&iacute;, a to i v př&iacute;padě, že druh&aacute; strana odm&iacute;tla p&iacute;semnost převz&iacute;t, nebo v př&iacute;padě, že z&aacute;silka byla uložena na po&scaron;tě a druh&aacute; strana si z&aacute;silku nevyzvedla.<br /><br />5. Provozovatel si vyhrazuje pr&aacute;vo jednostranně měnit tyto Podm&iacute;nky i bez předchoz&iacute;ho souhlasu Objednatele, a to t&iacute;m způsobem, že zveřejn&iacute; jejich posledn&iacute; nov&eacute; a &uacute;pln&eacute; zněn&iacute; na str&aacute;nk&aacute;ch Služby. Takov&yacute;m zveřejněn&iacute;m vstoup&iacute; nov&eacute; zněn&iacute; Podm&iacute;nek v &uacute;činnost, pokud nen&iacute; v Podm&iacute;nk&aacute;ch stanoveno datum pozděj&scaron;&iacute;. Upozorněn&iacute; na skutečnost, že do&scaron;lo ke změně Podm&iacute;nek, bude na Serveru zveřejněno nejm&eacute;ně po dobu jednoho měs&iacute;ce ode dne, kdy nov&eacute; zněn&iacute; Podm&iacute;nek vstoup&iacute; v &uacute;činnost. V př&iacute;padě, že Objednatel nesouhlas&iacute; s ustanoven&iacute;m nov&yacute;ch smluvn&iacute;ch podm&iacute;nek, může vypovědět registraci a v tomto př&iacute;padě mu n&aacute;lež&iacute; vr&aacute;cen&iacute; poměrn&eacute; č&aacute;sti &uacute;hrady za registraci.<br /><br />6. Tyto Podm&iacute;nky nab&yacute;vaj&iacute; platnosti a &uacute;činnosti dnem 1. června 2010.<br /><br /></p>\n<h2 style=\"margin-bottom: 10px; font-weight: bold;\">Pravidla inzerce</h2>\n<p><strong>a. pravidla inzerce</strong><br /><br />1. Syst&eacute;m je určen pro samoobslužn&eacute; vkl&aacute;d&aacute;n&iacute;, spr&aacute;vu a zveřejňov&aacute;n&iacute; inzerce, před vstupem do syst&eacute;mu se mus&iacute; Objednatel přihl&aacute;sit. K přihl&aacute;&scaron;en&iacute; slouž&iacute; e-mail a heslo.<br /><br />2. Inzertn&iacute; obsah je přij&iacute;m&aacute;n v česk&eacute;m jazyce, s diakritikou a celkově v souladu s platn&yacute;mi pravidly česk&eacute;ho pravopisu.<br /><br />3. Př&iacute;slu&scaron;n&eacute; zobrazen&eacute; URL mus&iacute; odpov&iacute;dat dom&eacute;ně c&iacute;lov&eacute;ho URL. Str&aacute;nka, na kterou je přesměrov&aacute;no, mus&iacute; b&yacute;t funkčn&iacute; a obsahově synchronn&iacute;, ze str&aacute;nky mus&iacute; b&yacute;t možnost vr&aacute;tit se jedin&yacute;m kliknut&iacute;m na tlač&iacute;tko zpět. Na Službě nebudou zveřejňov&aacute;ny odkazy na inzertn&iacute; servery se stejn&yacute;m zaměřen&iacute;m jako Služba.<br /><br />4. Objednatel se zavazuje zařazovat inzer&aacute;ty do odpov&iacute;daj&iacute;c&iacute; rubriky a co nejrychleji aktualizovat inzer&aacute;ty, když dojde ke změně jejich stavu nebo &uacute;dajů v nich uveden&yacute;ch.<br /><br />5. Objednatel se zavazuje zařazovat odpov&iacute;daj&iacute;c&iacute; &uacute;daje do spr&aacute;vn&yacute;ch kolonek a m&iacute;st k tomu určen&yacute;ch a nezveřejňovat ž&aacute;dn&eacute; kontaktn&iacute; informace na osoby, firmy nebo webov&eacute; str&aacute;nky mimo tato m&iacute;sta.<br /><br />6. Objednatel se zavazuje v prezentovan&eacute; nab&iacute;dce či popt&aacute;vce poskytovat informace pravdiv&eacute;, a to včetně fin&aacute;ln&iacute; tržn&iacute; ceny, DPH.<br /><br />7. Objednatel nebude vkl&aacute;dat či importovat do datab&aacute;ze identick&eacute; nab&iacute;dky s různou nebo nulovou cenou či drobn&yacute;mi odchylkami popisu.<br /><br />8. Provozovatel si vyhrazuje pr&aacute;vo kontroly obsahu. V textu inzer&aacute;tu je vyloučeno: o zveřejněn&iacute; verz&aacute;lkami psan&yacute;ch slov (pokud se nejedn&aacute; o zkratky či n&aacute;zvy, kter&eacute; se ofici&aacute;lně zapisuj&iacute; pouze verz&aacute;lkami),</p>\n<div style=\"margin: 6px 0px 6px 30px;\">\n<ul>\n<li>uveřejněn&iacute; v&iacute;ce než jednoho vykřičn&iacute;ku (před vykřičn&iacute;kem nesm&iacute; b&yacute;t mezera),</li>\n<li>psan&iacute; t&aacute;zac&iacute;ch, zvolac&iacute;ch a rozkazovac&iacute;ch vět,</li>\n<li>několikan&aacute;sobn&eacute; opakov&aacute;n&iacute; slova/fr&aacute;ze, nadměrn&aacute; interpunkce (tři tečky), &bdquo;smajl&iacute;ky&ldquo;, HTML značky,</li>\n<li>ukončen&iacute; v&yacute;čtu v&yacute;razy &bdquo;atd.&ldquo; , &bdquo;a tak d&aacute;le&ldquo;, &bdquo;...&ldquo; a jejich ekvivalenty,</li>\n<li>použ&iacute;v&aacute;n&iacute; superlativů a objektivně neposouditeln&yacute;ch zm&iacute;nek o kvalitě, např: &bdquo;nejlep&scaron;&iacute; služby poskytovan&eacute;&hellip;&ldquo;,</li>\n<li>obchodn&iacute; sdělen&iacute;, reklama či zm&iacute;nky o konkurenci,</li>\n</ul>\n</div>\n<p>9. Provozovatel nen&iacute; povinen sdělovat krit&eacute;ria řazen&iacute; (relevance) inzer&aacute;tů ve v&yacute;pisu. Provozovatel v&scaron;ak zaručuje, že řazen&iacute; inzer&aacute;tů je jednotn&eacute; a plně zautomatizov&aacute;no pro v&scaron;echny uživatele syst&eacute;mu. Na relevanci pořad&iacute; jednotliv&yacute;ch inzer&aacute;tů maj&iacute; vliv např&iacute;klad datum vložen&iacute; inzer&aacute;tu, čas a počet aktualizac&iacute;, změna ceny, počet vyplněn&yacute;ch položek k inzer&aacute;tu, životnost inzer&aacute;tů, počet inzer&aacute;tů, statistika n&aacute;v&scaron;těvnosti, věrnostn&iacute; charakter klienta (d&eacute;lka spolupr&aacute;ce) a dal&scaron;&iacute;.<br /><br />10. Vkl&aacute;d&aacute;n&iacute; inzerce je podporov&aacute;no z vět&scaron;iny softwarů nebo vlastn&iacute;ch aplikac&iacute; Objednatele. Využit&iacute; t&eacute;to funkcionality v&scaron;ak nen&iacute; souč&aacute;st&iacute; placen&yacute;ch služeb, a provozovatel tak nenese odpovědnost za aktu&aacute;lnost či spr&aacute;vnost takto do datab&aacute;ze vložen&yacute;ch inzer&aacute;tů.<br /><br />11. Při exportu vybran&eacute;ho souboru z datab&aacute;ze na dal&scaron;&iacute; m&iacute;sta na Internetu se může popis inzer&aacute;tů zkr&aacute;tit či upravit tak, aby odpov&iacute;dal technick&eacute; konfiguraci př&iacute;jemce.<br /><br />13. Provozovatel poskytuje v&scaron;em plat&iacute;c&iacute;m uživatelům technickou podporu na webmaster@evzdelavani.cz.<br /><br />14. Provozovatel si vyhrazuje pr&aacute;vo inzerci kdykoli pozastavit či ukončit, vyřadit inzer&aacute;ty nebo Objednatele ze syst&eacute;mu, odepř&iacute;t Objednateli př&iacute;stup na Službu z důvodu z&aacute;važn&eacute;ho poru&scaron;en&iacute; někter&eacute;ho ustanoven&iacute; těchto Podm&iacute;nek anebo z důvodu opakovan&eacute;ho m&eacute;ně z&aacute;važn&eacute;ho poru&scaron;ov&aacute;n&iacute;.<br /><br />15. V př&iacute;padě obnoven&iacute; registrace je Provozovatel opr&aacute;vněn po Objednateli, jehož registrace byla pozastavena či ukončena z důvodu uveden&eacute;ho v bodech 5&ndash;10 nebo 19 Pravidel inzerce těchto Podm&iacute;nek, požadovat &uacute;hradu jednor&aacute;zov&eacute;ho poplatku za obnovu registrace ve v&yacute;&scaron;i 20 000 Kč (slovy: dvacet tis&iacute;c korun česk&yacute;ch).<br /><br /></p>\n<div>16. S&nbsp;ohledem na z&aacute;kon č. 480/2004 Sb. o někter&yacute;ch služb&aacute;ch informačn&iacute;ch společnost&iacute; registrovan&yacute; uživatel souhlas&iacute; se zas&iacute;l&aacute;n&iacute;m aktu&aacute;ln&iacute;ch obchodn&iacute;ch informac&iacute; společnosti <strong>evzděl&aacute;v&aacute;n&iacute;.cz, s.r.o.</strong></div>\n<div>Obsahem jsou aktu&aacute;ln&iacute; informace z oboru vzděl&aacute;v&aacute;n&iacute; formou newsletteru nebo mailingu.</div>\n<div>Zas&iacute;l&aacute;n&iacute; informačn&iacute;ch e-mailů&nbsp;je možn&eacute;&nbsp;kdykoliv ukončit zasl&aacute;n&iacute;m e-mailu.</div>\n<p><br /><strong>b. pravidla pro využit&iacute; last moment</strong><br /><br />1. Last moment služba je určena pro kurzy zač&iacute;naj&iacute;c&iacute; nejdř&iacute;ve 3 t&yacute;dny před zah&aacute;jen&iacute;m kurzu.<br /><br />2. Službu lze nastavit automaticky, nebo pro každ&yacute; inzer&aacute;t zvl&aacute;&scaron;ť. K editaci slouž&iacute; kalend&aacute;ř, nebo rubrika pro editaci kurzů v Z&oacute;ně Objednatele.<br /><br />3. Objednatel může m&iacute;t aktivn&iacute; každ&yacute; den 1 inzer&aacute;t v sekci Last moment.<br /><br />4. Využit&iacute; služby podl&eacute;h&aacute; &uacute;hradě dle Cen&iacute;ku služeb.</p>',1,'2016-06-18 18:27:06');

/*!40000 ALTER TABLE `static_page` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table term
# ------------------------------------------------------------

DROP TABLE IF EXISTS `term`;

CREATE TABLE `term` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` char(128) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `course_id` int(11) unsigned NOT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  `noterm` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `price_flag` varchar(11) DEFAULT NULL,
  `price` int(11) unsigned NOT NULL,
  `currency` char(3) NOT NULL,
  `vat` int(11) DEFAULT NULL,
  `address_note` text NOT NULL,
  `city` varchar(128) NOT NULL,
  `street` varchar(128) NOT NULL,
  `registry_number` int(11) unsigned NOT NULL,
  `house_number` int(11) unsigned NOT NULL,
  `zip` int(8) NOT NULL,
  `address_flag` varchar(11) DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `lector_firstname` varchar(255) NOT NULL DEFAULT '',
  `lector_surname` varchar(255) NOT NULL DEFAULT '',
  `lector_degrees_before` varchar(255) NOT NULL DEFAULT '',
  `lector_degrees_after` varchar(255) NOT NULL DEFAULT '',
  `lector_description` text NOT NULL,
  `lector_skills` text NOT NULL,
  `lector_image` varchar(255) NOT NULL DEFAULT '',
  `country_key` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ibfk_term_1` (`address_flag`),
  KEY `ibfk_term_2` (`currency`),
  KEY `ibfk_term_3` (`course_id`),
  KEY `ibfk_term_4` (`country_key`),
  CONSTRAINT `ibfk_term_2` FOREIGN KEY (`currency`) REFERENCES `currency` (`currency`) ON UPDATE CASCADE,
  CONSTRAINT `ibfk_term_3` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `ibfk_term_4` FOREIGN KEY (`country_key`) REFERENCES `country` (`key`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL DEFAULT '',
  `surname` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ibu_users_2` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `firstname`, `surname`, `email`, `password`, `active`, `last_login`, `created_at`)
VALUES
	(1,'Jakub','Mares','jakubmares@ymail.com','$2y$10$.XQldab9Dm4re2v6UOd7cuwIGvlJgshcIEOGiT.og.oUOPvEIiay.',1,NULL,'0000-00-00 00:00:00'),
	(2, 'Petra', 'Dostalova', 'petra.dostalova@evzdelavani.cz', '$2y$10$hAl9.gIBTDKLYy.Zr.Si9e2cGJF2KonrDIRiSMz2HReeAekjnUftq', 1, NULL, '0000-00-00 00:00:00'),
	(3, 'Viktorie', 'Kopecna', 'viktorie.kopecna@evzdelavani.cz', '$2y$10$D3ttvzR/QWQzEn/wU9r8q.3MBGSYiWY2mHZcnivHu.e117BjaUc/u', 0, NULL, '0000-00-00 00:00:00');


/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `ibfk_user_role_2` (`role_id`),
  CONSTRAINT `ibfk_user_role_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ibfk_user_role_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;

INSERT INTO `user_role` (`user_id`, `role_id`)
VALUES
	(1, 4),
	(2, 3),
	(2, 4),
	(3, 3),
	(3, 4);


/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
