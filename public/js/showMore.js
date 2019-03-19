$(function () {
    $(".genre").slice(0, 5).show();

    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        if($(".genre:hidden").length !== 0){
            $(".genre:hidden").slideDown();
            $("#loadMore").text("Show less");
        }else{

            $(".genre").slice(5,$(".genre").length).slideUp();
            $("#loadMore").text("Show more");

        }


    });




});