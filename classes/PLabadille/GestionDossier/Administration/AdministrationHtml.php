<?php
namespace PLabadille\GestionDossier\Administration;

use PLabadille\GestionDossier\Controller\AccessControll;
use PLabadille\Common\Authentication\AuthenticationManager;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 4- Module création de compte et de droit
# 5- Module de gestion de l'application
# 6- Module de sauvegarde et de gestion de crise
//--------------------

#Gère l'affichage des Dossiers.
#Attention, la gestion d'affichage des formulaires est gérées directement par DossierForm par le biais de templates.
class AdministrationHtml 
{
    //--------------------
    //x- Fonctions utilitaires et génériques
    //--------------------
    public static function afficheListe($dossier) 
    {
        $html = $dossier['nom'] . ' ' . $dossier['prenom'];
        return $html;
    }

    //--------------------
    //4-module gestion et ajout de dossier
    //--------------------

    // 4-1- 'listCreatedFolderWithoutAccount':
    #Permet l'affichage de tout les dossiers (ou d'une partie)
    #appel afficheListe pour les afficher.
    public static function listAllWithOutAccount($dossier) 
    {
        $html = <<<EOT
            <h2>Liste des militaires n'ayant pas de compte</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=searchNameWithOutFolder">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchDossierWithOutAccount" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        foreach ($dossier as $dossier) {
            //affichage des boutonsNavigation en fonction des droits:
            $boutons = '';
            $typeBouton = 'createAccount';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutonsTest = ($right) ? '&nbsp;-&nbsp; <a href="?objet=administration&amp;action=creerCompte&amp;id=' . $dossier['matricule'] . '">Créer&nbsp;compte</a>' : null;
  
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier['matricule']}">{$liste} - </a>{$boutonsTest}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    // 4-2- 'seeAllAccount':
    public static function listAllAccount($dossier, $info = null) 
    {
        $auth = AuthenticationManager::getInstance();
        $userRole = $auth->getRole();

        if (isset($info)){
            $info = '<div id="printImportantInformation"><p>Le nouveau mot de passe de ' . $info['prenom'] . ' ' . $info['nom'] . ' est : ' . $info['psw'] . '. Veuillez suivre la procédure de transmission habituelle.</p></div>';
        } else { $info = ''; }

        $html = <<<EOT
            <h2>Liste des militaires des comptes utilisateurs</h2>
                {$info}
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=searchCompte">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchDossierWithAccount" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        //affichage des boutonsNavigation en fonction des droits:
        $typeBouton = 'alterMdp';
        $rightAlter = AccessControll::afficherBoutonNavigation($typeBouton);
        $typeBouton = 'alterAccountRight';
        $rightAlterRight = AccessControll::afficherBoutonNavigation($typeBouton);
        foreach ($dossier as $dossier) {
            $liste = self::afficheListe($dossier);
            $urlAlter = '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir réinitialiser le mot de passe du compte : ' . $dossier['matricule'] . ' (' . $liste . ') ?\')) document.location.href=\'?objet=administration&amp;action=changePassword&amp;id=' . $dossier['matricule'] . '\'">Réinitialiser Mot de Passe</a>';
            $urlAlterRight = '&nbsp;-&nbsp; <a href="?objet=administration&amp;action=changeDroitsCompte&amp;id=' . $dossier['matricule'] . '">Changer les droits</a>';
            if ( $userRole == 'superAdmin' ){ //le superAdmin peut changer n'importe quel mdp.
                $boutonAlter = ($rightAlter) ? $urlAlter : null;
                $boutonAlterRight = ($rightAlterRight) ? $urlAlterRight : null;
            } elseif ( $userRole == 'admin' ) { //les admin ne peuvent ni changer le mdp des autres admin, ni celui du super admin
                if ($dossier['role'] !== 'superAdmin' && $dossier['role'] != 'admin'){
                    $boutonAlter = ($rightAlter) ? $urlAlter : null;
                    $boutonAlterRight = ($rightAlterRight) ? $urlAlterRight : null;
                } else {
                    $boutonAlter = null;
                    $boutonAlterRight = null;
                }
            } else{ //les autres (dont on a eventuellement donné le droit ne peuvent moddifier que celui des militaires de base)
                if ($dossier['role'] == 'militaire'){
                    $boutonAlter = ($rightAlter) ? $urlAlter : null;
                    $boutonAlterRight = ($rightAlterRight) ? $urlAlterRight : null;
                } else {
                    $boutonAlter = null;
                    $boutonAlterRight = null;
                }
            }

            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier['matricule']}">{$liste} -</a> identifiant: {$dossier['matricule']}, rôle: {$dossier['role']} {$boutonAlter} {$boutonAlterRight}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    public static function listAllAccountToDelete($dossier)
    {

        //affichage des dossiers retraités
        $html = <<<EOT
            <h2>Liste des comptes à supprimer</h2>
            <h3>Dossiers retraités</h3>    
            <ul id="listeDossier">
EOT;
        foreach ($dossier['retraite'] as $dossierR) {
            //affichage des boutonsNavigation en fonction des droits:
            $typeBouton = 'deleteAccount';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutonSupr= ($right) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer le compte : ' . $dossierR['matricule'] . ' ?\')) document.location.href=\'?objet=administration&amp;action=suprCompte&amp;id=' . $dossierR['matricule'] . '\'">Supprimer compte</a>' : null;


  
            $liste = self::afficheListe($dossierR);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossierR['matricule']}">{$liste} - </a> - {$dossierR['date_retraite']} {$boutonSupr}
                </li>
EOT;
        }
        if (empty($dossier['retraite'])){
            $html .= 'aucun compte à supprimer';
        }

        $html .= "  </ul>\n";
        //affichage des dossiers archivés
        $html .= <<<EOT
            <h3>Dossiers archivés</h3>    
            <ul id="listeDossier">
EOT;
        foreach ($dossier['archive'] as $dossierA) {
            //affichage des boutonsNavigation en fonction des droits:
            $typeBouton = 'deleteAccount';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutonSupr= ($right) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer le compte : ' . $dossierA['matricule'] . ' ?\')) document.location.href=\'?objet=administration&amp;action=suprCompte&amp;id=' . $dossierA['matricule'] . '\'">Supprimer compte</a>' : null;
  
            $liste = self::afficheListe($dossierA);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossierA['matricule']}">{$liste} - </a> - {$dossierA['date_deces']} {$boutonSupr}
                </li>
EOT;
        }
        if (empty($dossier['archive'])){
            $html .= 'aucun compte à supprimer';
        }

        $html .= "  </ul>\n";
        return $html;

    }
    

}