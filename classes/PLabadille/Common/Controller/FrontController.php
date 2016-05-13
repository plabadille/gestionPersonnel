<?php

namespace PLabadille\Common\Controller;

use PLabadille\Common\Authentication\AuthenticationManager;
use PLabadille\Common\Authentication\AuthenticationHtml;
use PLabadille\GestionDossier\Controller\AccessControll;
use PLabadille\GestionDossier\Home\HomeHtml;
use PLabadille\Common\Cleaner\Cleaner;
use PLabadille\Common\Cleaner\CleanerTrim;
use PLabadille\Common\Cleaner\CleanerHtmlTags;

class FrontController
{
	protected $request;
	protected $response;
	protected $router;

	public function __construct($request, $response, $router)
	{
		$this->request = $request;
		$this->response = $response;
		$this->router = $router;
	}

	public function execute()
	{
		$authManager = AuthenticationManager::getInstance($this->request);
		$url = $_SERVER['REQUEST_URI'];
		$this->response->setPart('loginDisplay', AuthenticationHtml::afficher($url));
		$this->response->setPart('navigation', AccessControll::afficherNavigation());

        $login = $this->request->getPostAttribute(AuthenticationManager::LOGIN_KEYWORD);
        $password = $this->request->getPostAttribute(AuthenticationManager::PWD_KEYWORD);

        //netoyage des données reçue (à amméliorer et externaliser si temps)
        $cleaner = new Cleaner();
        $cleaner->addStrategy(new CleanerHtmlTags());
        $cleaner->addStrategy(new CleanerTrim());
        
        $login = $cleaner->applyStrategies($login);
        $password = $cleaner->applyStrategies($password);
 		
 		 if (!$authManager->isConnected() AND !empty($login)) {
                // donnée POST pour le login => l'utilisateur essaye de se connecter
                // vérifier le login/pwd
                $authManager->checkAuthentication($login, $password);
                if ($authManager->isConnected()) {
                    // la connexion a réussi
                    // => modifier loginDisplay pour afficher les infos utilisateur et non le formulaire
                    $this->response->setPart('loginDisplay', AuthenticationHtml::afficher($url));
                    $this->response->setPart('navigation', AccessControll::afficherNavigation());
                } else{
                    $error = 'Mauvais identifiant ou mot de passe';
                    $this->response->setPart('loginDisplay', AuthenticationHtml::afficher($url, $error));
                    $this->response->setPart('navigation', AccessControll::afficherNavigation()); 
                }
         } else{ //l'user n'essaye pas de ce connecter
            $this->response->setPart('loginDisplay', AuthenticationHtml::afficher($url));
            $this->response->setPart('navigation', AccessControll::afficherNavigation());
         }

		$router = $this->router;
		$classController = $router->getClassController();
		$action = $router->getAction();

		$controller = new $classController($this->request, $this->response);

		if (method_exists($controller, $action)){
			$controller->$action();
		} else {
			$controller->defaultAction();
		}
	}

}