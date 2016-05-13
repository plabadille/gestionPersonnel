<?php

namespace PLabadille\Common\Authentication;

use PLabadille\GestionDossier\Dossier\DossierManager;
use PLabadille\GestionDossier\Dossier\Dossier;

/**
* \author Pierre Labadille
* \namespace PLabadille\Common\Authentication
* \class AuthenticationHtml
* \brief Classe gérant l'affichage du menu de connexion.
*/
class AuthenticationHtml
{
    /**
    * \fn public static function afficher($urlAction)
    * \brief Fonction gérant l'affichage du menu de connexion/connecté.
    * \param $urlAction Variable contenant l'URL de la page d'où la connexion a été lancée.
    * \return Retourne le code HTML adequat.
    */
    public static function afficher($urlAction, $error = null)
    {
        $auth = AuthenticationManager::getInstance();
        if ($auth->isConnected()) {
            $dossier = DossierManager::getUserFullFolder($auth->getMatricule());
            if (isset($dossier['grades']['0'])){
                $grade = explode(' ',$dossier['grades']['0']['grade']);
                $grade = $grade[0];
            } else{
                $grade = null;
            }
            $html = "<div id=\"infosConnexion\">Bonjour {$grade} {$dossier['informations']->getPrenom()} {$dossier['informations']->getNom()} <br />Vous êtes {$auth->getRole()}\n";
            $html .= "<div id=\"quitter\"><a href=\"index.php?action=logout\">Déconnexion</a></div>";
        } else {
            $nameLogin = AuthenticationManager::LOGIN_KEYWORD;
            $namePwd = AuthenticationManager::PWD_KEYWORD;

            $html = <<<EOT
        <form action="{$urlAction}" method="post">
            <div id="connexionForm">
                <h2>Veuillez vous connecter</h2>
                <p>Si vous avez oublié vos identifiants, veuillez faire la demande d'un nouveau mot de passe à votre supérieur hierarchique.</p>
                <p id="coError">{$error}</p>
                <div class="content">
                    <input class="connexionChamps" type="text" id= "authlogin" name="{$nameLogin}" placeholder="Username" size="8" /><br />
                    <input class="connexionChamps" type="password" id="authpwd" name="{$namePwd}" placeholder="Password" size="8" /><br />
                </div>
                <input id="connexionBouton" type="submit" name="envoi" value="Login" />
            </div>
        </form>
EOT;
        }
        return $html;
    }
}