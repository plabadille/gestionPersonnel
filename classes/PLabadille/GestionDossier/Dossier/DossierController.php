<?php
namespace PLabadille\GestionDossier\Dossier;

use PLabadille\GestionDossier\Dossier\Dossier;
use PLabadille\GestionDossier\Dossier\DossierForm;
use PLabadille\GestionDossier\Dossier\DossierManager;
use PLabadille\Common\Controller\Response;
use PLabadille\Common\Controller\Request;

#Gère les appels de fonction selon les url post et get
#Fourni par le routeur.
class DossierController
{
    protected $request;
    protected $response;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    #affiche tout les dossiers
    public function afficherListeDossier() 
    {
        $dossier = DossierManager::getAll();
        $prez = DossierHtml::toHtml($dossier);
        $this->response->setPart('contenu', $prez);
    }

    #Permet de rechercher par id ou nom
    #formulaire dans le toHtml permettant l'affichage de tout les dossiers
    public function rechercher() 
    {
        $search = $this->request->getPostAttribute('search');
        $dossier = DossierManager::rechercherIdOrName($search);
        $prez = DossierHtml::toHtml($dossier);
        $this->response->setPart('contenu', $prez);
    }

    #Permet de voir le contenu d'un dossier
    public function voir() 
    {
        $id = $this->request->getGetAttribute('id');
        $dossier = DossierManager::getOneFromId($id);
        $prez = DossierHtml::afficheUn($dossier);
        $this->response->setPart('contenu', $prez);
    }

    #Permet d'afficher le formulaire de création d'un dossier
    public function creerDossier()
    {
        $type = 'sauvegarderNouveauDossier';

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        $prez = $form->traitementFormulaire($type);
        $this->response->setPart('contenu', $prez);
    }

    #Permet de sauvegarder un dossier créé si correct
    #utilise DossierManager::ajouterUnDossier
    public function sauvegarderNouveauDossier()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        #strategie de nettoyage des données Post:
        $cleaner = DossierForm::cleaningStrategy();
        foreach ($attributs as $key => $value) {
            $attributs[$key] = $cleaner->applyStrategies($value);
        }
        #strategie de validation des données Post
        #et par ce biais gestion des erreurs :
        $errors = DossierForm::validatingStrategy($attributs);
        $dossier = new Dossier();
        $cleanErrors = array_filter($errors); #retire les clés vides
        if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
            $type = "sauvegarderNouveauDossier";
            $form = new DossierForm($dossier);
            $this->response->setPart('contenu', $form->traitementFormulaire($type, $attributs, $errors));
        } else{
            #S'il n'y a pas d'erreur on ajoute le dossier en bdd
            $dossier = DossierManager::ajouterUnDossier($attributs);
            $prez = DossierHtml::afficheUn($dossier);
            $this->response->setPart('contenu', $prez);
        }
    }

    #Permet d'afficher le formulaire d'édition d'un dossier
    public function editerDossier()
    {
        $id = $this->request->getGetAttribute('id');
        $type = 'sauvegarderEditionDossier';
        $old_dossier = DossierManager::getOneFromId($id);

        $attributs['nom'] = $old_dossier->getNom();
        $attributs['prenom'] = $old_dossier->getPrenom();
        $attributs['date_naissance'] = $old_dossier->getDateNaissance();
        $attributs['genre'] = $old_dossier->getGenre();
        $attributs['tel1'] = $old_dossier->getTel1();
        $attributs['tel2'] = $old_dossier->getTel2();
        $attributs['email'] = $old_dossier->getEmail();
        $attributs['adresse'] = $old_dossier->getAdresse();
        $attributs['date_recrutement'] = $old_dossier->getDateRecrutement();
        $attributs['id'] = $id;

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        $prez = $form->traitementFormulaire($type, $attributs);
        $this->response->setPart('contenu', $prez);
    }

    #Permet de sauvegarder un dossier édité si correct
    public function sauvegarderEditionDossier()
    {   
        #réccupération des données du formulaire
        $attributs = $this->request->getPost();
        #Stratégie de nettoyage des données Post
        $cleaner = DossierForm::cleaningStrategy();
        foreach ($attributs as $key => $value) {
            $attributs[$key] = $cleaner->applyStrategies($value);
        }
        #strategie de validation des données Post
        #et par ce biais gestion des erreurs :
        $errors = DossierForm::validatingStrategy($attributs);
        $dossier = new Dossier();
        $cleanErrors = array_filter($errors);
        if(!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code editerDossier et adaptation
            $type = 'sauvegarderEditionDossier';
            $form = new DossierForm($dossier);
            $this->response->setPart('contenu', $form->traitementFormulaire($type, $attributs, $errors));
        } else{
            #S'il n'y a pas d'erreur on modifie le dossier en bdd
            $dossier = DossierManager::editerUnDossier($attributs);
            $prez = DossierHtml::afficheUn($dossier);
            $this->response->setPart('contenu', $prez);
        }   
    }


    #action par défault si l'url est vide.
    public function defaultAction()
    {
        $this->afficherListeDossier();
    }
}