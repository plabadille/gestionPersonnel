-- DROP TABLE IF EXISTS `Militaires`;
-- CREATE TABLE IF NOT EXISTS `Militaires` (
--   `matricule` int(12) NOT NULL AUTO_INCREMENT,
--   `nom` varchar(50) NOT NULL,
--   `prenom` varchar(50) NOT NULL,
--   `date_naissance` date NOT NULL,
--   `genre` varchar(1) NOT NULL,
--   `tel1` varchar(20) NOT NULL,
--   `tel2` varchar(20) DEFAULT NULL,
--   `email` varchar(50) NOT NULL,
--   `adresse` varchar(250) NOT NULL,
--   `date_recrutement` date NOT NULL,
--   PRIMARY KEY (`matricule`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `Actifs`;
-- CREATE TABLE IF NOT EXISTS `Actifs` (
--   `matricule` int(12) NOT NULL,
--   `eligible_retraite` tinyint(1) NOT NULL DEFAULT '0',
--   `eligible_promotion` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`matricule`),
--   FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `Retraites`;
-- CREATE TABLE IF NOT EXISTS `Retraites` (
--   `matricule` int(12) NOT NULL,
--   `date_retraite` date NOT NULL,
--   PRIMARY KEY (`matricule`),
--   FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `Archives`;
-- CREATE TABLE IF NOT EXISTS `Archives` (
--   `matricule` int(12) NOT NULL,
--   `date_deces` date NOT NULL,
--   `cause_deces` varchar(250) NOT NULL,
--   PRIMARY KEY (`matricule`),
--   FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS `Regiment`;
-- CREATE TABLE IF NOT EXISTS `Regiment` (
--   `id` varchar(30) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `AppartientRegiment`;
-- CREATE TABLE IF NOT EXISTS `AppartientRegiment` (
--   `matricule` int(12) NOT NULL,
--   `id` varchar(30) NOT NULL,
--   `date_appartenance` date NOT NULL,
--   FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE,
--   FOREIGN KEY (`id`) REFERENCES Regiment(`id`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS `Grades`;
-- CREATE TABLE IF NOT EXISTS `Grades` (
--   `id` varchar(12) NOT NULL,
--   `grade` varchar(70) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `DetientGrades`;
-- CREATE TABLE IF NOT EXISTS `DetientGrades` (
-- 	`matricule` int(12) NOT NULL,
-- 	`id` varchar(12) NOT NULL,
-- 	`date_promotion` date NOT NULL,
-- 	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`id`) REFERENCES Grades(`id`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS `Diplomes`;
-- CREATE TABLE IF NOT EXISTS `Diplomes` (
--   	`intitule` varchar(50) NOT NULL,
--   	`acronyme` varchar(15) NOT NULL,
--   	PRIMARY KEY (`acronyme`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `PossedeDiplomes`;
-- CREATE TABLE IF NOT EXISTS `PossedeDiplomes` (
-- 	`matricule` int(12) NOT NULL,
-- 	`id` varchar(50) NOT NULL,
-- 	`date_obtention` date NOT NULL,
-- 	`pays_obtention` varchar(30) NOT NULL,
-- 	`organisme_formateur` varchar(70) NOT NULL,
-- 	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`id`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS `Casernes`;
-- CREATE TABLE IF NOT EXISTS `Casernes` (
--   `id` int(12) NOT NULL AUTO_INCREMENT,
--   `nom` varchar(50) NOT NULL,
--   `adresse` varchar(150) NOT NULL,
--   `tel_standard` varchar(20) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `Affectation`;
-- CREATE TABLE IF NOT EXISTS `Affectation` (
-- 	`matricule` int(12),
-- 	`id` int(12),
-- 	`date_affectation` date NOT NULL,
-- 	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`id`) REFERENCES Casernes(`id`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS `ConditionsRetraites`;
-- CREATE TABLE IF NOT EXISTS `ConditionsRetraites` (
-- 	`id` int(11) NOT NULL AUTO_INCREMENT,
-- 	`idGrade` varchar(12) NOT NULL,
-- 	`service_effectif` int NOT NULL,
-- 	`age` int NOT NULL,
-- 	PRIMARY KEY (`id`),
-- 	FOREIGN KEY (`idGrade`) REFERENCES Grades(`id`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `ConditionsPromotions`;
-- CREATE TABLE IF NOT EXISTS `ConditionsPromotions` (
-- 	`id` int(11) NOT NULL AUTO_INCREMENT,
-- 	`idGrade` varchar(12) NOT NULL,
-- 	`annees_service_FA` int NOT NULL,
-- 	`annees_service_GN` int NOT NULL,
-- 	`annees_service_SOE` int NOT NULL,
-- 	`annees_service_grade` int NOT NULL,
-- 	`diplome` varchar(15),
-- 	`diplomeSup1` varchar(15),
-- 	`diplomeSup2` varchar(15),
-- 	PRIMARY KEY (`id`),
-- 	FOREIGN KEY (`idGrade`) REFERENCES Grades(`id`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplome`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeSup1`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeSup2`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `DiplomesEquivalences`;
-- CREATE TABLE IF NOT EXISTS `DiplomesEquivalences` (
-- 	`id` int(11) NOT NULL AUTO_INCREMENT,
-- 	`diplome` varchar(15) NOT NULL,
-- 	`diplomeEqui1` varchar(15),
-- 	`diplomeEqui2` varchar(15),
-- 	`diplomeEqui3` varchar(15),
-- 	`diplomeEqui4` varchar(15),
-- 	`diplomeEqui5` varchar(15),
-- 	`diplomeEqui6` varchar(15),
-- 	`diplomeEqui7` varchar(15),
-- 	`diplomeEqui8` varchar(15),
-- 	`diplomeEqui9` varchar(15),
-- 	`diplomeEqui10` varchar(15),
-- 	`diplomeEqui11` varchar(15),
-- 	`diplomeEqui12` varchar(15),
-- 	`diplomeEqui13` varchar(15),
-- 	`diplomeEqui14` varchar(15),
-- 	PRIMARY KEY (`id`),
-- 	FOREIGN KEY (`diplome`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui1`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui2`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui3`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui4`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui5`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui6`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui7`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui8`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui9`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui10`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui11`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui12`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui13`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE,
-- 	FOREIGN KEY (`diplomeEqui14`) REFERENCES Diplomes(`acronyme`) ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Droits` (
	`role` varchar(25),
	`noRights` tinyint(1) NOT NULL DEFAULT '0',
	`seeOwnFolderModule` tinyint(1) NOT NULL DEFAULT '1',
	`editOwnFolderPersonalInformation` tinyint(1) NOT NULL DEFAULT '1',
	`listCreatedFolder` tinyint(1) NOT NULL DEFAULT '0',
	`listAllFolder` tinyint(1) NOT NULL DEFAULT '0',
	`seeCreatedFolder` tinyint(1) NOT NULL DEFAULT '0',
	`seeAllFolder` tinyint(1) NOT NULL DEFAULT '0', 
	`createFolder` tinyint(1) NOT NULL DEFAULT '0',
	`addElementToAFolder` tinyint(1) NOT NULL DEFAULT '0',
	`editInformationIfAuthor` tinyint(1) NOT NULL DEFAULT '0',
	`editInformation` tinyint(1) NOT NULL DEFAULT '0',
	`deleteInformation` tinyint(1) NOT NULL DEFAULT '0',
	`useFileToAddFolders` tinyint(1) NOT NULL DEFAULT '0',
	`listEligible` tinyint(1) NOT NULL DEFAULT '0',
	`editEligibleCondition` tinyint(1) NOT NULL DEFAULT '0',
	`addEligibleCondition` tinyint(1) NOT NULL DEFAULT '0',
	`canRetireAFolder` tinyint(1) NOT NULL DEFAULT '0',
	`editEligibleEmailContent` tinyint(1) NOT NULL DEFAULT '0',
	`uploadFileForMail` tinyint(1) NOT NULL DEFAULT '0',
	`changePieceJointeForEligibleMail` tinyint(1) NOT NULL DEFAULT '0',
	`seeAllFolderWithoutAccount` tinyint(1) NOT NULL DEFAULT '0',
	`seeAllAccount` tinyint(1) NOT NULL DEFAULT '0',
	`createAccount` tinyint(1) NOT NULL DEFAULT '0',
	`alterMdp` tinyint(1) NOT NULL DEFAULT '0',
	`alterAccountRight` tinyint(1) NOT NULL DEFAULT '0',
	`seeAllConstanteTable` tinyint(1) NOT NULL DEFAULT '0',
	`editInAConstanteTable` tinyint(1) NOT NULL DEFAULT '0',
	`deleteInAConstanteTable` tinyint(1) NOT NULL DEFAULT '0',
	`allRights` tinyint(1) NOT NULL DEFAULT '0',
	UNIQUE (`role`),
	PRIMARY KEY(`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Users` (
	`matricule` int(12) NOT NULL,
	`role` varchar(25) NOT NULL DEFAULT 'militaire',
	`pass` varchar(255) NOT NULL,
	UNIQUE (`matricule`),
	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE,
	FOREIGN KEY (`role`) REFERENCES Droits(`role`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

ALTER TABLE `Actifs`
ADD CONSTRAINT `Actifs_ibfk_2` FOREIGN KEY (`saisie_by`) REFERENCES `Militaires` (`matricule`) ON UPDATE CASCADE;

UPDATE `Actifs`
SET `eligible_retraite`=0
WHERE `eligible_retraite`=1;