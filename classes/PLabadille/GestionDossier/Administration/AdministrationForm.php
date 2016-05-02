<?php
namespace PLabadille\GestionDossier\Administration;
use PLabadille\Common\Cleaner\Cleaner;
use PLabadille\Common\Cleaner\CleanerTrim;
use PLabadille\Common\Cleaner\CleanerHtmlTags;
use PLabadille\Common\Validator\Validator;
use PLabadille\Common\Validator\ValidatorNotEmpty;
use PLabadille\Common\Validator\ValidatorEmail;
use PLabadille\Common\Validator\ValidatorTitleMinLength;
use PLabadille\Common\Validator\ValidatorDateFormat;
use PLabadille\Common\Validator\ValidatorPhoneNumberFormat;
use PLabadille\Common\Validator\ValidatorCheckIfDateIsMoreRecent;
use PLabadille\Common\Validator\ValidatorIsNumber;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 4- Module création de compte et de droit
# 5- Module de gestion de l'application
# 6- Module de sauvegarde et de gestion de crise
//--------------------

#Gère le traitement des formulaires (affichage via template)
#Ainsi que les stratégies de validation et de nettoyage.
class AdministrationForm 
{
    //--------------------
    //x-Fonctions génériques
    //--------------------
    public static function cleaningStrategy()
    {
        $cleaner = new Cleaner();
        $cleaner->addStrategy(new CleanerHtmlTags());
        $cleaner->addStrategy(new CleanerTrim());
        return $cleaner;
    }

    public static function validatingStrategy(array $attributs, $type)
    {
        #initialisation des validators
        $validatorNotEmpty = new ValidatorNotEmpty();
        // $validatorTitleMinLength = new ValidatorTitleMinLength();
        // $validator_Email = new ValidatorEmail();
        // $validatorDateFormat = new ValidatorDateFormat();
        // $validatorPhoneNumberFormat = new ValidatorPhoneNumberFormat();
        // $validatorCheckIfDateIsMoreRecent = new ValidatorCheckIfDateIsMoreRecent();
        // $validatorIsNumber = new ValidatorIsNumber();

        #switch selon le type de formulaire.
        switch ($type) {
            case 'createFolder':
                //initialisation du tableau d'erreur pour éviter les undefined index
                //dans le form.
                $errors = [
                    'role' => null
                ];

                //Validateur champ tel1 :
                //1-Not empty
                //2-format téléphone
                $validatorRole = new Validator();
                $validatorRole->addStrategy($validatorNotEmpty);
                $error = $validatorRole->applyStrategies($attributs['role']);

                if($error !== null){
                    $errors['role']=$error;
                }
                break;
        }
    }
    //--------------------
    //4- Module création de compte et de droit
    //--------------------

    // 4-3- 'createAccount':
    public function traitementFormulaireCreerCompte($attributs, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formCreerCompte.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }
}