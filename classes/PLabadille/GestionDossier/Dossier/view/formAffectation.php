<h2 class="titreFormulaire">Formulaire d'ajout d'affectation:</h2>
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
    <label for="date_affectation">Date d'affectation<span class="dossierFormErrors"><?php echo $errors['date_affectation']; ?></span></label>
    <input type="text" name="date_affectation" class="datepicker" value="<?php echo (isset($attributs['date_affectation']) ? $attributs['date_affectation'] : null); ?>" />

    <label for="caserneId">Nom de la caserne:</label><br />
    <input type="text" name="caserneId" autocomplete="off" id="searchCaserne" value="<?php echo (isset($attributs['caserneId']) ? $attributs['caserneId'] . ' : ' . $attributs['listeCaserne'][$attributs['caserneId']] : null); ?>" />
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>