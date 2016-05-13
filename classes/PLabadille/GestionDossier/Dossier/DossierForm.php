<?php
namespace PLabadille\GestionDossier\Dossier;
use PLabadille\Common\Cleaner\Cleaner;
use PLabadille\Common\Cleaner\CleanerTrim;
use PLabadille\Common\Cleaner\CleanerHtmlTags;
use PLabadille\Common\Cleaner\CleanerPhoneNumber;
use PLabadille\Common\Validator\Validator;
use PLabadille\Common\Validator\ValidatorNotEmpty;
use PLabadille\Common\Validator\ValidatorEmail;
use PLabadille\Common\Validator\ValidatorMinLength;
use PLabadille\Common\Validator\ValidatorDateFormat;
use PLabadille\Common\Validator\ValidatorPhoneNumberFormat;
use PLabadille\Common\Validator\ValidatorCheckIfDateIsMoreRecent;
use PLabadille\Common\Validator\ValidatorCheckIfDateIsMoreRecentAndReturnDiff;
use PLabadille\Common\Validator\ValidatorIsNumber;
use PLabadille\Common\Validator\ValidatorNoNumbers;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions génériques
# 1- Module mon dossier
# 2- Module de gestion et ajout de dossier
# 3- Module de gestion de promotion et retraite
//--------------------

#Gère le traitement des formulaires (affichage via template)
#Ainsi que les stratégies de validation et de nettoyage.
class DossierForm 
{
    protected $dossier;
    public function __construct($dossier = null) {
        $this->dossier = $dossier;
    }

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
        $validator_Email = new ValidatorEmail();
        $validatorDateFormat = new ValidatorDateFormat();
        $validatorPhoneNumberFormat = new ValidatorPhoneNumberFormat();
        $validatorCheckIfDateIsMoreRecent = new ValidatorCheckIfDateIsMoreRecent();
        $validatorCheckIfDateIsMoreRecentAndReturnDiff = new ValidatorCheckIfDateIsMoreRecentAndReturnDiff();
        $validatorIsNumber = new ValidatorIsNumber();
        $validatorMinLength = new ValidatorMinLength();
        $validatorNoNumbers = new ValidatorNoNumbers();

