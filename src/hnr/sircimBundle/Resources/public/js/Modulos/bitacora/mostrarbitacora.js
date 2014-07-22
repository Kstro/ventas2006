jQuery(document).ready(function($) {

    $('#nombre_usr label').on('click', function(event) {
        var login = $('#nombre_usr label').attr('id');
        var href = Routing.generate('bitacora', { login: login });


        // alert(href);


         //Recuperar el atributo href del enlace actual
        // var href = $(this).attr('href');
        
        // crear un elemento para colocar la información
        
        $('#popup_box_bitacora').fadeIn('slow');
        $('#popup_box_bitacora').html("<div id='cargando'><img src='/bundles/hnrsircim/img/loading.gif'><div>");
        $('#deshabilitar').fadeIn('slow');
        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
        $('#popup_box_bitacora').load(href);
         
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;


        // event.preventDefault();
        
    });
});