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

//--------------------
//MODULE IMPLEMENTE
//--------------------
# x- Module de vérification d'éligibilité (uniquement par CRON)
#  -> Cf EligibleHandler.php
# 0- Module de connexion
#  -> Cf PLabadille\GestionDossier\Home
# 1- Module mon dossier
#  -> Cf PLabadille\GestionDossier\Dossier
# 2- Module de gestion et ajout de dossier
#  -> Cf PLabadille\GestionDossier\Dossier
# 3- Module de gestion de promotion et retraite
#  -> Cf PLabadille\GestionDossier\Dossier
//--------------------
//MODULE TO DO
//--------------------
# 4- Module de création de compte et de droit
#  -> Cf PLabadille\GestionDossier\Administration
# 5- Module de gestion de l'application
#  -> Cf PLabadille\GestionDossier\Administration
# 6- Module de sauvegarde et de gestion de crise
#  -> Cf PLabadille\GestionDossier\Administration ??
//--------------------