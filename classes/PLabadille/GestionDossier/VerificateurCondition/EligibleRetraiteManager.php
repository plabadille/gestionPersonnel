<?php
namespace PLabadille\GestionDossier\VerificateurCondition;
use PLabadille\Common\Bd\DB;

class EligibleRetraiteManager
{
    public static function getRetirementRules()
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select * from ConditionsRetraites';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        #La clé correspond à l'id du grade
        #On y stocke directement la règle age et service
        $retirementRules = array();
        foreach ($result as $key => $value) {
           foreach ($value as $key => $value) {
                if ($key == 'id'){
                    null;
                }
                elseif ($key == 'idGrade'){
                    $idGrade = $value;
                } else {
                    $retirementRules[$idGrade][$key] = $value;
                }
           }
        }
        return $retirementRules;
    }

    public static function getMilitairesNonEligiblesRetraite()
    {
        $pdo = DB::getInstance()->getPDO();

        #On selectionne tous les militaires qui ne sont pas éligible retraite dans la table actif
        #On s'interesse à la date de naissance, le matricule, la date de recrutement et au grade.
        $req = '
            SELECT m.matricule, dg.id, m.date_naissance, m.date_recrutement
            FROM Militaires m
            JOIN DetientGrades dg ON dg.matricule = m.matricule
            WHERE m.matricule IN(
                SELECT matricule
                FROM Actifs
                WHERE eligible_retraite = 0
            )
        ';
        $stmt = $pdo->prepare($req);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $militairesEligibles = array();
        #La clef correspond au matricule du militaire
        #on transforme les dates en année par rapport à la date d'aujourd'hui
        foreach ($result as $key => $value) {
           foreach ($value as $key => $value) {
                if ($key == 'matricule'){
                    $matricule = $value;
                } elseif ($key == 'id'){
                    $militairesEligibles[$matricule]['idGrade'] = $value;
                } else {
                    $date = EligibleRetraiteController::countYearsFromTodayToADate($value);
                    $militairesEligibles[$matricule][$key] = $date;
                }
           }
        }
        return $militairesEligibles;
    }

    public static function getFolderByMatricule($matricule)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select * from Militaires where matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);

        $result = $stmt->fetch();
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

    public static function setEligibleRetraiteByMatricule($matricule)
    {
        $pdo = DB::getInstance()->getPDO();

        $stmt = $pdo->prepare("update Actifs set eligible_retraite = '1' where matricule = :matricule");

        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();

        $stmt->closeCursor();
    }
}