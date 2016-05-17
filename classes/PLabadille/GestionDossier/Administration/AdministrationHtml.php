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

    //--------------------
    //6-module de sauvegarde et de gestion de crise
    //--------------------

    // 6-1- 'gestion de la bdd':
    //-----------------------------
    public static function displayBddManagement($info = null)
    {
        if (!empty($info)){
            $infoDisplay = '<div id="infoAction"><p>' . $info . '</p></div>';
        } else{
            $infoDisplay = null;
        }

        $html = <<<EOT
        <section id="adminDisplayContent">
            <h2>Gestion de la base de donnée</h2>
            {$infoDisplay}
            
            <div class="block">
                <h3>Sauvegarde de la base de donnée</h3>
                <p><a href="?objet=administration&action=downloadBddDump" alt="lien de téléchargement de la bdd">Télécharger l'intégralité de la base</a><p>
            </div>
            
            <div class="block">
                <h3>Importer une version différente de la base de donnée</h3>
                <p>Cette fonctionnalité est utile si vous souhaitez pour une raison quelcquonque revenir à un état antérieur de la base de donnée. Vous pouvez également vous servir de cette fonction pour importer une nouvelle base. Néanmoins il convient d'être prudent et nous vous conseillons fortement de n'importer que des fichiers de sauvegarde réalisé avec la fonctionnalité ci-dessus.</p>
                <p>Si le fichier importé n'est pas correct, il y a un risque réel de disfonctionnement de l'application suite à une importation nulle ou partielle, une intervention manuelle (hors application) serait alors nécessaire.</p>
                <p><b>!!! Le fichier importé doit nécessairement correspondre à la base de donnée complète et non une partie. N'utilisez pas cette fonctionnalité pour simplement ajouter des données !!!</b></p>

                <form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="?objet=administration&action=importDump">
                    <label for="dump">Ajouter un fichier SQL</label>
                    <input type="file" name="dump" />  
                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>   
            </div>
            
            <div class="block">
                <h3>Gestion des droits des utilisateurs (situation d'urgence)</h3>
                <p>Si vous souhaitez interdir l'accès à toutes les fonctionnalités des utilisateurs (temporairement, les droits préalablement prévu ne seront pas perdu) de toutes les classes excepté celle de superAdministrateur, choisissez "activer". Pour remettre les droits en l'état suite à une activation, choisissez "désactiver".</p>
                <p><a href="?objet=administration&action=setAllUsersToNoRight" alt="passer les droits de tous les utilisateurs à aucun droit">activer</a> -/-
                <a href="?objet=administration&action=unsetAllUsersToNoRight" alt="désactiver aucun droits">désactiver</a></p>
            </div>

            <div class="block">
                <h3>Suppression de la base de donnée</h3>
                <p>Si la base a été corrompue, que vous souhaitez rendre le site totalement indisponible ou que vous souhaitez protéger les données vous pouvez utiliser cette fonctionnalité. La supression totale sera cependant précédée d'une sauvegarde de secour dans /data/tmp. Le site deviendra alors <b>indisponible</b> pour tous les utilisateurs, <b>vous compris</b> : il faudra alors <b>réimporter la base de donnée manuellement</b>. Si vous souhaitez simplement retourner à une ancienne version de la base, choisissez plutôt la fonction d'import.</p>
                <p><a href="javascript:if(confirm('Cette action est irréversible, êtes-vous sûr de vouloir supprimer la base de donnée? Elle devra ensuite être réimporté manuellement.')) document.location.href='?objet=administration&amp;action=deleteTheWholeBdd&amp'" alt="Supprimer la base de donnée">Supprimer la base de donnée</a></p>
            </div>
        </section>

EOT;
    return $html;
    }

    //-----------------------------

    // 6-2- 'importer un DUMP':
    //-----------------------------

    //-----------------------------

    // 6-3- 'gérer les fichiers de LOG':
    //-----------------------------

    public static function displayLogsManagement($logsName, $log=null, $logSelected=null)
    {
        $path = './media/infos/';
        $html = '<h2>Gestion des fichiers de logs</h2>'."\n";

        $html .= '<h3>Liste des fichiers de log disponibles</h3>'."\n"."<ul>\n";
        foreach ($logsName as $key => $value){
            $html .= '<li>'.$value.'  <a href="?objet=administration&action=displayLog&filename='.$value.'" alt="afficher un fichier de log"><img src="media/img/icons/view.png" alt="Voir le fichier" title="Voir le fichier" /></a> - <a href="'.$path.$value.'" download="'.$value.'" alt="Télécharger le fichier '.$value.'"><img src="media/img/icons/download.png" alt="Télécharger le fichier" title="Télercharger le fichier" /></a></li>'."\n";
        }
        $html .= "</ul>\n".'<h3>Contenu du fichier : '. $logSelected .'</h3>'."\n";
        
        if ($log){
            $html .= '<div id="logsContent"><table class="tabLogs">'."\n";

            //affichage de l'en-tête du tableau
            $html .= '<tr>'."\n";
            $html .= '<th>line<th>'."\n";
            $head = explode(' ', $log[0]);
            foreach ($head as $key => $value) {
                $html .= '<th>'.$value.'</th>'."\n";
            }
            $html .= '</tr>'."\n";

            //affichage du contenu
            foreach ($log as $key => $value) {
                if ($key != 0){
                    $content = explode(' ', $value);
                    $html .= '<tr>'."\n";
                    $html .= '<td>'.$key.'<td>'."\n";
                    foreach ($content as $key => $txt) {
                        if ((strrchr($txt, ';')) != false){ //spécial pour les fichiers conservant le contenu supprimé
                            $txt = preg_replace('#(;)#', '<br/>', $txt); //si un ; existe, on le remplace par un saut de ligne pour l'affichage correct
                        }
                        
                        $html .= '<td>'.$txt.'</td>'."\n";
                    }
                    $html .= '</tr>'."\n";
                }
            }
            $html .= '</table></div>'."\n";
        } else{
            $html .= '<p>Aucun fichier selectionné</p>'."\n";
        }
        return $html;
    }
    //-----------------------------
}