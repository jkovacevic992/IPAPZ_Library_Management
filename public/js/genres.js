var $collectionHolder;

// setup an "add a tag" link
var $addGenreButton = $('<button type="button" class="add_genre_link">Add genre</button>');
var $newLinkLi = $('<li></li>').append($addGenreButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.genres');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addGenreButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addGenreForm($collectionHolder, $newLinkLi);
    });
});

function addGenreForm($collectionHolder, $newLinkLi) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);


    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    addGenreFormDeleteLink($newFormLi);
}

function addGenreFormDeleteLink($genreFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-primary">Remove this genre</button>');
    $genreFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {

        $genreFormLi.remove();
    });
}