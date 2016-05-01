<?php

namespace PLabadille\GestionDossier\Controller;

use PLabadille\Common\Authentication\AuthenticationManager;

/**
* \author Pierre Labadille
* \namespace PLabadille\MiniJournal\Controller
* \class AccessControll
* \brief Classe hybride gérant l'affichage du menu (selon les droits) et la gestion des droits
*/
class AccessControll
{
	/**
    * \fn public static function afficherNavigation()
    * \brief Génére le code HTML du menu de navigation en fonction des droits de l'utilisateur
    * \return $html string Contenant le code HTML de l'affichage du menu de navigation
    */
	//classe de gestion des droits et des affichages relatifs : gestions des erreurs, de la navigation...
	public static function afficherNavigation()
    {	//Init navigation par module
    	$module1 = '';
    	$module2 = '';
    	$module3 = '';
        $module4 = '';

    	$html = '';
    	//gère l'affichage du menu en fonction du statut de l'utilisateur.
        $auth = AuthenticationManager::getInstance();
        if ($auth->isConnected()) {
        	$droits = $auth->getDroits();
        	//1 = true 0 = false
        	//on génère l'affichage du menu en fonction des droits.
        	if ( $droits['noRights'] == 0 && $droits['allRights'] == 0 ){
        		//il faut regarder le détail des droits de l'utilisateur
        		foreach ($droits as $key => $value) {
        			//si la clef n'est pas true on ne s'y interesse pas.
        			if ( $value == 1 ){
	        			switch ($key) {
	        				//--------------------
	        				//1-module mon dossier
	        				//--------------------
	        				case 'seeOwnFolderModule':
	        					$module1 .= '<li><a href="?objet=dossier&action=afficherSonDossier">Afficher son dossier</a></li>';
	        					break;
	        				case 'editOwnFolderPersonalInformation':
	        					$module1 .= '<li><a href="?objet=dossier&action=editerSonDossier">Editer son dossier</a></li>';
	        					break;
	        				//--------------------
	        				//2-module gestion et ajout de dossier
	        				//--------------------
	        				case 'listCreatedFolder':
	        					$module2 .= '<li><a href="?objet=dossier&action=afficherListeDossierSiCreateur">Afficher liste des militaires créés</a></li>';
	        					break;
	        				case 'listAllFolder':
	        					$module2 .= '<li><a href="?objet=dossier&action=afficherListeDossier">Afficher liste des militaires</a></li>';
	        					break;
	        				case 'createFolder':
	        					$module2 .= '<li><a href="?objet=dossier&action=creerDossier">Créer un dossier</a></li>';
	        					break;
	        				// case 'useFileToAddFolders':
	        				// 	$html .='';
	        				// 	break;
	        				//--------------------
	        				//3-module gestion promotion et retraite
	        				//--------------------
	        				case 'listEligible':
	        					$module3 .= '<li><a href="?objet=dossier&action=afficherListeEligiblePromotion">Afficher militaires éligible promotion</a></li>';
            					$module3 .= '<li><a href="?objet=dossier&action=afficherListeEligibleRetraite">Afficher militaires éligible retraite</a></li>';
                                $module3 .= '<li><a href="?objet=dossier&action=afficherListeConditionsEligibilites">Afficher les conditions d\'éligibilités</a></li>';
	        					break;
	        				case 'addEligibleCondition':
	        					$module3 .= '<li><a href="?objet=dossier&action=ajouterConditionRetraite">Ajouter conditions d\'éligibilité retraite</a></li>';
                                $module3 .= '<li><a href="?objet=dossier&action=ajouterConditionPromotion">Ajouter conditions d\'éligibilité promotion</a></li>';
	        					break;
	        				// case 'editEligibleEmailContent':
	        				// 	$html .='';
	        				// 	break;
	        				// case 'uploadFileForMail':
	        				// 	$html .='';
	        				// 	break;
	        				// case 'changePieceJointeForEligibleMail':
	        				// 	$html .='';
	        				// 	break;
	        				//--------------------
	        				//4-module creation de compte et droit
	        				//--------------------
	        				case 'seeAllFolderWithoutAccount':
	        					$module4 .= '<li><a href="?objet=administration&action=afficherListeDossierSansCompte">Afficher les dossiers sans compte utilisateur</a></li>';
	        					break;
	        				case 'seeAllAccount':
	        					$module4 .= '<li><a href="?objet=administration&action=afficherListeDossierSansCompte">Afficher tous les comptes utilisateurs</a></li>';
	        					break;
	        				case 'createAccount':
	        					$module4 .= '<li><a href="?objet=administration&action=creerCompteUtilisateur">Créer un compte utilisateur</a></li>';
                                break;
	        				//--------------------
	        				//5-module gestion de l'application
	        				//--------------------
	        				// case 'seeAllConstanteTable':
	        				// 	$html .='';
	        				// 	break;
	        				// case 'editInAConstanteTable':
	        				// 	$html .='';
	        				// 	break;
	        				// case 'deleteInAConstanteTable':
	        				// 	$html .='';
	        				// 	break;
	        				//--------------------
	        				//6-module de sauvegarde et de gestion de crise
	        				//--------------------
	        				//seul les droits allRights le permettent donc on ne le remet pas ici	
	        			}
        			}
        		}
        	} elseif ( $droits['allRights'] == 1 ){
        		//on affiche tous les menus disponibles sans passer par une vérification champ par champ car l'utilisateur à tous les droits.
        		$module1 .= '<li><a href="?objet=dossier&action=afficherSonDossier">Afficher son dossier</a></li>';
        		$module1 .= '<li><a href="?objet=dossier&action=editerSonDossier">Editer son dossier</a></li>';
        		$module2 .= '<li><a href="?objet=dossier&action=afficherListeDossier">Afficher liste des militaires</a></li>';
        		$module2 .= '<li><a href="?objet=dossier&action=afficherListeDossierSiCreateur">Afficher liste des militaires créés</a></li>';
            	$module2 .= '<li><a href="?objet=dossier&action=creerDossier">Créer un dossier</a></li>';
            	$module3 .= '<li><a href="?objet=dossier&action=afficherListeEligiblePromotion">Afficher militaires éligible promotion</a></li>';
            	$module3 .= '<li><a href="?objet=dossier&action=afficherListeEligibleRetraite">Afficher militaires éligible retraite</a></li>';
				$module3 .= '<li><a href="?objet=dossier&action=afficherListeConditionsEligibilites">Afficher les conditions d\'éligibilités</a></li>';
				$module3 .= '<li><a href="?objet=dossier&action=ajouterConditionRetraite">Ajouter conditions d\'éligibilité retraite</a></li>';
                $module3 .= '<li><a href="?objet=dossier&action=ajouterConditionPromotion">Ajouter conditions d\'éligibilité promotion</a></li>';
                $module4 .= '<li><a href="?objet=administration&action=afficherListeDossierSansCompte">Afficher les dossiers sans compte utilisateur</a></li>';
                $module4 .= '<li><a href="?objet=administration&action=afficherListeDossierSansCompte">Afficher tous les comptes utilisateurs</a></li>';
                $module4 .= '<li><a href="?objet=administration&action=creerCompteUtilisateur">Créer un compte utilisateur</a></li>';
        	} else{
        		//ici les droits ont été passé à noRights, situation d'urgence enclenchée.
        		$html = '<p>système hors ligne, veuillez vous déconnecter</p>';
        	}
        }

        //Nom module navigation et affichage si non vide
        if ( !empty($module1) ){
        	$nameModule = '<div class="moduleConteneur"><h3>Module mon dossier</h3><ul class="module">';
        	$html .= $nameModule . $module1 . '</ul></div>';
        }
        if ( !empty($module2) ){
        	$nameModule = '<div class="moduleConteneur"><h3>Module gestion et ajout de dossier</h3><ul class="module">';
        	$html .= $nameModule . $module2 . '</ul></div>';
        }
        if ( !empty($module3) ){
        	$nameModule = '<div class="moduleConteneur"><h3>Module gestion promotion et retraite</h3><ul class="module">';
        	$html .= $nameModule . $module3 . '</ul></div>';
        }
        if ( !empty($module4) ){
            $nameModule = '<div class="moduleConteneur"><h3>Module de création de compte et de droit</h3><ul class="module">';
            $html .= $nameModule . $module4 . '</ul></div>';
        }

        return $html;
    }

