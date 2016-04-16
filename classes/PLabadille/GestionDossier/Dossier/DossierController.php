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

    #Gère l'affichage d'un dossier complet
    public function afficheDossierComplet($dossier)
    {
        $matricule = $dossier->getMatricule();
        $aff = DossierManager::getAffectationsById($matricule);
        $app = DossierManager::getAppartenancesById($matricule);
        $det = DossierManager::getGradesDetenuById($matricule);
        $poss = DossierManager::getDiplomesPossedeById($matricule);

        $dossier = DossierHtml::afficheUnDossier($dossier);
        $html = $dossier . "\n";
      
        $affectations = DossierHtml::afficheAffectations($aff);
        $html .= $affectations . "\n";

        $appartenances = DossierHtml::afficheAppartenances($app);
        $html .= $appartenances . "\n";

        $gradesDetenu = DossierHtml::afficheGradesDetenu($det);
        $html .= $gradesDetenu . "\n";

        $diplomePossede = DossierHtml::afficheDiplomesPossede($poss);
        $html .= $diplomePossede . "\n";

        return $html;
    }

    public function afficherListeEligiblePromotion()
    {
        $dossiersEligibles = DossierManager::getAllEligiblePromotion();
        $prez = DossierHtml::afficheDossiersEligiblesPromotion($dossiersEligibles);
        $this->response->setPart('contenu', $prez);
    }

    public function afficherListeEligibleRetraite()
    {
        $dossiersEligibles = DossierManager::getAllEligibleRetraite();
        $prez = DossierHtml::afficheDossiersEligiblesRetraite($dossiersEligibles);
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

    public function rechercherEligiblesRetraite() 
    {
        $search = $this->request->getPostAttribute('search');
        $dossier = DossierManager::rechercherIdOrNameRetraite($search);
        $prez = DossierHtml::afficheDossiersEligiblesRetraite($dossier);
        $this->response->setPart('contenu', $prez);
    }

    public function rechercherEligiblesPromotion() 
    {
        $search = $this->request->getPostAttribute('search');
        $dossier = DossierManager::rechercherIdOrNamePromotion($search);
        $prez = DossierHtml::afficheDossiersEligiblesPromotion($dossier);
        $this->response->setPart('contenu', $prez);
    }

    #Permet de voir le contenu d'un dossier
    public function voir() 
    {
        $id = $this->request->getGetAttribute('id');
        $dossier = DossierManager::getOneFromId($id);
        $prez = self::afficheDossierComplet($dossier);
        $this->response->setPart('contenu', $prez);
    }

    #Permet de lancer les différentes validations d'erreurs de formulaire + verification de doublon 
    #Ajoute et lance l'affichage si tout est ok
    public function checkErrorThenReturnOrAddAndView($type, $attributs, $edit)
    {
        #definition des éléments changeants selon l'action
        switch ($type) {
            case 'militaireForm':
                if ($edit == true){
                    $typeFormulaire = 'sauvegarderEditionDossier';
                    $addFunctionNameManager = 'editerUnDossier';

                } else{
                    $typeFormulaire = 'sauvegarderNouveauDossier';
                    $addFunctionNameManager = 'ajouterUnDossier';

                }
                $nomFonctionFormulaire = 'traitementFormulaireMilitaire';
                break;
            case 'regimentForm':
                $typeFormulaire = 'sauvegarderNouvelleAppartenanceRegiment';
                $nomFonctionFormulaire = 'traitementFormulaireAppartientRegiment';
                $addFunctionNameManager = 'ajouterUneAppartenanceRegiment';
                break;
            case 'affectationForm':
                $typeFormulaire = 'sauvegarderNouvelleAffectation';
                $nomFonctionFormulaire = 'traitementFormulaireAffectation';
                $addFunctionNameManager = 'ajouterUneAffectation';
                break;  
            case 'ajoutGradeDetenuForm':
                $typeFormulaire = 'sauvegarderNouveauGradeDetenu';
                $nomFonctionFormulaire = 'traitementFormulaireGradeDetenu';
                $addFunctionNameManager = 'ajouterUnGradeDetenu';
                break;
            case 'ajoutDiplomePossedeForm':
                $typeFormulaire = 'sauvegarderNouveauDiplomePossede';
                $nomFonctionFormulaire = 'traitementFormulaireDiplomePossede';
                $addFunctionNameManager = 'ajouterUnDiplomePossede';
                break;
        }

        $errors = DossierForm::validatingStrategy($attributs, $type);
        $dossier = new Dossier();
        $cleanErrors = array_filter($errors); #retire les clés vides
        if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
            $form = new DossierForm($dossier);
            $this->response->setPart('contenu', $form->$nomFonctionFormulaire($typeFormulaire, $attributs, $errors));
        } else{
            #Cette étape n'est nécessaire que pour l'ajout en bdd, pour l'édition on est sur de l'absence de doublon
            if ($edit == false){
                #S'il n'y a pas d'erreur on vérifi qu'il n'y a pas de doublon
                ##Si pas de doublon il s'ajoute en base et passe pour l'affichage (else) sinon il ne rentre pas en base et on affiche l'erreur.
                $dossier = DossierManager::$addFunctionNameManager($attributs);
                if(is_string($dossier)){
                    ##Dans ce cas c'est que l'entrée est un doublon, on relance le formulaire en affichant l'erreur.
                    $errors['doublon'] = $dossier;
                    $form = new DossierForm($dossier);
                    $this->response->setPart('contenu', $form->$nomFonctionFormulaire($typeFormulaire, $attributs, $errors));
                } else{
                    $prez = self::afficheDossierComplet($dossier);
                    $this->response->setPart('contenu', $prez);
                }
            } else{
                #Lors de l'édition on ne verifie pas les doublons
                $dossier = DossierManager::$addFunctionNameManager($attributs);
                $prez = self::afficheDossierComplet($dossier);
                $this->response->setPart('contenu', $prez);
            }
        }
    }

    #Permet d'afficher le formulaire de création d'un dossier
    public function creerDossier()
    {
        $type = 'sauvegarderNouveauDossier';

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        $prez = $form->traitementFormulaireMilitaire($type);
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
        #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
        $type = 'militaireForm';
        $edit = false;
        #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
        self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
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

        $prez = $form->traitementFormulaireMilitaire($type, $attributs);
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
        
        #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
        $type = 'militaireForm';
        $edit = true;
        #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
        self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);  
    }

    public function ajouterAffectation()
    {
        $type = 'sauvegarderNouvelleAffectation';

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        #on récuppère la liste de selection caserne
        $attributs['listeCaserne'] = DossierManager::listeNomCaserne();
        $attributs['id'] = $this->request->getGetAttribute('id');

        $prez = $form->traitementFormulaireAffectation($type, $attributs);
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouvelleAffectation()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        #on réccupère le dossier pour avoir la date de recrutement
        $miltDossier = DossierManager::getOneFromId($attributs['id']);
        $affectations = DossierManager::getAffectationsById($attributs['id']);
        
        #strategie de nettoyage des données Post:
        $cleaner = DossierForm::cleaningStrategy();
        foreach ($attributs as $key => $value) {
            $attributs[$key] = $cleaner->applyStrategies($value);
        }
        
        #variables nécessaires pour certaines verifications particulières
        $attributs['date_recrutement'] = $miltDossier->getDateRecrutement();
        $attributs['date_former_affectation'] = (isset($affectations['0']) ? $affectations['0']['date_affectation'] : null);
        $attributs['listeCaserne'] = DossierManager::listeNomCaserne();
        #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
        $type = 'affectationForm';
        $edit = false;
        #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)        
        self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
    }

    public function ajouterAppartenanceRegiment()
    {
        $type = 'sauvegarderNouvelleAppartenanceRegiment';

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        #on récuppère la liste de selection caserne
        $attributs['listeRegiment'] = DossierManager::listeNomRegiment();
        $attributs['id'] = $this->request->getGetAttribute('id');

        $prez = $form->traitementFormulaireAppartientRegiment($type, $attributs);
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouvelleAppartenanceRegiment()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        #on réccupère le dossier pour avoir la date de recrutement
        $miltDossier = DossierManager::getOneFromId($attributs['id']);
        $regiment = DossierManager::getAppartenancesById($attributs['id']);
        
        #strategie de nettoyage des données Post:
        $cleaner = DossierForm::cleaningStrategy();
        foreach ($attributs as $key => $value) {
            $attributs[$key] = $cleaner->applyStrategies($value);
        }
        
        #variables nécessaires pour certaines verifications particulières
        $attributs['date_recrutement'] = $miltDossier->getDateRecrutement();
        $attributs['date_former_regiment'] = (isset($regiment['0']) ? $regiment['0']['date_appartenance'] : null);
        $attributs['listeRegiment'] = DossierManager::listeNomRegiment();
        #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
        $type = 'regimentForm';
        $edit = false;
        #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)        
        self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
    }

    public function ajouterGradeDetenu()
    {
        $type = 'sauvegarderNouveauGradeDetenu';

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        #on récuppère la liste de selection caserne
        $attributs['listeGrade'] = DossierManager::listeNomGrade();
        $attributs['id'] = $this->request->getGetAttribute('id');

        $prez = $form->traitementFormulaireGradeDetenu($type, $attributs);
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouveauGradeDetenu()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        #on réccupère le dossier pour avoir la date de recrutement
        $miltDossier = DossierManager::getOneFromId($attributs['id']);
        $grade = DossierManager::getGradesDetenuById($attributs['id']);
        
        #strategie de nettoyage des données Post:
        $cleaner = DossierForm::cleaningStrategy();
        foreach ($attributs as $key => $value) {
            $attributs[$key] = $cleaner->applyStrategies($value);
        }
        
        #variables nécessaires pour certaines verifications particulières
        $attributs['date_recrutement'] = $miltDossier->getDateRecrutement();
        $attributs['date_former_grade'] = (isset($grade['0']) ? $grade['0']['date_promotion'] : null);
        $attributs['listeGrade'] = DossierManager::listeNomGrade();
        #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
        $type = 'ajoutGradeDetenuForm';
        $edit = false;
        #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)        
        self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
    }

    public function ajouterDiplomePossede()
    {
        $type = 'sauvegarderNouveauDiplomePossede';

        $dossier = new Dossier;
        $form = new DossierForm($dossier);

        #on récuppère la liste de selection caserne
        $attributs['listeDiplome'] = DossierManager::listeNomDiplome();
        $attributs['id'] = $this->request->getGetAttribute('id');

        $prez = $form->traitementFormulaireDiplomePossede($type, $attributs);
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouveauDiplomePossede()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        #on réccupère le dossier pour avoir la date de recrutement
        $miltDossier = DossierManager::getOneFromId($attributs['id']);
        $diplome = DossierManager::getDiplomesPossedeById($attributs['id']);
        
        #strategie de nettoyage des données Post:
        $cleaner = DossierForm::cleaningStrategy();
        foreach ($attributs as $key => $value) {
            $attributs[$key] = $cleaner->applyStrategies($value);
        }
        
        #variables nécessaires pour certaines verifications particulières
        $attributs['listeDiplome'] = DossierManager::listeNomDiplome();
        #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
        $type = 'ajoutDiplomePossedeForm';
        $edit = false;
        #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)        
        self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
    }

    #action par défault si l'url est vide.
    public function defaultAction()
    {
        $this->afficherListeDossier();
    }
}