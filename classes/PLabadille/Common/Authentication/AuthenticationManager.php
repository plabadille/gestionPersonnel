<?php

namespace PLabadille\Common\Authentication;

use PLabadille\Common\Bd\DB;
use PLabadille\Common\Controller\Request;

/**
* \author Pierre Labadille
* \namespace PLabadille\Common\Authentication
* \class AuthenticationManager
* \brief Classe gérant la connexion
*/
class AuthenticationManager
{
    const LOGIN_KEYWORD = 'matricule';
    const PWD_KEYWORD = 'pass';
    static protected $instance = null;
    protected $infoAuth = []; /*!< Tableau d'objet contenant les informations de SESSION */
    protected $request;

    /**
    * \fn private function __construct($request)
    * \brief Constructeur de la classe : singleton
    * \param $request contient le param de requête
    */
    private function __construct($request) 
    {
        $this->request = $request;
        if ($request == null){
            throw new \Exception("Error Processing Request");       
        }
        $this->infoAuth = $this->request->getSessionAttribute('infoAuth');
    }

    public function __clone() {}

    /**
    * \fn public static function getInstance($request = null)
    * \brief Permet de construire la classe en dehors de celle-ci si elle n'existe pas déjà.
    * \param $request Variable d'accès superglobale ici utile pour _SESSION, peut être nulle.
    */
    public static function getInstance($request = null) 
    {
        if (self::$instance === null)
            self::$instance = new self($request);
        return self::$instance;
    }

    /**
    * \fn public function checkAuthentication($user, $psw)
    * \brief Verifie la concordence entre le user et mdp fourni et celui stocké en BDD : on verifi le hash de le BD avec le psw fourni.
    * \param $user Nom d'utilisateur saisi par l'internaute dans le formulaire.
    * \param $psw Mdp saisi par l'internaute dans le formulaire (non hash).
    */
    public function checkAuthentication($matricule, $psw)
    {
        $pdo = DB::getInstance()->getPDO();

        $req = 'select * from Users where matricule = :matricule';
        $stmt = $pdo->prepare($req);
        $data = ['matricule' => $matricule];
        $stmt->execute($data);

        $userBd = $stmt->fetch();

        $req = 'select * from Droits where role = :role';
        $stmt = $pdo->prepare($req);
        $data = ['role' => $userBd['role']];
        $stmt->execute($data);

        $userRights = $stmt->fetch();
        unset($userRights['role']);
        $stmt->closeCursor();

        //Ne gérant pas de formulaire de création de compte dans cette application
        //Les hash de la base sont généré avec ma fonction : password_hash("mdp_a_hasher", PASSWORD_DEFAULT);
        //Puis stocké directement en BDD.
        $hash = $userBd['pass'];
        //vérifier si l'user est bien en BD
        if($matricule===$userBd['matricule']){
            // Pour vérifier le mot de passe lors d'une connexion
            if (password_verify($psw, $hash)) {
                $this->infoAuth = [
                    'matricule'=>$userBd['matricule'],
                    'role'=>$userBd['role'],
                    'droits'=>$userRights
                ];
                $this->synchronize();
            }
        }
    }

    /**
    * \fn public function isConnected()
    * \brief Vérifie si l'utilisateur est bien connecté
    * \return Renvois TRUE si connecté (infoAuth n'est pas vide) ou FALSE si l'inverse.
    */
    public function isConnected()
    {
        return (!empty($this->infoAuth));
    }

    /**
    * \fn public function logOut()
    * \brief Fonction de déconnexion : vide infoAuth contenant les informations de connexion puis set la nouvelle session vide.
    */
    public function logOut()
    {
        $this->infoAuth = [];
        $this->synchronize();
    }

    /**
    * \fn protected function synchronize()
    * \brief Met à jour la session
    */
    protected function synchronize()
    {
        $this->request->setSession('infoAuth', $this->infoAuth);
    }

    /* accesseurs des données en session */
    /**
    * \fn public function getMatricule()
    * \brief Assesseur propriété Matricule
    * \return Retourne le matricule stocké en session
    */
    public function getMatricule()
    {
        return $this->infoAuth['matricule'];
    }
    /**
    * \fn public function getRole()
    * \brief Assesseur propriété Role
    * \return Retourne le role stocké en session
    */
    public function getRole()
    {
        return $this->infoAuth['role'];
    }
    /**
    * \fn public function getDroits()
    * \brief Assesseur propriété droits
    * \return Retourne les droits stocké en session
    */
    public function getDroits()
    {
        return $this->infoAuth['droits'];
    }
}