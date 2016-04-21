<h2 class="titreFormulaire">Formulaire d'ajout de grade:</h2>
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
    <label for="date_promotion">Date de promotion<span class="dossierFormErrors"><?php echo $errors['date_promotion']; ?></span></label>
    <input type="text" name="date_promotion" class="datepicker" value="<?php echo (isset($attributs['date_promotion']) ? $attributs['date_promotion'] : null); ?>" />

    <label for="gradeId">Grade:</label><br />
    <select name="gradeId" id="gradeId">
    <?php
        foreach ($attributs['listeGrade'] as $id => $nom) {
            if (isset($attributs['gradeId'])){
                if($attributs['gradeId'] == $id){
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