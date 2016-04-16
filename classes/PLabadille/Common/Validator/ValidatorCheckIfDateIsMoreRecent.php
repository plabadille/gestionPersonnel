<?php
namespace PLabadille\Common\Validator;

class ValidatorCheckIfDateIsMoreRecent implements ValidatorInterface
{
	#Syntaxe :
	#$value['0'] -> date qui doit être plus ancienne
	#$value['1'] -> date qui doit être plus récente
    public function validate($value){
        //retourne false si erreure.
    	/*on "découpe" les dates de façon à obtenir un tableau de 3 lignes : 2=>jours, 1=>mois, 0=>années*/
        $dateDebut=explode("-",$value['0']);
        $dateFin=explode("-",$value['1']);
        /*A partir de ce tableau, on reconstitue le timestamp grâce à la fonction mktime*/
        $dateDebut=mktime(0,0,0,$dateDebut[1],$dateDebut[2],$dateDebut[0]);
        $dateFin=mktime(0,0,0,$dateFin[1],$dateFin[2],$dateFin[0]);
        /*On soustrait les deux dates et on obtient le nombre de secondes écoulé*/
        $d=$dateFin-$dateDebut;
        #si le résultat est négatif c'est que la condition n'est pas respecté
        return ($d >= 0) ? null : "<br>*La date saisie n'est pas possible, vérifié la bonne concordence avec une autre date liée (naissance, recrutement, etc)";
    }
} 