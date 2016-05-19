<?php
namespace PLabadille\GestionDossier\Dossier;

use PLabadille\GestionDossier\Controller\AccessControll;

//--------------------
//ORGANISATION DU CODE
//--------------------
# 0- Fonctions génériques
# 1- Module mon dossier
# 2- Module de gestion et ajout de dossier
# 3- Module de gestion de promotion et retraite
//--------------------

#Gère l'affichage des Dossiers.
#Attention, la gestion d'affichage des formulaires est gérées directement par DossalierForm par le biais de templates.
class DossierHtml 
{
    //--------------------
    //1-Fonctions génériques
    //--------------------
    #Fonction générique utilisée par les générateurs de liste de dossier, elle renvoit les boutons de navigation supp selon les droits de l'utilisateur.
    public static function commonListBouton($dossier)
    {
            $boutons = '';
            $typeBouton = 'editFolderInformation';
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            $right = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);

            $boutons .= ($right) ? '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=editerDossier&amp;id=' . $dossier->getMatricule() . '"><img src="media/img/icons/edit.png" alt="Editer" title="Editer" /></a>' : null;
            
            $typeBouton = 'canArchiveAFolder';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutons .= ($right) ? '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=archiverDossier&amp;id=' . $dossier->getMatricule() . '"><img src="media/img/icons/archive.png" alt="Archiver" title="Archiver" /></a>' : null;

            $typeBouton = 'canRetireAFolder';
            $right = AccessControll::afficherBoutonNavigation($typeBouton);

            $boutons .= ($right) ? '&nbsp;-&nbsp; <a href="?objet=dossier&amp;action=retraiterDossier&amp;id=' . $dossier->getMatricule() . '"><img src="media/img/icons/retirement.png" alt="Retraiter" title="Retraiter" /></a>' : null;

