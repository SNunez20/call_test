<?php
session_start();
if(isset($_SESSION["idauditoria"])){
        $idauditoria = $_SESSION["idauditoria"];
    }else{
        header('Location: login.php');
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<meta content="text/html" http-equiv="content-type">
<meta content="lolkittens" name="author">
<link href="css/estilos.css?v=1.1" rel="stylesheet">
<link rel="stylesheet" href="tabla/css/estilos.css">
<script type="text/javascript" language="javascript" src="tabla/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="tabla/media/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="tabla/media/css/jquery.dataTables.css" media="screen" />
<title>Call Auditoria</title>
<script src="Js/auditoriacall.js?v=1.1" type="text/javascript"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.js" type="text/javascript"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_es.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../css/jquery.datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="../jquery.datetimepicker.css"/ >
<script src="../build/jquery.datetimepicker.full.min.js"></script>
<link href="../img/icon.png" type="image/png" rel="icon">
<style>

@media screen and (max-width: 1400px) {
}
td{
 text-align:center;
 vertical-align:middle;
}
th{
 text-align:center;
 vertical-align:middle;
}
</style>

</head>

<body onload="procesoInicial()">
<div id="primaria">
<img src="../img/loading.gif" id="loading"/>
</div>

<div class="bienvenida">
	<span style="left:10px; position:absolute; color:#999">Bienvenid@ :</span><label style="left:100px; position:absolute; color:#fff;" id="lblBienv"></label>
	<span style="left:340px; position:absolute; color:#999">Grupo :</span><label style="left:390px; position:absolute; color:#fff;" id="lblGrupo"></label>
	<input type="button" class="botonsalir" id="cerrar" name="cerrar" onclick="window.location ='salir.php'" value="Salir" />
	<br />
</div>
<div class="contenidolateral" id="contenidolateral">
<input type="button" class="leftbutton" name="ver_agendados" id="btnVerAgendados" value="Ver Agendados" onclick="listarAgendados(desde.value,hasta.value)"/>
<input type="button" class="leftbutton" name="ver_evaluados" id="btnVerEvaluados" value="Ver Evaluados" onclick="listarEvaluados(desde2.value,hasta2.value)"/>
<input type="button" class="leftbutton" name="exportar_evaluados" id="btnVerExportacion" value="Exportar Evaluados" onclick="mostrarExportar()"/>
</div>

<!-- DIV AGENDADOS--><!-- INICIO DE DIV AGENDADOS -->
<div id="Agendados" style="margin-left: 12%; padding-right: 1%;display: none;">
<h1 style=" margin-top: 0;color: white;width: 72.4%;margin-left: 4.6%;">Agendados</h1>
    <table width="80%" height="auto" border="0" style="margin:auto;">
    <label>Desde:</label><input type="text" id="desde" class="hvr-border-fade" placeholder="Desde"/>
    <label style="margin-left: 1%;">Hasta:</label><input type="text" id="hasta" class="hvr-border-fade" placeholder="Hasta"/>
    <input type="button" value="Buscar" onclick="listarAgendados(desde.value,hasta.value)" class="botonAdmin" style="margin-left: 1%;"/>
    <input type="button" value="Actualizar" onclick="listarAgendados('','',true)" class="botonAdmin" style="margin-left: 1%;"/>
            <table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th width="10%" align="center" >Id</th>
                <th width="5%" align="center">Numero</th>
                <th width="5%" align="center">Ci Vendedor</th>
                <th width="10%" align="center">Call</th>
                <th width="10%" align="center" title="Fecha en la que hay que llamar.">Fecha Agendado</th>
                <th width="1%" align="center">No Contesta</th>
                <th width="100px" align="center" title="Fecha de agendado el numero.">Fecha</th>
                <th width="5%" align="center">Evaluado</th>
                <th width="5%" align="center">Evaluar</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>
</div>
<!-- MODAL DIV Evaluar -->
<form id="evaluar-form" method="post" name="evaluar-form">
<div id="divEvaluar" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close">X</span>
		<h2>Evaluacion</h2>
    </div>
    <div class="modal-body">
		<table width="80%" height="250px" border="0" style="margin:auto">
			<tr>
					<div id="divAgendadoEval" >
                    <td><input id="idAgeEval" name="idAgeEval" style="display: none;"/></td>
                    <td><input id="nomCall" name="nomCall" style="display: none;"/></td>
                    <td><input id="fechaAgendada" name="fechaAgendada" style="display: none;"/></td>
                    <td><input id="fechaDeAgendado" name="fechaDeAgendado" style="display: none;"/></td>
					<td style="text-align:center; padding-bottom:10px; vertical-align:text-top"><p><img src="img/numero.png"><br>Numero</p><input maxlength="9" name="numAge" type="text" class="hvr-border-fade solo_numeros" placeholder="Numero" id="numAge" style="width:250px;background-color: silver;" readonly="true"/></td>
                    <td style="text-align:center; padding-bottom:10px; vertical-align:text-top"><p><img src="img/nombre.png"><br>Usuario</p><input maxlength="100" name="usuEval" type="text" class="hvr-border-fade" placeholder="Usuario" id="usuEval" style="width:250px;background-color: silver;" readonly="true"/></td>
                    <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                        <p><img src="img/numero.png"><br>Puntaje</p>
                        <select id='puntaje' name='puntaje' class='hvr-border-fade'>
                            <option value="">-Puntaje-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </td>
					<td style="text-align:center; padding-bottom:10px; vertical-align:text-top"><p><img src="img/comentarios.png"><br>Comentario</p><textarea  rows="4" cols="50" id="comEval" class="hvr-border-fade" name="comEval" placeholder="Comentario"></textarea></td>
					
					
						
					<input type="button" class="mainbuttonsave" id="btnEvaluar" value="Enviar Evaluacion" onclick="evaluar()" style="position:absolute; bottom:12px"/>

					<label id="lblErrorEvaluar" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; margin-left:210px;"></label>

					</div>
			</tr>
		</table>
		<hr style="border:solid 1px; color:#999; position:relative; bottom:30px;"/>
    </div>

  </div>
</div>
</form>

<!-- DIV EXPORTACION--><!-- INICIO DE DIV EXPORTACION -->
<div id="Exportacion" style="margin-left: 12%; padding-right: 1%;display: none;">
<h1 style=" margin-top: 0;color: white;width: 72.4%;margin-left: 4.6%;">Exportar Evaluados</h1>
    <form id="exportar-form" method="post" name="exportar-form" action="ajax/exportarEvaluados.php">
    <table width="80%" height="auto" border="0" style="margin:auto;">
    <label>Desde:</label><input type="text" id="desde3" name="desde3" class="hvr-border-fade" placeholder="Desde"/>
    <label style="margin-left: 1%;">Hasta:</label><input type="text" id="hasta3" name="hasta3" class="hvr-border-fade" placeholder="Hasta"/>
    <input type="submit" value="Exportar" class="botonAdmin" style="margin-left: 1%;"/>
    <label id="lblErrorExportar" style="color:red;margin-left:50px;"></label>
    </table>
    </form>
</div>

<!-- DIV EVALUADOS--><!-- INICIO DE DIV EVALUADOS -->
<div id="Evaluados" style="margin-left: 12%; padding-right: 1%;display: none;">
<h1 style=" margin-top: 0;color: white;width: 72.4%;margin-left: 4.6%;">Evaluaciones</h1>
    <table width="80%" height="auto" border="0" style="margin:auto;">
    <label>Desde:</label><input type="text" id="desde2" class="hvr-border-fade" placeholder="Desde"/>
    <label style="margin-left: 1%;">Hasta:</label><input type="text" id="hasta2" class="hvr-border-fade" placeholder="Hasta"/>
    <input type="button" value="Buscar" onclick="listarEvaluados(desde2.value,hasta2.value)" class="botonAdmin" style="margin-left: 1%;"/>
    <input type="button" value="Actualizar" onclick="listarEvaluados('','',true)" class="botonAdmin" style="margin-left: 1%;"/>
            <table id="Jtabla2" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th width="5%" align="center" >Id</th>
                <th width="5%" align="center" >Id Agendado</th>
                <th width="5%" align="center">Numero</th>
                <th width="5%" align="center">Ci Vendedor</th>
                <th width="10%" align="center">Call</th>
                <th width="10%" align="center">Evaluacion</th>
                <th width="130px" align="center">Comentario</th>
                <th width="80px" align="center" title="Fecha en la que hay que llamar.">Fecha Agendada</th>
                <th width="5%" align="center" title="Fecha en la que se agendo el numero.">Fecha De Agendado</th>
                <th width="5%" align="center" title="Fecha de realizada la evaluacion.">Fecha Evaluacion</th>
                <th width="5%" align="center">Auditora</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>
</div>
<script>
/////////////////MODAL 1\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal = document.getElementById('divEvaluar');

// Get the <span> element that closes the modal
var span = document.getElementById("close");

// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<script>
$.datetimepicker.setLocale('es');
$('#desde').keypress(function(event) {event.preventDefault();});
$('#desde').datetimepicker({
  maxDate: 'today',
  pickTime: false,
   format:'Y-m-d',
   //minDate: getFormattedDate(new Date())
});

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString().slice(2);
    return day + '-' + month + '-' + year;
}

