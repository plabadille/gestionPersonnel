<?php
namespace PLabadille\Common\Validator;

class ValidatorMaxUploadWeight implements ValidatorInterface
{
    public function validate($value){
        //retourne false si erreure.
        $maxWeight = 5242880; //valeur en octet
        $maxWeight = '5Mo'; //valeur en mo pour l'affichage de l'erreur
        return ($value < $maxWeight) ? null : 'Votre image doit etre inférieure à' . $maxWeight;
    }
} 