

    jQuery(document).ready(function($) {
        

    $('#listado_acciones').perfectScrollbar({
              wheelSpeed: 20,
              wheelPropagation: true
            });        

        

        $('.cerrar').on('click', function(event) {
            $('#popup_box_bitacora').fadeOut('slow');
            $('#deshabilitar').fadeOut('slow');
            return false;
        });  

        $('#bitacora_fecha').prueba({
            direction: false,
            format:'d/m/Y',
            onSelect: function(){
                        filtrarBitacora();
                    },
            onClear:  function(){
                        filtrarBitacora();
                    }
        });


        $('#desplegable_bitacora').on('change', function(event) {
            // event.preventDefault();
            /* Act on the event */

            filtrarBitacora();
            
        });

        function filtrarBitacora(){

            var login = $('#nombre_usr label').attr('id');
            var accion = $('#desplegable_bitacora').val();
            var fecha = $('#bitacora_fecha').val();
            
            $('#listado_acciones').scrollTop(0);
            fecha = fecha.replace(/\//g,"-"); 
            // alert(accion+fecha);
            if(fecha==''){
                fecha=0;
            }

            var href = Routing.generate('bitacora_filtro', { login:login, accion: accion, fecha:fecha });
            // $('#listado_acciones').fadeIn('slow');
            $('#registros').html("<div id='cargando'><img src='/bundles/hnrsircim/img/loading.gif'><div>");
            // $('#deshabilitar').fadeIn('slow');
            // cargar mediante una llamada ajax la dirección que tiene href dentro de resultado 
            $('#registros').load(href);
            // alert("bieeen!");       

        }

    });

        
