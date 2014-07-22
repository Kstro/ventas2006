// Get the ul that holds the collection of tags
var collectionHolder = $('ul.placas');
var col=$('#hnr_sircimbundle_estudioradiologicotype_placas');
// setup an "add a tag" link
// var $addTagLink = '';
var $addTagLink = $('<a href="#" class="add_placa_link">+</a>');
var $removeTagLink = $('<a href="#" class="remove_placa_link">x</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);
var $delLinkLi = $('#hnr_sircimbundle_estudioradiologicotype_placas.required').append($removeTagLink);


    

$('.add_placa_link').on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();

                // add a new tag form (see next code block)
                addTagForm(collectionHolder,$newLinkLi);
            });






jQuery(document).ready(function() {

    $('#listado_estudioarea').perfectScrollbar({
          wheelSpeed: 20,
          wheelPropagation: false
        });

    
    function validarhorario(){
        
        var dia = [];
        var hora_inicio = [];
        var hora_fin = [];
        var id_dia = [];
        var id_hora_inicio = [];
        var id_hora_fin = [];
        var error_hora = 0; 
        var error_horario = 0;
        var horario_traslape=0;
        var error_horario_traslape=0;
        var horario = "";
        var num_elementos=0;

        // alert( $('.hora_inicio').length );
        $('.dia').each( function(index, val) {
              
             dia.push($(this).val());
             id_dia.push($(this).attr('id'));
        });

        $('.hora_inicio').each( function(index, val) {
              
             hora_inicio.push($(this).val());
             id_hora_inicio.push($(this).attr('id'));
        });

        $('.hora_fin').each( function(index, val) {
              
             hora_fin.push($(this).val());
             id_hora_fin.push($(this).attr('id'));
        });

        // mensaje_info(dia[2]);
        num_elementos = dia.length;
        error_hora=0;
        error_horario=0;
        var horario_aux="";
        horario_repetido=0;
        // mensaje_info(horario);
        $('.dia').removeClass('errorform');
        $('.hora_inicio').removeClass('errorform');
        $('.hora_fin').removeClass('errorform');
        for (var j = 0; j < dia.length; j++) { 
            horario = ""+dia[j]+hora_inicio[j]+hora_fin[j];    
            // mensaje_info(horario);
            error_horario=0;
            // for (var i = 0; i < dia.length; i++) {
            //     // mensaje_info(error_horario);
            //     if(horario == ""+dia[i]+hora_inicio[i]+hora_fin[i]){
            //         if(error_horario>=1){
            //             // mensaje_info("Hay una coincidencia");    
            //             $('#'+id_dia[i]).addClass('errorform');
            //             $('#'+id_hora_inicio[i]).addClass('errorform');
            //             $('#'+id_hora_fin[i]).addClass('errorform');
            //             horario_repetido=1;
            //         }
                    
                            
            //         error_horario++;
            //     }

            // }
            for (var i = 0; i < dia.length; i++) {
                
                if(hora_inicio[i]>=hora_fin[i]){
                    $('#'+id_dia[i]).addClass('errorform');
                    $('#'+id_hora_inicio[i]).addClass('errorform');
                    $('#'+id_hora_fin[i]).addClass('errorform');
                    error_hora = 1;
                }
                else{
                    
                    if(horario == ""+dia[i]+hora_inicio[i]+hora_fin[i]){
                        if(error_horario>=1){
                            // mensaje_info("Hay una coincidencia");    
                            $('#'+id_dia[i]).addClass('errorform');
                            $('#'+id_hora_inicio[i]).addClass('errorform');
                            $('#'+id_hora_fin[i]).addClass('errorform');
                            horario_repetido=1;

                        }
                                  // mensaje_info(error_horario_traslape);              
                        error_horario++;
                    }

                    if(dia[j]==dia[i]){
                            // if(i==j){
                            //     i++;
                            // }
                            if(j!=i){
                                $('#'+id_dia[j]).addClass('errorform');
                                $('#'+id_hora_inicio[j]).addClass('errorform');
                                $('#'+id_hora_fin[j]).addClass('errorform');
                            }
                            // else{
                            //     $('#'+id_dia[j]).removeClass('errorform');
                            //     $('#'+id_hora_inicio[j]).removeClass('errorform');
                            //     $('#'+id_hora_fin[j]).removeClass('errorform');
                            // }
                            
                            if(
                                
                                (hora_inicio[j]>=hora_inicio[i] && hora_inicio[j]<=hora_fin[i])
                                ||
                                (hora_fin[j]>=hora_inicio[i] && hora_fin[j]<=hora_fin[i])
                            ){
                                if(error_horario_traslape>1){
                                    horario_traslape=1;
                                     // mensaje_info(horario_traslape);
                                }
                                //mensaje_info(error_horario_traslape);  
                                error_horario_traslape++;
                            }
                        }
                }
            }
        }

//         if(
//             dia[i]+hora_inicio[i]>=
//             )

// Lunes   00:00 02:00
// Martes  10:00 14:00
// Lunes   00:00 03:00
// Martes  20:00 22:00
// Lunes   01:00 02:00

//         if( 
//             (horario>horario_aux && horario<horario_aux) 
//             || 

//             )

//         $fecha_inicio BETWEEN [campo_fecha_inicio] AND [campo_fecha_fin]
// OR $fecha_fin BETWEEN [campo_fecha_inicio] AND [campo_fecha_fin]
// OR campo_fecha_inicio BETWEEN $fecha_inicio AND $fecha_fin  

        // mensaje_info(error_horario);
        // return false;
        // if(error_horario>2){
        // mensaje_info("Horarrio: "+horario_traslape);
        if(error_hora!=0){
            mensaje_error("La hora de inicio debe ser menor que la hora de fin");
            return false;
        }
        
        if(horario_repetido==1){
            mensaje_error("Los horarios seleccionados se repiten");
            return false;
        }

        if(horario_traslape==1 && (error_horario_traslape-num_elementos)>1){
            mensaje_error("Los horarios seleccionados para los días repetidos, se transponen");
            return false;
        }
        return true;   
        // alert("Dia: "+dia[0]+" hora inicio: "+ hora_inicio[0]+"hora fin:"+hora_fin[0]);
        // alert(dia[1]);
}
    

    

    


    //verifica si el estudio area ya existe
    $('#hnr_sircimbundle_estudiotype_profile_idArea, #hnr_sircimbundle_estudiotype_esNombre, #hnr_sircimbundle_estudiotype_esAbreviatura').on('change input', function(event) {
        
        /* Act on the event */
            
            var area = $('#hnr_sircimbundle_estudiotype_profile_idArea').val();
            var estudio = $('#hnr_sircimbundle_estudiotype_esNombre').val();
            var abreviatura = $('#hnr_sircimbundle_estudiotype_esAbreviatura').val();
            $('#hnr_sircimbundle_estudiotype_profile_idArea').removeClass('errorform');
            $('#hnr_sircimbundle_estudiotype_esAbreviatura').removeClass('errorform');
            $('#hnr_sircimbundle_estudiotype_esNombre').removeClass('errorform');
            $('.guardar').removeAttr('existe');

            // alert(estudio+" "+area);
            if(area=='')
                area=0;
            if(estudio=='')
                estudio=0;

            $.getJSON(Routing.generate('estudio_consultar',{area:area,estudio:estudio, abreviatura:abreviatura}),
                                function(data) {
                                    $.each(data.regs, function(indice, reg) {
                                    
                                        $('.guardar').attr('existe','1');
                                        $('#hnr_sircimbundle_estudiotype_profile_idArea').addClass('errorform');
                                        $('#hnr_sircimbundle_estudiotype_esAbreviatura').addClass('errorform');
                                        $('#hnr_sircimbundle_estudiotype_esNombre').addClass('errorform');
                                        mensaje_error("El estudio seleccionado ya está asociado en el área seleccionada");
                    
                                    });

                                });

    });


    $('.guardar').click(function(event) {
        var existe= $('.guardar').attr('existe');
        var existe_abre= $('.guardar').attr('existe_abre');
        if(!validarhorario()){
            return false;
        }
        
        if(existe==1){
            
                mensaje_error("El estudio seleccionado ya está asociado en el área seleccionada");
                return false;
        }
        if(existe_abre==1){
                mensaje_error("La abreviatura del estudio '"+$('#hnr_sircimbundle_estudiotype_esAbreviatura').val()+"' ya esta asignada");
                return false;
        }


       
                    $.getJSON(Routing.generate('estudio_create', { login:login }),
                                    function(data) {
                                        $.each(data.regs, function(indice, reg) {
                                            
                                        });

                                    });
       
       
            
            event.preventDefault();
        
        // $.getJSON(Routing.generate('estudio_update'),
        //                     function(data) {
        //                         $.each(data.regs, function(indice, reg) {
                                    
        //                             // alert(data.length);
        //                         });

        //                     });
 
        


        
    });


    $('#cancelar').click(function(event) {
        /* Act on the event */
        $('#deshabilitar').fadeOut('fast');
        $('#popup_box_estudio').fadeOut('fast');
        mensaje_info("Acción cancelada");
        return false;
    });


    $('.link').on('click', function(event) {
        event.preventDefault();
        /* Act on the event */

        $(this).parent('li').remove();

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
    
    if(editar!='editar_estudio'){
        addTagForm(collectionHolder, $newLinkLi);   //Muestra un formulario en la carga de la página nuevo
    }


    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm(collectionHolder, $newLinkLi);
    });



    // $(document).on('click', '.close', function(){
    //         alert('del');
    //     $(this).closest('#hnr_sircimbundle_estudioradiologicotype_placas').fadeOut(500, function() {
    //         $(this).remove();
    //     });
    // });

    
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
    
    // $('.hora_inicio, .hora_fin').on('change', function(event) {
    //     // $('.hora_inicio, .hora_fin').removeClass('errorform');
    //     validarhorario();
    // });


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

