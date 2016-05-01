<h2 class="titreFormulaire">Formulaire d'ajout d'appartenance à une régiment:</h2>
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
<form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=<?php echo $type ; ?>">
    <label for="date_appartenance">Date d'appartenance:<span class="dossierFormErrors"><?php echo $errors['date_appartenance']; ?></span></label>
    <input type="text" name="date_appartenance" class="datepicker" value="<?php echo (isset($attributs['date_appartenance']) ? $attributs['date_appartenance'] : null); ?>" />

    <label for="regimentId">Nom du régiment:</label><br />
    <input type="text" name="regimentId" autocomplete="off" id="searchRegiment" value="<?php echo (isset($attributs['regimentId']) ? $attributs['regimentId'] : null); ?>" />
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>