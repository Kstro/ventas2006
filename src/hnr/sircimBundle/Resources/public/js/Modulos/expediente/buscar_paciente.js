jQuery(document).ready(function($) {
        $('#fecha_busqueda,#fecha_busqueda_trans').prueba({
        direction: false,
        format:'d-m-Y'
    });

    $('#opc_busqueda').hide();
    

    $('#comparar').on('click', function(event) {
        var num = 0;
        var href1 = "";
        var href2 = "";
        $('.comparacion').removeClass('errorform');
        $('.comparacion').each( function(index, val) {
            if($(this).is(':checked')){
                num++;
                if(href1==""){
                    href1 = $(this).attr('href');
                }                    
                else{
                    href2 = $(this).attr('href');
                }
                
                // alert(href);
            }
            else{
                $(this).addClass('errorform');
            }

            if(num==2){
                $('.comparacion').removeClass('errorform');
            }

        });
        if(num<2){
                mensaje_error("Debe seleciconar dos estudios para ser comparados");
        }
        else{
            $('#popup_box_comparar').fadeIn('slow');
            $('#imagen1').empty();
            $('#imagen2').empty();
            $('#imagen1').html("<div id='cargando'><img src='/bundles/hnrsircim/img/loading.gif'><div>");
            $('#imagen2').html("<div id='cargando'><img src='/bundles/hnrsircim/img/loading.gif'><div>");
            $('#deshabilitar').fadeIn('slow');
            // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
            var longitud = href1.length;
            href1= href1.substring(0,longitud-1);
            longitud = href2.length;
            href2= href2.substring(0,longitud-1);
            $('#imagen1').load(href2+"1");
            $('#imagen2').load(href1+"2");
        }

    });

    //Se encarga de buscar mediante ajax el historial del paciente
    $('#expediente').on('keyup', function(event) {

        $('#expediente').removeClass('errorform'); 
        if(event.keyCode!=13)
            event.preventDefault();
        else{
            if($('#expediente').val()==''){
                mensaje_error("Ingrese expediente de paciente...");
            }
            else{
                $('#opc_busqueda').hide();        
                var parameters = $(this).val();
                var expediente = $('#expediente').val(); 
                var estudio = $('#desplegable_busqueda').val();
                var fecha = $('#fecha_busqueda').val();
                $('.primero').removeClass('checked'); 
                $('#input_cita').prop('checked', false); 
                $('.ultimo').addClass('checked'); 
                $('#input_estudio').prop('checked', true); 
                
                $('#expediente').removeClass('errorform'); 
                $('#paciente_historial').html("<div id='cargando'><img src=\"/bundles/hnrsircim/img/loading.gif\"><div>");
 
                $('#paciente_historial').load(Routing.generate('diagnostico_exp', { exp: parameters, estudio:0,fecha:0 }));
                // $('.ultimo').addClass('checked');
            }
        }
        
            
    });

    


    $('#buscar').on('click', function(event) {
        $('#opc_busqueda').toggle('fast');
        $('#fecha_busqueda').removeClass('errorform'); 
    });

    $('#fecha_busqueda, #desplegable_busqueda').on('click', function(event) {
        // $('#opc_busqueda').toggle('fast');
        $(this).removeClass('errorform');
    });

    $('#fecha_busqueda_trans, #desplegable_busqueda_trans').on('click', function(event) {
        // $('#opc_busqueda').toggle('fast');
        $(this).removeClass('errorform');
    });

    $('.buscar').on('click', function(event) {

        var expediente = $('#expediente').val(); 
        var estudio = $('#desplegable_busqueda').val();
        var fecha = $('#fecha_busqueda').val();
        
        if(fecha=='')
            fecha=0;
        if(estudio=='')
            estudio=0;
        if(expediente==''){
            mensaje_error("Ingrese expediente de paciente...");
            $('#expediente').addClass('errorform'); 
        }
        else{
            if(fecha==0 && estudio==0){
                mensaje_error("Seleccione un parametro para realizar la búsqueda");
                $('#fecha_busqueda').addClass('errorform'); 
                $('#desplegable_busqueda').addClass('errorform'); 
            }
            else{
                // alert(estudio+fecha);
                // $('#paciente_historial').html('<div id="cargando"><img src={{ asset('bundles/hnrsircim/img/loading.gif') }}><div>');

                $('#paciente_historial').load(Routing.generate('diagnostico_exp', { exp: expediente, estudio:estudio,fecha:fecha }));
                $('#opc_busqueda').toggle('fast');
                
        
                // label.closest('.menu_grupo').find("label").removeClass('checked'); 
                $('.primero').removeClass('checked'); 
                $('#input_cita').prop('checked', false); 
                $('.ultimo').addClass('checked'); 
                $('#input_estudio').prop('checked', true); 
            
                  
            }
        }
        
        // alert(fecha);

        // $('#opc_busqueda').toggle('fast');
        
    });

    $(".menu_grupo label:not(.checked)").click(function(){ 
        $('#opc_busqueda').hide();
        var label = $(this); 
        var input = $('#' + label.attr('for')); 
        var href = label.attr('href'); 
        if(!input.prop('checked')){ 
            label.closest('.menu_grupo').find("label").removeClass('checked'); 
            label.addClass('checked'); 
            input.prop('checked', true); 
        } 
    });


 $('#input_cita').click(function(){ 
        $('.paciente_estudios').toggle();
        $('.paciente_citas').delay(600).fadeIn();
    });

    $('#input_estudio').click(function(){ 
        $('.paciente_citas').toggle();
        $('.paciente_estudios').delay(600).fadeIn();
        
    });
    

});


    