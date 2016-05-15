<?php
namespace PLabadille\GestionDossier\Administration;

use PLabadille\Common\Controller\Response;
use PLabadille\Common\Controller\Request;
use PLabadille\GestionDossier\Controller\AccessControll;
use PLabadille\GestionDossier\Home\HomeHtml;
use PLabadille\Common\Authentication\AuthenticationManager;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 4- Module création de compte et de droit
# 5- Module de gestion de l'application
# 6- Module de sauvegarde et de gestion de crise
//--------------------

#Gère les appels de fonction selon les url post et get
#Fourni par le routeur.
#Pour les modules indiqué ci-dessus.
class AdministrationController
{
    protected $request;
    protected $response;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    //--------------------
    //x- Fonctions utilitaires et génériques
    //--------------------
    public function autoComplete()
    {
        $search = $this->request->getGetAttribute('search');
        $type = $this->request->getGetAttribute('type');
        if (!empty($search)){
            switch ($type) {
                case 'listeSansCompte':
                    $result = AdministrationManager::ajaxRechercherNameWithOutFolder($search);
                    break;
                case 'listeAvecCompte':
                    $result = AdministrationManager::ajaxSearchCompte($search);
                    break;
            }           
            $json = json_encode($result);
            $this->response->setPart('contenu', $json);
        }
    }

