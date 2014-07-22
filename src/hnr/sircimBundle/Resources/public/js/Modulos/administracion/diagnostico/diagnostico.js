


jQuery(document).ready(function() {

    $('.cerrar').click(function(event) {
        /* Act on the event */
        $('#deshabilitar').fadeOut('fast');
        $('#popup_box_exp').fadeOut('fast');
        $('#popup_box_comparar').fadeOut('fast');
        return false;
    });    

});

