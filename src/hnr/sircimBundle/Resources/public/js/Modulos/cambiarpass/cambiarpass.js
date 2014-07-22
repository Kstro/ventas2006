jQuery(document).ready(function($) {



        $('#pass1, #pass2').on('input', function(event) {
            $('#pass1').removeClass('errorform');
            $('#pass2').removeClass('errorform');
        });


        $('.guardar').on('click', function(event) {
            var valido=1;
            var pass1 = $('#pass1').val();
            var pass2 = $('#pass2').val();
            if(pass1=='' && pass2=='' ){
                mensaje_error("Complete todos los campos");
                $('#pass1').addClass('errorform');
                $('#pass2').addClass('errorform');
                event.preventDefault();
            }
            else{
                if(pass1.length<8){
                    mensaje_error("La contraseña debe tener mínimo 8 caracteres");
                    $('#pass1').addClass('errorform');
                }
                else{
                    if(pass1!=pass2){
                        mensaje_error("Las contraseñas no son iguales");
                        $('#pass1').addClass('errorform');
                        $('#pass2').addClass('errorform');
                    }
                    else{
                        if(!CheckPassword(pass1)){
                            mensaje_error("La contraseña debe tener al menos un número, una mayúscula y una minúscula");
                            $('#pass1').addClass('errorform');
                            $('#pass2').addClass('errorform');       
                        }
                        else{
                            // var login = 
                            var login = $('#header label').attr('id');
                            var contrasena = $('#pass1').val();
                            // $.getJSON(Routing.generate('continuar_sistema', { login: login}), function(data) { 
                            // }); 
                            mensaje_exito("Contraseña modificada con éxito");
                            valido=0;

                        }
                    }


                }
            }
            if(valido==1)
                return false;
            
        });


        

        



    
    });
