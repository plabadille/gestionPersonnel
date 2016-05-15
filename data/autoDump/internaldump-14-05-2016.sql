-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: mysql.info.unicaen.fr    Database: 21101555_bd
-- ------------------------------------------------------
-- Server version	5.5.47-0+deb7u1-log

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
-- Table structure for table `Actifs`
--

DROP TABLE IF EXISTS `Actifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Actifs` (
  `matricule` int(12) NOT NULL,
  `eligible_retraite` tinyint(1) NOT NULL DEFAULT '0',
  `eligible_promotion` tinyint(1) NOT NULL DEFAULT '0',
  `saisie_by` int(12) DEFAULT NULL,
  PRIMARY KEY (`matricule`),
  KEY `Actifs_ibfk_2` (`saisie_by`),
  CONSTRAINT `Actifs_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `Actifs_ibfk_2` FOREIGN KEY (`saisie_by`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Actifs`
--

LOCK TABLES `Actifs` WRITE;
/*!40000 ALTER TABLE `Actifs` DISABLE KEYS */;
INSERT INTO `Actifs` VALUES (1,0,0,NULL),(2,0,0,NULL),(3,1,1,NULL),(4,1,0,NULL),(5,1,1,NULL),(6,1,0,NULL),(7,1,0,NULL),(8,1,0,NULL),(9,1,0,NULL),(10,1,0,NULL),(11,0,1,NULL),(12,1,0,NULL),(13,1,0,NULL),(14,0,1,NULL),(15,1,0,NULL),(16,1,0,NULL),(17,1,0,NULL),(18,1,1,NULL),(19,1,0,NULL),(20,1,0,NULL),(21,1,0,NULL),(22,1,0,NULL),(23,1,0,NULL),(24,1,1,NULL),(25,1,0,NULL),(26,1,1,NULL),(27,1,0,NULL),(28,1,0,NULL),(29,1,0,NULL),(30,1,0,NULL),(31,1,1,NULL),(32,1,1,NULL),(33,1,0,NULL),(34,0,0,NULL),(35,1,1,NULL),(36,1,0,NULL),(37,1,1,NULL),(38,1,0,NULL),(39,1,0,NULL),(40,1,0,NULL),(41,1,0,NULL),(42,0,0,NULL),(43,1,0,NULL),(44,1,1,NULL),(45,1,0,NULL),(46,0,0,NULL),(47,0,0,NULL),(48,1,0,NULL),(49,1,0,NULL),(50,0,0,NULL),(51,1,0,NULL),(52,1,1,NULL),(53,0,0,NULL),(54,1,0,NULL),(55,1,1,NULL),(56,1,0,NULL),(57,1,0,NULL),(58,1,1,NULL),(59,1,0,NULL),(60,1,0,NULL),(61,1,0,NULL),(62,1,1,NULL),(63,1,0,NULL),(65,1,0,NULL),(66,1,0,NULL),(67,1,0,NULL),(68,1,0,NULL),(69,1,0,NULL),(70,0,0,NULL),(71,1,0,NULL),(72,1,1,NULL),(73,0,0,NULL),(74,1,1,NULL),(75,1,0,NULL),(76,1,0,NULL),(77,1,1,NULL),(78,1,0,NULL),(79,1,1,NULL),(80,1,1,NULL),(81,1,0,NULL),(82,1,0,NULL),(83,0,0,NULL),(84,1,1,NULL),(85,1,0,NULL),(86,1,0,NULL),(87,0,0,NULL),(88,1,0,NULL),(89,1,0,NULL),(90,1,0,NULL),(91,1,0,NULL),(92,0,0,NULL),(93,0,0,NULL),(94,1,0,NULL),(95,1,0,NULL),(96,1,0,NULL),(97,1,0,NULL),(98,1,0,NULL),(99,0,1,NULL),(100,0,0,NULL),(111,0,0,NULL),(214,0,0,3),(241,0,0,111);
/*!40000 ALTER TABLE `Actifs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Affectation`
--

DROP TABLE IF EXISTS `Affectation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Affectation` (
  `nb` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` int(12) DEFAULT NULL,
  `id` int(12) DEFAULT NULL,
  `date_affectation` date NOT NULL,
  PRIMARY KEY (`nb`),
  KEY `Affectation_ibfk_1` (`matricule`),
  KEY `Affectation_ibfk_2` (`id`),
  CONSTRAINT `Affectation_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `Affectation_ibfk_2` FOREIGN KEY (`id`) REFERENCES `Casernes` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Affectation`
--

LOCK TABLES `Affectation` WRITE;
/*!40000 ALTER TABLE `Affectation` DISABLE KEYS */;
INSERT INTO `Affectation` VALUES (1,1,8,'1997-05-14'),(2,2,8,'2015-02-19'),(3,3,2,'2000-08-10'),(4,4,6,'2008-05-16'),(5,5,4,'1980-03-12'),(6,6,1,'1993-11-12'),(7,7,6,'1979-12-18'),(8,8,6,'1977-03-04'),(9,9,6,'1981-06-01'),(10,10,3,'2008-05-04'),(11,11,3,'2005-06-18'),(12,12,1,'1982-10-04'),(13,13,2,'1997-06-20'),(14,14,9,'2014-02-25'),(15,15,5,'1976-02-23'),(16,16,2,'1989-08-02'),(17,17,8,'1990-11-03'),(18,18,1,'1995-06-13'),(19,19,8,'2000-08-06'),(20,20,2,'1989-09-13'),(21,21,4,'2009-09-22'),(22,22,8,'1996-02-23'),(23,23,2,'2004-09-18'),(24,24,8,'1981-04-15'),(25,25,10,'1990-11-15'),(26,26,9,'1988-08-04'),(27,27,5,'1974-02-15'),(28,28,7,'2005-05-03'),(29,29,8,'2004-02-07'),(30,30,5,'1983-11-18'),(31,31,9,'1979-03-05'),(32,32,7,'1987-07-10'),(33,33,5,'2003-04-26'),(34,34,4,'2010-06-13'),(35,35,4,'1977-10-01'),(36,36,3,'1983-04-04'),(37,37,4,'1976-12-25'),(38,38,5,'1998-04-24'),(39,39,3,'1999-05-12'),(40,40,10,'2004-07-22'),(41,41,6,'2005-07-20'),(42,42,2,'2009-09-27'),(43,43,6,'1977-03-13'),(44,44,6,'1972-05-13'),(45,45,3,'1987-04-11'),(46,46,6,'2010-03-03'),(47,47,8,'2008-08-02'),(48,48,7,'2007-10-20'),(49,49,8,'2010-01-03'),(50,50,2,'2009-08-24'),(51,51,2,'1998-06-25'),(52,52,3,'1980-10-22'),(53,53,4,'2009-03-27'),(54,54,6,'1990-12-15'),(55,55,2,'1978-09-26'),(56,56,7,'1991-02-20'),(57,57,4,'2002-10-13'),(58,58,6,'1982-03-03'),(59,59,5,'1988-02-20'),(60,60,1,'1984-01-23'),(61,61,9,'1982-06-21'),(62,62,9,'2000-01-07'),(63,63,9,'2007-07-24'),(64,64,8,'1980-01-09'),(65,65,5,'1998-11-18'),(66,66,4,'1978-11-22'),(67,67,4,'2005-06-07'),(68,68,9,'1986-04-20'),(69,69,1,'1974-12-20'),(70,70,4,'2004-09-01'),(71,71,7,'1985-05-01'),(72,72,4,'1992-09-09'),(73,73,10,'2004-12-09'),(74,74,9,'1985-10-06'),(75,75,7,'2000-05-02'),(76,76,10,'2012-08-09'),(77,77,10,'1984-03-06'),(78,78,4,'1990-11-24'),(79,79,10,'2007-04-23'),(80,80,10,'1989-07-27'),(81,81,8,'1993-08-02'),(82,82,6,'2002-12-17'),(83,83,8,'2012-08-10'),(84,84,8,'1996-05-21'),(85,85,8,'2002-10-11'),(86,86,5,'1973-01-07'),(87,87,6,'2015-05-27'),(88,88,7,'1999-09-02'),(89,89,9,'1974-08-20'),(90,90,5,'1972-06-07'),(91,91,6,'1977-09-24'),(92,92,2,'2014-05-07'),(93,93,5,'2008-10-27'),(94,94,4,'1998-09-21'),(95,95,8,'1993-05-03'),(96,96,2,'1977-02-15'),(97,97,7,'2000-09-13'),(98,98,5,'2003-07-23'),(99,99,9,'2003-10-25'),(100,100,6,'2008-06-17'),(101,3,9,'2005-10-03'),(107,111,10,'2016-04-01'),(111,1,3,'2000-10-25'),(112,214,3,'2002-12-03'),(113,214,1,'2005-12-02'),(119,8,4,'1990-02-13'),(120,224,1,'2016-04-01'),(121,224,1,'2016-04-30'),(122,227,1,'2016-04-08'),(123,227,2,'2016-04-14'),(124,225,1,'2016-04-02'),(127,238,5,'1991-02-28');
/*!40000 ALTER TABLE `Affectation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AppartientRegiment`
--

DROP TABLE IF EXISTS `AppartientRegiment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AppartientRegiment` (
  `nb` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` int(12) NOT NULL,
  `id` varchar(30) NOT NULL,
  `date_appartenance` date NOT NULL,
  PRIMARY KEY (`nb`),
  KEY `AppartientRegiment_ibfk_1` (`matricule`),
  KEY `AppartientRegiment_ibfk_2` (`id`),
  CONSTRAINT `AppartientRegiment_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `AppartientRegiment_ibfk_2` FOREIGN KEY (`id`) REFERENCES `Regiment` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AppartientRegiment`
--

LOCK TABLES `AppartientRegiment` WRITE;
/*!40000 ALTER TABLE `AppartientRegiment` DISABLE KEYS */;
INSERT INTO `AppartientRegiment` VALUES (1,1,'Forces Armées','1997-05-14'),(2,2,'Forces Armées','2015-02-19'),(3,3,'Forces Armées','2000-08-10'),(4,4,'Forces Armées','2008-05-16'),(5,5,'Forces Armées','1980-03-12'),(6,6,'Forces Armées','1993-11-12'),(7,7,'Forces Armées','1979-12-18'),(8,8,'Forces Armées','1977-03-04'),(9,9,'Forces Armées','1981-06-01'),(10,10,'Forces Armées','2008-05-04'),(11,11,'Forces Armées','2005-06-18'),(12,12,'Forces Armées','1982-10-04'),(13,13,'Forces Armées','1997-06-20'),(14,14,'Forces Armées','2014-02-25'),(15,15,'Forces Armées','1976-02-23'),(16,16,'Forces Armées','1989-08-02'),(17,17,'Forces Armées','1990-11-03'),(18,18,'Forces Armées','1995-06-13'),(19,19,'Forces Armées','2000-08-06'),(20,20,'Forces Armées','1989-09-13'),(21,21,'Forces Armées','2009-09-22'),(22,22,'Forces Armées','1996-02-23'),(23,23,'Forces Armées','2004-09-18'),(24,24,'Forces Armées','1981-04-15'),(25,25,'Forces Armées','1990-11-15'),(26,26,'Forces Armées','1988-08-04'),(27,27,'Forces Armées','1974-02-15'),(28,28,'Forces Armées','2005-05-03'),(29,29,'Forces Armées','2004-02-07'),(30,30,'Forces Armées','1983-11-18'),(31,31,'Forces Armées','1979-03-05'),(32,32,'Forces Armées','1987-07-10'),(33,33,'Forces Armées','2003-04-26'),(34,34,'Forces Armées','2010-06-13'),(35,35,'Forces Armées','1977-10-01'),(36,36,'Forces Armées','1983-04-04'),(37,37,'Forces Armées','1976-12-25'),(38,38,'Forces Armées','1998-04-24'),(39,39,'Forces Armées','1999-05-12'),(40,40,'Forces Armées','2004-07-22'),(41,41,'Forces Armées','2005-07-20'),(42,42,'Forces Armées','2009-09-27'),(43,43,'Forces Armées','1977-03-13'),(44,44,'Forces Armées','1972-05-13'),(45,45,'Forces Armées','1987-04-11'),(46,46,'Forces Armées','2010-03-03'),(47,47,'Forces Armées','2008-08-02'),(48,48,'Forces Armées','2007-10-20'),(49,49,'Forces Armées','2010-01-03'),(50,50,'Forces Armées','2009-08-24'),(51,51,'Forces Armées','1998-06-25'),(52,52,'Forces Armées','1980-10-22'),(53,53,'Forces Armées','2009-03-27'),(54,54,'Forces Armées','1990-12-15'),(55,55,'Forces Armées','1978-09-26'),(56,56,'Forces Armées','1991-02-20'),(57,57,'Forces Armées','2002-10-13'),(58,58,'Forces Armées','1982-03-03'),(59,59,'Forces Armées','1988-02-20'),(60,60,'Forces Armées','1984-01-23'),(61,61,'Forces Armées','1982-06-21'),(62,62,'Forces Armées','2000-01-07'),(63,63,'Forces Armées','2007-07-24'),(64,64,'Forces Armées','1980-01-09'),(65,65,'Forces Armées','1998-11-18'),(66,66,'Forces Armées','1978-11-22'),(67,67,'Forces Armées','2005-06-07'),(68,68,'Forces Armées','1986-04-20'),(69,69,'Forces Armées','1974-12-20'),(70,70,'Forces Armées','2004-09-01'),(71,71,'Forces Armées','1985-05-01'),(72,72,'Forces Armées','1992-09-09'),(73,73,'Forces Armées','2004-12-09'),(74,74,'Forces Armées','1985-10-06'),(75,75,'Forces Armées','2000-05-02'),(76,76,'Forces Armées','2012-08-09'),(77,77,'Forces Armées','1984-03-06'),(78,78,'Forces Armées','1990-11-24'),(79,79,'Forces Armées','2007-04-23'),(80,80,'Forces Armées','1989-07-27'),(81,81,'Forces Armées','1993-08-02'),(82,82,'Forces Armées','2002-12-17'),(83,83,'Forces Armées','2012-08-10'),(84,84,'Forces Armées','1996-05-21'),(85,85,'Forces Armées','2002-10-11'),(86,86,'Forces Armées','1973-01-07'),(87,87,'Forces Armées','2015-05-27'),(88,88,'Forces Armées','1999-09-02'),(89,89,'Forces Armées','1974-08-20'),(90,90,'Forces Armées','1972-06-07'),(91,91,'Forces Armées','1977-09-24'),(92,92,'Forces Armées','2014-05-07'),(93,93,'Forces Armées','2008-10-27'),(94,94,'Forces Armées','1998-09-21'),(95,95,'Forces Armées','1993-05-03'),(96,96,'Forces Armées','1977-02-15'),(97,97,'Forces Armées','2000-09-13'),(98,98,'Forces Armées','2003-07-23'),(99,99,'Forces Armées','2003-10-25'),(100,100,'Forces Armées','2008-06-17'),(101,3,'Gendarmerie Nationale','2005-10-23'),(102,3,'Forces Armées','2008-05-02'),(103,1,'Gendarmerie Nationale','2000-10-25'),(104,214,'Ecole Militaire','2002-10-16'),(105,214,'Gendarmerie Nationale','2004-05-25'),(106,214,'Ecole Militaire','2003-01-22'),(107,214,'Forces Armées','2003-04-20'),(108,214,'Ecole Militaire','2006-01-12'),(109,224,'Forces Armées','2016-04-01'),(110,224,'Forces Armées','2016-04-15'),(111,227,'Ecole Militaire','2016-04-08'),(112,227,'Gendarmerie Nationale','2016-04-13'),(113,5,'Gendarmerie Nationale','2012-01-01'),(115,5,'Forces Armées','2013-01-01'),(116,5,'Gendarmerie Nationale','2014-01-01'),(117,5,'Forces Armées','2015-01-01'),(118,5,'Ecole Militaire','2016-01-01'),(122,238,'Forces Armées','1990-02-28');
/*!40000 ALTER TABLE `AppartientRegiment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Archives`
--

DROP TABLE IF EXISTS `Archives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Archives` (
  `matricule` int(12) NOT NULL,
  `date_deces` date NOT NULL,
  `cause_deces` varchar(250) NOT NULL,
  PRIMARY KEY (`matricule`),
  CONSTRAINT `Archives_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Archives`
--

LOCK TABLES `Archives` WRITE;
/*!40000 ALTER TABLE `Archives` DISABLE KEYS */;
INSERT INTO `Archives` VALUES (225,'2016-04-02','mort au combat'),(226,'2016-04-01','mort de vieillesse'),(227,'2016-04-01','mort au combat'),(228,'2016-04-21','test'),(229,'2016-04-14','test'),(230,'2016-04-15','test'),(231,'2016-04-01','test effectué'),(236,'2016-05-05','test'),(237,'2016-05-05','test');
/*!40000 ALTER TABLE `Archives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Casernes`
--

DROP TABLE IF EXISTS `Casernes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Casernes` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `adresse` varchar(150) NOT NULL,
  `tel_standard` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Casernes`
--

LOCK TABLES `Casernes` WRITE;
/*!40000 ALTER TABLE `Casernes` DISABLE KEYS */;
INSERT INTO `Casernes` VALUES (1,'Jesse Q. Elliott','Ap #953-2717 Quam. Street','08 37 54 89 65'),(2,'Emi X. Orr','503-5721 Aenean Ave','09 85 36 00 75'),(3,'Tamekah N. Beard','P.O. Box 237, 1440 Luctus Av.','06 64 95 59 06'),(4,'Kasper W. Nixon','Ap #643-1516 Elit Ave','02 01 09 07 84'),(5,'Raya D. Rollins','Ap #751-7414 Egestas Ave','02 59 41 43 79'),(6,'Tiger A. Gallagher','P.O. Box 911, 7384 Ante Rd.','08 20 44 91 20'),(7,'Mollie Z. Luna','P.O. Box 961, 7806 Habitant Road','06 71 11 37 18'),(8,'Penelope H. Bray','P.O. Box 975, 6137 Molestie. Street','02 16 11 45 98'),(9,'Shellie H. Sutton','P.O. Box 322, 3580 Viverra. Rd.','03 66 32 51 94'),(10,'Guy R. Scott','Ap #296-4199 Vel Street','01 34 78 21 59');
/*!40000 ALTER TABLE `Casernes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ConditionsPromotions`
--

DROP TABLE IF EXISTS `ConditionsPromotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConditionsPromotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idGrade` int(11) NOT NULL,
  `annees_service_FA` int(11) NOT NULL,
  `annees_service_GN` int(11) NOT NULL,
  `annees_service_SOE` int(11) NOT NULL,
  `annees_service_grade` int(11) NOT NULL,
  `diplome` varchar(15) DEFAULT NULL,
  `diplomeSup1` varchar(15) DEFAULT NULL,
  `diplomeSup2` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idGrade` (`idGrade`),
  KEY `ConditionsPromotions_ibfk_2` (`diplome`),
  KEY `ConditionsPromotions_ibfk_3` (`diplomeSup1`),
  KEY `ConditionsPromotions_ibfk_4` (`diplomeSup2`),
  CONSTRAINT `ConditionsPromotions_ibfk_4` FOREIGN KEY (`diplomeSup2`) REFERENCES `Diplomes` (`acronyme`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `ConditionsPromotions_ibfk_1` FOREIGN KEY (`idGrade`) REFERENCES `Grades` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `ConditionsPromotions_ibfk_2` FOREIGN KEY (`diplome`) REFERENCES `Diplomes` (`acronyme`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `ConditionsPromotions_ibfk_3` FOREIGN KEY (`diplomeSup1`) REFERENCES `Diplomes` (`acronyme`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ConditionsPromotions`
--

LOCK TABLES `ConditionsPromotions` WRITE;
/*!40000 ALTER TABLE `ConditionsPromotions` DISABLE KEYS */;
INSERT INTO `ConditionsPromotions` VALUES (1,2,20,0,0,3,'DEMS',NULL,NULL),(2,3,17,0,0,4,'DEMS',NULL,NULL),(3,4,13,0,0,5,'CPOS','DEMS',NULL),(4,5,8,0,0,4,'DCS','CPOS','DEMS'),(5,6,0,0,0,2,'DCS',NULL,NULL),(6,6,0,0,0,3,'DCS',NULL,NULL),(7,7,0,0,11,1,'DQSG2',NULL,NULL),(8,7,15,14,0,1,'DQSG2',NULL,NULL),(9,8,0,0,10,2,'DQSG2',NULL,NULL),(10,8,14,0,0,3,'DQSG2',NULL,NULL),(11,8,0,13,0,3,'DQSG2',NULL,NULL),(12,9,0,0,8,3,'BT1',NULL,NULL),(13,9,11,0,0,4,'BT1',NULL,NULL),(14,9,0,10,0,4,'BT1',NULL,NULL),(15,10,0,0,5,3,'BE2',NULL,NULL),(16,10,7,0,0,3,'BE2',NULL,NULL),(17,10,0,5,0,4,'BE2',NULL,NULL),(18,11,4,0,0,2,'BS',NULL,NULL),(19,12,0,0,0,1,'CAT1',NULL,NULL),(20,13,0,0,0,1,'CAT1',NULL,NULL),(21,14,0,0,0,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `ConditionsPromotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ConditionsRetraites`
--

DROP TABLE IF EXISTS `ConditionsRetraites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConditionsRetraites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idGrade` int(11) NOT NULL,
  `service_effectif` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idGrade` (`idGrade`),
  CONSTRAINT `ConditionsRetraites_ibfk_1` FOREIGN KEY (`idGrade`) REFERENCES `Grades` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ConditionsRetraites`
--

LOCK TABLES `ConditionsRetraites` WRITE;
/*!40000 ALTER TABLE `ConditionsRetraites` DISABLE KEYS */;
INSERT INTO `ConditionsRetraites` VALUES (1,1,65,47),(2,2,60,42),(3,3,60,42),(4,4,60,42),(5,5,55,37),(7,6,55,37),(8,7,55,37),(10,8,53,35),(13,9,53,35),(16,10,50,32),(19,11,50,32),(20,12,45,27),(21,13,45,27),(22,14,45,27);
/*!40000 ALTER TABLE `ConditionsRetraites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DetientGrades`
--

DROP TABLE IF EXISTS `DetientGrades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DetientGrades` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` int(12) NOT NULL,
  `id` int(11) NOT NULL,
  `date_promotion` date NOT NULL,
  PRIMARY KEY (`num`),
  KEY `DetientGrades_ibfk_1` (`matricule`),
  KEY `DetientGrades_ibfk_2` (`id`),
  CONSTRAINT `DetientGrades_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `DetientGrades_ibfk_2` FOREIGN KEY (`id`) REFERENCES `Grades` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DetientGrades`
--

LOCK TABLES `DetientGrades` WRITE;
/*!40000 ALTER TABLE `DetientGrades` DISABLE KEYS */;
INSERT INTO `DetientGrades` VALUES (1,1,1,'1997-05-18'),(2,2,8,'2015-03-01'),(3,3,7,'2005-11-25'),(4,4,7,'2008-05-16'),(5,5,7,'1980-03-12'),(6,6,6,'1996-12-08'),(7,7,9,'1984-07-08'),(8,8,5,'2000-02-03'),(9,9,6,'2005-11-25'),(10,10,7,'2008-05-04'),(11,11,10,'2013-10-20'),(12,12,4,'2011-11-01'),(13,13,10,'1997-07-12'),(14,14,7,'2015-03-10'),(15,15,6,'1985-03-01'),(16,16,8,'2000-07-27'),(17,17,12,'1998-04-14'),(18,18,5,'2001-09-03'),(19,19,7,'2000-08-06'),(20,20,7,'1989-09-13'),(21,21,7,'2009-09-22'),(22,22,7,'1996-02-23'),(23,23,8,'2013-05-26'),(24,24,8,'2003-03-15'),(25,25,7,'1990-11-15'),(26,26,7,'1998-06-22'),(27,27,8,'1981-11-02'),(28,28,7,'2005-05-03'),(29,29,7,'2004-02-07'),(30,30,7,'1983-11-18'),(31,31,9,'1987-11-01'),(32,32,9,'2004-03-19'),(33,33,6,'2014-10-17'),(34,34,9,'2014-11-15'),(35,35,10,'2008-08-13'),(36,36,6,'1991-01-22'),(37,37,9,'2004-11-04'),(38,38,9,'2013-12-05'),(39,39,7,'1999-05-12'),(40,40,13,'2015-10-25'),(41,41,7,'2005-07-20'),(42,42,6,'2013-02-09'),(43,43,7,'1977-03-13'),(44,44,9,'1981-02-19'),(45,45,6,'2006-09-14'),(46,46,11,'2013-09-13'),(47,47,8,'2012-09-06'),(48,48,7,'2007-10-20'),(49,49,7,'2010-01-03'),(50,50,7,'2009-08-24'),(51,51,3,'2005-01-10'),(52,52,7,'1985-12-27'),(53,53,11,'2013-10-15'),(54,54,9,'2006-05-04'),(55,55,7,'2006-10-12'),(56,56,7,'1991-02-20'),(57,57,6,'2004-10-16'),(58,58,9,'1989-02-12'),(59,59,10,'2015-08-04'),(60,60,7,'1984-01-23'),(61,61,6,'1989-11-08'),(62,62,9,'2000-09-15'),(63,63,6,'2013-01-13'),(64,64,7,'1980-01-09'),(65,65,6,'1998-04-06'),(66,66,6,'2002-04-08'),(67,67,9,'2005-08-21'),(68,68,8,'1992-09-22'),(69,69,7,'1974-12-20'),(70,70,8,'2007-02-10'),(71,71,7,'1985-05-01'),(72,72,9,'2000-07-02'),(73,73,9,'2005-05-03'),(74,74,7,'2007-09-03'),(75,75,6,'2004-05-24'),(76,76,7,'2012-08-09'),(77,77,5,'2006-08-15'),(78,78,7,'1990-11-24'),(79,79,7,'2011-03-10'),(80,80,8,'2006-02-21'),(81,81,6,'1997-11-20'),(82,82,8,'2012-08-02'),(84,84,7,'2012-08-26'),(85,85,7,'2002-10-11'),(86,86,7,'1973-01-07'),(87,87,9,'2015-01-06'),(88,88,7,'1999-09-02'),(89,89,7,'1974-08-20'),(90,90,7,'1972-06-07'),(91,91,8,'2012-06-05'),(92,92,11,'2015-10-22'),(93,93,6,'2011-04-26'),(94,94,7,'1998-09-21'),(95,95,7,'1993-05-03'),(96,96,6,'1995-08-03'),(97,97,6,'2005-05-12'),(98,98,3,'2004-12-14'),(99,99,10,'2014-02-24'),(100,100,9,'2013-07-17'),(101,3,6,'2013-12-03'),(102,12,12,'2013-05-12'),(103,10,7,'2010-12-03'),(104,227,6,'2016-04-20'),(105,214,7,'2005-12-02'),(106,214,10,'2016-04-01'),(112,10,6,'2016-04-01'),(113,6,3,'2016-05-01'),(122,238,7,'2016-05-02');
/*!40000 ALTER TABLE `DetientGrades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Diplomes`
--

DROP TABLE IF EXISTS `Diplomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Diplomes` (
  `acronyme` varchar(15) NOT NULL,
  `intitule` varchar(150) NOT NULL,
  PRIMARY KEY (`acronyme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Diplomes`
--

LOCK TABLES `Diplomes` WRITE;
/*!40000 ALTER TABLE `Diplomes` DISABLE KEYS */;
INSERT INTO `Diplomes` VALUES ('BA1','Brevet d\'armes du 1er degré'),('BA2','Brevet d\'armes du 2nd degré'),('BAT','Brevet d\'aptitude technique'),('BCG','Brevet de chef de groupe'),('BE1','Brevet élémentaire du 1er degré'),('BE2','Brevet élémentaire du 2nd degré'),('BEAT','Brevet élémentaire d\'aptitude technique'),('BEE','Brevet élémentaire des équipages'),('BES','Brevet élémentaire de spécialité'),('BS','Brevet supérieur'),('BT1','Brevet technique numéro 1 (ABC, artillerie du génie)'),('BT2','Brevet technique numéro 2'),('CAT1','Certificat d\'aptitude technique numéro 1'),('CAT2','Certificat d\'aptitude technique numéro 2'),('CI','Certificat interarmes'),('CPOS','Certificat de perfectionnement des officiers subalternes'),('CTOEMS','Certificat technique d\'officier des écoles militaires des services'),('DA','Diplôme d\'application'),('DBSO','Diplôme de base de sous-officier'),('DC','Diplôme de commissariat'),('DCBA','Diplôme de commandant de bataillon'),('DCBCG','Diplôme du cours de brigade de capitaine de gendarmerie'),('DCBR','Diplôme de commandant de brigade'),('DCC','Diplôme du cours des capitaines'),('DCP','Diplôme de commissaire de police'),('DCR','Diplôme de commandant de régiment'),('DCS','Diplôme de chef de section'),('DD','Diplôme de doctorat de médecines, de pharmacie et de vétérinaire'),('DEM','Diplôme d\'état-major'),('DEMS','Diplôme d\'enseignement militaire supérieur'),('DI','Diplôme d\'ingénieur'),('DLSI','Diplôme licence en soins infirmiers'),('DM','Diplôme de magistrat'),('DOEA','Diplôme d\'officier d\'école d\'administration'),('DOG','Diplôme d\'officier de gendarmerie'),('DOPJ','Diplôme d\'officier de police judiciaire'),('DQSG1','Diplôme de qualification supérieur de gendarmerie numéro 1'),('DQSG2','Diplôme de qualification supérieur de gendarmerie numéro 2'),('DTEMA','Diplôme technique des écoles militaires et assimilés'),('DTS','Diplôme de techniciens supérieurs'),('DU3','Diplôme universitaire de 3ème cycle obtenus dans les écoles civiles');
/*!40000 ALTER TABLE `Diplomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DiplomesEquivalences`
--

DROP TABLE IF EXISTS `DiplomesEquivalences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DiplomesEquivalences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `diplome` varchar(15) NOT NULL,
  `diplomeEqui1` varchar(15) DEFAULT NULL,
  `diplomeEqui2` varchar(15) DEFAULT NULL,
  `diplomeEqui3` varchar(15) DEFAULT NULL,
  `diplomeEqui4` varchar(15) DEFAULT NULL,
  `diplomeEqui5` varchar(15) DEFAULT NULL,
  `diplomeEqui6` varchar(15) DEFAULT NULL,
  `diplomeEqui7` varchar(15) DEFAULT NULL,
  `diplomeEqui8` varchar(15) DEFAULT NULL,
  `diplomeEqui9` varchar(15) DEFAULT NULL,
  `diplomeEqui10` varchar(15) DEFAULT NULL,
  `diplomeEqui11` varchar(15) DEFAULT NULL,
  `diplomeEqui12` varchar(15) DEFAULT NULL,
  `diplomeEqui13` varchar(15) DEFAULT NULL,
  `diplomeEqui14` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diplome` (`diplome`),
  KEY `diplomeEqui1` (`diplomeEqui1`),
  KEY `diplomeEqui2` (`diplomeEqui2`),
  KEY `diplomeEqui3` (`diplomeEqui3`),
  KEY `diplomeEqui4` (`diplomeEqui4`),
  KEY `diplomeEqui5` (`diplomeEqui5`),
  KEY `diplomeEqui6` (`diplomeEqui6`),
  KEY `diplomeEqui7` (`diplomeEqui7`),
  KEY `diplomeEqui8` (`diplomeEqui8`),
  KEY `diplomeEqui9` (`diplomeEqui9`),
  KEY `diplomeEqui10` (`diplomeEqui10`),
  KEY `diplomeEqui11` (`diplomeEqui11`),
  KEY `diplomeEqui12` (`diplomeEqui12`),
  KEY `diplomeEqui13` (`diplomeEqui13`),
  KEY `diplomeEqui14` (`diplomeEqui14`),
  CONSTRAINT `DiplomesEquivalences_ibfk_1` FOREIGN KEY (`diplome`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_10` FOREIGN KEY (`diplomeEqui9`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_11` FOREIGN KEY (`diplomeEqui10`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_12` FOREIGN KEY (`diplomeEqui11`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_13` FOREIGN KEY (`diplomeEqui12`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_14` FOREIGN KEY (`diplomeEqui13`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_15` FOREIGN KEY (`diplomeEqui14`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_2` FOREIGN KEY (`diplomeEqui1`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_3` FOREIGN KEY (`diplomeEqui2`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_4` FOREIGN KEY (`diplomeEqui3`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_5` FOREIGN KEY (`diplomeEqui4`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_6` FOREIGN KEY (`diplomeEqui5`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_7` FOREIGN KEY (`diplomeEqui6`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_8` FOREIGN KEY (`diplomeEqui7`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE,
  CONSTRAINT `DiplomesEquivalences_ibfk_9` FOREIGN KEY (`diplomeEqui8`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DiplomesEquivalences`
--

LOCK TABLES `DiplomesEquivalences` WRITE;
/*!40000 ALTER TABLE `DiplomesEquivalences` DISABLE KEYS */;
INSERT INTO `DiplomesEquivalences` VALUES (1,'DEMS','DEM','DI','DTEMA','DD','DM','DU3','DC','DOEA','DCC','DCP','DOPJ','DCR','DCBR','DCBA'),(2,'CPOS','DEMS','DTS','DOG','DCBCG','CTOEMS','DOEA','DLSI',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'DCS','CPOS','DQSG2','DA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'DQSG2','BT1','BT2','BA2','BS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'BT1','BE2','DQSG1','BAT','BA1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'BE2','BES','DOPJ','DBSO','BCG','CI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'BES','CAT2','BE2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'CAT1','BE1','BEE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `DiplomesEquivalences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Droits`
--

DROP TABLE IF EXISTS `Droits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Droits` (
  `role` varchar(25) NOT NULL DEFAULT '',
  `noRights` tinyint(1) NOT NULL DEFAULT '0',
  `allRights` tinyint(1) NOT NULL DEFAULT '0',
  `seeOwnFolderModule` tinyint(1) NOT NULL DEFAULT '1',
  `editOwnFolderPersonalInformation` tinyint(1) NOT NULL DEFAULT '1',
  `listCreatedFolder` tinyint(1) NOT NULL DEFAULT '0',
  `listAllFolder` tinyint(1) NOT NULL DEFAULT '0',
  `seeCreatedFolder` tinyint(1) NOT NULL DEFAULT '0',
  `seeAllFolder` tinyint(1) NOT NULL DEFAULT '0',
  `createFolder` tinyint(1) NOT NULL DEFAULT '0',
  `addElementToAFolder` tinyint(1) NOT NULL DEFAULT '0',
  `addElementToAFolderCreated` tinyint(1) NOT NULL DEFAULT '0',
  `editInformationIfAuthor` tinyint(1) NOT NULL DEFAULT '0',
  `editInformation` tinyint(1) NOT NULL DEFAULT '0',
  `deleteInformation` tinyint(1) NOT NULL DEFAULT '0',
  `useFileToAddFolders` tinyint(1) NOT NULL DEFAULT '0',
  `listEligible` tinyint(1) NOT NULL DEFAULT '0',
  `editEligibleCondition` tinyint(1) NOT NULL DEFAULT '0',
  `addEligibleCondition` tinyint(1) NOT NULL DEFAULT '0',
  `suprEligibleCondition` tinyint(1) NOT NULL DEFAULT '0',
  `canRetireAFolder` tinyint(1) NOT NULL DEFAULT '0',
  `canArchiveAFolder` tinyint(1) DEFAULT '0',
  `editEligibleEmailContent` tinyint(1) NOT NULL DEFAULT '0',
  `uploadFileForMail` tinyint(1) NOT NULL DEFAULT '0',
  `changePieceJointeForEligibleMail` tinyint(1) NOT NULL DEFAULT '0',
  `seeAllFolderWithoutAccount` tinyint(1) NOT NULL DEFAULT '0',
  `seeAllAccount` tinyint(1) NOT NULL DEFAULT '0',
  `createAccount` tinyint(1) NOT NULL DEFAULT '0',
  `alterMdp` tinyint(1) NOT NULL DEFAULT '0',
  `alterAccountRight` tinyint(1) NOT NULL DEFAULT '0',
  `deleteAccount` tinyint(1) NOT NULL DEFAULT '0',
  `seeAllConstanteTable` tinyint(1) NOT NULL DEFAULT '0',
  `editInAConstanteTable` tinyint(1) NOT NULL DEFAULT '0',
  `deleteInAConstanteTable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role`),
  UNIQUE KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Droits`
--

LOCK TABLES `Droits` WRITE;
/*!40000 ALTER TABLE `Droits` DISABLE KEYS */;
INSERT INTO `Droits` VALUES ('admin',0,0,1,1,1,1,0,1,1,1,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),('cadre',0,0,1,1,1,1,0,1,1,1,0,0,1,1,1,1,1,1,0,1,0,1,1,1,1,1,1,1,1,0,0,0,0),('secretaire',0,0,1,1,1,0,1,0,1,0,1,1,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),('superAdmin',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),('utilisateur',0,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `Droits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Grades`
--

DROP TABLE IF EXISTS `Grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade` varchar(150) NOT NULL,
  `hierarchie` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Grades`
--

LOCK TABLES `Grades` WRITE;
/*!40000 ALTER TABLE `Grades` DISABLE KEYS */;
INSERT INTO `Grades` VALUES (1,'General d\'Armee ou Amiral',1),(2,'Colonel ou capitaine de vaisseau',2),(3,'Lieutenant-colonel ou capitaine de fregate',3),(4,'Commandant ou capitaine de corvette',4),(5,'Capitaine ou lieutenant de vaisseau',5),(6,'Lieutenant ou enseigne de vaisseau de 1er classe',6),(7,'Sous-Lieutenant ou enseigne de vaisseau de 2nd classe',7),(8,'Adjudant-chef ou maître principal',8),(9,'Adjudant ou premier maître',9),(10,'Sergent-chef, maître maréchal de logis chef',10),(11,'Sergent ou second maître',11),(12,'Caporal-chef ou quartier-maître 1er classe',12),(13,'Caporal ou quartier maître 2nd classe',13),(14,'Soldat, matelot',14);
/*!40000 ALTER TABLE `Grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Militaires`
--

DROP TABLE IF EXISTS `Militaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Militaires` (
  `matricule` int(12) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `date_naissance` date NOT NULL,
  `genre` varchar(1) NOT NULL,
  `tel1` varchar(20) NOT NULL,
  `tel2` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `adresse` varchar(250) NOT NULL,
  `date_recrutement` date NOT NULL,
  PRIMARY KEY (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Militaires`
--

LOCK TABLES `Militaires` WRITE;
/*!40000 ALTER TABLE `Militaires` DISABLE KEYS */;
INSERT INTO `Militaires` VALUES (1,'Wesley','Clara','1974-08-11','H','09 87 18 28 90','09 89 23 86 63','ut.cursus@velitQuisque.org','Ap #939-4538 Faucibus Avenue','1997-05-14'),(2,'Brooks','Cédric','1990-11-07','M','09 55 71 04 96','05 80 32 72 33','Quisque@dictumPhasellusin.com','356-541 Aliquam Avenue','2015-02-19'),(3,'Hammond','Lilou','1980-10-07','M','08 24 70 41 68','06 99 74 11 83','web-VWu10k@mail-tester.com','3325 Lorem St.','2000-08-10'),(4,'Harrell','Malo','1987-05-18','H','06 90 68 95 44','08 05 32 63 19','dignissim.pharetra.Nam@placeratorcilacus.net','Ap #712-2229 Sem Road','2008-05-16'),(5,'Trevino','Agathe','1960-04-17','M','07 80 70 07 32','01 20 24 54 38','plabadille@gmail.com','521 Integer St.','1980-03-12'),(6,'Webster','Baptiste','1973-05-05','M','05 06 53 59 30','08 46 48 31 05','dolor@Proinvelnisl.org','737-7873 Adipiscing Ave','1993-11-12'),(7,'Burt','Léonie','1960-06-02','M','07 60 01 42 79','05 73 16 38 47','amet.risus.Donec@neque.co.uk','P.O. Box 407, 2322 Suspendisse Street','1979-12-18'),(8,'Hanson','Colin','1956-03-19','M','08 47 27 73 54','08 70 01 81 89','metus@auctornonfeugiat.com','1660 Ac Road','1977-03-04'),(9,'Rojas','Margot','1958-11-20','M','07 70 79 05 96','08 60 22 41 38','euismod.enim.Etiam@suscipitestac.org','Ap #340-7427 Scelerisque Rd.','1981-06-01'),(10,'Dotson','Léon','1984-12-22','H','07 46 26 83 96','01 17 32 41 10','aliquam.eros.turpis@nonummyipsumnon.co.uk','778-4706 Quisque St.','2008-05-04'),(11,'Castaneda','Lucas','1986-08-04','M','08 78 47 26 40','08 20 17 53 83','contact@le-timsam.com','P.O. Box 539, 4077 Elit. Road','2005-06-18'),(12,'Salinas','Titouan','1957-09-13','M','09 36 03 65 35','06 01 15 24 62','Fusce.aliquet@ipsumdolorsit.ca','Ap #994-1257 Tincidunt St.','1982-10-04'),(13,'Dudley','Dimitri','1975-05-13','M','05 88 06 22 45','04 31 26 47 45','in.consectetuer.ipsum@atarcu.net','8154 Magna Ave','1997-06-20'),(14,'Thompson','Lamia','1989-06-25','M','09 92 89 00 80','03 23 83 54 39','arcu@vitaeorci.net','Ap #445-6312 Eget, Av.','2014-02-25'),(15,'Nicholson','Maïwenn','1953-02-18','M','01 48 20 33 78','06 97 80 36 12','et.malesuada.fames@ultricies.ca','5238 Nullam St.','1976-02-23'),(16,'Hensley','Alexis','1969-12-23','M','09 57 29 25 12','03 80 45 09 92','egestas.urna.justo@ligulaDonecluctus.net','P.O. Box 193, 6937 Et St.','1989-08-02'),(17,'Terry','Louise','1970-07-25','M','08 10 18 84 51','09 24 30 99 73','facilisi.Sed.neque@ametconsectetuer.org','P.O. Box 913, 7846 Morbi St.','1990-11-03'),(18,'Park','Bastien','1970-08-24','M','04 01 92 30 89','07 95 02 81 29','faucibus@lacuspedesagittis.ca','3385 Nonummy. Rd.','1995-06-13'),(19,'Fuentes','Luna','1975-10-16','M','03 63 76 43 71','07 10 15 40 55','justo.faucibus@MaurisnullaInteger.edu','P.O. Box 765, 6142 Interdum St.','2000-08-06'),(20,'Walter','Mathis','1965-09-02','M','09 91 92 82 70','01 87 02 52 23','tristique.ac.eleifend@diamlorem.net','Ap #352-4836 Orci. St.','1989-09-13'),(21,'Booker','Félix','1986-09-09','M','07 09 09 54 35','05 04 68 18 19','nibh.Aliquam.ornare@tempuseu.edu','359-9125 Dui. St.','2009-09-22'),(22,'Stephenson','Benjamin','1977-07-04','M','09 42 38 12 33','06 85 81 96 90','neque.Morbi@convallisligula.co.uk','Ap #612-2570 Lacus. Street','1996-02-23'),(23,'Serrano','Margaux','1980-08-18','M','03 08 23 34 75','02 28 69 02 19','enim.Etiam@ligula.com','P.O. Box 697, 6905 Ac Street','2004-09-18'),(24,'Johnston','Dorian','1961-07-16','M','02 48 58 98 07','04 84 73 12 07','ac@tempus.org','P.O. Box 235, 4476 Euismod Street','1981-04-15'),(25,'Fitzgerald','Hugo','1971-10-24','M','05 91 35 93 26','02 09 34 58 53','aliquam.eu.accumsan@euismodindolor.com','P.O. Box 747, 3327 Ullamcorper, Rd.','1990-11-15'),(26,'Trevino','Lola','1963-10-08','M','02 18 28 21 58','03 83 77 19 75','Proin.nisl@metusAliquamerat.com','629-6835 Lorem, Ave','1988-08-04'),(27,'Snow','Pauline','1952-08-18','M','02 96 31 33 12','06 82 91 29 52','sed.turpis.nec@eget.net','975-174 Erat Street','1974-02-15'),(28,'Strickland','Anna','1980-07-01','M','01 15 82 38 71','02 72 25 99 56','sem.magna@duiaugueeu.net','P.O. Box 280, 194 Maecenas Rd.','2005-05-03'),(29,'Pena','Simon','1985-12-15','M','03 57 30 64 05','07 96 74 98 23','odio.Nam.interdum@scelerisqueneque.co.uk','P.O. Box 609, 9705 Volutpat. Street','2004-02-07'),(30,'Guerrero','Erwan','1964-09-14','M','03 13 59 20 58','09 46 21 12 77','varius@diam.edu','410-6611 In Road','1983-11-18'),(31,'Massey','Amine','1956-03-08','M','02 53 71 42 56','02 96 11 87 91','nunc.ullamcorper@Aliquamvulputateullamcorper.net','P.O. Box 848, 5063 Auctor Avenue','1979-03-05'),(32,'Thomas','Renaud','1966-08-07','M','09 90 93 95 18','01 85 94 52 57','blandit@odioEtiamligula.edu','2956 Amet, Rd.','1987-07-10'),(33,'Cotton','Constant','1984-12-07','M','09 08 80 30 27','08 44 57 12 43','commodo@sagittisaugue.ca','5099 Tempor Av.','2003-04-26'),(34,'Baldwin','Maïlé','1986-09-19','M','06 74 50 20 44','03 39 13 25 10','at.sem.molestie@vestibulummassa.co.uk','Ap #713-7412 Sed Road','2010-06-13'),(35,'Massey','Maéva','1956-07-21','M','05 97 39 42 44','04 03 77 01 43','vitae.odio.sagittis@nostra.edu','P.O. Box 716, 9082 Egestas Rd.','1977-10-01'),(36,'Sellers','Amine','1959-06-03','M','09 17 51 90 75','03 18 54 72 79','volutpat@Duisrisus.com','P.O. Box 510, 5965 Dui Road','1983-04-04'),(37,'Wilder','Clément','1956-08-05','M','03 44 48 10 60','06 35 98 63 66','urna.Nullam@dolorsitamet.org','Ap #740-7745 Et Av.','1976-12-25'),(38,'Grant','Pierre','1975-08-08','M','09 38 71 01 84','01 20 64 82 34','ac.nulla@Aliquam.co.uk','399-6868 Orci. Avenue','1998-04-24'),(39,'Quinn','Nathan','1979-08-15','M','01 98 27 06 28','03 49 42 20 23','cursus.Integer.mollis@mipedenonummy.net','P.O. Box 390, 2119 Mi Av.','1999-05-12'),(40,'Alvarez','Théo','1983-12-20','M','07 84 19 36 84','09 99 22 02 01','ullamcorper.eu.euismod@DonecestNunc.com','7592 Ultricies Rd.','2004-07-22'),(41,'Franks','Titouan','1986-09-15','M','07 39 51 36 49','02 24 06 89 77','Aliquam.ultrices.iaculis@molestieorcitincidunt.net','451-5888 Sit Street','2005-07-20'),(42,'Rowland','Anaëlle','1985-05-10','M','09 44 83 76 34','03 21 23 38 68','pharetra.Quisque.ac@tellus.edu','Ap #763-9447 Et, Ave','2009-09-27'),(43,'Griffin','Mathieu','1952-06-14','M','06 85 24 89 93','04 26 85 27 54','tellus.eu@vel.co.uk','P.O. Box 455, 2931 Vulputate, Av.','1977-03-13'),(44,'Mccarthy','Amandine','1952-05-19','M','05 03 68 21 87','01 73 43 44 49','Phasellus.vitae@arcuCurabiturut.edu','671-7843 Duis Rd.','1972-05-13'),(45,'Dawson','Valentine','1967-06-24','M','01 77 36 78 15','06 02 08 32 56','erat.in.consectetuer@neccursusa.org','P.O. Box 307, 4004 Tortor, Street','1987-04-11'),(46,'Sparks','Davy','1990-08-24','M','05 14 05 89 75','05 91 73 90 47','eros.turpis.non@mauris.org','106-2313 Consectetuer Avenue','2010-03-03'),(47,'Ingram','Lutécia','1983-06-01','M','01 73 53 70 57','07 92 50 35 56','tempus.scelerisque.lorem@blanditcongueIn.com','893 Maecenas St.','2008-08-02'),(48,'Guy','Justine','1985-10-20','M','04 51 69 05 16','07 42 02 48 74','purus@interdumSedauctor.co.uk','Ap #732-5515 Tincidunt Avenue','2007-10-20'),(49,'Britt','Nolan','1985-10-20','M','01 35 87 41 38','01 83 13 92 76','at.nisi.Cum@CuraePhasellusornare.co.uk','Ap #142-1663 At, Road','2010-01-03'),(50,'Stafford','Adrien','1989-08-15','M','01 30 39 59 21','03 99 44 78 12','lobortis.ultrices.Vivamus@necluctusfelis.net','Ap #236-5682 Imperdiet Rd.','2009-08-24'),(51,'Mccarty','Erwan','1979-07-09','M','09 34 22 87 73','03 39 96 82 50','ornare.lectus@Vivamus.ca','979-4162 Feugiat. Rd.','1998-06-25'),(52,'Caldwell','Dylan','1961-05-07','M','05 01 56 13 13','09 41 81 38 03','cursus.et@Phasellus.com','399 Vestibulum Avenue','1980-10-22'),(53,'Mayo','Lutécia','1990-11-22','M','09 79 63 83 85','08 21 08 23 58','elit.dictum.eu@DonecestNunc.co.uk','672-7797 Convallis Street','2009-03-27'),(54,'Cash','Catherine','1970-07-22','M','09 53 83 30 98','03 23 46 03 79','ante.ipsum@malesuadafames.net','P.O. Box 697, 2986 Nec Av.','1990-12-15'),(55,'Cole','Clément','1954-04-16','M','03 74 97 74 34','02 21 61 34 01','quis.urna@odioEtiam.com','Ap #844-6503 Pretium Av.','1978-09-26'),(56,'Valdez','Tom','1970-05-18','M','07 63 29 74 56','09 20 46 30 75','ut.mi@venenatis.co.uk','911-9307 Consectetuer, Ave','1991-02-20'),(57,'Morin','Mathis','1980-06-12','M','05 31 42 54 26','08 21 70 47 01','urna.Vivamus.molestie@semper.edu','766-5145 Luctus Street','2002-10-13'),(58,'Cummings','Mathieu','1958-10-08','M','08 21 44 98 55','06 33 47 44 02','eu.dolor.egestas@PraesentluctusCurabitur.org','Ap #651-8530 Etiam Avenue','1982-03-03'),(59,'Strong','Capucine','1966-12-15','M','07 77 32 82 90','05 49 02 19 62','erat.eget.tincidunt@cubiliaCuraeDonec.net','368-5350 Mauris Avenue','1988-02-20'),(60,'Nielsen','Thomas','1961-08-20','M','06 13 39 96 68','04 15 70 39 84','diam.dictum@elementumsem.org','P.O. Box 254, 4841 Tortor, St.','1984-01-23'),(61,'Massey','Tom','1961-10-18','F','05 99 69 81 63','05 45 00 49 56','Cras.dictum.ultricies@afacilisis.edu','822-6003 Diam. St.','1982-06-21'),(62,'Diaz','Rosalie','1976-07-21','F','05 99 55 00 95','09 48 27 72 59','auctor.vitae@dapibus.co.uk','987-9574 Varius Av.','2000-01-07'),(63,'Burns','Syrine','1987-12-11','F','03 04 13 03 80','01 22 34 95 20','Duis@Sedid.co.uk','Ap #267-1663 Purus Rd.','2007-07-24'),(64,'Gould','Guillaume','1955-12-25','F','02 13 80 81 02','06 30 95 07 01','luctus.felis@metusVivamuseuismod.edu','481-3640 Ad St.','1980-01-09'),(65,'Faulkner','Alexandre','1976-03-25','F','01 24 01 36 52','09 66 89 17 39','odio.Phasellus.at@cursusin.com','326-8830 Integer Ave','1998-11-18'),(66,'Woodward','Noah','1953-09-26','F','04 80 05 65 64','07 28 78 37 68','ipsum@dapibusligulaAliquam.ca','Ap #876-5878 Curabitur Rd.','1978-11-22'),(67,'Cooke','Sara','1980-06-25','F','09 49 82 35 39','02 57 89 78 53','vulputate.ullamcorper@molestiepharetra.edu','Ap #998-1443 Nunc Rd.','2005-06-07'),(68,'Young','Marwane','1965-06-06','F','01 12 79 85 72','03 58 63 94 20','eu@non.org','P.O. Box 166, 7902 Sem Av.','1986-04-20'),(69,'Castaneda','Romane','1950-11-21','F','07 37 53 19 42','04 91 33 65 80','Donec.porttitor.tellus@Aenean.org','P.O. Box 428, 8753 Ornare, Avenue','1974-12-20'),(70,'Castillo','Inès','1985-10-09','F','02 49 19 39 76','02 83 24 55 01','Morbi.neque.tellus@duiCraspellentesque.ca','5231 Volutpat. Street','2004-09-01'),(71,'Jenkins','Syrine','1963-05-13','F','07 03 40 25 13','05 86 98 53 12','Mauris.vestibulum@Donecnonjusto.com','456-5584 Sapien, Street','1985-05-01'),(72,'Hutchinson','Jules','1969-11-20','F','02 78 90 66 12','03 85 45 70 27','lobortis@netuset.edu','P.O. Box 146, 2019 Pellentesque Rd.','1992-09-09'),(73,'Bullock','Léonie','1982-02-09','F','09 91 36 39 89','04 70 55 61 65','semper.dui@mollisDuis.com','853-4161 Tincidunt Rd.','2004-12-09'),(74,'Walker','Jérémy','1966-03-12','F','04 98 28 25 35','08 60 95 66 44','Nullam@sedduiFusce.org','156 Egestas St.','1985-10-06'),(75,'Steele','Jasmine','1981-09-04','F','04 12 14 89 29','02 62 05 05 29','per.inceptos@bibendumfermentummetus.co.uk','692-5862 Pellentesque. Rd.','2000-05-02'),(76,'Harper','Laura','1987-10-23','F','01 49 94 17 32','08 60 58 78 99','ullamcorper.Duis@Phasellusin.co.uk','Ap #565-2438 Neque. Avenue','2012-08-09'),(77,'Mccarthy','Angelina','1962-04-10','F','06 96 98 51 07','07 04 01 32 88','pellentesque@sociis.org','602 Non St.','1984-03-06'),(78,'Brewer','Antonin','1966-09-22','F','02 91 61 68 46','03 20 61 82 90','iaculis.odio@est.com','2000 Nulla. Rd.','1990-11-24'),(79,'Montgomery','Guillaume','1983-03-20','F','02 77 49 90 89','03 35 98 62 87','tortor.Integer.aliquam@Donecegestas.edu','Ap #947-4694 Sit Rd.','2007-04-23'),(80,'Simpson','Léane','1970-01-25','F','01 74 49 43 19','03 25 78 13 21','nibh@viverraMaecenasiaculis.co.uk','862-1774 Ante Road','1989-07-27'),(81,'Hodges','Samuel','1969-01-19','M','02 42 05 29 79','05 58 35 13 58','non.Meugiat.nec@Curabitursed.ca','P.O. Box 362, 4099 Adipiscing St.','1993-08-02'),(82,'Curtis','Clotilde','1980-05-08','M','01 94 32 00 84','02 85 73 24 09','Sed@adipiscingnonluctus.org','Ap #485-5775 Ullamcorper Street','2002-12-17'),(83,'Poole','Marwane','1990-06-08','M','03 66 41 15 40','07 61 54 58 76','urna.et@diamnuncullamcorper.net','6253 Dapibus Street','2012-08-10'),(84,'Sellers','Colin','1972-06-25','M','06 88 24 26 13','01 95 84 55 80','Vivamus@tellus.com','P.O. Box 677, 7278 Velit. St.','1996-05-21'),(85,'Russell','Guillemette','1979-02-05','M','07 66 15 11 10','09 43 87 67 95','Morbi.non@gravida.edu','609 Metus Rd.','2002-10-11'),(86,'Donovan','Simon','1953-02-14','M','04 79 30 29 51','04 84 48 99 86','commodo@temporestac.co.uk','9666 Magna. St.','1973-01-07'),(87,'Rice','Emma','1990-12-21','M','09 39 86 80 49','08 23 96 70 35','eget@semper.com','Ap #114-6602 Consectetuer Rd.','2015-05-27'),(88,'Mrancis','Inès','1974-09-13','M','05 52 72 65 98','08 49 76 54 99','imperdiet.non.vestibulum@molestietellus.net','Ap #602-7215 Tincidunt St.','1999-09-02'),(89,'Aguilar','Yasmine','1953-10-24','H','04 15 12 13 44','09 56 35 70 20','nisl.elementum@maurisid.ca','av. 10 purple house - alberta','1974-08-20'),(90,'Miles','Kylian','1952-06-07','M','06 65 20 81 16','07 86 63 83 30','at.velit@quispede.edu','Ap #810-3908 Sem Av.','1972-06-07'),(91,'Perry','Lilou','1954-07-21','F','08 91 78 93 92','06 71 68 17 96','nisi.Aenean@accumsaninterdumlibero.ca','118-3156 Massa. Ave','1977-09-24'),(92,'Burton','Léa','1989-09-08','F','05 25 79 79 33','04 77 78 50 49','Nunc.sollicitudin@hendreritneque.net','Ap #273-6918 Elit Ave','2014-05-07'),(93,'Gallagher','Léane','1984-08-07','F','06 54 23 83 05','08 49 54 64 51','sed@dignissim.org','P.O. Box 855, 9593 Rutrum Rd.','2008-10-27'),(94,'Beard','Malo','1978-12-26','F','01 74 20 70 47','04 34 38 77 70','a.ultricies@necenimNunc.org','6198 Est. Av.','1998-09-21'),(95,'Frye','Jeanne','1972-04-10','F','05 66 04 31 99','03 44 82 62 02','pellentesque@Seddictum.co.uk','Ap #111-5001 Duis Avenue','1993-05-03'),(96,'Noel','Corentin','1955-10-12','F','09 33 35 77 08','02 39 81 38 30','velit.Aliquam.nisl@ultrices.net','6766 Ut Av.','1977-02-15'),(97,'Pierce','Félix','1980-07-25','F','04 51 49 04 88','05 56 15 01 37','Curabitur.egestas.nunc@eterosProin.edu','P.O. Box 383, 4855 Dis Av.','2000-09-13'),(98,'Vasquez','Sara','1978-01-24','F','01 51 60 10 66','07 51 99 28 07','velit.justo@faucibus.edu','395-3849 Ullamcorper St.','2003-07-23'),(99,'Black','Lucas','1984-09-08','F','04 35 51 16 18','08 84 28 67 66','auctor.Mauris.vel@Integerin.com','8167 Mattis. Rd.','2003-10-25'),(100,'Nieves','Bastien','1984-09-15','F','05 70 81 79 89','08 31 82 67 46','augue.eu.tellus@eratVivamusnisi.org','5270 Cras Ave','2008-06-17'),(111,'Labadille','Pierre','1992-12-04','H','02 33 66 43 10','06 17 21 09 45','plabadille@gmail.com','rue de la pigacière caen','2016-03-01'),(214,'Phouthavy','Victoria','1994-07-14','F','02 31 88 99 11','06 32 58 96 32','truc@machin.com','rue des feuillantine, Paris','2002-10-16'),(224,'Testa','testeur','1992-04-12','H','02 33 66 43 11','06 17 21 09 45','truc@machin.com','pierreville','2016-04-01'),(225,'test','créeateur','1992-04-12','H','02 33 66 43 11','06 17 21 09 45','plabadille@gmail.com','pigacière','2016-04-01'),(226,'Secretaire','milita','1992-05-12','F','02 33 66 43 11','06 17 21 09 45','jpg@ll.fr','galere','2016-04-08'),(227,'test2 fonctionne !','secretaire','1992-02-28','F','02 33 66 43 11','06 17 21 09 45','paorjazf@fazf.com','caen','2016-04-08'),(228,'rgreg','gergre','1992-02-29','H','02 33 66 43 11','06 17 21 09 45','fazfza@afza.fr','faefeaf','2016-04-01'),(229,'efezfez','fzefez','1992-02-23','F','02 33 66 43 11','06 17 21 20 45','fezafea@fazfa.com','aef','2016-04-01'),(230,'jytjyt','jyjyt','1992-12-04','H','02 33 66 43 11','06 17 21 20 45','kuy@jy.fr','grge','2016-04-01'),(231,'test','log','1992-10-02','H','02 33 66 43 11','06 17 21 20 45','gzeg@gzgez.com','lt\'est','2016-04-01'),(232,'test1','retraite','1990-12-25','F','02 33 66 43 11','06 17 21 20 45','pilabadille@gmail.com','faf','2016-04-01'),(233,'test2','retraiter','1968-05-02','H','02 33 66 43 11','06 17 21 20 45','abc@def.gh','truc','2016-04-01'),(234,'test3','retraite','1930-12-22','H','02 33 66 43 11','06 17 21 20 45','abc@def.gh','test','2016-04-15'),(235,'test4','Retired log','1910-12-20','H','02 33 66 43 11','06 17 21 20 45','abc@def.gh','test','2016-04-01'),(236,'Archivage','test','1992-05-12','H','06 90 68 95 45','06 32 58 96 32','plabadille@gmail.com','928','2016-05-01'),(237,'Archivage 2','test','1930-05-01','H','06 90 68 95 45','02 33 66 43 11','plabadille@gmail.com','587515','2016-05-04'),(238,'Jen','Hubert','1960-12-05','F','06 17 21 09 45','02 33 66 43 11','truc@machin.com','paris','1980-12-03'),(239,'BugUsers','Bug','1950-12-02','H','06 90 68 95 45','02 33 66 43 11','truc@machin.com','caen','2016-05-05'),(240,'bugUsers','TestFinal','1980-12-02','H','06 90 68 95 45','02 33 66 43 11','machin@truc.com','caen','2016-05-05'),(241,'Grojean','Hubert','1982-05-01','H','02 33 66 43 11','','truc@grojean.com','16 bd Leroy - Caen','2005-12-03');
/*!40000 ALTER TABLE `Militaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PossedeDiplomes`
--

DROP TABLE IF EXISTS `PossedeDiplomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PossedeDiplomes` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` int(12) NOT NULL,
  `id` varchar(50) NOT NULL,
  `date_obtention` date NOT NULL,
  `pays_obtention` varchar(30) NOT NULL,
  `organisme_formateur` varchar(70) NOT NULL,
  PRIMARY KEY (`num`),
  KEY `PossedeDiplomes_ibfk_1` (`matricule`),
  KEY `PossedeDiplomes_ibfk_2` (`id`),
  CONSTRAINT `PossedeDiplomes_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `PossedeDiplomes_ibfk_2` FOREIGN KEY (`id`) REFERENCES `Diplomes` (`acronyme`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PossedeDiplomes`
--

LOCK TABLES `PossedeDiplomes` WRITE;
/*!40000 ALTER TABLE `PossedeDiplomes` DISABLE KEYS */;
INSERT INTO `PossedeDiplomes` VALUES (1,1,'DEMS','1997-02-25','CONGO','centre de formation militaire'),(2,2,'DEMS','2015-03-25','CONGO','centre de formation militaire'),(3,3,'CAT1','2002-10-05','CONGO','centre de formation militaire'),(4,6,'CAT2','1994-11-19','CONGO','centre de formation militaire'),(5,7,'DCS','1984-02-03','CONGO','centre de formation militaire'),(6,8,'DOPJ','1994-02-11','CONGO','centre de formation militaire'),(7,9,'CAT2','2002-11-03','CONGO','centre de formation militaire'),(8,11,'DQSG2','2006-09-23','CONGO','centre de formation militaire'),(9,12,'DBSO','1997-04-14','CONGO','centre de formation militaire'),(10,13,'BA1','1997-02-22','CONGO','centre de formation militaire'),(11,14,'CAT1','2015-01-09','CONGO','centre de formation militaire'),(12,15,'CAT2','1982-03-16','CONGO','centre de formation militaire'),(13,16,'DEMS','1998-01-02','CONGO','centre de formation militaire'),(14,17,'BA1','1992-01-25','CONGO','centre de formation militaire'),(15,18,'DOPJ','1998-07-09','CONGO','centre de formation militaire'),(16,23,'DEMS','2007-01-09','CONGO','centre de formation militaire'),(17,24,'DEMS','1987-01-21','CONGO','centre de formation militaire'),(18,26,'CAT1','1994-06-13','CONGO','centre de formation militaire'),(19,27,'DEMS','1978-06-21','CONGO','centre de formation militaire'),(20,31,'DCS','1984-09-20','CONGO','centre de formation militaire'),(21,32,'DCS','1999-03-21','CONGO','centre de formation militaire'),(22,33,'CAT1','2005-06-08','CONGO','centre de formation militaire'),(23,34,'DCS','2014-02-27','CONGO','centre de formation militaire'),(24,35,'DQSG2','2000-07-25','CONGO','centre de formation militaire'),(25,36,'CAT1','1985-01-27','CONGO','centre de formation militaire'),(26,37,'DCS','1978-04-23','CONGO','centre de formation militaire'),(27,38,'DCS','2007-07-03','CONGO','centre de formation militaire'),(28,40,'BA1','2013-03-20','CONGO','centre de formation militaire'),(29,42,'CAT2','2010-01-07','CONGO','centre de formation militaire'),(30,44,'DCS','1981-02-10','CONGO','centre de formation militaire'),(31,45,'CAT1','2005-03-17','CONGO','centre de formation militaire'),(32,46,'BA1','2011-08-03','CONGO','centre de formation militaire'),(33,47,'DEMS','2008-03-08','CONGO','centre de formation militaire'),(34,51,'DBSO','1999-01-18','CONGO','centre de formation militaire'),(35,52,'CAT1','1980-02-14','CONGO','centre de formation militaire'),(36,53,'BA1','2012-03-17','CONGO','centre de formation militaire'),(37,54,'DCS','1997-02-17','CONGO','centre de formation militaire'),(38,55,'CAT1','1980-03-16','CONGO','centre de formation militaire'),(39,57,'CAT1','2003-05-01','CONGO','centre de formation militaire'),(40,58,'DCS','1982-02-02','CONGO','centre de formation militaire'),(41,59,'DQSG2','1994-08-02','CONGO','centre de formation militaire'),(42,61,'CAT1','1982-04-22','CONGO','centre de formation militaire'),(43,62,'DCS','2000-03-08','CONGO','centre de formation militaire'),(44,63,'CAT1','2010-01-17','CONGO','centre de formation militaire'),(45,65,'CAT2','1998-02-18','CONGO','centre de formation militaire'),(46,66,'CAT2','1996-04-22','CONGO','centre de formation militaire'),(47,67,'DCS','2005-05-26','CONGO','centre de formation militaire'),(48,68,'DEMS','1986-04-18','CONGO','centre de formation militaire'),(49,70,'DEMS','2005-01-22','CONGO','centre de formation militaire'),(50,72,'DCS','1992-03-22','CONGO','centre de formation militaire'),(51,73,'DCS','2005-03-02','CONGO','centre de formation militaire'),(52,74,'CAT1','1993-09-26','CONGO','centre de formation militaire'),(53,75,'CAT1','2003-02-22','CONGO','centre de formation militaire'),(54,77,'DOPJ','1985-08-18','CONGO','centre de formation militaire'),(55,79,'CAT1','2008-03-03','CONGO','centre de formation militaire'),(56,80,'DEMS','1997-01-05','CONGO','centre de formation militaire'),(57,81,'CAT2','1994-05-18','CONGO','centre de formation militaire'),(58,82,'DEMS','2011-04-19','CONGO','centre de formation militaire'),(59,83,'DCS','2014-10-11','CONGO','centre de formation militaire'),(60,84,'CAT1','1997-01-16','CONGO','centre de formation militaire'),(61,87,'DCS','2015-01-08','CONGO','centre de formation militaire'),(62,91,'DEMS','2002-05-07','CONGO','centre de formation militaire'),(63,92,'BA1','2015-03-07','CONGO','centre de formation militaire'),(64,93,'CAT2','2008-02-04','CONGO','centre de formation militaire'),(65,96,'CAT1','1981-02-09','CONGO','centre de formation militaire'),(66,97,'CAT1','2004-01-02','CONGO','centre de formation militaire'),(67,98,'DBSO','2003-07-01','CONGO','centre de formation militaire'),(68,99,'DQSG2','2013-02-18','CONGO','centre de formation militaire'),(69,100,'DCS','2011-07-21','CONGO','centre de formation militaire'),(70,3,'CAT2','2012-09-12','CONGO','centre de formation militaire'),(71,3,'BS','2013-03-11','CONGO','centre de formation militaire'),(72,5,'CAT1','2012-06-12','CONGO','école militaire'),(78,10,'BA1','1990-12-02','CONGO','centre de formation militaire'),(79,1,'BS','2016-04-01','France','Armée française'),(80,227,'BA1','2016-04-01','congo','centre de formation militaire'),(81,214,'BA1','2016-04-01','france','sorbonne'),(82,226,'BA1','2016-04-15','quebec','ecole primaire'),(85,238,'DEMS','2016-05-01','congo','force armée');
/*!40000 ALTER TABLE `PossedeDiplomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Regiment`
--

DROP TABLE IF EXISTS `Regiment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Regiment` (
  `id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Regiment`
--

LOCK TABLES `Regiment` WRITE;
/*!40000 ALTER TABLE `Regiment` DISABLE KEYS */;
INSERT INTO `Regiment` VALUES ('Ecole Militaire'),('Forces Armées'),('Gendarmerie Nationale');
/*!40000 ALTER TABLE `Regiment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Retraites`
--

DROP TABLE IF EXISTS `Retraites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Retraites` (
  `matricule` int(12) NOT NULL,
  `date_retraite` date NOT NULL,
  PRIMARY KEY (`matricule`),
  CONSTRAINT `Retraites_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Retraites`
--

LOCK TABLES `Retraites` WRITE;
/*!40000 ALTER TABLE `Retraites` DISABLE KEYS */;
INSERT INTO `Retraites` VALUES (64,'2016-05-05'),(232,'2016-04-25'),(233,'2016-04-19'),(234,'2016-04-20'),(235,'2016-04-30'),(238,'2016-05-07'),(239,'2016-05-07'),(240,'2016-05-10');
/*!40000 ALTER TABLE `Retraites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `matricule` int(12) NOT NULL,
  `role` varchar(25) NOT NULL DEFAULT 'militaire',
  `pass` varchar(255) NOT NULL,
  UNIQUE KEY `matricule` (`matricule`),
  KEY `role` (`role`),
  CONSTRAINT `Users_ibfk_1` FOREIGN KEY (`matricule`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE,
  CONSTRAINT `Users_ibfk_2` FOREIGN KEY (`role`) REFERENCES `Droits` (`role`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,'admin','$2y$10$Z6zrWScO7NsBnraxxzstdOQOPK.BnQj5ezwFOnoXTCh4y93/oNd3C'),(2,'cadre','$2y$10$fyntvLrsZSa9KGgPuygFyuamUfL5aYkUc9LbF3JVY0lPEciPoAM82'),(3,'secretaire','$2y$10$Arh4G6JmAd5oQB1yFkLdJ.aF8nb/0xvmLBkS5nrzdHlDNAkWgYfgu'),(4,'utilisateur','$2y$10$5xWehQljnG3xuKpz6PGJtu6NfQY7ZfgwniNspspUjcfD6y29KptNq'),(111,'superAdmin','$2y$10$CJLxK11C9WNxe9MmSM1.q.g5oxlbTkQbQGGwaz/Rv9rMICNkT4CgO');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-14 14:36:21
