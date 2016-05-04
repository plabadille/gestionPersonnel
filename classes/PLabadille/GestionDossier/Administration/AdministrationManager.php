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
        $stmt->bindParam(':pass', $attributs['hash']);
        
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
            WHERE EXISTS (select matricule from Archives where matricule = :matricule)
            OR EXISTS (select matricule from Retraites where matricule = :matricule)
        ';

        $stmt = $pdo->prepare($req);
        $data = ['matricule'=>$id];
        $stmt->execute($data);
    }
    
}