        #switch selon le type de formulaire.
        switch ($type) {
            case 'sonDossierForm':
                //initialisation du tableau d'erreur pour éviter les undefined index
                //dans le form.
                $errors = [
                    'tel1' => null,
                    'tel2' => null,
                    'email' => null,
                    'adresse' => null,
                ];

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
                //2-format téléphone (si non vide)
                $validatorTel2 = new Validator();
                if (!empty($attributs['tel2'])){ //optionnel, si non vide doit correspondre au format
                    $validatorTel2->addStrategy($validatorPhoneNumberFormat);
                    $error = $validatorTel2->applyStrategies($attributs['tel2']);

                    if($error !== null){
                        $errors['tel2']=$error;
                    }
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
                //2-Min length
                $validatorAdresse = new Validator();
                $validatorAdresse->addStrategy($validatorNotEmpty);
                $validatorAdresse->addStrategy($validatorMinLength);
                $error = $validatorAdresse->applyStrategies($attributs['adresse']);

                if($error !== null){
                    $errors['adresse']=$error;
                }
                break;
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
                // 2-No numbers
                $validatorNom = new Validator();
                $validatorNom->addStrategy($validatorNotEmpty);
                $validatorNom->addStrategy($validatorNoNumbers);
                $error = $validatorNom->applyStrategies($attributs['nom']);

                if($error !== null){
                    $errors['nom']=$error;
                }
                //Validateur champ Prenom :
                //1-Not empty
                //2-No numbers
                $validatorPrenom = new Validator();
                $validatorPrenom->addStrategy($validatorNotEmpty);
                $validatorPrenom->addStrategy($validatorNoNumbers);
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
                //1-format téléphone
                 $validatorTel2 = new Validator();
                if (!empty($attributs['tel2'])){ //optionnel, si non vide doit correspondre au format
                    $validatorTel2->addStrategy($validatorPhoneNumberFormat);
                    $error = $validatorTel2->applyStrategies($attributs['tel2']);

                    if($error !== null){
                        $errors['tel2']=$error;
                    }
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
                //2-Min Length
                $validatorAdresse = new Validator();
                $validatorAdresse->addStrategy($validatorNotEmpty);
                $validatorAdresse->addStrategy($validatorMinLength);
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
                    $validatorDateRecrutement->addStrategy($validatorCheckIfDateIsMoreRecentAndReturnDiff);
                    $diff = $validatorDateRecrutement->applyStrategies($value);
                    if(is_numeric($diff)){
                        if ($diff < 18){
                            $error = "<br>*La date saisie n'est pas possible, l'individu n'est pas majeur ($diff ans)";
                        }
                    } else{
                        $error = $diff;
                    }
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
                //vérification du champ d'autocompletion (présence en base requise)
                $explode = explode(' ', $attributs['caserneId']);
                $caserneId = DossierManager::getCaserneById($explode['0']);
                $errors['caserneId'] = (!empty($caserneId) ? null : 'la caserne saisie n\'existe pas, veuillez vous servir de l\'autocompletion' );
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
                //vérification du champ d'autocompletion (présence en base requise)
                $regimentId = DossierManager::getRegimentById($attributs['regimentId']);
                $errors['regimentId'] = (!empty($regimentId) ? null : 'le régiment saisi n\'existe pas, veuillez vous servir de l\'autocompletion' );
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
                    //vérification du champ d'autocompletion (présence en base requise)
                    $explode = explode(' ', $attributs['gradeId']);
                    $gradeId = DossierManager::getGradeById($explode['0']);
                    $errors['gradeId'] = (!empty($gradeId) ? null : 'le grade saisi n\'existe pas, veuillez vous servir de l\'autocompletion' );
                    break;

                case 'ajoutDiplomePossedeForm':
                    $errors = [
                        'date_obtention' => null,
                        'id' => null,
                        'pays_obtention' => null,
                        'organisme_formateur' => null
                    ];

                    // 1-Not empty
                    // 2-Not number
                    $validatorPaysObtention = new Validator();
                    $validatorPaysObtention->addStrategy($validatorNotEmpty);
                    $validatorPaysObtention->addStrategy($validatorNoNumbers);
                    $error = $validatorPaysObtention->applyStrategies($attributs['pays_obtention']);

                    if($error !== null){
                        $errors['pays_obtention']=$error;
                    }

                    //1-Not empty
                    // 2-Not number
                    $validatorOrganismeFormation = new Validator();
                    $validatorOrganismeFormation->addStrategy($validatorNotEmpty);
                    $validatorOrganismeFormation->addStrategy($validatorNoNumbers);
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
                    //vérification du champ d'autocompletion (présence en base requise)
                    var_dump($attributs['diplomeId']);
                    $explode = explode(' ', $attributs['diplomeId']);
                    $diplomeId = DossierManager::getDiplomeById($explode['0']);
                    $errors['diplomeId'] = (!empty($diplomeId) ? null : 'le diplome saisi n\'existe pas, veuillez vous servir de l\'autocompletion' );
                    break;
                case 'archivageForm':
                    //initialisation du tableau d'erreur pour éviter les undefined index
                    //dans le form.
                    $errors = [
                        'date_deces' => null,
                        'cause_deces' => null
                    ];

                    //1-Not empty
                    //2-Bon format yyyy-mm-jj
                    $validatorDateDeces = new Validator();
                    $validatorDateDeces->addStrategy($validatorNotEmpty);
                    $validatorDateDeces->addStrategy($validatorDateFormat);
                    $error = $validatorDateDeces->applyStrategies($attributs['date_deces']);

                    if($error !== null){
                        $errors['date_deces']=$error;
                    }

                    //Validateur champ cause :
                    //1-Not empty
                    $validatorCauseDeces = new Validator();
                    $validatorCauseDeces->addStrategy($validatorNotEmpty);
                    $error = $validatorCauseDeces->applyStrategies($attributs['cause_deces']);

                    if($error !== null){
                        $errors['cause_deces']=$error;
                    }
                    break;
                case 'retraiteForm':
                    //initialisation du tableau d'erreur pour éviter les undefined index
                    //dans le form.
                    $errors = [
                        'date_retraite' => null
                    ];

                    //1-Not empty
                    //2-Bon format yyyy-mm-jj
                    $validatorDateRetraite = new Validator();
                    $validatorDateRetraite->addStrategy($validatorNotEmpty);
                    $validatorDateRetraite->addStrategy($validatorDateFormat);
                    $error = $validatorDateRetraite->applyStrategies($attributs['date_retraite']);

                    if($error !== null){
                        $errors['date_dretraite']=$error;
                    }

                    break;
                case 'editConditionRetraite':
                case 'ajouterConditionRetraite':
                    //initialisation du tableau d'erreur pour éviter les undefined index
                    //dans le form.
                    $errors = [
                        'idGrade' => null,
                        'service_effectif' => null,
                        'age' => null
                    ];

                    //1-Not empty
                    $validatorIdGrade = new Validator();
                    $validatorIdGrade->addStrategy($validatorNotEmpty);
                    $error = $validatorIdGrade->applyStrategies($attributs['idGrade']);

                    if($error !== null){
                        $errors['idGrade']=$error;
                    }

                    //1-Not empty
                    $validatorServiceEffectif = new Validator();
                    $validatorServiceEffectif->addStrategy($validatorNotEmpty);
                    $error = $validatorServiceEffectif->applyStrategies($attributs['service_effectif']);

                    if($error !== null){
                        $errors['service_effectif']=$error;
                    }

                    //1-Not empty
                    //2-Is number
                    $validatorAge = new Validator();
                    $validatorAge->addStrategy($validatorNotEmpty);
                    $validatorAge->addStrategy($validatorIsNumber);
                    $error = $validatorAge->applyStrategies($attributs['age']);

                    if($error !== null){
                        $errors['age']=$error;
                    }

                    break;
                case 'editConditionPromotion':
                case 'ajouterConditionPromotion':
                    //initialisation du tableau d'erreur pour éviter les undefined index
                    //dans le form.
                    $errors = [
                        'idGrade' => null,
                        'annees_service_FA' => null,
                        'annees_service_GN' => null,
                        'annees_service_SOE' => null,
                        'annees_service_grade' => null
                    ];

                    //1-Not empty
                    $validatorIdGrade = new Validator();
                    $validatorIdGrade->addStrategy($validatorNotEmpty);
                    $error = $validatorIdGrade->applyStrategies($attributs['idGrade']);

                    if($error !== null){
                        $errors['idGrade']=$error;
                    }

                    //2-Is number
                    $validatorServiceFA = new Validator();
                    $validatorServiceFA->addStrategy($validatorIsNumber);
                    $error = $validatorServiceFA->applyStrategies($attributs['annees_service_FA']);

                    if($error !== null){
                        $errors['annees_service_FA']=$error;
                    }

                    //2-Is number
                    $validatorServiceSOE = new Validator();
                    $validatorServiceSOE->addStrategy($validatorIsNumber);
                    $error = $validatorServiceSOE->applyStrategies($attributs['annees_service_SOE']);

                    if($error !== null){
                        $errors['annees_service_SOE']=$error;
                    }

                    //2-Is number
                    $validatorServiceGN = new Validator();
                    $validatorServiceGN->addStrategy($validatorIsNumber);
                    $error = $validatorServiceGN->applyStrategies($attributs['annees_service_GN']);

                    if($error !== null){
                        $errors['annees_service_GN']=$error;
                    }

                    //2-Is number
                    $validatorServiceGrade = new Validator();
                    $validatorServiceGrade->addStrategy($validatorIsNumber);
                    $error = $validatorServiceGrade->applyStrategies($attributs['annees_service_grade']);

                    if($error !== null){
                        $errors['annees_service_grade']=$error;
                    }

                    break;
        }
        return $errors;
    }

    //--------------------
    //1-module mon dossier
    //--------------------
    // 1-2- 'editOwnFolderPersonalInformation':
    public function traitementFormulaireSonDossier($type, $attributs, $errors = null)
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formPropreDossier.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    //--------------------
    //2-module gestion et ajout de dossier
    //--------------------
    // 2-5 'createFolder' & 2-8- 'editInformation':

    public function traitementFormulaireMilitaire($type, $attributs = null, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formMilitaire.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    // 2-6- 'addElementToAFolder':

    public function traitementFormulaireAffectation($type, $attributs, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAffectation.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireAppartientRegiment($type, $attributs, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAppartenanceRegiment.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireGradeDetenu($type, $attributs, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAjoutGradeDetenu.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireDiplomePossede($type, $attributs, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formAjoutDiplomePossede.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireArchiveDossier($type, $attributs = null, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formArchiverUnDossier.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireRetraiterDossier($type, $attributs = null, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formRetraiterUnDossier.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireConditionPromotion($type, $attributs, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formConditionPromotion.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }

    public function traitementFormulaireConditionRetraite($type, $attributs, $errors = null) 
    {
        ob_start();
        include_once 'classes/PLabadille/GestionDossier/Dossier/view/formConditionRetraite.php';
        $prez = ob_get_contents();
        ob_end_clean();
        return $prez;
    }
    
    

    // 2-7- 'editInformationIfAuthor':
    #to do
    
    // 2-8- 'editInformation':
    #Géré directement dans la fonction 2-5-CreateFolder

    // 2-10 'useFileToAddFolders':
    #to do

    //--------------------
    //3-module gestion promotion et retraite
    //--------------------

    // 3-2- 'editEligibleCondition':
    #to do

    // 3-3- 'addEligibleCondition':
    #to do

    // 3-5- 'editEligibleEmailContent':
    #to do

    // 3-6- 'uploadFileForMail':
    #to do

    // 3-7- 'changePieceJointeForEligibleMail':
    #to do
}