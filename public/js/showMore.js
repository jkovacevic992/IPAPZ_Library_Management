$(function () {
    $(".genre").slice(0, 4).show();
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $(".genre:hidden").slice(0,4).slideDown();
        if($(".genre:hidden").length === 0){


            $("#loadMore").fadeOut();
        }

    });


});