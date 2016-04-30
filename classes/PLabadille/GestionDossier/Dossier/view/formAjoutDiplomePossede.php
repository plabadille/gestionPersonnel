<h2 class="titreFormulaire">Formulaire d'ajout de diplôme possédé:</h2>
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

    <label for="diplomeId">Diplome:</label><br />
    <select name="diplomeId" id="diplomeId">
    <?php
        foreach ($attributs['listeDiplome'] as $id => $nom) {
            if (isset($attributs['diplomeId'])){
                if($attributs['diplomeId'] == $id){
                    echo '<option value="' . $id . '" selected>' . $id . ': ' . $nom . '</option>';
                } else{
                    echo '<option value="' . $id . '" required>' . $id . ': ' . $nom . '</option>';
                }
            } else{
                echo '<option value="' . $id . '" required>' . $id . ': ' . $nom . '</option>'; 
            }   
        }        
    ?>
    </select>

    <label for="date_obtention">Date d'obtention:<span class="dossierFormErrors"><?php echo $errors['date_obtention']; ?></span></label>
    <input type="text" name="date_obtention" class="datepicker" value="<?php echo (isset($attributs['date_obtention']) ? $attributs['date_obtention'] : null); ?>" />

    <label for="pays_obtention">Pays d'obtention:<span class="dossierFormErrors"><?php echo $errors['pays_obtention']; ?></span></label>
    <input type="text" name="pays_obtention" id="pays_obtention" placeholder="Saisir le pays d'obtention" value="<?php echo (isset($attributs['pays_obtention']) ? $attributs['pays_obtention'] : null); ?>" />

    <label for="organisme_formateur">Organisme Formateur:<span class="dossierFormErrors"><?php echo $errors['organisme_formateur']; ?></span></label>
    <input type="text" name="organisme_formateur" id="organisme_formateur" placeholder="Saisir le nom de l'organisme formateur" value="<?php echo (isset($attributs['organisme_formateur']) ? $attributs['organisme_formateur'] : null); ?>" />
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>