<?php
namespace PLabadille\GestionDossier\Dossier;
use PLabadille\Common\Cleaner\Cleaner;
use PLabadille\Common\Cleaner\CleanerTrim;
use PLabadille\Common\Cleaner\CleanerHtmlTags;
use PLabadille\Common\Validator\Validator;
use PLabadille\Common\Validator\ValidatorNotEmpty;
use PLabadille\Common\Validator\ValidatorEmail;
use PLabadille\Common\Validator\ValidatorTitleMinLength;
use PLabadille\Common\Validator\ValidatorDateFormat;
use PLabadille\Common\Validator\ValidatorPhoneNumberFormat;

class DossierForm {
    protected $dossier;
    public function __construct($dossier = null) {
        $this->dossier = $dossier;
    }

    public function traitementFormulaire($type, $attributs = null, $errors = null) {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/form.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public static function cleaningStrategy()
    {
        $cleaner = new Cleaner();
        $cleaner->addStrategy(new CleanerHtmlTags());
        $cleaner->addStrategy(new CleanerTrim());
        return $cleaner;
    }

    public static function validatingStrategy(array $attributs)
    {
        //initialisation du tableau d'erreur pour éviter les undefined index
        //dans le form.
        $errors = [
            'nom' => null,
            'prenom' => null,
            'date_naissance' => null,
            'genre' => null,
            'tel1' => null,
            'tel2' => null,
            'email' => null,
            'adresse' => null,
            'date_recrutement' => null
        ];

        $validatorNotEmpty = new ValidatorNotEmpty();
        $validatorTitleMinLength = new ValidatorTitleMinLength();
        $validator_Email = new ValidatorEmail();
        $validatorDateFormat = new ValidatorDateFormat();
        $validatorPhoneNumberFormat = new ValidatorPhoneNumberFormat();

        //Validateur champ Nom :
        // 1-Not empty
        $validatorNom = new Validator();
        $validatorNom->addStrategy($validatorNotEmpty);
        $error = $validatorNom->applyStrategies($attributs['nom']);

        if($error !== null){
            $errors['nom']=$error;
        }
        //Validateur champ Prenom :
        //1-Not empty
        $validatorPrenom = new Validator();
        $validatorPrenom->addStrategy($validatorNotEmpty);
        $error = $validatorPrenom->applyStrategies($attributs['prenom']);

        if($error !== null){
            $errors['prenom']=$error;
        }
        //Validateur champ date_naissance :
        //1-Not empty
        //2-Bon format yyyy-mm-jj
        $validatorDateNaissance = new Validator();
        $validatorDateNaissance->addStrategy($validatorNotEmpty);
        $validatorDateNaissance->addStrategy($validatorDateFormat);
        $error = $validatorDateNaissance->applyStrategies($attributs['date_naissance']);

        if($error !== null){
            $errors['date_naissance']=$error;
        }
        //Validateur champ tel1 :
        //1-Not empty
        //2-format téléphone
        $validatorTel1 = new Validator();
        $validatorTel1->addStrategy($validatorNotEmpty);
        $validatorTel1->addStrategy($validatorPhoneNumberFormat);
        $error = $validatorTel1->applyStrategies($attributs['tel1']);

        if($error !== null){
            $errors['tel1']=$error;
        }
        //Validateur champ tel2 :
        //1-Not empty
        //2-format téléphone
        $validatorTel2 = new Validator();
        $validatorTel2->addStrategy($validatorNotEmpty);
        $validatorTel2->addStrategy($validatorPhoneNumberFormat);
        $error = $validatorTel2->applyStrategies($attributs['tel2']);

        if($error !== null){
            $errors['tel2']=$error;
        }
        //Validateur champ email :
        //1-Not empty
        //2-format email
        $validatorEmail = new Validator();
        $validatorEmail->addStrategy($validatorNotEmpty);
        $validatorEmail->addStrategy($validator_Email);
        $error = $validatorEmail->applyStrategies($attributs['email']);

        if($error !== null){
            $errors['email']=$error;
        }
        //Validateur champ adresse :
        //1-Not empty
        $validatorAdresse = new Validator();
        $validatorAdresse->addStrategy($validatorNotEmpty);
        $error = $validatorAdresse->applyStrategies($attributs['adresse']);

        if($error !== null){
            $errors['adresse']=$error;
        }
        //Validateur champ date_recrutement :
        //1-Not empty
        //2-Bon format yyyy-mm-jj
        $validatorDateRecrutement = new Validator();
        $validatorDateRecrutement->addStrategy($validatorNotEmpty);
        $validatorDateRecrutement->addStrategy($validatorDateFormat);
        $error = $validatorDateRecrutement->applyStrategies($attributs['date_recrutement']);

        if($error !== null){
            $errors['date_recrutement']=$error;
        }

        return $errors;
    }
}