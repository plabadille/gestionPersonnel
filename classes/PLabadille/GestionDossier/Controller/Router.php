<?php

namespace PLabadille\GestionDossier\Controller;
use PLabadille\Common\Controller\AbstractRouter;

class Router extends AbstractRouter
{
	public function getClassController()
	{
		$objet = $this->request->getGetAttribute('objet');
		switch ($objet) {
			case 'dossier':
			default:
				return "\\PLabadille\\GestionDossier\\Dossier\\DossierController";
			break;
			case 'home':
				return "\\PLabadille\\GestionDossier\\Home\\HomeController";
			break;
			default:
				return "\\PLabadille\\GestionDossier\\Home\\HomeController";
			break;
		}
	}

	public function getAction()
	{
		return $this->request->getGetAttribute('action');
	}


}