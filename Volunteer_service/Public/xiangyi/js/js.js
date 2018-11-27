function msgAlert(res) {
    $(".msgAlert").show();
    var html = '';
    html += '<div class="alert-win">'+res+'</div>';
    $('.msgAlert').html(html);
    var width=$(".msgAlert").width();
    var height=$(".msgAlert").height();
    $(".msgAlert").css({
        "margin-left":-width / 2,
        "margin-top":height / 2,
        "left":"50vw",
        "max-width":"60vw"
    })
    setTimeout(function () {
        $(".msgAlert").hide();
    },2000);
}