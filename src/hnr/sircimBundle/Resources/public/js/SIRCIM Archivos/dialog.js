$(document).ready(function() { 
              
        $('#nuevo, .acciones').click(function(){
        
        //Recuperar el atributo href del enlace actual
        var href = $(this).attr('href');
        
        // crear un elemento para colocar la información
        
        $('#popup_box').fadeIn('slow');
        $('#popup_box').html('<div id="cargando"><img src="../img/loading.gif"><div>');
        $('#deshabilitar').fadeIn('slow');
        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
        $('#popup_box').load(href);
         
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;

        });

  
        
           

});


    



        

       