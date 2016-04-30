<?php
namespace PLabadille\GestionDossier\VerificateurCondition;
require_once('EligibleMailer.php');

class EligibleRetraiteController
{
    public static function countYearsFromTodayToADate($date)
    { 
        $today=date('Y-m-d');
        /*on "découpe" les dates de façon à obtenir un tableau de 3 lignes : 2=>jours, 1=>mois, 0=>années*/
        $date=explode("-",$date);
        $today=explode("-",$today);
        /*A partir de ce tableau, on reconstitue le timestamp grâce à la fonction mktime*/
        $date=mktime(0,0,0,$date[1],$date[2],$date[0]);
        $today=mktime(0,0,0,$today[1],$today[2],$today[0]);
        /*On soustrait les deux dates et on obtient le nombre de secondes écoulé*/
        $d=$today-$date;
        
        $year = date('Y',$d)-1970;
        #Si le nombre d'année est inférieur à 1, on retourne 0 (sinon erreur et donc -1).
        if ( $year < 1 )
            $year = 0;

        return $year;
    }

    public static function checkMilitairesEligiblesRetraite()
    {
        #compare la règle du grade correspondant 'age et service' à un dossier
        #retourne un tableau des dossiers étant éligible
        #ici soit l'un soit l'autre suffis pour être éligible

        $militairesNonEligibles = EligibleRetraiteManager::getMilitairesNonEligiblesRetraite();
        $retirementRules = EligibleRetraiteManager::getRetirementRules();

        $militairesEligibles = array();

        foreach ($militairesNonEligibles as $key => $dossier) {
            $miltMatricule = $key;
            foreach ($dossier as $key => $value) {
                switch ($key) {
                    case 'idGrade':
                        $rules = $retirementRules[$value];
                        break;
                    case 'date_naissance':
                        if ( $value >= $rules['age'] ){
                            $militairesEligibles[$miltMatricule] = $dossier;
                        }
                        break;
                    case 'date_recrutement':
                        if ( $value >= $rules['service_effectif'] ){
                            $militairesEligibles[$miltMatricule] = $dossier;
                        }
                        break;
                }
            }        
        }
        if ( !empty($militairesEligibles) )
            return $militairesEligibles;
    }

    public static function sendMailToEligiblesRetraite()
    {   
        #les constantes EXPEDITEUR, ADRESSE de RETOUR et ADRESSE d'exp sont définie en début de script dans la classe

        #calcul du temps d'execution:
        $timestamp_debut = microtime(true);

        #on réccupère les informations sur les militaires éligibles (info concernant uniquement l'éligibilité)
        $militairesEligibles = self::checkMilitairesEligiblesRetraite();

        $timestamp_finEligible = microtime(true);
        $i = 0;
        #si $militairesEligibles n'est pas set, alors personne n'est éligible.
        if (isset($militairesEligibles)){
            foreach ($militairesEligibles as $matricule => $info) {
                $i++;
                #on réccupère les dossiers militaires dans $dossier.
                #$info lui contient l'age, les années de service et le grade
                $dossier = EligibleRetraiteManager::getFolderByMatricule($matricule);
                #on ajoute la dénomination du grade aux infos.
                $info['grade'] = EligibleRetraiteManager::getGradeDenominationById($info['idGrade']);
                
                #prepa des variables nécessaires :
                $sendTo['email'] = $dossier['email'];
                $sendTo['prenom'] = $dossier['prenom'];
                $sendTo['nom'] = $dossier['nom'];
                $n = "\r\n";

                ##Déclaration du message en HTML et PlainText
                $message_txt = <<<EOT
                    Bonjour,{$n}
                    Ce message est destiné à {$dossier['prenom']} {$dossier['nom']} actuellement au grade de {$info['grade']} au sein des Forces Armées de la République du Congo.{$n}
                    Vous avez désormais {$info['date_naissance']} ans et servez depuis {$info['date_recrutement']} ans chose pour laquelle vous avez tout nos remerciement.
                    Nous vous informons donc de votre éligibilité à la retraite, nous vous demandons par conséquent de bien vouloir entamer les démarches administratives requises pour faire valoir ce que de droit.{$n}
                    {$n}
                    Cordialement,{$n}
                    L'équipe administrative des Forces Armée de la République du Congo.
                    {$n}
                    Ce message est envoyé automatiquement, si vous n'êtes pas le destinataire, veuillez ne pas en tenir compte.
EOT;
                $message_html = <<<EOT
                <html>
                    <head>
                    </head>
                    <body>
                        <p>Bonjour,</p>
                        <p>Ce message est destiné à {$dossier['prenom']} {$dossier['nom']} actuellement au grade de {$info['grade']} au sein des Forces Armées de la République du Congo.</p>
                        <p>Vous avez désormais {$info['date_naissance']} ans et servez depuis {$info['date_recrutement']} ans chose pour laquelle vous avez tout nos remerciement.</p>
                        <p>Nous vous informons donc de votre éligibilité à la retraite, nous vous demandons par conséquent de bien vouloir entamer les démarches administratives requises pour faire valoir ce que de droit.</p>
                        </br>
                        <p>Cordialement,</p>
                        <b>L'équipe administrative des Forces Armée de la République du Congo.</b>
                        </br>
                        <i>Ce message est envoyé automatiquement, si vous n'êtes pas le destinataire, veuillez ne pas en tenir compte.</i>
                    </body>
                </html>
EOT;
                //on envoit le mail à l'aide de la fonction adequate situé dans le fichier inclu ci-dessus (pas de classe car conflit d'autoloader...)
                $send = sendMailWhenEligibleRetraite($message_html, $message_txt, $sendTo);

                #On set à true éligiblité retraite en bdd
                EligibleRetraiteManager::setEligibleRetraiteByMatricule($matricule);
            } 
        }
        #on retourne le temps d'execution total du script pour le handler
        $timestamp_fin = microtime(true);
        $difference_ms['eligible'] = $timestamp_finEligible - $timestamp_debut;
        $difference_ms['mailAndAlter'] = $timestamp_fin - $timestamp_debut;
        $difference_ms['nb'] = $i;

        return $difference_ms; 
    }
}