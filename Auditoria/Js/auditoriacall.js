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
function loginAuditoria(){
    $loginForm = $('#login-form');
    $usuario = $('#usuario').val();
    $contrasena = $('#contrasena').val();
    if($usuario == ''){
        $('#lblLogin').html("Ingrese Usuario !!");
        $('#usuario').css("background-color", "#FFCECF");
    }else if($contrasena == ''){
        $('#lblLogin').html("Ingrese Contrasena !!");
        $('#contrasena').css("background-color", "#FFCECF");
        $('#usuario').css("background-color", "white");
    }else{
        $('#lblLogin').html("");
        var $form 	 = $loginForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoLoginAuditoria.php',
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
						    $('#lblLogin').html('Usuario y/o contrasena incorrectos !!');
                            $('#usuario').css("background-color", "#FFCECF");
                            $('#contrasena').css("background-color", "#FFCECF");
						}
					},
					error: function() {
						$('#lblLogin').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
					}
				});  
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function procesoInicial(){
   $('#primaria').css("display","block");
			    $.ajax({
					url 	: 'ajax/procesoInicial.php',
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
                        $('#lblBienv').html(content.nombre);
                        $('#lblGrupo').html(content.nomGrupo);
						if(content.result){
                          $('#primaria').css("display","none");
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
						      }  
						}
					},
					error: function() {
						$('#lblError').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
				});  
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarAgendados(desde,hasta,limpiar,valor){
    ocultarDivs();
    $('#contenidolateral').height('150%');
    if(limpiar){
       $('#desde').val("");
       $('#hasta').val("");
    }
    $('#Jtabla').DataTable().destroy();
    $('#Jtabla').DataTable({
        lengthMenu: [10],
        searching: true,
        paging: true,
        oLanguage: {
        "sUrl": "../admin/tabla/examples/server_side/scripts/spanish.json"
            },
        lengthChange: false,
        ordering: true,
        info: true,
        order: [0,'desc'],
        processing: true,
        serverSide: true,
        ajax: "Ajax/listarAgendados.php?desde="+desde+"&hasta="+hasta,
        columnDefs: [ {
            targets: -1,
            data: null,
            defaultContent: "<button id='evaluar' class = 'llamar' onclick='mostrarEvaluar($(`#Jtabla`).DataTable().row( $(this).parents(`tr`) ).data())'>Evaluar</button>"
        } ]
    } );
    
    $('#Agendados').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ocultarDivs(){
    $('#Agendados').css("display","none");
    $('#Evaluados').css("display","none");
    $('#Exportacion').css("display","none");
    $('#contenidolateral').height('100%');
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarEvaluar(data){
    if(data[7] == 'No'){
        $('#divEvaluar').css("display","block");
        $('#numAge').val(data[1]);
        $('#usuEval').val(data[2]);
        $('#fechaAgendada').val(data[4]);
        $('#fechaDeAgendado').val(data[6]);
        $('#idAgeEval').val(data[0]); 
        $('#nomCall').val(data[3]);

     }else{
        alert("Ese numero ya fue evaluado.");
     }   
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarExportar(){
    ocultarDivs();
    $('#Exportacion').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function evaluar(){
    $('#primaria').css("display","block");
    $comentario = $('#comEval').val();
    $puntaje = $( "#puntaje option:selected" ).val();
    if($comentario == '' || $puntaje == ''){
       $('#lblErrorEvaluar').html('Complete todos los campos');
       $('#primaria').css("display","none");
    }else{
    $indexForm = $('#evaluar-form');
        var $form 	 = $indexForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/evaluarAgendados.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
						  //listarAgendados(desde.value,hasta.value);
                          $('#Jtabla').DataTable().clear().draw();
                          $('#lblErrorEvaluar').html('');
                          $('#numAge').val('');
                          $('#comEval').val('');
                          $('#puntaje').prop("selectedIndex", 0);
                          $('#divEvaluar').hide();
                          $('#primaria').css("display","none");
						}else{
						   $('#lblErrorEvaluar').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                           $('#primaria').css("display","none");
						}
					},
					error: function() {
						$('#lblErrorEvaluar').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
        }); 
     } 
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarEvaluados(desde,hasta,limpiar){
    ocultarDivs();
    $('#contenidolateral').height('150%');
    if(limpiar){
       $('#desde2').val("");
       $('#hasta2').val("");
    }
    $('#Jtabla2').DataTable().destroy();
    $('#Jtabla2').DataTable({
        lengthMenu: [10],
        searching: true,
        paging: true,
        oLanguage: {
        "sUrl": "../admin/tabla/examples/server_side/scripts/spanish.json"
            },
        lengthChange: false,
        ordering: true,
        info: true,
        order: [9,'desc'],
        processing: true,
        serverSide: true,
        ajax: "Ajax/listarEvaluados.php?desde="+desde+"&hasta="+hasta,
        
    } );
    $('#Evaluados').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////