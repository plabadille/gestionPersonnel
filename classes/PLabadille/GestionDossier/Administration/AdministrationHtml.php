<?php
namespace PLabadille\GestionDossier\Administration;

use PLabadille\GestionDossier\Controller\AccessControll;
use PLabadille\Common\Authentication\AuthenticationManager;

//--------------------
//ORGANISATION DU CODE
//--------------------
# x- Fonctions utilitaires et génériques
# 4- Module création de compte et de droit
# 5- Module de gestion de l'application
# 6- Module de sauvegarde et de gestion de crise
//--------------------

#Gère l'affichage des Dossiers.
#Attention, la gestion d'affichage des formulaires est gérées directement par DossierForm par le biais de templates.
class AdministrationHtml 
{
    //--------------------
    //x- Fonctions utilitaires et génériques
    //--------------------
    public static function afficheListe($dossier) 
    {
        $html = $dossier['nom'] . ' ' . $dossier['prenom'];
        return $html;
    }

    //--------------------
    //4-module gestion et ajout de dossier
    //--------------------

    // 4-1- 'listCreatedFolderWithoutAccount':
    #Permet l'affichage de tout les dossiers (ou d'une partie)
    #appel afficheListe pour les afficher.
    public static function listAllWithOutAccount($dossier) 
    {
        $html = <<<EOT
            <h2>Liste des militaires n'ayant pas de compte</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=searchNameWithOutFolder">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchDossierWithOutAccount" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        foreach ($dossier as $dossier) {
            //affichage des boutonsNavigation en fonction des droits:
            $boutons = '';
            $typeBouton = 'createAccount';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutonsTest = ($right) ? '<a href="?objet=administration&amp;action=creerCompte&amp;id=' . $dossier['matricule'] . '" alt="Créer compte" ><img src="media/img/icons/add.png" alt="Créer compte" title="Créer compte" /></a>' : null;
  
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier['matricule']}">{$liste} - </a>{$boutonsTest}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    // 4-2- 'seeAllAccount':
    public static function listAllAccount($dossier, $info = null) 
    {
        $auth = AuthenticationManager::getInstance();
        $userRole = $auth->getRole();

        if (isset($info)){
            $info = '<div id="printImportantInformation"><p>Le nouveau mot de passe de ' . $info['prenom'] . ' ' . $info['nom'] . ' est : ' . $info['psw'] . '. Veuillez suivre la procédure de transmission habituelle.</p></div>';
        } else { $info = ''; }

        $html = <<<EOT
            <h2>Liste des militaires des comptes utilisateurs</h2>
                {$info}
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=administration&action=searchCompte">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchDossierWithAccount" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        //affichage des boutonsNavigation en fonction des droits:
        $typeBouton = 'alterMdp';
        $rightAlter = AccessControll::afficherBoutonNavigation($typeBouton);
        $typeBouton = 'alterAccountRight';
        $rightAlterRight = AccessControll::afficherBoutonNavigation($typeBouton);
        foreach ($dossier as $dossier) {
            $liste = self::afficheListe($dossier);
            $urlAlter = '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir réinitialiser le mot de passe du compte : ' . $dossier['matricule'] . ' (' . $liste . ') ?\')) document.location.href=\'?objet=administration&amp;action=changePassword&amp;id=' . $dossier['matricule'] . '\'" alt="Réinitialiser mot de passe" ><img src="media/img/icons/newpass.png" alt="Réinitialiser mot de passe" title="Réinitialiser mot de passe" /></a>';
            $urlAlterRight = '&nbsp;-&nbsp; <a href="?objet=administration&amp;action=changeDroitsCompte&amp;id=' . $dossier['matricule'] . '"><img src="media/img/icons/edit.png" alt="changer les droits du compte" title="changer les droits du compte" /></a>';
            if ( $userRole == 'superAdmin' ){ //le superAdmin peut changer n'importe quel mdp.
                $boutonAlter = ($rightAlter) ? $urlAlter : null;
                $boutonAlterRight = ($rightAlterRight) ? $urlAlterRight : null;
            } elseif ( $userRole == 'admin' ) { //les admin ne peuvent ni changer le mdp des autres admin, ni celui du super admin
                if ($dossier['role'] !== 'superAdmin' && $dossier['role'] != 'admin'){
                    $boutonAlter = ($rightAlter) ? $urlAlter : null;
                    $boutonAlterRight = ($rightAlterRight) ? $urlAlterRight : null;
                } else {
                    $boutonAlter = null;
                    $boutonAlterRight = null;
                }
            } else{ //les autres (dont on a eventuellement donné le droit ne peuvent moddifier que celui des militaires de base)
                if ($dossier['role'] == 'militaire'){
                    $boutonAlter = ($rightAlter) ? $urlAlter : null;
                    $boutonAlterRight = ($rightAlterRight) ? $urlAlterRight : null;
                } else {
                    $boutonAlter = null;
                    $boutonAlterRight = null;
                }
            }

            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier['matricule']}">{$liste} -</a> identifiant: {$dossier['matricule']}, rôle: {$dossier['role']} {$boutonAlter} {$boutonAlterRight}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    public static function listAllAccountToDelete($dossier)
    {

        //affichage des dossiers retraités
        $html = <<<EOT
            <h2>Liste des comptes à supprimer</h2>
            <h3>Dossiers retraités</h3>    
            <ul id="listeDossier">
EOT;
        foreach ($dossier['retraite'] as $dossierR) {
            //affichage des boutonsNavigation en fonction des droits:
            $typeBouton = 'deleteAccount';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutonSupr= ($right) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer le compte : ' . $dossierR['matricule'] . ' ?\')) document.location.href=\'?objet=administration&amp;action=suprCompte&amp;id=' . $dossierR['matricule'] . '\'" alt="Supprimer Compte" title="Supprimer Compte"><img src="media/img/icons/delete.png" alt="Supprimer Compte" /></a>' : null;


  
            $liste = self::afficheListe($dossierR);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossierR['matricule']}">{$liste} - </a> - {$dossierR['date_retraite']} {$boutonSupr}
                </li>
EOT;
        }
        if (empty($dossier['retraite'])){
            $html .= 'aucun compte à supprimer';
        }

        $html .= "  </ul>\n";
        //affichage des dossiers archivés
        $html .= <<<EOT
            <h3>Dossiers archivés</h3>    
            <ul id="listeDossier">
EOT;
        foreach ($dossier['archive'] as $dossierA) {
            //affichage des boutonsNavigation en fonction des droits:
            $typeBouton = 'deleteAccount';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutonSupr= ($right) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer le compte : ' . $dossierA['matricule'] . ' ?\')) document.location.href=\'?objet=administration&amp;action=suprCompte&amp;id=' . $dossierA['matricule'] . '\'" alt="Supprimer compte" title="Supprimer compte"><img src="media/img/icons/delete.png" alt="Supprimer compte" /></a>' : null;
  
            $liste = self::afficheListe($dossierA);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossierA['matricule']}">{$liste} - </a> - {$dossierA['date_deces']} {$boutonSupr}
                </li>
EOT;
        }
        if (empty($dossier['archive'])){
            $html .= 'aucun compte à supprimer';
        }

