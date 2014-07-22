jQuery(document).ready(function($) {
    
    function comprobartamanoplaca(){
        var parameters = $('#hnr_sircimbundle_tamanoplacatype_tpTamano').val();
        
        $('#hnr_sircimbundle_tamanoplacatype_tpTamano').removeClass('errorform');
        $('.guardar').removeAttr('existe');
        $.getJSON(Routing.generate('tamano_consultar', { tam: parameters }),
                            function(data) {
                                $.each(data.regs, function(indice, reg) {
                                    
                                    // alert(data.length);
                                    if(data.length!=0){
                                        $('.guardar').attr('existe', '1');      
                                        $('#hnr_sircimbundle_tamanoplacatype_tpTamano').addClass('errorform');
                                        mensaje_error("El área con nombre '"+$('#hnr_sircimbundle_tamanoplacatype_tpTamano').val()+"' ya fue creada");
                                        // $('.guardar').prop('disabled', true);                    
                                    
                                    }    


                                });

                            });
    
    }

    $('#hnr_sircimbundle_tamanoplacatype_tpTamano').on('input', function(event) {
        
        if(comprobartamanoplaca()){
            alert('dcsd');
        }

        
    });

    //Deshabilita submit form con la tecla enter
    $("form :input").on("keypress", function(e) {
        return e.keyCode != 13;
    });


    $('.guardar').click(function(event) {

        var existe= $('.guardar').attr('existe');

        

        if(existe==1){
            $('#hnr_sircimbundle_tamanoplacatype_tpTamano').addClass('errorform');
            mensaje_error("El área con nombre '"+$('#hnr_sircimbundle_tamanoplacatype_tpTamano').val()+"' ya fue creada");
            
            return false;                
        }

        else{
            var tp=$('#hnr_sircimbundle_tamanoplacatype_tpTamano').val();
            
                if(tp.length!=0){

                    if(tp.length <3){
                        mensaje_error("El tamaño de placa debe tener mínimo 3 caracteres");
                        $('#hnr_sircimbundle_tamanoplacatype_tpTamano').addClass('errorform');
                        event.preventDefault();
                    }
                    else{
                        if(!letrasnum(tp)){
                            mensaje_error("El valor debe tener solo letras o números");
                            $('#hnr_sircimbundle_tamanoplacatype_tpTamano').addClass('errorform');
                            event.preventDefault();
                        }
                        else{
                            mensaje_exito("Operación realizada con éxito");        
                        }
                       
                        
                    }
                }
            
            

        }

        // $.getJSON(Routing.generate('tamano_consultar', { tam: parameters }),
        //                     function(data) {
        //                         $.each(data.regs, function(indice, reg) {
                                    
        //                             // alert(data.length);
        //                             if(data.regs.length!=0){
        //                                 mensaje_error("El área con nombre '"+$('#hnr_sircimbundle_tamanoplacatype_tpTamano').val()+"' ya fue creada");
        //                                 i=1;
        //                                 // $('.guardar').prop('disabled', true);                    
        //                             }
        //                             else{
        //                                 i=0;
        //                             }
             
        //                         });

        //                     });
        // // alert(i);
        // if(i==1){
        //     $('#hnr_sircimbundle_tamanoplacatype_tpTamano').addClass('errorform');
        //     return false;
        // }
        // else{
        //     mensaje_exito("Operación realizada con éxito");        
        // }
    });


 
    $('#cancelar').click(function(event) {
        /* Act on the event */
        $('#deshabilitar').fadeOut('fast');
        $('#popup_box_placa').fadeOut('fast');
        mensaje_info("Acción cancelada");

        return false;
    });




});