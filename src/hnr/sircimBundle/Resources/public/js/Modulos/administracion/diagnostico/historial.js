

jQuery(document).ready(function() {
    // $('.paciente_historial .paciente_citas').css('visibility', 'hidden');

    $('.comparacion').click(function(){ 
        // alert("Comparacion");
        var num = 0;
        // .is(':checked')
        $('.comparacion').removeClass('errorform');
        $('.comparacion').each( function(index, val) {
            if($(this).is(':checked')){
                // alert("Check");

                num++;  

                                
            }
        });
        if(num>=3){
                    $(this).prop('checked', false);
                    mensaje_info("Solo puede comparar dos estudios a la vez");
                    return false;
        }
                
        // alert(num);
    });
    
    


    $('#diagnostico_imagen, #diagnostico_cita').click(function(){
        
        //Verificar si existe el elemento 'resultado' creado de alguna llamada anterior, y lo borra
        ($('#popup_box_exp')) ? $('#popup_box_exp').remove():'';
 
        // crear un elemento para colocar la información
        var elem = $("<div id='popup_box_exp'></div>");
        
        
        elem.insertAfter($("#deshabilitar"));

        //Recuperar el atributo href del enlace actual
        var href = $(this).attr('href');
        // crear un elemento para colocar la información
        $('#popup_box_exp').fadeIn('slow');
        $('#popup_box_exp').html("<div id='cargando'><img src='/bundles/hnrsircim/img/loading.gif'><div>");
        $('#deshabilitar').fadeIn('slow');
        // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
        $('#popup_box_exp').load(href);
        
        // Para que no haga el comportamiento normal del enlace y cargue la página
        return false;

    });

});