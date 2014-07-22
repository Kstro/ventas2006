jQuery(document).ready(function($) {

    $('#info_sircim').on('click', function(event) {
        var login = $('#nombre_usr label').attr('id');
        var href = Routing.generate('info_sircim');


        // alert(href);


         //Recuperar el atributo href del enlace actual
        // var href = $(this).attr('href');
        
        // crear un elemento para colocar la información
        
        $('#popup_box_info').fadeIn('slow');
        $('#popup_box_info').html("<div id='cargando'><img src='/bundles/hnrsircim/img/loading.gif'><div>");
        $('#deshabilitar').fadeIn('slow');
        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
        $('#popup_box_info').load(href);
         
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;


        // event.preventDefault();
        
    });
});