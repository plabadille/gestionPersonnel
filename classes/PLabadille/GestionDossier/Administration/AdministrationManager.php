<?php
namespace PLabadille\GestionDossier\Administration;
use PLabadille\Common\Bd\DB;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 4- Module création de compte et de droit
# 5- Module de gestion de l'application
# 6- Module de sauvegarde et de gestion de crise
//--------------------

#Gère les requêtes en BDD, est appelé par le controller.
class AdministrationManager
{
    //--------------------
    //x- Fonctions utilitaires et génériques
    //--------------------
    public static function getNomPrenomFromId($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT nom, prenom
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            where m.matricule = :matricule
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function downloadBdd()
    {
        DB::getInstance()->getDump();
    }

    public static function internalDumpBdd()
    {
        DB::getInstance()->internalDump();
    }

    //--------------------
    //4-module gestion et ajout de dossier
    //--------------------

    // 4-1- 'listCreatedFolderWithoutAccount':
    #Reccupère tous les dossiers militaires
    public static function getAllWithOutAccount() 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT m.matricule, nom, prenom
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.matricule NOT IN (SELECT matricule from Users)
        ';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function searchNameWithOutFolder($search)
    {
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT m.matricule, nom, prenom
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.matricule NOT IN (SELECT matricule from Users)
            AND nom like concat("%",:search,"%") OR m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function ajaxRechercherNameWithOutFolder($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT nom, prenom
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.matricule NOT IN (SELECT matricule from Users)
            AND nom like concat("%",:search,"%") OR m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $data = ['search'=>$search];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    // 4-2- 'seeAllAccount':
    static public function getAllAccount()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT m.matricule, nom, prenom, role
            FROM Militaires m
            INNER JOIN Users u ON m.matricule = u.matricule
        ';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function searchNameAccount($search)
    {
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT m.matricule, nom, prenom, role
            FROM Militaires m
            INNER JOIN Users u ON m.matricule = u.matricule
            WHERE nom like concat("%",:search,"%") OR m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function ajaxSearchCompte($search)
    {
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT nom, prenom
            FROM Militaires m
            INNER JOIN Users u ON m.matricule = u.matricule
            WHERE nom like concat("%",:search,"%") OR m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    // 4-3- 'createAccount':
    static public function getListeRole()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'SELECT role FROM Droits WHERE role != "superAdmin"';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function addAccount($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
                INSERT INTO Users 
                    (matricule, role, pass) 
                VALUES
                    (:matricule, :role, :pass)
            ");
            $stmt->bindParam(':matricule', $attributs['username']);
            $stmt->bindParam(':role', $attributs['role']);
            $stmt->bindParam(':pass', $attributs['pass']);
            
            $stmt->execute();
    }

    // 4-4- 'alterPassword':
    static public function alterUserPassword($attributs)
    {
        //protection supplémentaire pour être bien sur que personne ne puisse changer le mdp du superAdmin.
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET pass = :pass
            WHERE matricule = :id
            AND role != 'superAdmin'
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':pass', $attributs['pass']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    // 4-5- 'alterAccountRight':
    static public function getAccountById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT *
            FROM Users
            WHERE matricule = :matricule
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule'=>$id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();

        return $result;
    }

    static public function alterAccountRight($attributs)
    {
        //protection supplémentaire pour être bien sur que personne ne puisse changer le role du superAdmin.
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Users 
            SET role = :role
            WHERE matricule = :id
            AND role != 'superAdmin'
        ");
        
        $stmt->bindParam(':id', $attributs['username']);
        $stmt->bindParam(':role', $attributs['role']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    // 4-6- 'deleteAccount'
    static public function getAllAccountToDelete()
    {
        //on tri par date afin d'afficher en premier les plus anciens (et donc plus important à supr)
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT r.matricule, date_retraite, nom, prenom
            FROM Retraites r
            JOIN Users u ON u.matricule = r.matricule
            JOIN Militaires m ON m.matricule = r.matricule
            ORDER BY date_retraite
        ';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result['retraite'] = $stmt->fetchAll();

        $req = '
            SELECT a.matricule, date_deces, nom, prenom
            FROM Archives a
            JOIN Users u ON u.matricule = a.matricule
            JOIN Militaires m ON m.matricule = a.matricule
            ORDER BY date_deces
        ';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result['archive'] = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function deleteAccountById($id)
    {
        //on s'assure également que la personne est bien présente dans la liste retraite ou archives
        $pdo = DB::getInstance()->getPDO();
        //on supprime le grade detenu
        $req = '
            DELETE 
            FROM Users 
            WHERE EXISTS (select matricule from Archives where matricule = :matricule) AND matricule = :matricule
            OR EXISTS (select matricule from Retraites where matricule = :matricule) AND matricule = :matricule
        ';

        $stmt = $pdo->prepare($req);
        $data = ['matricule'=>$id];
        $stmt->execute($data);
        $stmt->closeCursor();
    }

    //--------------------
    //5-module de gestion de l'application
    //--------------------

    // 5-1- 'seeAllConstanteTable':

    // 5-1-1 'seeAllCasernes':
    public static function getAllCasernes()
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'SELECT * FROM Casernes';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    // 5-1-2 'seeAllRegiments':
    public static function getAllRegiments()
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'SELECT * FROM Regiment';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    // 5-1-3 'seeAllDiplomes':
    public static function getAllDiplomes()
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'SELECT * FROM Diplomes';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    // 5-1-4 'seeAllGrades':
    public static function getAllGrades()
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'SELECT * FROM Grades ORDER BY hierarchie';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    // 5-1-5 'seeAllDroits':
    public static function getAllDroits()
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'SELECT * FROM Droits';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    // 5-2- 'addInConstanteTable':

    // 5-2-1 'addCasernes':
    static public function addCaserne($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
                INSERT INTO Casernes 
                    (id, nom, adresse, tel_standard) 
                VALUES
                    (:id, :nom, :adresse, :tel_standard)
            ");
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':nom', $attributs['nom']);
        $stmt->bindParam(':adresse', $attributs['adresse']);
        $stmt->bindParam(':tel_standard', $attributs['tel_standard']);
        
        $stmt->execute();
    }
   
    // 5-2-2 'addRegiments':
    static public function addRegiment($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
                INSERT INTO Regiment 
                    (id) 
                VALUES
                    (:id)
            ");
        $stmt->bindParam(':id', $attributs['id']);
        
        $stmt->execute();
    }

    // 5-2-3 'addlDiplomes':
    static public function addDiplome($attributs)
    {$pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
                INSERT INTO Diplomes 
                    (acronyme, intitule) 
                VALUES
                    (:acronyme, :intitule)
            ");
        $stmt->bindParam(':acronyme', $attributs['acronyme']);
        $stmt->bindParam(':intitule', $attributs['intitule']);
        
        $stmt->execute();
    }
    
    // 5-2-4 'addGrades':
    static public function getListeGrade()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'SELECT distinct hierarchie, grade FROM Grades';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function addGrade($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
            SELECT *
            FROM Grades
            WHERE grade = :grade AND hierarchie = :hierarchie
        ");
        $stmt->bindParam(':grade', $attributs['grade']);
        $stmt->bindParam(':hierarchie', $attributs['hierarchie']);
        
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false){
            $stmt = $pdo->prepare("
                    INSERT INTO Grades 
                        (grade, hierarchie) 
                    VALUES
                        (:grade, :hierarchie)
                ");
            $stmt->bindParam(':grade', $attributs['grade']);
            $stmt->bindParam(':hierarchie', $attributs['hierarchie']);
            
            $stmt->execute();

            return null;
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
    }
   
    // 5-2-5 'addDroits':
    static public function addClasseDroits($attributs)
    {
        //mise en forme ; si il existe il contient on, on veut 1. S'il existe pas on veut le set à false (0)
        $noRights = (isset($attributs['noRights']) ? 1 : 0);
        $allRights = (isset($attributs['allRights']) ? 1 : 0);
        $seeOwnFolderModule = (isset($attributs['seeOwnFolderModule']) ? 1 : 0);
        $editOwnFolderPersonalInformation = (isset($attributs['editOwnFolderPersonalInformation']) ? 1 : 0);
        $listCreatedFolder = (isset($attributs['listCreatedFolder']) ? 1 : 0);
        $listAllFolder = (isset($attributs['listAllFolder']) ? 1 : 0);
        $seeCreatedFolder = (isset($attributs['seeCreatedFolder']) ? 1 : 0);
        $seeAllFolder = (isset($attributs['seeAllFolder']) ? 1 : 0);
        $createFolder = (isset($attributs['createFolder']) ? 1 : 0);
        $addElementToAFolder = (isset($attributs['addElementToAFolder']) ? 1 : 0);
        $addElementToAFolderCreated = (isset($attributs['addElementToAFolderCreated']) ? 1 : 0);
        $editInformationIfAuthor = (isset($attributs['editInformationIfAuthor']) ? 1 : 0);
        $editInformation = (isset($attributs['editInformation']) ? 1 : 0);
        $deleteInformation = (isset($attributs['deleteInformation']) ? 1 : 0);
        $useFileToAddFolders = (isset($attributs['useFileToAddFolders']) ? 1 : 0);
        $listEligible = (isset($attributs['listEligible']) ? 1 : 0);
        $editEligibleCondition = (isset($attributs['editEligibleCondition']) ? 1 : 0);
        $addEligibleCondition = (isset($attributs['addEligibleCondition']) ? 1 : 0);
        $suprEligibleCondition = (isset($attributs['suprEligibleCondition']) ? 1 : 0);
        $canRetireAFolder = (isset($attributs['canRetireAFolder']) ? 1 : 0);
        $canArchiveAFolder = (isset($attributs['canArchiveAFolder']) ? 1 : 0);
        $editEligibleEmailContent = (isset($attributs['editEligibleEmailContent']) ? 1 : 0);
        $uploadFileForMail = (isset($attributs['uploadFileForMail']) ? 1 : 0);
        $changePieceJointeForEligibleMail = (isset($attributs['changePieceJointeForEligibleMail']) ? 1 : 0);
        $seeAllFolderWithoutAccount = (isset($attributs['seeAllFolderWithoutAccount']) ? 1 : 0);
        $seeAllAccount = (isset($attributs['seeAllAccount']) ? 1 : 0);
        $createAccount = (isset($attributs['createAccount']) ? 1 : 0);
        $alterMdp = (isset($attributs['alterMdp']) ? 1 : 0);
        $alterAccountRight = (isset($attributs['alterAccountRight']) ? 1 : 0);
        $deleteAccount = (isset($attributs['deleteAccount']) ? 1 : 0);
        $seeAllConstanteTable = (isset($attributs['seeAllConstanteTable']) ? 1 : 0);
        $editInAConstanteTable = (isset($attributs['editInAConstanteTable']) ? 1 : 0);
        $deleteInAConstanteTable = (isset($attributs['deleteInAConstanteTable']) ? 1 : 0);

        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
            INSERT INTO Droits 
                (role, noRights, allRights, seeOwnFolderModule, editOwnFolderPersonalInformation, listCreatedFolder, listAllFolder, seeCreatedFolder, seeAllFolder, createFolder, addElementToAFolder, addElementToAFolderCreated, editInformationIfAuthor, editInformation, deleteInformation, useFileToAddFolders, listEligible, editEligibleCondition, addEligibleCondition, suprEligibleCondition, canRetireAFolder, canArchiveAFolder, editEligibleEmailContent, uploadFileForMail, changePieceJointeForEligibleMail, seeAllFolderWithoutAccount, seeAllAccount, createAccount, alterMdp, alterAccountRight, deleteAccount, seeAllConstanteTable, editInAConstanteTable, deleteInAConstanteTable)
                VALUES
                    (:role, :noRights, :allRights, :seeOwnFolderModule, :editOwnFolderPersonalInformation, :listCreatedFolder, :listAllFolder, :seeCreatedFolder, :seeAllFolder, :createFolder, :addElementToAFolder, :addElementToAFolderCreated, :editInformationIfAuthor, :editInformation, :deleteInformation, :useFileToAddFolders, :listEligible, :editEligibleCondition, :addEligibleCondition, :suprEligibleCondition, :canRetireAFolder, :canArchiveAFolder, :editEligibleEmailContent, :uploadFileForMail, :changePieceJointeForEligibleMail, :seeAllFolderWithoutAccount, :seeAllAccount, :createAccount, :alterMdp, :alterAccountRight, :deleteAccount, :seeAllConstanteTable, :editInAConstanteTable, :deleteInAConstanteTable)
            ");

        $stmt->bindParam(':role', $attributs['role']);
        $stmt->bindParam(':noRights', $noRights);
        $stmt->bindParam(':allRights', $allRights);
        $stmt->bindParam(':seeOwnFolderModule', $seeOwnFolderModule);
        $stmt->bindParam(':editOwnFolderPersonalInformation', $editOwnFolderPersonalInformation);
        $stmt->bindParam(':listCreatedFolder', $listCreatedFolder);
        $stmt->bindParam(':listAllFolder', $listAllFolder);
        $stmt->bindParam(':seeCreatedFolder', $seeCreatedFolder);
        $stmt->bindParam(':seeAllFolder', $seeAllFolder);
        $stmt->bindParam(':createFolder', $createFolder);
        $stmt->bindParam(':addElementToAFolder', $addElementToAFolder);
        $stmt->bindParam(':addElementToAFolderCreated', $addElementToAFolderCreated);
        $stmt->bindParam(':editInformationIfAuthor', $editInformationIfAuthor);
        $stmt->bindParam(':editInformation', $editInformation);
        $stmt->bindParam(':deleteInformation', $deleteInformation);
        $stmt->bindParam(':useFileToAddFolders', $useFileToAddFolders);
        $stmt->bindParam(':listEligible', $listEligible);
        $stmt->bindParam(':editEligibleCondition', $editEligibleCondition);
        $stmt->bindParam(':addEligibleCondition', $addEligibleCondition);
        $stmt->bindParam(':suprEligibleCondition', $suprEligibleCondition);
        $stmt->bindParam(':canRetireAFolder', $canRetireAFolder);
        $stmt->bindParam(':canArchiveAFolder', $canArchiveAFolder);
        $stmt->bindParam(':editEligibleEmailContent', $editEligibleEmailContent);
        $stmt->bindParam(':uploadFileForMail', $uploadFileForMail);
        $stmt->bindParam(':changePieceJointeForEligibleMail', $changePieceJointeForEligibleMail);
        $stmt->bindParam(':seeAllFolderWithoutAccount', $seeAllFolderWithoutAccount);
        $stmt->bindParam(':seeAllAccount', $seeAllAccount);
        $stmt->bindParam(':createAccount', $createAccount);
        $stmt->bindParam(':alterMdp', $alterMdp);
        $stmt->bindParam(':alterAccountRight', $alterAccountRight);
        $stmt->bindParam(':deleteAccount', $deleteAccount);
        $stmt->bindParam(':seeAllConstanteTable', $seeAllConstanteTable);
        $stmt->bindParam(':editInAConstanteTable', $editInAConstanteTable);
        $stmt->bindParam(':deleteInAConstanteTable', $deleteInAConstanteTable);
        
        $stmt->execute();
    }

    //-----------------------------

    // 5-3- 'editInConstanteTable':
    //-----------------------------

    // 5-3-1 'editCaserne':

    public static function getCaserneById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT *
            FROM Casernes
            where id = :id
        ';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function editCaserne($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Casernes 
            SET nom = :nom, adresse = :adresse, tel_standard = :tel_standard
            WHERE id = :id
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':nom', $attributs['nom']);
        $stmt->bindParam(':adresse', $attributs['adresse']);
        $stmt->bindParam(':tel_standard', $attributs['tel_standard']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }
    
    // 5-3-2 'editRegiment':

    public static function getRegimentById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT *
            FROM Regiment
            where id = :id
        ';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function editRegiment($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Regiment 
            SET id = :id
            WHERE id = :oldId
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':oldId', $attributs['oldId']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    // 5-3-3 'editDiplome':

    public static function getDiplomeById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT *
            FROM Diplomes
            where acronyme = :acronyme
        ';
        $stmt = $pdo->prepare($req);
        $data = ['acronyme' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function editDiplome($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Diplomes 
            SET intitule = :intitule, acronyme = :acronyme
            WHERE acronyme = :oldAcronyme
        ");
        
        $stmt->bindParam(':intitule', $attributs['intitule']);
        $stmt->bindParam(':acronyme', $attributs['acronyme']);
        $stmt->bindParam(':oldAcronyme', $attributs['oldAcronyme']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    // 5-3-4 'editGrade':

    public static function getGradeById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT *
            FROM Grades
            where id = :id
        ';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function editGrade($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Grades 
            SET grade = :grade, hierarchie = :hierarchie
            WHERE id = :id
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':grade', $attributs['grade']);
        $stmt->bindParam(':hierarchie', $attributs['hierarchie']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    // 5-3-5 'editClasseDroits':

    public static function getClasseDroitsById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT *
            FROM Droits
            where role = :role
        ';
        $stmt = $pdo->prepare($req);
        $data = ['role' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public static function editClasseDroits($attributs)
    {
        $noRights = (isset($attributs['noRights']) ? 1 : 0);
        $allRights = (isset($attributs['allRights']) ? 1 : 0);
        $seeOwnFolderModule = (isset($attributs['seeOwnFolderModule']) ? 1 : 0);
        $editOwnFolderPersonalInformation = (isset($attributs['editOwnFolderPersonalInformation']) ? 1 : 0);
        $listCreatedFolder = (isset($attributs['listCreatedFolder']) ? 1 : 0);
        $listAllFolder = (isset($attributs['listAllFolder']) ? 1 : 0);
        $seeCreatedFolder = (isset($attributs['seeCreatedFolder']) ? 1 : 0);
        $seeAllFolder = (isset($attributs['seeAllFolder']) ? 1 : 0);
        $createFolder = (isset($attributs['createFolder']) ? 1 : 0);
        $addElementToAFolder = (isset($attributs['addElementToAFolder']) ? 1 : 0);
        $addElementToAFolderCreated = (isset($attributs['addElementToAFolderCreated']) ? 1 : 0);
        $editInformationIfAuthor = (isset($attributs['editInformationIfAuthor']) ? 1 : 0);
        $editInformation = (isset($attributs['editInformation']) ? 1 : 0);
        $deleteInformation = (isset($attributs['deleteInformation']) ? 1 : 0);
        $useFileToAddFolders = (isset($attributs['useFileToAddFolders']) ? 1 : 0);
        $listEligible = (isset($attributs['listEligible']) ? 1 : 0);
        $editEligibleCondition = (isset($attributs['editEligibleCondition']) ? 1 : 0);
        $addEligibleCondition = (isset($attributs['addEligibleCondition']) ? 1 : 0);
        $suprEligibleCondition = (isset($attributs['suprEligibleCondition']) ? 1 : 0);
        $canRetireAFolder = (isset($attributs['canRetireAFolder']) ? 1 : 0);
        $canArchiveAFolder = (isset($attributs['canArchiveAFolder']) ? 1 : 0);
        $editEligibleEmailContent = (isset($attributs['editEligibleEmailContent']) ? 1 : 0);
        $uploadFileForMail = (isset($attributs['uploadFileForMail']) ? 1 : 0);
        $changePieceJointeForEligibleMail = (isset($attributs['changePieceJointeForEligibleMail']) ? 1 : 0);
        $seeAllFolderWithoutAccount = (isset($attributs['seeAllFolderWithoutAccount']) ? 1 : 0);
        $seeAllAccount = (isset($attributs['seeAllAccount']) ? 1 : 0);
        $createAccount = (isset($attributs['createAccount']) ? 1 : 0);
        $alterMdp = (isset($attributs['alterMdp']) ? 1 : 0);
        $alterAccountRight = (isset($attributs['alterAccountRight']) ? 1 : 0);
        $deleteAccount = (isset($attributs['deleteAccount']) ? 1 : 0);
        $seeAllConstanteTable = (isset($attributs['seeAllConstanteTable']) ? 1 : 0);
        $editInAConstanteTable = (isset($attributs['editInAConstanteTable']) ? 1 : 0);
        $deleteInAConstanteTable = (isset($attributs['deleteInAConstanteTable']) ? 1 : 0);

        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Droits 
            SET role = :role, noRights = :noRights, allRights = :allRights, seeOwnFolderModule = :seeOwnFolderModule, editOwnFolderPersonalInformation = :editOwnFolderPersonalInformation, listCreatedFolder = :listCreatedFolder, listAllFolder = :listAllFolder, seeCreatedFolder = :seeCreatedFolder, seeAllFolder = :seeAllFolder, createFolder = :createFolder, addElementToAFolder = :addElementToAFolder, addElementToAFolderCreated = :addElementToAFolderCreated, editInformationIfAuthor = :editInformationIfAuthor, editInformation = :editInformation, deleteInformation = :deleteInformation, useFileToAddFolders = :useFileToAddFolders, listEligible = :listEligible, editEligibleCondition = :editEligibleCondition, addEligibleCondition = :addEligibleCondition, suprEligibleCondition = :suprEligibleCondition, canRetireAFolder = :canRetireAFolder, canArchiveAFolder = :canArchiveAFolder, editEligibleEmailContent = :editEligibleEmailContent, uploadFileForMail = :uploadFileForMail, changePieceJointeForEligibleMail = :changePieceJointeForEligibleMail, seeAllFolderWithoutAccount = :seeAllFolderWithoutAccount, seeAllAccount = :seeAllAccount, createAccount = :createAccount, alterMdp = :alterMdp, alterAccountRight = :alterAccountRight, deleteAccount = :deleteAccount, seeAllConstanteTable = :seeAllConstanteTable, editInAConstanteTable = :editInAConstanteTable, deleteInAConstanteTable = :deleteInAConstanteTable 
            WHERE role = :oldRole 
            AND role != 'superAdmin'
        ");
        
        $stmt->bindParam(':oldRole', $attributs['oldRole']);
        $stmt->bindParam(':role', $attributs['role']);
        $stmt->bindParam(':noRights', $noRights);
        $stmt->bindParam(':allRights', $allRights);
        $stmt->bindParam(':seeOwnFolderModule', $seeOwnFolderModule);
        $stmt->bindParam(':editOwnFolderPersonalInformation', $editOwnFolderPersonalInformation);
        $stmt->bindParam(':listCreatedFolder', $listCreatedFolder);
        $stmt->bindParam(':listAllFolder', $listAllFolder);
        $stmt->bindParam(':seeCreatedFolder', $seeCreatedFolder);
        $stmt->bindParam(':seeAllFolder', $seeAllFolder);
        $stmt->bindParam(':createFolder', $createFolder);
        $stmt->bindParam(':addElementToAFolder', $addElementToAFolder);
        $stmt->bindParam(':addElementToAFolderCreated', $addElementToAFolderCreated);
        $stmt->bindParam(':editInformationIfAuthor', $editInformationIfAuthor);
        $stmt->bindParam(':editInformation', $editInformation);
        $stmt->bindParam(':deleteInformation', $deleteInformation);
        $stmt->bindParam(':useFileToAddFolders', $useFileToAddFolders);
        $stmt->bindParam(':listEligible', $listEligible);
        $stmt->bindParam(':editEligibleCondition', $editEligibleCondition);
        $stmt->bindParam(':addEligibleCondition', $addEligibleCondition);
        $stmt->bindParam(':suprEligibleCondition', $suprEligibleCondition);
        $stmt->bindParam(':canRetireAFolder', $canRetireAFolder);
        $stmt->bindParam(':canArchiveAFolder', $canArchiveAFolder);
        $stmt->bindParam(':editEligibleEmailContent', $editEligibleEmailContent);
        $stmt->bindParam(':uploadFileForMail', $uploadFileForMail);
        $stmt->bindParam(':changePieceJointeForEligibleMail', $changePieceJointeForEligibleMail);
        $stmt->bindParam(':seeAllFolderWithoutAccount', $seeAllFolderWithoutAccount);
        $stmt->bindParam(':seeAllAccount', $seeAllAccount);
        $stmt->bindParam(':createAccount', $createAccount);
        $stmt->bindParam(':alterMdp', $alterMdp);
        $stmt->bindParam(':alterAccountRight', $alterAccountRight);
        $stmt->bindParam(':deleteAccount', $deleteAccount);
        $stmt->bindParam(':seeAllConstanteTable', $seeAllConstanteTable);
        $stmt->bindParam(':editInAConstanteTable', $editInAConstanteTable);
        $stmt->bindParam(':deleteInAConstanteTable', $deleteInAConstanteTable);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    //-----------------------------

    // 5-4- 'suprInConstanteTable':
    //-----------------------------

    // 5-4-1 'suprCaserne':
    static public function deleteCaserneById($id)
    {
        $pdo = DB::getInstance()->getPDO();
        //on regarde si la constante n'est pas utilisée ailleurs
        $req = '
            SELECT c.id
            FROM Casernes c
            WHERE c.id = :id
            AND c.id NOT IN(SELECT a.id
                FROM Affectation a)
        ';

        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();
        //si présent on retourne l'erreur
        if ($result == false){
            $stmt->closeCursor();
            return 'La supression n\'a pas pu être effectuée, cette constante est liée à des dossiers : vérifiez qu\'aucun militaire n\'est affecté à cette caserne';
        } else{ //sinon on supprime
            $req = '
                DELETE
                FROM Casernes 
                WHERE id = :id
            ';

            $stmt = $pdo->prepare($req);
            $data = ['id'=>$id];
            $stmt->execute($data);
            $stmt->closeCursor();
            return null;
        }
    }
    
    // 5-4-2 'suprRegiment':
    static public function deleteRegimentById($id)
    {
        $pdo = DB::getInstance()->getPDO();
        //on regarde si la constante n'est pas utilisée ailleurs
        $req = '
            SELECT r.id
            FROM Regiment r
            WHERE r.id = :id
            AND r.id NOT IN(SELECT ar.id
                FROM AppartientRegiment ar)
        ';

        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();
        //si présent on retourne l'erreur
        if ($result == false){
            $stmt->closeCursor();
            return 'La supression n\'a pas pu être effectuée, cette constante est liée à des dossiers : vérifiez qu\'aucun militaire n\'appartient à ce régiment';
        } else{ //sinon on supprime
            $req = '
                DELETE
                FROM Regiment 
                WHERE id = :id
            ';

            $stmt = $pdo->prepare($req);
            $data = ['id'=>$id];
            $stmt->execute($data);
            $stmt->closeCursor();
            return null;
        }
    }

    // 5-4-3 'suprDiplome':
    static public function deleteDiplomeById($id)
    {

        $pdo = DB::getInstance()->getPDO();
        //on regarde si la constante n'est pas utilisée ailleurs
        $req = '
            SELECT d.acronyme
            FROM Diplomes d
            WHERE d.acronyme = :acronyme
            AND(
                :acronyme IN(SELECT id FROM PossedeDiplomes)
                OR :acronyme IN(SELECT diplome FROM ConditionsPromotions) 
                OR :acronyme IN(SELECT diplomeSup1 FROM ConditionsPromotions)
                OR :acronyme IN(SELECT diplomeSup2 FROM ConditionsPromotions)
            )
        ';

        $stmt = $pdo->prepare($req);
        $data = ['acronyme'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();
        //si présent on retourne l'erreur
        if ($result != false){ //fonctionnement inverse des autres, bug avec NOT IN...
            $stmt->closeCursor();
            return 'La supression n\'a pas pu être effectuée, cette constante est liée à des dossiers/conditions : vérifiez qu\'aucun militaire ne possède ce diplome, qu\'il n\'a pas d\'équivalence ou qu\'il ne fait pas parti d\'une condition de promotion';
        } else{ //sinon on supprime
            $req = '
                DELETE
                FROM Diplomes 
                WHERE acronyme = :acronyme
            ';

            $stmt = $pdo->prepare($req);
            $data = ['acronyme'=>$id];
            $stmt->execute($data);
            $stmt->closeCursor();
            return null;
        }
    }

    // 5-4-4 'suprGrade':
    static public function deleteGradeById($id)
    {
        $pdo = DB::getInstance()->getPDO();
        //on regarde si la constante n'est pas utilisée ailleurs
        $req = '
            SELECT g.id
            FROM Grades g
            WHERE g.id = :id
            AND g.id NOT IN(SELECT dg.id
                FROM DetientGrades dg)
            AND g.id NOT IN(SELECT cp.idGrade
                FROM ConditionsPromotions cp)
            AND g.id NOT IN(SELECT cr.idGrade
                FROM ConditionsRetraites cr)
        ';

        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();
        //si présent on retourne l'erreur
        if ($result == false){
            $stmt->closeCursor();
            return 'La supression n\'a pas pu être effectuée, cette constante est liée à des dossiers/conditions : vérifiez qu\'aucun militaire ne détient ce grade, qu\'il ne fait pas parti d\'une condition de promotion ou de retraite';
        } else{ //sinon on verifi qu'un autre grade possède la hierarchie de celui ci
            $req = 'SELECT hierarchie FROM Grades WHERE id =:id';
            $stmt = $pdo->prepare($req);
            $data = ['id'=>$id];
            $stmt->execute($data);
            $test = $stmt->fetch();

            $req = 'SELECT * FROM Grades WHERE hierarchie =:hierarchie AND id != :id';
            $stmt = $pdo->prepare($req);
            $stmt->bindParam(':hierarchie', $test['hierarchie']);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();
            $testResult = $stmt->fetch();

            if ($testResult == false){
                $stmt->closeCursor();
                return 'La supression n\'a pas pu être effectuée, cette constante est la dernière représentante du niveau hierarchique '.$test['hierarchie'].' ça supression entrainerait des problèmes importants. Il vous suffit de créer un nouveau grade du même niveau hierarchique que celui-ci et vous pourrez ensuite supprimer ce grade sans problème.';
            } else{ #tout est ok, on supprime le grade
                $req = '
                    DELETE
                    FROM Grades
                    WHERE id = :id
                ';

                $stmt = $pdo->prepare($req);
                $data = ['id'=>$id];
                $stmt->execute($data);
                $stmt->closeCursor();
                return null;
            } 
        }
    }

    // 5-4-5 'suprClasseDroits':
    static public function deleteClasseDroitsById($id)
    {
        $pdo = DB::getInstance()->getPDO();
        //on regarde si la constante n'est pas utilisée ailleurs
        $req = '
            SELECT d.role
            FROM Droits d
            WHERE d.role = :role
            AND d.role NOT IN(SELECT u.role
                FROM Users u)
        ';

        $stmt = $pdo->prepare($req);
        $data = ['role'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();
        //si présent on retourne l'erreur
        if ($result == false){
            $stmt->closeCursor();
            return 'La supression n\'a pas pu être effectuée, cette constante est liée à un ou plusieurs comptes, supprimer les préalablements ou moddifier leurs roles';
        } else{ //sinon on supprime
            $req = '
                DELETE
                FROM Droits 
                WHERE role = :role
            ';

            $stmt = $pdo->prepare($req);
            $data = ['role'=>$id];
            $stmt->execute($data);
            $stmt->closeCursor();
            return null;
        }
    }
    //-----------------------------

    //--------------------
    //6-module de sauvegarde et de gestion de crise
    //--------------------

    // 6-1- 'gestion de la bdd':
    //-----------------------------

    // 6-1-1 'sauvegardeCompleteBdd':
    //Utilisation d'une fonction générique

    // 6-1-2 'suprAllBdd':
    public static function deleteBdd()
    {
        //on doit respecter un ordre pour eviter d'avoir des problèmes de dépendance à cause des foreigns keys.
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd
        //supression des tables utilisants les constantes et militaire
        $pdo->exec("
            DROP TABLE PossedeDiplomes, Users, DiplomesEquivalences, DetientGrades, ConditionsPromotions, ConditionsRetraites, AppartientRegiment, Affectation
        ");
        //supression des tables contenants les constantes
        $pdo->exec("
            DROP TABLE Actifs, Archives, Casernes, Diplomes, Grades, Regiment, Retraites, Droits
        ");
        //supression de la table Militaire
        $pdo->exec("DROP TABLE Militaires");
        
        return 'action effectuée, veuillez réimporter la base manuellement pour pouvoir de nouveau utiliser l\'application';
    }

    // 6-1-3 'setAllUsersRightToNoRight':
    public static function setNoRights()
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $count = $pdo->exec("
            UPDATE Droits 
            SET noRights = 1
            WHERE role != 'superAdmin'
        ");
        
        if ($count > 0){
            return 'action effectuée : les droits des utilisateurs sont maintenant null (effectif après déconnexion si ils sont déjà connecté).';
        } else{
            return 'erreur, les droits des rôles sont déjà à noRights';
        }
    }


    public static function unsetNoRights()
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $count = $pdo->exec("
            UPDATE Droits 
            SET noRights = 0
            WHERE role != 'superAdmin'
        ");

        if ($count > 0){
            return 'action effectuée : les droits des utilisateurs sont maintenant rétablie (effectif après déconnexion si ils sont déjà connecté).';
        } else{
            return 'erreur, les droits des rôles ne sont déjà plus à noRights';
        }
    }
    //-----------------------------

    // 6-2- 'importer un DUMP':
    //-----------------------------

    public static function dropBaseAndImportANewOne($fileContent)
    {
        if (!empty($fileContent)){ //au cas ou..
            $pdo = DB::getInstance()->getPDO();
            //supression de la base
            //supression des tables utilisants les constantes et militaire
            $pdo->exec("
                DROP TABLE PossedeDiplomes, Users, DiplomesEquivalences, DetientGrades, ConditionsPromotions, ConditionsRetraites, AppartientRegiment, Affectation
            ");
            //supression des tables contenants les constantes
            $pdo->exec("
                DROP TABLE Actifs, Archives, Casernes, Diplomes, Grades, Regiment, Retraites, Droits
            ");
            //supression de la table Militaire
            $pdo->exec("DROP TABLE Militaires");

            //on réimporte la base à partir du fichier fourni
            $count = $pdo->exec($fileContent);

            //les requêtes d'ajout ne sont pas comptabilisé par exec. On vérifi donc les rapports d'erreur. 00000 correspond à un succès et 01000 correspond à un succès avec warning.
            $error = $pdo->errorInfo();
            if ($error[0] === '00000' || $error[0] === '01000'){
                $success = true;
            } else{
                $success = false;
            }

            if ($success) { //l'importation c'est bien passée
                return 'action effectuée : la base de données correspond désormais à la sauvegarde fournie';
            } else{ //bug, à ce stage il n'y a problablement plus de bdd donc pas cool.
                return 'une erreur innatendue est survenu, le bon fonctionnement de l\'application risque d\'être compromis. Veuillez réimporter manuellement la base de donnée. Une sauvegarde locale de la base de données a été effectuée avant cette opération dans le répertoire : /data/tmp';
            }
        } else{ //le fichier est vide, ce n'est normalement pas possible.
            return 'fatal error : une maintenance de la fonctionnalité est nécessaire';
        }
    }
    //-----------------------------

    // 6-3- 'gérer les fichiers de LOG':
    //-----------------------------

    //-----------------------------
    
}