<?php
    if ( $type == 'sauvegarderCaserne' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'ajout de caserne:</h2>' . "\n";
    } elseif ( $type == 'sauvegarderEditionCaserne' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'édition de caserne:</h2>' . "\n";
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

    <label for="nom">Nom*<span class="dossierFormErrors"><?php echo $errors['nom']; ?></span></label>
    <input type="text" name="nom" id="nom" placeholder="Saisir le nom de la caserne" value="<?php echo $attributs['nom']; ?>" />

    <label for="adresse">Adresse*<span class="dossierFormErrors"><?php echo $errors['adresse']; ?></span></label>
    <input type="text" name="adresse" id="adresse" placeholder="Saisir l'adresse de la caserne" value="<?php echo $attributs['adresse']; ?>" />

    <label for="tel_standard">Téléphone du standard*<span class="dossierFormErrors"><?php echo $errors['tel_standard']; ?></span></label>
    <input type="text" name="tel_standard" id="tel_standard" placeholder="Saisir le numéro de téléphone du standard" value="<?php echo $attributs['tel_standard']; ?>" />

    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo $attributs['id'] ? $attributs['id'] : null; ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
    
</form>