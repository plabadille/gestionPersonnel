<h2 class="titreFormulaire">Formulaire de création de dossier:</h2>
<?php
if (isset($attributs['id'])){
    echo '<a href="?objet=dossier&action=voir&id=' . $attributs['id'] . '">Retourner à l\'affichage du dossier</a>';
} ?> 
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
    <label for="nom">Nom*<span class="dossierFormErrors"><?php echo $errors['nom']; ?></span></label>
    <input type="text" name="nom" id="nom" placeholder="Saisir le nom" value="<?php echo $attributs['nom']; ?>" />

    <label for="prenom">Prénom*<span class="dossierFormErrors"><?php echo $errors['prenom']; ?></span></label>
    <input type="text" name="prenom" id="prenom" placeholder="Saisir le prénom" value="<?php echo $attributs['prenom']; ?>" />

    <label for="date_naissance"><a rel="tooltip" title="le format de date à saisir est sous la forme américaine : YYYY-MM-DD, en cas de doute, utilisez le calendrier qui s'affiche automatiquement."><img src="media/img/icons/info.png" alt="informations" /></a> Date de naissance<span class="dossierFormErrors"><?php echo $errors['date_naissance']; ?></span></label>
    <input type="text" name="date_naissance" class="datepicker" value="<?php echo $attributs['date_naissance']; ?>" />

    <label for="genre">Genre*</label><br />
    <select name="genre" id="genre">
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
    
    <label for="date_recrutement"><a rel="tooltip" title="le format de date à saisir est sous la forme américaine : YYYY-MM-DD. Attention, la date doit être cohérente avec la date de naissance de l'individu (non recrutable avant ses 18 ans)"><img src="media/img/icons/info.png" alt="informations" /></a> Date de recrutement*<span class="dossierFormErrors"><?php echo $errors['date_recrutement']; ?></span></label>
    <input type="text" name="date_recrutement" class="datepicker" value="<?php echo $attributs['date_recrutement']; ?>" />
    
    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='id' value="<?php echo $attributs['id'] ? $attributs['id'] : null; ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>