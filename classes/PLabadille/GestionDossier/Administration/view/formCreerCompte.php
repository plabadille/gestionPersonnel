<?php
    if (isset($attributs['identite'])){
        $attributs['prenom'] = $attributs['identite']['prenom'];
        $attributs['nom'] = $attributs['identite']['nom'];
    }
    if ( $type == 'sauvegarderCompte' ){
        echo '<h2 class="titreFormulaire">Formulaire de création de compte utilisateur:</h2>' . "\n";
    } elseif ( $type == 'sauvegarderEditionCompte' ){
        echo '<h2 class="titreFormulaire">Formulaire de modification de droit d\'un compte:</h2>' . "\n";
    }
?>
<h3>Le compte concerné est celui de l'utilisateur <?php echo $attributs['prenom'] . ' ' . $attributs['nom']; ?></h3>
<?php 
if ( $type == 'sauvegarderCompte' ){
    echo '<p>Voici le mot de passe à transmettre pour celui-ci par courrier postal (celui-ci ne pourra pas être réaffiché) : ' . $attributs['psw'] . '</p>';
}
?>
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

<form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=<?php echo $type ; ?>">
    <label for="username">Nom d'utilisateur</label>
    <input type="text" name="username" id="username" value="<?php echo $attributs['id']; ?>" readonly/>

    <label for="role">Selectionnez le rôle de cet utilisateur</label>
    <select name="role" id="role">
    <?php
        foreach ($attributs['listeRole'] as $role) {
            if (isset($attributs['role'])){
                if($attributs['role'] == $role['role']){//si on connait le rôle déjà défini (édition etc)
                   echo '<option value="' . $role['role'] . '" selected>' . $role['role'] . '</option>';
                } else{
                    echo '<option value="' . $role['role'] . '">' . $role['role'] . '</option>';
                }
            } else{ //si le role n'est pas déjà set, on met militaire par défaut
                if ($role['role'] == "militaire"){
                    echo '<option value="' . $role['role'] . '" selected>' . $role['role'] . '</option>';
                } else{
                    echo '<option value="' . $role['role'] . '">' . $role['role'] . '</option>'; 
                }
            }   
        }        
    ?>
   </select>

    <input type='hidden' name='pass' value="<?php echo $attributs['pass'] ? $attributs['pass'] : null; ?>">
    <?php
    if ( $type == 'sauvegarderCompte' ){
        echo '<input type=\'hidden\' name=\'psw\' value="' . $attributs['psw'] . '">';
    }
    ?>
    <input type='hidden' name='nom' value="<?php echo $attributs['nom'] ? $attributs['nom'] : null; ?>">
    <input type='hidden' name='prenom' value="<?php echo $attributs['prenom'] ? $attributs['prenom'] : null; ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />
    </form>