
function searchArticle(urlImgWait) {
    var searchText = $('#searchText').val();

    if (searchText === ''){
        searchText = 'none';
    }

    $.ajax({
        type: 'get',
        url: 'http://localhost/Emmaus/web/app_dev.php/home/' + searchText + '/' + $('#searchCategorie').val(),
        beforeSend: function() {
            $("#listArticle").html('<img src="' + urlImgWait + '" height="120" width="150">');
        },
        success: function(data) {
            $("#listArticle").html(data);
        }
    });
}

$( document ).ready(function() {
    console.log( "ready!" );
});