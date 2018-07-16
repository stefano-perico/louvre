$(document).ready(function () {
    // Ajout billets dans vue Panier
    // On récupère la balise <div> en question qui contient l'attribut "data-prototype".
    var $container = $('div#appbundle_commandes_billets');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement.
    var index = $container.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_billet').click(function (e) {
        addBillet($container);

        $('.form-check-input').change(function () {
            if($('.form-check-input').is(':checked')){
                $('#alert_demiTarif').css('display', 'block');
            }else{
                $('#alert_demiTarif').css('display', 'none');
            }
        });

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
    
    $('.form-check-input').change(function () {
        if($('.form-check-input').is(':checked')){
            $('#alert_demiTarif').css('display', 'block');
        }else{
            $('#alert_demiTarif').css('display', 'none');
        }
    });
});


