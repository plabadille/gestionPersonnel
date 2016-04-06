<?php
namespace PLabadille\GestionDossier\Dossier;

#Gère l'affichage des Dossiers.
class DossierHtml {
    #Permet l'affichage de tout les dossiers (ou d'une partie)
    #appel afficheListe pour les afficher.
    public static function toHtml($dossier) {
        $html = <<<EOT
            <h2>Liste des militaires</h2>
                <form id="formSearch" enctype="multipart/form-data" method="post" action="index.php?action=rechercher">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Saisir un matricule ou un nom"/>

                    <input id="boutonOk" type="submit" value="Envoyer" >
                </form>
            <ul id="listeDossier">
EOT;

        foreach ($dossier as $dossier) {
            $liste = self::afficheListe($dossier);
            $html .= <<<EOT
                <li>
                    <a href="?objet=dossier&amp;action=voir&amp;id={$dossier->getMatricule()}">{$liste} -</a>   &nbsp;-&nbsp; <a href="?objet=dossier&amp;action=editerDossier&amp;id={$dossier->getMatricule()}">Editer</a>
                </li>
EOT;
        }

        $html .= "  </ul>\n";
        return $html;
    }

    #appelé par toHtml pour afficher tous les dossier
    public static function afficheListe($dossier) {
        $html = $dossier->getNom() . ' ' . $dossier->getPrenom();
        return $html;
    }

    #Permet d'afficher uniquement un dossier
    public static function afficheUn($dossier) {
        $html = <<<EOT
            <h3>Dossier de {$dossier->getNom()} {$dossier->getPrenom()}, matricule : {$dossier->getMatricule()}</h3>
                <p>Date de naissance : {$dossier->getDateNaissance()}</p>
                <p>Genre : {$dossier->getGenre()}</p>
                <p>Tel1 : {$dossier->getTel1()}</p>
                <p>Tel 2 : {$dossier->getTel2()}</p>
                <p>Email : {$dossier->getEmail()}</p>
                <p>Adresse : {$dossier->getAdresse()}</p>
                <p>Date de recrutement : {$dossier->getDateRecrutement()}</p>
EOT;
        return $html;
    }
}