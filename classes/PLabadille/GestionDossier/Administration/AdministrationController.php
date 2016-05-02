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
        $attributs['hash'] = password_hash($psw, PASSWORD_DEFAULT);
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

    //--------------------
    //4-module gestion et ajout de dossier
    //--------------------

    // 4-1- 'listCreatedFolderWithoutAccount':
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

    // 4-2- 'seeAllAccount':
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

    // 4-3- 'createAccount':
    public function creerCompte()
    {
        //sécurité
        $action = 'seeAllAccount';
        $error = AccessControll::checkRight($action);
        if ( empty($error) ){ //ok
            $attributs = array();
            //on réccupère l'id:
            $attributs['id'] = $this->request->getGetAttribute('id');
            $attributs['identite'] = AdministrationManager::getNomPrenomFromId($attributs['id']);
            $attributs['listeRole'] = AdministrationManager::getListeRole();
            //on genere le mdp et le hash:
            //l'int envoyé correspond à la taille du mdp souhaité
            $attributs += self::generatePasswordAndHashPassword(10);
            //on affiche le formulaire pour le choix de la classe d'utilisateur:
            $prez = AdministrationForm::traitementFormulaireCreerCompte($attributs);
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
                $this->response->setPart('contenu', AdministrationForm::traitementFormulaireCreerCompte($attributs, $errors));
            } else{
                AdministrationManager::addAccount($attributs);
                self::afficherListeDossierSansCompte();
            }
        } else{ //pas ok
            header("location: index.php");
           die($error);
        }
    }

    // 4-4- 'alterPassword':
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
}