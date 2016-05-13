<h2 class="titreFormulaire">Formulaire d'enregistrement ou de modification de conditions de promotion:</h2>
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
    
    <label for="idGrade">Grade*</label><br />
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

    <label for="annees_service_FA">Année de service (Force Armée)*<span class="dossierFormErrors"><?php echo $errors['annees_service_FA']; ?></span></label>
    <input type="number" name="annees_service_FA" id="annees_service_FA" placeholder="Saisir le nombre d'année de service requise" value="<?php echo (isset($attributs['annees_service_FA']) ? $attributs['annees_service_FA'] : null); ?>" />

    <label for="annees_service_GN">Année de service (Gendarmerie Nationale)*<span class="dossierFormErrors"><?php echo $errors['annees_service_GN']; ?></span></label>
    <input type="number" name="annees_service_GN" id="annees_service_GN" placeholder="Saisir le nombre d'année de service requise" value="<?php echo (isset($attributs['annees_service_GN']) ? $attributs['annees_service_GN'] : null); ?>" />

    <label for="annees_service_SOE">Année de service (Sous-Officier d'école)*<span class="dossierFormErrors"><?php echo $errors['annees_service_SOE']; ?></span></label>
    <input type="number" name="annees_service_SOE" id="annees_service_SOE" placeholder="Saisir le nombre d'année de service requise" value="<?php echo (isset($attributs['annees_service_SOE']) ? $attributs['annees_service_SOE'] : null); ?>" />

    <label for="annees_service_grade">Année de service (grade actuel)*<span class="dossierFormErrors"><?php echo $errors['annees_service_grade']; ?></span></label>
    <input type="number" name="annees_service_grade" id="annees_service_grade" placeholder="Saisir le nombre d'année de service requise" value="<?php echo (isset($attributs['annees_service_grade']) ? $attributs['annees_service_grade'] : null); ?>" />

    <label for="diplome">Diplome</label><br />
    <select name="diplome" id="diplome">
    <?php
        if (isset($attributs['diplome'])){
            echo '<option value=\'\'></option>';
        } else{
            echo '<option selected value=\'\'></option>';
        }
        foreach ($attributs['listeDiplome'] as $id => $nom) {
            if (isset($attributs['diplome'])){
                if($attributs['diplome'] == $id){
                    echo '<option value="' . $id . '" selected>' . $id . ': ' . $nom . '</option>';
                } else{
                    echo '<option value="' . $id . '" >' . $id . ': ' . $nom . '</option>';
                }
            } else{
                echo '<option value="' . $id . '" >' . $id . ': ' . $nom . '</option>'; 
            }   
        }        
    ?>
    </select>
    
    <label for="diplomeSup1">Diplome Sup1</label><br />
    <select name="diplomeSup1" id="diplomeSup1">
    <?php
        if (isset($attributs['diplomeSup1'])){
            echo '<option value=\'\'></option>';
        } else{
            echo '<option selected value=\'\'></option>';
        }
        foreach ($attributs['listeDiplome'] as $id => $nom) {
            if (isset($attributs['diplomeSup1'])){
                if($attributs['diplomeSup1'] == $id){
                    echo '<option value="' . $id . '" selected>' . $id . ': ' . $nom . '</option>';
                } else{
                    echo '<option value="' . $id . '" >' . $id . ': ' . $nom . '</option>';
                }
            } else{
                echo '<option value="' . $id . '" >' . $id . ': ' . $nom . '</option>'; 
            }   
        }        
    ?>
    </select>

    <label for="diplomeSup2">Diplome Sup2</label><br />
    <select name="diplomeSup2" id="diplomeSup2">
    <?php
        if (isset($attributs['diplomeSup2'])){
            echo '<option value=\'\'></option>';
        } else{
            echo '<option selected value=\'\'></option>';
        }
        foreach ($attributs['listeDiplome'] as $id => $nom) {
            if (isset($attributs['diplomeSup2'])){
                if($attributs['diplomeSup2'] == $id){
                    echo '<option value="' . $id . '" selected>' . $id . ': ' . $nom . '</option>';
                } else{
                    echo '<option value="' . $id . '" >' . $id . ': ' . $nom . '</option>';
                }
            } else{
                echo '<option value="' . $id . '" >' . $id . ': ' . $nom . '</option>'; 
            }   
        }        
    ?>
    </select>
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>