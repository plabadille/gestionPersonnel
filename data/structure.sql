DROP TABLE IF EXISTS `Militaires`;
CREATE TABLE IF NOT EXISTS `Militaires` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Actifs`;
CREATE TABLE IF NOT EXISTS `Actifs` (
  `matricule` int(12) NOT NULL,
  `eligible_retraite` BOOLEAN NOT NULL DEFAULT '0',
  `eligible_promotion` BOOLEAN NOT NULL DEFAULT '0',
  PRIMARY KEY (`matricule`),
  FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Retraites`;
CREATE TABLE IF NOT EXISTS `Retraites` (
  `matricule` int(12) NOT NULL,
  `date_retraite` date NOT NULL,
  PRIMARY KEY (`matricule`),
  FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Archives`;
CREATE TABLE IF NOT EXISTS `Archives` (
  `matricule` int(12) NOT NULL,
  `date_deces` date NOT NULL,
  `cause_deces` varchar(250) NOT NULL,
  PRIMARY KEY (`matricule`),
  FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `Regiment`;
CREATE TABLE IF NOT EXISTS `Regiment` (
  `id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `AppartientRegiment`;
CREATE TABLE IF NOT EXISTS `AppartientRegiment` (
  `matricule` int(12) NOT NULL,
  `id` varchar(30) NOT NULL,
  `date_appartenance` date NOT NULL,
  FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`),
  FOREIGN KEY (`id`) REFERENCES Regiment(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `Grades`;
CREATE TABLE IF NOT EXISTS `Grades` (
  `id` varchar(12) NOT NULL,
  `grade` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `DetientGrades`;
CREATE TABLE IF NOT EXISTS `DetientGrades` (
	`matricule` int(12) NOT NULL,
	`id` varchar(12) NOT NULL,
	`date_promotion` date NOT NULL,
	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`),
	FOREIGN KEY (`id`) REFERENCES Grades(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `Diplomes`;
CREATE TABLE IF NOT EXISTS `Diplomes` (
  	`id` varchar(50) NOT NULL,
  	`acronyme` varchar(15) NOT NULL,
  	PRIMARY KEY (`acronyme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `PossedeDiplomes`;
CREATE TABLE IF NOT EXISTS `PossedeDiplomes` (
	`matricule` int(12) NOT NULL,
	`id` varchar(50) NOT NULL,
	`date_obtention` date NOT NULL,
	`pays_obtention` varchar(30) NOT NULL,
	`organisme_formateur` varchar(70) NOT NULL,
	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`),
	FOREIGN KEY (`id`) REFERENCES Diplomes(`acronyme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `Casernes`;
CREATE TABLE IF NOT EXISTS `Casernes` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `adresse` varchar(150) NOT NULL,
  `tel_standard` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Affectation`;
CREATE TABLE IF NOT EXISTS `Affectation` (
	`matricule` int(12),
	`id` int(12),
	`date_affectation` date NOT NULL,
	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`),
	FOREIGN KEY (`id`) REFERENCES Casernes(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;



-- DROP TABLE IF EXISTS `ConditionsPromotions`;
-- CREATE TABLE IF NOT EXISTS `ConditionsPromotions` (
-- 	`id` varchar(12) NOT NULL,
-- 	`annees_service` int NOT NULL,
-- 	`annees_service_grade` int NOT NULL,
-- 	`diplomes` varchar(50) NOT NULL,
-- 	PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS `ConditionsRetraites`;
-- CREATE TABLE IF NOT EXISTS `ConditionsRetraites` (
-- 	`denominationGrade` varchar(50) NOT NULL,
-- 	`annees_service` int NOT NULL,
-- 	`age` int NOT NULL,
-- 	PRIMARY KEY (`denominationGrade`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;