        $html .= "  </ul>\n";
        return $html;

    }
    
    //--------------------
    //5-module de gestion de l'application
    //--------------------

    // 5-1- 'seeAllConstanteTable':

    // 5-1-1 'seeAllCasernes':
    public static function listAllCasernes($casernes, $info = null)
    {
        if (isset($info)){
            $prezInfo = '<div id="info"><p>' . $info . '</p></div>';
        } else{
            $prezInfo = null;
        }
    $html = <<<EOT
            <h2>Liste des casernes</h2>
            {$prezInfo}
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>#</th>
                    <th>nom</th>
                    <th>adresse</th>
                    <th>tel_standard</th>
                    <th>actions</th>
                </tr>
EOT;
        //droits d'affichage des boutons
        $typeBouton = 'editInAConstanteTable';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
        $typeBouton = 'deleteInAConstanteTable';
        $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);

        foreach ($casernes as $key => $tab) {
            //affichage des boutons si droit
                //addslashes utilisé pour échappé les caractères dans la chaine de caractère nom
            $boutonEditer = ($rightEdit) ? '<a href="?objet=administration&amp;action=editerCaserne&amp;id=' . $tab['id'] . '"><img src="media/img/icons/edit.png" alt="Editer" title="Editer" /></a>' : null;
            $boutonSupprimer = ($rightSupr) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer cette caserne : ' . addslashes($tab['nom']) . ' ?\')) document.location.href=\'?objet=administration&amp;action=supprimerCaserne&amp;id=' . $tab['id'] . '\'" alt="Supprimer caserne" title="Supprimer caserne"><img src="media/img/icons/delete.png" alt="Supprimer caserne" /></a>' : null;

            $html .= '<tr>' . "\n";
            foreach ($tab as $key => $value) {
                //affichage data
                $html .= '<td>' . $value . '</td>' . "\n";
            }
            //affichage bouton
            $html .= '<td>' . $boutonEditer . ' ' . $boutonSupprimer . '</td>' . "\n";

            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";

        return $html;
    }
    // 5-1-2 'seeAllRegiments':
    public static function listAllRegiments($regiments, $info = null)
    {
        if (isset($info)){
            $prezInfo = '<div id="info"><p>' . $info . '</p></div>';
        } else{
            $prezInfo = null;
        }
    $html = <<<EOT
            <h2>Liste des régiments</h2>
            {$prezInfo}
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>noms</th>
                    <th>actions</th>
                </tr>
EOT;
        //droits d'affichage des boutons
        $typeBouton = 'editInAConstanteTable';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
        $typeBouton = 'deleteInAConstanteTable';
        $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);

        foreach ($regiments as $key => $tab) {
            //affichage des boutons si droit
        $boutonEditer = ($rightEdit) ? '<a href="?objet=administration&amp;action=editerRegiment&amp;id=' . $tab['id'] . '"><img src="media/img/icons/edit.png" alt="Editer" title="Editer" /></a>' : null;
        $boutonSupprimer = ($rightSupr) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce régiment : ' . $tab['id'] . ' ?\')) document.location.href=\'?objet=administration&amp;action=supprimerRegiment&amp;id=' . $tab['id'] . '\'" alt="Supprimer régiment" title="Supprimer régiment"><img src="media/img/icons/delete.png" alt="Supprimer régiment" /></a>' : null;

            $html .= '<tr>' . "\n";
            foreach ($tab as $key => $value) {
                $html .= '<td>' . $value . '</td>' . "\n";
            }
             //affichage bouton
            $html .= '<td>' . $boutonEditer . ' ' . $boutonSupprimer . '</td>' . "\n";

            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";

        return $html;
    }
    // 5-1-3 'seeAllDiplomes':
    public static function listAllDiplomes($diplomes, $info = null)
    {
        if (isset($info)){
            $prezInfo = '<div id="info"><p>' . $info . '</p></div>';
        } else{
            $prezInfo = null;
        }
    $html = <<<EOT
            <h2>Liste des diplômes</h2>
            {$prezInfo}
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>acronyme</th>
                    <th>intitulé</th>
                    <th>actions</th>
                </tr>
EOT;
        //droits d'affichage des boutons
        $typeBouton = 'editInAConstanteTable';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
        $typeBouton = 'deleteInAConstanteTable';
        $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);

        foreach ($diplomes as $key => $tab) {
            //affichage des boutons si droit
                //addslashes utilisé pour échappé les caractères dans la chaine de caractère intitule
            $boutonEditer = ($rightEdit) ? '<a href="?objet=administration&amp;action=editerDiplome&amp;id=' . $tab['acronyme'] . '"><img src="media/img/icons/edit.png" alt="Editer" title="Editer" /></a>' : null;
            $boutonSupprimer = ($rightSupr) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce diplôme : ' . addslashes($tab['intitule']) . ' ?\')) document.location.href=\'?objet=administration&amp;action=supprimerDiplome&amp;id=' . $tab['acronyme'] . '\'" alt="Supprimer diplome" title="Supprimer diplome"><img src="media/img/icons/delete.png" alt="Supprimer diplome" /></a>' : null;

            $html .= '<tr>' . "\n";
            foreach ($tab as $key => $value) {
                $html .= '<td>' . $value . '</td>' . "\n";
            }
            //affichage bouton
            $html .= '<td>' . $boutonEditer . ' ' . $boutonSupprimer . '</td>' . "\n";
            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";
        return $html;
    }
    // 5-1-4 'seeAllGrades':
    public static function listAllGrades($grades, $info = null)
    {
        if (isset($info)){
            $prezInfo = '<div id="info"><p>' . $info . '</p></div>';
        } else{
            $prezInfo = null;
        }
    $html = <<<EOT
            <h2>Liste des grades</h2>
            {$prezInfo}
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>id</th>
                    <th>grade</th>
                    <th>hierarchie</th>
                    <th>actions</th>
                </tr>
EOT;
        //droits d'affichage des boutons
        $typeBouton = 'editInAConstanteTable';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
        $typeBouton = 'deleteInAConstanteTable';
        $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);

        foreach ($grades as $key => $tab) {
            //affichage des boutons si droit
            $boutonEditer = ($rightEdit) ? '<a href="?objet=administration&amp;action=editerGrade&amp;id=' . $tab['id'] . '"><img src="media/img/icons/edit.png" alt="Editer" title="Editer" /></a>' : null;
            $boutonSupprimer = ($rightSupr) ? '&nbsp;-&nbsp; <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce grade : ' . addslashes($tab['grade']) . ' ?\')) document.location.href=\'?objet=administration&amp;action=supprimerGrade&amp;id=' . $tab['id'] . '\'" alt="Supprimer grade" title="Supprimer grade"><img src="media/img/icons/delete.png" alt="Supprimer grade" /></a>' : null;

            $html .= '<tr>' . "\n";
            foreach ($tab as $key => $value) {
                $html .= '<td>' . $value . '</td>' . "\n";
            }
            //affichage bouton
            $html .= '<td>' . $boutonEditer . ' ' . $boutonSupprimer . '</td>' . "\n";
            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";

        return $html;
    }
    // 5-1-5 'seeAllDroits':
    public static function listAllDroits($droits, $info = null)
    {
        if (isset($info)){
            $prezInfo = '<div id="info"><p>' . $info . '</p></div>';
        } else{
            $prezInfo = null;
        }
        $auth = AuthenticationManager::getInstance();
        $userRole = $auth->getRole();


        $html = <<<EOT
            <h2>Liste des classes de droits</h2>
            {$prezInfo}
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>type de droit / role</th>
EOT;
        //droits d'affichage des boutons
        $typeBouton = 'editInAConstanteTable';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
        
        if( $userRole == 'superAdmin' ){ //seul le superAdmin est autorisé à supprimer une classe de droit
            $rightSupr = true;
        } else{
            $rightSupr = false;
        }

        for ($i=0; $i < count($droits); $i++) {
            //affichage des boutons si droit
            $boutonEditer = ($rightEdit) ? '<br/> <a href="?objet=administration&amp;action=editerClasseDroits&amp;id=' . $droits[$i]['role'] . '"><img src="media/img/icons/edit.png" alt="Editer" title="Editer" /></a>' : null;
            $boutonSupprimer = ($rightSupr) ? '<br/> <a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer la classe de droit : ' . addslashes($droits[$i]['role']) . ' ?\')) document.location.href=\'?objet=administration&amp;action=supprimerClasseDroits&amp;id=' . $droits[$i]['role'] . '\'" alt="Supprimer classe de droits" title="Supprimer classe de droits"><img src="media/img/icons/delete.png" alt="Supprimer classe de droits" /></a>' : null;

            if ( $droits[$i]['role'] == 'superAdmin' ){ //la classe superAdmin n'est ni moddifiable, ni supprimable
                $boutonEditer = null;
                $boutonSupprimer = null;
            }
            $html .= '<th>' . $droits[$i]['role'] . $boutonEditer . $boutonSupprimer . '</th>' . "\n";
            ;
        }
        $html .= '</tr>' . "\n";

        foreach ($droits['0'] as $key => $value) {
            if ($key != 'role'){
                $html .= '<tr>' . "\n";
                $column = $key;
                $html .= '<td>' . $column . '</td>' . "\n";
                foreach ($droits as $key => $value) {
                    if ($value[$column] === '0'){
                        $valueC = 'non';
                    } elseif ($value[$column] === '1'){
                        $valueC = 'oui';
                    }
                    $html .= '<td>' . $valueC . '</td>' . "\n";
               }
               $html .= '</tr>' . "\n";
           }
        }

        $html .= '</table>' . "\n";

        return $html;
    }
}