
// Get the ul that holds the collection of tags
var collectionHolder = $('ul.placas');
var col=$('#hnr_sircimbundle_estudioradiologicotype_placas');
// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="add_placa_link">+</a>');
var $removeTagLink = $('<a href="#" class="remove_placa_link">x</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);
var $delLinkLi = $('#hnr_sircimbundle_estudioradiologicotype_placas.required').append($removeTagLink);



jQuery(document).ready(function() {


    //Deshabilita submit form con la tecla enter
    $("form :input").on("keypress", function(e) {
        return e.keyCode != 13;
    });

    
    $('#listado').perfectScrollbar({
          wheelSpeed: 20,
          wheelPropagation: false
        });

    $('#hnr_sircimbundle_roltype_roNombre').on('input', function(event) {
        
        var parameters = $('#hnr_sircimbundle_roltype_roNombre').val();
        $(this).removeClass('errorform');
        $('.guardar').removeAttr('existe');
        $.getJSON(Routing.generate('rol_consultar', { rol: parameters }),
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
        $('.guardar').removeAttr('error');
        
        

        var existe= $('.guardar').attr('existe');
        var nom_rol= $('#hnr_sircimbundle_roltype_roNombre').val();
        var opc_sistema = 0;
        // hnr_sircimbundle_roltype_roNombre
        // mensaje_info(nom_rol.length);
        if(!letrasnum_espacio(nom_rol)){
            if($('#hnr_sircimbundle_roltype_roNombre').val().length !=0){
                mensaje_error("Nombre incorrecto, solo se permiten letras y números");
                $('#hnr_sircimbundle_roltype_roNombre').addClass('errorform');
                event.preventDefault();    
            }
        }

        if(!verificarREG(1)){
            mensaje_error("No puede ingresar un rol con dos opciones del sistema iguales");
            return false;   
        }
        
            if(existe==1){
                mensaje_error("El rol '"+$('#hnr_sircimbundle_roltype_roNombre').val()+"' ya fue creado");
                $('#hnr_sircimbundle_roltype_roNombre').addClass('errorform');
                // event.preventDefault();           
                return false;
            }
            
                if(nom_rol.length != 0){
                    if ( nom_rol.length<10) {
                        mensaje_error("El nombre del rol debe tener mínimo 10 caracteres");
                        return false;


                    }
                    else{
                        $('select').each(function(index, val) {
                             /* iterate through array or object */
                             if ( $(this).val()=="" ) {
                                opc_sistema = 1;
                                // mensaje_info(opc_sistema);
                             }
                        });
                        if(opc_sistema==0){
                            mensaje_exito("Operación exitosa");
                        }
                        
                    }
                    
                     
                    
                    // var id=$('#titulo').attr('name');
                    // var login = $('#nombre_usr label').attr('id');
                    // $.getJSON(Routing.generate('rol_create', { id: id, login:login }),
                    //                 function(data) {
                    //                     $.each(data.regs, function(indice, reg) {
                    //                             $('.guardar').attr('error','1');
                                            
                    //                     });

                    //                 });
                    // return false;
                }
            
        
    });
     
        

    $('#cancelar').click(function(event) {
        /* Act on the event */
        $('#popup_box_rol').fadeOut('fast');
        $('#deshabilitar').fadeOut('fast');
        
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
    

    var editar=$('ul.placas').attr('id');
    
    if(editar!='editar_rol'){
        addTagForm(collectionHolder, $newLinkLi);   //Muestra un formulario en la carga de la página nuevo
    }

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm(collectionHolder, $newLinkLi);
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
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormA = $('<a class="link" href="#">x</a>');
    $tagFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $tagFormLi.remove();
    });
}

$('#editar_rol')
            .on('mouseover','li', function(){
                $(this).find('a.link').css('visibility','visible');
            })
            .on('mouseout','li', function(){ 
                $(this).find('a.link').css('visibility','hidden'); 
            });

$('#editar_rol')
            .on('mouseover','li:first-child', function(){
                $(this).find('a.link').css('visibility','hidden');
            });

