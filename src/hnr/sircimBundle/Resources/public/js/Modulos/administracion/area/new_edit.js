jQuery(document).ready(function() {


    //Deshabilita submit form con la tecla enter
    $("form :input").on("keypress", function(e) {
        return e.keyCode != 13;
    });

    $('#hnr_sircimbundle_areatype_arNombre').on('input', function(event) {
        
        var parameters = $('#hnr_sircimbundle_areatype_arNombre').val();
        $('#hnr_sircimbundle_areatype_arNombre').removeClass('errorform');       
        $('.guardar').removeAttr('existe');
        $.getJSON(Routing.generate('consultar_nom_area', { nom: parameters }),
                            function(data) {
                                $.each(data.regs, function(indice, reg) {
                                    
                                    // alert(data.length);
                                    if(data.length!=0){
                                        $('.guardar').attr('existe', '1');      
                                        // $('.guardar').prop('disabled', true);                    
                                    }    

                                });

                            });

        
    });

    $('.guardar').click(function(event) {
        
        var existe= $('.guardar').attr('existe');
        var ar_nombre=$('#hnr_sircimbundle_areatype_arNombre').val();
        var ar_des = $('#hnr_sircimbundle_areatype_arDescripcion').val();
        var ar_cupo = $('#hnr_sircimbundle_areatype_arCupo').val();
        if(existe==1){
            
                mensaje_error("El área con nombre '"+$('#hnr_sircimbundle_areatype_arNombre').val()+"' ya fue creada");
            return false;                
        }
        else{
            

            var cupo = $('#hnr_sircimbundle_areatype_arCupo').val();
                if(ar_nombre.length!=0 && ar_des!=0 ){
                    if(ar_nombre.length <7){
                        mensaje_error("El nombre del área debe tener mínimo 7 caracteres");
                        $('#hnr_sircimbundle_areatype_arNombre').addClass('errorform');
                        event.preventDefault();
                    }
                    else{
                        if(!letrasnum_espacio(ar_nombre)){
                            mensaje_error("El nombre debe tener solo letras o números");
                            $('#hnr_sircimbundle_areatype_arNombre').addClass('errorform');
                            event.preventDefault();
                        }
                        else{
                            if(!numeros(cupo)){
                                mensaje_error("El valor del cupo debe ser un número");
                                $('#hnr_sircimbundle_areatype_arCupo').addClass('errorform');
                                event.preventDefault();
                            }   
                            else{
                                mensaje_exito("Operación realizada con éxito");    
                            }
                        }     
                    }
                }

        }
    });


    $('#cancelar').click(function(event) {
        /* Act on the event */
        $('#deshabilitar').fadeOut('fast');
        $('#popup_box').fadeOut('fast');
        mensaje_info("Acción cancelada");

        return false;
    });
});


