<?php
namespace PLabadille\GestionDossier\VerificateurCondition;
require_once('EligibleMailer.php');

class EligiblePromotionController
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

    public static function countYearsBetweenTwoDate($dateDebut, $dateFin)
    {
        /*on "découpe" les dates de façon à obtenir un tableau de 3 lignes : 2=>jours, 1=>mois, 0=>années*/
        $dateDebut=explode("-",$dateDebut);
        $dateFin=explode("-",$dateFin);
        /*A partir de ce tableau, on reconstitue le timestamp grâce à la fonction mktime*/
        $dateDebut=mktime(0,0,0,$dateDebut[1],$dateDebut[2],$dateDebut[0]);
        $dateFin=mktime(0,0,0,$dateFin[1],$dateFin[2],$dateFin[0]);
        /*On soustrait les deux dates et on obtient le nombre de secondes écoulé*/
        $d=$dateFin-$dateDebut;
        $year = date('Y',$d)-1970;
        #Si le nombre d'année est inférieur à 1, on retourne 0 (sinon erreur et donc -1).
        if ( $year < 1 )
            $year = 0;
        return $year;
    }

    public static function getMilitairesATesterPromotionDossier()
    {
        #Cette fonction réunie toutes les informations dont on a besoin pour faire les vérifications :
        ##0)La liste des matricules à tester (bool false sur le champ eligible_promotion)
        ##1)Les infos basiques : date naissance, date recrutement, genre, age et service total
        ###Contenu dans $dossiers[$matricule]['infos']
        ##2)La liste des diplômes possédés par la personne
        ###Contenu dans $dossiers[$matricule]['diplomes']
        ##3)Le grade actuel et les années de services pour celui-ci
        ###Contenu dans $dossiers[$matricule]['grade']
        ##4)Le ou les regiments et les années de service par régiment
        ###Contenu dans $dossiers[$matricule]['regiments']

        #0 : liste des matricules à tester
        $matriculesATester = EligiblePromotionManager::getMatriculesNonEligiblesPromotion();
        foreach ($matriculesATester as $key => $matricule) {

            #1 : infos basiques
            $dossiers[$matricule]['infos'] = EligiblePromotionManager::getInfos($matricule);
            ##on transforme la date de naissance et date de recrutement en année:
            $dossiers[$matricule]['infos']['age'] = self::countYearsFromTodayToADate($dossiers[$matricule]['infos']['date_naissance']);
            $dossiers[$matricule]['infos']['service_total'] = self::countYearsFromTodayToADate($dossiers[$matricule]['infos']['date_recrutement']);

            #2 : liste des diplomes
            $dossiers[$matricule]['diplomes'] = EligiblePromotionManager::getDiplomes($matricule);

            #3 : grade actuel et années de services
            ##On ne conserve que le plus récent (pour avoir l'actuel):
            $grades = EligiblePromotionManager::getGrades($matricule);
            ##On calcul le nombre d'année de service à ce grade
            $serviceGrade = self::countYearsFromTodayToADate($grades['0']['date_promotion']);
            $dossiers[$matricule]['grade']['id'] = $grades['0']['id'];
            $dossiers[$matricule]['grade']['service_grade'] = $serviceGrade;

            #4 : Les regiments
            $regiments = EligiblePromotionManager::getRegiments($matricule);
            ##On calcul les dates en fonction, la clé 0 contient le régiment le plus récent
            $serviceRegiment = self::countYearsFromTodayToADate($regiments['0']['date_appartenance']);
            $dossiers[$matricule]['regiments']['0']['id'] = $regiments['0']['id'];
            $dossiers[$matricule]['regiments']['0']['serviceRegiment'] = $serviceRegiment;
            ##S'il reste des regiments :
            ##on compare la date d'appartenance de l'autre regiments par rapport à celui plus récent.
            ##On calcul le nombre de régiment en plus de l'actuel il reste:
            $nbRegimentRestant = count($regiments) -1;
            for ($i=1; $i <= $nbRegimentRestant; $i++) {
                ##On calcul l'identifiant du nouveau régiment (pour comparer les dates): 
                $newRegiment = $i -1;
                $serviceRegiment = self::countYearsBetweenTwoDate($regiments[$i]['date_appartenance'], $regiments[$newRegiment]['date_appartenance']);
                $dossiers[$matricule]['regiments'][$i]['id'] = $regiments[$i]['id'];
                $dossiers[$matricule]['regiments'][$i]['serviceRegiment'] = $serviceRegiment;
            }
            ##Si le militaire a rejoint minimum de 3 regiments (2 car le nb de régiment est déduit de 1 au début) alors on regarde si il a rejoint deux fois le même régiment dans sa carrière. Si c'est le cas on les additionne.
            if (  $nbRegimentRestant >= 2 ){
                for ($i=0; $i <= $nbRegimentRestant; $i++) {
                    for ($y=0; $y <= $nbRegimentRestant; $y++) {
                        if ($i != $y){
                            if ( isset($dossiers[$matricule]['regiments'][$y]) && isset($dossiers[$matricule]['regiments'][$i])){
                                if ($dossiers[$matricule]['regiments'][$i]['id'] == $dossiers[$matricule]['regiments'][$y]['id']){
                                    $dossiers[$matricule]['regiments'][$i]['serviceRegiment'] = $dossiers[$matricule]['regiments'][$i]['serviceRegiment'] + $dossiers[$matricule]['regiments'][$y]['serviceRegiment'];
                                    unset($dossiers[$matricule]['regiments'][$y]);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $dossiers;

    }

    public static function checkMilitairesEligiblesPromotion()
    {
        $militairesATester = self::getMilitairesATesterPromotionDossier();
        $promotionRules = EligiblePromotionManager::getPromotionRules();

        $militairesEligibles = array();

        
        #on parcourt les militaires
        foreach ($militairesATester as $matricule => $dossier) {
            #on réccupère le grade supérieur
            $gradeSup = EligiblePromotionManager::getGradeSup($dossier['grade']['id']);
            #on parcourt les grades supérieurs
            foreach ($gradeSup as $key => $value) {
                #on parcourt les régles de promotion
                foreach ($promotionRules as $num => $rules) {
                    #variable qui seront set à true si conditions vérifiées:
                    $diplome = false;
                    $regiment = false;

                    #on trouve la règle correspondante
                    if ( $rules['idGrade'] ==  $value) {
                        #1)A-t-il servi assez longtemps à ce grade ?
                        if ( $dossier['grade']['service_grade'] >= $rules['annees_service_grade'] ){
                            ##afin de gagner en performance on ne regarde pas les deux autres exigences si celle-ci n'est pas vérifiée
                            #2)A-t-il les diplômes nécessaires ?
                            ##On continue si les deux sont vides ou les deux set
                            if ( (!empty($rules['diplomes']) && !empty($dossier['diplomes'])) || (empty($rules['diplomes']) && empty($dossier['diplomes'])) ){
                                ##on parcourt les diplomes du militaire, sinon c'est déjà ok
                                foreach ($dossier['diplomes'] as $key => $possDip) {
                                    ##on parcourt les diplomes requis
                                    foreach ($rules['diplomes'] as $key => $needDip) {
                                        ##on match les deux diplomes :
                                        if ( $possDip == $needDip ){
                                            $diplome = true;
                                            break;
                                        }
                                    }
                                    if ( $diplome == true){
                                        break;
                                    }
                                }
                            }
                            #3)A-t-il les temps de services adequat?
                            #############
                            #############
                            ###A REVOIR##
                            #############
                            #############
                            if ( isset($rules['annees_service_FA']) || isset($rules['annees_service_GN']) || isset($rules['annees_service_SOE']) ){
                                if ( isset($rules['annees_service_FA']) ){
                                    $idRegiments['Forces Armées'] = $rules['annees_service_FA'];
                                }
                                if ( isset($rules['annees_service_GN']) ){
                                    $idRegiments['Gendarmerie Nationale'] = $rules['annees_service_GN'];
                                }
                                if ( isset($rules['annees_service_SOE']) ){
                                    $idRegiments['Ecole Militaire'] = $rules['annees_service_SOE'];
                                }
                                ############
                                #On parcourt le tableau des régiments nécessaires.
                                foreach ($idRegiments as $idRegiment => $service) {
                                    #On parcourt le tableau des régiments du militaire
                                    foreach ($dossier['regiments'] as $key => $tab) {
                                        if ( $tab['id'] == $idRegiment ){
                                            if ( $tab['serviceRegiment'] >= $service ){
                                                #on est okey pour celui la, il faut vérifier le reste
                                                unset($idRegiments[$idRegiment]);
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else{
                                $idRegiments = null;
                            }
                            #Si le tableau de régiment est a été complétement vidé c'est que toutes les conditions sont remplies.
                            if ( empty($idRegiments ) ){
                                $regiment = true;
                            }
                            #4) Si toutes les conditions sont remplies on selectionne le dossier
                            if ( $regiment == true && $diplome == true ){
                                $militaireEligible[$matricule] = $militairesATester[$matricule];
                                $militaireEligible[$matricule]['eligible'] = $value;
                            }
                        }   
                    }
                }  
            }     
        }
        if ( isset($militaireEligible) ){
            return $militaireEligible;
        }   
    }

    public static function sendMailToEligiblesPromotion()
    {   
        #note fonction : http://www.mail-tester.com/web-VWu10k (modd sur l'user 3).
        #calcul du temps d'execution:
        $timestamp_debut = microtime(true);

        #on réccupère les informations sur les militaires éligibles
        $militairesEligibles = self::checkMilitairesEligiblesPromotion();

        $timestamp_finEligible = microtime(true);
        $i = 0;
        #si $militairesEligibles n'est pas set, alors personne n'est éligible.
        if (isset($militairesEligibles)){
            foreach ($militairesEligibles as $matricule => $info) {
                $i++;
                #on ajoute le dossier :
                $info['infos']['matricule'] = $matricule;
                $dossier = $info['infos'];

                #on ajoute la dénomination du grade et du grade supérieur aux infos.
                $dossier['gradeActuel'] = EligiblePromotionManager::getGradeDenominationById($info['grade']['id']);
                $dossier['gradeEligible'] = EligiblePromotionManager::getGradeDenominationById($info['eligible']);

                #on passe maintenant à la fonction de MAIL
                ##adresse d'envois :
                $sendTo['email'] = $dossier['email'];
                $sendTo['prenom'] = $dossier['prenom'];
                $sendTo['nom'] = $dossier['nom'];
                $n = "\r\n";

                ##Déclaration du message en HTML et PlainText
                $message_txt = <<<EOT
                    Bonjour,{$n}
                    Ce message est destiné à {$dossier['prenom']} {$dossier['nom']} actuellement au grade de {$dossier['gradeActuel']} au sein des Forces Armées de la République du Congo.{$n}
                    Nous vous informons que vous remplissez les conditions pour remplir un dossier de promotion pour le grade de {$dossier['gradeEligible']}. Vous trouverez ci-joins les pièces justificatives et documents à nous remettre. Attention, ce mail est automatique et ne vous informe pas d'une promotion mais d'une éligibilité, votre dossier devra être étudié une fois les pièces remises. 
                    Vous pouvez également retrouver cette information directement sur l'espace dédié au gestion de dossier mis à votre disposition. 
                    Nous vous recommandons par conséquent d'entamer les démarches administratives requises pour faire valoir ce que de droit.{$n}
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
                        <p>Ce message est destiné à {$dossier['prenom']} {$dossier['nom']} actuellement au grade de {$dossier['gradeActuel']} au sein des Forces Armées de la République du Congo.</p>
                        <p>Nous vous informons que vous remplissez les conditions pour remplir un dossier de promotion pour le grade de {$dossier['gradeEligible']}. Vous trouverez ci-joins les pièces justificatives et documents à nous remettre. Attention, ce mail est automatique et ne vous informe pas d'une promotion mais d'une éligibilité, votre dossier devra être étudié une fois les pièces remises.</p>
                        <p>Vous pouvez également retrouver cette information directement sur l'espace dédié au gestion de dossier mis à votre disposition. </p>
                        <p>Nous vous recommandons par conséquent d'entamer les démarches administratives requises pour faire valoir ce que de droit.</p>
                        </br>
                        <p>Cordialement,</p>
                        <b>L'équipe administrative des Forces Armée de la République du Congo.</b>
                        </br>
                        <i>Ce message est envoyé automatiquement, si vous n'êtes pas le destinataire, veuillez ne pas en tenir compte.</i>
                    </body>
                </html>
EOT;
                $attachment = './media/promotion_files/modalite_promotion.pdf';
                //on envoit le mail à l'aide de la fonction adequate situé dans le fichier inclu ci-dessus (pas de classe car conflit d'autoloader...)
                $send = sendMailWhenEligiblePromotion($message_html, $message_txt, $attachment, $sendTo);

                // #On set à true éligiblité retraite en bdd
                EligiblePromotionManager::setEligiblePromotionByMatricule($dossier['matricule']);
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
