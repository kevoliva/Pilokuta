var $collectionHolderEquipe;

// setup an "add a poule" link
var $addEquipeButton = $('<button type="button" class="btn-success">Ajouter une Équipe</button>');
var $newLinkLiEquipe = $('<li></li>').append($addEquipeButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of equipes
    $collectionHolderEquipe = $('ul.equipes');
    
    // add a delete link to all of the existing equipe form li elements
    $collectionHolderEquipe.find('li').each(function() {
        addEquipeFormDeleteLink($(this));
    });
    
    // add the "add a poule" anchor and li to the poules ul
    $collectionHolderEquipe.append($newLinkLiEquipe);
    
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolderEquipe.data('index', $collectionHolderEquipe.find('input').length);
    
    $addEquipeButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addEquipeForm($collectionHolderEquipe, $newLinkLiEquipe);
    });
});

function addEquipeForm($collectionHolderEquipe, $newLinkLiEquipe) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolderEquipe.data('prototype');
    
    // get the new index
    var index = $collectionHolderEquipe.data('index');
    
    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your poules field in TournoiType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);
    
    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);
    
    // increase the index with one for the next item
    $collectionHolderEquipe.data('index', index + 1);
    
    // Display the form in the page in an li, before the "Add an equipe" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLiEquipe.before($newFormLi);
    
    addEquipeFormDeleteLink($newFormLi);
}

function addEquipeFormDeleteLink($equipeFormLi) {
    var $removeFormButton = $('<button type="button" class="btn-danger">Supprimer cette Équipe</button><hr>');
    $equipeFormLi.append($removeFormButton);
    
    $removeFormButton.on('click', function(e) {
        // remove the li for the poule form
        $equipeFormLi.remove();
    });
}