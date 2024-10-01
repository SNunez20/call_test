$('document').ready(function(){
    $(".solo_numeros").keydown(function (e) {
        
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 40]) !== -1 ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        if(e.altKey){
            return false;
        }
    });
	
	$('body').on('keypress', 'input', function(args) {
    if (args.keyCode == 13) {
        $("#btnEntrar").click();
        return false;
    }
    });
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
function login(){
    $loginForm = $('#login-form');
    $usuario = $('#usuario').val();
    $contrasena = $('#contrasena').val();
    if($usuario == ''){
        $('#lblLogin').html("<img src='img/error.png' width='15px'> Ingrese Usuario !");
        $('#usuario').css("background-color", "#FFCECF");
    }else if($contrasena == ''){
        $('#lblLogin').html("<img src='img/error.png' width='15px'> Ingrese Contrasena !");
        $('#contrasena').css("background-color", "#FFCECF");
        $('#usuario').css("background-color", "white");
    }else{
        $('#lblLogin').html("");
        var $form 	 = $loginForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoLogin.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
						  window.location.href = "index.php";
						}else{
						    $('#lblLogin').html('<img src="img/error.png" width="15px"> Usuario y/o contrasena incorrectos !!');
                            $('#usuario').css("background-color", "#FFCECF");
                            $('#contrasena').css("background-color", "#FFCECF");
						}
					},
					error: function() {
						$('#lblLogin').html("<img src='img/error.png' width='15px'> Ocurrió un error,<br> intente en un momento.");
					}
				});  
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function numeroInicial(){
   $('#primaria').css("display","block");
   $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoInicial.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
                        $('#lblBienv').html(content.nombre);
                        $('#lblGrupo').html(content.grupo);
						if(content.result){
						  buscarAgendados();
                          buscarReferidos();
                          buscarVendidos();
						  habilitarBotones();
                          buscarInfo();
                          limpiarLabels();
						  $('#lblNumero').html(content.numero);
                          $('#lblLocalidad').html(content.localidad);
                          $('#primaria').css("display","none");
                          if(content.agendado){
                            $('#divAlert').css("display","block");
                          } 
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }else{
						          buscarAgendados();
                                  buscarReferidos();
                                  deshabilitarBotones();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
						          $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  $('#primaria').css("display","none");
                                  if(content.agendado){
                                    $('#divAlert').css("display","block");
                                  }
						      }  
						}
					},
					error: function() {
					    deshabilitarBotones();
                        limpiarLabels();
						$('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
				});  
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function vendido(){
$('#primaria').css("display","block");
$('#btnReagendar').hide();
$integrantes = $('#integrantesFamilia').val();
$direccion = $('#direccion').val();
$servicio = $('#servicio').val();
$observacion2 = $('#observacion2').val();
if($integrantes=='' || $direccion=='' || $observacion2=='' || ($('#checkServicio').is(':checked') && $servicio=='')){
  limpiarLabels();
  $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Complete todos los campos');
  $('#primaria').css("display","none");  
}else if (!/^([0-9])*$/.test($integrantes)){
     limpiarLabels();
     $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Integrantes de familia debe ser un numero !!!');
     $('#primaria').css("display","none"); 
}else{
    $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoVendido.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
                          buscarAgendados();
                          buscarReferidos();
                          buscarVendidos();
						  habilitarBotones();
                          limpiarNts();
                          limpiarSv();
                          limpiarTosnic();
                          limpiarTosic();
                          mostrarNts();
                          buscarInfo();
                          limpiarLabels();
						  $('#lblNumero').html(content.numero);
                          $('#lblLocalidad').html(content.localidad);
                          $('#divAtendio').hide();
                          $('#integrantesFamilia').val('');
                          $('#direccion').val('');
                          $('#checkServicio').prop("checked", false);
                          $('#servicio').val('');
                          $('#observacion2').val('');
                          $('#servicio').attr('disabled', true);
                          $('#servicio').css("background-color","#BCBCBC");
                          $('#primaria').css("display","none");
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }else{
                                  buscarAgendados();
                                  buscarReferidos();
                                  limpiarNts();
                                  limpiarSv();
                                  limpiarTosnic();
                                  limpiarTosic();
                                  mostrarNts();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
                                  $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  deshabilitarBotones();
                                  $('#divAtendio').hide();
                                  $('#integrantesFamilia').val('');
                                  $('#direccion').val('');
                                  $('#checkServicio').prop("checked", false);
                                  $('#servicio').val('');
                                  $('#observacion2').val('');
                                  $('#servicio').attr('disabled', true);
                                  $('#servicio').css("background-color","#BCBCBC");
                                  $('#primaria').css("display","none");
						      }   
						}
					},
					error: function() {
                        limpiarLabels();
					    deshabilitarBotones();
						$('#lblErrorVender').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
				});
  }  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function noInteresado(){
$('#primaria').css("display","block");
$('#btnReagendar').hide();
$integrantes = $('#integrantesFamilia').val();
$direccion = $('#direccion').val();
$servicio = $('#servicio').val();
$observacion2 = $('#observacion2').val();
if($integrantes=='' || $direccion=='' || $observacion2=='' || ($('#checkServicio').is(':checked') && $servicio=='')){
  limpiarLabels();
  $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Complete todos los campos');
  $('#primaria').css("display","none");  
}else if (!/^([0-9])*$/.test($integrantes)){
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Integrantes de familia debe ser un numero !!!');
    $('#primaria').css("display","none"); 
}else{
    $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoNoInteresado.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
                          buscarAgendados();
                          buscarReferidos();
						  habilitarBotones();
                          limpiarNts();
                          limpiarSv();
                          limpiarTosnic();
                          limpiarTosic();
                          mostrarNts();
                          buscarInfo();
                          limpiarLabels();
						  $('#lblNumero').html(content.numero);
                          $('#lblLocalidad').html(content.localidad);
                          $('#divAtendio').hide();
                          $('#integrantesFamilia').val('');
                          $('#direccion').val('');
                          $('#checkServicio').prop("checked", false);
                          $('#servicio').val('');
                          $('#observacion2').val('');
                          $('#servicio').attr('disabled', true);
                          $('#servicio').css("background-color","#BCBCBC");
                          $('#primaria').css("display","none");
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }else{
                                  buscarAgendados();
                                  buscarReferidos();
                                  limpiarNts();
                                  limpiarSv();
                                  limpiarTosnic();
                                  limpiarTosic();
                                  mostrarNts();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
						          $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  deshabilitarBotones();
                                  $('#divAtendio').hide();
                                  $('#integrantesFamilia').val('');
                                  $('#direccion').val('');
                                  $('#checkServicio').prop("checked", false);
                                  $('#servicio').val('');
                                  $('#observacion2').val('');
                                  $('#servicio').attr('disabled', true);
                                  $('#servicio').css("background-color","#BCBCBC");
                                  $('#primaria').css("display","none");
						      }    
						}
					},
					error: function() {
					    limpiarLabels();
                        $('#primaria').css("display","none");
                        $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        deshabilitarBotones();
					}
        });
     }  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function noContesta(){
    $('#primaria').css("display","block");
    $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoNoContesta.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
                          buscarAgendados();
                          buscarReferidos();
						  habilitarBotones();
                          buscarInfo();
                          limpiarLabels();
						  $('#lblNumero').html(content.numero);
                          $('#lblLocalidad').html(content.localidad);
                          $('#primaria').css("display","none");
						}else{
						   if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }else{
                                  buscarAgendados();
                                  buscarReferidos();
						          deshabilitarBotones();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
						          $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  $('#primaria').css("display","none");
						      }  
						}
					},
					error: function() {
					   deshabilitarBotones();
                       limpiarLabels();
						$('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
        });  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function quitarPermanente(){
    $('#primaria').css("display","block");
    $('#btnReagendar').hide();
    $obs = $('#observacion').val();
    if($obs == ''){
       limpiarLabels();
       $('#lblErrorListaNegra').html('<img src="img/error.png" width="15px"> Ingrese observacion');
       $('#primaria').css("display","none");
    }else{
    $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoQuitarPermanente.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
                          buscarAgendados();
                          buscarReferidos();
						  habilitarBotones();
                          buscarInfo();
                          limpiarLabels();
						  $('#lblNumero').html(content.numero);
                          $('#lblLocalidad').html(content.localidad);
                          $('#observacion').val('');
                          $('#divNoLlamarMas').hide();
                          $('#primaria').css("display","none");
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }else{
                                  buscarAgendados();
                                  buscarReferidos();
						          deshabilitarBotones();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
                                  $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  $('#observacion').val('');
                                  $('#divNoLlamarMas').hide();
                                  $('#primaria').css("display","none");
						      }  
						}
					},
					error: function() {
                        deshabilitarBotones();
                        limpiarLabels();
						$('#lblErrorListaNegra').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
        }); 
     } 
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agendado(){
$('#primaria').css("display","block");
$integrantes = $('#integrantesFamilia').val();
$direccion = $('#direccion').val();
$servicio = $('#servicio').val();
$observacion2 = $('#observacion2').val();
$nombre = $('#nom').val();
$fecha = $('#datetimepicker').val();
$comentario = $('#com').val();
if($nombre=='' || $fecha=='' || $comentario=='' || $integrantes=='' || $direccion=='' || $observacion2=='' || ($('#checkServicio').is(':checked') && $servicio=='')){
  limpiarLabels();
  $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Complete todos los campos');
  $('#primaria').css("display","none");  
}else if (!/^([0-9])*$/.test($integrantes)){
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Integrantes de familia debe ser un numero !!!');
    $('#primaria').css("display","none"); 
}else{
    $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoAgendar.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
						  buscarAgendados();
                          buscarReferidos();
						  habilitarBotones();
                          limpiarNts();
                          limpiarSv();
                          limpiarTosnic();
                          limpiarTosic();
                          mostrarNts();
                          buscarInfo();
                          limpiarLabels();
						  $('#lblNumero').html(content.numero);
                          $('#lblLocalidad').html(content.localidad);
                          $('#divAtendio').hide();
                          $('#nom').val('');
                          $('#datetimepicker').val('');
                          $('#com').val('');
                          $('#integrantesFamilia').val('');
                          $('#direccion').val('');
                          $('#divSiAtendio2').hide();
                          $('#checkServicio').prop("checked", false);
                          $('#servicio').val('');
                          $('#observacion2').val('');
                          $('#servicio').attr('disabled', true);
                          $('#servicio').css("background-color","#BCBCBC");
                          $('#primaria').css("display","none");
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }else{
                                  buscarAgendados();
                                  buscarReferidos();
						          deshabilitarBotones();
                                  limpiarNts();
                                  limpiarSv();
                                  limpiarTosnic();
                                  limpiarTosic();
                                  mostrarNts();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
                                  $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  $('#divAtendio').hide();
                                  $('#nom').val('');
                                  $('#datetimepicker').val('');
                                  $('#com').val('');
                                  $('#integrantesFamilia').val('');
                                  $('#direccion').val('');
                                  $('#divSiAtendio2').hide();
                                  $('#checkServicio').prop("checked", false);
                                  $('#servicio').val('');
                                  $('#observacion2').val('');
                                  $('#servicio').attr('disabled', true);
                                  $('#servicio').css("background-color","#BCBCBC");
                                  $('#primaria').css("display","none");
						      }   
						}
					},
					error: function() {
					   deshabilitarBotones();
						limpiarLabels();
                        $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
				});
  }  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function referido(){
    $('#primaria').css("display","block");
    $('#btnReagendar').hide();
    $nombre = $('#nomRef').val();
    $numero = $('#numRef').val();
    $observacion = $('#obsRef').val();
    if($nombre=='' || $numero=='' || $observacion==''){
        limpiarLabels();
        $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> Complete todos los campos');
        $('#primaria').css("display","none");  
    }else if (!/^([0-9])*$/.test($numero)){
        limpiarLabels();
        $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> Numero debe ser un numero !!!');
        $('#primaria').css("display","none"); 
    }else if((!/^(09)/.test($numero) || $numero.length != 9) && (!/^4/.test($numero) || $numero.length != 8) && (!/^2/.test($numero) || $numero.length != 8)){
        limpiarLabels();
        $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado!!!');
        $('#primaria').css("display","none"); 
    }else{
        $indexForm = $('#index-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoReferido.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
						  habilitarBotones();
                          buscarReferidos();
                          limpiarLabels();
                          $('#nomRef').val('');
                          $('#numRef').val('');
                          $('#obsRef').val('');
                          $('#divReferido').hide();
                          $('#primaria').css("display","none");
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
		                  }else if(content.repetido){
		                         limpiarLabels();
                                 $('#nomRef').val('');
                                 $('#numRef').val('');
                                 $('#obsRef').val('');
                                 $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> El numero esta en uso o ya fue usado !!!');
                                 $('#primaria').css("display","none");
                                 
		                  }else{
                                  buscarAgendados();
                                  buscarReferidos();
						          deshabilitarBotones();
                                  buscarInfo();
                                  limpiarLabels();
						          $('#lblNumero').html('');
                                  $('#lblLocalidad').html('');
                                  $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
                                  $('#nomRef').val('');
                                  $('#numRef').val('');
                                  $('#obsRef').val('');
                                  $('#divReferido').hide();
                                  $('#primaria').css("display","none"); 
						      }   
						}
					},
					error: function() {
					   deshabilitarBotones();
						limpiarLabels();
                        $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
				});
    }
    
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function desbloquear(){
    
    if($('#checkServicio').is(':checked')){
       $('#servicio').css("background-color","white");
       $('#servicio').prop( "disabled", false );
    }else{
       $('#servicio').css("background-color","#BCBCBC");
       $('#servicio').prop( "disabled", true );
       $('#servicio').val('');
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function verAgendar(){
    $('#divSiAtendio2').toggle();
    $('#btnReagendar').hide();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function deshabilitarBotones(){
    $('#btnAtendio').attr("disabled", true);
    $('#btnNoContesta').attr("disabled", true);
    $('#btnReferido').attr("disabled", true);
    $('#btnNoLLamarMas').attr("disabled", true);
	$('#lblLocalidad').css("display","none");
    $('#vineta').css("display","none");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function habilitarBotones(){  
    $('#btnAtendio').attr("disabled", false);
    $('#btnNoContesta').attr("disabled", false);
    $('#btnReferido').attr("disabled", false);
    $('#btnNoLLamarMas').attr("disabled", false);
    $('#btnVerAgendados').attr("disabled", false);
    $('#referidos').attr("disabled", false);
    $('#lblLocalidad').css("display","block");
    $('#vineta').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarAgendados(){
$('#Jtabla').DataTable().destroy();
var url="Ajax/listarAgendados.php";
$("#Jtabla tbody").html("");
$.getJSON(url,function(agendados){
$.each(agendados, function(i,agendados){
    
if(agendados.estado == 'vencido'){
   var tr = "<tr class = 'vencido'>"; 
}if(agendados.estado == 'del dia'){
   var tr = "<tr class = 'dehoy'>"; 
}if(agendados.estado == 'futuro'){
   var tr = "<tr>";
}
var newRow =
"</tbody>"
+tr
+"<td style='color:red;font-weight: bold;width:14%'>"+agendados.numero+"</td>"
+"<td style='width:14%'>"+agendados.nombre+"</td>"
+"<td style='font-weight: bold;width:14%;'>"+agendados.fecha_agendado+"</td>"
+"<td style='width:14%'>"+agendados.fecha+"</td>"
+"<td>"+agendados.comentario+"</td>"
+"<td style='width:10%'><input type='button' class='llamar' id='llamar' value='llamar' onclick='llamarAgendado("+"`"+agendados.numero+"`"+")' /></td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#Jtabla tbody");
});

if ($(window).width() <= 1400) {  
      tam = 2;
      search = false;          
}else{
      tam = 3;
      search = true;
}
$('.vencido').css('background-color','#FEDEE7');
$('.vencido td').css('background-color','#FEDEE7');
        
$('.dehoy').css('background-color','#B0FFBA');
$('.dehoy td').css('background-color','#B0FFBA');
$('#Jtabla').DataTable({
            lengthMenu: [tam],
            searching: search,
            paging: true,
            lengthChange: false,
            ordering: true,
            info: true,
            order: [2,'asc'],
            language : {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "No se encontraron resultados.",
                "sInfo":           "Mostrando _START_ al _END_ de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando del 0 al 0 de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
            },
     
            columnDefs: [ {
     
                targets: [ 0 ],
     
                orderData: [ 0, 1 ]
     
            }, {
     
                targets: [ 1 ],
     
                orderData: [ 1, 0 ]
     
            }, {
     
                targets: [3 ],
     
                orderData: [ 3, 0 ]
     
            } ]
     
        } );
        stateSave: true
});

llenarBadge();
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llamarAgendado(numero){
    $('#primaria').css("display","block");
    $.ajax({
        url 	: 'ajax/procesoLlamarAgendado.php?numero='+numero,
        method	: 'GET',
	    dataType: 'json',
        beforeSend:function(){
        },
        success: function(content) {
            console.log(content.id);
            if(content.result){
              $('#lblNumero').html(numero);
              $('#lblLocalidad').html(content.localidad);
			  habilitarBotones();
              buscarInfo();
			 limpiarLabels();
              $('#divVerAgendados').hide();
              $('#primaria').css("display","none");
            }else{
		     if(content.message == "Sin Sesion"){
                window.location.href = "login.php";
             }else{
                $('#primaria').css("display","none");
                $('#divVerAgendados').hide();
             }
            }
       },
        error: function() {
        deshabilitarBotones();
				limpiarLabels();
                $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
        }
        });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llamarReferido(numero){
    $('#primaria').css("display","block");
    $.ajax({
        url 	: 'ajax/procesoLlamarReferido.php?numero='+numero,
        method	: 'GET',
	    dataType: 'json',
        beforeSend:function(){
        },
        success: function(content) {
            console.log(content.id);
            if(content.result){
              $('#lblNumero').html(numero);
              $('#lblLocalidad').html(content.localidad);
			  habilitarBotones();
              buscarInfo();
              limpiarLabels();
              $('#divVerReferidos').hide();
              $('#primaria').css("display","none");
            }else{
		     if(content.message == "Sin Sesion"){
                window.location.href = "login.php";
             }else{
                $('#primaria').css("display","none");
                $('#divVerReferidos').hide();
             }
            }
       },
        error: function() {
             deshabilitarBotones();
             limpiarLabels();
             $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
             $('#primaria').css("display","none");
        }
        });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarVendidos(){
$('#Jtabla3').DataTable().destroy();
var url="Ajax/listarVendidos.php";
$("#Jtabla3 tbody").html("");
$.getJSON(url,function(vendidos){
$.each(vendidos, function(i,vendidos){
var newRow =
"</tbody>"
+"<tr>"
+"<td style='color:red;font-weight: bold;width:14%'>"+vendidos.numero+"</td>"
+"<td style='width:5%'>"+vendidos.int_familia+"</td>"
+"<td style='width:20%'>"+vendidos.direccion+"</td>"
+"<td style='width:10%'>"+vendidos.otro_servicio+"</td>"
+"<td style='width:25%'>"+vendidos.observaciones+"</td>"
+"<td style='width:15%'>"+vendidos.fecha+"</td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#Jtabla3 tbody");
});

if ($(window).width() <= 1400) {  
      tam = 2;
      search = false;          
}else{
      tam = 3;
      search = true;
}
$('#Jtabla3').DataTable({
            lengthMenu: [tam],
            searching: search,
            paging: true,
            lengthChange: false,
            ordering: true,
            info: true,
            order: [5,'asc'],
     
            columnDefs: [ {
     
                targets: [ 0 ],
     
                orderData: [ 0, 1 ]
     
            }, {
     
                targets: [ 1 ],
     
                orderData: [ 1, 0 ]
     
            }, {
     
                targets: [3 ],
     
                orderData: [ 3, 0 ]
     
            } ]
     
        } );
        stateSave: true
});
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarInfo(){
    var url="Ajax/listarInfo.php";
    $("#tblInfo tbody").html("");
    $.getJSON(url,function(info){
      if(info!= ""){
        $("#modalhover").css("pointer-events","all");
        $("#vineta").removeClass("vineta2");
        $("#vineta").addClass("vineta");
        $.each(info, function(i,info){
        var newRow2 =
        "</tbody>"
        +"<tr>"
        +"<td style='color:#004681'><img src='img/right-arrow.png' width='14px'>"+info.estado+"</td>"
        +"<td>"+info.fecha+"</td>"
        +"<td>"+info.integrantes+"</td>"
        +"<td>"+info.direccion+"</td>"
        +"<td>"+info.otro_servicio+"</td>"
        +"<td>"+info.observaciones+"</td>"
        +"</tr>"
        +"</tbody>";
        $(newRow2).appendTo("#tblInfo tbody");
        }); 
      }else{
        $("#modalhover").css("pointer-events","none");
        $("#vineta").removeClass("vineta");
        $("#vineta").addClass("vineta2");
      }    
});
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llenarBadge(){
             $.ajax({
                    url 	: 'ajax/procesoLlenarBadge.php',
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
						  $('#lblBadge1').html(content.hoy);
						}
					},
					error: function() {
                        $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
					}
            });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarReferidos(){
$('#Jtabla2').DataTable().destroy();
var url="Ajax/listarReferidos.php";
$("#Jtabla2 tbody").html("");
$.getJSON(url,function(referidos){
$.each(referidos, function(i,referidos){
var newRow =
"</tbody>"
+"<tr>"
+"<td style='color:red;font-weight: bold;width:14%'>"+referidos.numero+"</td>"
+"<td style='width:14%'>"+referidos.nombre+"</td>"
+"<td style='width:14%'>"+referidos.fecha+"</td>"
+"<td>"+referidos.observacion+"</td>"
+"<td style='width:10%'><input type='button' class='llamar' id='llamar2' value='llamar' onclick='llamarReferido("+"`"+referidos.numero+"`"+")' /></td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#Jtabla2 tbody");
});

if ($(window).width() <= 1400) {  
      tam = 2;
      search = false;          
}else{
      tam = 3;
      search = true;
}
$('#Jtabla2').DataTable({
            searching: search,
            paging: true,
            lengthMenu: [tam],
            lengthChange: false,
            ordering: true,
            info: true,
            order: [2,'asc'],
     
            columnDefs: [ {
                targets: [ 0 ],
     
                orderData: [ 0, 1 ]
     
            }, {
     
                targets: [ 1 ],
     
                orderData: [ 1, 0 ]
     
            }, {
     
                targets: [3 ],
     
                orderData: [ 3, 0 ]
     
            } ]
     
        } );
        stateSave: true
});
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarSv(){
    $("#btnSv").removeClass("botoncartilla");
    $("#btnSv").addClass("botoncartillaseleccionado");
    
    $("#btnNts").addClass("botoncartilla");
    $("#btnNts").removeClass("botoncartillaseleccionado");
    
    $("#btnTosic").addClass("botoncartilla");
    $("#btnTosic").removeClass("botoncartillaseleccionado");
    
    $("#btnTosnic").addClass("botoncartilla");
    $("#btnTosnic").removeClass("botoncartillaseleccionado");
    
    $('#sv').css("display","block");
    $('#nts').css("display","none");
    $('#tosic').css("display","none");
    $('#tosnic').css("display","none");
    
    $('#chksv').prop("checked", true);
    $('#chknts').prop("checked", false);
    $('#chktosic').prop("checked", false);
    $('#chktosnic').prop("checked", false);
    
    limpiarNts();
    limpiarTosic();
    limpiarTosnic();  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarNts(){
    $("#btnNts").removeClass("botoncartilla");
    $("#btnNts").addClass("botoncartillaseleccionado");
    
    $("#btnSv").addClass("botoncartilla");
    $("#btnSv").removeClass("botoncartillaseleccionado");
    
    $("#btnTosic").addClass("botoncartilla");
    $("#btnTosic").removeClass("botoncartillaseleccionado");
    
    $("#btnTosnic").addClass("botoncartilla");
    $("#btnTosnic").removeClass("botoncartillaseleccionado");
    
    $('#nts').css("display","block");
    $('#sv').css("display","none");
    $('#tosic').css("display","none");
    $('#tosnic').css("display","none");
    
    $('#chksv').prop("checked", false);
    $('#chknts').prop("checked", true);
    $('#chktosic').prop("checked", false);
    $('#chktosnic').prop("checked", false);
    
    limpiarSv();
    limpiarTosic();
    limpiarTosnic();
    
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarTosic(){
    $("#btnTosic").removeClass("botoncartilla");
    $("#btnTosic").addClass("botoncartillaseleccionado");
    
    $("#btnSv").addClass("botoncartilla");
    $("#btnSv").removeClass("botoncartillaseleccionado");
    
    $("#btnNts").addClass("botoncartilla");
    $("#btnNts").removeClass("botoncartillaseleccionado");
    
    $("#btnTosnic").addClass("botoncartilla");
    $("#btnTosnic").removeClass("botoncartillaseleccionado");
    
    $('#tosic').css("display","block");
    $('#sv').css("display","none");
    $('#nts').css("display","none");
    $('#tosnic').css("display","none");
    
    $('#chksv').prop("checked", false);
    $('#chknts').prop("checked", false);
    $('#chktosic').prop("checked", true);
    $('#chktosnic').prop("checked", false);
    
    limpiarNts();
    limpiarSv();
    limpiarTosnic();
    
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarTosnic(){
    $("#btnTosnic").removeClass("botoncartilla");
    $("#btnTosnic").addClass("botoncartillaseleccionado");
    
    $("#btnSv").addClass("botoncartilla");
    $("#btnSv").removeClass("botoncartillaseleccionado");
    
    $("#btnNts").addClass("botoncartilla");
    $("#btnNts").removeClass("botoncartillaseleccionado");
    
    $("#btnTosic").addClass("botoncartilla");
    $("#btnTosic").removeClass("botoncartillaseleccionado");
    
    $('#tosnic').css("display","block");
    $('#sv').css("display","none");
    $('#nts').css("display","none");
    $('#tosic').css("display","none");
    
    $('#chksv').prop("checked", false);
    $('#chknts').prop("checked", false);
    $('#chktosic').prop("checked", false);
    $('#chktosnic').prop("checked", true);
    
    limpiarNts();
    limpiarTosic();
    limpiarSv();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarSv(){
    $('#sva').prop("checked", false);
    $('#svb').prop("checked", false);
    $('#svc').prop("checked", false);
    $('#svd').prop("checked", false);
    $('#sve').prop("checked", false);
    $('#svf').prop("checked", false);
    $('#svg').prop("checked", false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarNts(){
    $('#ntsa').prop("checked", false);$('#ntsb').prop("checked", false);
    $('#ntsc').prop("checked", false);$('#ntsd').prop("checked", false);
    $('#ntse').prop("checked", false);$('#ntsf').prop("checked", false);
    $('#ntsg').prop("checked", false);$('#ntsh').prop("checked", false);
    $('#ntsi').prop("checked", false);$('#ntsj').prop("checked", false);
    $('#ntsk').prop("checked", false);$('#ntsl').prop("checked", false);
    $('#ntsm').prop("checked", false);$('#ntsn').prop("checked", false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarTosic(){
    $('#tosica').prop("checked", false);$('#tosicb').prop("checked", false);
    $('#tosicc').prop("checked", false);$('#tosicd').prop("checked", false);
    $('#tosice').prop("checked", false);$('#tosicf').prop("checked", false);
    $('#tosicg').prop("checked", false);$('#tosich').prop("checked", false);
    $('#tosici').prop("checked", false);$('#tosicj').prop("checked", false);
    $('#tosick').prop("checked", false);$('#tosicl').prop("checked", false);
    $('#tosicm').prop("checked", false);$('#tosicn').prop("checked", false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarTosnic(){
    $('#tosnica').prop("checked", false);$('#tosnicb').prop("checked", false);
    $('#tosnicc').prop("checked", false);$('#tosnicd').prop("checked", false);
    $('#tosnice').prop("checked", false);$('#tosnicf').prop("checked", false);
    $('#tosnicg').prop("checked", false);$('#tosnich').prop("checked", false);
    $('#tosnici').prop("checked", false);$('#tosnicj').prop("checked", false);
    $('#tosnick').prop("checked", false);$('#tosnicl').prop("checked", false);
    $('#tosnicm').prop("checked", false);$('#tosnicn').prop("checked", false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarLabels(){
    $('#lblErrorVender').html('');
    $('#lblErrorReferido').html('');
    $('#lblErrorListaNegra').html('');
    $('#lblError').html('');  
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////