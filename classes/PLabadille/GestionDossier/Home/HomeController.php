<?php

namespace PLabadille\GestionDossier\Home;

use PLabadille\Common\Controller\Request;
use PLabadille\Common\Authentication\AuthenticationManager;

/**
* \author Pierre Labadille
* \namespace PLabadille\GestionDossier\Home
* \class HomeController
* \brief Sous-Controller gérant la page d'accueil
*/
class HomeController 
{
	protected $request;
	protected $response;

	public function __construct($request, $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	/**
    * \fn public function home()
    * \brief Set la page d'accueil en affichage
    */
	public function home() 
    {
    	$prez = HomeHtml::toHtml();
		$this->response->setPart('contenu', $prez);
	}

	public function defaultAction()
	{
		$this->home();
	}

	/**
    * \fn public function logOut()
    * \brief Permet à un utilisateur de se déconnecter et le renvoit sur la page d'accueil.
    */
	public function logOut()
	{
		AuthenticationManager::getInstance()->logOut();
		header('Location: index.php');
		die();
	}
}