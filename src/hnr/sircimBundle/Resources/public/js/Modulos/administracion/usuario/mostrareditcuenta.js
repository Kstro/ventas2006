jQuery(document).ready(function($) {

    $('#conf_cuenta').click(function(){
        
                //Recuperar el atributo href del enlace actual
                var href = $(this).attr('href');
                
                // crear un elemento para colocar la información
                
                $('#popup_box_cuenta').fadeIn('slow');
                $('#popup_box_cuenta').html("<div id='cargando'><img src=/bundles/hnrsircim/img/loading.gif><div>");
                $('#deshabilitar').fadeIn('slow');
                // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
                $('#popup_box_cuenta').load(href);
                 
                // Para que no haga el comportamiento normal del enlace y cargue la página
                return false;

    });

});
