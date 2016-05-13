<?php
    if ( $type == 'sauvegarderGrade' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'ajout de grade:</h2>' . "\n";
    } elseif ( $type == 'sauvegarderEditionGrade' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'édition de grade:</h2>' . "\n";
    }
?>
<div id="formErrorsExist">
    <?php
        if (is_array($errors)){
            $cleanErrors = array_filter($errors); 
            if ($cleanErrors !== null){
                echo '<div id="errorFieldDossierForm"><p>Il existe des erreurs dans la saisie du formulaire, voir ci-dessous :</p>';
                if (isset($errors['doublon'])){
                    echo '<p>' . $errors['doublon'] . '</p>';
                }
                echo '</div>';
            }
        } 
    ?>
</div>

<form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=<?php echo $type ; ?>">

    <label for="grade">Nom du grade*<span class="dossierFormErrors"><?php echo (isset($errors['grade']) ? $errors['grade'] : null); ?></span></label>
    <input type="text" name="grade" id="grade" placeholder="Saisir le nom de la classe" value="<?php echo (isset($attributs['grade']) ? $attributs['grade'] : null); ?>" />

    <label for="hierarchie"><a rel="tooltip" title="Selectionnez le grade équivalent dans la liste."><img src="media/img/icons/info.png" alt="informations" /></a> Selectionnez l'équivalence du grade en terme de hierarchie*</label>
    <select name="hierarchie" id="hierarchie">
    <?php
        foreach ($attributs['listeGrade'] as $grade) {
            if (isset($attributs['hierarchie'])){
                if($attributs['hierarchie'] == $grade['hierarchie']){//si on connait le rôle déjà défini (édition etc)
                   echo '<option value="' . $grade['hierarchie'] . '" selected>' . $grade['hierarchie'] . ' : ' . $grade['grade'] . '</option>';
                } else{
                    echo '<option value="' . $grade['hierarchie'] . '">' . $grade['hierarchie'] . ' : ' . $grade['grade'] . '</option>';
                }
            } else{
                echo '<option value="' . $grade['hierarchie'] . '">' . $grade['hierarchie'] . ' : ' . $grade['grade'] . '</option>'; 
            }   
        }        
    ?>
   </select>

   <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />

</form>