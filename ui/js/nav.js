$(document).ready( function () {
    //1------------------------------
    //Menu de navigation
    //-------------------------------

    // On cache les sous-menus :
    $("#navigation ul.subMenu").hide(); 

    // On sélectionne tous les items de liste portant la classe "toggleSubMenu"
    // et on remplace l'élément span qu'ils contiennent par un lien :
    $("#navigation li.toggleSubMenu h3").each( function () {
        $(this).replaceWith('<a href="" title="Afficher le sous-menu">' + $(this).text() + '<\/a>') ;
    });

    // On modifie l'évènement "click" sur les liens dans les items de liste
    // qui portent la classe "toggleSubMenu" :
    $("#navigation li.toggleSubMenu .moduleConteneur > a").click( function () {
        // Si le sous-menu était déjà ouvert, on le referme :
        if ($(this).next("ul.subMenu:visible").length != 0) {
            $(this).next("ul.subMenu").slideUp("normal", function() {
                $(this).parent().removeClass("open")
            });
        }
        // Si le sous-menu est caché, on ferme les autres et on l'affiche :
        else {
            $("#navigation ul.subMenu").slideUp("normal", function() {
                $(this).parent().removeClass("open")
            });
            $(this).next("ul.subMenu").slideDown("normal", function() {
                $(this).parent().addClass("open")
            });
        }
        // On empêche le navigateur de suivre le lien :
        return false;
    });

    //2-------------------------------------
    //infobulle dans les formulaires
    //auteurs Kevin Liew / Didier Mouronval
    //--------------------------------------

    // Sélectionner tous les liens ayant l'attribut rel valant tooltip
    $('a[rel=tooltip]').mouseover(function(e) {
 
        // Récupérer la valeur de l'attribut title et l'assigner à une variable
        var tip = $(this).attr('title');   
 
        // Supprimer la valeur de l'attribut title pour éviter l'infobulle native
        $(this).attr('title','');
 
        // Insérer notre infobulle avec son texte dans la page
        $(this).append('<div id="tooltip"><div class="tipHeader"></div><div class="tipBody">' + tip + '</div><div class="tipFooter"></div></div>');    
 
        // Ajuster les coordonnées de l'infobulle
        $('#tooltip').css('top', e.pageY + 10 );
        $('#tooltip').css('left', e.pageX + 20 );
 
        // Faire apparaitre l'infobulle avec un effet fadeIn
        $('#tooltip').fadeIn('500');
        $('#tooltip').fadeTo('10',0.8);
 
    }).mousemove(function(e) {
 
        // Ajuster la position de l'infobulle au déplacement de la souris
        $('#tooltip').css('top', e.pageY + 10 );
        $('#tooltip').css('left', e.pageX + 20 );
 
    }).mouseout(function() {
 
        // Réaffecter la valeur de l'attribut title
        $(this).attr('title',$('.tipBody').html());
 
        // Supprimer notre infobulle
        $(this).children('div#tooltip').remove();
 
    });

});