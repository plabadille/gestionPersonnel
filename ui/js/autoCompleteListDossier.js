//1- autocomplete recherche
$('#searchListDossier').autocomplete({
    source: function(term, response){
        // console.log(term);
        $.getJSON('?objet=dossier&action=autoComplete&type=listDossier&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                console.log(field.nom);
            });
            response(noms);
        });
    },
    close: function(event, ui) { //permet de lancer la recherche après le click
        document.getElementById('formSearch').submit();
    }
});

$('#searchListCreatedDossier').autocomplete({
    source: function(term, response){
        // console.log(term);
        $.getJSON('?objet=dossier&action=autoComplete&type=listCreatedDossier&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                // console.log(field.nom);
            });
            response(noms);
        });
    },
    close: function(event, ui) { //permet de lancer la recherche après le click
        document.getElementById('formSearch').submit();
    }
});

$('#searchListEligiblePromotion').autocomplete({
    source: function(term, response){
        // console.log(term);
        $.getJSON('?objet=dossier&action=autoComplete&type=listEligiblePromotion&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                // console.log(field.nom);
            });
            response(noms);
        });
    },
    close: function(event, ui) { //permet de lancer la recherche après le click
        document.getElementById('formSearch').submit();
    }
});

$('#searchListEligibleRetraite').autocomplete({
    source: function(term, response){
        // console.log(term);
        $.getJSON('?objet=dossier&action=autoComplete&type=listEligibleRetraite&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                // console.log(field.nom);
            });
            response(noms);
        });
    },
    close: function(event, ui) { //permet de lancer la recherche après le click
        document.getElementById('formSearch').submit();
    }
});

$('#searchListEligibleRetraite').autocomplete({
    source: function(term, response){
        // console.log(term);
        $.getJSON('?objet=dossier&action=autoComplete&type=listEligibleRetraite&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                // console.log(field.nom);
            });
            response(noms);
        });
    },
    close: function(event, ui) { //permet de lancer la recherche après le click
        document.getElementById('formSearch').submit();
    }
});

$('#searchDossierWithOutAccount').autocomplete({
    source: function(term, response){
        $.getJSON('?objet=administration&action=autoComplete&type=listeSansCompte&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                console.log(field.nom);
            });
            response(noms);
         });
        }
});

$('#searchDossierWithAccount').autocomplete({
    source: function(term, response){
        $.getJSON('?objet=administration&action=autoComplete&type=listeAvecCompte&search='+term.term, function(data){ 
            var noms = new Array();
            $.each(data, function(i, field){
                noms.push(field.nom+" "+field.prenom);
                console.log(field.nom);
            });
            response(noms);
         });
        }
});

//2-autocomplete formulaire

$('#searchGrade').autocomplete({
    source: function(term, response){
        $.getJSON('?objet=dossier&action=autoComplete&type=listeNomGrade&search='+term.term, function(data){ 
            var grades = new Array();
            $.each(data, function(i, field){
                grades.push(field.id+" : "+field.grade);
            });
            response(grades);
         });
        }
});

$('#searchDiplome').autocomplete({
    source: function(term, response){
        $.getJSON('?objet=dossier&action=autoComplete&type=listeNomDiplome&search='+term.term, function(data){ 
            var diplomes = new Array();
            $.each(data, function(i, field){
                diplomes.push(field.acronyme+" : "+field.intitule);
            });
            response(diplomes);
         });
        }
});

$('#searchCaserne').autocomplete({
    source: function(term, response){
        $.getJSON('?objet=dossier&action=autoComplete&type=listeNomCaserne&search='+term.term, function(data){ 
            var casernes = new Array();
            $.each(data, function(i, field){
                casernes.push(field.id+" : "+field.nom);
            });
            response(casernes);
         });
        }
});

$('#searchRegiment').autocomplete({
    source: function(term, response){
        $.getJSON('?objet=dossier&action=autoComplete&type=listeNomRegiment&search='+term.term, function(data){ 
            var regiments = new Array();
            $.each(data, function(i, field){
                regiments.push(field.id);
            });
            response(regiments);
         });
        }
});