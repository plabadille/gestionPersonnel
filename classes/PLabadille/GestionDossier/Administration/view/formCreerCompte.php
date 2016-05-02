<?php 
    if (isset($attributs['identite'])){
        $attributs['prenom'] = $attributs['identite']['prenom'];
        $attributs['nom'] = $attributs['identite']['nom'];
    }
?>
<h2 class="titreFormulaire">Formulaire de création de compte utilisateur:</h2>
<h3>Le compte en cours de création concerne l'utilisateur <?php echo $attributs['prenom'] . ' ' . $attributs['nom']; ?></h3>
<p>Voici le mot de passe à transmettre pour celui-ci par courrier postal (celui-ci ne pourra pas être réaffiché) : <?php echo $attributs['psw']; ?></p>
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

<form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=sauvegarderCompte">
    <label for="username">Nom d'utilisateur</label>
    <input type="text" name="username" id="username" value="<?php echo $attributs['id']; ?>" readonly/>

    <label for="role">Selectionnez le rôle de cet utilisateur</label>
    <select name="role" id="role">
    <?php
        foreach ($attributs['listeRole'] as $role) {
            if (isset($attributs['role'])){
                if($attributs['role'] == $role['role']){
                   echo '<option value="' . $role['role'] . '" selected>' . $role['role'] . '</option>';
                } else{
                    echo '<option value="' . $role['role'] . '">' . $role['role'] . '</option>';
                }
            } else{
                if ($role['role'] == "militaire"){
                    echo '<option value="' . $role['role'] . '" selected>' . $role['role'] . '</option>';
                }
                echo '<option value="' . $role['role'] . '">' . $role['role'] . '</option>'; 
            }   
        }        
    ?>
   </select>

    <input type='hidden' name='hash' value="<?php echo $attributs['hash'] ? $attributs['hash'] : null; ?>">
    <input type='hidden' name='psw' value="<?php echo $attributs['psw'] ? $attributs['psw'] : null; ?>">
    <input type='hidden' name='nom' value="<?php echo $attributs['nom'] ? $attributs['nom'] : null; ?>">
    <input type='hidden' name='prenom' value="<?php echo $attributs['prenom'] ? $attributs['prenom'] : null; ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
</form>