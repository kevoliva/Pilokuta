var $collectionHolder;

// setup an "add a serie" link
var $addSerieButton = $('<button type="button" class="btn-success">Add a Serie</button>');
var $newLinkLi = $('<li></li>').append($addSerieButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of series
    $collectionHolder = $('ul.series');
    
    // add a delete link to all of the existing serie form li elements
    $collectionHolder.find('li').each(function() {
        addSerieFormDeleteLink($(this));
    });
    
    // add the "add a serie" anchor and li to the series ul
    $collectionHolder.append($newLinkLi);
    
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find('input').length);
    
    $addSerieButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addSerieForm($collectionHolder, $newLinkLi);
    });
});

function addSerieForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    
    // get the new index
    var index = $collectionHolder.data('index');
    
    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your series field in TournoiType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);
    
    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);
    
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);
    
    // Display the form in the page in an li, before the "Add a serie" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    
    addSerieFormDeleteLink($newFormLi);
}

function addSerieFormDeleteLink($serieFormLi) {
    var $removeFormButton = $('<button type="button" class="btn-danger">Delete this serie</button><hr>');
    $serieFormLi.append($removeFormButton);
    
    $removeFormButton.on('click', function(e) {
        // remove the li for the serie form
        $serieFormLi.remove();
    });
}