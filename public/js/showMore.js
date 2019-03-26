
$(function () {

    $.ajax({
        method: 'POST',
        url: "username"
    }).done(function (data) {
        var cookie = getCookie(data.username);
        something(cookie, data.username)
    })


});


function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
    }
    // because unescape has been deprecated, replaced with decodeURI
    //return unescape(dc.substring(begin + prefix.length, end));
    return decodeURI(dc.substring(begin + prefix.length, end));
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function something(cookie, value)
{
    if (cookie === value+'1') {
        $(".genre").show();
        $("#loadMore").text("Show Less");
        $("#loadMore").on('click', function (e) {
            e.preventDefault();
            if ($(".genre:hidden").length !== 0) {
                $("#loadMore").text("Show Less");
                $.ajax({
                    method: 'POST',
                    url: "username"
                }).done(function (data) {
                    createCookie(data.username,data.username+'1',1);

                    $(".genre:hidden").slideDown();
                })


            } else {
                $("#loadMore").text("Show More");
                $.ajax({
                    method: 'POST',
                    url: "username"
                }).done(function (data) {
                    createCookie(data.username,data.username+'0',1);
                    $(".genre").slice(5, $(".genre").length).slideUp();

                })
            }



        });
    } else {
        $(".genre").slice(0, 5).show();
        $("#loadMore").on('click', function (e) {

            e.preventDefault();

            if ($(".genre:hidden").length !== 0) {
                $("#loadMore").text("Show Less");
                $.ajax({
                    method: 'POST',
                    url: "username"
                }).done(function (data) {
                    createCookie(data.username,data.username+'1',1);

                    $(".genre:hidden").slideDown();
                })


            } else {
                $("#loadMore").text("Show More");
                $.ajax({
                    method: 'POST',
                    url: "username"
                }).done(function (data) {
                    createCookie(data.username,data.username+'0',1);
                    $(".genre").slice(5, $(".genre").length).slideUp();

                })
            }


        });


    }
}

