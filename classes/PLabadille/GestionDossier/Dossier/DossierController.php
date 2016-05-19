<?php
namespace PLabadille\GestionDossier\Dossier;

use PLabadille\Common\Controller\Response;
use PLabadille\Common\Controller\Request;
use PLabadille\GestionDossier\Controller\AccessControll;
use PLabadille\GestionDossier\Home\HomeHtml;
use PLabadille\Common\Authentication\AuthenticationManager;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 1- Module mon dossier
# 2- Module de gestion et ajout de dossier
# 3- Module de gestion de promotion et retraite
//--------------------

#Gère les appels de fonction selon les url post et get
#Fourni par le routeur.
#Pour les modules indiqué ci-dessus.
class DossierController
{
    protected $request;
    protected $response;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    //--------------------
    //x-1-Fonctions utilitaires
    //--------------------
    #Ajoute dans un fichier de log une entrée par élément de dossier supprimé avec le contenu suppr, la date et le nom de l'utilisateur qui a effectué la suppression.
    public function logDeletedInformation($data, $username, $type)
    {
        #date après le lancement du script (avec  heure, minute et seconde)
        $today=date('Y-m-d-H-i-s');

        ##On stock les informations d'executions dans un fichier pour faire un récap.
        #contenu à ajouter au fichier
        $content = "\n" . $today . ' ' . $username . ' ' . $type . ' ';
        $j = 0;
        $col = count($data);
        foreach ($data as $key => $value) {
            $content .= $key . ':';
            $str = explode(' ', $value); //on s'occupe des cas ayant un espace dans la valeur supprimée
            $number = count($str);
            if ($number >= 1){
                for ($i=0; $i < $number ; $i++) { 
                    if($i != $number-1){
                        $content .= $str[$i] . '_';
                    } else{ //dernier élément, on rajoute le ; séparateur
                        if ($j < $col-1){ 
                            $content .= $str[$i] . ';';
                        } else{//dernier élément du tableau
                            $content .= $str[$i];
                        }
                    } 
                }
            }
            $j++;     
        }
        $monfichier = fopen('media/infos/logDeletedFolderInformations.txt', 'r+');
        #on se positionne à la fin du fichier
        fseek($monfichier, 0, SEEK_END);
        fputs($monfichier, $content);
        fclose($monfichier);
    }

    public function logArchivedFolder($username, $matricule)
    {
        #date après le lancement du script (avec  heure, minute et seconde)
        $today=date('Y-m-d-H-i-s');

        ##On stock les informations d'executions dans un fichier pour faire un récap.
        #contenu à ajouter au fichier
        $content = "\n" . $today . ' ' . $username . ' ' . $matricule;

        $monfichier = fopen('media/infos/logArchivedFolderInformations.txt', 'r+');
        #on se positionne à la fin du fichier
        fseek($monfichier, 0, SEEK_END);
        fputs($monfichier, $content);
        fclose($monfichier);
    }

    public function logRetiredFolder($username, $matricule)
    {
        #date après le lancement du script (avec  heure, minute et seconde)
        $today=date('Y-m-d-H-i-s');

        ##On stock les informations d'executions dans un fichier pour faire un récap.
        #contenu à ajouter au fichier
        $content = "\n" . $today . ' ' . $username . ' ' . $matricule;

        $monfichier = fopen('media/infos/logRetiredFolderInformations.txt', 'r+');
        #on se positionne à la fin du fichier
        fseek($monfichier, 0, SEEK_END);
        fputs($monfichier, $content);
        fclose($monfichier);
    }

    public function logDeletedConditionsEligible($data, $username, $type)
    {
        if (!empty($data)){ #on evite les logs de rechargement de page avec l'action de supression.
            #date après le lancement du script (avec  heure, minute et seconde)
            $today=date('Y-m-d-H-i-s');

            ##On stock les informations d'executions dans un fichier pour faire un récap.
            #contenu à ajouter au fichier
            $content = "\n" . $today . ' ' . $username . ' ' . $type . ' ';
            $j = 0;
            $col = count($data);
            foreach ($data as $key => $value) {
                $content .= $key . ':';
                $str = explode(' ', $value); //on s'occupe des cas ayant un espace dans la valeur supprimée
                $number = count($str);
                if ($number >= 1){
                    for ($i=0; $i < $number ; $i++) { 
                        if($i != $number-1){
                            $content .= $str[$i] . '_';
                        } else{ //dernier élément, on rajoute le ; séparateur
                            if ($j < $col-1){ 
                                $content .= $str[$i] . ';';
                            } else{//dernier élément du tableau
                                $content .= $str[$i];
                            }
                        } 
                    }
                }
                $j++;     
            }
            $monfichier = fopen('media/infos/logDeletedConditionsEligible.txt', 'r+');
            #on se positionne à la fin du fichier
            fseek($monfichier, 0, SEEK_END);
            fputs($monfichier, $content);
            fclose($monfichier);
        }
        
    }
    
