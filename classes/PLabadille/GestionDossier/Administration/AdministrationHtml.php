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
        $right = AccessControll::afficherBoutonNavigation($typeBouton);
        foreach ($dossier as $dossier) {
            $liste = self::afficheListe($dossier);
            $url = '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir réinitialiser le mot de passe du compte : ' . $dossier['matricule'] . ' (' . $liste . ') ?\')) document.location.href=\'?objet=administration&amp;action=changePassword&amp;id=' . $dossier['matricule'] . '\'">Réinitialiser Mot de Passe</a>';
            if ( $userRole == 'superAdmin' ){ //le superAdmin peut changer n'importe quel mdp.
                $boutonAlter = ($right) ? $url : null;
            } elseif ( $userRole == 'admin' ) { //les admin ne peuvent ni changer le mdp des autres admin, ni celui du super admin
                if ($dossier['role'] !== 'superAdmin' && $dossier['role'] != 'admin'){
                    $boutonAlter = ($right) ? $url : null;
                } else { $boutonAlter = null; }
            } else{ //les autres (dont on a eventuellement donné le droit ne peuvent moddifier que celui des militaires de base)
                if ($dossier['role'] == 'militaire'){
                    $boutonAlter = ($right) ? $url : null;
                } else { $boutonAlter = null; }
            }

            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier['matricule']}">{$liste} -</a> identifiant: {$dossier['matricule']}, rôle: {$dossier['role']} {$boutonAlter}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }
    

}