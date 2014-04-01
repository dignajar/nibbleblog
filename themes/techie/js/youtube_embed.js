// Fix YouTube iframes
$(document).ready(function() {
    $("iframe[src*='youtube.com']").each(function(){
        var url = $(this).attr('src');
        var append = "?";
        if (url.indexOf("?") != -1)
            var append = "&";
        $(this).attr('src',url+append+'wmode=transparent');
    });
});
