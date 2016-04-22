<?php
namespace PLabadille\GestionDossier\Dossier;
use PLabadille\Common\Bd\DB;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 1- Module mon dossier
# 2- Module de gestion et ajout de dossier
# 3- Module de gestion de promotion et retraite
//--------------------

#Gère les requêtes en BDD, est appelé par le controller.
class DossierManager
{
    //--------------------
    //x-Fonctions utilitaires (potentiellement utilisée par n'importe quelle fonction, elle retourne des informations disponibles en bd)
    //--------------------
    #Reccupère tous les dossiers militaires
    public static function getAll() 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT m.matricule, nom, prenom, date_naissance, genre, tel1, tel2, email, adresse, date_recrutement, saisie_by
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
        ';
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

        $req = 
        '
            SELECT m.matricule, nom, prenom, date_naissance, genre, tel1, tel2, email, adresse, date_recrutement, saisie_by
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            where m.matricule = :matricule
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();
        $attributs = $result;
        $dossier = new Dossier($attributs);
        return $dossier;
    }

    //--------------------
    //1- Module mon dossier
    //--------------------

    // 1-1- 'seeOwnFolderModule':
    static public function getUserFullFolder($matricule)
    {
        $pdo = DB::getInstance()->getPDO();
        //on fait toutes les requêtes nécessaires pour obtenir le dossier militaire complet. Une seule requête pour simplifier les choses niveau sécurité.
        $req = 
        '
            SELECT *
            FROM Militaires
            where matricule = :matricule
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $informations = $stmt->fetch();

        $req = 
        '
            SELECT nom, date_affectation FROM Affectation a
            INNER JOIN Casernes c ON a.id = c.id
            WHERE a.matricule = :matricule ORDER BY date_affectation DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $casernes = $stmt->fetchAll();

        $req = 
        '
            SELECT id, date_appartenance FROM AppartientRegiment
            WHERE matricule = :matricule ORDER BY date_appartenance DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $regiments = $stmt->fetchAll();

        $req = 
        '
            SELECT grade, date_promotion FROM DetientGrades dg
            INNER JOIN Grades g ON dg.id = g.id
            WHERE matricule = :matricule order by date_promotion DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $grades = $stmt->fetchAll();

        $req = 
        '
            SELECT acronyme, intitule, date_obtention FROM PossedeDiplomes pd
            INNER JOIN Diplomes d ON pd.id = d.acronyme
            WHERE matricule = :matricule order by date_obtention DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $diplomes = $stmt->fetchAll();

        $stmt->closeCursor();

        $dossier['informations'] = new Dossier($informations);
        $dossier['casernes'] = $casernes;
        $dossier['regiments'] = $regiments;
        $dossier['grades'] = $grades;
        $dossier['diplomes'] = $diplomes;

        return $dossier;
    }

    // 1-2- 'editOwnFolderPersonalInformation':
    static public function editerSonDossier($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE Militaires 
            SET 
                tel1 = :tel1, 
                tel2 = :tel2,
                email = :email, 
                adresse = :adresse
            WHERE 
                matricule = :id
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':tel1', $attributs['tel1']);
        $stmt->bindParam(':tel2', $attributs['tel2']);
        $stmt->bindParam(':email', $attributs['email']);
        $stmt->bindParam(':adresse', $attributs['adresse']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }

    //--------------------
    //2- Module de gestion et ajout de dossier
    //--------------------

    // 2-1- 'listCreatedFolder':

    public static function getAllCreatedFolder($username) 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT m.matricule, nom, prenom, date_naissance, genre, tel1, tel2, email, adresse, date_recrutement, saisie_by
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.saisie_by = :username;
        ';
        $stmt = $pdo->prepare($req);
        $data = ['username' => $username];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $dossier = array();
        foreach ($result as $attributs) {
            $dossier[] = new Dossier($attributs);
        }
        return $dossier;
    }

    static public function rechercherIdOrNameCreatedFolder($search, $username)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT m.matricule, nom, prenom, date_naissance, genre, tel1, tel2, email, adresse, date_recrutement, a.saisie_by
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.saisie_by = :username AND m.nom like concat("%",:search,"%") OR m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $dossier = array();
        foreach ($result as $attributs) {
            $dossier[] = new Dossier($attributs);
        }
        return $dossier;
    }

    // 2-2- 'listAllFolder':
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

    // 2-3- 'seeCreatedFolder':
    #utilises des fonctions génériques situées tout en haut.
    #to do
    
    // 2-4- 'seeAllFolder':
    #utilises des fonctions génériques situées tout en haut.

    // 2-5 'createFolder':
    #utilises des fonctions génériques situées tout en haut.
    #Permet d'ajouter un dossier en BDD
    public static function ajouterUnDossier($attributs) 
    {
        $pdo = DB::getInstance()->getPDO();
        $stmt = $pdo->prepare("
            SELECT *
            FROM Militaires
            WHERE nom = :nom AND prenom = :prenom AND date_naissance = :date_naissance AND date_recrutement = :date_recrutement
        ");
        $stmt->bindParam(':nom', $attributs['nom']);
        $stmt->bindParam(':prenom', $attributs['prenom']);
        $stmt->bindParam(':date_naissance', $attributs['date_naissance']);
        $stmt->bindParam(':date_recrutement', $attributs['date_recrutement']);
        
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false){
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

            //on indique maintenant que le militaire est actif (ainsi que la personne qui l'a enregistré)
            $stmt = $pdo->prepare("
                INSERT INTO Actifs 
                    (matricule, saisie_by) 
                VALUES
                    (:matricule, :saisieBy)
            ");
            $stmt->bindParam(':matricule', $matricule);
            $stmt->bindParam(':saisieBy', $attributs['create_by']);
            
            $stmt->execute();

            $stmt->closeCursor();
            //affichage de l'article créé
            $dossier = DossierManager::getOneFromId($matricule);
            return $dossier;
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
    }

    // 2-6- 'addElementToAFolder':
    #utilises des fonctions génériques situées tout en haut
    #Permet d'ajouter une affectation en BDD

    // 2-6-1- Affectation (casernes)
    public static function getAffectationsById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            select nom, date_affectation from Affectation a
            INNER JOIN Casernes c ON a.id = c.id
            where a.matricule = :matricule order by date_affectation DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function listeNomCaserne()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select id, nom from Casernes';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        #on associe les clés au nom de caserne
        foreach ($result as $key => $value) {
            foreach ($value as $key => $name) {
                if ($key == "id"){
                    $id = $name;
                } else{
                    $caserneName[$id] = $name;
                }
            }
        }
        
        return $caserneName;
    }

    public static function ajouterUneAffectation($attributs) 
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
            SELECT *
            FROM Affectation
            WHERE matricule = :matricule AND id = :id AND date_affectation = :date_affectation
        ");
        $stmt->bindParam(':matricule', $attributs['id']);
        $stmt->bindParam(':id', $attributs['caserneId']);
        $stmt->bindParam(':date_affectation', $attributs['date_affectation']);
        
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result == false){
            //requête d'insertion en bdd
            $stmt = $pdo->prepare("
                INSERT INTO Affectation 
                    (matricule, id, date_affectation) 
                VALUES
                    (:matricule, :id, :date_affectation)
            ");
            $stmt->bindParam(':matricule', $attributs['id']);
            $stmt->bindParam(':id', $attributs['caserneId']);
            $stmt->bindParam(':date_affectation', $attributs['date_affectation']);
            
            $stmt->execute();
            //lastInsertId retourne l'id de la dernière ligne insérée.
            $stmt->closeCursor();
            //affichage de l'article créé
            $dossier = DossierManager::getOneFromId($attributs['id']);
            return $dossier;
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
        
    }

    // 2-6-2- Appartenances (régiments)
    public static function getAppartenancesById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            select id, date_appartenance from AppartientRegiment
            where matricule = :matricule order by date_appartenance DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function listeNomRegiment()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select id from Regiment';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        #on associe les clés au nom de caserne
        foreach ($result as $key => $value) {
            foreach ($value as $key => $name) {
                    $regimentName[] = $name;
            }
        }
        
        return $regimentName;
    }

    #Permet d'ajouter une appartenance regiment en BDD
    public static function ajouterUneAppartenanceRegiment($attributs) 
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
            SELECT *
            FROM AppartientRegiment
            WHERE matricule = :matricule AND id = :id AND date_appartenance = :date_appartenance
        ");
        $stmt->bindParam(':matricule', $attributs['id']);
        $stmt->bindParam(':id', $attributs['regimentId']);
        $stmt->bindParam(':date_appartenance', $attributs['date_appartenance']);
        
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result == false){
        //requête d'insertion en bdd
            $stmt = $pdo->prepare("
                INSERT INTO AppartientRegiment 
                    (matricule, id, date_appartenance) 
                VALUES
                    (:matricule, :id, :date_appartenance)
            ");
            $stmt->bindParam(':matricule', $attributs['id']);
            $stmt->bindParam(':id', $attributs['regimentId']);
            $stmt->bindParam(':date_appartenance', $attributs['date_appartenance']);
            
            $stmt->execute();
            //lastInsertId retourne l'id de la dernière ligne insérée.
            $stmt->closeCursor();
            //affichage de l'article créé
            $dossier = DossierManager::getOneFromId($attributs['id']);
            return $dossier;
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
    }

    // 2-6-3- Detient (grades)
    public static function getGradesDetenuById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT grade, date_promotion FROM DetientGrades dg
            INNER JOIN Grades g ON dg.id = g.id
            WHERE matricule = :matricule order by date_promotion DESC
        ';

        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function listeNomGrade()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select id, grade from Grades';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        #on associe les clés au nom de grade
        foreach ($result as $key => $value) {
            foreach ($value as $key => $name) {
                if ($key == "id"){
                    $id = $name;
                } else{
                    $gradeName[$id] = $name;
                }
            }
        }
        
        return $gradeName;
    }

    #Permet d'ajouter un grade detenu en BDD
    public static function ajouterUnGradeDetenu($attributs) 
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
            SELECT *
            FROM DetientGrades
            WHERE matricule = :matricule AND id = :id AND date_promotion = :date_promotion
        ");
        $stmt->bindParam(':matricule', $attributs['id']);
        $stmt->bindParam(':id', $attributs['gradeId']);
        $stmt->bindParam(':date_promotion', $attributs['date_promotion']);
        
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result == false){
        //requête d'insertion en bdd
            $stmt = $pdo->prepare("
                INSERT INTO DetientGrades 
                    (matricule, id, date_promotion) 
                VALUES
                    (:matricule, :id, :date_promotion)
            ");
            $stmt->bindParam(':matricule', $attributs['id']);
            $stmt->bindParam(':id', $attributs['gradeId']);
            $stmt->bindParam(':date_promotion', $attributs['date_promotion']);
            
            $stmt->execute();
            //lastInsertId retourne l'id de la dernière ligne insérée.
            $stmt->closeCursor();
            //affichage de l'article créé
            $dossier = DossierManager::getOneFromId($attributs['id']);
            return $dossier;
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
    }

    // 2-6-4- Possede (diplomes)
    public static function getDiplomesPossedeById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT acronyme, intitule, date_obtention FROM PossedeDiplomes pd
            INNER JOIN Diplomes d ON pd.id = d.acronyme
            WHERE matricule = :matricule order by date_obtention DESC
        ';

        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    static public function listeNomDiplome()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select acronyme, intitule from Diplomes';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        #on associe les clés au nom de diplome
        foreach ($result as $key => $value) {
            foreach ($value as $key => $name) {
                if ($key == "acronyme"){
                    $id = $name;
                } else{
                    $diplomeName[$id] = $name;
                }
            }
        }
        
        return $diplomeName;
    }

    #Permet d'ajouter un diplome possédé en BDD
    public static function ajouterUnDiplomePossede($attributs) 
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("
            SELECT *
            FROM PossedeDiplomes
            WHERE matricule = :matricule AND id = :id
        ");
        $stmt->bindParam(':matricule', $attributs['id']);
        $stmt->bindParam(':id', $attributs['diplomeId']);
        
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result == false){
        //requête d'insertion en bdd
            $stmt = $pdo->prepare("
                INSERT INTO PossedeDiplomes 
                    (matricule, id, date_obtention, pays_obtention, organisme_formateur) 
                VALUES
                    (:matricule, :id, :date_obtention, :pays_obtention, :organisme_formateur)
            ");
            $stmt->bindParam(':matricule', $attributs['id']);
            $stmt->bindParam(':id', $attributs['diplomeId']);
            $stmt->bindParam(':date_obtention', $attributs['date_obtention']);
            $stmt->bindParam(':pays_obtention', $attributs['pays_obtention']);
            $stmt->bindParam(':organisme_formateur', $attributs['organisme_formateur']);
            
            $stmt->execute();
            //lastInsertId retourne l'id de la dernière ligne insérée.
            $stmt->closeCursor();
            //affichage de l'article créé
            $dossier = DossierManager::getOneFromId($attributs['id']);
            return $dossier;
        } else{
            $doublonError = 'Système anti-doublon : ce diplome est déjà rentré en base pour ce militaire, impossible de l\'ajouter à nouveau';
            return $doublonError;
        }
    }

    // 2-7- 'editInformationIfAuthor':
    
    public static function getCreatorById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT saisie_by
            FROM Actifs
            WHERE matricule = :matricule
        ';

        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();

        $creator = $result['saisie_by'];
        return $creator;
    }

    // 2-8- 'editInformation':
    #utilises des fonctions génériques situées tout en haut.
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

    // 2-9- 'deleteInformation':
    #to do

    // 2-10 'useFileToAddFolders':
    #to do

    //--------------------
    //3-module gestion promotion et retraite
    //--------------------

    // 3-1- 'listEligible':
    #utilises des fonctions génériques situées tout en haut.

        #Reccupère tous les dossiers militaires
    public static function getAllEligiblePromotion() 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT * from Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE eligible_promotion = 1
        ';
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

    static public function rechercherIdOrNamePromotion($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT * from Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_promotion = 1
            )
            AND nom like concat("%",:search,"%") OR matricule = :search
        ';

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

    #Reccupère tous les dossiers militaires
    public static function getAllEligibleRetraite() 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = ' 
            SELECT * from Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE eligible_retraite = 1
        ';
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

    static public function rechercherIdOrNameRetraite($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req =
        '
            SELECT * from Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_retraite = 1
            )
            AND nom like concat("%",:search,"%") OR matricule = :search
        ';
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
    
    // 3-2- 'editEligibleCondition':
    #to do

    // 3-3- 'addEligibleCondition':
    #to do

    // 3-4- 'canRetireAFolder':
    #to do

    // 3-5- 'editEligibleEmailContent':
    #to do

    // 3-6- 'uploadFileForMail':
    #to do

    // 3-7- 'changePieceJointeForEligibleMail':
    #to do
}