

// Get the ul that holds the collection of tags
var collectionHolder = $('ul.placas');
var col=$('#hnr_sircimbundle_estudioradiologicotype_placas');
// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="add_placa_link">+</a>');
var $removeTagLink = $('<a href="#" class="remove_placa_link">x</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);
var $delLinkLi = $('#hnr_sircimbundle_estudioradiologicotype_placas.required').append($removeTagLink);





jQuery(document).ready(function() {


    // function verificarRol(){
    //     var existe_rol=0;
    //     var rol = [];
    //     var id_rol = [];
    //     var rol_aux = 0;
    //     $('.placas .desplegable').each( function(index, val) {
    //          rol.push($(this).val());
    //          id_rol.push($(this).attr('id'));
    //     });
    //     // mensaje_info(rol);
    //     $('.placas .desplegable').removeClass('errorform');
    //     for (var j = 0; j < rol.length; j++) {
    //         existe_rol=0;
    //         rol_aux=rol[j];
    //         for (var i = 0; i < rol.length; i++) {
                
    //             if(rol_aux==rol[i]){
    //                 // alert("Ya existe el rol");
                    
    //                 if(existe_rol!=0){
    //                     $('#'+id_rol[i]).addClass('errorform');    
    //                 }
    //                 if(rol_aux==""){
    //                     existe_rol=0;
    //                 }
    //                 existe_rol++;
    //             }
    //         }
    //     }
    //     if (existe_rol>1){
    //         return false;
    //     }
    //     else{
    //         return true;
    //     }    
    // }

    
    $('.btn_radio').click(function(event) {
        /* Act on the event */
        var id = $(this).attr('id');
        // alert(id);
        $('.btn_radio').prop('checked', false);
        // $('#'+id).prop('checked', true);
        $(this).prop('checked', true);
     
    });
    

    $('#listado').perfectScrollbar({
          wheelSpeed: 20,
          wheelPropagation: false
        });

    $('#hnr_sircimbundle_usuariotype_usCorreo').on('input', function(event) {
        var parameters = $('#hnr_sircimbundle_usuariotype_usCorreo').val();          
        $('.guardar').removeAttr('existe');
        $.getJSON(Routing.generate('correo_consultar', { email: parameters }),
                            function(data) {
                                $.each(data.regs, function(indice, reg) {
                                    if(data.length!=0){
                                        $('.guardar').attr('existe', '1');      
                                    }    
                                });
                            });        
    });

    $('#hnr_sircimbundle_usuariotype_usLogin').on('input', function(event) {
        var parameters = $('#hnr_sircimbundle_usuariotype_usLogin').val();          
        $('.guardar').removeAttr('login');
        $.getJSON(Routing.generate('login_consultar', { login: parameters }),
                            function(data) {
                                $.each(data.regs, function(indice, reg) {
                                    if(data.length!=0){
                                        $('.guardar').attr('login', '1');      
                                    }    
                                });
                            });        
    });

    

    $('#hnr_sircimbundle_usuariotype_usContrasena_pass, #hnr_sircimbundle_usuariotype_usContrasena_confirm').on('input', function(event) {
            $('#hnr_sircimbundle_usuariotype_usContrasena_pass').removeClass('errorform');
            $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').removeClass('errorform');
        });

    $('.guardar').click(function(event) {






        
        // alert('ascsa');
        var existe= $('.guardar').attr('existe');
        var login= $('.guardar').attr('login');
        var errores=0;

        var check=0;

         $('.btn_radio:checked').each(function() {
            // $(this).val() es el valor del checkbox correspondiente
           // alert('chequeado'); 
           check++;
           // $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);
        });// $('.btn_radio').attr('name', 'rol_predet');

        if(!verificarREG(2)){
            mensaje_error("No puede ingresar un usuario con dos roles iguales");
            errores++;   
        }

        if(check==0){
            mensaje_error("Seleccione un rol predeterminado para el usuario");
            $('.btn_radio').addClass('errorform');
            return false;
        }

        if(existe==1){
            mensaje_error("El e-mail '"+$('#hnr_sircimbundle_usuariotype_usCorreo').val()+"' ya existe");
            errores++;
        }

        if(login==1){
            mensaje_error("El login '"+$('#hnr_sircimbundle_usuariotype_usLogin').val()+"' ya existe");
            errores++;
        }

        


        var valido=1;
        var pass1 = $('#hnr_sircimbundle_usuariotype_usContrasena_pass').val();
        var pass2 = $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').val();
        
        if(pass1!='' || pass2!='' ){
            if(pass1.length<8){
                mensaje_error("La contraseña debe tener mínimo 8 caracteres");
                $('#hnr_sircimbundle_usuariotype_usContrasena_pass').addClass('errorform');
                $('#hnr_sircimbundle_usuariotype_usContrasena_confirm').addClass('errorform');
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
            if(valido!=0){
                return false;
            }
        }
        



        if(errores!=0){
            //var login = $('#nombre_usr label').attr('id');
            // $.getJSON(Routing.generate('usuario_create',{login:login}),
            //                 function(data) {
            //                     $.each(data.regs, function(indice, reg) {
            //                             // $('.guardar').attr('error','1');
                                    
            //                     });

            //                 });
            
            
                // $('#deshabilitar').fadeOut('fast');
                // $('#popup_box_rol').fadeOut('fast');
                // alertify.success("<div class='icon_msg'><img src={{ asset('bundles/hnrsircim/img/info.png') }}></div><div class=\"msg\">El rol '"+$('#hnr_sircimbundle_roltype_roNombre').val()+"' creado con éxito</div>");   
            // return false;
            event.preventDefault();
        }
    });
     


    $('#cancelar').click(function(event) {
        /* Act on the event */
        $('#deshabilitar').fadeOut('fast');
        $('#popup_box').fadeOut('fast');
        mensaje_info("Acción cancelada");
        return false;
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
    
    //Identificar que form esta activo
    var editar=$('ul.placas').attr('id');
    if(editar!='editar_usuario'){
        addTagForm(collectionHolder, $newLinkLi);   //Muestra un formulario en la carga de la página nuevo
    }
    


    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm(collectionHolder, $newLinkLi);
        // $('.btn_radio').attr('name', 'rol_predet');
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
    var $newFormLi = $('<li></li>').append(newForm);
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
    });
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormA = $('<a class="link" href="#">x</a>');
    $tagFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $('input[name="rol_predet"]:checked').each(function() {
            //$(this).val() es el valor del checkbox correspondiente
           // alert('chequeado'); 
           $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);
        });

        $tagFormLi.remove();
        
    });
}


$('#editar_usuario')
            .on('mouseover','li', function(){
                $(this).find('a.link').css('visibility','visible');
            })
            .on('mouseout','li', function(){ 
                $(this).find('a.link').css('visibility','hidden'); 
            });

$('#editar_usuario')
            .on('mouseover','li:first-child', function(){
                $(this).find('a.link').css('visibility','hidden');
            });


    
var editar=$('ul.placas').attr('id');
if(editar!='editar_usuario'){
    // $('#hnr_sircimbundle_usuariotype_placas_0_usroPredeterminado').prop('checked', true);//Se carga el radiobutton seleccionado
}