    public static function afficherBoutonNavigation($typeBouton, $creatorIsLog = null)
    {
    	//gère l'affichage du menu en fonction du statut de l'utilisateur.
    	//retourne true si l'utilisateur à le droit de voir le bouton, false sinon.
        $auth = AuthenticationManager::getInstance();
        if ($auth->isConnected()) {
        	$droits = $auth->getDroits();
        	//1 = true 0 = false
        	//on génère l'affichage du menu en fonction des droits.
        	if ( $droits['noRights'] == 0 ){
        		if ( $droits['allRights'] == 1 ){
        			return true;
        		}
        		//on regarde si l'utilisateur doit voir le bouton de menu
    			switch ($typeBouton) {
    				//--------------------
    				//1-module mon dossier
    				//--------------------
    				#pas de bouton
    				//--------------------
    				//2-module gestion et ajout de dossier
    				//--------------------
    				case 'addElementToAFolder':
    					if ( $droits['addElementToAFolder'] == 1 || ( $droits['addElementToAFolderCreated'] == 1 && $creatorIsLog ) ){
    						return true;
    					}
    					break;
    				case 'editFolderInformation':
    					if ( $droits['editInformation'] == 1 || ( $droits['editInformationIfAuthor'] == 1 && $creatorIsLog ) ){
    						return true;
    					}
    					break;
    				case 'deleteFolderInformation':
    					if ( $droits['deleteInformation'] == 1 || $droits['allRights'] == 1 ){
    						return true;
    					}
    					break;
    					case 'canArchiveAFolder':
    					if ( $droits['canArchiveAFolder'] == 1 || $droits['allRights'] == 1 ){
    						return true;
    					}
    					break;
    				//--------------------
    				//3-module gestion promotion et retraite
    				//--------------------
    				case 'canRetireAFolder':
    					if ( $droits['canRetireAFolder'] == 1 || $droits['allRights'] == 1 ){
    						return true;
    					}
    					break;
                    case 'editEligibleCondition':
                        if ( $droits['editEligibleCondition'] == 1 || $droits['allRights'] == 1 ){
                             return true;
                            }
                        break;
                    case 'suprEligibleCondition':
                        if ( $droits['suprEligibleCondition'] == 1 || $droits['allRights'] == 1 ){
                            return true;
                        }
                        break;
    				//--------------------
    				//4-module creation de compte et droit
    				//--------------------
    				// case 'alterMdp':
    				// 	if ( $droits['alterMdp'] == 1 || $droits['allRights'] == 1 ){
    					// 	return true;
    					// }
    				// 	break;
    				// case 'alterAccountRight':
    				// 	if ( $droits['alterAccountRight'] == 1 || $droits['allRights'] == 1 ){
    					// 	return true;
    					// }
    				// 	break;
    				//--------------------
    				//5-module gestion de l'application
    				//--------------------
    				// case 'editInAConstanteTable':
    				// 	if ( $droits['editInAConstanteTable'] == 1 || $droits['allRights'] == 1 ){
    					// 	return true;
    					// }
    				// 	break;
    				// case 'deleteInAConstanteTable':
    				// 	if ( $droits['deleteInAConstanteTable'] == 1 || $droits['allRights'] == 1 ){
    					// 	return true;
    					// }
    				// 	break;
    				//--------------------
    				//6-module de sauvegarde et de gestion de crise
    				//--------------------
    				//seul les droits allRights le permettent donc on ne le remet pas ici	
    			}
        	} else{
        		return false;
        	}
        }
    }
    
