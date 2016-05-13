<?php
    if ( $type == 'sauvegarderClasseDroits' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'ajout de classe de droit:</h2>' . "\n";
    } elseif ( $type == 'sauvegarderEditionClasseDroits' ){
        echo '<h2 class="titreFormulaire">Formulaire d\'édition de classe de droit:</h2>' . "\n";
        foreach ($attributs as $key => $value) { //puisqu'en base les valeurs sont des 0 ou des 1, on supprime les clés fausses afin de cocher les bonnes case.
            if ($key != 'role'){
                if ($value == '0'){
                    unset($attributs[$key]);
                }                
            }
        }
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

<form enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=<?php echo $type ; ?>">

    <label for="role">Nom de la classe*<span class="dossierFormErrors"><?php echo $errors['role']; ?></span></label>
    <input type="text" name="role" id="role" placeholder="Saisir le nom de la classe" value="<?php echo $attributs['role']; ?>" /><br>

    <input type=checkbox name="noRights"<?php echo (isset($attributs['noRights']) ? ' checked' : null); ?> >noRights<br>
    <input type=checkbox name="allRights" disabled >allRights<br>
    <input type=checkbox name="seeOwnFolderModule"<?php echo (isset($attributs['seeOwnFolderModule']) ? ' checked' : null); ?> >seeOwnFolderModule<br>
    <input type=checkbox name="editOwnFolderPersonalInformation"<?php echo (isset($attributs['editOwnFolderPersonalInformation']) ? ' checked' : null); ?> >editOwnFolderPersonalInformation<br>
    <input type=checkbox name="listCreatedFolder"<?php echo (isset($attributs['listCreatedFolder']) ? ' checked' : null); ?> >listCreatedFolder<br>
    <input type=checkbox name="listAllFolder"<?php echo (isset($attributs['listAllFolder']) ? ' checked' : null); ?> >listAllFolder<br>
    <input type=checkbox name="seeCreatedFolder"<?php echo (isset($attributs['seeCreatedFolder']) ? ' checked' : null); ?> >seeCreatedFolder<br>
    <input type=checkbox name="seeAllFolder"<?php echo (isset($attributs['seeAllFolder']) ? ' checked' : null); ?> >seeAllFolder<br>
    <input type=checkbox name="createFolder"<?php echo (isset($attributs['createFolder']) ? ' checked' : null); ?> >createFolder<br>
    <input type=checkbox name="addElementToAFolder"<?php echo (isset($attributs['addElementToAFolder']) ? ' checked' : null); ?> >addElementToAFolder<br>
    <input type=checkbox name="addElementToAFolderCreated"<?php echo (isset($attributs['addElementToAFolderCreated']) ? ' checked' : null); ?> >addElementToAFolderCreated<br>
    <input type=checkbox name="editInformationIfAuthor"<?php echo (isset($attributs['editInformationIfAuthor']) ? ' checked' : null); ?> >editInformationIfAuthor<br>
    <input type=checkbox name="editInformation"<?php echo (isset($attributs['editInformation']) ? ' checked' : null); ?> >editInformation<br>
    <input type=checkbox name="deleteInformation"<?php echo (isset($attributs['deleteInformation']) ? ' checked' : null); ?> >deleteInformation<br>
    <input type=checkbox name="useFileToAddFolders"<?php echo (isset($attributs['useFileToAddFolders']) ? ' checked' : null); ?> >useFileToAddFolders<br>
    <input type=checkbox name="listEligible"<?php echo (isset($attributs['listEligible']) ? ' checked' : null); ?> >listEligible<br>
    <input type=checkbox name="editEligibleCondition"<?php echo (isset($attributs['editEligibleCondition']) ? ' checked' : null); ?> >editEligibleCondition<br>
    <input type=checkbox name="addEligibleCondition"<?php echo (isset($attributs['addEligibleCondition']) ? ' checked' : null); ?> >addEligibleCondition<br>
    <input type=checkbox name="suprEligibleCondition"<?php echo (isset($attributs['suprEligibleCondition']) ? ' checked' : null); ?> >suprEligibleCondition<br>
    <input type=checkbox name="canRetireAFolder"<?php echo (isset($attributs['canRetireAFolder']) ? ' checked' : null); ?> >canRetireAFolder<br>
    <input type=checkbox name="canArchiveAFolder"<?php echo (isset($attributs['canArchiveAFolder']) ? ' checked' : null); ?> >canArchiveAFolder<br>
    <input type=checkbox name="editEligibleEmailContent"<?php echo (isset($attributs['editEligibleEmailContent']) ? ' checked' : null); ?> >editEligibleEmailContent<br>
    <input type=checkbox name="uploadFileForMail"<?php echo (isset($attributs['uploadFileForMail']) ? ' checked' : null); ?> >uploadFileForMail<br>
    <input type=checkbox name="changePieceJointeForEligibleMail"<?php echo (isset($attributs['changePieceJointeForEligibleMail']) ? ' checked' : null); ?> >changePieceJointeForEligibleMail<br>
    <input type=checkbox name="seeAllFolderWithoutAccount"<?php echo (isset($attributs['seeAllFolderWithoutAccount']) ? ' checked' : null); ?> >seeAllFolderWithoutAccount<br>
    <input type=checkbox name="seeAllAccount"<?php echo (isset($attributs['seeAllAccount']) ? ' checked' : null); ?> >seeAllAccount<br>
    <input type=checkbox name="createAccount"<?php echo (isset($attributs['createAccount']) ? ' checked' : null); ?> >createAccount<br>
    <input type=checkbox name="alterMdp"<?php echo (isset($attributs['alterMdp']) ? ' checked' : null); ?> >alterMdp<br>
    <input type=checkbox name="alterAccountRight"<?php echo (isset($attributs['alterAccountRight']) ? ' checked' : null); ?> >alterAccountRight<br>
    <input type=checkbox name="deleteAccount"<?php echo (isset($attributs['deleteAccount']) ? ' checked' : null); ?> >deleteAccount<br>
    <input type=checkbox name="seeAllConstanteTable"<?php echo (isset($attributs['seeAllConstanteTable']) ? ' checked' : null); ?> >seeAllConstanteTable<br>
    <input type=checkbox name="editInAConstanteTable"<?php echo (isset($attributs['editInAConstanteTable']) ? ' checked' : null); ?> >editInAConstanteTable<br>
    <input type=checkbox name="deleteInAConstanteTable"<?php echo (isset($attributs['deleteInAConstanteTable']) ? ' checked' : null); ?> >deleteInAConstanteTable<br>

    <!-- champ hidden conservant l'ancien id lors de l'édition afin de le supprimer -->
    <input type='hidden' name='oldRole' value="<?php echo (isset($attributs['oldRole']) ? $attributs['oldRole'] : ($attributs['role'] ? $attributs['role'] : null)); ?>">

    <input id="boutonOk" type="submit" value="Envoyer" />

</form>