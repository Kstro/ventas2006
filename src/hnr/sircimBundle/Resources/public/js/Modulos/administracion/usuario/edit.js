
// Get the ul that holds the collection of tags
var collectionHolder = $('ul.placas');
var col=$('#hnr_sircimbundle_estudioradiologicotype_placas');
// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="add_placa_link">+</a>');
var $removeTagLink = $('<a href="#" class="remove_placa_link">x</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);
var $delLinkLi = $('#hnr_sircimbundle_estudioradiologicotype_placas.required').append($removeTagLink);

$('.add_placa_link').on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();

                // add a new tag form (see next code block)
                addTagForm(collectionHolder);
            });



jQuery(document).ready(function() {

    var rol="";
    var rolpredet = 0;

    

    $('.btn_radio').click(function(event) {
        $('.btn_radio').removeClass('errorform');
        var accion = "";
        accion = $('form').attr('action');
        accion = accion.substring(0,41);
        var id = $(this).attr('id');

        // alert(id);
        $('.btn_radio').prop('checked', false);
        $(this).prop('checked', true);
        // hnr_sircimbundle_usuariotype_placas_20_usroPredeterminado
        if(id.length==56){
            rol=id.substring(0,38)+"idRol";
        }
        else{
            rol=id.substring(0,39)+"idRol";   
        }

        rolpredet = $('#'+rol).val();
        $('form').attr('action',accion+'/'+rolpredet+'/0');
        // accion = $('form').attr('action');
        // alert(accion);
     
    });

             
    $('#listado').perfectScrollbar({
          wheelSpeed: 20,
          wheelPropagation: false
    });


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


    $('.guardar').click(function(event) {
        


        // return false;
        var pass = $('#hnr_sircimbundle_usuariotype_usContrasena_pass').val();
        var confirm = $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').val();
        var correo = $('#hnr_sircimbundle_usuariotype_usCorreo').val();
        var uslogin = $('#hnr_sircimbundle_usuariotype_usLogin').val();
        var uslogin_viejo = $('#nombre_usr label').attr('id');
        // alert('ascsa');
        var existe= $('.guardar').attr('existe');
        var login= $('.guardar').attr('login');
        var errores=0;
        var id=$('#titulo').attr('name');

        var check=0;

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
            $('.btn_radio').addClass('errorform');
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
        
        // $.getJSON(Routing.generate('usuario_update', { id: id, login:uslogin, opc:'1' }),
        //                     function(data) {
        //                         $.each(data.regs, function(indice, reg) {

        //                         });

        //                     });   

        // $.getJSON(Routing.generate('usuario_update_rolpredet', { id: id, rolpredet:rolpredet, login:uslogin }),
        //                     function(data) {
        //                         $.each(data.regs, function(indice, reg) {

        //                         });

        //                     });   


                // mensaje_info(errores);
        // if(errores!=0){

                
            // var login = $('#nombre_usr label').attr('id');
            // $.getJSON(Routing.generate('usuario_update',{login:login}),
            
            //                 function(data) {
            //                     $.each(data.regs, function(indice, reg) {
            //                             // $('.guardar').attr('error','1');
                                    
            //                     });

            //                 });
            
            
                // $('#deshabilitar').fadeOut('fast');
                // $('#popup_box_rol').fadeOut('fast');
                // alertify.success("<div class='icon_msg'><img src={{ asset('bundles/hnrsircim/img/info.png') }}></div><div class=\"msg\">El rol '"+$('#hnr_sircimbundle_roltype_roNombre').val()+"' creado con éxito</div>");   
            // return false;
            // mensaje_exito("Usuario modificado con éxito");
        // }
        mensaje_exito("Usuario modificado con éxito");
        // return false;
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

    $('.link').on('click', function(event) {
        
        /* Act on the event */

        // alert('dc');

        // $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);//Se carga el radiobutton seleccionado
        $(this).parent('li').remove();
        event.preventDefault();
    });
        

    $('#hnr_sircimbundle_estudioradiologicotype_placas>div>label.required').hide('slow/400/fast');
    col.children('div').each(function() {
        addTagFormDeleteLink($(this));
    }); 

    collectionHolder.find('li').each(function() {
        addTagFormDeleteLink($(this));

    });

    

    // add the "add a tag" anchor and li to the tags ul
    collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    collectionHolder.data('index', collectionHolder.find(':input').length);
    

    // addTagForm(collectionHolder, $newLinkLi);


    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm(collectionHolder, $newLinkLi);
        // $('.btn_radio').attr('name', 'rol_predet');
    });



$(document).on('click', '.close', function(){
        alert('del');
    $(this).closest('#hnr_sircimbundle_estudioradiologicotype_placas').fadeOut(500, function() {
        $(this).remove();
    });
});

    
});




function addTagForm(collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = collectionHolder.data('prototype');

    // get the new index
    var index = collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    // var $newFormLi = $('<li></li>').append(newForm);

    var $newFormLi = $('<li></li>').append('<div>'+newForm+'</div>');
    $newLinkLi.before($newFormLi);
    

    
    // add a delete link to the new form

    addTagFormDeleteLink($newFormLi);
    $('.btn_radio').click(function(event) {
        /* Act on the event */
        var id = $(this).attr('id');
        // alert(id);
        $('.btn_radio').prop('checked', false);
        $('#'+id).prop('checked', true);
        $('.btn_radio').removeClass('errorform');
        // actualizarrolpredet();
    });
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormA = $('<a class="link" href="#">x</a>');
    $tagFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // $('.btn_radio:checked').each(function() {
        //     //$(this).val() es el valor del checkbox correspondiente
        //    // alert('chequeado'); 
        //    $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);
        // });
        // remove the li for the tag form
        $('.btn_radio').prop('checked', false);
        // $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);//Se carga el radiobutton seleccionado
        $tagFormLi.remove();

    });
    
}

$('ul.placas')
            .on('mouseover','li', function(){
                $(this).find('a.link').css('visibility','visible');
            })
            .on('mouseout','li', function(){ 
                $(this).find('a.link').css('visibility','hidden'); 
            });

$('ul.placas')
            .on('mouseover','li:first-child', function(){
                $(this).find('a.link').css('visibility','hidden');
            });

        // $('.btn_radio').each(function() {
        //     //$(this).val() es el valor del checkbox correspondiente
        //    // alert('chequeado'); 
        //    $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);
        // });// $('.btn_radio').attr('name', 'rol_predet');

