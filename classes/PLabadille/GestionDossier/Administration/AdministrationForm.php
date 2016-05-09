<?php
namespace PLabadille\GestionDossier\Administration;
use PLabadille\Common\Cleaner\Cleaner;
use PLabadille\Common\Cleaner\CleanerTrim;
use PLabadille\Common\Cleaner\CleanerHtmlTags;
use PLabadille\Common\Cleaner\CleanerPhoneNumber;
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
        $cleaner->addStrategy(new CleanerPhoneNumber());

        return $cleaner;
    }

    public static function validatingStrategy(array $attributs, $type)
    {
        #initialisation des validators
        $validatorNotEmpty = new ValidatorNotEmpty();
        // $validatorTitleMinLength = new ValidatorTitleMinLength();
        // $validator_Email = new ValidatorEmail();
        // $validatorDateFormat = new ValidatorDateFormat();
        $validatorPhoneNumberFormat = new ValidatorPhoneNumberFormat();
        // $validatorCheckIfDateIsMoreRecent = new ValidatorCheckIfDateIsMoreRecent();
        // $validatorIsNumber = new ValidatorIsNumber();

        #switch selon le type de formulaire.
        switch ($type) {
            case 'createFolder':
            case 'alterRightFolder':
                $errors = [
                    'role' => null
                ];

                //1-Not empty
                $validatorRole = new Validator();
                $validatorRole->addStrategy($validatorNotEmpty);
                $error = $validatorRole->applyStrategies($attributs['role']);

                if($error !== null){
                    $errors['role']=$error;
                }
                break;
            case 'ajouterDiplome':
                $errors = [
                    'acronyme' => null,
                    'intitule' => null          
                ];

                //1-Not empty
                $validatorAcronyme = new Validator();
                $validatorAcronyme->addStrategy($validatorNotEmpty);
                $error = $validatorAcronyme->applyStrategies($attributs['acronyme']);

                if($error !== null){
                    $errors['acronyme']=$error;
                }

                //1-Not empty
                $validatorIntitule = new Validator();
                $validatorIntitule->addStrategy($validatorNotEmpty);
                $error = $validatorIntitule->applyStrategies($attributs['intitule']);

                if($error !== null){
                    $errors['intitule']=$error;
                }
                break;
            case 'ajouterRegiment':
                $errors = [
                    'id' => null          
                ];

                //1-Not empty
                $validatorId = new Validator();
                $validatorId->addStrategy($validatorNotEmpty);
                $error = $validatorId->applyStrategies($attributs['id']);

                if($error !== null){
                    $errors['id']=$error;
                }
                break;
            case 'ajouterCaserne':
                $errors = [
                    'nom' => null,
                    'adresse' => null,
                    'tel_standard' => null         
                ];

                //1-Not empty
                $validatorNom = new Validator();
                $validatorNom->addStrategy($validatorNotEmpty);
                $error = $validatorNom->applyStrategies($attributs['nom']);

                if($error !== null){
                    $errors['nom']=$error;
                }

                //1-Not empty
                $validatorAdresse = new Validator();
                $validatorAdresse->addStrategy($validatorNotEmpty);
                $error = $validatorAdresse->applyStrategies($attributs['adresse']);

                if($error !== null){
                    $errors['adresse']=$error;
                }

                //1-Not empty
                //2-Phone number
                $validatorTelStandard = new Validator();
                $validatorTelStandard->addStrategy($validatorNotEmpty);
                $validatorTelStandard->addStrategy($validatorPhoneNumberFormat);
                $error = $validatorTelStandard->applyStrategies($attributs['tel_standard']);

                if($error !== null){
                    $errors['tel_standard']=$error;
                }
                break;
            case 'ajouterGrade':
                $errors = [
                    'grade' => null,
                    'hierarchie' => null        
                ];

                //1-Not empty
                $validatorGrade = new Validator();
                $validatorGrade->addStrategy($validatorNotEmpty);
                $error = $validatorGrade->applyStrategies($attributs['grade']);

                if($error !== null){
                    $errors['grade']=$error;
                }

                //1-Not empty
                $validatorHierarchie = new Validator();
                $validatorHierarchie->addStrategy($validatorNotEmpty);
                $error = $validatorHierarchie->applyStrategies($attributs['hierarchie']);

                if($error !== null){
                    $errors['hierarchie']=$error;
                }
                break;
            case 'ajouterClasseDroits':
                $errors = [
                    'role' => null      
                ];

                //1-Not empty
                $validatorRole = new Validator();
                $validatorRole->addStrategy($validatorNotEmpty);
                $error = $validatorRole->applyStrategies($attributs['role']);

                if($error !== null){
                    $errors['role']=$error;
                }
                break;
        }
        return $errors;
    }
    //--------------------
    //4- Module création de compte et de droit
    //--------------------

    // 4-3- 'createAccount':
    public function traitementFormulaireCreerCompte($attributs, $type, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formCreerCompte.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    //--------------------
    //5-module de gestion de l'application
    //--------------------

    // 5-1- 'seeAllConstanteTable':


    // 5-2- 'addInConstanteTable':

    // 5-2-1 'addCasernes':
    public function traitementFormulaireAjouterCaserne($type, $attributs = null, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formAjouterCaserne.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }
   
    // 5-2-2 'addRegiments':
    public function traitementFormulaireAjouterRegiment($type, $attributs = null, $errors = null)
    {
       ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formAjouterRegiment.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez; 
    }

    // 5-2-3 'addlDiplomes':
    public function traitementFormulaireAjouterDiplome($type, $attributs = null, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formAjouterDiplome.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }
    
    // 5-2-4 'addGrades':
    public function traitementFormulaireAjouterGrade($type, $attributs = null, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formAjouterGrade.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }
   
    // 5-2-5 'addDroits':
    public function traitementFormulaireAjouterClasseDroits($type, $attributs = null, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Administration/view/formAjouterClasseDroits.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

}