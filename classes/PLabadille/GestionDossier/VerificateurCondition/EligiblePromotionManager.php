<?php
namespace PLabadille\GestionDossier\VerificateurCondition;
use PLabadille\Common\Bd\DB;

class EligiblePromotionManager
{
    public static function getPromotionRules()
    {
        $pdo = DB::getInstance()->getPDO();

        #On réccupère les règles de promotions
        $req = 'select * from ConditionsPromotions';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $conditions = $stmt->fetchAll();

        #On réccupère la liste des équivalences (diplômes)
        $req = 'select * from DiplomesEquivalences';
        $stmt = $pdo->prepare($req);
        $stmt->execute();
        $diplomesEqui = $stmt->fetchAll();

        $stmt->closeCursor();

        #on parcourt $conditions
        foreach ($conditions as $key => $liste) {
           foreach ($liste as $key2 => $value) {
                #nettoyage de $conditions
                unset($conditions[$key]['id']);
                if ( empty($value) ){
                    unset($conditions[$key][$key2]);
                }
                #assemblage des données
                if ( $key2 == 'diplome' || $key2 == 'diplomeSup1' || $key2 == 'diplomeSup2' ){
                    #on parcourt $diplomeEqui
                    foreach ($diplomesEqui as $key3 => $liste2) {
                        #Si le diplome demandé existe dans la table équivalence on les ajoute
                        if ( $value == $liste2['diplome'] ){
                            #nettoyage de la clé diplome correspondant (puisqu'elle est contenue dans la table équivalence)
                            unset($conditions[$key][$key2]);
                            foreach ($liste2 as $id => $value2) {
                                #nettoyage de $diplomeEqui
                                unset($liste2['id']);
                                if ( empty($value2) ){
                                    #si la col était null on supprime
                                    unset($liste2[$id]);
                                }
                                #ajout du diplome si la clef n'était pas null
                                if ( isset($liste2[$id]) ){
                                    $conditions[$key]['diplomes'][] = $liste2[$id];
                                }
                            }
                        }
                    }
                    #Si une cette clé existe encore, c'est qu'elle n'a pas d'équivalence. On l'ajoute donc à la liste de diplome.
                    if ( isset($conditions[$key][$key2]) ){
                        $conditions[$key]['diplomes'][] = $conditions[$key][$key2];
                        unset($conditions[$key][$key2]);
                    }   
                }
           }
        }
        return $conditions;
    }

    public static function getMatriculesNonEligiblesPromotion()
    {
        $pdo = DB::getInstance()->getPDO();

        #On selectionne tous les militaires qui ne sont pas éligible retraite dans la table actif
        #Et qui n'ont pas le grade maximum.
        $req = '
            SELECT matricule
            FROM Militaires
            WHERE matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_promotion = 0
            )
            AND matricule IN(
                SELECT matricule
                FROM DetientGrades
                WHERE id != "1"
            )
        ';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $matriculeMilitairesEligibles = array();
        #on transforme les dates en année par rapport à la date d'aujourd'hui
        foreach ($result as $key => $value) {
            foreach ($value as $key => $value) {
            $matriculeMilitairesEligibles[] = $value;
            }
        }
        return $matriculeMilitairesEligibles;
    }

    public static function getInfos($matricule)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select date_naissance, date_recrutement, genre, nom, prenom, email from Militaires where matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        #ici on aura toujours qu'une clé, on ne la renvoit donc pas.
        return $result['0'];
    }

    public static function getDiplomes($matricule)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select id from PossedeDiplomes where matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        #ici on ne s'interesse qu'au nom du diplôme, on ne renvoit donc pas les clés:
        $diplomes = array();
        foreach ($result as $key => $value) {
            $diplomes[] = $value['id'];
        }
        return $diplomes;
    }

    public static function getGrades($matricule)
    {
        $pdo = DB::getInstance()->getPDO();
        # attention, on précise date DESC pour avoir en clé 0 le grade le plus récent (et donc actuel)
        $req = 'select id, date_promotion from DetientGrades where matricule = :matricule order by date_promotion DESC';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    public static function getGradeSup($grade)
    {
        $pdo = DB::getInstance()->getPDO();
        # attention, on précise date DESC pour avoir en clé 0 le grade le plus récent (et donc actuel)
        $req = 'select hierarchie from Grades where id = :grade';
        $stmt = $pdo->prepare($req);
        $data = ['grade' => $grade];
        $stmt->execute($data);
        $result = $stmt->fetchAll();

        #On retire 1 au résultat afin de trouver le grade supérieur
        $result['0']['hierarchie'] = $result['0']['hierarchie'] -1;
        #On refait une requête afin de réccupérer les id des grades (possible plusieurs selon parcours pro)
        $req = 'select id from Grades where hierarchie = :hierarchie';
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

    public static function getRegiments($matricule)
    {
        $pdo = DB::getInstance()->getPDO();
        # attention, on précise date DESC pour avoir en clé 0 le grade le plus récent (et donc actuel)
        $req = 'select id, date_appartenance from AppartientRegiment where matricule = :matricule order by date_appartenance DESC';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    public static function getGradeDenominationById($id)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select grade from Grades where id = :id';
        $stmt = $pdo->prepare($req);
        $data = ['id' => $id];
        $stmt->execute($data);

        $result = $stmt->fetch();
        $stmt->closeCursor();

        foreach ($result as $key => $value) {
           return $value;
        } 
    }

    public static function setEligiblePromotionByMatricule($matricule)
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("update Actifs set eligible_promotion = '1' where matricule = :matricule");

        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();

        $stmt->closeCursor();
    }
}