    public function autoComplete()
    {
        $search = $this->request->getGetAttribute('search');
        $type = $this->request->getGetAttribute('type');
        if (!empty($search)){
            switch ($type) {
                case 'listDossier':
                    $result = DossierManager::ajaxRechercherName($search);
                    break;
                case 'listCreatedDossier':
                    $auth = AuthenticationManager::getInstance();
                    $username = $auth->getMatricule();
                    $result = DossierManager::ajaxRechercherCreatedName($search, $username);
                    break;
                case 'listEligibleRetraite':
                    $result = DossierManager::ajaxRechercherEligibleRetraite($search);
                    break;
                case 'listEligiblePromotion':
                    $result = DossierManager::ajaxRechercherEligiblePromotion($search);
                    break;
                case 'listeNomGrade':
                    $result = DossierManager::ajaxListeNomGrade($search);
                    break;
                case 'listeNomCaserne':
                    $result = DossierManager::ajaxListeNomCaserne($search);
                    break; 
                case 'listeNomDiplome':
                    $result = DossierManager::ajaxListeNomDiplome($search);
                    break; 
                case 'listeNomRegiment':
                    $result = DossierManager::ajaxListeNomRegiment($search);
                    break;
            }
            
            $json = json_encode($result);
            $this->response->setPart('contenu', $json);
        }
    }

    //--------------------
    //x-2-Fonctions génériques
    //--------------------

    #Gère l'affichage d'un dossier complet
    #Utilisée en 2-3 et 2-4
    public function afficheDossierComplet($dossier, $username=null)
    {
        //sécurité
        $action = 'seeAllFolder';
        $error = AccessControll::checkRight($action, $username);
        if ( empty($error) ){ //ok
            $matricule = $dossier->getMatricule();
            $aff = DossierManager::getAffectationsById($matricule);
            $app = DossierManager::getAppartenancesById($matricule);
            $det = DossierManager::getGradesDetenuById($matricule);
            $poss = DossierManager::getDiplomesPossedeById($matricule);

            $info = DossierHtml::afficheUnDossier($dossier);
            $html = $info . "\n";
          
            $affectations = DossierHtml::afficheAffectations($aff, $dossier);
            $html .= $affectations . "\n";

            $appartenances = DossierHtml::afficheAppartenances($app, $dossier);
            $html .= $appartenances . "\n";

            $gradesDetenu = DossierHtml::afficheGradesDetenu($det, $dossier);
            $html .= $gradesDetenu . "\n";

            $diplomePossede = DossierHtml::afficheDiplomesPossede($poss, $dossier);
            $html .= $diplomePossede . "\n";

            return $html;
        } else{ //pas ok
            return $html = HomeHtml::toHtml($error);
        }    
    }

