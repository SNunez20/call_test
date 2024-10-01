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
function loginAdmin(){
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
					url 	: 'ajax/procesoLoginAdmin.php',
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
function listarHistorico(desde,hasta,limpiar){
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
        ajax: "Ajax/listarHistorico.php?desde="+desde+"&hasta="+hasta,
        columnDefs: [ {
            targets: -1,
            data: null,
            defaultContent: "<button id='ver' class = 'llamar' onclick='mostrarOfrecidos($(`#Jtabla`).DataTable().row( $(this).parents(`tr`) ).data())'>Ver</button>"
        } ]
    } );
    $('#historico').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarOfrecidos(data){
    if(data[6] != ''){
        if(data[6]=='nts'){
            ofrecidosNTS(data[0]);
        }
        if(data[6]=='tosnic'){
           ofrecidosTOSNIC(data[0]); 
        }
        if(data[6]=='tosic'){
           ofrecidosTOSIC(data[0]); 
        }
        if(data[6]=='sv'){
           ofrecidosSV(data[0]); 
        }
    } 
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ofrecidosNTS(idhistorico){
$('#JtablaNTS').DataTable().destroy();
var url="Ajax/listarNTS.php?id="+idhistorico;
$("#JtablaNTS tbody").html("");
$.getJSON(url,function(nts){
$.each(nts, function(i,nts){

var newRow =
"</tbody>"
+"<tr>"
+"<td align='center'>"+nts.san+"</td>"
+"<td align='center'>"+nts.con+"</td>"
+"<td align='center'>"+nts.dom+"</td>"
+"<td align='center'>"+nts.rei+"</td>"
+"<td align='center'>"+nts.amp+"</td>"
+"<td align='center'>"+nts.ampp+"</td>"
+"<td align='center'>"+nts.asse+"</td>"
+"<td align='center'>"+nts.assp+"</td>"
+"<td align='center'>"+nts.hot+"</td>"
+"<td align='center'>"+nts.gru+"</td>"
+"<td align='center'>"+nts.tar+"</td>"
+"<td align='center'>"+nts.fb+"</td>"
+"<td align='center'>"+nts.sup+"</td>"
+"<td align='center'>"+nts.pro+"</td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaNTS tbody");
});
$('#JtablaNTS').DataTable({
            lengthMenu: [1],
            searching: false,
            paging: false,
            lengthChange: false,
            ordering: false,
            info: false,
        } );
        stateSave: true
});
    $('#divVerOfrecidosNTS').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ofrecidosTOSNIC(idhistorico){
$('#JtablaTOSNIC').DataTable().destroy();
var url="Ajax/listarTOSNIC.php?id="+idhistorico;
$("#JtablaTOSNIC tbody").html("");
$.getJSON(url,function(tosnic){
$.each(tosnic, function(i,tosnic){

var newRow =
"</tbody>"
+"<tr>"
+"<td align='center'>"+tosnic.san+"</td>"
+"<td align='center'>"+tosnic.con+"</td>"
+"<td align='center'>"+tosnic.dom+"</td>"
+"<td align='center'>"+tosnic.rei+"</td>"
+"<td align='center'>"+tosnic.amp+"</td>"
+"<td align='center'>"+tosnic.ampp+"</td>"
+"<td align='center'>"+tosnic.asse+"</td>"
+"<td align='center'>"+tosnic.assp+"</td>"
+"<td align='center'>"+tosnic.hot+"</td>"
+"<td align='center'>"+tosnic.gru+"</td>"
+"<td align='center'>"+tosnic.tar+"</td>"
+"<td align='center'>"+tosnic.fb+"</td>"
+"<td align='center'>"+tosnic.sup+"</td>"
+"<td align='center'>"+tosnic.pro+"</td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaTOSNIC tbody");
});
$('#JtablaTOSNIC').DataTable({
            lengthMenu: [1],
            searching: false,
            paging: false,
            lengthChange: false,
            ordering: false,
            info: false,
        } );
        stateSave: true
});
    $('#divVerOfrecidosTOSNIC').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ofrecidosTOSIC(idhistorico){
$('#JtablaTOSIC').DataTable().destroy();
var url="Ajax/listarTOSIC.php?id="+idhistorico;
$("#JtablaTOSIC tbody").html("");
$.getJSON(url,function(tosic){
$.each(tosic, function(i,tosic){

var newRow =
"</tbody>"
+"<tr>"
+"<td align='center'>"+tosic.san+"</td>"
+"<td align='center'>"+tosic.con+"</td>"
+"<td align='center'>"+tosic.dom+"</td>"
+"<td align='center'>"+tosic.rei+"</td>"
+"<td align='center'>"+tosic.amp+"</td>"
+"<td align='center'>"+tosic.ampp+"</td>"
+"<td align='center'>"+tosic.asse+"</td>"
+"<td align='center'>"+tosic.assp+"</td>"
+"<td align='center'>"+tosic.hot+"</td>"
+"<td align='center'>"+tosic.gru+"</td>"
+"<td align='center'>"+tosic.tar+"</td>"
+"<td align='center'>"+tosic.fb+"</td>"
+"<td align='center'>"+tosic.sup+"</td>"
+"<td align='center'>"+tosic.pro+"</td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaTOSIC tbody");
});
$('#JtablaTOSIC').DataTable({
            lengthMenu: [1],
            searching: false,
            paging: false,
            lengthChange: false,
            ordering: false,
            info: false,
        } );
        stateSave: true
});
    $('#divVerOfrecidosTOSIC').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ofrecidosSV(idhistorico){
$('#JtablaSV').DataTable().destroy();
var url="Ajax/listarSV.php?id="+idhistorico;
$("#JtablaSV tbody").html("");
$.getJSON(url,function(sv){
$.each(sv, function(i,sv){

var newRow =
"</tbody>"
+"<tr>"
+"<td align='center'>"+sv.san+"</td>"
+"<td align='center'>"+sv.con+"</td>"
+"<td align='center'>"+sv.dom+"</td>"
+"<td align='center'>"+sv.rei+"</td>"
+"<td align='center'>"+sv.ampp+"</td>"
+"<td align='center'>"+sv.assp+"</td>"
+"<td align='center'>"+sv.hot+"</td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaSV tbody");
});
$('#JtablaSV').DataTable({
            lengthMenu: [1],
            searching: false,
            paging: false,
            lengthChange: false,
            ordering: false,
            info: false,
        } );
        stateSave: true
});
    $('#divVerOfrecidosSV').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ocultarDivs(){
    $('#historico').css("display","none");
    $('#agregarUsuario').css("display","none");
    $('#agregarCall').css("display","none");
    $('#listarUsuarios').css("display","none");
    $('#listarCalls').css("display","none");
    $('#agregarAdmin').css("display","none");
    $('#listarAdmins').css("display","none");
    $('#quitarNumero').css("display","none");
    $('#listarQuitarNumeros').css("display","none");
    $('#contenidolateral').height('100%');
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarAgregarUsuario(){
    ocultarDivs();
    $('#agregarUsuario').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarAgregarCall(){
    ocultarDivs();
    $('#agregarCall').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarAgregarAdmin(){
    ocultarDivs();
    $('#agregarAdmin').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarQuitarNumero(){
    ocultarDivs();
    $('#quitarNumero').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function comprobarCI(cedi){
        //controlci();
        //Inicializo los coefcientes en el orden correcto
        var arrCoefs = [2,9,8,7,6,3,4,1];
        var suma = 0;
        //Para el caso en el que la CI tiene menos de 8 digitos
        //calculo cuantos coeficientes no voy a usar
        var difCoef = parseInt(arrCoefs.length - cedi.length);
        //var difCoef = parseInt(arrCoefs.length � ci.length);
        //recorro cada digito empezando por el de m�s a la derecha
        //o sea, el digito verificador, el que tiene indice mayor en el array
        for(var i=cedi.length - 1; i> -1;i--){
        //for (var i = ci.length � 1; i > -1; i�) {
        //ooObtengo el digito correspondiente de la ci recibida
        var dig = cedi.substring(i, i+1);
        //Lo ten�a como caracter, lo transformo a int para poder operar
        var digInt = parseInt(dig);
        //Obtengo el coeficiente correspondiente al �sta posici�n del digito
        var coef = arrCoefs[i+difCoef];
        //Multiplico d�gito por coeficiente y lo acumulo a la suma total
        suma = suma + digInt * coef;
        }
        // si la suma es m�ltiplo de 10 es una ci v�lida
        if ( (suma % 10) == 0 ) {
            return true;
        } else {
            return false;
            }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agregarUsuario(){
    $('#primaria').css("display","block");
    $nombre = $('#nomUsu').val();
    $cedula = $('#cedUsu').val();
    $grupo = $( "#grupoUsu option:selected" ).val();
    if($nombre=='' || $cedula=='' || $grupo==''){
        $('#lblErrorUsu').html('Complete todos los campos');
        $('#primaria').css("display","none");  
    }else if (!/^([0-9])*$/.test($cedula)){
        $('#lblErrorUsu').html('Cedula debe ser un numero !!!');
        $('#primaria').css("display","none"); 
    }else{
        var ciok = comprobarCI($cedula);
        if(ciok){
            $agregarUsu = $('#agregarUsu-form');
            var $form 	 = $agregarUsu;
					
            var $data 	 = $form.serialize();
				    var ua = navigator.userAgent.toLowerCase();
                
			     $.ajax({
					   url 	: 'ajax/procesoAgregarUsuario.php',
					   data 	: $data,
					   method	: 'POST',
     				   dataType: 'json',
 				   beforeSend:function(){
					   },
					   success: function(content) {
						  console.log(content.id);
						  if(content.result){
                                if(!content.repetido){
                                    listarUsuarios();
                                    $('#lblErrorUsu').html('');
                                    $('#nomUsu').val('');
                                    $('#cedUsu').val('');
                                    $( "#grupoUsu").prop("selectedIndex", 0);
                                    $('#primaria').css("display","none");
                                }else{
                                    $('#lblErrorUsu').html('La cedula ya existe !!!');
                                    $('#primaria').css("display","none");
                                }
                                
						  }else{
						      if(content.message == "Sin Sesion"){
						              window.location.href = "login.php";
						          }else{
                                    $('#lblErrorUsu').html('Error');
                                    $('#primaria').css("display","none");
						          }   
						  }
					   },
					   error: function() {
					    
						  $('#lblErrorUsu').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                            $('#primaria').css("display","none");
					   }
				    });
        }else{
            $('#lblErrorUsu').html('La cedula esta mal !!!');
            $('#primaria').css("display","none");
        }
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agregarCall(){
    $('#primaria').css("display","block");
    $nombre = $('#nomCall').val();
    if($nombre==''){
        $('#lblErrorCall').html('Complete el nombre');
        $('#primaria').css("display","none");  
    }else{
            $agregarCall = $('#agregarCall-form');
            var $form 	 = $agregarCall;
					
            var $data 	 = $form.serialize();
				    var ua = navigator.userAgent.toLowerCase();
                
			     $.ajax({
					   url 	: 'ajax/procesoAgregarCall.php',
					   data 	: $data,
					   method	: 'POST',
     				   dataType: 'json',
 				   beforeSend:function(){
					   },
					   success: function(content) {
						  console.log(content.id);
						  if(content.result){
						        listarCall();
                                $('#lblErrorCall').html('');
                                $('#nomCall').val('');
                                $('#grupoUsu').append("<option value="+"'"+content.idcall+"'"+">"+content.nombrecall+"</option>");
                                $('#grupoUsuAdmin').append("<option value="+"'"+content.idcall+"'"+">"+content.nombrecall+"</option>");
                                $('#primaria').css("display","none");     
						  }else{
						      if(content.message == "Sin Sesion"){
						              window.location.href = "login.php";
						          }else if(content.repetido){
						             $('#lblErrorCall').html('El call ya existe');
                                    $('#primaria').css("display","none"); 
						          }else{
                                    $('#lblErrorCall').html('Error');
                                    $('#primaria').css("display","none");
						          }     
						  }
					   },
					   error: function() {
					    
						  $('#lblErrorCall').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                            $('#primaria').css("display","none");
					   }
				    });
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarUsuarios(){
$('#contenidolateral').height('150%');
$('#JtablaUsu').DataTable().destroy();
var url="Ajax/listarUsuarios.php";
$("#JtablaUsu tbody").html("");
$.getJSON(url,function(usuarios){
$.each(usuarios, function(i,usuarios){

var newRow =
"</tbody>"
+"<tr>"
+"<td align='center'>"+usuarios.id+"</td>"
+"<td align='center'>"+usuarios.nombre+"</td>"
+"<td align='center'>"+usuarios.cedula+"</td>"
+"<td align='center'>"+usuarios.grupo+"</td>"
+"<td align='center'>"+usuarios.activo+"</td>"
+"<td style='width:10%'><input type='button' class='llamar2' id='eliminarUsu' value='Eliminar' onclick='if(confirm(`Seguro desea eliminar el ususario?`)) eliminarUsuario("+"`"+usuarios.id+"`"+",$(this).parents(`tr`))' /></td>"
+"<td style='width:10%'><input type='button' class='llamar' id='suspenderUsu' value='Suspender' onclick='if(confirm(`Seguro desea suspender el ususario?`)) suspenderUsuario("+"`"+usuarios.id+"`"+",$(this).parents(`tr`))' /></td>"
+"<td style='width:10%'><input type='button' class='llamar' id='reactivarUsu' value='Re-Activar' onclick='if(confirm(`Seguro desea re-activar el ususario?`)) reactivarUsuario("+"`"+usuarios.id+"`"+",$(this).parents(`tr`))' /></td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaUsu tbody");
});
$('#JtablaUsu').DataTable({
            lengthMenu: [10],
            searching: true,
            paging: true,
            lengthChange: false,
            ordering: true,
            info: true,
            order: [0,'desc'],
            oLanguage: {
                "sUrl": "../admin/tabla/examples/server_side/scripts/spanish.json"
            },
            dom: 'Bfrtip',
            buttons: [
                'excel'
            ]
        } );
        stateSave: true
});
    $('#listarUsuarios').css("display","block");   
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function suspenderUsuario(usuario,rowActual){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/procesoSuspenderUsu.php?usuario='+usuario,
	   method	: 'GET',
	   dataType: 'json',
	    beforeSend:function(){
					   },
		   success: function(content) {
			  console.log(content.id);
			  if(content.result){
                    var d = $('#JtablaUsu').DataTable().row(rowActual).data();
                    d[4] = "No";
                    $('#JtablaUsu').DataTable().row(rowActual).data(d);
                    $('#primaria').css("display","none");     
              }else{
                if(content.message == "Sin Sesion"){
		              window.location.href = "login.php";
                }else{
                    alert('ERROR');
                    $('#primaria').css("display","none");
                }   
			  }
		      },
		      error: function() {
					    
			    alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
		      }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function reactivarUsuario(usuario,rowActual){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/procesoReactivarUsu.php?usuario='+usuario,
	   method	: 'GET',
	   dataType: 'json',
	    beforeSend:function(){
					   },
		   success: function(content) {
			  console.log(content.id);
			  if(content.result){
                    var d = $('#JtablaUsu').DataTable().row(rowActual).data();
                    d[4] = "Si";
                    $('#JtablaUsu').DataTable().row(rowActual).data(d);
                    $('#primaria').css("display","none");     
              }else{
                if(content.message == "Sin Sesion"){
		              window.location.href = "login.php";
                }else{
                    alert('ERROR');
                    $('#primaria').css("display","none");
                }   
			  }
		      },
		      error: function() {
					    
			    alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
		      }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function eliminarUsuario(usuario,rowActual){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/procesoEliminarUsu.php?usuario='+usuario,
	   method	: 'GET',
	   dataType: 'json',
	    beforeSend:function(){
					   },
		   success: function(content) {
			  console.log(content.id);
			  if(content.result){
                    $('#JtablaUsu').DataTable().row(rowActual).remove().draw(true);
                    $('#primaria').css("display","none");   
              }else{
                if(content.message == "Sin Sesion"){
		              window.location.href = "login.php";
                }else{
                    alert('ERROR');
                    $('#primaria').css("display","none");
                }   
			  }
		      },
		      error: function() {
					    
			    alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
		      }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarCall(){
$('#contenidolateral').height('220%');
$('#JtablaCall').DataTable().destroy();
var url="Ajax/listarCalls.php";
$("#JtablaCall tbody").html("");
$.getJSON(url,function(calls){
$.each(calls, function(i,calls){

var newRow =
"</tbody>"
+"<tr>"
+"<td align='center'>"+calls.id+"</td>"
+"<td align='center'>"+calls.nombre+"</td>"
+"<td align='center'>"+calls.vendedor+"</td>"
+"<td align='center'>"+calls.grupos+"</td>"
+"<td style='width:10%'><input type='button' class='llamar' id='editarGrupos' value='Editar Grupos' onclick='editarGrupos("+calls.id+","+"`"+calls.nombre+"`"+")' /></td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaCall tbody");
});
$('#JtablaCall').DataTable({
            lengthMenu: [15],
            searching: true,
            paging: true,
            lengthChange: false,
            ordering: true,
            info: true,
            order: [1,'asc'],
            oLanguage: {
                "sUrl": "../admin/tabla/examples/server_side/scripts/spanish.json"
            },
        } );
        stateSave: true
});
    
    $('#listarCalls').css("display","block");   
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function editarGrupos(idcall,nombreCall){
    $('#divEditarGrupos').css("display","block");
    var boton = "";
    $('#btnAgregarGrupo').remove();
    $('#lblErrorAgregarGrupo').html('');
    $( "#grupoTel").prop("selectedIndex", 0);
    boton = "<input type='button' id='btnAgregarGrupo' value='Agregar Grupo'onclick='agregarGrupos("+idcall+")'/>";
    $(boton).appendTo("#modal-body-grupos");
    $('#lblCall').html(nombreCall);
    listarGrupos(idcall);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarGrupos(idcall){
    $('#JtablaGrupos').DataTable().destroy();
    var url="Ajax/listarGrupos.php?call="+idcall;
    $("#JtablaGrupos tbody").html("");
    $.getJSON(url,function(grupos){
    $.each(grupos, function(i,grupos){

    var newRow =
    "</tbody>"
    +"<tr>"
    +"<td align='center'>"+grupos.call+"</td>"
    +"<td align='center'>"+grupos.grupo+"</td>"
    +"<td style='width:10%'><input type='button' class='llamar' id='eliminarGrupo' value='Eliminar' onclick='if(confirm(`Seguro desea eliminar el grupo?`)) eliminarGrupo("+"`"+grupos.id+"`"+",$(this).parents(`tr`))' /></td>"
    +"</tr>"
    +"</tbody>";
    $(newRow).appendTo("#JtablaGrupos tbody");
    });
    $('#JtablaGrupos').DataTable({
            lengthMenu: [4],
            searching: false,
            paging: true,
            lengthChange: false,
            ordering: true,
            info: false,
            order: [1,'asc'],
            oLanguage: {
                "sUrl": "../admin/tabla/examples/server_side/scripts/spanish.json"
            },
        } );
        stateSave: true
});
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agregarGrupos(idcall){
    $grupo = $("#grupoTel option:selected" ).val();
    if($grupo == 0){
        $('#lblErrorAgregarGrupo').html('Seleccione un grupo');
    }else{
			     $.ajax({
					   url 	: 'ajax/procesoAgregarGrupo.php?idcall='+idcall+'&grupo='+$grupo,
					   method	: 'GET',
     				   dataType: 'json',
 				   beforeSend:function(){
					   },
					   success: function(content) {
						  console.log(content.id);
						  if(content.result){
                                $('#lblErrorAgregarGrupo').html('');
                                $( "#grupoTel").prop("selectedIndex", 0);
						        listarGrupos(idcall);
                                listarCall();
                                $('#primaria').css("display","none");     
						  }else{
						      if(content.message == "Sin Sesion"){
						              window.location.href = "login.php";
						          }else if(content.repetido){
						              $('#lblErrorAgregarGrupo').html('Ya tiene ese grupo asignado');
                                      $('#primaria').css("display","none");
						          }else{
                                    $('#lblErrorAgregarGrupo').html('Error');
                                    $('#primaria').css("display","none");
						          }       
						  }
					   },
					   error: function() {
					    
						  $('#lblErrorAgregarGrupo').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                            $('#primaria').css("display","none");
					   }
				    });
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function eliminarGrupo(idgrupo,rowEliminada){
    $.ajax({
	   url 	: 'ajax/procesoEliminarGrupo.php?idgrupo='+idgrupo,
	   method	: 'GET',
	   dataType: 'json',
	   beforeSend:function(){
	   },
	   success: function(content) {
		  console.log(content.id);
			 if(content.result){
                $('#lblErrorAgregarGrupo').html('');
                $( "#grupoTel").prop("selectedIndex", 0);
                //listarGrupos(idcall);
                $('#JtablaGrupos').DataTable().row(rowEliminada).remove().draw(true);
                listarCall();
                $('#primaria').css("display","none");     
		  }else{
        if(content.message == "Sin Sesion"){
            window.location.href = "login.php";
        }else{
            $('#lblErrorAgregarGrupo').html('Error');
            $('#primaria').css("display","none");
        }       
    }
	   },
	   error: function() {
            $('#lblErrorAgregarGrupo').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
            $('#primaria').css("display","none");
	   }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function numerosLibres(){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/listarNumerosLibres.php',
	   method	: 'POST',
	   dataType: 'json',
	   beforeSend:function(){
	   },
	   success: function(content) {
		  console.log(content.id);
			 if(content.result){
                  $('#A').html(content.A);$('#B').html(content.B);
                  $('#C').html(content.C);$('#D').html(content.D);
                  $('#E').html(content.E);$('#F').html(content.F);
                  $('#G').html(content.G);$('#H').html(content.H);
                  $('#I').html(content.I);$('#J').html(content.J);
                  $('#K').html(content.K);$('#L').html(content.L);
                  $('#M').html(content.M);$('#N').html(content.N);
                  $('#O').html(content.O);$('#P').html(content.P);
                  $('#Q').html(content.Q);$('#R').html(content.R);
                  $('#S').html(content.S);$('#T').html(content.T);
                  $('#U').html(content.U);
                  $('#primaria').css("display","none");  
		  }else{
        if(content.message == "Sin Sesion"){
            window.location.href = "login.php";
        }else{
            alert('Error cargando numeros libres');
            $('#primaria').css("display","none");
        }       
    }
	   },
	   error: function() {
            $('#lblErrorAgregarGrupo').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
            $('#primaria').css("display","none");
	   }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agregarAdmin(){
    $('#primaria').css("display","block");
    $nombre = $('#nomAdmin').val();
    $cedula = $('#cedAdmin').val();
    $grupo = $( "#grupoAdmin option:selected" ).val();
    if($nombre=='' || $cedula=='' || $grupo==''){
        $('#lblErrorAdmin').html('Complete todos los campos');
        $('#primaria').css("display","none");  
    }else if (!/^([0-9])*$/.test($cedula)){
        $('#lblErrorAdmin').html('Cedula debe ser un numero !!!');
        $('#primaria').css("display","none"); 
    }else{
        var ciok = comprobarCI($cedula);
        if(ciok){
            $agregarAdmin = $('#agregarAdmin-form');
            var $form 	 = $agregarAdmin;
					
            var $data 	 = $form.serialize();
				    var ua = navigator.userAgent.toLowerCase();
                
			     $.ajax({
					   url 	: 'ajax/procesoAgregarAdmin.php',
					   data 	: $data,
					   method	: 'POST',
     				   dataType: 'json',
 				   beforeSend:function(){
					   },
					   success: function(content) {
						  console.log(content.id);
						  if(content.result){
                                if(!content.repetido){
                                    listarAdmins();
                                    $('#lblErrorAdmin').html('');
                                    $('#nomAdmin').val('');
                                    $('#cedAdmin').val('');
                                    $( "#grupoAdmin").prop("selectedIndex", 0);
                                    $('#primaria').css("display","none");
                                }else{
                                    $('#lblErrorAdmin').html('La cedula ya existe !!!');
                                    $('#primaria').css("display","none");
                                }
                                
						  }else{
						      if(content.message == "Sin Sesion"){
						              window.location.href = "login.php";
						          }else{
                                    $('#lblErrorAdmin').html('Error');
                                    $('#primaria').css("display","none");
						          }   
						  }
					   },
					   error: function() {
					    
						  $('#lblErrorAdmin').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                            $('#primaria').css("display","none");
					   }
				    });
        }else{
            $('#lblErrorAdmin').html('La cedula esta mal !!!');
            $('#primaria').css("display","none");
        }
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarAdmins(){
$('#JtablaAdmin').DataTable().destroy();
var url="Ajax/listarAdmins.php";
$("#JtablaAdmin tbody").html("");
$.getJSON(url,function(admins){
$.each(admins, function(i,admins){

var newRow =
"<tbody>"
+"<tr>"
+"<td align='center'>"+admins.id+"</td>"
+"<td align='center'>"+admins.nombre+"</td>"
+"<td align='center'>"+admins.cedula+"</td>"
+"<td align='center'>"+admins.grupo+"</td>"
+"<td align='center'>"+admins.activo+"</td>"
+"<td style='width:10%'><input type='button' class='llamar2' id='eliminarAdmin' value='Eliminar' onclick='if(confirm(`Seguro desea suspender el Admin?`)) eliminarAdmin("+"`"+admins.id+"`"+",$(this).parents(`tr`))' /></td>"
+"<td style='width:10%'><input type='button' class='llamar' id='suspenderAdmin' value='Suspender' onclick='if(confirm(`Seguro desea suspender el Admin?`)) suspenderAdmin("+"`"+admins.id+"`"+",$(this).parents(`tr`))' /></td>"
+"<td style='width:10%'><input type='button' class='llamar' id='reactivarAdmin' value='Re-Activar' onclick='if(confirm(`Seguro desea re-activar el Admin?`)) reactivarAdmin("+"`"+admins.id+"`"+",$(this).parents(`tr`))' /></td>"
+"</tr>"
+"</tbody>";
$(newRow).appendTo("#JtablaAdmin tbody");
});
$('#JtablaAdmin').DataTable({
            lengthMenu: [10],
            searching: true,
            paging: true,
            lengthChange: false,
            ordering: true,
            info: true,
            order: [0,'desc'],
            oLanguage: {
                "sUrl": "../admin/tabla/examples/server_side/scripts/spanish.json"
            },
} );
        stateSave: true
});
    $('#listarAdmins').css("display","block"); 
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function suspenderAdmin(admin,rowActual){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/procesoSuspenderAdmin.php?admin='+admin,
	   method	: 'GET',
	   dataType: 'json',
	    beforeSend:function(){
					   },
		   success: function(content) {
			  console.log(content.id);
			  if(content.result){
                    var d = $('#JtablaAdmin').DataTable().row(rowActual).data();
                    d[4] = "No";
                    $('#JtablaAdmin').DataTable().row(rowActual).data(d);
                    $('#primaria').css("display","none");     
              }else{
                if(content.message == "Sin Sesion"){
		              window.location.href = "login.php";
                }else{
                    alert('ERROR');
                    $('#primaria').css("display","none");
                }   
			  }
		      },
		      error: function() {
					    
			    alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
		      }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function reactivarAdmin(admin,rowActual){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/procesoReactivarAdmin.php?admin='+admin,
	   method	: 'GET',
	   dataType: 'json',
	    beforeSend:function(){
					   },
		   success: function(content) {
			  console.log(content.id);
			  if(content.result){
                    var d = $('#JtablaAdmin').DataTable().row(rowActual).data();
                    d[4] = "Si";
                    $('#JtablaAdmin').DataTable().row(rowActual).data(d);
                    $('#primaria').css("display","none");     
              }else{
                if(content.message == "Sin Sesion"){
		              window.location.href = "login.php";
                }else{
                    alert('ERROR');
                    $('#primaria').css("display","none");
                }   
			  }
		      },
		      error: function() {
					    
			    alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
		      }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function eliminarAdmin(admin,rowActual){
    $('#primaria').css("display","block");
    $.ajax({
	   url 	: 'ajax/procesoEliminarAdmin.php?admin='+admin,
	   method	: 'GET',
	   dataType: 'json',
	    beforeSend:function(){
					   },
		   success: function(content) {
			  console.log(content.id);
			  if(content.result){
                    $('#JtablaAdmin').DataTable().row(rowActual).remove().draw(true);
                    $('#primaria').css("display","none");   
              }else{
                if(content.message == "Sin Sesion"){
		              window.location.href = "login.php";
                }else{
                    alert('ERROR');
                    $('#primaria').css("display","none");
                }   
			  }
		      },
		      error: function() {
					    
			    alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
		      }
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function quitarNumero(){
    $('#primaria').css("display","block");
    $numero = $('#numQuitar').val();
    $observacion = $('#obsQuitar').val();
    if($numero=='' || $observacion==''){
        $('#lblErrorQuitar').html('Complete todos los campos !!!');
        $('#primaria').css("display","none"); 
        
    }else if (!/^([0-9])*$/.test($numero)){
        $('#lblErrorQuitar').html('Numero debe ser un numero !!!');
        $('#primaria').css("display","none"); 
    }else if((!/^(09)/.test($numero) || $numero.length != 9) && (!/^4/.test($numero) || $numero.length != 8) && (!/^2/.test($numero) || $numero.length != 8)){
        limpiarLabels();
        $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado!!!');
        $('#primaria').css("display","none"); 
    }else{
        $quitarForm = $('#quitar-form');
        var $form 	 = $quitarForm;
					
        var $data 	 = $form.serialize();
				var ua = navigator.userAgent.toLowerCase();
                
			    $.ajax({
					url 	: 'ajax/procesoQuitarNumero.php',
					data 	: $data,
					method	: 'POST',
     				dataType: 'json',
 				beforeSend:function(){
					},
					success: function(content) {
						console.log(content.id);
						if(content.result){
						  if(!content.repetido){
						      listarQuitarNumeros();
						      $('#numQuitar').val('');
                              $('#obsQuitar').val('');
                              $('#lblErrorQuitar').html('');
                              $('#primaria').css("display","none");
						  }else{
						      $('#numQuitar').val('');
                              $('#obsQuitar').val('');
                              $('#lblErrorQuitar').html('ERROR: El numero ya fue quitado anteriormente !!!');
                              $('#primaria').css("display","none");
						  }
						}else{
						  if(content.message == "Sin Sesion"){
						          window.location.href = "login.php";
		                  }else{
                                  alert('ERROR');
                                  $('#primaria').css("display","none");
						      }   
						}
					},
					error: function() {
					   deshabilitarBotones();
						$('#lblErrorQuitar').html('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                        $('#primaria').css("display","none");
					}
				});
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function listarQuitarNumeros(){
    $('#JtablaQuitarNumero').DataTable().destroy();
    $('#JtablaQuitarNumero').DataTable({
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
        ajax: "Ajax/listarQuitarNumero.php",
        columnDefs: [
            { "targets": [0, 2, 3, 4, 5], "searchable": false }
        ]
        
    
    } );
    $('#listarQuitarNumeros').css("display","block");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function liberarGrupo(grupo){
    $('#primaria').css("display","block");
    $grupo = grupo;
    
    $.ajax({
        url 	: 'ajax/procesoLiberarGrupo.php?grupo='+$grupo,
        method	: 'GET',
        dataType: 'json',
        beforeSend:function(){
        },
        success: function(content) {
            console.log(content.id);
            if(content.result){
			 $('#primaria').css("display","none");
             $('#'+$grupo).html(content.total);	  
            }else{
		      if(content.message == "Sin Sesion"){
                window.location.href = "login.php";
              }else{
                alert('ERROR');
                $('#primaria').css("display","none");
		      }   
            }
        },
        error: function() {
				alert('Ocurrio un error. Por favor vuelva a intentar en instantes.');
                $('#primaria').css("display","none");
        }
    }); 
}