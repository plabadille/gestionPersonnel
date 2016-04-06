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

$request = new Request();
$response = new Response();
$router = new Router($request);

$frontController = new FrontController($request, $response, $router);
$frontController->execute();
$prez = $response->getPart('contenu');

include_once 'ui/views/template.php';