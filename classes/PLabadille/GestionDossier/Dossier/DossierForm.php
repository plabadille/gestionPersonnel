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
use PLabadille\Common\Validator\ValidatorCheckIfDateIsMoreRecent;

class DossierForm {
    protected $dossier;
    public function __construct($dossier = null) {
        $this->dossier = $dossier;
    }

    public function traitementFormulaireMilitaire($type, $attributs = null, $errors = null) {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formMilitaire.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireAffectation($type, $attributs, $errors = null) {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAffectation.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireAppartientRegiment($type, $attributs, $errors = null) {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAppartenanceRegiment.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireGradeDetenu($type, $attributs, $errors = null) {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAjoutGradeDetenu.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireDiplomePossede($type, $attributs, $errors = null) {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAjoutDiplomePossede.php';
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

    public static function validatingStrategy(array $attributs, $type)
    {
        #initialisation des validators
        $validatorNotEmpty = new ValidatorNotEmpty();
        $validatorTitleMinLength = new ValidatorTitleMinLength();
        $validator_Email = new ValidatorEmail();
        $validatorDateFormat = new ValidatorDateFormat();
        $validatorPhoneNumberFormat = new ValidatorPhoneNumberFormat();
        $validatorCheckIfDateIsMoreRecent = new ValidatorCheckIfDateIsMoreRecent();

        #switch selon le type de formulaire.
        switch ($type) {
            case 'militaireForm':
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
                } else{
                    //validation supplémentaire spéciale date
                    #syntaxe $value['0'] => date devant être plus ancienne
                    #        $value['1'] => date devant être plus récente
                    $value['0'] = $attributs['date_naissance'];
                    $value['1'] = $attributs['date_recrutement'];
                    $validatorDateRecrutement = new Validator();
                    $validatorDateRecrutement->addStrategy($validatorCheckIfDateIsMoreRecent);
                    $error = $validatorDateRecrutement->applyStrategies($value);

                    if($error !== null){
                        $errors['date_recrutement']=$error;
                    } 
                }
                break;
            case 'affectationForm':
                //Validateur champ d'affectation' :
                //1-Not empty
                //2-Bon format yyyy-mm-jj
                //3-Concordence date
                $errors = [
                    'date_affectation' => null,
                ];
                $validatorDateAffectation = new Validator();
                $validatorDateAffectation->addStrategy($validatorNotEmpty);
                $validatorDateAffectation->addStrategy($validatorDateFormat);
                $error = $validatorDateAffectation->applyStrategies($attributs['date_affectation']);

                if($error !== null){
                    $errors['date_affectation']=$error;
                } else{
                    //validation supplémentaire spéciale date
                    #syntaxe $value['0'] => date devant être plus ancienne
                    #        $value['1'] => date devant être plus récente
                    $value['0'] = $attributs['date_recrutement'];
                    $value['1'] = $attributs['date_affectation'];
                    $validatorDateAffectation = new Validator();
                    $validatorDateAffectation->addStrategy($validatorCheckIfDateIsMoreRecent);
                    $error = $validatorDateAffectation->applyStrategies($value);

                    if($error !== null){
                        $errors['date_affectation']=$error;
                    } else{
                        //validation supplémentaire spéciale date : la date doit être plus récente que celle de l'ancienne affectation s'il y en a une.
                        #syntaxe $value['0'] => date devant être plus ancienne
                        #        $value['1'] => date devant être plus récente
                        if (!empty($attributs['date_former_affectation'])){
                            $value['0'] = $attributs['date_former_affectation'];
                            $value['1'] = $attributs['date_affectation'];
                            $validatorDateAppartenance = new Validator();
                            $validatorDateAppartenance->addStrategy($validatorCheckIfDateIsMoreRecent);
                            $error = $validatorDateAppartenance->applyStrategies($value);

                            if($error !== null){
                                $errors['date_affectation']=$error;
                            }
                        }
                    }
                }
                break;

            case 'regimentForm':
                //Validateur champ d'affectation' :
                //1-Not empty
                //2-Bon format yyyy-mm-jj
                //3-Concordence date
                $errors = [
                    'date_appartenance' => null,
                ];

                $validatorDateAppartenance = new Validator();
                $validatorDateAppartenance->addStrategy($validatorNotEmpty);
                $validatorDateAppartenance->addStrategy($validatorDateFormat);
                $error = $validatorDateAppartenance->applyStrategies($attributs['date_appartenance']);

                if($error !== null){
                    $errors['date_appartenance']=$error;
                } else{
                    //validation supplémentaire spéciale date
                    #syntaxe $value['0'] => date devant être plus ancienne
                    #        $value['1'] => date devant être plus récente
                    $value['0'] = $attributs['date_recrutement'];
                    $value['1'] = $attributs['date_appartenance'];
                    $validatorDateAppartenance = new Validator();
                    $validatorDateAppartenance->addStrategy($validatorCheckIfDateIsMoreRecent);
                    $error = $validatorDateAppartenance->applyStrategies($value);

                    if($error !== null){
                        $errors['date_appartenance']=$error;
                    } else{
                        //validation supplémentaire spéciale date : la date doit être plus récente que celle de l'ancien régiment s'il y en a un.
                        #syntaxe $value['0'] => date devant être plus ancienne
                        #        $value['1'] => date devant être plus récente
                        if (!empty($attributs['date_former_regiment'])){
                            $value['0'] = $attributs['date_former_regiment'];
                            $value['1'] = $attributs['date_appartenance'];
                            $validatorDateAppartenance = new Validator();
                            $validatorDateAppartenance->addStrategy($validatorCheckIfDateIsMoreRecent);
                            $error = $validatorDateAppartenance->applyStrategies($value);

                            if($error !== null){
                                $errors['date_appartenance']=$error;
                            }
                        }
                    }
                }
                break;

                case 'ajoutGradeDetenuForm':
                    //1-Not empty
                    //2-Bon format yyyy-mm-jj
                    //3-Concordence date
                    $errors = [
                        'date_promotion' => null,
                    ];

                    $validatorDateAppartenance = new Validator();
                    $validatorDateAppartenance->addStrategy($validatorNotEmpty);
                    $validatorDateAppartenance->addStrategy($validatorDateFormat);
                    $error = $validatorDateAppartenance->applyStrategies($attributs['date_promotion']);

                    if($error !== null){
                        $errors['date_promotion']=$error;
                    } else{
                        //validation supplémentaire spéciale date
                        #syntaxe $value['0'] => date devant être plus ancienne
                        #        $value['1'] => date devant être plus récente
                        $value['0'] = $attributs['date_recrutement'];
                        $value['1'] = $attributs['date_promotion'];
                        $validatorDateAppartenance = new Validator();
                        $validatorDateAppartenance->addStrategy($validatorCheckIfDateIsMoreRecent);
                        $error = $validatorDateAppartenance->applyStrategies($value);

                        if($error !== null){
                            $errors['date_promotion']=$error;
                        } else{
                            //validation supplémentaire spéciale date : la date doit être plus récente que celle de l'ancien régiment s'il y en a un.
                            #syntaxe $value['0'] => date devant être plus ancienne
                            #        $value['1'] => date devant être plus récente
                            if (!empty($attributs['date_former_grade'])){
                                $value['0'] = $attributs['date_former_grade'];
                                $value['1'] = $attributs['date_promotion'];
                                $validatorDateAppartenance = new Validator();
                                $validatorDateAppartenance->addStrategy($validatorCheckIfDateIsMoreRecent);
                                $error = $validatorDateAppartenance->applyStrategies($value);

                                if($error !== null){
                                    $errors['date_promotion']=$error;
                                }
                            }
                        }
                    }
                    break;

                case 'ajoutDiplomePossedeForm':
                    $errors = [
                        'date_obtention' => null,
                        'id' => null,
                        'pays_obtention' => null,
                        'organisme_formateur' => null
                    ];

                    // 1-Not empty
                    $validatorPaysObtention = new Validator();
                    $validatorPaysObtention->addStrategy($validatorNotEmpty);
                    $error = $validatorPaysObtention->applyStrategies($attributs['pays_obtention']);

                    if($error !== null){
                        $errors['pays_obtention']=$error;
                    }

                    //1-Not empty
                    $validatorOrganismeFormation = new Validator();
                    $validatorOrganismeFormation->addStrategy($validatorNotEmpty);
                    $error = $validatorOrganismeFormation->applyStrategies($attributs['organisme_formateur']);

                    if($error !== null){
                        $errors['organisme_formateur']=$error;
                    }

                    //1-Not empty
                    //2-Bon format yyyy-mm-jj
                    $validatorDateObtention = new Validator();
                    $validatorDateObtention->addStrategy($validatorNotEmpty);
                    $validatorDateObtention->addStrategy($validatorDateFormat);
                    $error = $validatorDateObtention->applyStrategies($attributs['date_obtention']);

                    if($error !== null){
                        $errors['date_obtention']=$error;
                    }
                    break;
        }
        return $errors;
    }
}