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
        <nav>
            <h2>Compte</h2>
            <?php echo $prezAuth ?>
            <h2>Navigation</h2>
            <ul id="navigation">
                <?php echo $navigation ?>
            </ul>
        </nav>
        <main>
            <?php echo $prez ?>
        </main>
    </div>
    <footer><p> ~ M1-DNR2i 2015-2016 - Projet tuteuré - Pierre Labadille ~ </p></footer>

    <script src="//code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <!-- Champ date auto pour les formulaires -->
    <!-- https://jqueryui.com/datepicker/#date-formats -->
    <script>
    $(function() {
        $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    });
    </script>
    <script src="ui/js/autoCompleteListDossier.js"></script>
</body>
</html> 