    #Permet de lancer les différentes validations d'erreurs de formulaire + verification de doublon 
    #Ajoute et lance l'affichage si tout est ok
    #Utilisée par les fonctions de création et d'édition
    public function checkErrorThenReturnOrAddAndView($type, $attributs, $edit)
    {
        $username = null;
        if ( isset($attributs['create_by'] ) ){
            $username = $attributs['create_by'];
        }
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
            case 'sonDossierForm':
                $typeFormulaire = 'sauvegardeEditionSonDossier';
                $addFunctionNameManager = 'editerSonDossier';
                $nomFonctionFormulaire = 'traitementFormulaireSonDossier';
                break;
            case 'archivageForm':
                $typeFormulaire = 'sauvegarderArchivageDossier';
                $addFunctionNameManager = 'archiverUnDossier';
                $nomFonctionFormulaire = 'traitementFormulaireArchiveDossier';
                break;
            case 'retraiteForm':
                $typeFormulaire = 'sauvegarderRetraiterDossier';
                $addFunctionNameManager = 'retraiterUnDossier';
                $nomFonctionFormulaire = 'traitementFormulaireRetraiterDossier';
                break;
            case 'editConditionRetraite':
                $typeFormulaire = 'sauvegarderEditionConditionRetraite';
                $addFunctionNameManager = 'EditerConditionsRetraite';
                $nomFonctionFormulaire = 'traitementFormulaireConditionRetraite';
                break;
            case 'editConditionPromotion':
                $typeFormulaire = 'sauvegarderEditionConditionPromotion';
                $addFunctionNameManager = 'EditerConditionsPromotion';
                $nomFonctionFormulaire = 'traitementFormulaireConditionPromotion';
                break;
            case 'ajouterConditionRetraite':
                $typeFormulaire = 'sauvegarderAjouterConditionRetraite';
                $addFunctionNameManager = 'ajouterConditionsRetraite';
                $nomFonctionFormulaire = 'traitementFormulaireConditionRetraite';
                break;
            case 'ajouterConditionPromotion':
                $typeFormulaire = 'sauvegarderAjouterConditionPromotion';
                $addFunctionNameManager = 'ajouterConditionsPromotion';
                $nomFonctionFormulaire = 'traitementFormulaireConditionPromotion';
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
                } elseif ( $typeFormulaire == 'sauvegarderArchivageDossier' || $typeFormulaire == 'sauvegarderRetraiterDossier' ){
                    $prez = self::afficherListeDossier();
                } elseif ( $typeFormulaire == 'sauvegarderAjouterConditionRetraite' || $typeFormulaire == 'sauvegarderAjouterConditionPromotion' ){
                    $prez = self::afficherListeConditionsEligibilites();
                } else{
                    $prez = self::afficheDossierComplet($dossier, $username);
                    $this->response->setPart('contenu', $prez);
                }
            } else{
                #Lors de l'édition on ne verifie pas les doublons
                $dossier = DossierManager::$addFunctionNameManager($attributs);
                //exception pour l'édit de son dossier
                if ( $typeFormulaire == 'sauvegardeEditionSonDossier' ){
                    $prez = self::afficherSonDossier();
                } elseif ( $typeFormulaire == 'sauvegarderEditionConditionRetraite' || $typeFormulaire == 'sauvegarderEditionConditionPromotion' ){
                    $prez = self::afficherListeConditionsEligibilites();
                } else{
                    $prez = self::afficheDossierComplet($dossier, $username);
                    $this->response->setPart('contenu', $prez);
                }
            }
        }
    }

    //--------------------
    //1-module mon dossier
    //--------------------
    // 1-1- 'seeOwnFolderModule':
    public function afficherSonDossier()
    {
        //rappel : l'username = matricule
        $auth = AuthenticationManager::getInstance();
        $matricule = $auth->getMatricule();
        //sécurité
        $action = 'seeOwnFolderModule';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossier = DossierManager::getUserFullFolder($matricule);
            $prez = DossierHtml::viewUserFolder($dossier);
        } else{
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    
    // 1-2- 'editOwnFolderPersonalInformation':
    public function editerSonDossier()
    {
        $auth = AuthenticationManager::getInstance();
        $matricule = $auth->getMatricule();
        //sécurité
        $action = 'editOwnFolderPersonalInformation';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $old_dossier = DossierManager::getOneFromId($matricule);
            $attributs['nom'] = $old_dossier->getNom();
            $attributs['prenom'] = $old_dossier->getPrenom();
            $attributs['date_naissance'] = $old_dossier->getDateNaissance();
            $attributs['genre'] = $old_dossier->getGenre();
            $attributs['tel1'] = $old_dossier->getTel1();
            $attributs['tel2'] = $old_dossier->getTel2();
            $attributs['email'] = $old_dossier->getEmail();
            $attributs['adresse'] = $old_dossier->getAdresse();
            $attributs['date_recrutement'] = $old_dossier->getDateRecrutement();
            $attributs['id'] = $matricule;

            $dossier = new Dossier;
            $form = new DossierForm($dossier);
            $type = 'sauvegardeEditionSonDossier';

            $prez = $form->traitementFormulaireSonDossier($type, $attributs);
        } else{
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegardeEditionSonDossier()
    {   
        //sécurité
        $action = 'editOwnFolderPersonalInformation';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #réccupération des données du formulaire
            $data = $this->request->getPost();
            $attributs['tel1'] = $data['tel1'];
            $attributs['tel2'] = $data['tel2'];
            $attributs['email'] = $data['email'];
            $attributs['adresse'] = $data['adresse'];
            $attributs['id'] = $data['id'];
            #Stratégie de nettoyage des données Post
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            
            #réccupération des données originales qui ne changent pas
            $old_dossier = DossierManager::getOneFromId($attributs['id']);
            $attributs['nom'] = $old_dossier->getNom();
            $attributs['prenom'] = $old_dossier->getPrenom();
            $attributs['date_naissance'] = $old_dossier->getDateNaissance();
            $attributs['genre'] = $old_dossier->getGenre();
            $attributs['date_recrutement'] = $old_dossier->getDateRecrutement();

            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'sonDossierForm';
            $edit = true;
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }  
    }

    //--------------------
    //2-module gestion et ajout de dossier
    //--------------------
    // 2-1- 'listCreatedFolder':
    public function afficherListeDossierSiCreateur() 
    {
        $auth = AuthenticationManager::getInstance();
        $username = $auth->getMatricule();
        //sécurité
        $action = 'listCreatedFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossier = DossierManager::getAllCreatedFolder($username);
            $prez = DossierHtml::listCreatedFolderHtml($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    #Permet de rechercher par id ou nom
    #formulaire dans le toHtml permettant l'affichage de tout les dossiers
    public function rechercherCreatedFolder() 
    {
        $auth = AuthenticationManager::getInstance();
        $username = $auth->getMatricule();
        //sécurité
        $action = 'listCreatedFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $search = $this->request->getPostAttribute('search');
            $dossier = DossierManager::rechercherIdOrNameCreatedFolder($search, $username);
            $prez = DossierHtml::listCreatedFolderHtml($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }


    // rechercherCreatedFolder

    // 2-2- 'listAllFolder':

    #affiche tous les dossiers
    public function afficherListeDossier() 
    {
        //sécurité
        $action = 'listAllFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossier = DossierManager::getAll();
            $prez = DossierHtml::toHtml($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    #Permet de rechercher par id ou nom
    #formulaire dans le toHtml permettant l'affichage de tout les dossiers
    public function rechercher() 
    {
        //sécurité
        $action = 'listAllFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $search = $this->request->getPostAttribute('search');
            $dossier = DossierManager::rechercherIdOrName($search);
            $prez = DossierHtml::toHtml($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    
    // 2-3- 'seeCreatedFolder':
    
    // 2-4- 'seeAllFolder':

    #Permet de voir le contenu d'un dossier
    #Utilise la fonction générique afficheDossierComplet pour afficher les éléments liés à un dossier
    public function voir()
    {   
        $id = $this->request->getGetAttribute('id');
        $dossier = DossierManager::getOneFromId($id);

        //sécurité
        $action = 'seeAllFolder';
        $createBy = $dossier->getWhoCreateFolder();
        $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
        $error = AccessControll::checkRight($action, $creatorIsLog);

        if ( empty($error) ){ //ok
            $prez = self::afficheDossierComplet($dossier, $creatorIsLog);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    // 2-5 'createFolder':

    #Permet d'afficher le formulaire de création d'un dossier
    public function creerDossier()
    {
        //sécurité
        $action = 'createFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderNouveauDossier';

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireMilitaire($type);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }
        
        $this->response->setPart('contenu', $prez);
    }

    #Permet de sauvegarder un dossier créé si correct
    #utilise DossierManager::ajouterUnDossier
    public function sauvegarderNouveauDossier()
    {
        //sécurité
        $action = 'createFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #On regarde qui est le créateur du dossier
            $auth = AuthenticationManager::getInstance();
            $createur = $auth->getMatricule();
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            $attributs['create_by'] = $createur;
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'militaireForm';
            $edit = false;
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }
    
    // 2-6- 'addElementToAFolder':

    public function ajouterAffectation()
    {
        //sécurité
        $attributs['id'] = $this->request->getGetAttribute('id');
        $createur = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createur);
        if ( empty($error) ){ //ok
            #On regarde qui est le créateur du dossier
            $type = 'sauvegarderNouvelleAffectation';

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            #on récuppère la liste de selection caserne
            $attributs['listeCaserne'] = DossierManager::listeNomCaserne();

            $prez = $form->traitementFormulaireAffectation($type, $attributs);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouvelleAffectation()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        //pour l'ajax on explode le grade pour conserver que l'id
        $explode = explode(' ', $attributs['caserneId']);
        $attributs['caserneId'] = $explode['0'];
        //sécurité
        $createurDossier = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createurDossier);
        if ( empty($error) ){ //ok
            $auth = AuthenticationManager::getInstance();
            $createur = $auth->getMatricule();

            $attributs['create_by'] = $createur;
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
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    public function ajouterAppartenanceRegiment()
    {
        $attributs['id'] = $this->request->getGetAttribute('id');
        //sécurité
        $createur = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createur);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderNouvelleAppartenanceRegiment';

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            #on récuppère la liste de selection caserne
            $attributs['listeRegiment'] = DossierManager::listeNomRegiment();

            $prez = $form->traitementFormulaireAppartientRegiment($type, $attributs);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouvelleAppartenanceRegiment()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        //sécurité
        $createurDossier = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createurDossier);
        if ( empty($error) ){ //ok
            $auth = AuthenticationManager::getInstance();
            $createur = $auth->getMatricule();

            $attributs['create_by'] = $createur;
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
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    public function ajouterGradeDetenu()
    {
        $attributs['id'] = $this->request->getGetAttribute('id');
        //sécurité
        $createur = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createur);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderNouveauGradeDetenu';

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            #on récuppère la liste de selection caserne
            $attributs['listeGrade'] = DossierManager::listeNomGrade();

            $prez = $form->traitementFormulaireGradeDetenu($type, $attributs);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouveauGradeDetenu()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        //pour l'ajax on explode le grade pour conserver que l'id
        $explode = explode(' ', $attributs['gradeId']);
        $attributs['gradeId'] = $explode['0'];
        //sécurité
        $createurDossier = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createurDossier);
        if ( empty($error) ){ //ok
            $auth = AuthenticationManager::getInstance();
            $createur = $auth->getMatricule();

            $attributs['create_by'] = $createur;
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
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    public function ajouterDiplomePossede()
    {
        $attributs['id'] = $this->request->getGetAttribute('id');
        //sécurité
        $createur = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createur);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderNouveauDiplomePossede';

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            #on récuppère la liste de selection caserne
            $attributs['listeDiplome'] = DossierManager::listeNomDiplome();

            $prez = $form->traitementFormulaireDiplomePossede($type, $attributs);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderNouveauDiplomePossede()
    {
        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        //pour l'ajax on explode le grade pour conserver que l'id
        $explode = explode(' ', $attributs['diplomeId']);
        $attributs['diplomeId'] = $explode['0'];
        //sécurité
        $createurDossier = DossierManager::getCreatorById($attributs['id']);
        $action = 'addElementToAFolder';
        $error = AccessControll::checkRight($action, $createurDossier);
        if ( empty($error) ){ //ok
            $auth = AuthenticationManager::getInstance();
            $createur = $auth->getMatricule();

            $attributs['create_by'] = $createur;
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
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 2-7- 'editInformationIfAuthor':
    #géré directement
    
    // 2-8- 'editInformation':

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

        //sécurité
        $action = 'editInformation';
        $createBy = $old_dossier->getWhoCreateFolder();
        $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
        $error = AccessControll::checkRight($action, $creatorIsLog);
        if ( empty($error) ){ //ok
            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireMilitaire($type, $attributs);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    #Permet de sauvegarder un dossier édité si correct
    public function sauvegarderEditionDossier()
    {   
        #réccupération des données du formulaire
        $attributs = $this->request->getPost();

        //sécurité
        $createurDossier = DossierManager::getCreatorById($attributs['id']);
        $action = 'editInformation';
        $error = AccessControll::checkRight($action, $createurDossier);
        if ( empty($error) ){ //ok
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
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }  
    }

    // 2-9- 'deleteInformation':

    // 2-9-1 supprimer une affectation:
    public function suprAffectation()
    {
        //sécurité :
        $action = 'deleteFolderInformation';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère l'id à supr (clée primaire)
            $id = $this->request->getGetAttribute('id');
            //ajout d'une entrée dans le fichier de log
            $data = DossierManager::getAffectationByClef($id);
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            $type = 'affectationCaserne';
            self::logDeletedInformation($data, $username, $type);
            //on supprime (retourne le matricule)
            $matricule = DossierManager::suprAffectationById($id);
            //on réaffiche le dossier actualisé
            $dossier = DossierManager::getOneFromId($matricule);
            $prez = self::afficheDossierComplet($dossier);
            $this->response->setPart('contenu', $prez);
        } else{
            header("location: index.php");
            die($error);
        }
    }

    // 2-9-2 supprimer une appartenance:
    public function suprRegimentAppartenance()
    {
        //sécurité :
        $action = 'deleteFolderInformation';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère l'id à supr (clée primaire)
            $id = $this->request->getGetAttribute('id');
            //ajout d'une entrée dans le fichier de log
            $data = DossierManager::getAppartenanceByClef($id);
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            $type = 'appartenanceRegiment';
            self::logDeletedInformation($data, $username, $type);
            //on supprime (retourne le matricule)
            $matricule = DossierManager::suprRegimentAppartenanceById($id);
            //on réaffiche le dossier actualisé
            $dossier = DossierManager::getOneFromId($matricule);
            $prez = self::afficheDossierComplet($dossier);
            $this->response->setPart('contenu', $prez);
        } else{
            header("location: index.php");
            die($error);
        }
    }

    // 2-9-3 supprimer un grade detenu:
    public function suprGradeDetenu()
    {
        //sécurité :
        $action = 'deleteFolderInformation';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère l'id à supr (clée primaire)
            $id = $this->request->getGetAttribute('id');
            //ajout d'une entrée dans le fichier de log
            $data = DossierManager::getDetenuByClef($id);
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            $type = 'detientGrade';
            self::logDeletedInformation($data, $username, $type);
            //on supprime (retourne le matricule)
            $matricule = DossierManager::suprGradeDetenuById($id);
            //on réaffiche le dossier actualisé
            $dossier = DossierManager::getOneFromId($matricule);
            $prez = self::afficheDossierComplet($dossier);
            $this->response->setPart('contenu', $prez);
        } else{
            header("location: index.php");
            die($error);
        }
    }

    // 2-9-4 supprimer un diplome:
    public function suprDiplomePossede()
    {
        //sécurité :
        $action = 'deleteFolderInformation';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère l'id à supr (clée primaire)
            $id = $this->request->getGetAttribute('id');
            //ajout d'une entrée dans le fichier de log
            $data = DossierManager::getPossedeByClef($id);
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            $type = 'possedeDiplome';
            self::logDeletedInformation($data, $username, $type);
            //on supprime (retourne le matricule)
            $matricule = DossierManager::suprDiplomePossedeById($id);
            //on réaffiche le dossier actualisé
            $dossier = DossierManager::getOneFromId($matricule);
            $prez = self::afficheDossierComplet($dossier);
            $this->response->setPart('contenu', $prez);
        } else{
            header("location: index.php");
            die($error);
        }
    }
    // 2-10 'useFileToAddFolders':
    public function importFoldersWithFile($info = null, $content = null, $filename = null)
    {
        //sécurité
        $action = 'useFileToAddFolders';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $prez = DossierHtml::viewParsingForm($info, $content, $filename);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function displaySample()
    {
        //sécurité
        $action = 'useFileToAddFolders';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $filename = $this->request->getGetAttribute('filename');
            //on réccupère le contenu du fichier et on le redécoupe en ligne dans un tableau
            $path = 'media/samples/';
            $content = file_get_contents($path.$filename);
            $content = explode("\n", $content);
            //on l'affiche
            self::importFoldersWithFile(null, $content, $filename);

        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    public function parseAndAddsFolders()
    {
        //sécurité
        $action = 'useFileToAddFolders';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $file = $this->request->getFiles();
            if (!empty($file['importedFile']['tmp_name'])){ //un fichier est bien présent
                switch ($file['importedFile']['type']) { //si le type n'est pas accepté on apsse dans le default qui set accepterType à false
                    case 'text/csv':
                    $acceptedType = true;
                    $trueLine = true; //les deux autres ne donnent pas un num de ligne mais sont des tableaux, il faut donc distinguer ce cas pour l'affichage de la ligne/erreur dans la validation
                    //1- Le cas CSV
                        if (($handle = fopen($file['importedFile']['tmp_name'], "r")) !== FALSE) {
                            //1-1-on commence par trouver le séparateur (, ou ;)
                            $fileHead = fgets($handle);
                            if (substr_count($fileHead, ',') === 8){
                                $delimiter = ',';
                            } elseif (substr_count($fileHead, ';') === 8) {
                                $delimiter = ';';
                            } else{ //la modélisation de la table Militaires induit qu'il est obligatoire d'avoir 9 colonnes et donc 8 séparateurs. Ici on a donc une erreur de mise en forme.
                                echo 'Le fichier n\'est pas correctement mis en forme, veuillez vous référer à l\'exemple proposé';
                            }

                            //1-2-Maintenant qu'on connait le delimiter, on réccupère les données et on les mets en forme.
                            $row = 2; //fgetcsv ne prend pas en compte la première ligne
                            $columnName = explode($delimiter, rtrim($fileHead));
                            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                            {
                                $num = count($data);
                                if ($num === 9){ //on met en forme les données dans un tableau
                                    for ($c=0; $c < $num; $c++) {
                                        $dataAfterParsing[$row][$columnName[$c]] = $data[$c];
                                    }
                                } else{ //erreur de mise en forme, on stock les lignes affectés
                                    $error[] = 'La ligne '.$row.' du fichier importé ne comporte que '.$num.' colonnes, celle-ci n\'a donc pas pu être importée';
                                }
                                $row++;
                            }
                        }
                        break;

                    case 'text/xml':
                    //2- Le cas XML
                        $acceptedType = true;
                        $trueLine = false; //ici le numéro des erreurs n'est pas correct puisqu'il démarre à 0 et non à 1
                        //2-1- On réccupère les donnée à partir du fichier
                        $handler = fopen($file['importedFile']['tmp_name'], "r");  
                        $xml = '';                    
                        while (($data = fgets($handler)) !== FALSE)
                        {  
                            $xml .= $data;  
                        }
                        fclose($handler);
                        //2-2-On met en forme les données.
                        $completeXml = simplexml_load_string($xml);
                        $array = json_decode(json_encode((array) $completeXml), 1);
                        $array = array($completeXml->getName() => $array);
                        $xmlInArray = $array['militaires']['militaire'];

                        foreach ($xmlInArray as $key => $value) {
                            $num = count($value);
                            foreach ($value as $col => $str) { //on nettoye les tableaux vides
                                if (is_array($str)){
                                    $xmlInArray[$key][$col] = null;
                                }
                                if ($num === 9){ //nb de colonnes requises correspondant en base
                                    $dataAfterParsing[$key][$col] = $xmlInArray[$key][$col];
                                } else{ //le fichier n'est pas bien mis en forme
                                    $id = ($trueLine ? $key : $key+1);
                                    $error[] = 'Le groupe de donnée '.$id.' du fichier importé ne comporte que '.$num.' colonnes, celui-ci n\'a donc pas pu être importée';
                                    break;
                                }
                            }
                        }
                        break;

                    case 'application/json':
                    //3-Le cas Json
                        $acceptedType = true;
                        $trueLine = false; //ici le numéro des erreurs n'est pas correct puisqu'il démarre à 0 et non à 1
                        //3-1- On réccupère les données
                        $handler = fopen($file['importedFile']['tmp_name'], "r");
                        $json = '';
                        while (($data = fgets($handler)) !== FALSE) {
                            $json .= $data;
                        }
                        fclose($handler);
                        //3-2-On met en forme les données
                        $json = json_decode($json, true);

                        foreach ($json['militaires'] as $key => $col) {
                            $num = count($col);
                            if ($num === 9){ //nb de colonnes requises correspondant en base
                                foreach ($col as $nameCol => $value) {
                                    $dataAfterParsing[$key][$nameCol] = $json['militaires'][$key][$nameCol];
                                }
                            } else{ //le fichier n'est pas bien mis en forme
                                $id = ($trueLine ? $key : $key+1);
                                $error[] = 'Le groupe de donnée '.$id.' du fichier importé ne comporte que '.$num.' colonnes, celui-ci n\'a donc pas pu être importée';
                            }
                        }
                        break;
                    
                    default: //le type du fichier importé n'est pas correct
                        $error = 'Le format du fichier n\'est pas valide, veuillez importer un document CSV (, ou ;), XML ou JSON.';
                        $acceptedType = false;
                        break;
                }

                if ($acceptedType === true){
                    //à ce stade s'il n'y pas eue d'erreur importante, on a un tableau associatif (ligne)=>array()'nomColonneEnBase'=>data
                    //Les données sont donc déjà parsé qu'importe le format. On a également un tableau d'erreur contenant les lignes mal formatée
                    //4-1 : Nettoyage des données (on rend impossible l'injection)
                    $cleaner = DossierForm::cleaningStrategy();
                    foreach ($dataAfterParsing as $row => $set) {
                        foreach ($set as $key => $value) {
                            $dataAfterParsing[$row][$key] = $cleaner->applyStrategies($value);
                        }
                    }

                    //4-2 : Validation des données
                    $type = 'militaireForm';
                    foreach ($dataAfterParsing as $row => $set) {
                        $validationErrors = DossierForm::validatingStrategy($set, $type);
                        $cleanErrors = array_filter($validationErrors); #retire les clés vides
                        if (!empty($cleanErrors)){
                            #s'il y a une erreur on rajoute la ligne au tableau d'erreur et on la supprime du tableau de donnée
                            $displayError = 'Erreurs retournées: ';
                            foreach ($cleanErrors as $key => $value) {
                                $displayError .= $key.': '.$value.' ; ';
                            }
                            unset($dataAfterParsing[$row]);
                            $id = ($trueLine ? $row : $row+1);
                            $error[] = 'Les données de la ligne/groupe '.$id.' n\'ont pas pu être validées, vérifier que son contenu respecte bien les obligations de formatage. '.$displayError;
                        }
                    }
                    
                    //4-3 : On envoit les lignes correctes complètes pour envois en base
                    $cleanData = array_filter($dataAfterParsing); #retire les clés vides
                    if (!empty($cleanData)){ //dans ce cas les données restantes sont correcte, on les importes
                        $info = DossierManager::addParsingFolderInBase($dataAfterParsing);
                        $info['error'] = $error;
                        echo 'ajout:'."\n";
                        var_dump($info);
                    } else{ //aucune donnée correcte.
                        $info = $error;
                        $info['success'] = 0;
                    }
                    //4-4 : On réaffiche la page en indiquant combien de lignes ont été importé et si des lignes comprennent des erreurs (si oui lesquels et quel type d'erreur).
                    self::importFoldersWithFile($info);
                } else{ //le type du fichier importé n'est pas supporté
                    self::importFoldersWithFile($error);
                }
            } else{ //aucun fichier reçu
                $error = 'Aucun fichier n\'a été correctement reçu par le système, veuillez réessayer';
                self::importFoldersWithFile($error);
            }   
        } else{ //erreur de droits
            $prez = HomeHtml::toHtml($error);
            $this->response->setPart('contenu', $prez);   
        }
    }

    // 2-11- 'canArchiveAFolder':
    public function archiverDossier()
    {
        //sécurité
        $action = 'canArchiveAFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderArchivageDossier';
            $matricule = $this->request->getGetAttribute('id');
            $dossier = DossierManager::getOneFromId($matricule);
            $attributs['id'] = $matricule;
            $attributs['nom'] = $dossier->getNom();
            $attributs['prenom'] = $dossier->getPrenom();

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireArchiveDossier($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderArchivageDossier()
    {
        //sécurité
        $action = 'canArchiveAFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok

            // $auth = AuthenticationManager::getInstance();
            // $createur = $auth->getMatricule();
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            //ajout d'une entrée dans le fichier de log
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            self::logArchivedFolder($username, $attributs['id']);
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'archivageForm';
            $edit = false;
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    //--------------------
    //3-module gestion promotion et retraite
    //--------------------
    // 3-1- 'listEligible':

    #affichage de la liste des militaires éligible à la promotion
    public function afficherListeEligiblePromotion()
    {
        //sécurité
        $action = 'listEligible';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossiersEligibles = DossierManager::getAllEligiblePromotion();
            //affichage du grade
            foreach ($dossiersEligibles as $key => $value) {
                $gradeSup = DossierManager::getGradeSup($value->getGrade());
                $value->setGrade($gradeSup['0']);
            }      
            $prez = DossierHtml::afficheDossiersEligiblesPromotion($dossiersEligibles);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        }    
        $this->response->setPart('contenu', $prez);
    }

    public function rechercherEligiblesPromotion() 
    {
        //sécurité
        $action = 'listEligible';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $search = $this->request->getPostAttribute('search');
            $dossier = DossierManager::rechercherIdOrNamePromotion($search);
            $prez = DossierHtml::afficheDossiersEligiblesPromotion($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    #affichage de la liste des militaires éligible à la retraite
    public function afficherListeEligibleRetraite()
    {
        //sécurité
        $action = 'listEligible';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossiersEligibles = DossierManager::getAllEligibleRetraite();
            $prez = DossierHtml::afficheDossiersEligiblesRetraite($dossiersEligibles);
        } else{ //pas ok
           $prez = HomeHtml::toHtml($error);
        } 
        $this->response->setPart('contenu', $prez);
    }

    public function rechercherEligiblesRetraite() 
    {
        //sécurité
        $action = 'listEligible';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $search = $this->request->getPostAttribute('search');
            $dossier = DossierManager::rechercherIdOrNameRetraite($search);
            $prez = DossierHtml::afficheDossiersEligiblesRetraite($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    
    // 3-2- 'editEligibleCondition':
    #fonction d'affichage de la liste (avec boutons édit)
    public function afficherListeConditionsEligibilites()
    {
        //sécurité
        $action = 'listEligible';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $conditionsPromotion = DossierManager::getAllConditionsPromotion();
            $conditionsRetraite = DossierManager::getAllConditionsRetraite();
            $liste['nomGrade'] = DossierManager::listeNomGrade();
            $liste['nomDiplome'] = DossierManager::listeNomDiplome();
            $prez = DossierHtml::viewConditionsList($conditionsPromotion, $conditionsRetraite, $liste);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function editConditionRetraite()
    {
        //sécurité
        $action = 'editEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionConditionRetraite';
            $attributs = DossierManager::getConditionRetraiteFromId($id);
            #on récuppère la liste de selection de grade
            $attributs['listeGrade'] = DossierManager::listeNomGrade();

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireConditionRetraite($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionConditionRetraite()
    {
        //sécurité
        $action = 'editEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'editConditionRetraite';
            $edit = true;
            #on récuppère la liste de selection de grade
            $attributs['listeGrade'] = DossierManager::listeNomGrade();
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)        
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    public function suprConditionRetraite()
    {
        //sécurité
        $action = 'suprEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère l'id à supr (clée primaire)
            $id = $this->request->getGetAttribute('id');

            // //ajout d'une entrée dans le fichier de log
            $data = DossierManager::getRetraiteConditionsByClef($id);
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            $type = 'conditionRetraite';
            self::logDeletedConditionsEligible($data, $username, $type);

            DossierManager::suprEligibleConditionRetraite($id);
            //on réaffiche la liste des conditions actualisée
            self::afficherListeConditionsEligibilites();

        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
    }

    public function suprConditionPromotion()
    {
        //sécurité
        $action = 'suprEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère l'id à supr (clée primaire)
            $id = $this->request->getGetAttribute('id');

            // //ajout d'une entrée dans le fichier de log
            $data = DossierManager::getPromotionConditionsByClef($id);
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            $type = 'conditionPromotion';
            self::logDeletedConditionsEligible($data, $username, $type);

            DossierManager::suprEligibleConditionPromotion($id);
            //on réaffiche la liste des conditions actualisée
            self::afficherListeConditionsEligibilites();
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
    }

    public function editConditionPromotion()
    {
        //sécurité
        $action = 'editEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionConditionPromotion';
            $attributs = DossierManager::getConditionPromotionFromId($id);

            #on récuppère la liste de selection de diplome & grade
            $attributs['listeDiplome'] = DossierManager::listeNomDiplome();
            $attributs['listeGrade'] = DossierManager::listeNomGrade();

            $form = new DossierForm();
            $prez = $form->traitementFormulaireConditionPromotion($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionConditionPromotion()
    {
        //sécurité
        $action = 'editEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'editConditionPromotion';
            $edit = true;
            #on récuppère la liste de selection de diplome & grade
            $attributs['listeDiplome'] = DossierManager::listeNomDiplome();
            $attributs['listeGrade'] = DossierManager::listeNomGrade();
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)        
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    

    // 3-3- 'addEligibleCondition':
    public function ajouterConditionRetraite()
    {
        //sécurité
        $action = 'addEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderAjouterConditionRetraite';

            #on récuppère la liste de selection de grade
            $attributs['listeGrade'] = DossierManager::listeNomGrade();

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireConditionRetraite($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderAjouterConditionRetraite()
    {
        //sécurité
        $action = 'addEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'ajouterConditionRetraite';
            $edit = false;
            #on récuppère la liste de selection de grade
            $attributs['listeGrade'] = DossierManager::listeNomGrade();
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
    }


    public function ajouterConditionPromotion()
    {
        //sécurité
        $action = 'addEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderAjouterConditionPromotion';

            #on récuppère la liste de selection de diplome & grade
            $attributs['listeDiplome'] = DossierManager::listeNomDiplome();
            $attributs['listeGrade'] = DossierManager::listeNomGrade();

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireConditionPromotion($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }

        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderAjouterConditionPromotion()
    {
        //sécurité
        $action = 'addEligibleCondition';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #on récuppère la liste de selection de diplome & grade
            $attributs['listeDiplome'] = DossierManager::listeNomDiplome();
            $attributs['listeGrade'] = DossierManager::listeNomGrade();
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'ajouterConditionPromotion';
            $edit = false;
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
    }

    // 3-4- 'canRetireAFolder':
    public function retraiterDossier()
    {
        //sécurité
        $action = 'canRetireAFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderRetraiterDossier';
            $matricule = $this->request->getGetAttribute('id');
            $dossier = DossierManager::getOneFromId($matricule);
            $attributs['id'] = $matricule;
            $attributs['nom'] = $dossier->getNom();
            $attributs['prenom'] = $dossier->getPrenom();

            $dossier = new Dossier;
            $form = new DossierForm($dossier);

            $prez = $form->traitementFormulaireRetraiterDossier($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderRetraiterDossier()
    {
        //sécurité
        $action = 'canRetireAFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok

            // $auth = AuthenticationManager::getInstance();
            // $createur = $auth->getMatricule();
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            //ajout d'une entrée dans le fichier de log
            $auth = AuthenticationManager::getInstance();
            $username = $auth->getMatricule();
            self::logRetiredFolder($username, $attributs['id']);
            #strategie de nettoyage des données Post:
            $cleaner = DossierForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #variables nécessaires pour l'identification de l'action dans la fonction générique de vérification:
            $type = 'retraiteForm';
            $edit = false;
            #fonction générique de vérification (validation + protection contre doublon + affichage et ajout quand ok)
            self::checkErrorThenReturnOrAddAndView($type, $attributs, $edit);
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 3-5- 'editEligibleEmailContent':
    #to do

    // 3-6- 'uploadFileForMail':
    #to do

    // 3-7- 'changePieceJointeForEligibleMail':
    #to do
}