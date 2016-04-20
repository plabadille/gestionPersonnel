CREATE TABLE IF NOT EXISTS `Droits` (
	`role` varchar(25),
	-- pour le module de gestion de crise
	`noRights` tinyint(1) NOT NULL DEFAULT '0',
	-- module mon dossier
	`seeOwnFolderModule` tinyint(1) NOT NULL DEFAULT '1',
	`editOwnFolderPersonalInformation` tinyint(1) NOT NULL DEFAULT '1',
	-- module gestion et ajout de dossier
	`listCreatedFolder` tinyint(1) NOT NULL DEFAULT '0', --lister les dossiers créé par le compte connecté
	`listAllFolder` tinyint(1) NOT NULL DEFAULT '0', --lister tous les dossiers de la base sans restriction
	`seeCreatedFolder` tinyint(1) NOT NULL DEFAULT '0', --voir un dossiers créé par le compte connecté
	`seeAllFolder` tinyint(1) NOT NULL DEFAULT '0', --voir dossier de la base sans restriction
	`createFolder` tinyint(1) NOT NULL DEFAULT '0', --créer un dossier
	`addElementToAFolder` tinyint(1) NOT NULL DEFAULT '0', --rajout de grade, diplome etc à un militaire
	`editInformationIfAuthor` tinyint(1) NOT NULL DEFAULT '0', --modifier certaines informations d'un dossier si auteur
	`editInformation` tinyint(1) NOT NULL DEFAULT '0', --modifier certaines informations sans condition particulière
	`deleteInformation` tinyint(1) NOT NULL DEFAULT '0', --pouvoir suprimer des ou toutes les informations (dossier, grade etc)
	`useFileToAddFolders` tinyint(1) NOT NULL DEFAULT '0', --permet d'utiliser le parseur pour ajouter des dossiers à la volé.
	-- module gestion promotion et retraite
	`listEligible` tinyint(1) NOT NULL DEFAULT '0', --lister les personnes éligibles retraite ou promotion
	`editEligibleCondition` tinyint(1) NOT NULL DEFAULT '0', --modifier des conditions d'éligibilité
	`addEligibleCondition` tinyint(1) NOT NULL DEFAULT '0', --ajouter des conditions d'éligibilité
	`canRetireAFolder` tinyint(1) NOT NULL DEFAULT '0', --peut passer un dossier d'actif à Retraites
	`editEligibleEmailContent` tinyint(1) NOT NULL DEFAULT '0', --permet modifier le texte envoyé par mail lorsque éligible
	`uploadFileForMail` tinyint(1) NOT NULL DEFAULT '0',
	`changePieceJointeForEligibleMail` tinyint(1) NOT NULL DEFAULT '0',
	-- module creation de compte et d'attribution des rôles
	`seeAllFolderWithoutAccount` tinyint(1) NOT NULL DEFAULT '0', --permet de visualiser tous les militaires n'ayant pas de compte
	`seeAllAccount` tinyint(1) NOT NULL DEFAULT '0', --permet de visualiser tous les comptes
	`createAccount` tinyint(1) NOT NULL DEFAULT '0', --permet de créer un compte
	`alterMdp` tinyint(1) NOT NULL DEFAULT '0', --change mdp si perdu
	`alterAccountRight` tinyint(1) NOT NULL DEFAULT '0', --permet de changer les droits d'un compte
	-- module de gestion de l'application
	`seeAllConstanteTable` tinyint(1) NOT NULL DEFAULT '0', --permet de voir le contenu des tables contenant les constantes (caserne, Droits etc)
	`editInAConstanteTable` tinyint(1) NOT NULL DEFAULT '0', --permet de modifier une table de constante
	`deleteInAConstanteTable` tinyint(1) NOT NULL DEFAULT '0', --permet de supprimer quelque chose dans une table de constante
	-- module de sauvegarde et de gestion de crise
	`allRights` tinyint(1) NOT NULL DEFAULT '0', --permet d'avoir tous les droits (sudo)
	UNIQUE (`role`),
	PRIMARY KEY(`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- Hierarchie roles : 
-- militaire
-- secretaire
-- cadre
-- admin
-- superAdmin



CREATE TABLE IF NOT EXISTS `Users` (
	`matricule` int(12) NOT NULL,
	`role` varchar(25) NOT NULL DEFAULT 'militaire',
	`pass` varchar(255) NOT NULL,
	UNIQUE (`matricule`),
	FOREIGN KEY (`matricule`) REFERENCES Militaires(`matricule`) ON UPDATE CASCADE,
	FOREIGN KEY (`role`) REFERENCES Droits(`role`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;