            return $boutons;
    }

    //--------------------
    //1-module mon dossier
    //--------------------
    // 1-1- 'seeOwnFolderModule':
    public static function viewUserFolder($dossier)
    {
        //4-affichage dossier
        $html = <<<EOT
            <h2>Dossier de {$dossier['informations']->getNom()} {$dossier['informations']->getPrenom()}, matricule : {$dossier['informations']->getMatricule()}</h2>
            <div id="completeFolder">
                <h3>Informations personelles:</h3>
                <div id="contentFolder">
                    <p>Date de naissance : {$dossier['informations']->getDateNaissance()}</p>
                    <p>Genre : {$dossier['informations']->getGenre()}</p>
                    <p>Tel1 : {$dossier['informations']->getTel1()}</p>
                    <p>Tel 2 : {$dossier['informations']->getTel2()}</p>
                    <p>Email : {$dossier['informations']->getEmail()}</p>
                    <p>Adresse : {$dossier['informations']->getAdresse()}</p>
                    <p>Date de recrutement : {$dossier['informations']->getDateRecrutement()}</p>
               </div>\n\n
EOT;
        $html .= self::afficheAffectations($dossier['casernes']) . "\n\n";
        $html .= self::afficheAppartenances($dossier['regiments']) . "\n\n";
        $html .= self::afficheGradesDetenu($dossier['grades']) . "\n\n";
        $html .= self::afficheDiplomesPossede($dossier['diplomes']); 
        return $html;
    }

    // 1-2- 'editOwnFolderPersonalInformation':
    #géré par template dans DossierForm

    //--------------------
    //2-module gestion et ajout de dossier
    //--------------------
    // 2-1- 'listCreatedFolder':
    public static function listCreatedFolderHtml($dossier) 
    {
        $html = <<<EOT
            <h2>Liste des militaires que vous avez ajouté</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercherCreatedFolder">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchListCreatedDossier" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        foreach ($dossier as $dossier) {
            //affichage des boutonsNavigation en fonction des droits:
            $boutonsNav = self::commonListBouton($dossier);

            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a> {$boutonsNav}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    // 2-2- 'listAllFolder':

    #Permet l'affichage de tout les dossiers (ou d'une partie)
    #appel afficheListe pour les afficher.
    public static function toHtml($dossier) 
    {
        $html = <<<EOT
            <h2>Liste des militaires</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercher">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchListDossier" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;
        foreach ($dossier as $dossier) {
            //affichage des boutonsNavigation en fonction des droits:
            $boutonsNav = self::commonListBouton($dossier);
            
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a> {$boutonsNav}
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    #appelé par toHtml pour afficher tous les dossier
    public static function afficheListe($dossier) 
    {
        $html = $dossier->getNom() . ' ' . $dossier->getPrenom();
        return $html;
    }

    // 2-3- 'seeCreatedFolder':
    #to do
    
    // 2-4- 'seeAllFolder':

    #Permet d'afficher uniquement un dossier
    public static function afficheUnDossier($dossier) 
    {
        //affichage des boutonsNavigation en fonction des droits:
        $createBy = $dossier->getWhoCreateFolder();
        $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);

        //1-edit bouton
        $typeBouton = 'editFolderInformation';
        $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
        $editBouton = ($rightEdit) ? '<a href="?objet=dossier&amp;action=editerDossier&amp;id=' . $dossier->getMatricule() . '" alt="Editer Informations" title="Editer Informations"><img src="media/img/icons/edit.png" alt="Editer Informations" /></a>&nbsp;&nbsp;' : null;

        //2-affichage dossier
        $html = <<<EOT
            <h2>Dossier de {$dossier->getNom()} {$dossier->getPrenom()}, matricule : {$dossier->getMatricule()}</h2>
            <div id="completeFolder">
                <h3>{$editBouton}Informations personnelles :</h3>
                <div id="contentFolder">
                    <p>Date de naissance : {$dossier->getDateNaissance()}</p>
                    <p>Genre : {$dossier->getGenre()}</p>
                    <p>Tel1 : {$dossier->getTel1()}</p>
                    <p>Tel 2 : {$dossier->getTel2()}</p>
                    <p>Email : {$dossier->getEmail()}</p>
                    <p>Adresse : {$dossier->getAdresse()}</p>
                    <p>Date de recrutement : {$dossier->getDateRecrutement()}</p>
                </div>
EOT;
        return $html;
    }

    #Permet d'afficher les affectations liées à un dossier
    public static function afficheAffectations($affectations, $dossier = null) 
    {
        if(isset($dossier)){
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            //2-addElement Boutons
            $typeBouton = 'addElementToAFolder';
            $rightAddElement = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
            //3-Affichage boutons
            $addElementBoutons = null;
            if ( $rightAddElement ){
                $addElementBoutons = '&nbsp;&nbsp; <a href="?objet=dossier&amp;action=ajouterAffectation&amp;id=' . $dossier->getMatricule() . '" alt="Ajouter affectation" title="Ajouter affectation"><img src="media/img/icons/add.png" alt="Ajouter affectation" /></a>&nbsp;&nbsp;';
            }
        } else{
            $addElementBoutons = null;
        }
        $html = "</br>\n<h3>" . $addElementBoutons . "Liste des affectations :</h3> \n";

        if (!empty($affectations)){
            //sécurité bouton suprimer grade:
            $typeBouton = 'deleteFolderInformation';
            $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);
            //bouton grade actuel (avec lien JS pour demander confirmation)
            $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer cette affectation de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprAffectation&amp;id=' . $affectations['0']['nb'] . '\'" alt="Supprimer Affectation" title="Supprimer Affectation"><img src="media/img/icons/delete.png" alt="Supprimer Affectation" /></a>&nbsp;&nbsp;' : null;

            $html .= "<div id=\"contentAffectations\"> \n <h4>Affectation actuelle :</h4> \n ";
            $html .= "<p>" . $suprBouton . "Caserne " . $affectations['0']['nom'] . " depuis le " . $affectations['0']['date_affectation'] . "</p> \n";

            $html .= "<h4>Ancienne(s) affectation(s) :</h4> \n";
            if (isset($affectations['1'])){
                foreach ($affectations as $key => $liste) {
                    if ($key > 0){
                        //bouton anciens grades
                        $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer cette affectation de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprAffectation&amp;id=' . $affectations[$key]['nb'] . '\'" alt="Supprimer Affectation" title="Supprimer Affectation"><img src="media/img/icons/delete.png" alt="Supprimer Affectation" /></a>&nbsp;&nbsp;' : null;

                        $html .= "<p>" . $suprBouton . "Caserne " . $liste['nom'] . " le " . $liste['date_affectation'] . "</p> \n";
                    }
                }
            } else{
                $html .= "<p>Aucune autre affectation</p> \n";
            }
        } else{
            $html .= "<div id=\"contentAffectations\"> \n <p>Aucune affectation</p> \n";
        }
        $html .=  "</div> \n";

        return $html;
    }

    #Permet d'afficher les régiments d'appartenances liées à un dossier
    public static function afficheAppartenances($appartenances, $dossier = null) 
    {
        if(isset($dossier)){
            //affichage des boutonsNavigation en fonction des droits:
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            //2-addElement Boutons
            $typeBouton = 'addElementToAFolder';
            $rightAddElement = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
            //3-Affichage boutons
            $addElementBoutons = null;
            if ( $rightAddElement ){
                $addElementBoutons = '&nbsp;&nbsp; <a href="?objet=dossier&amp;action=ajouterAppartenanceRegiment&amp;id=' . $dossier->getMatricule() . '" alt="Ajouter régiment" ><img src="media/img/icons/add.png" alt="Ajouter régiment" title="Ajouter régiment" /></a>&nbsp;&nbsp;';
            }
        } else{
            $addElementBoutons = null;
        }

        $html = "</br>\n<h3>" . $addElementBoutons . "Liste des régiments d'appartenances :</h3> \n";
        if (!empty($appartenances)){
            //sécurité bouton suprimer grade:
            $typeBouton = 'deleteFolderInformation';
            $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);

            //bouton grade actuel (avec lien JS pour demander confirmation)
            $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce régiment de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprRegimentAppartenance&amp;id=' . $appartenances['0']['nb'] . '\'" alt="Supprimer appartenance régiment" title="Supprimer régiment"><img src="media/img/icons/delete.png" alt="Supprimer appartenance régiment" /></a>&nbsp;&nbsp;' : null;

            $html .= "<div id=\"contentAppartenances\"> \n <h4>Appartenance actuelle :</h4> \n";
            $html .= "<p>" . $suprBouton . "Régiment: " . $appartenances['0']['id'] . " depuis le " . $appartenances['0']['date_appartenance'] . "</p> \n";

            $html .= "<h4>Ancienne(s) appartenance(s) :</h4> \n";
            if (isset($appartenances['1'])){
                foreach ($appartenances as $key => $liste) {
                    if ($key > 0){
                        //bouton anciens grades
                        $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce régiment de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprRegimentAppartenance&amp;id=' . $appartenances[$key]['nb'] . '\'" alt="Supprimer appartenance régiment" title="Supprimer appartenance régiment"><img src="media/img/icons/delete.png" alt="Supprimer appartenance régiment" /></a>&nbsp;&nbsp;' : null;

                        $html .= "<p>" . $suprBouton . "Régiment: " . $liste['id'] . " le " . $liste['date_appartenance'] . "</p> \n";
                    }
                }
            } else{
                $html .= "<p>Aucun autre régiment</p> \n";
            }
        } else{

            $html .= "<div id=\"contentAppartenances\"> \n <p>Aucun régiment d'appartenance</p> \n";
        }
        $html .= "</div> \n";

        return $html;
    }

    #Permet d'afficher les grades detenu liés à un dossier
    public static function afficheGradesDetenu($grades, $dossier = null) 
    {
        if(isset($dossier)){
            //affichage des boutonsNavigation en fonction des droits:
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            //2-addElement Boutons
            $typeBouton = 'addElementToAFolder';
            $rightAddElement = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
            //3-Affichage boutons
            $addElementBoutons = null;
            if ( $rightAddElement ){
                $addElementBoutons = '&nbsp;&nbsp; <a href="?objet=dossier&amp;action=ajouterGradeDetenu&amp;id=' . $dossier->getMatricule() . '" alt="Ajouter grade" title="Ajouter grade"><img src="media/img/icons/add.png" alt="Ajouter grade" /></a>&nbsp;&nbsp;';
            }
        } else{
            $addElementBoutons = null;
        }

        $html = "</br>\n<h3>" . $addElementBoutons . "Liste des grades du militaire :</h3> \n";
        if (!empty($grades)){
            //sécurité bouton suprimer grade:
            $typeBouton = 'deleteFolderInformation';
            $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);
            //bouton grade actuel (avec lien JS pour demander confirmation)
            $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce grade de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprGradeDetenu&amp;id=' . $grades['0']['num'] . '\'" alt="Supprimer grade détenu" title="Supprimer grade détenu"><img src="media/img/icons/delete.png" alt="Supprimer grade détenu" /></a>&nbsp;&nbsp;' : null;

            $html .= "<div id=\"contentGrades\"> \n <h4>Grade actuel :</h4> \n";
            $html .= "<p>" . $suprBouton . "Grade: " . $grades['0']['grade'] . " depuis le " . $grades['0']['date_promotion'] . "</p> \n";

            $html .= "<h4>Ancien(s) grade(s) :</h4> \n";
            if (isset($grades['1'])){
                foreach ($grades as $key => $liste) {
                    if ($key > 0){
                        //bouton anciens grades
                        $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce grade de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprGradeDetenu&amp;id=' . $grades[$key]['num'] . '\'" alt="Supprimer grade détenu" title="Supprimer grade détenu"><img src="media/img/icons/delete.png" alt="Supprimer grade détenu" /></a>&nbsp;&nbsp;' : null;

                        $html .= "<p>" . $suprBouton . "Grade: " . $liste['grade'] . " le " . $liste['date_promotion'] . "</p> \n";
                    }
                }
            } else{
                $html .= "<p>Aucun autre grade</p> \n";
            }
        } else{
            $html .= "<div id=\"contentGrades\"> \n <p>Aucun grade</p> \n";
        }
        $html .= "</div> \n";

        return $html;
    }

    #Permet d'afficher les diplomes possede liés à un dossier
    public static function afficheDiplomesPossede($diplomes, $dossier = null)  
    {
        if(isset($dossier)){
            //affichage des boutonsNavigation en fonction des droits:
            $createBy = $dossier->getWhoCreateFolder();
            $creatorIsLog = AccessControll::checkIfConnectedIsAuthor($createBy);
            //2-addElement Boutons
            $typeBouton = 'addElementToAFolder';
            $rightAddElement = AccessControll::afficherBoutonNavigation($typeBouton, $creatorIsLog);
            //3-Affichage boutons
            $addElementBoutons = null;
            if ( $rightAddElement ){
                $addElementBoutons = '&nbsp;&nbsp; <a href="?objet=dossier&amp;action=ajouterDiplomePossede&amp;id=' . $dossier->getMatricule() . '" alt="Ajouter diplôme" title="Ajouter diplôme"><img src="media/img/icons/add.png" alt="Ajouter diplôme" /></a>&nbsp;&nbsp;';
            }
        } else{
            $addElementBoutons = null;
        }

        $html = "</br>\n<h3>" . $addElementBoutons . "Liste des diplômes possédés :</h3> \n <div id=\"contentDiplomes\"> \n ";

        if (!empty($diplomes)){
            //sécurité bouton suprimer grade:  (avec lien JS pour demander confirmation)
            $typeBouton = 'deleteFolderInformation';
            $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);

            foreach ($diplomes as $key => $liste) {
                $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer ce diplome de ce dossier ?\')) document.location.href=\'?objet=dossier&amp;action=suprDiplomePossede&amp;id=' . $diplomes[$key]['num'] . '\'" alt="Supprimer diplome possédé" title="Supprimer diplome possédé"><img src="media/img/icons/delete.png" alt="Supprimer diplome possédé" /></a>&nbsp;&nbsp;' : null;

                $html .= "<p>" . $suprBouton . $liste['intitule'] . " (" . $liste['acronyme'] . ") obtenu le " . $liste['date_obtention'] . "</p> \n";
            }
        } else{
            $html .= "<div id=\"contentDiplomexs\"> \n <p>Aucun diplôme</p> \n";
        }
        $html .= "</div> \n </div> \n";

        return $html;
    }

    // 2-5 'createFolder':
    #géré par template dans DossierForm

    // 2-6- 'addElementToAFolder':
    #géré par template dans DossierForm

    // 2-7- 'editInformationIfAuthor':
    #géré par template dans DossierForm

    // 2-8- 'editInformation':
    #géré par template dans DossierForm

    // 2-9- 'deleteInformation':
    #directement en 2-4-

    // 2-10 'useFileToAddFolders':
    public static function viewParsingForm($info = null, $content = null, $filename = null)
    {
        if (!is_null($filename) || !is_null($content)){
            $type = explode('.', $filename);
            $prezSample = '<h3>Exemple de modélisation de fichier .'.$type['1'].'</h3>'."\n";
            $prezSample .= '<div id="logsContent"><table class="tabSample">'."\n";

            //affichage de l'en-tête du tableau
            $prezSample .= '<tr>'."\n".'<th>line</th>'."\n";
            $prezSample .= '<th>'.$filename.'</th>'."\n".'</tr>'."\n";

            //affichage du contenu
            $line = 0;
            foreach ($content as $key => $value) {
                $prezSample .= '<tr>'."\n".'<td>'.$line.'</td>'."\n";
                $prezSample .= '<td><xmp>'.$value.'</xmp></td>'."\n".'</tr>'."\n";
                $line++;
            }
            $prezSample .= '</table></div>'."\n";
        } else{
            $prezSample = null;
        }
        if (!is_null($info)){
            $prezInfo = '<div id="infoAction">'."\n";
            if (is_array($info)){ //si $info est un tableau c'est que le système à accepté le type de fichier et qu'il en a bien reçu un.
                if ($info['success'] === 0){
                    $prezInfo .= '<p>Aucune ligne n\'a été insérée en base de données.</p>'."\n";
                } elseif ($info['success'] === 1) {
                    $prezInfo .= '<p>'.$info['success'].' ligne a été insérée en base de données.</p>'."\n";
                } else{
                    $prezInfo .= '<p>'.$info['success'].' lignes ont été insérées en base de données.</p>'."\n";
                }
                if(!empty($info['error'])){
                    $prezInfo .= '<p><b>Erreur de validation/fichier :</b></p>'."\n";
                    foreach ($info['error'] as $key => $value) {
                        $prezInfo .= '<p>'.$value.'</p>'."\n";
                    }
                }
                if(!empty($info['doublonError'])){
                    $prezInfo .= '<p><b>Erreur de doublon :</b></p>'."\n";
                    foreach ($info['doublonError'] as $key => $value) {
                        $prezInfo .= '<p>'.$value.'</p>'."\n";
                    }
                }
            } else{ //le système n'a pas reçu le fichier ou n'accepte pas le type du fichier
                $prezInfo .= '<p>'.$info.'</p>'."\n";
            }
            $prezInfo .= '</div>'."\n";
        } else{
            $prezInfo = null;
        }
        $path = './media/samples/';
        $html = <<<EOT
            <h2>Ajouter des dossiers avec un fichier de donnée structurée</h2>
            <div class="pads">
                <p>Les fichiers sont ajoutés dans la base à la volée (si certaines entrées sont correctes et d'autres non, les entrées correctes seront tout de même importé). Après l'importation, le système vous retournera les numéros des lignes contenant des erreurs et n'ayant pas été importé le cas échéant, ainsi que le nombre de dossier correctement importé. Veuillez enfin noter que le système vérifie également les doublons.</p>
            </div>

            {$prezInfo}

            <form id="formSaisieDossier" enctype="multipart/form-data" method="post" action="?objet=dossier&action=parseAndAddsFolders">
                    <label for="importedFile">Ajouter un fichier de donnée structurée (CSV, XML, JSON)</label>
                    <input type="file" name="importedFile" />  
                    <input id="boutonOk" type="submit" value="Envoyer" >
            </form>

            <h3>Informations sur la forme de modélisation requise:</h3>
            <div class="pads">
                <p>Veuillez noter que les dossiers importer par cette méthode doivent respecter les conditions d'ajouts actuelles (concordence des dates, adresse email valide, etc).</p>
            </div>
            <ul>
                <li>Modélisation CSV avec séparateur "," <a href="?objet=dossier&action=displaySample&filename=sample1.csv" alt="afficher l'exemple"><img src="media/img/icons/view.png" alt="Voir le fichier" title="Voir le fichier" /></a> - <a href="{$path}model1.csv" download="model1.csv" alt="Télécharger le fichier"><img src="media/img/icons/download.png" alt="Télécharger le fichier" title="Télercharger le fichier" /></a></li>
                <li>Modélisation CSV avec séparateur ";" <a href="?objet=dossier&action=displaySample&filename=sample2.csv" alt="afficher l'exemple"><img src="media/img/icons/view.png" alt="Voir le fichier" title="Voir le fichier" /></a> - <a href="{$path}model2.csv" download="model2.csv" alt="Télécharger le fichier"><img src="media/img/icons/download.png" alt="Télécharger le fichier" title="Télercharger le fichier" /></a></li>
                <li>Modélisation XML <a href="?objet=dossier&action=displaySample&filename=sample.xml" alt="afficher l'exemple"><img src="media/img/icons/view.png" alt="Voir le fichier" title="Voir le fichier" /></a> - <a href="{$path}model.xml" download="model.xml" alt="Télécharger le fichier"><img src="media/img/icons/download.png" alt="Télécharger le fichier" title="Télercharger le fichier" /></a></li>
                <li>Modélisation JSON <a href="?objet=dossier&action=displaySample&filename=sample.json" alt="afficher l'exemple"><img src="media/img/icons/view.png" alt="Voir le fichier" title="Voir le fichier" /></a> - <a href="{$path}model.json" download="model.json" alt="Télécharger le fichier"><img src="media/img/icons/download.png" alt="Télécharger le fichier" title="Télercharger le fichier" /></a></li>
            </ul>

            {$prezSample}



EOT;
        return $html;
    }

    //--------------------
    //3-module gestion promotion et retraite
    //--------------------
    // 3-1- 'listEligible':

    public static function afficheDossiersEligiblesPromotion($dossier) 
    {
        $html = <<<EOT
            <h2>Liste des militaires éligibles à une promotion</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercherEligiblesPromotion">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchListEligiblePromotion" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;

        foreach ($dossier as $dossier) {
            //affichage des boutons selon droits:
            $boutonsNav = self::commonListBouton($dossier);

            $liste = self::afficheListe($dossier);

            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} - Eligible au grade : {$dossier->getGrade()} - </a> {$boutonsNav}  
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    public static function afficheDossiersEligiblesRetraite($dossier) 
    {
        $html = <<<EOT
            <h2>Liste des militaires éligibles à la retraite</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?objet=dossier&action=rechercherEligiblesRetraite">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" autocomplete="off" id="searchListEligibleRetraite" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;

        foreach ($dossier as $dossier) {
            //affichage des boutons selon droits:
            $boutonsNav = self::commonListBouton($dossier);

            $liste = self::afficheListe($dossier);

            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a> {$boutonsNav}  
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    } 

    // 3-2- 'editEligibleCondition':
    #géré par template dans DossierForm
    public static function viewConditionsList($conditionsPromotion, $conditionsRetraite, $liste)
    {
        //1-Affichage Conditions
        $html = <<<EOT
            <h2>Liste des conditions d'éligibilité</h2>

            <h3>Conditions de retraite:</h3>
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>#</th>
                    <th>Grade</th>      
                    <th>Service effectif</th>
                    <th>Âge</th>
                    <th>Action</th>
                </tr>
EOT;
        foreach ($conditionsRetraite as $key => $tab) {
            $html .= '<tr>' . "\n";
            foreach ($tab as $key => $value) {
                //Boutons
                $typeBouton = 'editEligibleCondition';
                $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
                $editBouton = ($rightEdit) ? '<a href="?objet=dossier&amp;action=editConditionRetraite&amp;id=' . $tab['id'] . '" alt="édition de la condition de retraite" title="édition de la condition de retraite"><img src="media/img/icons/edit.png" alt="édition de la condition de retraite" title="édition de la condition de retraite" /></a>' : null;

                $typeBouton = 'suprEligibleCondition';
                $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);
                $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer cette condition de retraite ?\')) document.location.href=\'?objet=dossier&amp;action=suprConditionRetraite&amp;id=' . $tab['id'] . '\'" alt="supression de la condition de retraite" title="supression de la condition de retraite"><img src="media/img/icons/delete.png" alt="supression de la condition de retraite" title="supression de la condition de retraite" /></a>' : null;

                $denominationGrade = ( $key == 'idGrade' ? ': ' . $liste['nomGrade'][$value] : null );
                $html .= '<td>' . $value . $denominationGrade . '</td>' . "\n";
            }

            $html .= '<td>' . $editBouton . ' - ' . $suprBouton . '</td>' . "\n";
            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";

        $html .= <<<EOT
            <h3>Conditions de promotion:</h3>
            <table class="tab" border="1" style="width:100%">
                <tr>
                    <th>#</th>
                    <th>Grade</th>
                    <th>Service FA</th>      
                    <th>Service GN</th>
                    <th>Service SOE</th>
                    <th>Service grade</th>
                    <th>Diplôme</th>
                    <th>Diplôme Sup1</th>
                    <th>Diplôme Sup2</th>
                    <th>Action</th>
                </tr>
EOT;
        foreach ($conditionsPromotion as $key => $tab) {
            $html .= '<tr>' . "\n";
            foreach ($tab as $key => $value) {
                //Boutons
                $typeBouton = 'editEligibleCondition';
                $rightEdit = AccessControll::afficherBoutonNavigation($typeBouton);
                $editBouton = ($rightEdit) ? '<a href="?objet=dossier&amp;action=editConditionPromotion&amp;id=' . $tab['id'] . '" alt="Edition de la condition de promotion" title="Edition de la condition de promotion"><img src="media/img/icons/edit.png" alt="Edition de la condition de promotion" title="Edition de la condition de promotion" /></a>' : null;

                $typeBouton = 'suprEligibleCondition';
                $rightSupr = AccessControll::afficherBoutonNavigation($typeBouton);
                $suprBouton = ($rightSupr) ? '<a href="javascript:if(confirm(\'Cette action est irréversible, êtes-vous sûr de vouloir supprimer cette condition de promotion ?\')) document.location.href=\'?objet=dossier&amp;action=suprConditionPromotion&amp;id=' . $tab['id'] . '\'" alt="supression de la condition de promotion" title="supression de la condition de promotion"><img src="media/img/icons/delete.png" alt="supression de la condition de promotion" title="supression de la condition de promotion" /></a>' : null;

                $denominationGrade = ( $key == 'idGrade' ? ': ' . $liste['nomGrade'][$value] : null );
                if ( $key == 'diplome' || $key == 'diplomeSup1' || $key == 'diplomeSup2' ){
                    $denominationDiplome = (!empty($value) ?  ': ' . $liste['nomDiplome'][$value] : null);
                } else{
                    $denominationDiplome = null;
                }
                $html .= '<td>' . $value . $denominationGrade . $denominationDiplome . '</td>' . "\n";
            }

            $html .= '<td>' . $editBouton . ' - ' . $suprBouton . '</td>' . "\n";
            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";

        return $html;

    }
    // 3-3- 'addEligibleCondition':
    #géré par template dans DossierForm

    // 3-4- 'canRetireAFolder':
    #to do?

    // 3-5- 'editEligibleEmailContent':
    #géré par template dans DossierForm

    // 3-6- 'uploadFileForMail':
    #géré par template dans DossierForm

    // 3-7- 'changePieceJointeForEligibleMail':
    #géré par template dans DossierForm
}