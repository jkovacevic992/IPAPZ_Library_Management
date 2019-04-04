var $collectionHolder;

// setup an "add a tag" link
var $addBookButton = $('<button type="button" class="btn btn-secondary">Add a book</button>');
var $newLinkLi = $('<li></li>').append($addBookButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.books');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addBookButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addBookForm($collectionHolder, $newLinkLi);
    });
});

function addBookForm($collectionHolder, $newLinkLi) {

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);

    $collectionHolder.data('index', index + 1);


    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    addBookFormDeleteLink($newFormLi);
}

function addBookFormDeleteLink($bookFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-danger">Remove this book</button>');
    $bookFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {

        $bookFormLi.remove();
    });
}