    public function generatePasswordAndHashPassword($length)
    {
        //on genere le mdp (non hash pour le moment)
        $psw = '';
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            //Le mieux serait d'utiliser la fonction random_int() au lieu de mt_rand, mais dispo qu'à partir de PHP7...
            $psw .= $keyspace[mt_rand(0, $max)];
        }
        $attributs['psw'] = $psw;
        //on le hash:
        $attributs['pass'] = password_hash($psw, PASSWORD_DEFAULT);
        //on retourne les deux :
        return $attributs;
    }

    public function logAlterPassword($actionUsername, $username)
    {
        #date après le lancement du script (avec  heure, minute et seconde)
        $today=date('Y-m-d-H-i-s');

        ##On stock les informations d'executions dans un fichier pour faire un récap.
        #contenu à ajouter au fichier
        $content = "\n" . $today . ' ' . $actionUsername . ' ' . $username;
        $monfichier = fopen('media/infos/logPasswordAltered.txt', 'r+');
        #on se positionne à la fin du fichier
        fseek($monfichier, 0, SEEK_END);
        fputs($monfichier, $content);
        fclose($monfichier);
    }

    public function logSuprAccount($actionUsername, $username)
    {
        #date après le lancement du script (avec  heure, minute et seconde)
        $today=date('Y-m-d-H-i-s');

        ##On stock les informations d'executions dans un fichier pour faire un récap.
        #contenu à ajouter au fichier
        $content = "\n" . $today . ' ' . $actionUsername . ' ' . $username;
        $monfichier = fopen('media/infos/logAccountDeleted.txt', 'r+');
        #on se positionne à la fin du fichier
        fseek($monfichier, 0, SEEK_END);
        fputs($monfichier, $content);
        fclose($monfichier);
    }

    public function logSuprConstante($actionUsername, $type, $attributs)
    {
        #date après le lancement du script (avec  heure, minute et seconde)
        $today=date('Y-m-d-H-i-s');

        ##On stock les informations d'executions dans un fichier pour faire un récap.
        #contenu à ajouter au fichier
        $content = "\n" . $today . ' ' . $actionUsername . ' ' . $type;
        foreach ($attributs as $key => $value) {
            $content .= ' ' . $key . ':' . $value;
        }
        $monfichier = fopen('media/infos/logSuprConstanteInformations.txt', 'r+');
        #on se positionne à la fin du fichier
        fseek($monfichier, 0, SEEK_END);
        fputs($monfichier, $content);
        fclose($monfichier);
    }

    //-----------------------------

    //--------------------
    //4-module gestion et ajout de dossier
    //--------------------

    // 4-1- 'listCreatedFolderWithoutAccount':
    //-----------------------------
    public function afficherListeDossierSansCompte() 
    {
        //sécurité
        $action = 'seeAllFolderWithoutAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossier = AdministrationManager::getAllWithOutAccount();
            $prez = AdministrationHtml::listAllWithOutAccount($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function searchNameWithOutFolder() 
    {
        //sécurité
        $action = 'seeAllFolderWithoutAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $search = $this->request->getPostAttribute('search');
            $dossier = AdministrationManager::searchNameWithOutFolder($search);
            $prez = AdministrationHtml::listAllWithOutAccount($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    //-----------------------------

    // 4-2- 'seeAllAccount':
    //-----------------------------
    public function afficherListeCompte($info = null) 
    {
        //sécurité
        $action = 'seeAllAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossier = AdministrationManager::getAllAccount();
            $prez = AdministrationHtml::listAllAccount($dossier, $info);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function searchCompte() 
    {
        //sécurité
        $action = 'seeAllAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $search = $this->request->getPostAttribute('search');
            $dossier = AdministrationManager::searchNameAccount($search);
            $prez = AdministrationHtml::listAllAccount($dossier);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    //-----------------------------

    // 4-3- 'createAccount':
    //-----------------------------
    public function creerCompte()
    {
        //sécurité
        $action = 'seeAllAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $attributs = array();
            //on réccupère l'id:
            $type = 'sauvegarderCompte';
            $attributs['id'] = $this->request->getGetAttribute('id');
            $attributs['identite'] = AdministrationManager::getNomPrenomFromId($attributs['id']);
            $attributs['listeRole'] = AdministrationManager::getListeRole();
            $auth = AuthenticationManager::getInstance();
            $actionUserRole = $auth->getRole();
            if ($actionUserRole != 'superAdmin'){ //seul le superAdmin peut nommer un admin
                for ($i=0; $i < count($attributs['listeRole']) ; $i++) { 
                    unset($attributs['listeRole'][array_search('admin', $attributs['listeRole'][$i])]);
                }    
            }
            //on genere le mdp et le hash:
            //l'int envoyé correspond à la taille du mdp souhaité
            $attributs += self::generatePasswordAndHashPassword(10);
            //on affiche le formulaire pour le choix de la classe d'utilisateur:
            $prez = AdministrationForm::traitementFormulaireCreerCompte($attributs, $type);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderCompte()
    {
        //sécurité
        $action = 'createFolder';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }

            $type = 'createFolder';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderCompte';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireCreerCompte($attributs, $type, $errors));
            } else{
                AdministrationManager::addAccount($attributs);
                self::afficherListeDossierSansCompte();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }
    //-----------------------------

    // 4-4- 'alterPassword':
    //-----------------------------
    public function changePassword()
    {
        //sécurité
        $action = 'alterMdp';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $auth = AuthenticationManager::getInstance();
            $actionUserRole = $auth->getRole();
            $attributs['id'] = $this->request->getGetAttribute('id');
            $attributs += AdministrationManager::searchNameAccount($attributs['id'])['0'];
            //un admin ou superAdmin ne peuvent pas être modifié quoi qu'il arrive, sauf si l'user connecté est superAdmin.
            if ($actionUserRole == 'superAdmin' || ( $attributs['role'] != 'superAdmin' && $attributs['role'] != 'admin' )){
                //creer nouveau mdp
                $attributs += self::generatePasswordAndHashPassword(10);
                AdministrationManager::alterUserPassword($attributs);
                //faire le log
                $actionUsername = $auth->getMatricule();
                self::logAlterPassword($actionUsername, $attributs['id']);
                //affichage
                self::afficherListeCompte($attributs);
            } else{
                $prez = HomeHtml::toHtml($error);
                $this->response->setPart('contenu', $prez);
            }
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
            $this->response->setPart('contenu', $prez);
        }    
    }
    //-----------------------------

    // 4-5- 'alterAccountRight'
    //-----------------------------
    public function changeDroitsCompte()
    {
        //sécurité
        $action = 'alterAccountRight';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //un admin ou superAdmin ne peuvent pas être modifié quoi qu'il arrive, sauf si l'user connecté est superAdmin.
            $auth = AuthenticationManager::getInstance();
            $actionUserRole = $auth->getRole();
            $type = 'sauvegarderChangementDroits';
            //on réccupère les données
            $attributs['id'] = $this->request->getGetAttribute('id');
            $attributs += AdministrationManager::getAccountById($attributs['id']);
            $attributs['identite'] = AdministrationManager::getNomPrenomFromId($attributs['id']);
            $attributs['listeRole'] = AdministrationManager::getListeRole();
            if ($actionUserRole != 'superAdmin'){ //seul le superAdmin peut nommer un admin
                for ($i=0; $i < count($attributs['listeRole']) ; $i++) { 
                    unset($attributs['listeRole'][array_search('admin', $attributs['listeRole'][$i])]);
                }    
            }
            if ($actionUserRole == 'superAdmin' || ( $attributs['role'] != 'superAdmin' && $attributs['role'] != 'admin' )){
                //on affiche le formulaire pour le choix de la classe d'utilisateur:
                $prez = AdministrationForm::traitementFormulaireCreerCompte($attributs, $type);
            } else{
                $prez = HomeHtml::toHtml($error);
            }
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }


    public function sauvegarderChangementDroits()
    {
        //sécurité
        $action = 'alterAccountRight';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            //un admin ou superAdmin ne peuvent pas être modifié quoi qu'il arrive, sauf si l'user connecté est superAdmin.
            $auth = AuthenticationManager::getInstance();
            $actionUserRole = $auth->getRole();
            if ($actionUserRole == 'superAdmin' || ( $attributs['role'] != 'superAdmin' && $attributs['role'] != 'admin' )){
                #strategie de nettoyage des données Post:
                $cleaner = AdministrationForm::cleaningStrategy();
                foreach ($attributs as $key => $value) {
                    $attributs[$key] = $cleaner->applyStrategies($value);
                }

                $type = 'alterRightFolder';
                $errors = AdministrationForm::validatingStrategy($attributs, $type);
                $cleanErrors = array_filter($errors);

                if (!empty($cleanErrors)){
                #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
                #reprise du code creerDossier et adaptation
                    $type = 'sauvegarderChangementDroits';
                    $this->response->setPart('contenu', AdministrationForm::traitementFormulaireCreerCompte($attributs, $type, $errors));
                } else{
                    AdministrationManager::alterAccountRight($attributs);
                    self::afficherListeCompte();
                }
            } else{ //tentative de forcing par url
                header("location: index.php");
                die($error);
            }
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }
    //-----------------------------

    // 4-6- 'deleteAccount'
    //-----------------------------
    public function afficherListeCompteASupr()
    {
        //sécurité
        $action = 'deleteAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $dossier = AdministrationManager::getAllAccountToDelete();
            $prez = AdministrationHtml::listAllAccountToDelete($dossier);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function suprCompte()
    {
        //sécurité
        $action = 'deleteAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère les données
            $id = $this->request->getGetAttribute('id');
            AdministrationManager::deleteAccountById($id);
            //LOG
            $auth = AuthenticationManager::getInstance();
            $actionUsername = $auth->getMatricule();
            self::logSuprAccount($actionUsername, $id);

            self::afficherListeCompteASupr();
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }
    //-----------------------------

    //--------------------
    //5-module de gestion de l'application
    //--------------------

    // 5-1- 'seeAllConstanteTable':
    //-----------------------------

    // 5-1-1 'seeAllCasernes':
    public function afficherListeCasernes($info = null) 
    {
        //sécurité
        $action = 'seeAllConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $casernes = AdministrationManager::getAllCasernes();
            $prez = AdministrationHtml::listAllCasernes($casernes, $info);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    // 5-1-2 'seeAllRegiments':
    public function afficherListeRegiments($info = null) 
    {
        //sécurité
        $action = 'seeAllConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $regiments = AdministrationManager::getAllRegiments();
            $prez = AdministrationHtml::listAllRegiments($regiments, $info);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    // 5-1-3 'seeAllDiplomes':
    public function afficherListeDiplomes($info = null) 
    {
        //sécurité
        $action = 'seeAllConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $diplomes = AdministrationManager::getAllDiplomes();
            $prez = AdministrationHtml::listAllDiplomes($diplomes, $info);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    // 5-1-4 'seeAllGrades':
    public function afficherListeGrades($info = null) 
    {
        //sécurité
        $action = 'seeAllConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $grades = AdministrationManager::getAllGrades();
            $prez = AdministrationHtml::listAllGrades($grades, $info);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    // 5-1-5 'seeAllDroits':
    public function afficherListeDroits($info = null) 
    {
        //sécurité
        $action = 'seeAllConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $droits = AdministrationManager::getAllDroits();
            $prez = AdministrationHtml::listAllDroits($droits, $info);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }
    //-----------------------------


    // 5-2- 'addInConstanteTable':
    //-----------------------------

    // 5-2-1 'addCasernes':
    public function ajouterCaserne()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderCaserne';
            $prez = AdministrationForm::traitementFormulaireAjouterCaserne($type);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderCaserne()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);

        #Reccupération des données du formulaire
        $attributs = $this->request->getPost();
        if ( empty($error) ){ //ok
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterCaserne';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderCaserne';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterCaserne($type, $attributs, $errors));
            } else{
                AdministrationManager::addCaserne($attributs);
                self::afficherListeCasernes();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-2-2 'addRegiments':
    public function ajouterRegiment()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderRegiment';
            $prez = AdministrationForm::traitementFormulaireAjouterRegiment($type);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderRegiment()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterRegiment';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderRegiment';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterRegiment($type, $attributs, $errors));
            } else{
                AdministrationManager::addRegiment($attributs);
                self::afficherListeRegiments();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-2-3 'addlDiplomes':
    public function ajouterDiplome()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderDiplome';
            $prez = AdministrationForm::traitementFormulaireAjouterDiplome($type);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderDiplome()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterDiplome';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderDiplome';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterDiplome($type, $attributs, $errors));
            } else{
                AdministrationManager::addDiplome($attributs);
                self::afficherListeDiplomes();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-2-4 'addGrades':
    public function ajouterGrade()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderGrade';
            $attributs['listeGrade'] = AdministrationManager::getListeGrade();
            $prez = AdministrationForm::traitementFormulaireAjouterGrade($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderGrade()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterGrade';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderGrade';
                $attributs['listeGrade'] = AdministrationManager::getListeGrade();
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterGrade($type, $attributs, $errors));
            } else{
                $errors['doublon'] = AdministrationManager::addGrade($attributs);
                if (!empty($errors['doublon'])){ //s'il y a un doublon en base on réaffiche et erreur
                    $type = 'sauvegarderGrade';
                    $attributs['listeGrade'] = AdministrationManager::getListeGrade();
                    $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterGrade($type, $attributs, $errors));
                } else{//sinon c'est que l'enregistrement c'est bien effectué
                    self::afficherListeGrades();
                }
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-2-5 'addDroits':
    public function ajouterClasseDroits()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $type = 'sauvegarderClasseDroits';
            $prez = AdministrationForm::traitementFormulaireAjouterClasseDroits($type);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderClasseDroits()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterClasseDroits';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderClasseDroits';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterClasseDroits($type, $attributs, $errors));
            } else{
                AdministrationManager::addClasseDroits($attributs);
                self::afficherListeDroits();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }
    //-----------------------------

    // 5-3- 'editInConstanteTable':
    //-----------------------------

    // 5-3-1 'editCaserne':
    public function editerCaserne()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionCaserne';
            $attributs = AdministrationManager::getCaserneById($id);
            $prez = AdministrationForm::traitementFormulaireAjouterCaserne($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionCaserne()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterCaserne';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderEditionCaserne';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterCaserne($type, $attributs, $errors));
            } else{
                AdministrationManager::editCaserne($attributs);
                self::afficherListeCasernes();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }
    
    // 5-3-2 'editRegiment':
    public function editerRegiment()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionRegiment';
            $attributs = AdministrationManager::getRegimentById($id);
            $prez = AdministrationForm::traitementFormulaireAjouterRegiment($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionRegiment()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterRegiment';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderEditionRegiment';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterRegiment($type, $attributs, $errors));
            } else{
                AdministrationManager::editRegiment($attributs);
                self::afficherListeRegiments();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-3-3 'editDiplome':
    public function editerDiplome()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionDiplome';
            $attributs = AdministrationManager::getDiplomeById($id);
            $prez = AdministrationForm::traitementFormulaireAjouterDiplome($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionDiplome()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterDiplome';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderEditionDiplome';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterDiplome($type, $attributs, $errors));
            } else{
                AdministrationManager::editDiplome($attributs);
                self::afficherListeDiplomes();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-3-4 'editGrade':
    public function editerGrade()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionGrade';
            $attributs = AdministrationManager::getGradeById($id);
            $attributs['listeGrade'] = AdministrationManager::getListeGrade();
            $prez = AdministrationForm::traitementFormulaireAjouterGrade($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionGrade()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterGrade';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderEditionGrade';
                $attributs['listeGrade'] = AdministrationManager::getListeGrade();
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterGrade($type, $attributs, $errors));
            } else{
                AdministrationManager::editGrade($attributs);
                self::afficherListeGrades();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 5-3-5 'editClasseDroits':
    public function editerClasseDroits()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $id = $this->request->getGetAttribute('id');
            $type = 'sauvegarderEditionClasseDroits';
            $attributs = AdministrationManager::getClasseDroitsById($id);
            $prez = AdministrationForm::traitementFormulaireAjouterClasseDroits($type, $attributs);
        } else{ //pas ok
            $prez = HomeHtml::toHtml($error);
        }
        $this->response->setPart('contenu', $prez);
    }

    public function sauvegarderEditionClasseDroits()
    {
        //sécurité
        $action = 'editInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            #Reccupération des données du formulaire
            $attributs = $this->request->getPost();
            #strategie de nettoyage des données Post:
            $cleaner = AdministrationForm::cleaningStrategy();
            foreach ($attributs as $key => $value) {
                $attributs[$key] = $cleaner->applyStrategies($value);
            }
            #Strat de validation
            $type = 'ajouterClasseDroits';
            $errors = AdministrationForm::validatingStrategy($attributs, $type);
            $cleanErrors = array_filter($errors);

            if (!empty($cleanErrors)){
            #s'il y a une erreur on réaffiche le formulaire et les erreurs correspondantes
            #reprise du code creerDossier et adaptation
                $type = 'sauvegarderEditionClasseDroits';
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireAjouterClasseDroits($type, $attributs, $errors));
            } else{
                AdministrationManager::editClasseDroits($attributs);
                self::afficherListeDroits();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    //-----------------------------

    // 5-4- 'suprInConstanteTable':
    //-----------------------------

    // 5-4-1 'suprCaserne':
    public function supprimerCaserne()
    {
        //sécurité
        $action = 'deleteInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère les données
            $id = $this->request->getGetAttribute('id');
            $attributs = AdministrationManager::getCaserneById($id);
            $error = AdministrationManager::deleteCaserneById($id); //si foreign key liée
            if (empty($error)){ //si $error est null on supprime
                //LOG
                $type = 'suprCaserne';
                $auth = AuthenticationManager::getInstance();
                $actionUsername = $auth->getMatricule();
                self::logSuprConstante($actionUsername, $type, $attributs);
                //affichage
                $info = 'supression effectuée';
                self::afficherListeCasernes($info);
            } else{ //sinon c'est que la ligne est utilisée, on affiche une erreur
                self::afficherListeCasernes($error);
            }
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }
    
    // 5-4-2 'suprRegiment':
    public function supprimerRegiment()
    {
        //sécurité
        $action = 'deleteInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère les données
            $id = $this->request->getGetAttribute('id');
            $attributs = AdministrationManager::getRegimentById($id);
            $error = AdministrationManager::deleteRegimentById($id); //si foreign key liée
            if (empty($error)){ //si $error est null on supprime
                //LOG
                $type = 'suprRegiment';
                $auth = AuthenticationManager::getInstance();
                $actionUsername = $auth->getMatricule();
                self::logSuprConstante($actionUsername, $type, $attributs);
                //affichage
                $info = 'supression effectuée';
                self::afficherListeRegiments($info);
            } else{ //sinon c'est que la ligne est utilisée, on affiche une erreur
                self::afficherListeRegiments($error);
            }
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    // 5-4-3 'suprDiplome':
    public function supprimerDiplome()
    {
        //sécurité
        $action = 'deleteInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère les données
            $id = $this->request->getGetAttribute('id');
            $attributs = AdministrationManager::getDiplomeById($id);
            $error = AdministrationManager::deleteDiplomeById($id); //si foreign key liée
            if (empty($error)){ //si $error est null on supprime
                //LOG
                $type = 'suprDiplome';
                $auth = AuthenticationManager::getInstance();
                $actionUsername = $auth->getMatricule();
                self::logSuprConstante($actionUsername, $type, $attributs);
                //affichage
                $info = 'supression effectuée';
                self::afficherListeDiplomes($info);
            } else{ //sinon c'est que la ligne est utilisée, on affiche une erreur
                self::afficherListeDiplomes($error);
            }
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    // 5-4-4 'suprGrade':
    public function supprimerGrade()
    {
        //sécurité
        $action = 'deleteInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère les données
            $id = $this->request->getGetAttribute('id');
            $attributs = AdministrationManager::getGradeById($id);
            $error = AdministrationManager::deleteGradeById($id); //si foreign key liée
            if (empty($error)){ //si $error est null on supprime
                //LOG
                $type = 'suprGrade';
                $auth = AuthenticationManager::getInstance();
                $actionUsername = $auth->getMatricule();
                self::logSuprConstante($actionUsername, $type, $attributs);
                //affichage
                $info = 'supression effectuée';
                self::afficherListeGrades($info);
            } else{ //sinon c'est que la ligne est utilisée, on affiche une erreur
                self::afficherListeGrades($error);
            }
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    // 5-4-5 'suprClasseDroits':
    public function supprimerClasseDroits()
    {
        //sécurité
        $action = 'deleteInAConstanteTable';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            //on réccupère les données
            $id = $this->request->getGetAttribute('id');
            $attributs = AdministrationManager::getClasseDroitsById($id);

            $auth = AuthenticationManager::getInstance();
            $userRole = $auth->getRole();
            if ($userRole == 'superAdmin'){ //seul le superAdmin peut supprimer une classe de droit
                $error = AdministrationManager::deleteClasseDroitsById($id); //si foreign key liée
                if (empty($error)){ //si $error est null on supprime
                    //LOG
                    $type = 'suprClasseDroits';
                    $actionUsername = $auth->getMatricule();
                    self::logSuprConstante($actionUsername, $type, $attributs);
                    //affichage
                    $info = 'supression effectuée';
                    self::afficherListeDroits($info);
                } else{ //sinon c'est que la ligne est utilisée, on affiche une erreur
                    self::afficherListeDroits($error);
                }
            } else{
                $error = 'Si vous souhaitez supprimer une classe d\'utilisateur, veuillez demander au superAdministrateur';
                self::afficherListeDroits($error);
            }
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }
    //-----------------------------

    //--------------------
    //6-module de sauvegarde et de gestion de crise
    //--------------------

    // 6-1- 'gestion de la bdd':
    //-----------------------------

    // 6-1-0 'display'
    public function bddManagement($info = null)
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            $prez = AdministrationHtml::displayBddManagement($info);
            $this->response->setPart('contenu', $prez);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    // 6-1-1 'sauvegardeCompleteBdd':
    public function downloadBddDump()
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            AdministrationManager::downloadBdd();
            self::bddManagement();
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }
    // 6-1-2 'suprAllBdd':
    public function deleteTheWholeBdd()
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            AdministrationManager::internalDumpBdd();
            $info = AdministrationManager::deleteBdd();
            self::bddManagement($info);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    // 6-1-3 'setAllUsersRightToNoRight':
    public function setAllUsersToNoRight()
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            $info = AdministrationManager::setNoRights();
            self::bddManagement($info);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    public function unsetAllUsersToNoRight()
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            $info = AdministrationManager::unsetNoRights();
            self::bddManagement($info);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    //-----------------------------

    // 6-2- 'importer un DUMP':
    //-----------------------------

    public function importDump()
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){

        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    //-----------------------------

    // 6-3- 'gérer les fichiers de LOG':
    //-----------------------------

    public function logsManagement($log = null)
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            //on génère un tableau contenant la liste des noms des fichiers de log
            $path = "media/infos";
            if (is_dir($path)) {
                if ($dh = opendir($path)) {
                    while (($file = readdir($dh)) !== false) {
                        //on ne veut pas réccupérer de fichier caché ou autre '.' donc on filtre
                        if (preg_match('#[a-w]{3,}(.txt)#', $file)){
                            $logsName[] = $file;
                        }
                    }
                    closedir($dh);
                }
            }
            // $logsName = explode('\n', $logsName);
            $prez = AdministrationHtml::displayLogsManagement($logsName, $log);
            $this->response->setPart('contenu', $prez);
        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    public function displayLog()
    {
        //sécurité
        $auth = AuthenticationManager::getInstance();
        $actionUserRole = $auth->getRole();
        if ($actionUserRole == 'superAdmin'){
            $filename = $this->request->getGetAttribute('filename');
            //on réccupère le contenu du fichier et on le redécoupe en ligne dans un tableau
            $path = 'media/infos/';
            $content = file_get_contents($path.$filename);
            $content = explode("\n", $content);
            //on l'affiche
            self::logsManagement($content);

        } else{ //pas ok
            header("location: index.php");
            die($error);
        }
    }

    //-----------------------------

}