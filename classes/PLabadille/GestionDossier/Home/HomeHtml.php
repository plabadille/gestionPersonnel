<?php

namespace PLabadille\GestionDossier\Home;

use PLabadille\Common\Authentication\AuthenticationManager;
use PLabadille\GestionDossier\Dossier\DossierManager;
use PLabadille\GestionDossier\Dossier\Dossier;

/**
* \author Pierre Labadille
* \namespace PLabadille\MiniJournal\Home
* \class HomeHtml
* \brief Gère l'affichage HTML de la page d'accueil
*/

//--------------------
//ORGANISATION DU CODE
//--------------------
# 0- Module de connexion
//--------------------

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
        $auth = AuthenticationManager::getInstance();
        if ($auth->isConnected()){
            $username = $auth->getMatricule();
            $folder = DossierManager::getOneFromId($username);
            $prenom = $folder->getPrenom();
            $nom = $folder->getNom();
        } else{
            $prenom = null;
            $nom = null;
        }
		$nameLogin = AuthenticationManager::LOGIN_KEYWORD;
        $namePwd = AuthenticationManager::PWD_KEYWORD;

        $html = <<<EOT
        {$viewErrors}
        <h2>Accueil</h2>
        <h3>Bonjour {$prenom} {$nom},</h3>
        <p>Vous pouvez utiliser le menu de gauche afin de naviguer sur l'application. Vous ne verrez automatiquement que les modules que votre classe d'utilisateur vous permet d'utiliser.</p>
        <p>Si vous remarquez un problème dans l'application, veuillez contacter un administrateur : admin@test.com</p>
EOT;
		return $html;
	}
}