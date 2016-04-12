<?php
namespace PLabadille\GestionDossier\VerificateurCondition;

class EligibleRetraiteController
{
    const EMAIL_EXPEDITEUR = "21101555@etu.unicaen.fr";
    const EMAIL_RETOUR = "21101555@etu.unicaen.fr";
    const EXPEDITEUR = "21101555";
    const EMAIL_SUJET = "Email de notification : vous êtes éligible à la retraite";

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
        #si $militairesEligibles n'est pas set, alors personne n'est éligible.
        if (isset($militairesEligibles)){
            foreach ($militairesEligibles as $matricule => $info) {
                #on réccupère les dossiers militaires dans $dossier.
                #$info lui contient l'age, les années de service et le grade
                $dossier = EligibleRetraiteManager::getFolderByMatricule($matricule);
                #on ajoute la dénomination du grade aux infos.
                $info['grade'] = EligibleRetraiteManager::getGradeDenominationById($info['idGrade']);
                
                #on passe maintenant à la fonction de MAIL
                ##adresse d'envois :
                $mail = $dossier['email'];

                ##pour les serveurs qui ne respectent pas la norme :
                if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) {
                    $n = "\r\n";
                } else{
                    $n = "\n";
                }

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
                ##---fin message

                ##Création de la boundary
                $boundary = "-----=".md5(rand());
                ##---fin

                ##Sujet du mail
                $sujet = self::EMAIL_SUJET;
                ##---fin

                ##header
                $header = "From: \"" . self::EXPEDITEUR . "\"<" . self::EMAIL_EXPEDITEUR . ">" . $n;
                $header.= "Reply-to: \"" . self::EXPEDITEUR . "\"<" . self::EMAIL_RETOUR . ">" . $n;
                $header.= "MIME-Version: 1.0" . $n;
                $header.= "Content-Type: multipart/alternative;" . $n . " boundary=\"$boundary\"" . $n;
                ##---fin header

                ##création du message
                $message = $n . "--" . $boundary . $n;
                ###Ajout du message au format texte.
                $message.= "Content-Type: text/plain; charset=\"UTF-8\"" .$n;
                $message.= "Content-Transfer-Encoding: 8bit" . $n;
                $message.= $n . $message_txt . $n;
                ###---
                $message.= $n . "--" . $boundary . $n;
                ###Ajout du message au format HTML
                $message.= "Content-Type: text/html; charset=\"UTF-8\"" . $n;
                $message.= "Content-Transfer-Encoding: 8bit" . $n;
                $message.= $n . $message_html . $n;
                ###---
                $message.= $n . "--" . $boundary . "--" . $n;
                $message.= $n . "--" . $boundary . "--" . $n;
                ##--fin
                 
                #Envoi de l'e-mail.
                mail($mail,$sujet,$message,$header);
                #On set à true éligiblité retraite en bdd
                EligibleRetraiteManager::setEligibleRetraiteByMatricule($matricule);
            } 
        }
        #on retourne le temps d'execution total du script pour le handler
        $timestamp_fin = microtime(true);
        $difference_ms = $timestamp_fin - $timestamp_debut;

        return $difference_ms; 
    }
}