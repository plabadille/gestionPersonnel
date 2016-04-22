<?php
namespace PLabadille\GestionDossier\Dossier;

use PLabadille\GestionDossier\Controller\AccessControll;

//--------------------
//ORGANISATION DU CODE
//--------------------
# 1- Module mon dossier
# 2- Module de gestion et ajout de dossier
# 3- Module de gestion de promotion et retraite
//--------------------

#Gère l'affichage des Dossiers.
#Attention, la gestion d'affichage des formulaires est gérées directement par DossierForm par le biais de templates.
class DossierHtml {
    //--------------------
    //1-module mon dossier
    //--------------------
    // 1-1- 'seeOwnFolderModule':
    public static function viewUserFolder($dossier){
        //4-affichage dossier
        $html = <<<EOT
            <h3>Dossier de {$dossier['informations']->getNom()} {$dossier['informations']->getPrenom()}, matricule : {$dossier['informations']->getMatricule()}</h3>
                <p>Date de naissance : {$dossier['informations']->getDateNaissance()}</p>
                <p>Genre : {$dossier['informations']->getGenre()}</p>
                <p>Tel1 : {$dossier['informations']->getTel1()}</p>
                <p>Tel 2 : {$dossier['informations']->getTel2()}</p>
                <p>Email : {$dossier['informations']->getEmail()}</p>
                <p>Adresse : {$dossier['informations']->getAdresse()}</p>
                <p>Date de recrutement : {$dossier['informations']->getDateRecrutement()}</p>\n\n
EOT;
        $html .= self::afficheAffectations($dossier['casernes']) . "\n\n";
        $html .= self::afficheAppartenances($dossier['regiments']) . "\n\n";
        $html .= self::afficheGradesDetenu($dossier['grades']) . "\n\n";
        $html .= self::afficheDiplomesPossede($dossier['diplomes']); 
        return $html;
    }

    // 1-2- 'editOwnFolderPersonalInformation':
    #géré par template dans DossierForm

    //--------------------
    //2-module gestion et ajout de dossier
    //--------------------
    // 2-1- 'listCreatedFolder':
    public static function listCreatedFolderHtml($dossier) {
        $html = <<<EOT
            <h2>Liste des militaires que vous avez ajouté</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercherCreatedFolder">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        foreach ($dossier as $dossier) {
            //affichage des boutonsNavigation en fonction des droits:
            $typeBouton = 'editFolderInformation';
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            $right = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);

            $editBouton = ($right) ? '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=editerDossier&amp;id=' . $dossier->getMatricule() . '">Editer</a>' : null;
            
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a> {$editBouton}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    // 2-2- 'listAllFolder':

    #Permet l'affichage de tout les dossiers (ou d'une partie)
    #appel afficheListe pour les afficher.
    public static function toHtml($dossier) {
        $html = <<<EOT
            <h2>Liste des militaires</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercher">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        foreach ($dossier as $dossier) {
            //affichage des boutonsNavigation en fonction des droits:
            $typeBouton = 'editFolderInformation';
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            $right = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);

            $editBouton = ($right) ? '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=editerDossier&amp;id=' . $dossier->getMatricule() . '">Editer</a>' : null;
            
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a> {$editBouton}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    #appelé par toHtml pour afficher tous les dossier
    public static function afficheListe($dossier) {
        $html = $dossier->getNom() . ' ' . $dossier->getPrenom();
        return $html;
    }

    // 2-3- 'seeCreatedFolder':
    #to do
    
    // 2-4- 'seeAllFolder':

    #Permet d'afficher uniquement un dossier
    public static function afficheUnDossier($dossier) {
        //affichage des boutonsNavigation en fonction des droits:
        $createBy = $dossier->getWhoCreateFolder();
        $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);

        //1-edit bouton
        $typeBouton = 'editFolderInformation';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
        $editBouton = ($rightEdit) ? '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=editerDossier&amp;id=' . $dossier->getMatricule() . '">Editer</a>' : null;
        
        //2-addElement Boutons
        $typeBouton = 'addElementToAFolder';
        $rightAddElement = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
        $addElementBoutons = null;

        //3-Affichage boutons
        if ( $rightAddElement ){
            $addElementBoutons = '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=ajouterAffectation&amp;id=' . $dossier->getMatricule() . '">Ajouter une affectation</a>';
            $addElementBoutons .= '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=ajouterAppartenanceRegiment&amp;id=' . $dossier->getMatricule() . '">Ajouter un régiment d\'appartenance</a>';
            $addElementBoutons .= '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=ajouterGradeDetenu&amp;id=' . $dossier->getMatricule() . '">Ajouter un grade</a>';
            $addElementBoutons .= '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=ajouterDiplomePossede&amp;id=' . $dossier->getMatricule() . '">Ajouter un diplôme</a>';
        }

        //4-affichage dossier
        $html = <<<EOT
            {$editBouton}
            {$addElementBoutons}

