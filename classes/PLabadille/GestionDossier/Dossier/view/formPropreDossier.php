<h2 class="titreFormulaire">Formulaire d'édition de dossier personnel:</h2>
<a href="?objet=dossier&action=afficherSonDossier">Retourner à l'affichage de votre dossier</a>

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
    <label for="nom"><a rel="tooltip" title="Ces champs ne sont pas modifiables, si vous les jugez éronnés, veuillez contacter votre supérieur hierarchique."><img src="media/img/icons/info.png" alt="informations" /></a> Nom</label>
    <input type="text" name="nom" id="nom" placeholder="Saisir le nom" value="<?php echo $attributs['nom']; ?>" disabled/>

    <label for="prenom"><a rel="tooltip" title="Ces champs ne sont pas modifiables, si vous les jugez éronnés, veuillez contacter votre supérieur hierarchique."><img src="media/img/icons/info.png" alt="informations" /></a> Prénom</label>
    <input type="text" name="prenom" id="prenom" placeholder="Saisir le prénom" value="<?php echo $attributs['prenom']; ?>" disabled/>

    <label for="date_naissance"><a rel="tooltip" title="Ces champs ne sont pas modifiables, si vous les jugez éronnés, veuillez contacter votre supérieur hierarchique."><img src="media/img/icons/info.png" alt="informations" /></a> Date de naissance</label>
    <input type="text" name="date_naissance" class="datepicker" value="<?php echo $attributs['date_naissance']; ?>" disabled/>

    <label for="genre"><a rel="tooltip" title="Ces champs ne sont pas modifiables, si vous les jugez éronnés, veuillez contacter votre supérieur hierarchique."><img src="media/img/icons/info.png" alt="informations" /></a> Genre</label><br />
    <select name="genre" id="genre" disabled>
        <option value="H" <?php if($attributs['genre'] == 'M'){ echo ' selected'; } ?> >Homme</option>
        <option value="F" <?php if($attributs['genre'] == 'F'){ echo ' selected'; } ?> >Femme</option>
    </select>
	
    <label for="tel1">Numéro de téléphone principal*<span class="dossierFormErrors"><?php echo $errors['tel1']; ?></span></label>
    <input type="text" name="tel1" id="tel1" placeholder="format : xx xx xx xx xx" value="<?php echo $attributs['tel1']; ?>" />
    
    <label for="tel2">Numéro de téléphone secondaire<span class="dossierFormErrors"><?php echo $errors['tel2']; ?></span></label>
    <input type="text" name="tel2" id="tel2" placeholder="format : xx xx xx xx xx" value="<?php echo $attributs['tel2']; ?>" />
    
    <label for="email">Adresse email*<span class="dossierFormErrors"><?php echo $errors['email']; ?></span></label>
    <input type="email" name="email" id="email" placeholder="format xxxxx@xxx.xxx" value="<?php echo $attributs['email']; ?>" />

    <label for="adresse">Adresse postale*<span class="dossierFormErrors"><?php echo $errors['adresse']; ?></span></label>
    <input type="text" name="adresse" id="adresse" placeholder="format : numéro, rue, code postal, ville" value="<?php echo $attributs['adresse']; ?>" />
    
    <label for="date_recrutement"><a rel="tooltip" title="Ces champs ne sont pas modifiables, si vous les jugez éronnés, veuillez contacter votre supérieur hierarchique."><img src="media/img/icons/info.png" alt="informations" /></a> Date de recrutement</label>
    <input type="text" name="date_recrutement" class="datepicker" value="<?php echo $attributs['date_recrutement']; ?>" disabled/>
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo $attributs['id'] ? $attributs['id'] : null; ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>