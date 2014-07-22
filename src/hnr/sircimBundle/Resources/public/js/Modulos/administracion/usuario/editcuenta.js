jQuery(document).ready(function() {

    // $('#tbl_nuevo_usuario tr:nth-child(5),#tbl_nuevo_usuario tr:nth-child(6)').css({
    //         visibility: 'hidden',
    //         height:     '10px',
    // });


    $('#hnr_sircimbundle_usuariotype_usCorreo').on('input', function(event) {
        var parameters = $('#hnr_sircimbundle_usuariotype_usCorreo').val();          
        $('.guardar').removeAttr('existe');
        if(parameters!=''){


            $.getJSON(Routing.generate('correo_consultar', { email: parameters }),
                                function(data) {
                                    $.each(data.regs, function(indice, reg) {
                                        if(data.length!=0){
                                            $('.guardar').attr('existe', '1');      
                                        }    
                                    });
                                });        
        }
        else{
            return false;
        }
    });

    $('#hnr_sircimbundle_usuariotype_usLogin').on('input', function(event) {
        var parameters = $('#hnr_sircimbundle_usuariotype_usLogin').val();          
        $('.guardar').removeAttr('login');
        if(parameters!=''){

            $.getJSON(Routing.generate('login_consultar', { login: parameters }),
                                function(data) {
                                    $.each(data.regs, function(indice, reg) {
                                        if(data.length!=0){
                                            $('.guardar').attr('login', '1');      
                                        }    
                                    });
                                });        
        }   
        else{
            return false;
        }
    });


    $('.guardar_editcuenta').click(function(event) {

        var pass = $('#hnr_sircimbundle_usuariotype_usContrasena_pass').val();
        var confirm = $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').val();
        var correo = $('#hnr_sircimbundle_usuariotype_usCorreo').val();
        var uslogin_viejo = $('#nombre_usr label').attr('id');
        var uslogin = $('#hnr_sircimbundle_usuariotype_usLogin').val();
        uslogin = uslogin.toLowerCase();
        // alert('ascsa');
        var existe= $('.guardar').attr('existe');
        var login= $('.guardar').attr('login');
        var errores=0;
        var id=$('#titulo').attr('name');
        
        
        var accion ="";
        accion = $('form').attr('action');
        
        // accion = $('form').attr('action');
        // accion = accion.substring(0,42);
        
        // accion = accion.replace(uslogin_viejo, uslogin);
        // alert(accion);
        accion = accion.replace("hnr:sircim", uslogin);
        // alert(accion);
        // accion = accion+uslogin_viejo;
        
        $('form').attr('action',accion);
        // return false;
        var check=0;
        // alert(accion);
        // return false;
        // $('form').attr('action',accion+'/'+rolpredet);

        $('.btn_radio:checked').each(function() {
            // $(this).val() es el valor del checkbox correspondiente
           // alert('chequeado'); 
           check++;
           // $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);
        });// $('.btn_radio').attr('name', 'rol_predet');

        if(!verificarREG(2)){
            mensaje_error("No puede ingresar un usuario con dos roles iguales");
            return false;// errores++;   
        }

        if(check==0){
            mensaje_error("Seleccione un rol predeterminado para el usuario");
            return false;// errores++;
        }

        if(pass!=confirm){
            mensaje_error("Las contraseñas no son iguales");
            
            return false;// errores++;
        }
        if(existe==1){
            
            mensaje_error("El e-mail '"+$('#hnr_sircimbundle_usuariotype_usCorreo').val()+"' ya fue creado");
            return false;// errores++;
        }

        if(login==1){
            mensaje_error("El login '"+$('#hnr_sircimbundle_usuariotype_usLogin').val()+"' ya fue creado");
            return false;// errores++;
        }

        if(!loginusuario($('#hnr_sircimbundle_usuariotype_usLogin').val())){
            mensaje_error("El login solo debe incluir letras, números");
            return false;// errores++;
        }

        if( correo =='' || uslogin==''){
            return false;// errores++;
        }
            var valido=0;
            var pass1 = $('#hnr_sircimbundle_usuariotype_usContrasena_pass').val();
            var pass2 = $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').val();
            
            if(pass1!='' || pass2!='' ){
                valido=1;
                if(pass1.length<8){
                    mensaje_error("La contraseña debe tener mínimo 8 caracteres");
                    $('#hnr_sircimbundle_usuariotype_usContrasena_pass').addClass('errorform');
                }
                else{
                    if(pass1!=pass2){
                        mensaje_error("Las contraseñas no son iguales");
                        $('#hnr_sircimbundle_usuariotype_usContrasena_pass').addClass('errorform');
                        $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').addClass('errorform');
                    }
                    else{
                        if(!CheckPassword(pass1)){
                            mensaje_error("La contraseña debe tener al menos un número, una mayúscula y una minúscula");
                            $('#hnr_sircimbundle_usuariotype_usContrasena_pass').addClass('errorform');
                            $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').addClass('errorform');       
                        }
                        else{
                            // var login = 
                            var login = $('#header label').attr('id');
                            var contrasena = $('#hnr_sircimbundle_usuariotype_usContrasena_pass').val();
                            // $.getJSON(Routing.generate('continuar_sistema', { login: login}), function(data) { 
                            // }); 
                            // mensaje_exito("Contraseña modificada con éxito");
                            valido=0;
                        }
                    }
                }
            }

        

        if(valido==1){
            return false;
        }
        // mensaje_info(errores);
        // if(errores!=0){
            var login = $('#nombre_usr label').attr('id');
            $.getJSON(Routing.generate('usuario_update',{login:login}),    
                            function(data) {
                                $.each(data.regs, function(indice, reg) {
                                        // $('.guardar').attr('error','1');
                                        mensaje_exito("Usuario modificado con éxito");
                                });
                            });
                $('#deshabilitar').fadeOut('fast');
                $('#popup_box_rol').fadeOut('fast');
                alertify.success("<div class='icon_msg'><img src={{ asset('bundles/hnrsircim/img/info.png') }}></div><div class=\"msg\">El rol '"+$('#hnr_sircimbundle_roltype_roNombre').val()+"' creado con éxito</div>");   
            return false;
        

    });



    //Verifica si la contraseña tiene un numero, una mayuscula y una minuscula 
        function CheckPassword(inputtxt){   
            var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;  
            if(inputtxt.match(passw)){   
                return true;
            }
            else{
                return false;
            }
        }

    $('#cancelar, .cerrar_cuenta').click(function(event) {
        /* Act on the event */
        $('#deshabilitar').fadeOut('fast');
        $('#popup_box').fadeOut('fast');
        $('#popup_box_cuenta').fadeOut('fast');
        mensaje_info("Acción cancelada");
        return false;
    });

$(document).on('click', '.close', function(){
        alert('del');
    $(this).closest('#hnr_sircimbundle_estudioradiologicotype_placas').fadeOut(500, function() {
        $(this).remove();
    });
});    
});

        // $('.btn_radio').each(function() {
        //     //$(this).val() es el valor del checkbox correspondiente
        //    // alert('chequeado'); 
        //    $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);
        // });// $('.btn_radio').attr('name', 'rol_predet');

