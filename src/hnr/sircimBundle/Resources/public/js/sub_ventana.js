/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() { 
        

        $(".paciente_estudios a").click(function(){
        //$("a").click(function(){
        
        //Recuperar el atributo href del enlace actual
        var href = $(this).attr('href');
        // crear un elemento para colocar la información
        $('#popup_box_exp').html('<div id="cargando"><img src="bundles/hnrsircim/imagenes/loading.gif"/></div>');
        $('#popup_box_exp').fadeIn("slow");
        $('#deshabilitar').fadeIn('slow');
        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 

        $('#popup_box_exp').load(href, function(){
            $(this).dialog({ modal:true,with:600});
        });
         
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;
        });


        $("#paciente_citas a").click(function(){
        //$("a").click(function(){
        
        //Recuperar el atributo href del enlace actual
        var href = $(this).attr('href');
       
        // crear un elemento para colocar la información
        $('#popup_box').html('<div id="cargando"><img src="bundles/hnrsircim/imagenes/loading.gif"/></div>');
        $('#popup_box').fadeIn("slow");
        $('#deshabilitar').fadeIn('slow');
        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 

        $('#popup_box').load(href);
         
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;
        });



        $('#cerrar, #cerrar_exp img').click(function(){
        $('#popup_box').fadeOut("slow");
        $('#popup_box_exp').fadeOut("slow");
        $('#deshabilitar').fadeOut('slow');        
        // Para que no haga el comportamiento normal del enlace y cargue la página
        });


        $("#pestanas a").click(function(){
        
        //Recuperar el atributo href del enlace actual
        href = $(this).attr('href');    
        // crear un elemento para colocar la información
        $('#formulario').html('<div id="cargando"><img src="bundles/hnrsircim/imagenes/loading.gif"/></div>');

        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
        $('#formulario').load(href);
         
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;
        });

        $("#pestanas div").click(function(){
                $("#pestanas div").css({ background: "#D9D9D9" });
                $(this).css({ background :"#FFF" });
        });           
        $('#pestanas div:first').css({ background :"#FFF" });
        var href = $('#pestanas a:first').attr('href');    
        $('#formulario').load(href);


        
});


