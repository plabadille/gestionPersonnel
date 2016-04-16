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
<form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="index.php?action=<?php echo $type ; ?>">
    <label for="date_affectation">Date d'affectation<span class="dossierFormErrors"><?php echo $errors['date_affectation']; ?></span></label>
    <input type="text" name="date_affectation" class="datepicker" value="<?php echo (isset($attributs['date_affectation']) ? $attributs['date_affectation'] : null); ?>" />

    <label for="caserneId">Nom de la caserne:</label><br />
    <select name="caserneId" id="caserneId">
    <?php
        foreach ($attributs['listeCaserne'] as $id => $nom) {
            if (isset($attributs['caserneId'])){
                if($attributs['caserneId'] == $id){
                    echo '<option value="' . $id . '" selected>' . $nom . '</option>';
                } else{
                    echo '<option value="' . $id . '">' . $nom . '</option>';
                }
            } else{
                echo '<option value="' . $id . '">' . $nom . '</option>'; 
            }   
        }        
    ?>
    </select>
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>