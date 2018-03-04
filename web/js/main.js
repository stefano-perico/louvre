$(function() {
    $('.js-header-search-toggle').on('click', function() {
        $('.search-bar').slideToggle();
    });

    // Datepicker vue Panier
    $(".datepicker").datepicker({
        format: "dd-mm-yyyy",
        startDate: "0d",
        language: "fr",
        daysOfWeekDisabled: "2,0",
        autoclose: true,
        todayHighlight: true,
        endDate: '31-12-2018',
        datesDisabled:['02-04-2018', '01-05-2018', '08-05-2018', '10-05-2018', '20-05-2018', '21-05-2018', '14-07-2018', '15-08-2018', '01-11-2018', '11-11-2018', '25-12-2018', '31-12-2018', '01-01-2019', '21-04-2019', '22-04-2019', '01-05-2019', '08-05-2109', '30-05-2019', '09-06-2019', '10-06-2019', '14-07-2019', '15-08-2019', '01-11-2019', '11-11-2019', '25-12-2019', '31-12-2019']
    });

    // Ajout billets dans vue Panier
    // On récupère la balase <div> en question qui contient l'attribut "data-prototype" qui nous intéresse.
    var $container = $('div#appbundle_commandes_billet');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $container.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_billet').click(function (e) {
        addBillet($container);

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un
    if (index == 0) {
        addBillet($container)
    } else {

        // S'il existe déjà des billets, on ajoute un lien de suppression pour chacun d'entre eux
        $container.children('div').each(function () {
            addDeleteLink($(this));
        });
    }

    // La fonction qui ajoute un formulaire BilletType
    function addBillet($container) {
        // Dans le contenu de l'attribut "data-prototype", on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il conien par le numéro du champ
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Billet n°' + (index+1))
            .replace(/__name__/g,        index)
        ;

        // On crée un objet jquery qui contient ce template
        var $prototype = $(template);

        // On ajoute au prototype un lien pour pouvoir supprimer le billet
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppressio d'une catégorie
    function addDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer<a>');

        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $deleteLink.click(function (e) {
            $prototype.remove();

            e.preventDefault(); // évite qu'un # appraraisse dans l'URL
            return false;
        });
    }
});


