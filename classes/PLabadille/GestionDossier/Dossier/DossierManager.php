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
            ORDER BY nom
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

    public static function getCaserneById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT id
            FROM Casernes
            where id = :id
        ';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        return $result;
    }

    public static function getRegimentById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT id
            FROM Regiment
            where id = :id
        ';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        return $result;
    }

    public static function getGradeById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT grade
            FROM Grades
            where id = :id
        ';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        return $result;
    }

    public static function getDiplomeById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT acronyme
            FROM Diplomes
            where acronyme = :acronyme
        ';
        $stmt = $pdo->prepare($req);
        $data = ['acronyme' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        return $result;
    }

    public static function getGradeSup($grade)
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'select hierarchie from Grades where id = :grade';
        $stmt = $pdo->prepare($req);
        $data = ['grade' => $grade];
        $stmt->execute($data);
        $result = $stmt->fetchAll();

        #On retire 1 au résultat afin de trouver le grade supérieur
        $result['0']['hierarchie'] = $result['0']['hierarchie'] -1;
        #On refait une requête afin de réccupérer les id des grades (possible plusieurs selon parcours pro)
        $req = 'select grade from Grades where hierarchie = :hierarchie';
        $stmt = $pdo->prepare($req);
        $data = ['hierarchie' => $result['0']['hierarchie']];
        $stmt->execute($data);
        $result = $stmt->fetchAll();

        $stmt->closeCursor();

        #on retire les étages de tableau inutile puis on renvoit.
        foreach ($result as $num) {
            foreach ($num as $key => $value) {
                $gradesSup[] = $value;
            } 
        }
        return $gradesSup;
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
            SELECT nom, date_affectation, nb FROM Affectation a
            INNER JOIN Casernes c ON a.id = c.id
            WHERE a.matricule = :matricule ORDER BY date_affectation DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $casernes = $stmt->fetchAll();

        $req = 
        '
            SELECT id, date_appartenance, nb FROM AppartientRegiment
            WHERE matricule = :matricule ORDER BY date_appartenance DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $regiments = $stmt->fetchAll();

        $req = 
        '
            SELECT grade, date_promotion, num FROM DetientGrades dg
            INNER JOIN Grades g ON dg.id = g.id
            WHERE matricule = :matricule order by date_promotion DESC
        ';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $grades = $stmt->fetchAll();

        $req = 
        '
            SELECT acronyme, intitule, date_obtention, num FROM PossedeDiplomes pd
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
            ORDER BY nom
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
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT nom, prenom, date_naissance, genre, tel1, tel2, email, adresse, date_recrutement, a.saisie_by
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.saisie_by = :username AND m.nom like concat("%",:search,"%") OR a.saisie_by = :username AND m.matricule = :search
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

    static public function ajaxRechercherCreatedName($search, $username)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT nom, prenom
            FROM Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE a.saisie_by = :username AND m.nom like concat("%",:search,"%") OR a.saisie_by = :username AND m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    // 2-2- 'listAllFolder':
    #permet de réccupérer un ou plusieurs dossier correspondant au résultat d'une recherche
    #formulaire dans le toHtml permettant l'affichage de tous les dossiers.
    static public function rechercherIdOrName($search)
    {
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req = 
        '
            SELECT m.matricule, nom, prenom
            FROM Militaires m
            JOIN Actifs a ON a.matricule = m.matricule
            where nom like concat("%",:search,"%") OR m.matricule = :search
           
        ';
        $stmt = $pdo->prepare($req);
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

    static public function ajaxRechercherName($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            select nom, prenom
            from Militaires m
            JOIN Actifs a ON a.matricule = m.matricule
            where nom like concat("%",:search,"%") OR m.matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $data = ['search'=>$search];
        $stmt->execute($data);

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    // 2-3- 'seeCreatedFolder':
    #utilises des fonctions génériques situées tout en haut.
    
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
            select nom, date_affectation, nb from Affectation a
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

    static public function ajaxListeNomCaserne($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            select id, nom
            from Casernes
            WHERE id like concat("%",:search,"%")
                OR nom like concat ("%",:search,"%")
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
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
            select id, date_appartenance, nb from AppartientRegiment
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

    static public function ajaxListeNomRegiment($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            select id
            from Regiment
            WHERE id like concat("%",:search,"%")
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
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
            SELECT grade, date_promotion, num FROM DetientGrades dg
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

    static public function ajaxListeNomGrade($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            select id, grade
            from Grades
            WHERE grade like concat("%",:search,"%")
                OR id like concat ("%",:search,"%")
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
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

            //Set Eligible retraite et promotion à false (on a changé le grade donc il faut revérifier les conditions)
            $stmt = $pdo->prepare(
            "
                UPDATE Actifs 
                SET 
                    eligible_retraite = 0, 
                    eligible_promotion = 0 
                WHERE matricule = :id
            ");
            $stmt->bindParam(':id', $attributs['id']);
            $stmt->execute();

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
            SELECT acronyme, intitule, date_obtention, num FROM PossedeDiplomes pd
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

    static public function ajaxListeNomDiplome($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            select acronyme, intitule
            from Diplomes
            WHERE acronyme like concat("%",:search,"%")
                OR intitule like concat ("%",:search,"%")
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
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

    // 2-9-1 delete affectation:
    public static function suprAffectationById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT matricule from Affectation WHERE nb = :nb';
        $stmt = $pdo->prepare($req);
        $data = ['nb'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();

        //on supprime le grade detenu
        $req = 'DELETE FROM Affectation WHERE nb = :nb';
        $stmt = $pdo->prepare($req);
        $data = ['nb'=>$id];
        $stmt->execute($data);

        //Set Eligible retraite et promotion à false (on a changé le grade donc il faut revérifier les conditions)
        $stmt = $pdo->prepare(
        "
            UPDATE Actifs 
            SET 
                eligible_retraite = 0, 
                eligible_promotion = 0 
            WHERE matricule = :id
        ");
        $stmt->bindParam(':id', $result['matricule']);
        $stmt->execute();

        $stmt->closeCursor();
        $matricule = $result['matricule'];

        return $matricule;
    }
    #Permet de réccupérer l'affectation par le biais de la clé primaire
    public static function getAffectationByClef($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT * from Affectation WHERE nb = :nb';
        $stmt = $pdo->prepare($req);
        $data = ['nb'=>$id];
        $stmt->execute($data);
        $affectation = $stmt->fetch();

        return $affectation;
    }

    // 2-9-2 delete appartenance:
    public static function suprRegimentAppartenanceById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT matricule from AppartientRegiment WHERE nb = :nb';
        $stmt = $pdo->prepare($req);
        $data = ['nb'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();

        //on supprime le grade detenu
        $req = 'DELETE FROM AppartientRegiment WHERE nb = :nb';
        $stmt = $pdo->prepare($req);
        $data = ['nb'=>$id];
        $stmt->execute($data);

        //Set Eligible promotion à false (on a changé le grade donc il faut revérifier les conditions)
        $stmt = $pdo->prepare(
        "
            UPDATE Actifs 
            SET eligible_promotion = 0 
            WHERE matricule = :id
        ");
        $stmt->bindParam(':id', $result['matricule']);
        $stmt->execute();

        $stmt->closeCursor();
        $matricule = $result['matricule'];

        return $matricule;
    }
    #Permet de réccupérer l'appartenance par le biais de la clé primaire
    public static function getAppartenanceByClef($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT * from AppartientRegiment WHERE nb = :nb';
        $stmt = $pdo->prepare($req);
        $data = ['nb'=>$id];
        $stmt->execute($data);
        $appartenance = $stmt->fetch();

        return $appartenance;
    }

    // 2-9-3 delete grade detenu:
    public static function suprGradeDetenuById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT matricule from DetientGrades WHERE num = :num';
        $stmt = $pdo->prepare($req);
        $data = ['num'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();

        //on supprime le grade detenu
        $req = 'DELETE FROM DetientGrades WHERE num = :num';
        $stmt = $pdo->prepare($req);
        $data = ['num'=>$id];
        $stmt->execute($data);

        //Set Eligible retraite et promotion à false (on a changé le grade donc il faut revérifier les conditions)
        $stmt = $pdo->prepare(
        "
            UPDATE Actifs 
            SET 
                eligible_retraite = 0, 
                eligible_promotion = 0 
            WHERE matricule = :id
        ");
        $stmt->bindParam(':id', $result['matricule']);
        $stmt->execute();

        $stmt->closeCursor();
        $matricule = $result['matricule'];

        return $matricule;
    }
    #Permet de réccupérer le grade detenu par le biais de la clé primaire
    public static function getDetenuByClef($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT * from DetientGrades WHERE num = :num';
        $stmt = $pdo->prepare($req);
        $data = ['num'=>$id];
        $stmt->execute($data);
        $detenu = $stmt->fetch();

        return $detenu;
    }

    // 2-9-4 delete diplome possédé:
    public static function suprDiplomePossedeById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT matricule from PossedeDiplomes WHERE num = :num';
        $stmt = $pdo->prepare($req);
        $data = ['num'=>$id];
        $stmt->execute($data);
        $result = $stmt->fetch();

        //on supprime le grade detenu
        $req = 'DELETE FROM PossedeDiplomes WHERE num = :num';
        $stmt = $pdo->prepare($req);
        $data = ['num'=>$id];
        $stmt->execute($data);

        //Set Eligible promotion à false (on a changé le grade donc il faut revérifier les conditions)
        $stmt = $pdo->prepare(
        "
            UPDATE Actifs 
            SET eligible_promotion = 0 
            WHERE matricule = :id
        ");
        $stmt->bindParam(':id', $result['matricule']);
        $stmt->execute();

        $stmt->closeCursor();
        $matricule = $result['matricule'];

        return $matricule;
    }
    #Permet de réccupérer le diplome par le biais de la clé primaire
    public static function getPossedeByClef($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT * from PossedeDiplomes WHERE num = :num';
        $stmt = $pdo->prepare($req);
        $data = ['num'=>$id];
        $stmt->execute($data);
        $possede = $stmt->fetch();

        return $possede;
    }

    // 2-10 'useFileToAddFolders':
    #to do

    // 2-11- 'canArchiveAFolder':
    public static function archiverUnDossier($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd
        $stmt = $pdo->prepare("
            INSERT INTO Archives 
                (matricule, date_deces, cause_deces) 
            VALUES
                (:matricule, :date_deces, :cause_deces)
        ");
        $stmt->bindParam(':matricule', $attributs['id']);
        $stmt->bindParam(':date_deces', $attributs['date_deces']);
        $stmt->bindParam(':cause_deces', $attributs['cause_deces']);
        $stmt->execute();

        //on supprime l'entrée dans la table actif'
        $req = 'DELETE FROM Actifs WHERE matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule'=>$attributs['id']];
        $stmt->execute($data);
    }

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
            SELECT a.matricule, nom, prenom, dg.id from Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            INNER JOIN DetientGrades dg ON dg.matricule = a.matricule
            WHERE eligible_promotion = 1
            ORDER BY nom
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
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT * from Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_promotion = 1
            ) AND nom like concat("%",:search,"%") 
            OR matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_promotion = 1
            ) AND matricule = :search
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

    static public function ajaxRechercherEligiblePromotion($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT nom, prenom from Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_promotion = 1
            ) AND nom like concat("%",:search,"%") 
            OR matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_promotion = 1
            ) AND matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    #Reccupère tous les dossiers militaires
    public static function getAllEligibleRetraite() 
    {
        $pdo = DB::getInstance()->getPDO();

        $req = ' 
            SELECT * from Militaires m
            INNER JOIN Actifs a ON m.matricule = a.matricule
            WHERE eligible_retraite = 1
            ORDER BY nom
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
        //on ne conserve que le nom en cas d'Ajax
        $search = explode(' ', $search);
        $search = $search[0];

        $pdo = DB::getInstance()->getPDO();

        $req =
        '
            SELECT * from Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_retraite = 1
            ) AND nom like concat("%",:search,"%") 
            OR matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_retraite = 1
            ) AND matricule = :search
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

    static public function ajaxRechercherEligibleRetraite($search)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = '
            SELECT nom, prenom from Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_retraite = 1
            ) AND nom like concat("%",:search,"%") 
            OR matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_retraite = 1
            ) AND matricule = :search
        ';
        $stmt = $pdo->prepare($req);
        $stmt->bindParam(':search', $search);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }
    
    // 3-2- 'editEligibleCondition':
    public static function getAllConditionsPromotion()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'SELECT * from ConditionsPromotions';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $conditionsPromotion = $stmt->fetchAll();
        $stmt->closeCursor();

        return $conditionsPromotion;
    }

    public static function getConditionPromotionFromId($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'SELECT * FROM ConditionsPromotions WHERE id = :id ';
        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);

        $conditionPromotion= $stmt->fetch();
        $stmt->closeCursor();

        return $conditionPromotion;
    }

    public static function editerConditionsPromotion($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE ConditionsPromotions 
            SET
                idGrade = :idGrade,
                annees_service_FA = :annees_service_FA,
                annees_service_GN = :annees_service_GN,
                annees_service_SOE = :annees_service_SOE,
                annees_service_grade = :annees_service_grade, 
                diplome = :diplome,
                diplomeSup1 = :diplomeSup1, 
                diplomeSup2 = :diplomeSup2
            WHERE 
                id = :id
        ");
        
        //pour éviter des erreurs de foreign key constraint, on remplace une chaine vide par null si on veut set à null. 
        $attributs['diplome'] = (!empty($attributs['diplome'])) ? $attributs['diplome'] : null ;
        $attributs['diplomeSup1'] = (!empty($attributs['diplomeSup1'])) ? $attributs['diplomeSup1'] : null ;
        $attributs['diplomeSup2'] = (!empty($attributs['diplomeSup2'])) ? $attributs['diplomeSup2'] : null ;

        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':idGrade', $attributs['idGrade']);
        $stmt->bindParam(':annees_service_FA', $attributs['annees_service_FA']);
        $stmt->bindParam(':annees_service_GN', $attributs['annees_service_GN']);
        $stmt->bindParam(':annees_service_SOE', $attributs['annees_service_SOE']);
        $stmt->bindParam(':annees_service_grade', $attributs['annees_service_grade']);
        $stmt->bindParam(':diplome', $attributs['diplome']);
        $stmt->bindParam(':diplomeSup1', $attributs['diplomeSup1']);
        $stmt->bindParam(':diplomeSup2', $attributs['diplomeSup2']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }


    public static function getAllConditionsRetraite()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'SELECT * from ConditionsRetraites';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $conditionsRetraite = $stmt->fetchAll();
        $stmt->closeCursor();

        return $conditionsRetraite;
    }

    public static function getConditionRetraiteFromId($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'SELECT * FROM ConditionsRetraites WHERE id = :id ';
        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);

        $conditionRetraite= $stmt->fetch();
        $stmt->closeCursor();

        return $conditionRetraite;
    }

    public static function EditerConditionsRetraite($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd   
        $stmt = $pdo->prepare("
            UPDATE ConditionsRetraites 
            SET 
                idGrade = :idGrade, 
                service_effectif = :service_effectif,
                age = :age
            WHERE 
                id = :id
        ");
        
        $stmt->bindParam(':id', $attributs['id']);
        $stmt->bindParam(':idGrade', $attributs['idGrade']);
        $stmt->bindParam(':service_effectif', $attributs['service_effectif']);
        $stmt->bindParam(':age', $attributs['age']);
        
        $stmt->execute();
        $stmt->closeCursor();
    }


    // 3-3- 'addEligibleCondition':
    
    public static function ajouterConditionsRetraite($attributs)
    {
        $pdo = DB::getInstance()->getPDO();
        $stmt = $pdo->prepare("
            SELECT *
            FROM ConditionsRetraites
            WHERE idGrade = :idGrade AND service_effectif = :service_effectif AND age = :age 
        ");
        $stmt->bindParam(':idGrade', $attributs['idGrade']);
        $stmt->bindParam(':service_effectif', $attributs['service_effectif']);
        $stmt->bindParam(':age', $attributs['age']);
        
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false){
            //requête d'insertion en bdd
            $stmt = $pdo->prepare("
                INSERT INTO ConditionsRetraites 
                    (idGrade, service_effectif, age) 
                VALUES
                    (:idGrade, :service_effectif, :age)
            ");
            $stmt->bindParam(':idGrade', $attributs['idGrade']);
            $stmt->bindParam(':service_effectif', $attributs['service_effectif']);
            $stmt->bindParam(':age', $attributs['age']);
            
            $stmt->execute();
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
    }

    public static function ajouterConditionsPromotion($attributs)
    {
        //pour éviter des erreurs de foreign key constraint, on remplace une chaine vide par null si on veut set à null. 
        $attributs['diplome'] = (!empty($attributs['diplome'])) ? $attributs['diplome'] : null ;
        $attributs['diplomeSup1'] = (!empty($attributs['diplomeSup1'])) ? $attributs['diplomeSup1'] : null ;
        $attributs['diplomeSup2'] = (!empty($attributs['diplomeSup2'])) ? $attributs['diplomeSup2'] : null ;
        
        $pdo = DB::getInstance()->getPDO();
        $stmt = $pdo->prepare("
            SELECT *
            FROM ConditionsPromotions
            WHERE idGrade = :idGrade AND annees_service_FA = :annees_service_FA AND annees_service_GN = :annees_service_GN AND annees_service_SOE = :annees_service_SOE AND annees_service_grade = :annees_service_grade AND diplome = :diplome AND diplomeSup1 = :diplomeSup1 AND diplomeSup2 = :diplomeSup2
        ");
        $stmt->bindParam(':idGrade', $attributs['idGrade']);
        $stmt->bindParam(':annees_service_FA', $attributs['annees_service_FA']);
        $stmt->bindParam(':annees_service_GN', $attributs['annees_service_GN']);
        $stmt->bindParam(':annees_service_SOE', $attributs['annees_service_SOE']);
        $stmt->bindParam(':annees_service_grade', $attributs['annees_service_grade']);
        $stmt->bindParam(':diplome', $attributs['diplome']);
        $stmt->bindParam(':diplomeSup1', $attributs['diplomeSup1']);
        $stmt->bindParam(':diplomeSup2', $attributs['diplomeSup2']);
        
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false){
            //requête d'insertion en bdd
            $stmt = $pdo->prepare("
                INSERT INTO ConditionsPromotions 
                    (idGrade, annees_service_FA, annees_service_GN, annees_service_SOE, annees_service_grade, diplome, diplomeSup1, diplomeSup2) 
                VALUES
                    (:idGrade, :annees_service_FA, :annees_service_GN, :annees_service_SOE, :annees_service_grade, :diplome, :diplomeSup1, :diplomeSup2)
            ");
            $stmt->bindParam(':idGrade', $attributs['idGrade']);
            $stmt->bindParam(':annees_service_FA', $attributs['annees_service_FA']);
            $stmt->bindParam(':annees_service_GN', $attributs['annees_service_GN']);
            $stmt->bindParam(':annees_service_SOE', $attributs['annees_service_SOE']);
            $stmt->bindParam(':annees_service_grade', $attributs['annees_service_grade']);
            $stmt->bindParam(':diplome', $attributs['diplome']);
            $stmt->bindParam(':diplomeSup1', $attributs['diplomeSup1']);
            $stmt->bindParam(':diplomeSup2', $attributs['diplomeSup2']);
            
            $stmt->execute();
        } else{
            $doublonError = 'Système anti-doublon : cette entrée existe déjà dans la base de donnée, impossible de la mettre à nouveau';
            return $doublonError;
        }
    }

    //Delete eligible condition
    public static function suprEligibleConditionRetraite($id)
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'DELETE FROM ConditionsRetraites WHERE id = :id';
        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
    }

     #Permet de réccupérer le diplome par le biais de la clé primaire
    public static function getRetraiteConditionsByClef($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT * from ConditionsRetraites WHERE id = :id';
        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
        $possede = $stmt->fetch();

        return $possede;
    }

    public static function suprEligibleConditionPromotion($id)
    {
        $pdo = DB::getInstance()->getPDO();
        $req = 'DELETE FROM ConditionsPromotions WHERE id = :id';
        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
    }

     #Permet de réccupérer le diplome par le biais de la clé primaire
    public static function getPromotionConditionsByClef($id)
    {
        $pdo = DB::getInstance()->getPDO();

        //on réccupère le matricule
        $req = 'SELECT * from ConditionsPromotions WHERE id = :id';
        $stmt = $pdo->prepare($req);
        $data = ['id'=>$id];
        $stmt->execute($data);
        $possede = $stmt->fetch();

        return $possede;
    }

    // 3-4- 'canRetireAFolder':
    public static function retraiterUnDossier($attributs)
    {
        $pdo = DB::getInstance()->getPDO();

        //requête d'insertion en bdd
        $stmt = $pdo->prepare("
            INSERT INTO Retraites 
                (matricule, date_retraite) 
            VALUES
                (:matricule, :date_retraite)
        ");
        $stmt->bindParam(':matricule', $attributs['id']);
        $stmt->bindParam(':date_retraite', $attributs['date_retraite']);
        $stmt->execute();

        //on supprime l'entrée dans la table actif'
        $req = 'DELETE FROM Actifs WHERE matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule'=>$attributs['id']];
        $stmt->execute($data);
    }

    // 3-5- 'editEligibleEmailContent':
    #to do

    // 3-6- 'uploadFileForMail':
    #to do

    // 3-7- 'changePieceJointeForEligibleMail':
    #to do
}