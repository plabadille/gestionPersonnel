<?php

namespace PLabadille;

//Autoloader:
require_once 'config/config.php';

//Declaration emplacement:
use PLabadille\Common\Bd\DB;
use PLabadille\Common\Controller\Response;
use PLabadille\Common\Controller\Request;
use PLabadille\Common\Controller\FrontController;
use PLabadille\GestionDossier\Dossier\DossierHtml;
use PLabadille\GestionDossier\Dossier\DossierController;
use PLabadille\GestionDossier\Controller\Router;
use PLabadille\Common\Authentication\AuthenticationManager;

session_name('miltFramework');
session_start();

$request = new Request();
$response = new Response();
$router = new Router($request);

$frontController = new FrontController($request, $response, $router);
$frontController->execute();

$prezAuth = $response->getPart('loginDisplay');
$prez = $response->getPart('contenu');
$navigation = $response->getPart('navigation');

//Quand l'utilisateur n'est pas connecté on est forcément sur une page de connexion (on utilise un changement de template pour ce faire).
$auth = AuthenticationManager::getInstance();
if ( !$auth->isConnected() ){
	include_once 'ui/views/templateConnexion.php';
} else{
	include_once 'ui/views/template.php';
}
