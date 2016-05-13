<?php
    if ( $type == 'sauvegarderDiplome' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'ajout de diplome:</h2>' . "\n";
    } elseif ( $type == 'sauvegarderEditionDiplome' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'édition de diplome:</h2>' . "\n";
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

    <label for="acronyme"><a rel="tooltip" title="Exemple : BS pour Brevet Supérieur"><img src="media/img/icons/info.png" alt="informations" /></a> Acronyme*<span class="dossierFormErrors"><?php echo $errors['acronyme']; ?></span></label>
    <input type="text" name="acronyme" id="acronyme" placeholder="Saisir l'acronyme du diplôme" value="<?php echo $attributs['acronyme']; ?>" />

    <label for="intitule"><a rel="tooltip" title="Exemple : Brevet Supérieur"><img src="media/img/icons/info.png" alt="informations" /></a> Intitulé*<span class="dossierFormErrors"><?php echo $errors['intitule']; ?></span></label>
    <input type="text" name="intitule" id="intitule" placeholder="Saisir l'intitulé du diplôme" value="<?php echo $attributs['intitule']; ?>" />

    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='oldAcronyme' value="<?php echo (isset($attributs['oldAcronyme']) ? $attributs['oldAcronyme'] : ($attributs['acronyme'] ? $attributs['acronyme'] : null)); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />

</form>