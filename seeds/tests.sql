-- MySQL dump 10.13  Distrib 5.6.38, for osx10.9 (x86_64)
--
-- Host: localhost    Database: craftql
-- ------------------------------------------------------
-- Server version	5.6.38-log

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
-- Table structure for table `assetindexdata`
--

DROP TABLE IF EXISTS `assetindexdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assetindexdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessionId` varchar(36) NOT NULL DEFAULT '',
  `volumeId` int(11) NOT NULL,
  `uri` text,
  `size` bigint(20) unsigned DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `recordId` int(11) DEFAULT NULL,
  `inProgress` tinyint(1) DEFAULT '0',
  `completed` tinyint(1) DEFAULT '0',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `assetindexdata_sessionId_volumeId_idx` (`sessionId`,`volumeId`),
  KEY `assetindexdata_volumeId_idx` (`volumeId`),
  CONSTRAINT `assetindexdata_volumeId_fk` FOREIGN KEY (`volumeId`) REFERENCES `volumes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assetindexdata`
--

LOCK TABLES `assetindexdata` WRITE;
/*!40000 ALTER TABLE `assetindexdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `assetindexdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `volumeId` int(11) DEFAULT NULL,
  `folderId` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `kind` varchar(50) NOT NULL DEFAULT 'unknown',
  `width` int(11) unsigned DEFAULT NULL,
  `height` int(11) unsigned DEFAULT NULL,
  `size` bigint(20) unsigned DEFAULT NULL,
  `focalPoint` varchar(13) DEFAULT NULL,
  `dateModified` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_filename_folderId_unq_idx` (`filename`,`folderId`),
  KEY `assets_folderId_idx` (`folderId`),
  KEY `assets_volumeId_idx` (`volumeId`),
  CONSTRAINT `assets_folderId_fk` FOREIGN KEY (`folderId`) REFERENCES `volumefolders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assets_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assets_volumeId_fk` FOREIGN KEY (`volumeId`) REFERENCES `volumes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (22,1,1,'Screen-Shot-2019-02-24-at-9.18.39-AM.png','image',637,549,480559,NULL,'2019-02-24 14:18:52','2019-02-24 14:18:52','2019-02-24 14:18:52','17d8d47a-8f99-425d-b2ab-4435631f5fcc');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assettransformindex`
--

DROP TABLE IF EXISTS `assettransformindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assettransformindex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assetId` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `volumeId` int(11) DEFAULT NULL,
  `fileExists` tinyint(1) NOT NULL DEFAULT '0',
  `inProgress` tinyint(1) NOT NULL DEFAULT '0',
  `dateIndexed` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `assettransformindex_volumeId_assetId_location_idx` (`volumeId`,`assetId`,`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assettransformindex`
--

LOCK TABLES `assettransformindex` WRITE;
/*!40000 ALTER TABLE `assettransformindex` DISABLE KEYS */;
/*!40000 ALTER TABLE `assettransformindex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assettransforms`
--

DROP TABLE IF EXISTS `assettransforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assettransforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `mode` enum('stretch','fit','crop') NOT NULL DEFAULT 'crop',
  `position` enum('top-left','top-center','top-right','center-left','center-center','center-right','bottom-left','bottom-center','bottom-right') NOT NULL DEFAULT 'center-center',
  `width` int(11) unsigned DEFAULT NULL,
  `height` int(11) unsigned DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `quality` int(11) DEFAULT NULL,
  `interlace` enum('none','line','plane','partition') NOT NULL DEFAULT 'none',
  `dimensionChangeTime` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `assettransforms_name_unq_idx` (`name`),
  UNIQUE KEY `assettransforms_handle_unq_idx` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assettransforms`
--

LOCK TABLES `assettransforms` WRITE;
/*!40000 ALTER TABLE `assettransforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `assettransforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `categories_groupId_idx` (`groupId`),
  CONSTRAINT `categories_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `categorygroups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `categories_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,1,'2019-02-21 20:04:04','2019-02-21 20:04:09','2a7c83e4-5413-4c84-a443-1984916f2445'),(3,1,'2019-02-21 20:04:15','2019-02-21 20:04:21','f1604a3e-b84c-47df-93af-9542060b9c4d'),(4,1,'2019-02-21 20:04:30','2019-02-21 20:04:30','136e8c9e-8201-4935-811f-59725d3220f7'),(5,3,'2019-02-21 20:06:36','2019-02-21 20:06:59','b9da3cd9-872e-4476-9d27-a5dcfb77bae1'),(6,3,'2019-02-21 20:06:49','2019-02-21 20:07:03','36f039fb-67ba-4ef9-b013-7745615f919a'),(7,3,'2019-02-21 20:07:32','2019-02-21 20:07:32','9ac62771-62c8-47f2-ab23-7af1516047eb'),(8,3,'2019-02-21 20:07:52','2019-02-21 20:07:52','96e06d7b-681f-484a-93d3-e6240bf11f53');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorygroups`
--

DROP TABLE IF EXISTS `categorygroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorygroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structureId` int(11) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorygroups_name_unq_idx` (`name`),
  UNIQUE KEY `categorygroups_handle_unq_idx` (`handle`),
  KEY `categorygroups_structureId_idx` (`structureId`),
  KEY `categorygroups_fieldLayoutId_idx` (`fieldLayoutId`),
  CONSTRAINT `categorygroups_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `categorygroups_structureId_fk` FOREIGN KEY (`structureId`) REFERENCES `structures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorygroups`
--

LOCK TABLES `categorygroups` WRITE;
/*!40000 ALTER TABLE `categorygroups` DISABLE KEYS */;
INSERT INTO `categorygroups` VALUES (1,1,6,'Test Category Group','testCategoryGroup','2019-02-21 20:03:17','2019-02-21 20:03:17','991eb00a-637c-491c-99c1-c57b0b0c7113'),(2,2,7,'Test Empty Category Group','testEmptyCategoryGroup','2019-02-21 20:03:45','2019-02-21 20:03:45','c06598c7-a97e-4278-8134-454e9488d268'),(3,3,8,'Test Nested Category Group','testNestedCategoryGroup','2019-02-21 20:06:08','2019-02-21 20:06:08','7b45d3c7-f64d-4f84-8b3b-609ae9eee0c7');
/*!40000 ALTER TABLE `categorygroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorygroups_sites`
--

DROP TABLE IF EXISTS `categorygroups_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorygroups_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `hasUrls` tinyint(1) NOT NULL DEFAULT '1',
  `uriFormat` text,
  `template` varchar(500) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorygroups_sites_groupId_siteId_unq_idx` (`groupId`,`siteId`),
  KEY `categorygroups_sites_siteId_idx` (`siteId`),
  CONSTRAINT `categorygroups_sites_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `categorygroups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `categorygroups_sites_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorygroups_sites`
--

LOCK TABLES `categorygroups_sites` WRITE;
/*!40000 ALTER TABLE `categorygroups_sites` DISABLE KEYS */;
INSERT INTO `categorygroups_sites` VALUES (1,1,1,1,'test-category-group/{slug}','test-category-group/_category','2019-02-21 20:03:17','2019-02-21 20:03:17','be92165a-e278-442b-b71a-3edb87912912'),(2,2,1,1,'test-empty-category-group/{slug}','test-empty-category-group/_category','2019-02-21 20:03:45','2019-02-21 20:03:45','ebd196a6-0a56-42f6-bb68-39ee5e5cd03f'),(3,3,1,1,'test-nested-category-group/{slug}','test-nested-category-group/_category','2019-02-21 20:06:08','2019-02-21 20:06:08','8101b3d0-a5c2-40de-a52a-5a990282d9c4');
/*!40000 ALTER TABLE `categorygroups_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  `field_testPlainText` text,
  `field_testPlainTextWithCharacterLimit` text,
  `field_testCheckboxes` varchar(255) DEFAULT NULL,
  `field_testCheckboxesWithOneBadValue` varchar(255) DEFAULT NULL,
  `field_testCheckboxesWithNumericValue` varchar(255) DEFAULT NULL,
  `field_testColorField` varchar(7) DEFAULT NULL,
  `field_testColorFieldWithDefaultValue` varchar(7) DEFAULT NULL,
  `field_testDateField` datetime DEFAULT NULL,
  `field_testTimeField` datetime DEFAULT NULL,
  `field_testDateAndTimeField` datetime DEFAULT NULL,
  `field_testDropdownField` varchar(255) DEFAULT NULL,
  `field_testEmailField` varchar(255) DEFAULT NULL,
  `field_testLightswitchField` tinyint(1) DEFAULT NULL,
  `field_testLightswitchOnField` tinyint(1) DEFAULT NULL,
  `field_testMultiSelectField` varchar(255) DEFAULT NULL,
  `field_testNumberField` int(10) DEFAULT NULL,
  `field_testNumberFloatField` decimal(12,2) DEFAULT NULL,
  `field_testNumberMaxField` smallint(2) DEFAULT NULL,
  `field_testRadioButtonField` varchar(255) DEFAULT NULL,
  `field_testTableField` text,
  `field_testUrlField` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_elementId_siteId_unq_idx` (`elementId`,`siteId`),
  KEY `content_siteId_idx` (`siteId`),
  KEY `content_title_idx` (`title`),
  CONSTRAINT `content_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `content_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,1,1,NULL,'2019-02-21 19:51:04','2019-02-24 00:56:35','af911f2a-47bd-4131-960f-ff45f1bf7f1d',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,2,1,'Test Category One','2019-02-21 20:04:04','2019-02-21 20:04:09','c743c203-7ac9-4d60-b963-d3661782a33b',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,3,1,'Test Category Two','2019-02-21 20:04:15','2019-02-21 20:04:21','a1ae65c5-eafd-45ea-9e77-627716d36eb9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,4,1,'Test Category Three','2019-02-21 20:04:30','2019-02-21 20:04:30','b665e47a-b64c-48df-82c3-83c56d9c500a',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,5,1,'Test Grandparent Category','2019-02-21 20:06:36','2019-02-21 20:06:59','97a09a34-d4b9-4278-8eb9-be7f7b0f4dd6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,6,1,'Test Parent Category','2019-02-21 20:06:49','2019-02-21 20:07:03','805b547f-c2d4-4687-be86-0841eef44e94',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,7,1,'Test Nested Category','2019-02-21 20:07:32','2019-02-21 20:07:32','0fd99ce9-ae51-4a18-88d9-fa5824c7b5b6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,8,1,'Test Child Category','2019-02-21 20:07:52','2019-02-21 20:07:52','278856cc-052c-46f9-a54e-8a216e6a3dd0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,9,1,'Test Empty Entry','2019-02-21 20:14:28','2019-02-24 14:31:43','c584c09a-2108-4bbd-982b-f8a4f724e969',NULL,NULL,'[\"valueTwo\"]','[]','[]',NULL,'#ffcc00',NULL,NULL,NULL,'labelOne','',0,1,'[]',NULL,NULL,NULL,NULL,'[{\"col1\":\"\",\"col2\":\"\",\"col3\":null,\"col4\":\"\"}]',''),(10,10,1,'tag one','2019-02-21 20:15:39','2019-02-21 20:15:39','be751a77-00c3-455e-86dc-43e4d56e900b',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,11,1,'Test Partial Entry','2019-02-21 20:16:31','2019-02-24 14:31:44','67a6ba3c-f269-4cfb-82e7-6c22bebf29eb','Test Plain Text Content',NULL,'[\"valueTwo\"]','[]','[\"3\"]',NULL,'#ffcc00','2019-02-21 08:00:00',NULL,NULL,'labelThree','test@email.content',0,1,'[\"optionThree\"]',1,1.23,8,NULL,'[{\"col1\":\"column one content\",\"col2\":\"column two content\",\"col3\":\"#ffcc00\",\"col4\":\"1\"}]',''),(12,13,1,'tag two','2019-02-21 21:26:15','2019-02-21 21:26:15','fbc35ad4-234e-4c3d-82c1-4bce391c8a2c',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,14,1,'tw','2019-02-21 21:26:21','2019-02-21 21:26:21','03b9fe83-87c0-4a49-b3d7-beb830cfff3b',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,15,1,'Full Entry','2019-02-21 21:28:43','2019-02-24 14:31:45','fdcec87d-ab5b-44e6-9414-5539869ab550','Test Plain Text Content','Test Plain Text With Character Limit Content','[\"valueTwo\"]','[\"labelTwo\"]','[\"2\"]','#ff0000','#ff0000','2019-02-21 08:00:00','2019-02-24 21:00:00','2019-02-21 21:00:00','labelTwo','test@email.content',1,1,'[\"optionTwo\"]',1,2.34,5,'optionTwo','[{\"col1\":\"Column One Content\",\"col2\":\"Column Two Content\",\"col3\":\"#ff0000\",\"col4\":\"1\"}]','http://www.test-url-field.com'),(15,19,1,NULL,'2019-02-23 23:38:37','2019-02-23 23:40:35','708de5c9-fa61-432f-975d-70a1b416de4e','Test Plain Text Content','Test Plain Text Content Limited',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,20,1,NULL,'2019-02-23 23:39:31','2019-02-23 23:40:45','1796b591-e493-4876-bc42-3576469271b9','Test Second Global Set Text Content',NULL,'[\"valueTwo\"]',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'labelOne',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,21,1,NULL,'2019-02-23 23:39:56','2019-02-23 23:39:56','aa2e1068-52b9-47ed-a6b2-dfcfc5748a9c',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,22,1,'Screen Shot 2019 02 24 At 9 18 39 Am','2019-02-24 14:18:48','2019-02-24 14:18:48','f57c4a5b-e071-4dc5-9f67-bdc8f7e043b0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `craftidtokens`
--

DROP TABLE IF EXISTS `craftidtokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craftidtokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `accessToken` text NOT NULL,
  `expiryDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `craftidtokens_userId_fk` (`userId`),
  CONSTRAINT `craftidtokens_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `craftidtokens`
--

LOCK TABLES `craftidtokens` WRITE;
/*!40000 ALTER TABLE `craftidtokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `craftidtokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `craftql_tokens`
--

DROP TABLE IF EXISTS `craftql_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craftql_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` char(128) DEFAULT NULL,
  `token` char(64) NOT NULL,
  `scopes` text,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `craftql_tokens`
--

LOCK TABLES `craftql_tokens` WRITE;
/*!40000 ALTER TABLE `craftql_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `craftql_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deprecationerrors`
--

DROP TABLE IF EXISTS `deprecationerrors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deprecationerrors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `fingerprint` varchar(255) NOT NULL,
  `lastOccurrence` datetime NOT NULL,
  `file` varchar(255) NOT NULL,
  `line` smallint(6) unsigned DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `traces` text,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `deprecationerrors_key_fingerprint_unq_idx` (`key`,`fingerprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deprecationerrors`
--

LOCK TABLES `deprecationerrors` WRITE;
/*!40000 ALTER TABLE `deprecationerrors` DISABLE KEYS */;
/*!40000 ALTER TABLE `deprecationerrors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elementindexsettings`
--

DROP TABLE IF EXISTS `elementindexsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elementindexsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `settings` text,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `elementindexsettings_type_unq_idx` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elementindexsettings`
--

LOCK TABLES `elementindexsettings` WRITE;
/*!40000 ALTER TABLE `elementindexsettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `elementindexsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elements`
--

DROP TABLE IF EXISTS `elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `elements_fieldLayoutId_idx` (`fieldLayoutId`),
  KEY `elements_type_idx` (`type`),
  KEY `elements_enabled_idx` (`enabled`),
  KEY `elements_archived_dateCreated_idx` (`archived`,`dateCreated`),
  CONSTRAINT `elements_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elements`
--

LOCK TABLES `elements` WRITE;
/*!40000 ALTER TABLE `elements` DISABLE KEYS */;
INSERT INTO `elements` VALUES (1,NULL,'craft\\elements\\User',1,0,'2019-02-21 19:51:04','2019-02-24 00:56:35','5dd11407-3ad2-4d95-aa7b-5366ddd2db1e'),(2,6,'craft\\elements\\Category',1,0,'2019-02-21 20:04:04','2019-02-21 20:04:09','bc874eba-0b2e-4270-a0aa-92b9b5ad9a09'),(3,6,'craft\\elements\\Category',1,0,'2019-02-21 20:04:15','2019-02-21 20:04:21','6b197a7c-fc66-4504-bdce-7dcf48c0cbe4'),(4,6,'craft\\elements\\Category',1,0,'2019-02-21 20:04:30','2019-02-21 20:04:30','eea85eae-eef8-476c-9f84-db64e2217608'),(5,8,'craft\\elements\\Category',1,0,'2019-02-21 20:06:36','2019-02-21 20:06:59','8e6a09aa-be1e-47c4-8cf2-851e6695a98c'),(6,8,'craft\\elements\\Category',1,0,'2019-02-21 20:06:49','2019-02-21 20:07:03','a45b5669-2403-43b3-8ef9-8bc6bd2d6686'),(7,8,'craft\\elements\\Category',1,0,'2019-02-21 20:07:32','2019-02-21 20:07:32','6051083c-db68-4030-a456-a685e7c9e7b4'),(8,8,'craft\\elements\\Category',1,0,'2019-02-21 20:07:51','2019-02-21 20:07:51','337a915c-0e05-4229-8bf2-b3b18c86e395'),(9,1,'craft\\elements\\Entry',1,0,'2019-02-21 20:14:28','2019-02-24 14:31:43','b3611dde-1b20-4445-8444-b6fbbf5953ea'),(10,4,'craft\\elements\\Tag',1,0,'2019-02-21 20:15:39','2019-02-21 20:15:39','dd806fd6-1f6c-4609-92dd-3dc95c3db1eb'),(11,1,'craft\\elements\\Entry',1,0,'2019-02-21 20:16:31','2019-02-24 14:31:44','f44a124f-471c-49a7-b5a8-cdec82ec5b3e'),(12,2,'craft\\elements\\MatrixBlock',1,0,'2019-02-21 20:16:31','2019-02-24 14:31:45','a722c2bf-0ca8-4ff2-ba8f-e26238a52eab'),(13,4,'craft\\elements\\Tag',1,0,'2019-02-21 21:26:15','2019-02-21 21:26:15','1697a5d4-dc9c-4422-83e0-9b32a6e261b3'),(14,4,'craft\\elements\\Tag',1,0,'2019-02-21 21:26:21','2019-02-21 21:26:21','bc5bc50f-5b36-401a-99f2-32c297ea2f92'),(15,1,'craft\\elements\\Entry',1,0,'2019-02-21 21:28:43','2019-02-24 14:31:45','b3c82a03-d285-4377-8e82-6c90283db6ef'),(16,2,'craft\\elements\\MatrixBlock',1,0,'2019-02-21 21:28:43','2019-02-24 14:31:46','7cc24075-fabd-4cd1-947c-51754912013e'),(17,2,'craft\\elements\\MatrixBlock',1,0,'2019-02-21 21:28:43','2019-02-24 14:31:46','60f44530-fbb2-46cc-acbb-3e3b13572208'),(18,3,'craft\\elements\\MatrixBlock',1,0,'2019-02-21 21:28:43','2019-02-24 14:31:46','19e86fac-f53b-4289-996a-698645e7d6ad'),(19,9,'craft\\elements\\GlobalSet',1,0,'2019-02-23 23:38:37','2019-02-23 23:40:35','e8cb6f72-9319-4ccc-8b6c-2b45c0b5181e'),(20,10,'craft\\elements\\GlobalSet',1,0,'2019-02-23 23:39:31','2019-02-23 23:40:45','eadb6463-92d1-449d-ad64-e8965e8ff993'),(21,11,'craft\\elements\\GlobalSet',1,0,'2019-02-23 23:39:56','2019-02-23 23:39:56','d08053c5-7d55-426d-b114-3d73c09bce9c'),(22,12,'craft\\elements\\Asset',1,0,'2019-02-24 14:18:48','2019-02-24 14:18:48','89827ad6-0ff6-4fed-bc04-07449eaf568b');
/*!40000 ALTER TABLE `elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elements_sites`
--

DROP TABLE IF EXISTS `elements_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `elements_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `elements_sites_elementId_siteId_unq_idx` (`elementId`,`siteId`),
  KEY `elements_sites_siteId_idx` (`siteId`),
  KEY `elements_sites_slug_siteId_idx` (`slug`,`siteId`),
  KEY `elements_sites_enabled_idx` (`enabled`),
  KEY `elements_sites_uri_siteId_idx` (`uri`,`siteId`),
  CONSTRAINT `elements_sites_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `elements_sites_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elements_sites`
--

LOCK TABLES `elements_sites` WRITE;
/*!40000 ALTER TABLE `elements_sites` DISABLE KEYS */;
INSERT INTO `elements_sites` VALUES (1,1,1,NULL,NULL,1,'2019-02-21 19:51:04','2019-02-24 00:56:35','58396bbc-0054-4fa9-9017-f13f868f33b7'),(2,2,1,'test-category-one','test-category-group/test-category-one',1,'2019-02-21 20:04:04','2019-02-21 20:04:09','db365168-db3d-4b84-ba31-f4620eebf8d6'),(3,3,1,'test-category-two','test-category-group/test-category-two',1,'2019-02-21 20:04:15','2019-02-21 20:04:21','e27ec80a-a0fd-4bc9-8522-8be2abd2feb8'),(4,4,1,'test-category-three','test-category-group/test-category-three',1,'2019-02-21 20:04:30','2019-02-21 20:04:34','228c1dbe-fca6-492d-b39d-74f55fc870df'),(5,5,1,'test-grandparent-category','test-nested-category-group/test-grandparent-category',1,'2019-02-21 20:06:36','2019-02-21 20:06:59','a01b5133-ad6d-4cf3-bb6b-6b7d68ef7365'),(6,6,1,'test-parent-category','test-nested-category-group/test-parent-category',1,'2019-02-21 20:06:49','2019-02-21 20:07:07','f6884b94-2c43-42c7-947b-cb28e27f97dc'),(7,7,1,'test-nested-category','test-nested-category-group/test-nested-category',1,'2019-02-21 20:07:32','2019-02-21 20:07:35','d1823543-a633-4663-8da0-3c86867ba6fc'),(8,8,1,'test-child-category','test-nested-category-group/test-child-category',1,'2019-02-21 20:07:52','2019-02-21 20:07:56','0d104bd6-2a74-472e-a868-cce832d52181'),(9,9,1,'test-empty-entry','blog-post/test-empty-entry',1,'2019-02-21 20:14:28','2019-02-24 14:31:43','74cb5771-3aec-4a6a-8c8b-f55e889c5533'),(10,10,1,'tag-one',NULL,1,'2019-02-21 20:15:39','2019-02-21 20:15:39','94301231-04dc-473f-baee-483a82cc0442'),(11,11,1,'test-partial-entry','blog-post/test-partial-entry',1,'2019-02-21 20:16:31','2019-02-24 14:31:44','1b0a4043-b190-4632-a6b1-c9dd34847f5c'),(12,12,1,NULL,NULL,1,'2019-02-21 20:16:31','2019-02-24 14:31:45','8253aef7-6e9e-4719-a532-912f0d3fcb41'),(13,13,1,'tag-two',NULL,1,'2019-02-21 21:26:15','2019-02-21 21:26:15','30a357da-c028-45d3-95d3-d8559458f18b'),(14,14,1,'tw',NULL,1,'2019-02-21 21:26:21','2019-02-21 21:26:21','12bb4fa1-2b3b-4b5c-ab4d-f058c9dbedc2'),(15,15,1,'full-entry','blog-post/full-entry',1,'2019-02-21 21:28:43','2019-02-24 14:31:45','ad555b95-0fce-4f57-8bb2-807666b9f153'),(16,16,1,NULL,NULL,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','abc7394e-dc79-472c-98be-6c01979ece04'),(17,17,1,NULL,NULL,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','176f73e6-3131-4cd2-9b16-bb37dc5b95c8'),(18,18,1,NULL,NULL,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','fe2fa474-7277-4178-9bd6-5ffd38aa83ae'),(19,19,1,NULL,NULL,1,'2019-02-23 23:38:37','2019-02-23 23:40:35','5c11d51c-f538-4df2-bbe8-46c0365a0470'),(20,20,1,NULL,NULL,1,'2019-02-23 23:39:31','2019-02-23 23:40:45','f3a43708-472e-4469-9355-1497cfdccaba'),(21,21,1,NULL,NULL,1,'2019-02-23 23:39:56','2019-02-23 23:39:56','af063321-cf6f-467e-b5b0-295f103c8404'),(22,22,1,NULL,NULL,1,'2019-02-24 14:18:48','2019-02-24 14:18:48','bcdfda97-1db9-45bf-9c87-94e340d2ab11');
/*!40000 ALTER TABLE `elements_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entries`
--

DROP TABLE IF EXISTS `entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `sectionId` int(11) NOT NULL,
  `typeId` int(11) NOT NULL,
  `authorId` int(11) DEFAULT NULL,
  `postDate` datetime DEFAULT NULL,
  `expiryDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entries_postDate_idx` (`postDate`),
  KEY `entries_expiryDate_idx` (`expiryDate`),
  KEY `entries_authorId_idx` (`authorId`),
  KEY `entries_sectionId_idx` (`sectionId`),
  KEY `entries_typeId_idx` (`typeId`),
  CONSTRAINT `entries_authorId_fk` FOREIGN KEY (`authorId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entries_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entries_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entries_typeId_fk` FOREIGN KEY (`typeId`) REFERENCES `entrytypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entries`
--

LOCK TABLES `entries` WRITE;
/*!40000 ALTER TABLE `entries` DISABLE KEYS */;
INSERT INTO `entries` VALUES (9,1,1,1,'2019-02-21 20:14:00',NULL,'2019-02-21 20:14:28','2019-02-24 14:31:44','29836371-f2d5-4e51-8f7e-67b61eeff7db'),(11,1,1,1,'2019-02-21 20:16:00',NULL,'2019-02-21 20:16:31','2019-02-24 14:31:44','00e27b01-a305-45ed-87e8-880fad12bacb'),(15,1,1,1,'2019-02-21 21:28:00',NULL,'2019-02-21 21:28:43','2019-02-24 14:31:45','25ea20fd-24f3-4c92-9a51-316bb20bf805');
/*!40000 ALTER TABLE `entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entrydrafts`
--

DROP TABLE IF EXISTS `entrydrafts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entrydrafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryId` int(11) NOT NULL,
  `sectionId` int(11) NOT NULL,
  `creatorId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notes` text,
  `data` mediumtext NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entrydrafts_sectionId_idx` (`sectionId`),
  KEY `entrydrafts_entryId_siteId_idx` (`entryId`,`siteId`),
  KEY `entrydrafts_siteId_idx` (`siteId`),
  KEY `entrydrafts_creatorId_idx` (`creatorId`),
  CONSTRAINT `entrydrafts_creatorId_fk` FOREIGN KEY (`creatorId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entrydrafts_entryId_fk` FOREIGN KEY (`entryId`) REFERENCES `entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entrydrafts_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entrydrafts_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entrydrafts`
--

LOCK TABLES `entrydrafts` WRITE;
/*!40000 ALTER TABLE `entrydrafts` DISABLE KEYS */;
/*!40000 ALTER TABLE `entrydrafts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entrytypes`
--

DROP TABLE IF EXISTS `entrytypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entrytypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `hasTitleField` tinyint(1) NOT NULL DEFAULT '1',
  `titleLabel` varchar(255) DEFAULT 'Title',
  `titleFormat` varchar(255) DEFAULT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entrytypes_name_sectionId_unq_idx` (`name`,`sectionId`),
  UNIQUE KEY `entrytypes_handle_sectionId_unq_idx` (`handle`,`sectionId`),
  KEY `entrytypes_sectionId_idx` (`sectionId`),
  KEY `entrytypes_fieldLayoutId_idx` (`fieldLayoutId`),
  CONSTRAINT `entrytypes_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `entrytypes_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entrytypes`
--

LOCK TABLES `entrytypes` WRITE;
/*!40000 ALTER TABLE `entrytypes` DISABLE KEYS */;
INSERT INTO `entrytypes` VALUES (1,1,1,'Blog Post','blogPost',1,'Title',NULL,1,'2019-02-21 19:52:32','2019-02-24 14:31:41','f984f1f4-2168-41f8-b3dc-ce17fe81ab66'),(2,1,13,'Offsite Link','offsiteLink',1,'Title',NULL,2,'2019-03-01 18:56:19','2019-03-01 18:56:19','738f2ef2-7b82-452a-934d-95b637f6a6a3');
/*!40000 ALTER TABLE `entrytypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entryversions`
--

DROP TABLE IF EXISTS `entryversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entryversions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryId` int(11) NOT NULL,
  `sectionId` int(11) NOT NULL,
  `creatorId` int(11) DEFAULT NULL,
  `siteId` int(11) NOT NULL,
  `num` smallint(6) unsigned NOT NULL,
  `notes` text,
  `data` mediumtext NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entryversions_sectionId_idx` (`sectionId`),
  KEY `entryversions_entryId_siteId_idx` (`entryId`,`siteId`),
  KEY `entryversions_siteId_idx` (`siteId`),
  KEY `entryversions_creatorId_idx` (`creatorId`),
  CONSTRAINT `entryversions_creatorId_fk` FOREIGN KEY (`creatorId`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `entryversions_entryId_fk` FOREIGN KEY (`entryId`) REFERENCES `entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entryversions_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entryversions_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entryversions`
--

LOCK TABLES `entryversions` WRITE;
/*!40000 ALTER TABLE `entryversions` DISABLE KEYS */;
INSERT INTO `entryversions` VALUES (1,9,1,1,1,1,'','{\"typeId\":\"1\",\"authorId\":\"1\",\"title\":\"Test Empty Entry\",\"slug\":\"test-empty-entry\",\"postDate\":1550780040,\"expiryDate\":null,\"enabled\":true,\"newParentId\":null,\"fields\":{\"28\":[],\"29\":[],\"3\":[\"valueTwo\"],\"5\":[],\"4\":[],\"7\":\"#ffcc00\",\"11\":\"labelOne\",\"12\":\"\",\"22\":[],\"13\":[],\"14\":[],\"15\":false,\"16\":true,\"17\":[],\"23\":[],\"24\":[],\"32\":[{\"col1\":\"\",\"col2\":\"\",\"col3\":null,\"col4\":\"\"}],\"30\":[],\"33\":\"\",\"34\":[]}}','2019-02-21 20:14:28','2019-02-21 20:14:28','827a3009-2df0-4d32-a3ce-b8a3ae9c43cc'),(2,11,1,1,1,1,'','{\"typeId\":\"1\",\"authorId\":\"1\",\"title\":\"Test Partial Entry\",\"slug\":\"test-partial-entry\",\"postDate\":1550780160,\"expiryDate\":null,\"enabled\":true,\"newParentId\":null,\"fields\":{\"28\":[\"3\"],\"29\":[],\"3\":[\"valueTwo\"],\"5\":[\"3\"],\"4\":[],\"7\":\"#ffcc00\",\"8\":\"2019-02-21 08:00:00\",\"11\":\"labelThree\",\"12\":\"test@email.content\",\"22\":[],\"13\":[\"9\"],\"14\":[],\"15\":false,\"16\":true,\"17\":{\"12\":{\"type\":\"testBlockType\",\"enabled\":true,\"collapsed\":false,\"fields\":{\"testBlockPlainTextField\":\"block plain text content\",\"testBlockCheckboxesField\":[\"optionTwo\"],\"testBlockEntriesField\":[],\"testBlockTableField\":[{\"col1\":\"block column one content\",\"col2\":\"block column two content\",\"col3\":\"\"}]}}},\"23\":[],\"24\":[\"optionThree\"],\"25\":\"1\",\"26\":\"1.23\",\"27\":\"8\",\"1\":\"Test Plain Text Content\",\"32\":[{\"col1\":\"column one content\",\"col2\":\"column two content\",\"col3\":\"#ffcc00\",\"col4\":\"1\"}],\"30\":[\"10\"],\"33\":\"\",\"34\":[]}}','2019-02-21 20:16:31','2019-02-21 20:16:31','e5fb9774-d344-4cb8-bfe1-578d80ca4965'),(3,15,1,1,1,1,'','{\"typeId\":\"1\",\"authorId\":\"1\",\"title\":\"Full Entry\",\"slug\":\"full-entry\",\"postDate\":1550784480,\"expiryDate\":null,\"enabled\":true,\"newParentId\":null,\"fields\":{\"28\":[\"3\"],\"29\":[\"5\",\"6\",\"7\"],\"3\":[\"valueTwo\"],\"5\":[\"2\"],\"4\":[\"labelTwo\"],\"6\":\"#ff0000\",\"7\":\"#ff0000\",\"10\":\"2019-02-21 21:00:00\",\"8\":\"2019-02-21 08:00:00\",\"11\":\"labelTwo\",\"12\":\"test@email.content\",\"22\":[],\"13\":[\"11\",\"9\"],\"14\":[\"11\"],\"15\":true,\"16\":true,\"17\":{\"16\":{\"type\":\"testBlockType\",\"enabled\":true,\"collapsed\":false,\"fields\":{\"testBlockPlainTextField\":\"Matrix Plain Text Field Content\",\"testBlockCheckboxesField\":[\"optionTwo\"],\"testBlockEntriesField\":[\"11\"],\"testBlockTableField\":[{\"col1\":\"Column One Content\",\"col2\":\"Column Two Content\",\"col3\":\"1\"}]}},\"17\":{\"type\":\"testBlockType\",\"enabled\":true,\"collapsed\":false,\"fields\":{\"testBlockPlainTextField\":\"Matrix Second Block Plain Text Field Content\",\"testBlockCheckboxesField\":[\"optionTwo\"],\"testBlockEntriesField\":[\"11\",\"9\"],\"testBlockTableField\":[{\"col1\":\"Block Two Column One Content\",\"col2\":\"Block Two Column Two Content\",\"col3\":\"1\"}]}}},\"23\":{\"18\":{\"type\":\"testEmptyBlock\",\"enabled\":true,\"collapsed\":false,\"fields\":[]}},\"24\":[\"optionTwo\"],\"25\":\"1\",\"26\":\"2.34\",\"27\":\"5\",\"1\":\"Test Plain Text Content\",\"2\":\"Test Plain Text With Character Limit Content\",\"31\":\"optionTwo\",\"32\":[{\"col1\":\"Column One Content\",\"col2\":\"Column Two Content\",\"col3\":\"#ff0000\",\"col4\":\"1\"}],\"30\":[\"13\",\"10\"],\"9\":\"2019-02-21 21:00:00\",\"33\":\"http://www.test-url-field.com\",\"34\":[\"1\"]}}','2019-02-21 21:28:44','2019-02-21 21:28:44','2d6b6699-1c60-4d4d-bf77-2a622ae27c84'),(4,15,1,1,1,2,'','{\"typeId\":\"1\",\"authorId\":\"1\",\"title\":\"Full Entry\",\"slug\":\"full-entry\",\"postDate\":1550784480,\"expiryDate\":null,\"enabled\":true,\"newParentId\":null,\"fields\":{\"35\":[\"22\"],\"28\":[\"3\"],\"29\":[\"5\",\"6\",\"7\"],\"3\":[\"valueTwo\"],\"5\":[\"2\"],\"4\":[\"labelTwo\"],\"6\":\"#ff0000\",\"7\":\"#ff0000\",\"10\":\"2019-02-21 21:00:00\",\"8\":\"2019-02-21 08:00:00\",\"11\":\"labelTwo\",\"12\":\"test@email.content\",\"22\":[],\"13\":[\"11\",\"9\"],\"14\":[\"11\"],\"15\":true,\"16\":true,\"17\":{\"16\":{\"type\":\"testBlockType\",\"enabled\":true,\"collapsed\":false,\"fields\":{\"testBlockPlainTextField\":\"Matrix Plain Text Field Content\",\"testBlockCheckboxesField\":[\"optionTwo\"],\"testBlockEntriesField\":[\"11\"],\"testBlockTableField\":[{\"col1\":\"Column One Content\",\"col2\":\"Column Two Content\",\"col3\":\"1\"}]}},\"17\":{\"type\":\"testBlockType\",\"enabled\":true,\"collapsed\":false,\"fields\":{\"testBlockPlainTextField\":\"Matrix Second Block Plain Text Field Content\",\"testBlockCheckboxesField\":[\"optionTwo\"],\"testBlockEntriesField\":[\"11\",\"9\"],\"testBlockTableField\":[{\"col1\":\"Block Two Column One Content\",\"col2\":\"Block Two Column Two Content\",\"col3\":\"1\"}]}}},\"23\":{\"18\":{\"type\":\"testEmptyBlock\",\"enabled\":true,\"collapsed\":false,\"fields\":[]}},\"24\":[\"optionTwo\"],\"25\":\"1\",\"26\":\"2.34\",\"27\":\"5\",\"1\":\"Test Plain Text Content\",\"2\":\"Test Plain Text With Character Limit Content\",\"31\":\"optionTwo\",\"32\":[{\"col1\":\"Column One Content\",\"col2\":\"Column Two Content\",\"col3\":\"#ff0000\",\"col4\":\"1\"}],\"30\":[\"13\",\"10\"],\"9\":\"2019-02-24 21:00:00\",\"33\":\"http://www.test-url-field.com\",\"34\":[\"1\"]}}','2019-02-24 14:18:57','2019-02-24 14:18:57','cef80141-76e1-4764-8df6-6fe7963954d2');
/*!40000 ALTER TABLE `entryversions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fieldgroups`
--

DROP TABLE IF EXISTS `fieldgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fieldgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fieldgroups_name_unq_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fieldgroups`
--

LOCK TABLES `fieldgroups` WRITE;
/*!40000 ALTER TABLE `fieldgroups` DISABLE KEYS */;
INSERT INTO `fieldgroups` VALUES (1,'Common','2019-02-21 19:51:04','2019-02-21 19:51:04','19f5c7d1-7706-41b2-887d-38a84c518acb');
/*!40000 ALTER TABLE `fieldgroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fieldlayoutfields`
--

DROP TABLE IF EXISTS `fieldlayoutfields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fieldlayoutfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layoutId` int(11) NOT NULL,
  `tabId` int(11) NOT NULL,
  `fieldId` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fieldlayoutfields_layoutId_fieldId_unq_idx` (`layoutId`,`fieldId`),
  KEY `fieldlayoutfields_sortOrder_idx` (`sortOrder`),
  KEY `fieldlayoutfields_tabId_idx` (`tabId`),
  KEY `fieldlayoutfields_fieldId_idx` (`fieldId`),
  CONSTRAINT `fieldlayoutfields_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fieldlayoutfields_layoutId_fk` FOREIGN KEY (`layoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fieldlayoutfields_tabId_fk` FOREIGN KEY (`tabId`) REFERENCES `fieldlayouttabs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fieldlayoutfields`
--

LOCK TABLES `fieldlayoutfields` WRITE;
/*!40000 ALTER TABLE `fieldlayoutfields` DISABLE KEYS */;
INSERT INTO `fieldlayoutfields` VALUES (1,2,1,18,0,1,'2019-02-21 20:01:14','2019-02-21 20:01:14','720d0ddc-571e-4551-ace4-c7caf8603e40'),(2,2,1,19,0,2,'2019-02-21 20:01:14','2019-02-21 20:01:14','836161b0-65b2-49fc-9ff6-b4ed3fdcdfb9'),(3,2,1,20,0,3,'2019-02-21 20:01:14','2019-02-21 20:01:14','e07cf2c1-b1fc-4d5f-a1ca-5168de8c1926'),(4,2,1,21,0,4,'2019-02-21 20:01:14','2019-02-21 20:01:14','35ab8712-04f9-4839-8019-68fccce3e6b2'),(35,9,9,1,0,1,'2019-02-23 23:38:37','2019-02-23 23:38:37','597ee79c-30d7-43c4-a6df-352a698688c8'),(36,9,9,2,0,2,'2019-02-23 23:38:37','2019-02-23 23:38:37','9ea911d4-ec61-4e8d-a022-b1e1c96a4de1'),(37,10,10,1,0,1,'2019-02-23 23:39:31','2019-02-23 23:39:31','665087e1-1b55-4a9c-88c9-022e88d3d4b6'),(38,10,11,11,0,1,'2019-02-23 23:39:31','2019-02-23 23:39:31','350b37b2-5ad3-4ec4-87b0-dc29602f796a'),(39,10,11,3,0,2,'2019-02-23 23:39:31','2019-02-23 23:39:31','b6e213c8-aeae-448d-affb-8ba49bd63013'),(40,12,12,1,0,1,'2019-02-24 00:49:41','2019-02-24 00:49:41','0b54c387-e4d3-4a60-884d-f88ccf526931'),(41,12,12,2,0,2,'2019-02-24 00:49:41','2019-02-24 00:49:41','2a11314a-f0aa-4557-9ea3-b172cb9976a8'),(73,1,20,1,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','3c03d5a7-f064-49a2-8376-0ddb13da4f22'),(74,1,20,2,0,2,'2019-02-24 14:31:41','2019-02-24 14:31:41','62204e80-7baf-47b1-9cbf-bd72dac9e3a7'),(75,1,20,12,0,3,'2019-02-24 14:31:41','2019-02-24 14:31:41','1fb61ed2-985d-4725-98d7-738141626e75'),(76,1,20,25,0,4,'2019-02-24 14:31:41','2019-02-24 14:31:41','f747da57-9007-4628-910e-8fb90d9987e2'),(77,1,20,26,0,5,'2019-02-24 14:31:41','2019-02-24 14:31:41','e959112a-3458-4703-b597-39ce8e176bb4'),(78,1,20,27,0,6,'2019-02-24 14:31:41','2019-02-24 14:31:41','8bb3c676-6f90-4168-9ab6-464a116d0332'),(79,1,20,32,0,7,'2019-02-24 14:31:41','2019-02-24 14:31:41','3dc9ecc4-12eb-4e38-a1a3-c0026fa2a36e'),(80,1,20,33,0,8,'2019-02-24 14:31:41','2019-02-24 14:31:41','541c814a-539f-4a00-9662-7aac305d65fe'),(81,1,21,35,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','dcd597bf-7da6-4937-8b3c-a5bdcc451f68'),(82,1,22,28,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','2cb12218-0c95-43f6-a59a-a4090100a373'),(83,1,22,29,0,2,'2019-02-24 14:31:41','2019-02-24 14:31:41','0b1b9fce-694d-403e-b538-15d20aaa32c3'),(84,1,22,13,0,3,'2019-02-24 14:31:41','2019-02-24 14:31:41','359a8413-eb93-4e31-94f8-673cca2362a2'),(85,1,22,14,0,4,'2019-02-24 14:31:41','2019-02-24 14:31:41','c7411d94-1192-4799-b311-870f4d0f73ff'),(86,1,22,30,0,5,'2019-02-24 14:31:41','2019-02-24 14:31:41','1a7cd19f-c246-4de3-bb8b-b8dae64fcd1f'),(87,1,22,34,0,6,'2019-02-24 14:31:41','2019-02-24 14:31:41','5a354244-5cc7-4932-a8d0-339a35bf3ff7'),(88,1,23,3,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','6e438687-ef3d-45b7-a6ce-689ed42ec870'),(89,1,23,5,0,2,'2019-02-24 14:31:41','2019-02-24 14:31:41','2b344649-ebe3-4c71-afbf-21ff4ad1021c'),(90,1,23,4,0,3,'2019-02-24 14:31:41','2019-02-24 14:31:41','d14ddbaf-470c-411e-85fb-4b7e23e4c015'),(91,1,23,11,0,4,'2019-02-24 14:31:41','2019-02-24 14:31:41','bb80f645-cd8c-4e23-9fb1-1f0870a929c3'),(92,1,23,24,0,5,'2019-02-24 14:31:41','2019-02-24 14:31:41','046a8a28-a0e0-49f6-883f-bd8a7958b1f8'),(93,1,23,31,0,6,'2019-02-24 14:31:41','2019-02-24 14:31:41','ca9b6591-398c-4ba9-8573-a16d1cc5f23b'),(94,1,24,6,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','d08b4143-862c-4211-806a-82fc15b0c6eb'),(95,1,24,7,0,2,'2019-02-24 14:31:41','2019-02-24 14:31:41','677c908d-a7ac-4b05-9e15-30282af5e459'),(96,1,24,15,0,3,'2019-02-24 14:31:41','2019-02-24 14:31:41','e25dfe47-6aa6-426d-a21d-5482e67eec43'),(97,1,24,16,0,4,'2019-02-24 14:31:41','2019-02-24 14:31:41','0bc1e6d5-733f-4a3c-900a-b75b6435d809'),(98,1,25,8,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','aa0ed456-1317-4154-803b-f997696e3314'),(99,1,25,9,0,2,'2019-02-24 14:31:41','2019-02-24 14:31:41','8e9046d5-990b-4c5e-8a00-f3134455738d'),(100,1,25,10,0,3,'2019-02-24 14:31:41','2019-02-24 14:31:41','22e67b3e-278e-433d-8d14-94849e40b1e8'),(101,1,26,17,0,1,'2019-02-24 14:31:41','2019-02-24 14:31:41','10d2a5e6-7bdb-4d63-8ac1-ba4384382e61'),(102,1,26,23,0,2,'2019-02-24 14:31:41','2019-02-24 14:31:41','16a3eee8-2cdb-45c3-b659-e0c6af94e53a'),(103,1,26,22,0,3,'2019-02-24 14:31:41','2019-02-24 14:31:41','2213a6d9-98e2-4f49-982e-cc7e12598f92'),(104,13,27,33,0,1,'2019-03-01 18:56:19','2019-03-01 18:56:19','0acc22bc-95d2-487b-9944-f2ee0fb7e4b1');
/*!40000 ALTER TABLE `fieldlayoutfields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fieldlayouts`
--

DROP TABLE IF EXISTS `fieldlayouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fieldlayouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fieldlayouts_type_idx` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fieldlayouts`
--

LOCK TABLES `fieldlayouts` WRITE;
/*!40000 ALTER TABLE `fieldlayouts` DISABLE KEYS */;
INSERT INTO `fieldlayouts` VALUES (1,'craft\\elements\\Entry','2019-02-21 19:52:32','2019-02-24 14:31:41','15a8e2e4-94b5-45a8-91b1-f121a68c4cb7'),(2,'craft\\elements\\MatrixBlock','2019-02-21 20:01:14','2019-02-21 20:01:14','874abbf5-9b6a-44ce-9f0a-0fe1861839d8'),(3,'craft\\elements\\MatrixBlock','2019-02-21 20:02:19','2019-02-21 20:02:19','dfa6a3c8-3d09-4a3f-acc9-d60b923749f5'),(4,'craft\\elements\\Tag','2019-02-21 20:02:38','2019-02-21 20:02:38','c7a222f6-c2be-4beb-9717-d14c7944344a'),(5,'craft\\elements\\Tag','2019-02-21 20:02:49','2019-02-21 20:02:49','e7fd06c1-4d8c-4a97-962d-ba93aeba8cac'),(6,'craft\\elements\\Category','2019-02-21 20:03:17','2019-02-21 20:03:17','ffa0f547-94f8-412b-a3fb-5820f1fb3656'),(7,'craft\\elements\\Category','2019-02-21 20:03:45','2019-02-21 20:03:45','8702f817-acc6-413e-8206-35b372d635d7'),(8,'craft\\elements\\Category','2019-02-21 20:06:08','2019-02-21 20:06:08','4cd8badd-b413-46cf-8fc3-bc6635cc4650'),(9,'craft\\elements\\GlobalSet','2019-02-23 23:38:37','2019-02-23 23:38:37','4e2ed69d-a430-4e08-8d0c-f27c10502f31'),(10,'craft\\elements\\GlobalSet','2019-02-23 23:39:31','2019-02-23 23:39:31','694a4f38-9138-4e83-a5c7-45fbe2e2795e'),(11,'craft\\elements\\GlobalSet','2019-02-23 23:39:56','2019-02-23 23:39:56','bc0d1333-1a0e-4e92-9c79-0ca1beb18e92'),(12,'craft\\elements\\Asset','2019-02-24 00:49:41','2019-02-24 00:49:41','5ab0c2bc-5472-479d-ab5b-da247e57fa3c'),(13,'craft\\elements\\Entry','2019-03-01 18:56:19','2019-03-01 18:56:19','fd905129-87ee-40b4-9896-63ce85777930');
/*!40000 ALTER TABLE `fieldlayouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fieldlayouttabs`
--

DROP TABLE IF EXISTS `fieldlayouttabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fieldlayouttabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layoutId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fieldlayouttabs_sortOrder_idx` (`sortOrder`),
  KEY `fieldlayouttabs_layoutId_idx` (`layoutId`),
  CONSTRAINT `fieldlayouttabs_layoutId_fk` FOREIGN KEY (`layoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fieldlayouttabs`
--

LOCK TABLES `fieldlayouttabs` WRITE;
/*!40000 ALTER TABLE `fieldlayouttabs` DISABLE KEYS */;
INSERT INTO `fieldlayouttabs` VALUES (1,2,'Content',1,'2019-02-21 20:01:14','2019-02-21 20:01:14','1b7ff8ee-21c4-443f-a92d-14c7783b185f'),(2,3,'Content',1,'2019-02-21 20:02:19','2019-02-21 20:02:19','593d3903-acfb-489d-90c1-c63a50162e28'),(9,9,'Content',1,'2019-02-23 23:38:37','2019-02-23 23:38:37','ea2a9969-9f87-4754-9762-f47747abd785'),(10,10,'Content',1,'2019-02-23 23:39:31','2019-02-23 23:39:31','31ae96d7-4c6b-46cc-bbdf-3601e1074395'),(11,10,'Selection',2,'2019-02-23 23:39:31','2019-02-23 23:39:31','8f03fb1d-3f31-4b70-802e-bb16cf35cb12'),(12,12,'Content',1,'2019-02-24 00:49:41','2019-02-24 00:49:41','c6d21968-ca39-4a31-a430-44cd615a93d6'),(20,1,'Text',1,'2019-02-24 14:31:41','2019-02-24 14:31:41','81b06d9d-ed22-48d2-87fb-022fd679c90f'),(21,1,'Assets',2,'2019-02-24 14:31:41','2019-02-24 14:31:41','ed86c325-d10d-49c9-bb8a-1277f71c457d'),(22,1,'Relationships',3,'2019-02-24 14:31:41','2019-02-24 14:31:41','448cdfb4-e063-485d-b6ce-f518c18d259c'),(23,1,'Selections',4,'2019-02-24 14:31:41','2019-02-24 14:31:41','793515be-a8cc-41d6-ba0f-f1579813def9'),(24,1,'Misc',5,'2019-02-24 14:31:41','2019-02-24 14:31:41','de33955f-d71c-4430-9af1-4a78de721042'),(25,1,'Date/Time',6,'2019-02-24 14:31:41','2019-02-24 14:31:41','35766cd5-1734-4d87-810c-d24882d218ee'),(26,1,'Matrix',7,'2019-02-24 14:31:41','2019-02-24 14:31:41','3832dd11-84b0-41ab-aeb5-1c01f93a0e05'),(27,13,'Content',1,'2019-03-01 18:56:19','2019-03-01 18:56:19','65cc3668-a980-4190-af80-da401228863a');
/*!40000 ALTER TABLE `fieldlayouttabs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fields`
--

DROP TABLE IF EXISTS `fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(64) NOT NULL,
  `context` varchar(255) NOT NULL DEFAULT 'global',
  `instructions` text,
  `translationMethod` varchar(255) NOT NULL DEFAULT 'none',
  `translationKeyFormat` text,
  `type` varchar(255) NOT NULL,
  `settings` text,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fields_handle_context_unq_idx` (`handle`,`context`),
  KEY `fields_groupId_idx` (`groupId`),
  KEY `fields_context_idx` (`context`),
  CONSTRAINT `fields_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `fieldgroups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fields`
--

LOCK TABLES `fields` WRITE;
/*!40000 ALTER TABLE `fields` DISABLE KEYS */;
INSERT INTO `fields` VALUES (1,1,'Test Plain Text','testPlainText','global','','none',NULL,'craft\\fields\\PlainText','{\"placeholder\":\"\",\"code\":\"\",\"multiline\":\"\",\"initialRows\":\"4\",\"charLimit\":\"\",\"columnType\":\"text\"}','2019-02-21 19:53:05','2019-02-21 19:53:05','5aaf52ef-487b-4f02-8af4-9ce345501d6e'),(2,1,'Test Plain Text With Character Limit','testPlainTextWithCharacterLimit','global','','none',NULL,'craft\\fields\\PlainText','{\"placeholder\":\"\",\"code\":\"\",\"multiline\":\"\",\"initialRows\":\"4\",\"charLimit\":\"50\",\"columnType\":\"text\"}','2019-02-21 19:53:31','2019-02-21 19:53:31','cc77cd2a-6a30-4533-85da-f8235ef5afba'),(3,1,'Test Checkboxes','testCheckboxes','global','','none',NULL,'craft\\fields\\Checkboxes','{\"options\":[{\"label\":\"Label One\",\"value\":\"valueOne\",\"default\":\"\"},{\"label\":\"Label Two\",\"value\":\"valueTwo\",\"default\":\"1\"},{\"label\":\"Label Three\",\"value\":\"valueThree\",\"default\":\"\"}]}','2019-02-21 19:54:14','2019-02-21 19:54:14','2ed10b3e-893f-4438-965c-961dfe43a01b'),(4,1,'Test Checkboxes With One Bad Value','testCheckboxesWithOneBadValue','global','','none',NULL,'craft\\fields\\Checkboxes','{\"options\":[{\"label\":\"Select\",\"value\":\"...\",\"default\":\"\"},{\"label\":\"Label One\",\"value\":\"labelOne\",\"default\":\"\"},{\"label\":\"Label Two\",\"value\":\"labelTwo\",\"default\":\"\"},{\"label\":\"Label Three\",\"value\":\"labelThree\",\"default\":\"\"}]}','2019-02-21 19:54:50','2019-02-21 19:54:50','cb89ff59-09f5-4374-9d4d-04ec66a86126'),(5,1,'Test Checkboxes With Numeric Value','testCheckboxesWithNumericValue','global','','none',NULL,'craft\\fields\\Checkboxes','{\"options\":[{\"label\":\"Option One\",\"value\":\"1\",\"default\":\"\"},{\"label\":\"Option Two\",\"value\":\"2\",\"default\":\"\"},{\"label\":\"Option Three\",\"value\":\"3\",\"default\":\"\"}]}','2019-02-21 19:55:24','2019-02-21 19:55:24','23691227-3f6c-49a2-808f-11e32479ed23'),(6,1,'Test Color Field','testColorField','global','','none',NULL,'craft\\fields\\Color','{\"defaultColor\":\"\"}','2019-02-21 19:55:39','2019-02-21 19:55:39','63c61906-8059-4ea2-9899-c0cb9b80a068'),(7,1,'Test Color Field With Default Value','testColorFieldWithDefaultValue','global','','none',NULL,'craft\\fields\\Color','{\"defaultColor\":\"#ffcc00\"}','2019-02-21 19:56:02','2019-02-21 19:56:02','f3d62d80-3c4f-4746-a4b4-5aaf12c75fdf'),(8,1,'Test Date Field','testDateField','global','','none',NULL,'craft\\fields\\Date','{\"showDate\":true,\"showTime\":false,\"minuteIncrement\":\"30\"}','2019-02-21 19:56:21','2019-02-21 19:56:35','f81411a6-e225-4bb7-80d1-8228c154f882'),(9,1,'Test Time Field','testTimeField','global','','none',NULL,'craft\\fields\\Date','{\"showDate\":false,\"showTime\":true,\"minuteIncrement\":\"30\"}','2019-02-21 19:56:49','2019-02-21 19:56:49','ed6867f2-98d9-4b90-90fa-850b9e9c4b20'),(10,1,'Test Date And Time Field','testDateAndTimeField','global','','none',NULL,'craft\\fields\\Date','{\"showDate\":true,\"showTime\":true,\"minuteIncrement\":\"15\"}','2019-02-21 19:57:02','2019-02-21 19:57:02','39b499c8-d19e-4801-b7fb-ee8c48f87b49'),(11,1,'Test Dropdown Field','testDropdownField','global','','none',NULL,'craft\\fields\\Dropdown','{\"options\":[{\"label\":\"Label One\",\"value\":\"labelOne\",\"default\":\"\"},{\"label\":\"Label Two\",\"value\":\"labelTwo\",\"default\":\"\"},{\"label\":\"Label Three\",\"value\":\"labelThree\",\"default\":\"\"}]}','2019-02-21 19:57:32','2019-02-21 19:57:32','e07fdd73-250d-483b-afe9-f8a7b7883d32'),(12,1,'Test Email Field','testEmailField','global','','none',NULL,'craft\\fields\\Email','{\"placeholder\":\"\"}','2019-02-21 19:57:52','2019-02-21 19:57:52','7953faa3-aa42-438d-9d3c-bbb38f12e6e9'),(13,1,'Test Entries Field','testEntriesField','global','','site',NULL,'craft\\fields\\Entries','{\"sources\":\"*\",\"source\":null,\"targetSiteId\":null,\"viewMode\":null,\"limit\":\"\",\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 19:58:10','2019-02-21 19:58:10','a29e1098-307f-4f9f-9857-0537ff91ee1f'),(14,1,'Test Entries Field With Limit','testEntriesFieldWithLimit','global','','site',NULL,'craft\\fields\\Entries','{\"sources\":\"*\",\"source\":null,\"targetSiteId\":null,\"viewMode\":null,\"limit\":\"1\",\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 19:58:38','2019-02-21 20:14:10','8d46a9d0-7024-4438-a0cd-3bac9934ff1c'),(15,1,'Test Lightswitch Field','testLightswitchField','global','','none',NULL,'craft\\fields\\Lightswitch','{\"default\":\"\"}','2019-02-21 19:59:14','2019-02-21 19:59:14','a3a6be3f-af7c-4d5f-8c2b-a93d73b41e23'),(16,1,'Test Lightswitch On Field','testLightswitchOnField','global','','none',NULL,'craft\\fields\\Lightswitch','{\"default\":\"1\"}','2019-02-21 19:59:28','2019-02-21 19:59:28','41da93ff-386e-4fb1-80ed-14d58f44acd6'),(17,1,'Test Matrix Field','testMatrixField','global','','site',NULL,'craft\\fields\\Matrix','{\"minBlocks\":\"\",\"maxBlocks\":\"\",\"contentTable\":\"{{%matrixcontent_testmatrixfield}}\",\"localizeBlocks\":false}','2019-02-21 20:01:14','2019-02-21 20:01:14','2c3f1154-f5e1-44a2-990c-5a85c8370981'),(18,NULL,'Test Block Plain Text Field','testBlockPlainTextField','matrixBlockType:1','','none',NULL,'craft\\fields\\PlainText','{\"placeholder\":\"\",\"code\":\"\",\"multiline\":\"\",\"initialRows\":\"4\",\"charLimit\":\"\",\"columnType\":\"text\"}','2019-02-21 20:01:14','2019-02-21 20:01:14','2fcdef04-1ecc-4bc2-a6f9-fb0f24c02b02'),(19,NULL,'Test Block Checkboxes Field','testBlockCheckboxesField','matrixBlockType:1','','none',NULL,'craft\\fields\\Checkboxes','{\"options\":[{\"label\":\"Option One\",\"value\":\"optionOne\",\"default\":\"\"},{\"label\":\"Option Two\",\"value\":\"optionTwo\",\"default\":\"\"},{\"label\":\"Option Three\",\"value\":\"optionThree\",\"default\":\"\"}]}','2019-02-21 20:01:14','2019-02-21 20:01:14','15a1ac0e-5d2e-4ed7-8648-eff1d901b161'),(20,NULL,'Test Block Entries Field','testBlockEntriesField','matrixBlockType:1','','site',NULL,'craft\\fields\\Entries','{\"sources\":\"*\",\"source\":null,\"targetSiteId\":null,\"viewMode\":null,\"limit\":\"\",\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 20:01:14','2019-02-21 20:01:14','ae61af7f-5b09-4233-88d7-aa26c8382f51'),(21,NULL,'Test Block Table Field','testBlockTableField','matrixBlockType:1','','none',NULL,'craft\\fields\\Table','{\"addRowLabel\":\"Add a row\",\"maxRows\":\"\",\"minRows\":\"\",\"columns\":{\"col1\":{\"heading\":\"Column One\",\"handle\":\"columnOne\",\"width\":\"\",\"type\":\"singleline\"},\"col2\":{\"heading\":\"Column Two\",\"handle\":\"columnTwo\",\"width\":\"\",\"type\":\"multiline\"},\"col3\":{\"heading\":\"Column Three\",\"handle\":\"columnThree\",\"width\":\"\",\"type\":\"lightswitch\"}},\"defaults\":{\"row1\":{\"col1\":\"\",\"col2\":\"\",\"col3\":\"\"}},\"columnType\":\"text\"}','2019-02-21 20:01:14','2019-02-21 20:01:14','8c7ece18-717f-4d24-bf06-b5b6927f380a'),(22,1,'Test Empty Matrix Field','testEmptyMatrixField','global','','site',NULL,'craft\\fields\\Matrix','{\"minBlocks\":\"\",\"maxBlocks\":\"\",\"contentTable\":\"{{%matrixcontent_testemptymatrixfield}}\",\"localizeBlocks\":false}','2019-02-21 20:01:30','2019-02-21 20:01:30','2c744dc3-4265-47ba-bf58-cde4f09e4f44'),(23,1,'Test Matrix Field With Empty Block','testMatrixFieldWithEmptyBlock','global','','site',NULL,'craft\\fields\\Matrix','{\"minBlocks\":\"\",\"maxBlocks\":\"\",\"contentTable\":\"{{%matrixcontent_testmatrixfieldwithemptyblock}}\",\"localizeBlocks\":false}','2019-02-21 20:01:52','2019-02-21 20:02:19','1d095f1d-0de5-40cc-9073-e12d4779c85b'),(24,1,'Test Multi-select Field','testMultiSelectField','global','','none',NULL,'craft\\fields\\MultiSelect','{\"options\":[{\"label\":\"Option One\",\"value\":\"optionOne\",\"default\":\"\"},{\"label\":\"Option Two\",\"value\":\"optionTwo\",\"default\":\"\"},{\"label\":\"Option Three\",\"value\":\"optionThree\",\"default\":\"\"}]}','2019-02-21 20:03:37','2019-02-21 20:03:37','8cde2b19-23ea-4739-9c06-ab5c5d62297d'),(25,1,'Test Number Field','testNumberField','global','','none',NULL,'craft\\fields\\Number','{\"defaultValue\":null,\"min\":\"0\",\"max\":null,\"decimals\":0,\"size\":null}','2019-02-21 20:04:39','2019-02-21 20:04:39','1a6cb9aa-ba35-48c3-8d72-0cc084244dc1'),(26,1,'Test Number Float Field','testNumberFloatField','global','','none',NULL,'craft\\fields\\Number','{\"defaultValue\":null,\"min\":\"0\",\"max\":null,\"decimals\":\"2\",\"size\":null}','2019-02-21 20:05:06','2019-02-21 20:05:06','0ead4096-0a99-49fd-969a-3c35d725c15e'),(27,1,'Test Number Max Field','testNumberMaxField','global','','none',NULL,'craft\\fields\\Number','{\"defaultValue\":null,\"min\":\"0\",\"max\":\"10\",\"decimals\":0,\"size\":null}','2019-02-21 20:05:20','2019-02-21 20:05:20','92bcf619-46a2-41ca-90c6-349ea3548b65'),(28,1,'Test Category Field','testCategoryField','global','','site',NULL,'craft\\fields\\Categories','{\"branchLimit\":\"\",\"sources\":\"*\",\"source\":\"group:1\",\"targetSiteId\":null,\"viewMode\":null,\"limit\":null,\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 20:05:36','2019-02-21 20:05:36','a900079b-4697-4d92-81b1-a1f38e009cfb'),(29,1,'Test Category Nesting Field','testCategoryNestingField','global','','site',NULL,'craft\\fields\\Categories','{\"branchLimit\":\"2\",\"sources\":\"*\",\"source\":\"group:3\",\"targetSiteId\":null,\"viewMode\":null,\"limit\":null,\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 20:08:38','2019-02-21 20:08:38','59478d10-851d-42bf-b237-23b143618594'),(30,1,'Test Tag Field','testTagField','global','','site',NULL,'craft\\fields\\Tags','{\"sources\":\"*\",\"source\":\"taggroup:1\",\"targetSiteId\":null,\"viewMode\":null,\"limit\":null,\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 20:08:59','2019-02-21 20:08:59','606a7c21-4ca3-4ca4-bcba-fe38f88fa5e5'),(31,1,'Test Radio Button Field','testRadioButtonField','global','','none',NULL,'craft\\fields\\RadioButtons','{\"options\":[{\"label\":\"Option One\",\"value\":\"optionOne\",\"default\":\"\"},{\"label\":\"Option Two\",\"value\":\"optionTwo\",\"default\":\"\"},{\"label\":\"Option Three\",\"value\":\"optionThree\",\"default\":\"\"}]}','2019-02-21 20:09:22','2019-02-21 20:09:22','0663d8a9-3aee-4bd7-8953-303fa3abbb09'),(32,1,'Test Table Field','testTableField','global','','none',NULL,'craft\\fields\\Table','{\"addRowLabel\":\"Add a row\",\"maxRows\":\"\",\"minRows\":\"\",\"columns\":{\"col1\":{\"heading\":\"Column One\",\"handle\":\"columnOne\",\"width\":\"\",\"type\":\"singleline\"},\"col2\":{\"heading\":\"Column Two\",\"handle\":\"columnTwo\",\"width\":\"\",\"type\":\"multiline\"},\"col3\":{\"heading\":\"Column Three\",\"handle\":\"columnThree\",\"width\":\"\",\"type\":\"color\"},\"col4\":{\"heading\":\"Column Four\",\"handle\":\"columnFour\",\"width\":\"\",\"type\":\"lightswitch\"}},\"defaults\":{\"row1\":{\"col1\":\"\",\"col2\":\"\",\"col3\":\"\",\"col4\":\"\"}},\"columnType\":\"text\"}','2019-02-21 20:10:02','2019-02-21 20:14:04','7729d71d-9eab-4d6f-982f-e45e92a49d58'),(33,1,'Test URL Field','testUrlField','global','','none',NULL,'craft\\fields\\Url','{\"placeholder\":\"\"}','2019-02-21 20:10:53','2019-02-21 20:10:53','248d87b2-5529-460c-ac02-5812d4e7e9c5'),(34,1,'Test Users Field','testUsersField','global','','site',NULL,'craft\\fields\\Users','{\"sources\":\"*\",\"source\":null,\"targetSiteId\":null,\"viewMode\":null,\"limit\":\"\",\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-21 20:11:05','2019-02-21 20:11:05','5fb4ebd7-1e98-403a-a4b4-d680c00613f0'),(35,1,'Test Assets Field','testAssetsField','global','','site',NULL,'craft\\fields\\Assets','{\"useSingleFolder\":\"\",\"defaultUploadLocationSource\":\"folder:1\",\"defaultUploadLocationSubpath\":\"\",\"singleUploadLocationSource\":\"folder:1\",\"singleUploadLocationSubpath\":\"\",\"restrictFiles\":\"\",\"allowedKinds\":null,\"sources\":\"*\",\"source\":null,\"targetSiteId\":null,\"viewMode\":\"list\",\"limit\":\"\",\"selectionLabel\":\"\",\"localizeRelations\":false}','2019-02-24 14:17:35','2019-02-24 14:17:35','0d1440f6-b283-4aba-b91a-df1db0b02563');
/*!40000 ALTER TABLE `fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `globalsets`
--

DROP TABLE IF EXISTS `globalsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `globalsets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `globalsets_name_unq_idx` (`name`),
  UNIQUE KEY `globalsets_handle_unq_idx` (`handle`),
  KEY `globalsets_fieldLayoutId_idx` (`fieldLayoutId`),
  CONSTRAINT `globalsets_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `globalsets_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `globalsets`
--

LOCK TABLES `globalsets` WRITE;
/*!40000 ALTER TABLE `globalsets` DISABLE KEYS */;
INSERT INTO `globalsets` VALUES (19,'Test Global Set','testGlobalSet',9,'2019-02-23 23:38:37','2019-02-23 23:38:37','d51a7505-8c1e-4823-ae55-01b1c350c4e9'),(20,'Test Second Global Set','testSecondGlobalSet',10,'2019-02-23 23:39:31','2019-02-23 23:39:31','aaa86a3f-8c2f-4eaf-869f-e40e80421c32'),(21,'Test Empty Global Set','testEmptyGlobalSet',11,'2019-02-23 23:39:56','2019-02-23 23:39:56','01ba37dc-1c50-4a57-b1af-0a2fff6b563d');
/*!40000 ALTER TABLE `globalsets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL,
  `schemaVersion` varchar(15) NOT NULL,
  `edition` tinyint(3) unsigned NOT NULL,
  `timezone` varchar(30) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `on` tinyint(1) NOT NULL DEFAULT '0',
  `maintenance` tinyint(1) NOT NULL DEFAULT '0',
  `fieldVersion` char(12) NOT NULL DEFAULT '000000000000',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info`
--

LOCK TABLES `info` WRITE;
/*!40000 ALTER TABLE `info` DISABLE KEYS */;
INSERT INTO `info` VALUES (1,'3.0.41','3.0.94',0,'America/Los_Angeles','Test Site',1,0,'E2UtdFiMhzx1','2019-02-21 19:51:04','2019-03-01 18:55:08','14ab1133-efb5-4eac-bcea-1b236829ba8d');
/*!40000 ALTER TABLE `info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matrixblocks`
--

DROP TABLE IF EXISTS `matrixblocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixblocks` (
  `id` int(11) NOT NULL,
  `ownerId` int(11) NOT NULL,
  `ownerSiteId` int(11) DEFAULT NULL,
  `fieldId` int(11) NOT NULL,
  `typeId` int(11) NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `matrixblocks_ownerId_idx` (`ownerId`),
  KEY `matrixblocks_fieldId_idx` (`fieldId`),
  KEY `matrixblocks_typeId_idx` (`typeId`),
  KEY `matrixblocks_sortOrder_idx` (`sortOrder`),
  KEY `matrixblocks_ownerSiteId_idx` (`ownerSiteId`),
  CONSTRAINT `matrixblocks_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixblocks_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixblocks_ownerId_fk` FOREIGN KEY (`ownerId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixblocks_ownerSiteId_fk` FOREIGN KEY (`ownerSiteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matrixblocks_typeId_fk` FOREIGN KEY (`typeId`) REFERENCES `matrixblocktypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrixblocks`
--

LOCK TABLES `matrixblocks` WRITE;
/*!40000 ALTER TABLE `matrixblocks` DISABLE KEYS */;
INSERT INTO `matrixblocks` VALUES (12,11,NULL,17,1,1,'2019-02-21 20:16:31','2019-02-24 14:31:45','9e028740-a097-4233-9867-78059e2d94b7'),(16,15,NULL,17,1,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','368f2405-e01a-4a79-893a-40c40044bfc3'),(17,15,NULL,17,1,2,'2019-02-21 21:28:43','2019-02-24 14:31:46','72615c5c-8197-4544-8762-39bb760c8f36'),(18,15,NULL,23,2,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','1973802f-965b-4f34-a168-582e3a9b2693');
/*!40000 ALTER TABLE `matrixblocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matrixblocktypes`
--

DROP TABLE IF EXISTS `matrixblocktypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixblocktypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matrixblocktypes_name_fieldId_unq_idx` (`name`,`fieldId`),
  UNIQUE KEY `matrixblocktypes_handle_fieldId_unq_idx` (`handle`,`fieldId`),
  KEY `matrixblocktypes_fieldId_idx` (`fieldId`),
  KEY `matrixblocktypes_fieldLayoutId_idx` (`fieldLayoutId`),
  CONSTRAINT `matrixblocktypes_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixblocktypes_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrixblocktypes`
--

LOCK TABLES `matrixblocktypes` WRITE;
/*!40000 ALTER TABLE `matrixblocktypes` DISABLE KEYS */;
INSERT INTO `matrixblocktypes` VALUES (1,17,2,'Test Block Type','testBlockType',1,'2019-02-21 20:01:14','2019-02-21 20:01:14','e11c63c8-2716-4e61-99b2-dc6ca8682351'),(2,23,3,'Test Empty Block','testEmptyBlock',1,'2019-02-21 20:02:19','2019-02-21 20:02:19','d84e0d70-ece3-4468-9acb-9a5526f95c90');
/*!40000 ALTER TABLE `matrixblocktypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matrixcontent_testemptymatrixfield`
--

DROP TABLE IF EXISTS `matrixcontent_testemptymatrixfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixcontent_testemptymatrixfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matrixcontent_testemptymatrixfield_elementId_siteId_unq_idx` (`elementId`,`siteId`),
  KEY `matrixcontent_testemptymatrixfield_siteId_fk` (`siteId`),
  CONSTRAINT `matrixcontent_testemptymatrixfield_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixcontent_testemptymatrixfield_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrixcontent_testemptymatrixfield`
--

LOCK TABLES `matrixcontent_testemptymatrixfield` WRITE;
/*!40000 ALTER TABLE `matrixcontent_testemptymatrixfield` DISABLE KEYS */;
/*!40000 ALTER TABLE `matrixcontent_testemptymatrixfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matrixcontent_testmatrixfield`
--

DROP TABLE IF EXISTS `matrixcontent_testmatrixfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixcontent_testmatrixfield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  `field_testBlockType_testBlockPlainTextField` text,
  `field_testBlockType_testBlockCheckboxesField` varchar(255) DEFAULT NULL,
  `field_testBlockType_testBlockTableField` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matrixcontent_testmatrixfield_elementId_siteId_unq_idx` (`elementId`,`siteId`),
  KEY `matrixcontent_testmatrixfield_siteId_fk` (`siteId`),
  CONSTRAINT `matrixcontent_testmatrixfield_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixcontent_testmatrixfield_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrixcontent_testmatrixfield`
--

LOCK TABLES `matrixcontent_testmatrixfield` WRITE;
/*!40000 ALTER TABLE `matrixcontent_testmatrixfield` DISABLE KEYS */;
INSERT INTO `matrixcontent_testmatrixfield` VALUES (1,12,1,'2019-02-21 20:16:31','2019-02-24 14:31:45','be9e78dd-2022-4eac-b039-27cc73fe2a24','block plain text content','[\"optionTwo\"]','[{\"col1\":\"block column one content\",\"col2\":\"block column two content\",\"col3\":\"\"}]'),(2,16,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','c12bddb5-26a9-4ff3-86fe-3fb60a55100e','Matrix Plain Text Field Content','[\"optionTwo\"]','[{\"col1\":\"Column One Content\",\"col2\":\"Column Two Content\",\"col3\":\"1\"}]'),(3,17,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','2542ecd5-3dc9-47f3-bb65-870228bb9270','Matrix Second Block Plain Text Field Content','[\"optionTwo\"]','[{\"col1\":\"Block Two Column One Content\",\"col2\":\"Block Two Column Two Content\",\"col3\":\"1\"}]');
/*!40000 ALTER TABLE `matrixcontent_testmatrixfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matrixcontent_testmatrixfieldwithemptyblock`
--

DROP TABLE IF EXISTS `matrixcontent_testmatrixfieldwithemptyblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matrixcontent_testmatrixfieldwithemptyblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `elementId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matrixconten_testmatrixfieldwithemptyblo_elementI_siteId_unq_idx` (`elementId`,`siteId`),
  KEY `matrixcontent_testmatrixfieldwithemptyblock_siteId_fk` (`siteId`),
  CONSTRAINT `matrixcontent_testmatrixfieldwithemptyblock_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matrixcontent_testmatrixfieldwithemptyblock_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrixcontent_testmatrixfieldwithemptyblock`
--

LOCK TABLES `matrixcontent_testmatrixfieldwithemptyblock` WRITE;
/*!40000 ALTER TABLE `matrixcontent_testmatrixfieldwithemptyblock` DISABLE KEYS */;
INSERT INTO `matrixcontent_testmatrixfieldwithemptyblock` VALUES (1,18,1,'2019-02-21 21:28:43','2019-02-24 14:31:46','91385fe9-aef7-4539-9508-b15be16520e7');
/*!40000 ALTER TABLE `matrixcontent_testmatrixfieldwithemptyblock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pluginId` int(11) DEFAULT NULL,
  `type` enum('app','plugin','content') NOT NULL DEFAULT 'app',
  `name` varchar(255) NOT NULL,
  `applyTime` datetime NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `migrations_pluginId_idx` (`pluginId`),
  KEY `migrations_type_pluginId_idx` (`type`,`pluginId`),
  CONSTRAINT `migrations_pluginId_fk` FOREIGN KEY (`pluginId`) REFERENCES `plugins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,NULL,'app','Install','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','08f4b9a4-282f-442b-bd95-bbcec1a1f8e8'),(2,NULL,'app','m150403_183908_migrations_table_changes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','55d83e39-ed75-4b47-afd1-820a3d43138a'),(3,NULL,'app','m150403_184247_plugins_table_changes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','099b8339-b938-4b9d-b1ac-005063b204d0'),(4,NULL,'app','m150403_184533_field_version','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','bbb10fee-3c78-469e-8356-246b540f371a'),(5,NULL,'app','m150403_184729_type_columns','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','f11d7a0f-555d-41aa-9d5c-c6732b4aa3f7'),(6,NULL,'app','m150403_185142_volumes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','c509592e-87c1-47ba-aedc-07a0e99eb712'),(7,NULL,'app','m150428_231346_userpreferences','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','fe43ee78-80b7-4102-b423-4e1ea2b4342b'),(8,NULL,'app','m150519_150900_fieldversion_conversion','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','6fb05c1b-9635-46a4-abe5-dbd75f04fe05'),(9,NULL,'app','m150617_213829_update_email_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5738abc2-43e4-44d5-880c-af62174801a2'),(10,NULL,'app','m150721_124739_templatecachequeries','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','db9ca009-4acb-447f-ab4c-f8082a989c3c'),(11,NULL,'app','m150724_140822_adjust_quality_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','c6128dd2-51af-4d30-8e07-b9631eab57f8'),(12,NULL,'app','m150815_133521_last_login_attempt_ip','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','f79c1938-2814-40c5-9f9c-db60cfbfe287'),(13,NULL,'app','m151002_095935_volume_cache_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5da690c1-e8f5-4087-8f01-8cfa059af893'),(14,NULL,'app','m151005_142750_volume_s3_storage_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','a166416e-f469-4746-99a9-fa53b03d23ea'),(15,NULL,'app','m151016_133600_delete_asset_thumbnails','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','911a68fa-5502-488e-ad31-898d2a41f9c5'),(16,NULL,'app','m151209_000000_move_logo','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','6e4a0a65-c34d-4ad0-9526-fa4581bbbbed'),(17,NULL,'app','m151211_000000_rename_fileId_to_assetId','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','44a55518-b132-4bb2-af7b-7c9d21e7bfe3'),(18,NULL,'app','m151215_000000_rename_asset_permissions','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','e0ae6882-55ae-45c3-81db-0605b9839f9e'),(19,NULL,'app','m160707_000001_rename_richtext_assetsource_setting','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','35011ded-d8a3-4e45-a163-857004b641af'),(20,NULL,'app','m160708_185142_volume_hasUrls_setting','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','73014a2a-3484-47c1-b9e0-bd2d599617f7'),(21,NULL,'app','m160714_000000_increase_max_asset_filesize','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5fb264e2-04bf-4710-808f-d0c9e92a0693'),(22,NULL,'app','m160727_194637_column_cleanup','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','f02d7da0-3ce1-4c81-be92-38bfc983017a'),(23,NULL,'app','m160804_110002_userphotos_to_assets','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','4b30e5eb-b227-48dd-a260-20896e2c7411'),(24,NULL,'app','m160807_144858_sites','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','84787312-35a1-4735-9c2c-c8b02cd5a634'),(25,NULL,'app','m160829_000000_pending_user_content_cleanup','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','c9832459-8808-4762-a5f1-b7f20fa5567f'),(26,NULL,'app','m160830_000000_asset_index_uri_increase','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','4b2c38fe-d423-4229-836b-0b32da9bf300'),(27,NULL,'app','m160912_230520_require_entry_type_id','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','d0db305f-98c3-4665-8fb8-0d55501b2f8b'),(28,NULL,'app','m160913_134730_require_matrix_block_type_id','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','37b45f3b-9d80-4b0a-a02e-80fdcd88f8c4'),(29,NULL,'app','m160920_174553_matrixblocks_owner_site_id_nullable','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','f8b708c3-e92f-43f2-b1b2-1a53d89c9b47'),(30,NULL,'app','m160920_231045_usergroup_handle_title_unique','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','34017589-cc15-4ad3-844b-5be4258db334'),(31,NULL,'app','m160925_113941_route_uri_parts','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','58de6281-0f65-4855-b1a5-7a76db0da722'),(32,NULL,'app','m161006_205918_schemaVersion_not_null','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','06d3345e-89b4-4b30-aae4-a7656e8d19c8'),(33,NULL,'app','m161007_130653_update_email_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','829cd985-4e31-4aee-8363-ce80ba0c73b8'),(34,NULL,'app','m161013_175052_newParentId','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','b0bcdd23-7f0b-4e89-875a-25b6959b0a63'),(35,NULL,'app','m161021_102916_fix_recent_entries_widgets','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','d2855dcb-0fb9-4d9f-bbbd-d8a801958ba1'),(36,NULL,'app','m161021_182140_rename_get_help_widget','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','b3b56b4b-a5ac-444e-8ba4-49ac91ca6f4b'),(37,NULL,'app','m161025_000000_fix_char_columns','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','43dfad34-8509-40b6-869d-4dc60aed06b8'),(38,NULL,'app','m161029_124145_email_message_languages','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','1837478a-0072-4797-9336-593fb5702b47'),(39,NULL,'app','m161108_000000_new_version_format','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','c472d28c-4aaa-4879-9889-2aec7eb5aa1e'),(40,NULL,'app','m161109_000000_index_shuffle','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','ba375070-e526-4a75-8a72-56cd1ec4166c'),(41,NULL,'app','m161122_185500_no_craft_app','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5c45bb45-860b-42a9-ac94-01b66dfc81ba'),(42,NULL,'app','m161125_150752_clear_urlmanager_cache','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','409f708c-4e2a-4cd0-a308-54b5a9bc0487'),(43,NULL,'app','m161220_000000_volumes_hasurl_notnull','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','16d2f4c2-0aac-4376-8690-f1b862057b27'),(44,NULL,'app','m170114_161144_udates_permission','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','aade37a4-2011-4f0e-93f3-4d7a3c530d80'),(45,NULL,'app','m170120_000000_schema_cleanup','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','fd8bd553-5358-4319-b5bd-c625dd74aa18'),(46,NULL,'app','m170126_000000_assets_focal_point','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','da826448-2202-4500-81a3-4bf3b94118de'),(47,NULL,'app','m170206_142126_system_name','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','a0b29d92-0396-4b70-a35a-8317c88daf04'),(48,NULL,'app','m170217_044740_category_branch_limits','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','effe3f5e-4131-4e0d-a952-c54ffc2fe12c'),(49,NULL,'app','m170217_120224_asset_indexing_columns','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','81594d04-7905-4450-bd19-44ac571170cf'),(50,NULL,'app','m170223_224012_plain_text_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','9855b804-1078-49d2-afd4-b6ed5c3aeb02'),(51,NULL,'app','m170227_120814_focal_point_percentage','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','485fb7ab-15d9-42b6-86b7-955410bd21b5'),(52,NULL,'app','m170228_171113_system_messages','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','0e1d40be-8556-4760-bb38-ab69be821dd9'),(53,NULL,'app','m170303_140500_asset_field_source_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','01fd9972-3e56-4933-85aa-43f1bdb97a76'),(54,NULL,'app','m170306_150500_asset_temporary_uploads','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','33d3e32f-6e62-4194-8580-62f7d211e9a8'),(55,NULL,'app','m170414_162429_rich_text_config_setting','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','c879df86-556a-4dba-a163-ea4b6bc6dbc9'),(56,NULL,'app','m170523_190652_element_field_layout_ids','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','b044f7c7-6b04-4774-8f28-f35f3590d273'),(57,NULL,'app','m170612_000000_route_index_shuffle','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','213c72b5-24c8-4ae3-895c-e091ab377550'),(58,NULL,'app','m170621_195237_format_plugin_handles','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','13d5356c-c264-488f-b9c5-fa593dfcf912'),(59,NULL,'app','m170630_161028_deprecation_changes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','b11fae47-f162-4ca7-bfff-541bf8c43df6'),(60,NULL,'app','m170703_181539_plugins_table_tweaks','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','b280b888-4335-4f24-a8a2-893555d6ddef'),(61,NULL,'app','m170704_134916_sites_tables','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','e4da4cf8-7e84-4c50-9124-1b3411cdc54b'),(62,NULL,'app','m170706_183216_rename_sequences','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','92a24a58-b4ad-4685-9d96-687d9ca5a1dd'),(63,NULL,'app','m170707_094758_delete_compiled_traits','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','10e06713-2dfe-408f-9ceb-39cfd5f962fb'),(64,NULL,'app','m170731_190138_drop_asset_packagist','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','6cbc3bc4-836f-4fdd-9038-5f9040269a1e'),(65,NULL,'app','m170810_201318_create_queue_table','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','d0f48b4b-effc-4c3e-ac95-175534ecde0a'),(66,NULL,'app','m170816_133741_delete_compiled_behaviors','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','c901fdbb-3443-4ca0-99a1-466f0684362f'),(67,NULL,'app','m170821_180624_deprecation_line_nullable','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5474fa76-7e41-48ca-9cee-e90768288536'),(68,NULL,'app','m170903_192801_longblob_for_queue_jobs','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','34857bf1-e19f-4542-944f-f97fea014cbe'),(69,NULL,'app','m170914_204621_asset_cache_shuffle','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','17688e8b-446a-4a17-ad3f-f5cb3335b00b'),(70,NULL,'app','m171011_214115_site_groups','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','3b061431-d373-4098-a176-14f3caf3d91e'),(71,NULL,'app','m171012_151440_primary_site','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5a8bd8eb-1ea6-489b-b858-046c08b74840'),(72,NULL,'app','m171013_142500_transform_interlace','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','90b49931-4b67-4c58-876a-78e36a1a6945'),(73,NULL,'app','m171016_092553_drop_position_select','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','7d93e7cd-87eb-4b15-bf46-9d43ed0cd681'),(74,NULL,'app','m171016_221244_less_strict_translation_method','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','cf06e871-be7e-469c-88f1-e1e5d1b86cd5'),(75,NULL,'app','m171107_000000_assign_group_permissions','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','2aae5324-ce70-4f22-a8f8-5a09033ba2bf'),(76,NULL,'app','m171117_000001_templatecache_index_tune','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','7ac55c34-6ca2-41d8-962a-547494c3f58a'),(77,NULL,'app','m171126_105927_disabled_plugins','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','72b46468-6039-43f1-8561-b4ea9da20a2e'),(78,NULL,'app','m171130_214407_craftidtokens_table','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','ac89a2b8-b9f2-4b77-b58d-9ca459d54119'),(79,NULL,'app','m171202_004225_update_email_settings','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','3f3eee24-6439-4fec-8cce-76b82208298c'),(80,NULL,'app','m171204_000001_templatecache_index_tune_deux','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','cbd9662a-4880-4e8d-a868-0a70fae780cc'),(81,NULL,'app','m171205_130908_remove_craftidtokens_refreshtoken_column','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','4cf9cd2b-be3b-446c-b0d1-72c93383327d'),(82,NULL,'app','m171218_143135_longtext_query_column','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','38ebb9f6-ce09-45a7-8c09-b3fd504bd053'),(83,NULL,'app','m171231_055546_environment_variables_to_aliases','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','4664a4db-80c8-4d42-b362-af431eb4224c'),(84,NULL,'app','m180113_153740_drop_users_archived_column','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','778af219-c241-4b42-889a-cc51ba74e5b3'),(85,NULL,'app','m180122_213433_propagate_entries_setting','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','29c7ef24-c787-4f54-9770-b7f1e6efdf3d'),(86,NULL,'app','m180124_230459_fix_propagate_entries_values','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','fc2be9ab-4972-4325-8265-0c1b7125355c'),(87,NULL,'app','m180128_235202_set_tag_slugs','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','58cbb641-9cf7-4787-a2bb-ea5c0617d7fd'),(88,NULL,'app','m180202_185551_fix_focal_points','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','f0f0bcc5-a6e9-4d72-b253-ed54532d678f'),(89,NULL,'app','m180217_172123_tiny_ints','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','ff9674f3-88e4-4f48-87ad-d4816e3ab4b7'),(90,NULL,'app','m180321_233505_small_ints','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','9123b060-3c63-46b0-bf25-3f009a227d96'),(91,NULL,'app','m180328_115523_new_license_key_statuses','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','cfac063b-a737-47c5-a11d-75ba9c9ee35e'),(92,NULL,'app','m180404_182320_edition_changes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','db25bb04-fa26-4638-a043-0c6f10f92207'),(93,NULL,'app','m180411_102218_fix_db_routes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','6cc125fb-3ab5-451b-860c-e0c8b9499ae1'),(94,NULL,'app','m180416_205628_resourcepaths_table','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','91661b1e-4da2-491f-b986-929334f8a2b7'),(95,NULL,'app','m180418_205713_widget_cleanup','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','5dee1f6e-5bfa-44ed-8e70-810d2c9a66a7'),(96,NULL,'app','m180824_193422_case_sensitivity_fixes','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','f9da9b5f-bff5-4c79-bd4e-f9726985cb06'),(97,NULL,'app','m180901_151639_fix_matrixcontent_tables','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','6fae206c-f7ec-4f99-9ba2-bc13e7b6fac9'),(98,NULL,'app','m181112_203955_sequences_table','2019-02-21 19:51:06','2019-02-21 19:51:06','2019-02-21 19:51:06','0e6164ef-7c78-4135-9832-b5b0b2654dec'),(99,1,'plugin','Install','2019-02-21 19:51:31','2019-02-21 19:51:31','2019-02-21 19:51:31','c9d74b5d-224d-41f9-a277-192819e71ea0'),(100,1,'plugin','m170804_170613_add_scopes','2019-02-21 19:51:31','2019-02-21 19:51:31','2019-02-21 19:51:31','3b4f2f22-2002-433b-bab0-9f9cffb390ed');
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `handle` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `schemaVersion` varchar(255) NOT NULL,
  `licenseKey` char(24) DEFAULT NULL,
  `licenseKeyStatus` enum('valid','invalid','mismatched','astray','unknown') NOT NULL DEFAULT 'unknown',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `settings` text,
  `installDate` datetime NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugins_handle_unq_idx` (`handle`),
  KEY `plugins_enabled_idx` (`enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
INSERT INTO `plugins` VALUES (1,'craftql','dev-imager','1.1.0',NULL,'invalid',1,NULL,'2019-02-21 19:51:31','2019-02-21 19:51:31','2019-03-04 15:21:52','ad980d19-365e-4dfb-b0d9-78b032d7930d');
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job` longblob NOT NULL,
  `description` text,
  `timePushed` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) unsigned NOT NULL DEFAULT '1024',
  `dateReserved` datetime DEFAULT NULL,
  `timeUpdated` int(11) DEFAULT NULL,
  `progress` smallint(6) NOT NULL DEFAULT '0',
  `attempt` int(11) DEFAULT NULL,
  `fail` tinyint(1) DEFAULT '0',
  `dateFailed` datetime DEFAULT NULL,
  `error` text,
  PRIMARY KEY (`id`),
  KEY `queue_fail_timeUpdated_timePushed_idx` (`fail`,`timeUpdated`,`timePushed`),
  KEY `queue_fail_timeUpdated_delay_idx` (`fail`,`timeUpdated`,`delay`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queue`
--

LOCK TABLES `queue` WRITE;
/*!40000 ALTER TABLE `queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relations`
--

DROP TABLE IF EXISTS `relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) NOT NULL,
  `sourceId` int(11) NOT NULL,
  `sourceSiteId` int(11) DEFAULT NULL,
  `targetId` int(11) NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `relations_fieldId_sourceId_sourceSiteId_targetId_unq_idx` (`fieldId`,`sourceId`,`sourceSiteId`,`targetId`),
  KEY `relations_sourceId_idx` (`sourceId`),
  KEY `relations_targetId_idx` (`targetId`),
  KEY `relations_sourceSiteId_idx` (`sourceSiteId`),
  CONSTRAINT `relations_fieldId_fk` FOREIGN KEY (`fieldId`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `relations_sourceId_fk` FOREIGN KEY (`sourceId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `relations_sourceSiteId_fk` FOREIGN KEY (`sourceSiteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relations_targetId_fk` FOREIGN KEY (`targetId`) REFERENCES `elements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relations`
--

LOCK TABLES `relations` WRITE;
/*!40000 ALTER TABLE `relations` DISABLE KEYS */;
INSERT INTO `relations` VALUES (50,28,11,NULL,3,1,'2019-02-24 14:31:44','2019-02-24 14:31:44','4db855ff-0bb1-4839-b972-4e19803ddd4f'),(51,13,11,NULL,9,1,'2019-02-24 14:31:45','2019-02-24 14:31:45','76fa7995-1fb6-4214-bbe8-0ca1a531bb2f'),(52,30,11,NULL,10,1,'2019-02-24 14:31:45','2019-02-24 14:31:45','f6193338-6bf5-4b71-97b5-4b0d383111bd'),(53,35,15,NULL,22,1,'2019-02-24 14:31:45','2019-02-24 14:31:45','294afab4-b6e2-41f8-bca4-051e08d4d888'),(54,28,15,NULL,3,1,'2019-02-24 14:31:45','2019-02-24 14:31:45','49a0af1a-4928-4918-863f-50d5d490ef06'),(55,29,15,NULL,5,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','6466cfbe-f245-4c5d-bbaa-530142be433d'),(56,29,15,NULL,6,2,'2019-02-24 14:31:46','2019-02-24 14:31:46','699dae0c-6d48-4cd0-a078-5109bd1549ed'),(57,29,15,NULL,7,3,'2019-02-24 14:31:46','2019-02-24 14:31:46','d9615349-5cad-4ae3-bcda-b78b1198031a'),(58,13,15,NULL,11,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','be518fe0-0bba-4995-afec-30f69a72f7ea'),(59,13,15,NULL,9,2,'2019-02-24 14:31:46','2019-02-24 14:31:46','98daff86-a950-4812-b6ed-74f6cbb2fbe8'),(60,14,15,NULL,11,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','eac10165-55f1-4e14-8954-6bd79a0c90e1'),(61,30,15,NULL,13,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','03dcfc01-8ba7-4fa7-9bfc-7c928ddb51b9'),(62,30,15,NULL,10,2,'2019-02-24 14:31:46','2019-02-24 14:31:46','3ab35357-ce20-4a65-990b-c07263a2de11'),(63,34,15,NULL,1,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','b03df8ce-5828-4d73-900d-0e9f0bf0ee49'),(64,20,16,NULL,11,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','5aad2f68-1bfa-4aa2-9005-56f67fe98d5c'),(65,20,17,NULL,11,1,'2019-02-24 14:31:46','2019-02-24 14:31:46','a960b91a-0b89-447d-9d1d-926f1171ffb9'),(66,20,17,NULL,9,2,'2019-02-24 14:31:46','2019-02-24 14:31:46','97348758-b29d-4fa0-ac84-bc1af0ff09b6');
/*!40000 ALTER TABLE `relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resourcepaths`
--

DROP TABLE IF EXISTS `resourcepaths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resourcepaths` (
  `hash` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resourcepaths`
--

LOCK TABLES `resourcepaths` WRITE;
/*!40000 ALTER TABLE `resourcepaths` DISABLE KEYS */;
INSERT INTO `resourcepaths` VALUES ('12207c6e','@lib/xregexp'),('15e04055','@lib/garnishjs'),('184c38e4','@lib/selectize'),('1df870a1','@lib/timepicker'),('1e631dad','@craft/web/assets/cp/dist'),('220728d4','@lib/xregexp'),('265d57d3','@lib/d3'),('286b6c5e','@lib/selectize'),('2871eff3','@craft/web/assets/tablesettings/dist'),('288093fc','@craft/web/assets/matrix/dist'),('28f87782','@lib/picturefill'),('2dda41c9','@lib/prismjs'),('2e444917','@craft/web/assets/cp/dist'),('2f5c4b40','@lib/jquery-touch-events'),('3010767c','@lib/fileupload'),('343a9086','@craft/web/assets/updateswidget/dist'),('3aa78652','@craft/web/assets/recententries/dist'),('410d760d','@lib/garnishjs'),('41dc43c','@craft/web/assets/updateswidget/dist'),('42973531','@lib/d3'),('491546f9','@lib/timepicker'),('4b9629a2','@lib/jquery-touch-events'),('4c321560','@lib/picturefill'),('544ba233','@lib/fabric'),('548e6bde','@craft/web/assets/fields/dist'),('54da149e','@lib/fileupload'),('635d86','@craft/web/assets/fields/dist'),('646cf689','@lib/fabric'),('64fd4024','@lib/fileupload'),('712a22b7','@lib/garnishjs'),('72b0618b','@lib/d3'),('76ea1e8c','@lib/xregexp'),('7aa97f4f','@craft/web/assets/cp/dist'),('7bb17d18','@lib/jquery-touch-events'),('7c1541da','@lib/picturefill'),('7c6da5a4','@craft/web/assets/matrix/dist'),('7c865a06','@lib/selectize'),('7c9cd9ab','@craft/web/assets/tablesettings/dist'),('8bc2e0f0','@lib/element-resize-detector'),('9396668f','@craft/web/assets/craftsupport/dist'),('9a2a482b','@craft/web/assets/edituser/dist'),('9b90d142','@craft/web/assets/login/dist'),('9ca40f71','@craft/web/assets/feed/dist'),('9ee7a834','@craft/web/assets/matrixsettings/dist'),('9fbe453f','@lib/jquery-ui'),('a18b8958','@craft/web/assets/editentry/dist'),('a3b13235','@craft/web/assets/craftsupport/dist'),('a4524b05','@bower/jquery/dist'),('a6946b','@lib/fabric'),('a80d2e8','@craft/web/assets/recententries/dist'),('a9fa1390','@lib/jquery.payment'),('ac835bcb','@craft/web/assets/feed/dist'),('af991185','@lib/jquery-ui'),('b5ddbc78','@lib/velocity'),('c09829e7','@bower/jquery/dist'),('ca0a9e6c','@craft/web/assets/matrixsettings/dist'),('cd307172','@lib/jquery.payment'),('d117de9a','@lib/velocity'),('d5c07d00','@craft/web/assets/dashboard/dist'),('df2fd6a8','@lib/element-resize-detector'),('e1308a20','@lib/velocity'),('e5e729ba','@craft/web/assets/dashboard/dist'),('ef088212','@lib/element-resize-detector'),('f0bf7d5d','@bower/jquery/dist'),('f566bf00','@craft/web/assets/editentry/dist'),('f9280caf','/Users/markhuot/Sites/craftql/src/resources'),('f93ccbeb','@craft/web/assets/editcategory/dist'),('fb7427dd','@lib/jquery-ui'),('fd1725c8','@lib/jquery.payment');
/*!40000 ALTER TABLE `resourcepaths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteId` int(11) DEFAULT NULL,
  `uriParts` varchar(255) NOT NULL,
  `uriPattern` varchar(255) NOT NULL,
  `template` varchar(500) NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `routes_uriPattern_idx` (`uriPattern`),
  KEY `routes_siteId_idx` (`siteId`),
  CONSTRAINT `routes_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `searchindex`
--

DROP TABLE IF EXISTS `searchindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `searchindex` (
  `elementId` int(11) NOT NULL,
  `attribute` varchar(25) NOT NULL,
  `fieldId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `keywords` text NOT NULL,
  PRIMARY KEY (`elementId`,`attribute`,`fieldId`,`siteId`),
  FULLTEXT KEY `searchindex_keywords_idx` (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `searchindex`
--

LOCK TABLES `searchindex` WRITE;
/*!40000 ALTER TABLE `searchindex` DISABLE KEYS */;
INSERT INTO `searchindex` VALUES (1,'username',0,1,' admin '),(1,'firstname',0,1,''),(1,'lastname',0,1,''),(1,'fullname',0,1,''),(1,'email',0,1,' foo example com '),(1,'slug',0,1,''),(2,'slug',0,1,' test category one '),(2,'title',0,1,' test category one '),(3,'slug',0,1,' test category two '),(3,'title',0,1,' test category two '),(4,'slug',0,1,' test category three '),(4,'title',0,1,' test category three '),(5,'slug',0,1,' test grandparent category '),(5,'title',0,1,' test grandparent category '),(6,'slug',0,1,' test parent category '),(6,'title',0,1,' test parent category '),(7,'slug',0,1,' test nested category '),(7,'title',0,1,' test nested category '),(8,'slug',0,1,' test child category '),(8,'title',0,1,' test child category '),(9,'field',1,1,''),(9,'field',2,1,''),(9,'field',12,1,''),(9,'field',25,1,''),(9,'field',26,1,''),(9,'field',27,1,''),(9,'field',32,1,''),(9,'field',33,1,''),(9,'field',28,1,''),(9,'field',29,1,''),(9,'field',13,1,''),(9,'field',14,1,''),(9,'field',30,1,''),(9,'field',34,1,''),(9,'field',3,1,' valuetwo '),(9,'field',5,1,''),(9,'field',4,1,''),(9,'field',11,1,' labelone '),(9,'field',24,1,''),(9,'field',31,1,''),(9,'field',6,1,''),(9,'field',7,1,' ffcc00 '),(9,'field',15,1,''),(9,'field',16,1,' 1 '),(9,'field',8,1,''),(9,'field',9,1,''),(9,'field',10,1,''),(9,'field',17,1,''),(9,'field',23,1,''),(9,'field',22,1,''),(9,'slug',0,1,' test empty entry '),(9,'title',0,1,' test empty entry '),(10,'slug',0,1,' tag one '),(10,'title',0,1,' tag one '),(11,'field',1,1,' test plain text content '),(11,'field',2,1,''),(11,'field',12,1,' test email content '),(11,'field',25,1,' 1 '),(11,'field',26,1,' 1 23 '),(11,'field',27,1,' 8 '),(11,'field',32,1,' column one content column two content ffcc00 1 column one content column two content ffcc00 1 '),(11,'field',33,1,''),(11,'field',28,1,' test category two '),(11,'field',29,1,''),(11,'field',13,1,' test empty entry '),(11,'field',14,1,''),(11,'field',30,1,' tag one '),(11,'field',34,1,''),(11,'field',3,1,' valuetwo '),(11,'field',5,1,' 3 '),(11,'field',4,1,''),(11,'field',11,1,' labelthree '),(11,'field',24,1,' optionthree '),(11,'field',31,1,''),(11,'field',6,1,''),(11,'field',7,1,' ffcc00 '),(11,'field',15,1,''),(11,'field',16,1,' 1 '),(11,'field',8,1,''),(11,'field',9,1,''),(11,'field',10,1,''),(11,'field',17,1,' optiontwo block plain text content block column one content block column two content block column one content block column two content '),(11,'field',23,1,''),(11,'field',22,1,''),(12,'field',18,1,' block plain text content '),(12,'field',19,1,' optiontwo '),(12,'field',20,1,''),(12,'field',21,1,' block column one content block column two content block column one content block column two content '),(12,'slug',0,1,''),(11,'slug',0,1,' test partial entry '),(11,'title',0,1,' test partial entry '),(13,'slug',0,1,' tag two '),(13,'title',0,1,' tag two '),(14,'slug',0,1,' tw '),(14,'title',0,1,' tw '),(15,'field',1,1,' test plain text content '),(15,'field',2,1,' test plain text with character limit content '),(15,'field',12,1,' test email content '),(15,'field',25,1,' 1 '),(15,'field',26,1,' 2 34 '),(15,'field',27,1,' 5 '),(15,'field',32,1,' column one content column two content ff0000 1 column one content column two content ff0000 1 '),(15,'field',33,1,' http www test url field com '),(15,'field',28,1,' test category two '),(15,'field',29,1,' test grandparent category test parent category test nested category '),(15,'field',13,1,' test partial entry test empty entry '),(15,'field',14,1,' test partial entry '),(15,'field',30,1,' tag two tag one '),(15,'field',34,1,' admin '),(15,'field',3,1,' valuetwo '),(15,'field',5,1,' 2 '),(15,'field',4,1,' labeltwo '),(15,'field',11,1,' labeltwo '),(15,'field',24,1,' optiontwo '),(15,'field',31,1,' optiontwo '),(15,'field',6,1,' ff0000 '),(15,'field',7,1,' ff0000 '),(15,'field',15,1,' 1 '),(15,'field',16,1,' 1 '),(15,'field',8,1,''),(15,'field',9,1,''),(15,'field',10,1,''),(15,'field',17,1,' optiontwo test partial entry matrix plain text field content column one content column two content 1 column one content column two content 1 optiontwo test partial entry test empty entry matrix second block plain text field content block two column one content block two column two content 1 block two column one content block two column two content 1 '),(15,'field',23,1,''),(15,'field',22,1,''),(16,'field',18,1,' matrix plain text field content '),(16,'field',19,1,' optiontwo '),(16,'field',20,1,' test partial entry '),(16,'field',21,1,' column one content column two content 1 column one content column two content 1 '),(16,'slug',0,1,''),(17,'field',18,1,' matrix second block plain text field content '),(17,'field',19,1,' optiontwo '),(17,'field',20,1,' test partial entry test empty entry '),(17,'field',21,1,' block two column one content block two column two content 1 block two column one content block two column two content 1 '),(17,'slug',0,1,''),(18,'slug',0,1,''),(15,'slug',0,1,' full entry '),(15,'title',0,1,' full entry '),(19,'field',1,1,' test plain text content '),(19,'field',2,1,' test plain text content limited '),(19,'slug',0,1,''),(20,'field',1,1,' test second global set text content '),(20,'field',11,1,' labelone '),(20,'field',3,1,' valuetwo '),(20,'slug',0,1,''),(21,'slug',0,1,''),(9,'field',35,1,''),(11,'field',35,1,''),(15,'field',35,1,' screen shot 2019 02 24 at 9 18 39 am '),(22,'field',1,1,''),(22,'field',2,1,''),(22,'filename',0,1,' screen shot 2019 02 24 at 9 18 39 am png '),(22,'extension',0,1,' png '),(22,'kind',0,1,' image '),(22,'slug',0,1,''),(22,'title',0,1,' screen shot 2019 02 24 at 9 18 39 am ');
/*!40000 ALTER TABLE `searchindex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structureId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `type` enum('single','channel','structure') NOT NULL DEFAULT 'channel',
  `enableVersioning` tinyint(1) NOT NULL DEFAULT '0',
  `propagateEntries` tinyint(1) NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sections_handle_unq_idx` (`handle`),
  UNIQUE KEY `sections_name_unq_idx` (`name`),
  KEY `sections_structureId_idx` (`structureId`),
  CONSTRAINT `sections_structureId_fk` FOREIGN KEY (`structureId`) REFERENCES `structures` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,NULL,'Blog Post','blogPost','channel',1,1,'2019-02-21 19:52:32','2019-02-21 19:52:32','8a56676a-83bf-451a-846b-da552ce9ca2e');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections_sites`
--

DROP TABLE IF EXISTS `sections_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionId` int(11) NOT NULL,
  `siteId` int(11) NOT NULL,
  `hasUrls` tinyint(1) NOT NULL DEFAULT '1',
  `uriFormat` text,
  `template` varchar(500) DEFAULT NULL,
  `enabledByDefault` tinyint(1) NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sections_sites_sectionId_siteId_unq_idx` (`sectionId`,`siteId`),
  KEY `sections_sites_siteId_idx` (`siteId`),
  CONSTRAINT `sections_sites_sectionId_fk` FOREIGN KEY (`sectionId`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sections_sites_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections_sites`
--

LOCK TABLES `sections_sites` WRITE;
/*!40000 ALTER TABLE `sections_sites` DISABLE KEYS */;
INSERT INTO `sections_sites` VALUES (1,1,1,1,'blog-post/{slug}','blog-post/_entry',1,'2019-02-21 19:52:32','2019-02-21 19:52:32','0853ded2-c793-4f64-83aa-a58e88a4c11c');
/*!40000 ALTER TABLE `sections_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sequences`
--

DROP TABLE IF EXISTS `sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sequences` (
  `name` varchar(255) NOT NULL,
  `next` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sequences`
--

LOCK TABLES `sequences` WRITE;
/*!40000 ALTER TABLE `sequences` DISABLE KEYS */;
/*!40000 ALTER TABLE `sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `token` char(100) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sessions_uid_idx` (`uid`),
  KEY `sessions_token_idx` (`token`),
  KEY `sessions_dateUpdated_idx` (`dateUpdated`),
  KEY `sessions_userId_idx` (`userId`),
  CONSTRAINT `sessions_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,1,'0O9Fwy1soXtndsRR9AlCYa7F69tPkMoYgzERKigyRvAS2jxG6pYGbwvZTZbAP4Ae30QdEZmCYTy62qsQUQmYtcnf6qfM8DGb2icr','2019-02-21 19:51:16','2019-02-21 21:40:05','cc5c8771-e3c9-4d21-87d8-c95439444b15'),(3,1,'bvA33jJSYZRwqh-xZZVzN0C6mrVkPDSfthsLkxRmJoCLe92apKhHLfKDTLLkJn1YkW9MI2BsrqUOz4wWPrnEGgJq73k2zg01i9FE','2019-02-24 14:06:17','2019-02-24 14:32:02','e2126419-c858-46f5-8a30-038a0a899c8d'),(4,1,'dldVANAp4PdWuBkxO0M4Q4lSCx2Ros_Jtggja0RZ6g8mo6a8UQFXOvKiLn2SRr99qCsj5DnaFsNaJIZa4rcWhG5FuAAqaamKCqL6','2019-03-01 21:44:46','2019-03-01 21:48:55','b81b2aaa-fb5d-4b14-a30a-4fceaa0f36bc'),(5,1,'mvxBLE3inrtL99qLAPoBzslNMf2DfCCGOjFUCf1rH8fhIWlCnVE6EfuytbqONOIDgvPs2Iw3dpa-qgGua1SlnJUIn-ci2-TmxSue','2019-03-01 21:50:58','2019-03-01 21:50:58','e4dd4f7d-6af9-42ef-957b-f2c03c42b85e'),(6,1,'tYMaG283B15bGQknR_WvJS-jnwF5NyAKr4edYU-WltILpgp2OIbopUQFfP8GO62Eqw63mPcOuChHLUq3VOm1FGLagXEuAOP4MIUq','2019-03-01 22:45:07','2019-03-01 22:45:07','901661cb-4a7b-4c6d-8465-9f8d18de3a15'),(7,1,'oKqZmZre2-iO514qHMYtVWiAxlnNZZOUoFKCf7G_7KFXYw7W-7PZiaSM77p1duqaC2fxRN8q9aNnh20nqOFLB1I8QH2D9I6dM_yt','2019-03-01 23:46:08','2019-03-01 23:46:08','ba75df78-49a2-4f37-9a06-db1d808b2607');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shunnedmessages`
--

DROP TABLE IF EXISTS `shunnedmessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shunnedmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `expiryDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shunnedmessages_userId_message_unq_idx` (`userId`,`message`),
  CONSTRAINT `shunnedmessages_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shunnedmessages`
--

LOCK TABLES `shunnedmessages` WRITE;
/*!40000 ALTER TABLE `shunnedmessages` DISABLE KEYS */;
/*!40000 ALTER TABLE `shunnedmessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sitegroups`
--

DROP TABLE IF EXISTS `sitegroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sitegroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sitegroups_name_unq_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sitegroups`
--

LOCK TABLES `sitegroups` WRITE;
/*!40000 ALTER TABLE `sitegroups` DISABLE KEYS */;
INSERT INTO `sitegroups` VALUES (1,'Test Site','2019-02-21 19:51:04','2019-02-21 19:51:04','d8e7c1f7-1f98-45f6-b519-f60ca585e2b3');
/*!40000 ALTER TABLE `sitegroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `primary` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `language` varchar(12) NOT NULL,
  `hasUrls` tinyint(1) NOT NULL DEFAULT '0',
  `baseUrl` varchar(255) DEFAULT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sites_handle_unq_idx` (`handle`),
  KEY `sites_sortOrder_idx` (`sortOrder`),
  KEY `sites_groupId_fk` (`groupId`),
  CONSTRAINT `sites_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `sitegroups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sites`
--

LOCK TABLES `sites` WRITE;
/*!40000 ALTER TABLE `sites` DISABLE KEYS */;
INSERT INTO `sites` VALUES (1,1,1,'Test Site','default','en',1,'http://localhost/',1,'2019-02-21 19:51:04','2019-02-21 19:51:04','88874c8c-6e44-4034-a813-5113ad300df2');
/*!40000 ALTER TABLE `sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `structureelements`
--

DROP TABLE IF EXISTS `structureelements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `structureelements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structureId` int(11) NOT NULL,
  `elementId` int(11) DEFAULT NULL,
  `root` int(11) unsigned DEFAULT NULL,
  `lft` int(11) unsigned NOT NULL,
  `rgt` int(11) unsigned NOT NULL,
  `level` smallint(6) unsigned NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `structureelements_structureId_elementId_unq_idx` (`structureId`,`elementId`),
  KEY `structureelements_root_idx` (`root`),
  KEY `structureelements_lft_idx` (`lft`),
  KEY `structureelements_rgt_idx` (`rgt`),
  KEY `structureelements_level_idx` (`level`),
  KEY `structureelements_elementId_idx` (`elementId`),
  CONSTRAINT `structureelements_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `structureelements_structureId_fk` FOREIGN KEY (`structureId`) REFERENCES `structures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `structureelements`
--

LOCK TABLES `structureelements` WRITE;
/*!40000 ALTER TABLE `structureelements` DISABLE KEYS */;
INSERT INTO `structureelements` VALUES (1,1,NULL,1,1,8,0,'2019-02-21 20:04:04','2019-02-21 20:04:30','030a9e27-3590-441b-8c6c-d99892ff5eca'),(2,1,2,1,2,3,1,'2019-02-21 20:04:04','2019-02-21 20:04:04','09efd4a8-8391-42b7-8106-c242f5390d90'),(3,1,3,1,4,5,1,'2019-02-21 20:04:15','2019-02-21 20:04:15','dcb2c726-0a91-4e9d-9adc-6d0e4f83baf6'),(4,1,4,1,6,7,1,'2019-02-21 20:04:30','2019-02-21 20:04:30','f3563936-087d-4feb-955a-ae78b04ef987'),(5,3,NULL,5,1,10,0,'2019-02-21 20:06:36','2019-02-21 20:07:52','4b338f9b-3149-49eb-b135-7da68ea91ced'),(6,3,5,5,2,9,1,'2019-02-21 20:06:36','2019-02-21 20:07:52','c6ec4154-802f-4e8f-b74b-53a6d044fd0d'),(7,3,6,5,3,8,2,'2019-02-21 20:06:49','2019-02-21 20:07:52','09b63356-890c-4d24-baa4-18ede484736f'),(8,3,7,5,4,7,3,'2019-02-21 20:07:32','2019-02-21 20:07:52','7c17ce37-908a-4257-9106-1c345593b713'),(9,3,8,5,5,6,4,'2019-02-21 20:07:52','2019-02-21 20:07:52','5b88f439-b963-4b7f-97b4-102810c5cfde');
/*!40000 ALTER TABLE `structureelements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `structures`
--

DROP TABLE IF EXISTS `structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maxLevels` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `structures`
--

LOCK TABLES `structures` WRITE;
/*!40000 ALTER TABLE `structures` DISABLE KEYS */;
INSERT INTO `structures` VALUES (1,NULL,'2019-02-21 20:03:17','2019-02-21 20:03:17','ed0743ed-9498-45cf-8709-ede754dae84a'),(2,NULL,'2019-02-21 20:03:45','2019-02-21 20:03:45','09e22bee-d9dd-4ff5-a038-51a8e5e33493'),(3,NULL,'2019-02-21 20:06:08','2019-02-21 20:06:08','976f1cf1-99a6-41a5-aae4-bb02aa3a2060');
/*!40000 ALTER TABLE `structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemmessages`
--

DROP TABLE IF EXISTS `systemmessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `systemmessages_key_language_unq_idx` (`key`,`language`),
  KEY `systemmessages_language_idx` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemmessages`
--

LOCK TABLES `systemmessages` WRITE;
/*!40000 ALTER TABLE `systemmessages` DISABLE KEYS */;
/*!40000 ALTER TABLE `systemmessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemsettings`
--

DROP TABLE IF EXISTS `systemsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(15) NOT NULL,
  `settings` text,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `systemsettings_category_unq_idx` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemsettings`
--

LOCK TABLES `systemsettings` WRITE;
/*!40000 ALTER TABLE `systemsettings` DISABLE KEYS */;
INSERT INTO `systemsettings` VALUES (1,'email','{\"fromEmail\":\"foo@example.com\",\"fromName\":\"Test Site\",\"transportType\":\"craft\\\\mail\\\\transportadapters\\\\Sendmail\"}','2019-02-21 19:51:06','2019-02-21 19:51:06','f720ba93-637d-4ab3-8aa6-29ad58d0c4e9');
/*!40000 ALTER TABLE `systemsettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taggroups`
--

DROP TABLE IF EXISTS `taggroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taggroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `taggroups_name_unq_idx` (`name`),
  UNIQUE KEY `taggroups_handle_unq_idx` (`handle`),
  KEY `taggroups_fieldLayoutId_fk` (`fieldLayoutId`),
  CONSTRAINT `taggroups_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taggroups`
--

LOCK TABLES `taggroups` WRITE;
/*!40000 ALTER TABLE `taggroups` DISABLE KEYS */;
INSERT INTO `taggroups` VALUES (1,'Test Tag Group','testTagGroup',4,'2019-02-21 20:02:38','2019-02-21 20:02:38','40de92d5-10c2-4d0a-b7c8-1086edfbe28d'),(2,'Test Empty Tag Group','testEmptyTagGroup',5,'2019-02-21 20:02:49','2019-02-21 20:02:49','9e52c295-e4fd-4cd6-9029-d9587889b984');
/*!40000 ALTER TABLE `taggroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tags_groupId_idx` (`groupId`),
  CONSTRAINT `tags_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `taggroups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tags_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (10,1,'2019-02-21 20:15:39','2019-02-21 20:15:39','ffe10bdb-618a-43c1-ac62-e8544bcd0b77'),(13,1,'2019-02-21 21:26:15','2019-02-21 21:26:15','a49da2b0-43d0-428c-8367-8f73ffbba6a8'),(14,1,'2019-02-21 21:26:21','2019-02-21 21:26:21','4fcffb2a-d2d3-4fd5-8cdc-7fbca107f4a8');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templatecacheelements`
--

DROP TABLE IF EXISTS `templatecacheelements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templatecacheelements` (
  `cacheId` int(11) NOT NULL,
  `elementId` int(11) NOT NULL,
  KEY `templatecacheelements_cacheId_idx` (`cacheId`),
  KEY `templatecacheelements_elementId_idx` (`elementId`),
  CONSTRAINT `templatecacheelements_cacheId_fk` FOREIGN KEY (`cacheId`) REFERENCES `templatecaches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `templatecacheelements_elementId_fk` FOREIGN KEY (`elementId`) REFERENCES `elements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templatecacheelements`
--

LOCK TABLES `templatecacheelements` WRITE;
/*!40000 ALTER TABLE `templatecacheelements` DISABLE KEYS */;
/*!40000 ALTER TABLE `templatecacheelements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templatecachequeries`
--

DROP TABLE IF EXISTS `templatecachequeries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templatecachequeries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cacheId` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `query` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `templatecachequeries_cacheId_idx` (`cacheId`),
  KEY `templatecachequeries_type_idx` (`type`),
  CONSTRAINT `templatecachequeries_cacheId_fk` FOREIGN KEY (`cacheId`) REFERENCES `templatecaches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templatecachequeries`
--

LOCK TABLES `templatecachequeries` WRITE;
/*!40000 ALTER TABLE `templatecachequeries` DISABLE KEYS */;
/*!40000 ALTER TABLE `templatecachequeries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templatecaches`
--

DROP TABLE IF EXISTS `templatecaches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templatecaches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteId` int(11) NOT NULL,
  `cacheKey` varchar(255) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `expiryDate` datetime NOT NULL,
  `body` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `templatecaches_cacheKey_siteId_expiryDate_path_idx` (`cacheKey`,`siteId`,`expiryDate`,`path`),
  KEY `templatecaches_cacheKey_siteId_expiryDate_idx` (`cacheKey`,`siteId`,`expiryDate`),
  KEY `templatecaches_siteId_idx` (`siteId`),
  CONSTRAINT `templatecaches_siteId_fk` FOREIGN KEY (`siteId`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templatecaches`
--

LOCK TABLES `templatecaches` WRITE;
/*!40000 ALTER TABLE `templatecaches` DISABLE KEYS */;
/*!40000 ALTER TABLE `templatecaches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` char(32) NOT NULL,
  `route` text,
  `usageLimit` tinyint(3) unsigned DEFAULT NULL,
  `usageCount` tinyint(3) unsigned DEFAULT NULL,
  `expiryDate` datetime NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokens_token_unq_idx` (`token`),
  KEY `tokens_expiryDate_idx` (`expiryDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usergroups`
--

DROP TABLE IF EXISTS `usergroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usergroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usergroups_handle_unq_idx` (`handle`),
  UNIQUE KEY `usergroups_name_unq_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usergroups`
--

LOCK TABLES `usergroups` WRITE;
/*!40000 ALTER TABLE `usergroups` DISABLE KEYS */;
/*!40000 ALTER TABLE `usergroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usergroups_users`
--

DROP TABLE IF EXISTS `usergroups_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usergroups_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usergroups_users_groupId_userId_unq_idx` (`groupId`,`userId`),
  KEY `usergroups_users_userId_idx` (`userId`),
  CONSTRAINT `usergroups_users_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `usergroups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usergroups_users_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usergroups_users`
--

LOCK TABLES `usergroups_users` WRITE;
/*!40000 ALTER TABLE `usergroups_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `usergroups_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userpermissions`
--

DROP TABLE IF EXISTS `userpermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userpermissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userpermissions_name_unq_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userpermissions`
--

LOCK TABLES `userpermissions` WRITE;
/*!40000 ALTER TABLE `userpermissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `userpermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userpermissions_usergroups`
--

DROP TABLE IF EXISTS `userpermissions_usergroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userpermissions_usergroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permissionId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userpermissions_usergroups_permissionId_groupId_unq_idx` (`permissionId`,`groupId`),
  KEY `userpermissions_usergroups_groupId_idx` (`groupId`),
  CONSTRAINT `userpermissions_usergroups_groupId_fk` FOREIGN KEY (`groupId`) REFERENCES `usergroups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `userpermissions_usergroups_permissionId_fk` FOREIGN KEY (`permissionId`) REFERENCES `userpermissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userpermissions_usergroups`
--

LOCK TABLES `userpermissions_usergroups` WRITE;
/*!40000 ALTER TABLE `userpermissions_usergroups` DISABLE KEYS */;
/*!40000 ALTER TABLE `userpermissions_usergroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userpermissions_users`
--

DROP TABLE IF EXISTS `userpermissions_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userpermissions_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permissionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userpermissions_users_permissionId_userId_unq_idx` (`permissionId`,`userId`),
  KEY `userpermissions_users_userId_idx` (`userId`),
  CONSTRAINT `userpermissions_users_permissionId_fk` FOREIGN KEY (`permissionId`) REFERENCES `userpermissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `userpermissions_users_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userpermissions_users`
--

LOCK TABLES `userpermissions_users` WRITE;
/*!40000 ALTER TABLE `userpermissions_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `userpermissions_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userpreferences`
--

DROP TABLE IF EXISTS `userpreferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userpreferences` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `preferences` text,
  PRIMARY KEY (`userId`),
  CONSTRAINT `userpreferences_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userpreferences`
--

LOCK TABLES `userpreferences` WRITE;
/*!40000 ALTER TABLE `userpreferences` DISABLE KEYS */;
INSERT INTO `userpreferences` VALUES (1,'{\"language\":\"en\",\"weekStartDay\":\"0\",\"enableDebugToolbarForSite\":true,\"enableDebugToolbarForCp\":true}');
/*!40000 ALTER TABLE `userpreferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `photoId` int(11) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `pending` tinyint(1) NOT NULL DEFAULT '0',
  `lastLoginDate` datetime DEFAULT NULL,
  `lastLoginAttemptIp` varchar(45) DEFAULT NULL,
  `invalidLoginWindowStart` datetime DEFAULT NULL,
  `invalidLoginCount` tinyint(3) unsigned DEFAULT NULL,
  `lastInvalidLoginDate` datetime DEFAULT NULL,
  `lockoutDate` datetime DEFAULT NULL,
  `hasDashboard` tinyint(1) NOT NULL DEFAULT '0',
  `verificationCode` varchar(255) DEFAULT NULL,
  `verificationCodeIssuedDate` datetime DEFAULT NULL,
  `unverifiedEmail` varchar(255) DEFAULT NULL,
  `passwordResetRequired` tinyint(1) NOT NULL DEFAULT '0',
  `lastPasswordChangeDate` datetime DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `users_uid_idx` (`uid`),
  KEY `users_verificationCode_idx` (`verificationCode`),
  KEY `users_email_idx` (`email`),
  KEY `users_username_idx` (`username`),
  KEY `users_photoId_fk` (`photoId`),
  CONSTRAINT `users_id_fk` FOREIGN KEY (`id`) REFERENCES `elements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_photoId_fk` FOREIGN KEY (`photoId`) REFERENCES `assets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin',NULL,'','','foo@example.com','$2y$13$s/2WOUOvOALDZz6JWuGoqOsNHSIVjQsGjlnraC8XhiVsHJxFbHuhW',1,0,0,0,'2019-03-01 23:46:09','::1',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,0,'2019-02-21 19:51:06','2019-02-21 19:51:06','2019-03-01 23:46:09','e427d19a-4457-4704-98f6-edf2c51ca615');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volumefolders`
--

DROP TABLE IF EXISTS `volumefolders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volumefolders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) DEFAULT NULL,
  `volumeId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `volumefolders_name_parentId_volumeId_unq_idx` (`name`,`parentId`,`volumeId`),
  KEY `volumefolders_parentId_idx` (`parentId`),
  KEY `volumefolders_volumeId_idx` (`volumeId`),
  CONSTRAINT `volumefolders_parentId_fk` FOREIGN KEY (`parentId`) REFERENCES `volumefolders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `volumefolders_volumeId_fk` FOREIGN KEY (`volumeId`) REFERENCES `volumes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volumefolders`
--

LOCK TABLES `volumefolders` WRITE;
/*!40000 ALTER TABLE `volumefolders` DISABLE KEYS */;
INSERT INTO `volumefolders` VALUES (1,NULL,1,'Test Volume','','2019-02-24 00:49:41','2019-02-24 00:49:41','d93f3a46-b409-4cf9-8da9-7c43c5f70436'),(2,NULL,NULL,'Temporary source',NULL,'2019-02-24 14:18:44','2019-02-24 14:18:44','f402d85d-1d3f-43af-afec-9a5bc42f272b'),(3,2,NULL,'user_1','user_1/','2019-02-24 14:18:44','2019-02-24 14:18:44','6bd0b6bf-e017-466c-8cb1-6485d9489b77');
/*!40000 ALTER TABLE `volumefolders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volumes`
--

DROP TABLE IF EXISTS `volumes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volumes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldLayoutId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `hasUrls` tinyint(1) NOT NULL DEFAULT '1',
  `url` varchar(255) DEFAULT NULL,
  `settings` text,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `volumes_name_unq_idx` (`name`),
  UNIQUE KEY `volumes_handle_unq_idx` (`handle`),
  KEY `volumes_fieldLayoutId_idx` (`fieldLayoutId`),
  CONSTRAINT `volumes_fieldLayoutId_fk` FOREIGN KEY (`fieldLayoutId`) REFERENCES `fieldlayouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volumes`
--

LOCK TABLES `volumes` WRITE;
/*!40000 ALTER TABLE `volumes` DISABLE KEYS */;
INSERT INTO `volumes` VALUES (1,12,'Test Volume','testVolume','craft\\volumes\\Local',1,'/','{\"path\":\"@webroot\"}',1,'2019-02-24 00:49:41','2019-02-24 00:49:41','c2d5ad12-650e-42c4-b25e-8b7e693fa770');
/*!40000 ALTER TABLE `volumes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widgets`
--

DROP TABLE IF EXISTS `widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `sortOrder` smallint(6) unsigned DEFAULT NULL,
  `colspan` tinyint(1) NOT NULL DEFAULT '0',
  `settings` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `dateCreated` datetime NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `uid` char(36) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `widgets_userId_idx` (`userId`),
  CONSTRAINT `widgets_userId_fk` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widgets`
--

LOCK TABLES `widgets` WRITE;
/*!40000 ALTER TABLE `widgets` DISABLE KEYS */;
INSERT INTO `widgets` VALUES (1,1,'craft\\widgets\\RecentEntries',1,0,'{\"section\":\"*\",\"siteId\":\"1\",\"limit\":10}',1,'2019-02-21 19:51:27','2019-02-21 19:51:27','9e718da8-aae5-4ac2-910a-1dfb1a7ba551'),(2,1,'craft\\widgets\\CraftSupport',2,0,'[]',1,'2019-02-21 19:51:27','2019-02-21 19:51:27','505f59ac-a419-4d1d-b4a3-e3719f18e2da'),(3,1,'craft\\widgets\\Updates',3,0,'[]',1,'2019-02-21 19:51:27','2019-02-21 19:51:27','16e21ee6-18e4-4bec-a486-e5e7860a583b'),(4,1,'craft\\widgets\\Feed',4,0,'{\"url\":\"https://craftcms.com/news.rss\",\"title\":\"Craft News\",\"limit\":5}',1,'2019-02-21 19:51:27','2019-02-21 19:51:27','0434c845-da79-4dfe-b5b9-44cdeb3e2297');
/*!40000 ALTER TABLE `widgets` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-04 15:13:38