            <h3>Dossier de {$dossier->getNom()} {$dossier->getPrenom()}, matricule : {$dossier->getMatricule()}</h3>
                <p>Date de naissance : {$dossier->getDateNaissance()}</p>
                <p>Genre : {$dossier->getGenre()}</p>
                <p>Tel1 : {$dossier->getTel1()}</p>
                <p>Tel 2 : {$dossier->getTel2()}</p>
                <p>Email : {$dossier->getEmail()}</p>
                <p>Adresse : {$dossier->getAdresse()}</p>
                <p>Date de recrutement : {$dossier->getDateRecrutement()}</p>
EOT;
        return $html;
    }

    #Permet d'afficher les affectations liées à un dossier
    public static function afficheAffectations($affectations) {
        $html = "</br>\n<h3>Liste des affectations :</h3> \n";

        if (!empty($affectations)){
            $html .= "<h4>Affectation actuelle :</h4> \n";
            $html .= "<p>Caserne " . $affectations['0']['nom'] . " depuis le " . $affectations['0']['date_affectation'] . "</p> \n";

            $html .= "<h4>Ancienne(s) affectation(s) :</h4> \n";
            if (isset($affectations['1'])){
                foreach ($affectations as $key => $liste) {
                    if ($key > 0){
                        $html .= "<p>Caserne " . $liste['nom'] . " le " . $liste['date_affectation'] . "</p> \n";
                    }
                }
            } else{
                $html .= "<p>Aucune autre affectation</p> \n";
            }
        } else{
            $html .= "<p>Aucune affectation</p> \n";
        }

        return $html;
    }

    #Permet d'afficher les régiments d'appartenances liées à un dossier
    public static function afficheAppartenances($appartenances) {
        $html = "</br>\n<h3>Liste des régiments d'appartenances :</h3> \n";

        if (!empty($appartenances)){
            $html .= "<h4>Appartenance actuelle :</h4> \n";
            $html .= "<p>Régiment: " . $appartenances['0']['id'] . " depuis le " . $appartenances['0']['date_appartenance'] . "</p> \n";

            $html .= "<h4>Ancienne(s) appartenance(s) :</h4> \n";
            if (isset($appartenances['1'])){
                foreach ($appartenances as $key => $liste) {
                    if ($key > 0){
                        $html .= "<p>Régiment: " . $liste['id'] . " le " . $liste['date_appartenance'] . "</p> \n";
                    }
                }
            } else{
                $html .= "<p>Aucun autre régiment</p> \n";
            }
        } else{
            $html .= "<p>Aucun régiment d'appartenance</p> \n";
        }

        return $html;
    }

    #Permet d'afficher les grades detenu liés à un dossier
    public static function afficheGradesDetenu($grades) {
        $html = "</br>\n<h3>Liste des grades du militaire :</h3> \n";

        if (!empty($grades)){
            $html .= "<h4>Grade actuel :</h4> \n";
            $html .= "<p>Grade: " . $grades['0']['grade'] . " depuis le " . $grades['0']['date_promotion'] . "</p> \n";

            $html .= "<h4>Ancien(s) grade(s) :</h4> \n";
            if (isset($grades['1'])){
                foreach ($grades as $key => $liste) {
                    if ($key > 0){
                        $html .= "<p>Grade: " . $liste['grade'] . " le " . $liste['date_promotion'] . "</p> \n";
                    }
                }
            } else{
                $html .= "<p>Aucun autre grade</p> \n";
            }
        } else{
            $html .= "<p>Aucun grade</p> \n";
        }

        return $html;
    }

    #Permet d'afficher les diplomes possede liés à un dossier
    public static function afficheDiplomesPossede($diplomes) {
        $html = "</br>\n<h3>Liste des diplômes possédés :</h3> \n";

        if (!empty($diplomes)){
            foreach ($diplomes as $key => $liste) {
                $html .= "<p>" . $liste['intitule'] . " (" . $liste['acronyme'] . ") obtenu le " . $liste['date_obtention'] . "</p> \n";
            }
        } else{
            $html .= "<p>Aucun diplôme</p> \n";
        }

        return $html;
    }

    // 2-5 'createFolder':
    #géré par template dans DossierForm

    // 2-6- 'addElementToAFolder':
    #géré par template dans DossierForm

    // 2-7- 'editInformationIfAuthor':
    #géré par template dans DossierForm

    // 2-8- 'editInformation':
    #géré par template dans DossierForm

    // 2-9- 'deleteInformation':
    #to do?

    // 2-10 'useFileToAddFolders':
    #géré par template dans DossierForm

    //--------------------
    //3-module gestion promotion et retraite
    //--------------------
    // 3-1- 'listEligible':

    public static function afficheDossiersEligiblesPromotion($dossier) {
        $html = <<<EOT
            <h2>Liste des militaires éligibles à une promotion</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercherEligiblesPromotion">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;

        foreach ($dossier as $dossier) {
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a>   
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    public static function afficheDossiersEligiblesRetraite($dossier) {
        $html = <<<EOT
            <h2>Liste des militaires éligibles à la retraite</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercherEligiblesRetraite">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;

        foreach ($dossier as $dossier) {
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a>   
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    } 

    // 3-2- 'editEligibleCondition':
    #géré par template dans DossierForm

    // 3-3- 'addEligibleCondition':
    #géré par template dans DossierForm

    // 3-4- 'canRetireAFolder':
    #to do?

    // 3-5- 'editEligibleEmailContent':
    #géré par template dans DossierForm

    // 3-6- 'uploadFileForMail':
    #géré par template dans DossierForm

    // 3-7- 'changePieceJointeForEligibleMail':
    #géré par template dans DossierForm
}