</script>
<script>

$.datetimepicker.setLocale('es');
$('#hasta').keypress(function(event) {event.preventDefault();});
$('#hasta').datetimepicker({
  maxDate: 'today',
  pickTime: false,
   format:'Y-m-d',
   //minDate: getFormattedDate(new Date())
});

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString().slice(2);
    return day + '-' + month + '-' + year;
}

</script>

<script>
$.datetimepicker.setLocale('es');
$('#desde2').keypress(function(event) {event.preventDefault();});
$('#desde2').datetimepicker({
  maxDate: 'today',
  pickTime: false,
   format:'Y-m-d',
   //minDate: getFormattedDate(new Date())
});

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString().slice(2);
    return day + '-' + month + '-' + year;
}

</script>
<script>

$.datetimepicker.setLocale('es');
$('#hasta2').keypress(function(event) {event.preventDefault();});
$('#hasta2').datetimepicker({
  maxDate: 'today',
  pickTime: false,
   format:'Y-m-d',
   //minDate: getFormattedDate(new Date())
});

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString().slice(2);
    return day + '-' + month + '-' + year;
}

</script>

<script>
$.datetimepicker.setLocale('es');
$('#desde3').keypress(function(event) {event.preventDefault();});
$('#desde3').datetimepicker({
  maxDate: 'today',
  pickTime: false,
   format:'Y-m-d',
   //minDate: getFormattedDate(new Date())
});

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString().slice(2);
    return day + '-' + month + '-' + year;
}

</script>
<script>

$.datetimepicker.setLocale('es');
$('#hasta3').keypress(function(event) {event.preventDefault();});
$('#hasta3').datetimepicker({
  maxDate: 'today',
  pickTime: false,
   format:'Y-m-d',
   //minDate: getFormattedDate(new Date())
});

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString().slice(2);
    return day + '-' + month + '-' + year;
}

</script>
</body>
</html>