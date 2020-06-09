var $collectionHolderPoule;

// setup an "add a poule" link
var $addPouleButton = $('<button type="button" class="btn-success">Add a Poule</button>');
var $newLinkLiPoule = $('<li></li>').append($addPouleButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of poules
    $collectionHolderPoule = $('ul.poules');
    
    // add a delete link to all of the existing poule form li elements
    $collectionHolderPoule.find('li').each(function() {
        addPouleFormDeleteLink($(this));
    });
    
    // add the "add a poule" anchor and li to the poules ul
    $collectionHolderPoule.append($newLinkLiPoule);
    
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolderPoule.data('index', $collectionHolderPoule.find('input').length);
    
    $addPouleButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addPouleForm($collectionHolderPoule, $newLinkLiPoule);
    });
});

function addPouleForm($collectionHolderPoule, $newLinkLiPoule) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolderPoule.data('prototype');
    
    // get the new index
    var index = $collectionHolderPoule.data('index');
    
    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your poules field in TournoiType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);
    
    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);
    
    // increase the index with one for the next item
    $collectionHolderPoule.data('index', index + 1);
    
    // Display the form in the page in an li, before the "Add a poule" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLiPoule.before($newFormLi);
    
    addPouleFormDeleteLink($newFormLi);
}

function addPouleFormDeleteLink($pouleFormLi) {
    var $removeFormButton = $('<button type="button" class="btn-danger">Delete this Poule</button><hr>');
    $pouleFormLi.append($removeFormButton);
    
    $removeFormButton.on('click', function(e) {
        // remove the li for the poule form
        $pouleFormLi.remove();
    });
}