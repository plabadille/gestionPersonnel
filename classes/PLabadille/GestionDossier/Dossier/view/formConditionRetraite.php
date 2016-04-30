<h2 class="titreFormulaire">Formulaire d'enregistrement ou de modification de conditions de retraite:</h2>
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

    <label for="idGrade">Grade:</label><br />
    <select name="idGrade" id="idGrade">
    <?php
    
        foreach ($attributs['listeGrade'] as $id => $nom) {
            if (isset($attributs['idGrade'])){
                if($attributs['idGrade'] == $id){
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

    <label for="service_effectif">Service effectif:<span class="dossierFormErrors"><?php echo $errors['service_effectif']; ?></span></label>
    <input type="number" name="service_effectif" placeholder="Saisir le nombre d'année de service pour le départ à la retraite'" value="<?php echo (isset($attributs['service_effectif']) ? $attributs['service_effectif'] : null); ?>" />

    <label for="age">Âge:<span class="dossierFormErrors"><?php echo $errors['age']; ?></span></label>
    <input type="number" name="age" id="age" placeholder="Saisir l'âge de départ à la retraite'" value="<?php echo (isset($attributs['age']) ? $attributs['age'] : null); ?>" />
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>