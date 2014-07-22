
        jQuery(document).ready(function($) {

            $('#nuevo, .acciones').click(function(){
        
            //Recuperar el atributo href del enlace actual
            var href = $(this).attr('href');
            
            // crear un elemento para colocar la información
            
            $('#popup_box').fadeIn('slow');
            $('#popup_box').html("<div id='cargando'><img src=/bundles/hnrsircim/img/loading.gif><div>");
            $('#deshabilitar').fadeIn('slow');
            // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
            $('#popup_box').load(href);
             
            // Para que no haga el comportamiento normal del enlace y cargue la página
            return false;

        });
            $('.slideThree input').click(function(event) {
                var estado = !$(this).is(':checked');
                var nombre_id = $(this).attr('name');
                var nombre = $(this).attr('nombre');
                var id = $(this).attr('id');
                var opc='';
                var cupo= $(this).attr('cupo');
                if (id.substring(0,4)=='cupo'){
                    opc="del cupo del área '"+nombre+"'?";
                }
                else{
                    opc="del área '"+nombre+"'?";
                }


                // confirm dialog
                


                if(cupo==0){
                    mensaje_error("No se puede activar cupos con valores de '0'");
                    // $('#deshabilitar').fadeOut('fast');
                    return false;
                }
                $('#deshabilitar').fadeIn('fast');
                alertify.confirm("<div class='icon_msg confirm_icon'><img src='/bundles/hnrsircim/img/confirm.png'></div><div class=\"msg confirm_msg\">¿Desea cambiar el estado "+opc+"</div>", 
                    function (e) {
                    if (e) {
                        // user clicked "ok"
                        var cupo= $('#cupo'+nombre_id).is(':checked');
                        var activo=$('#activo'+nombre_id).is(':checked');
                        // alert(Routing.generate('usuario_update_estados', { id:nombre, activo:activo, bloqueado:bloqueo}));
                        // alert(activo);
                        // alert(bloqueo);
                        if (cupo){
                            cupo=1;
                        }
                        else{
                            cupo=0;
                        }
                        if (activo){
                            activo=1;
                        }
                        else{
                            activo=0;
                        }
                        var login = $('#nombre_usr label').attr('id');
                        $.getJSON(Routing.generate('area_update_estados', { id: nombre_id, cupo: cupo, activo: activo,login:login}), 
                            function(data) { 
                                if(data.regs==0){
                                    mensaje_exito("Estado de '"+nombre+"' modificado con éxito");
                                }
                                else{
                                    $('.slideThree input[id='+id+']').prop('checked', estado);
                                    mensaje_error("La sesión ha finalizado, para continuar inicie sesión nuevamente");   
                                }
                        }); 
                    } else {
                        // user clicked "cancel"
                        $('.slideThree input[id='+id+']').prop('checked', estado);
                        mensaje_info("Acción cancelada");
                    }
                    $('#deshabilitar').fadeOut('fast');
                });

            });  

            

        });

