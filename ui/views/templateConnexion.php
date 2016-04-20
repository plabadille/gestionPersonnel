<?php // echo password_hash("test", PASSWORD_DEFAULT); ?> 
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Blog prog pour le web</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="ui/css/design.css">
</head>
<body>
    <header>
        <h1>Application de gestion du personnel d'un service</h1>
    </header>
    <div id="conteneur">
        <main>
            <h2>Accueil</h2>
            <p class="pPublicHome">Pour accéder à cette application, l'authentification est obligatoire. <br />Si vous avez oublié votre mot de passe ou vos identifiant, veuillez contacter votre responsable hierarchique.</p>
            <?php echo $prezAuth ?>
            <p class="pPublicHome">superAdmin : Login : 111, mdp: test.</p>
            <p class="pPublicHome">admin : Login : 1, mdp: test.</p>
            <p class="pPublicHome">cadre : Login : 2, mdp: test.</p>
            <p class="pPublicHome">secretaire : Login : 3, mdp: test.</p>
            <p class="pPublicHome">militaire : Login : 4, mdp: test.</p>
        </main>
    </div>
    <footer><p> ~ M1-DNR2i 2015-2016 - Projet tuteuré - Pierre Labadille ~ </p></footer>

    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <!-- Champ date auto pour les formulaires -->
    <!-- https://jqueryui.com/datepicker/#date-formats -->
    <script>
    $(function() {
        $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    });
    </script>
</body>
</html> 