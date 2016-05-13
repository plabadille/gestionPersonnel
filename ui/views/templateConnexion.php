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
        <div id="verticalAlign">
            <?php echo $prezAuth ?>
        </div>
        <!-- <p class="pPublicHome">superAdmin : Login : 111, mdp: test.</p>
        <p class="pPublicHome">admin : Login : 1, mdp: test.</p>
        <p class="pPublicHome">cadre : Login : 2, mdp: test.</p>
        <p class="pPublicHome">secretaire : Login : 3, mdp: test.</p>
        <p class="pPublicHome">militaire : Login : 4, mdp: test.</p> -->
    </div>
    <footer><p> ~ M1-DNR2i 2015-2016 - Projet tuteuré - Pierre Labadille ~ </p></footer>

    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</body>
</html> 