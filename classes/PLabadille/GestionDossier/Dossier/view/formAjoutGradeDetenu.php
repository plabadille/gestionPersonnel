<h2 class="titreFormulaire">Formulaire d'ajout de grade:</h2>
<div id="formErrorsExist">
    <?php
    $js = true;
        if (is_array($errors)){
            $cleanErrors = array_filter($errors); 
            if ($cleanErrors !== null){
                echo '<div id="errorFieldDossierForm"><p>Il existe des erreurs dans la saisie du formulaire, voir ci-dessous :</p>';
                if (isset($errors['doublon'])){
                    echo '<p>' . $errors['doublon'] . '</p>';
                }
                echo '</div>';
                if (isset($errors['gradeId'])){
                    $attributs['gradeId'] = null;
                }
            }
        } 
    ?>
</div>
<form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=<?php echo $type ; ?>">
    <label for="date_promotion"><a rel="tooltip" title="le format de date à saisir est sous la forme américaine : YYYY-MM-DD. Attention, la date doit être cohérente avec la date de recrutement et de son dernier grade."><img src="media/img/icons/info.png" alt="informations" /></a> Date de promotion*<span class="dossierFormErrors"><?php echo $errors['date_promotion']; ?></span></label>
    <input type="text" name="date_promotion" class="datepicker" value="<?php echo (isset($attributs['date_promotion']) ? $attributs['date_promotion'] : null); ?>" />
    
    <label for="gradeId"><a rel="tooltip" title="Veuillez utiliser l'autocompletion pour remplir ce champ, il vous suffit pour celà de commencer la saisie."><img src="media/img/icons/info.png" alt="informations" /></a> Grade*<span class="dossierFormErrors"><?php echo $errors['gradeId']; ?></span></label><br />
    <input type="text" name="gradeId" autocomplete="off" id="searchGrade" value="<?php echo (isset($attributs['gradeId']) ? $attributs['gradeId'] . ' : ' . $attributs['listeGrade'][$attributs['gradeId']] : null); ?>" />

    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo (isset($attributs['id']) ? $attributs['id'] : null); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>