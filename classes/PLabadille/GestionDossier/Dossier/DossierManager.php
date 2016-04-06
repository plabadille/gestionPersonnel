<?php
namespace PLabadille\GestionDossier\Dossier;
use PLabadille\Common\Bd\DB;

require_once 'classes/PLabadille/GestionDossier/Dossier/Dossier.php';

#Gère les requêtes en BDD, est appelé par le controller.
class DossierManager
{
    #Reccupère tous les dossiers militaires
    public static function getAll() 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select * from Militaires';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $dossier = array();
        foreach ($result as $attributs) {
            $dossier[] = new Dossier($attributs);
        }
        return $dossier;
    }

    #Permet de réccupérer un dossier spécifique selon son matricule
    public static function getOneFromId($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select * from Militaires where matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();

        $attributs = $result;
        $dossier = new Dossier($attributs);
        return $dossier;
    }

    #permet de réccupérer un ou plusieurs dossier correspondant au résultat d'une recherche
    #formulaire dans le toHtml permettant l'affichage de tous les dossiers.
    static public function rechercherIdOrName($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select * from Militaires where nom like concat("%",:search,"%") OR matricule = :search';
        $stmt = $pdo->prepare($req);
        $data = ['search'=>$search];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $dossier = array();
        foreach ($result as $attributs) {
            $dossier[] = new Dossier($attributs);
        }
        return $dossier;
    }

    #Permet d'ajouter un dossier en BDD
    public static function ajouterUnDossier($attributs) 
    {
        $pdo = DB::getInstance()->getPDO();
        //requête d'insertion en bdd
        $stmt = $pdo->prepare("
            INSERT INTO Militaires 
                (nom, prenom, date_naissance, genre, tel1, tel2, email, adresse, date_recrutement) 
            VALUES
                (:nom, :prenom, :date_naissance, :genre, :tel1, :tel2, :email, :adresse, :date_recrutement)
        ");
        $stmt->bindParam(':nom', $attributs['nom']);
        $stmt->bindParam(':prenom', $attributs['prenom']);
        $stmt->bindParam(':date_naissance', $attributs['date_naissance']);
        $stmt->bindParam(':genre', $attributs['genre']);
        $stmt->bindParam(':tel1', $attributs['tel1']);
        $stmt->bindParam(':tel2', $attributs['tel2']);
        $stmt->bindParam(':email', $attributs['email']);
        $stmt->bindParam(':adresse', $attributs['adresse']);
        $stmt->bindParam(':date_recrutement', $attributs['date_recrutement']);
        
        $stmt->execute();
        //lastInsertId retourne l'id de la dernière ligne insérée.
        $matricule = $pdo->lastInsertId();
        $stmt->closeCursor();
        //affichage de l'article créé
        $dossier = DossierManager::getOneFromId($matricule);
        return $dossier;
    }

    #Permet d'éditer un dossier en BDD
    public static function editerUnDossier($attributs)
    {
        $pdo = DB::getInstance()->getPDO();
        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Militaires 
            SET 
                nom = :nom, 
                prenom = :prenom, 
                date_naissance = :date_naissance, 
                genre = :genre, 
                tel1 = :tel1, 
                tel2 = :tel2,
                email = :email, 
                adresse = :adresse, 
                date_recrutement = :date_recrutement
            WHERE 
                matricule = :id
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':nom', $attributs['nom']);
        $stmt->bindParam(':prenom', $attributs['prenom']);
        $stmt->bindParam(':date_naissance', $attributs['date_naissance']);
        $stmt->bindParam(':genre', $attributs['genre']);
        $stmt->bindParam(':tel1', $attributs['tel1']);
        $stmt->bindParam(':tel2', $attributs['tel2']);
        $stmt->bindParam(':email', $attributs['email']);
        $stmt->bindParam(':adresse', $attributs['adresse']);
        $stmt->bindParam(':date_recrutement', $attributs['date_recrutement']);
        
        $stmt->execute();
        $stmt->closeCursor();

        //affichage de l'article créé
        $dossier = DossierManager::getOneFromId($attributs['id']);
        return $dossier;
    }
}