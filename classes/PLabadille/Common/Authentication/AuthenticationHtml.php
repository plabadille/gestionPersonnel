<?php

namespace PLabadille\Common\Authentication;

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
    public static function afficher($urlAction)
    {
        $auth = AuthenticationManager::getInstance();
        if ($auth->isConnected()) {
            $html = "<div id=\"infosConnexion\">Bonjour {$auth->getMatricule()} <br />Vous êtes {$auth->getRole()}\n";
            $html .= "<div id=\"quitter\"><a href=\"index.php?action=logout\">Déconnexion</a></div>";
        } else {
            $nameLogin = AuthenticationManager::LOGIN_KEYWORD;
            $namePwd = AuthenticationManager::PWD_KEYWORD;

            $html = <<<EOT
        <form action="{$urlAction}" method="post">
            <div id="connexionForm">
                Login : <input class="connexionChamps" type="text" id= "authlogin" name="{$nameLogin}" value="" size="8" /><br />
                Password : <input class="connexionChamps" type="password" id="authpwd" name="{$namePwd}" value="" size="8" /><br />
            <input id="connexionBouton" type="submit" name="envoi" value="Envoi" />
            </div>
        </form>
EOT;
        }
        return $html;
    }
}