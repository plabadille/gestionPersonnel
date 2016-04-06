<?php
namespace PLabadille\GestionDossier\Dossier;

class Dossier
{
    protected $matricule;
    protected $nom;
    protected $prenom;
    protected $date_naissance;
    protected $genre;
    protected $tel1;
    protected $tel2;
    protected $email;
    protected $adresse;
    protected $date_recrutement;

    public function __construct($attributs = null)
    {
        if (isset($attributs['matricule']))
            $this->matricule=$attributs['matricule'];
        else
            $this->matricule="";

        if (isset($attributs['nom']))
            $this->nom=$attributs['nom'];
        else
            $this->nom="";

        if (isset($attributs['prenom']))
            $this->prenom=$attributs['prenom'];
        else
            $this->prenom="";

        if (isset($attributs['date_naissance']))
            $this->date_naissance=$attributs['date_naissance'];
        else
            $this->date_naissance="";

        if (isset($attributs['genre']))
            $this->genre=$attributs['genre'];
        else
            $this->genre="";

        if (isset($attributs['tel1']))
            $this->tel1=$attributs['tel1'];
        else
            $this->tel1="";

        if (isset($attributs['tel2']))
            $this->tel2=$attributs['tel2'];
        else
            $this->tel2="";

        if (isset($attributs['email']))
            $this->email=$attributs['email'];
        else
            $this->email="";

        if (isset($attributs['adresse']))
            $this->adresse=$attributs['adresse'];
        else
            $this->adresse="";

        if (isset($attributs['date_recrutement']))
            $this->date_recrutement=$attributs['date_recrutement'];
        else
            $this->date_recrutement="";
    }  

    public function getMatricule()
    {
        return $this->matricule;
    }
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;
    }
    public function getNom()
    {
        return $this->nom;
    }
    public function setNom($nom)
    {
        $this->nom = $nom;
    }
    public function getPrenom()
    {
        return $this->prenom;
    }
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }
    public function getDateNaissance()
    {
        return $this->date_naissance;
    }
    public function setDateNaissance($date_naissance)
    {
        $this->date_naissance = $date_naissance;
    }
    public function getGenre()
    {
        return $this->genre;
    }
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }
    public function getTel1()
    {
        return $this->tel1;
    }
    public function setTel1($tel1)
    {
        $this->tel1 = $tel1;
    }
    public function getTel2()
    {
        return $this->tel2;
    }
    public function setTel2($tel2)
    {
        $this->tel2 = $tel2;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getAdresse()
    {
        return $this->adresse;
    }
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }
    public function getDateRecrutement()
    {
        return $this->date_recrutement;
    }
    public function setDateRecrutement($date_recrutement)
    {
        $this->date_recrutement = $date_recrutement;
    }

}