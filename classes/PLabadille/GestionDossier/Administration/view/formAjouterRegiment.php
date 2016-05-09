<?php
    if ( $type == 'sauvegarderRegiment' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'ajout de regiment:</h2>' . "\n";
    } elseif ( $type == 'sauvegarderEditionRegiment' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'édition de regiment:</h2>' . "\n";
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

    <label for="id">Acronyme<span class="dossierFormErrors"><?php echo $errors['id']; ?></span></label>
    <input type="text" name="id" id="id" placeholder="Saisir le nom du régiment" value="<?php echo $attributs['id']; ?>" />

    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='oldId' value="<?php echo (isset($attributs['oldId']) ? $attributs['oldId'] : ($attributs['id'] ? $attributs['id'] : null)); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />

</form>