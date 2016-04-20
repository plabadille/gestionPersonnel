<?php

namespace PLabadille\GestionDossier\Home;

use PLabadille\Common\Authentication\AuthenticationManager;

/**
* \author Pierre Labadille
* \namespace PLabadille\MiniJournal\Home
* \class HomeHtml
* \brief Gère l'affichage HTML de la page d'accueil
*/
class HomeHtml
{
	/**
    * \fn public function toHtml($errors=null)
    * \brief Set la page d'accueil en affichage
    * \param $errors array, null possible, contient d'éventuelles erreurs
    * \return $html string Contenant le code HTML de la page d'accueil
    */
	public function toHtml($error=null)
	{
		//gestion des erreurs de droit
        $viewErrors = '';
        if ( !empty($error) ){
            $viewErrors = '<div id="errorFieldDossierForm">';   
            $viewErrors .= '<p>' . $error . '</p>';
            $viewErrors .= '</div>';
        }

		$nameLogin = AuthenticationManager::LOGIN_KEYWORD;
        $namePwd = AuthenticationManager::PWD_KEYWORD;

        $html = <<<EOT
        {$viewErrors}
        <h2>Accueil</h2>
        <p>Connexion effectuée! Bonne navigation</p>
        <p>à changer dans HomeHtml.php</p>
EOT;
		return $html;
	}
}