    /**
    * \fn public static function checkIfConnectedIsAuthor($auteur)
    * \brief Compare le nom de l'auteur d'un article au Login de la personne connecté
    * \param $auteur string contient le nom de l'auteur d'un article
    * \return $articleAuthorIsLog bool Retourne true si une personne est l'auteur d'un article, sinon false.
    */
    public static function checkIfConnectedIsAuthor($createBy)
    {
    	$auth = AuthenticationManager::getInstance();
		if ($auth->isConnected()){
			$username = $auth->getMatricule();
			if ($username == $createBy){
				$creatorIsLog = true;
			} else{
				$creatorIsLog = false;
			}
		} else{
			$creatorIsLog = false;
		}
		return $creatorIsLog;
    }

    /**
    * \fn public static function checkRight($action, $articleAuthorIsLog=null)
    * \brief Verifie si un utilisateur a le droit d'effectuer une action
    * \param $action string contient le nom de l'action que l'utilisateur veut effectuer
    * \param $articleAuthorIsLog bool, peut être null, contient true si l'utilisateur est l'auteur d'un article
    * \return null si pas d'erreur et $erreur array si l'utilisateur n'a pas les droits requis pour effectuer une action
    */
	public static function checkRight($action, $creatorIsLog=null)
	{
		//gère les droits d'execution des fonctions (niveau controller).
    	//retourne une erreur si l'utilisateur n'a pas les droits requis.
    	//securité supp en cas d'url directe ou de problème au niveau des générateurs de lien selon les droits.
    	$error = null;
        $auth = AuthenticationManager::getInstance();
        if ($auth->isConnected()) {
        	$droits = $auth->getDroits();
        	//cas ne nécessitant pas de vérification
        	if ( $droits['noRights'] == 1 || $droits['allRights'] == 1 ){
        		if ( $droits['allRights'] == 1 ){ //tous les droits
					return null;
				} else{ //aucun droit (situation d'urgence)
					header("Location: index.php");
					die('Maintenance en cours, veuillez vous connecter plus tard.'); 
				}
        	} else{ //on doit passer par une vérification
        		//fonctionne par erreur et non autorisation.
				//on regarde donc si la personne essayant d'accéder à la fonction n'a pas le statut qu'il faut.
				switch ($action) {
					//--------------------
					//1-module mon dossier
					//--------------------
					case 'seeOwnFolderModule':
						if ( $droits['seeOwnFolderModule'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'editOwnFolderPersonalInformation':
						if ( $droits['editOwnFolderPersonalInformation'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					//--------------------
					//2-module gestion et ajout de dossier
					//--------------------
					case 'listCreatedFolder':
						if ( $droits['listCreatedFolder'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'listAllFolder':
						if ( $droits['listAllFolder'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'seeAllFolder':
						if ( $droits['seeAllFolder'] == 0 ){
							if ( $droits['seeCreatedFolder'] == 1 && $creatorIsLog ){
								break;
							} else{
								return 'Vous n\'avez pas les droits requis pour effectuer cette action';
							}
						}
						break;
					case 'createFolder':
						if ( $droits['createFolder'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'addElementToAFolder':
						if ( $droits['addElementToAFolder'] == 0 ){
							if ( $droits['addElementToAFolderCreated'] == 1 && $creatorIsLog ){

								break;
							} else{
								return 'Vous n\'avez pas les droits requis pour effectuer cette action';
							}
						}
						break;
					case 'editInformation':
						if ( $droits['editInformation'] == 0  ){
							if ( $droits['editInformationIfAuthor'] == 1 && $creatorIsLog ){
								break;
							} else{
								return 'Vous n\'avez pas les droits requis pour effectuer cette action';
							}
						}
						break;
					case 'deleteInformation':
						if ( $droits['deleteInformation'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'canArchiveAFolder':
						if ( $droits['canArchiveAFolder'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					// case 'useFileToAddFolders':
					// 	$html .='';
					// 	break;
					//--------------------
					//3-module gestion promotion et retraite
					//--------------------
					case 'listEligible':
						if ( $droits['listEligible'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'editEligibleCondition':
						if ( $droits['editEligibleCondition'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					case 'addEligibleCondition':
						if ( $droits['addEligibleCondition'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
                    case 'suprEligibleCondition':
                        if ( $droits['suprEligibleCondition'] == 0 ){
                            return 'Vous n\'avez pas les droits requis pour effectuer cette action';
                        }
                        break;
					case 'canRetireAFolder':
						if ( $droits['canRetireAFolder'] == 0 ){
							return 'Vous n\'avez pas les droits requis pour effectuer cette action';
						}
						break;
					// case 'editEligibleEmailContent':
					// 	$html .='';
					// 	break;
					// case 'uploadFileForMail':
					// 	$html .='';
					// 	break;
					// case 'changePieceJointeForEligibleMail':
					// 	$html .='';
					// 	break;
					//--------------------
					//4-module creation de compte et droit
					//--------------------
					// case 'seeAllFolderWithoutAccount':
					// 	$html .='';
					// 	break;
					// case 'seeAllAccount':
					// 	$html .='';
					// 	break;
					// case 'createAccount':
					// 	$html .='';
					// 	break;
					// case 'alterMdp':
					// 	$html .='';
					// 	break;
					// case 'alterAccountRight':
					// 	$html .='';
					// 	break;
					//--------------------
					//5-module gestion de l'application
					//--------------------
					// case 'seeAllConstanteTable':
					// 	$html .='';
					// 	break;
					// case 'editInAConstanteTable':
					// 	$html .='';
					// 	break;
					// case 'deleteInAConstanteTable':
					// 	$html .='';
					// 	break;
					//--------------------
					//6-module de sauvegarde et de gestion de crise
					//--------------------
					//seul les droits allRights le permettent donc on ne le remet pas ici	
				}
				//Si le tableau d'erreur est vide on ne retourne rien et l'action peut s'effectuer.
				//Sinon on retourne l'erreur.
				return ( empty($error) ? null : $error );
			} 
		} else{
			//un utilisateur non connecté tente d'accéder à des fonctions par URL
			//normalement impossible (car sur une autre template). Si jamais
			header("Location: index.php");
			die('Vous devez être connecté');
		}

		//Si on arrive jusqu'ici c'est qu'il n'y a pas eu d'erreur, on ne retourne donc rien.
		return null;
	}
}


