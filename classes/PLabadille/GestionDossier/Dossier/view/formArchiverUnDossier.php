<h2 class="titreFormulaire">Formulaire d'archivage de dossier (seulement en cas de décès):</h2>
<h3>Dossier de <?php echo $attributs['prenom'] . ' ' . $attributs['nom'] . ' (matricule: ' . $attributs['id'] . ')'; ?></h3>
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
    
    <label for="date_deces"><a rel="tooltip" title="le format de date à saisir est sous la forme américaine : YYYY-MM-DD."><img src="media/img/icons/info.png" alt="informations" /></a> Date de décès*<span class="dossierFormErrors"><?php echo $errors['date_deces']; ?></span></label>
    <input type="text" name="date_deces" class="datepicker" value="<?php echo (isset($attributs['date_deces']) ? $attributs['date_deces'] : null); ?>" />

    <label for="cause_deces">Cause du décès*<span class="dossierFormErrors"><?php echo $errors['cause_deces']; ?></span></label>
    <input type="text" name="cause_deces" id="cause_deces" placeholder="Saisir la cause du décès" value="<?php echo (isset($attributs['cause_deces']) ? $attributs['cause_deces'] : null); ?>" />
    
    <input type='hidden' name='id' value="<?php echo $attributs['id'] ? $attributs['id'] : null; ?>">
    <input type='hidden' name='prenom' value="<?php echo $attributs['prenom'] ? $attributs['prenom'] : null; ?>">
    <input type='hidden' name='nom' value="<?php echo $attributs['nom'] ? $attributs['nom'] : null; ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>


