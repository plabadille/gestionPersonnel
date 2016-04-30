<?php
namespace PLabadille;

//Autoloader:
require_once 'config/config.php';

use PLabadille\GestionDossier\VerificateurCondition\EligibleRetraiteController;
use PLabadille\GestionDossier\VerificateurCondition\EligiblePromotionController;

#L'algorithme de retraite en premier (moins complexe donc plus rapide):
$delayRetraite = EligibleRetraiteController::sendMailToEligiblesRetraite();
#l'algorithme de promotion
$delayPromotion = EligiblePromotionController::sendMailToEligiblesPromotion();
#total d'execution
$delayTotal = $delayPromotion['mailAndAlter'] + $delayRetraite['mailAndAlter'];
#date après le lancement du script (avec  heure, minute et seconde)
$today=date('Y-m-d-H-i-s');

##On stock les informations d'executions dans un fichier pour faire un récap.
#contenu à ajouter au fichier
$content = "\n" . $today . ' ' . $delayPromotion['eligible'] . ' ' . $delayPromotion['nb'] . ' '  . $delayPromotion['mailAndAlter'] . ' ' . $delayRetraite['eligible'] . ' ' . $delayRetraite['nb'] . ' ' . $delayRetraite['mailAndAlter'] . ' ' . $delayTotal;
$monfichier = fopen('media/infos/statsEligibleCron.txt', 'r+');
#on se positionne à la fin du fichier
fseek($monfichier, 0, SEEK_END);
fputs($monfichier, $content);
fclose($monfichier);
##---

#test par navigateur :
// echo 'Temps d\'execution de l\'algo retraite : ' . $delayRetraite . ' secondes' . "\n";
// echo 'Temps d\'execution de l\'algo promotion : ' . $delayPromotion . ' secondes' . "\n";
// echo 'Temps d\'execution total : ' . $delay . ' secondes';