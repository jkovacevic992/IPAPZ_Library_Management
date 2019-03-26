
$(function () {
    var cookie = getCookie('showMoreCookie');

    if(cookie != 'clicked') {
        $(".genre").slice(0, 5).show();
        $("#loadMore").on('click', function (e) {
            e.preventDefault();

            if ($(".genre:hidden").length !== 0) {
                $(".genre:hidden").slideDown();
                $("#loadMore").text("Show Less");
                createCookie('showMoreCookie','clicked',1);

            } else {

                $(".genre").slice(5, $(".genre").length).slideUp();
                $("#loadMore").text("Show More");
                createCookie('showMoreCookie','unclicked',1);
            }


        });
    }else {
        $(".genre").show();
        $("#loadMore").text("Show Less");
        $("#loadMore").on('click', function (e) {
            e.preventDefault();
            $(".genre").slice(5, $(".genre").length).slideUp();
            $("#loadMore").text("Show More");
            createCookie('showMoreCookie','unclicked',1);
        });


    }

});


function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
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



