<?php
 session_start();
if(isset($_SESSION["idadmin"])){
        $idadmin = $_SESSION["idadmin"];
        $tipoAdmin = $_SESSION["tipo_admin"];
    }else{
        header('Location: login.php');
    } 
?>
<!DOCTYPE html>
<html lang="es">

<head>
<link rel="stylesheet" href="style/main.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script> -->
<meta content="text/html" http-equiv="content-type">
<meta content="lolkittens" name="author">
<link href="css/estilos.css?v=1.1" rel="stylesheet">
<!-- <link rel="stylesheet" href="tabla/css/estilos.css"> -->
<script type="text/javascript" language="javascript" src="tabla/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="tabla/media/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="tabla/media/css/jquery.dataTables.css" media="screen" />
<title>Call Admin</title>
<script src="Js/admincall.js?v=1.24" type="text/javascript"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.js" type="text/javascript"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_es.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css">
<!-- <link rel="stylesheet" type="text/css" href="../css/jquery.datetimepicker.css"/>
<link rel="stylesheet" type="text/css" href="../jquery.datetimepicker.css"/ > -->
<!-- <script src="../build/jquery.datetimepicker.full.min.js"></script> -->
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
 <!-- onload="procesoInicial()" -->
<body >
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
<!-- <input type="button" class="leftbutton" name="ver_historico" id="btnVerHistorico" value="Ver Historico" onclick="listarHistorico(desde.value,hasta.value)"/> -->
<input type="button" class="leftbutton" name="ver_historico" id="btnVerHistorico" value="Ver Historico" onclick="listarHistoricoIframe()"/>
<input type="button" class="leftbutton" name="agregar_usuario" id="btnAgregarUsuario" value="Agregar Usuario" onclick="mostrarAgregarUsuario()"/>
<?php
    if($tipoAdmin == 'full'){
        echo "<input type='button' class='leftbutton' name='agregar_call' id='btnAgregarCall' value='Agregar Call' onclick='mostrarAgregarCall()'/>";
        echo "<input type='button' class='leftbutton' name='agregar_admin' id='btnAgregarAdmin' value='Agregar Admin' onclick='mostrarAgregarAdmin()'/>";
    }
?>
<input type='button' class='leftbutton' name='quitar_numero' id='btnQuitarNumero' value='Quitar Numero' onclick='mostrarQuitarNumero()'/>
</div>

<!-- DIV HISTORICO--><!-- INICIO DE DIV HISTORICO -->
<div id="historico" style="margin-left: 12%; padding-right: 1%;display: none;">
<h1 style=" margin-top: 0;color: white;width: 72.4%;margin-left: 4.6%;">Historico</h1>
<button id="exportExcel" class="botonAdmin" style="margin-bottom: 5px">Exportar a Excel</button>
          <div id="myGrid" class="ag-theme-balham" style="width:100%;height: 90%"></div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="bundle.js"></script></body>
<!-- <iframe src="http://localhost:8000/" height="650px" width="100%" frameborder="0"></iframe>  -->
<!--     <table width="80%" height="auto" border="0" style="margin:auto;">
    <label>Desde:</label><input type="text" id="desde" class="hvr-border-fade" placeholder="Desde"/>
    <label style="margin-left: 1%;">Hasta:</label><input type="text" id="hasta" class="hvr-border-fade" placeholder="Hasta"/>
    <input type="button" value="Buscar" onclick="listarHistorico(desde.value,hasta.value)" class="botonAdmin" style="margin-left: 1%;"/>
    <input type="button" value="Actualizar" onclick="listarHistorico('','',true)" class="botonAdmin" style="margin-left: 1%;"/>
            <table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th width="10%" align="center" >Id</th>
                <th width="108" align="center">Numero</th>
                <th width="108" align="center">Estado</th>
                <th width="124" align="center">CI Vendedor</th>
                <th width="120" align="center">Grupo</th>
                <th width="100" align="center">Fecha</th>
                <th width="108" align="center">Tipo</th>
                <th width="108" align="center">Int. Familia</th>
                <th width="108" align="center">Direccion</th>
                <th width="108" align="center">Otro Servicio</th>
                <th width="120" align="center">Observaciones</th>
                <th width="120" align="center">Ver Ofrecidos</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table> -->

<!-- MODAL DIV VER OFRECIDOS NTS -->
<div id="divVerOfrecidosNTS" class="modal">

  <!-- Modal content -->
  <div class="modal-content" style="width: 90%;">
    <div class="modal-header">
		<span class="close" id="close">X</span>
		<h2>Ofrecidos "No Tiene Servicio"</h2>
    </div>
    <div class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="JtablaNTS" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th align="center">Sanatorio</th>
                <th align="center">Convalecencia</th>
                <th align="center">Dom. Especial</th>
                <th align="center">Reintegro</th>
                <th align="center">Amparo</th>
                <th align="center">Amparo Plus</th>
                <th align="center">Assist Express</th>
                <th align="center">Assist Plus</th>
                <th align="center">Hotel</th>
                <th align="center">Grupo Familiar</th>
                <th align="center">Tarj. Vida</th>
                <th align="center">Fb 2012</th>
                <th align="center">Super Promo</th>
                <th align="center">Promo Comp.</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>	
    </div>
  </div>
</div>
<!-- MODAL DIV VER OFRECIDOS TOSNIC -->
<div id="divVerOfrecidosTOSNIC" class="modal">

  <!-- Modal content -->
  <div class="modal-content" style="width: 90%;">
    <div class="modal-header">
		<span class="close" id="close2">X</span>
		<h2>Ofrecidos "Tiene Otro Servicio No Le Interesa Cambiar"</h2>
    </div>
    <div class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="JtablaTOSNIC" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th align="center">Sanatorio</th>
                <th align="center">Convalecencia</th>
                <th align="center">Dom. Especial</th>
                <th align="center">Reintegro</th>
                <th align="center">Amparo</th>
                <th align="center">Amparo Plus</th>
                <th align="center">Assist Express</th>
                <th align="center">Assist Plus</th>
                <th align="center">Hotel</th>
                <th align="center">Grupo Familiar</th>
                <th align="center">Tarj. Vida</th>
                <th align="center">Fb 2012</th>
                <th align="center">Super Promo</th>
                <th align="center">Promo Comp.</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>	
    </div>
  </div>
</div>
<!-- MODAL DIV VER OFRECIDOS TOSIC -->
<div id="divVerOfrecidosTOSIC" class="modal">

  <!-- Modal content -->
  <div class="modal-content" style="width: 90%;">
    <div class="modal-header">
		<span class="close" id="close3">X</span>
		<h2>Ofrecidos "Tiene Otro Servicio Le Interesa Cambiar"</h2>
    </div>
    <div class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="JtablaTOSIC" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                    <th align="center">Sanatorio</th>
                    <th align="center">Convalecencia</th>
                    <th align="center">Dom. Especial</th>
                    <th align="center">Reintegro</th>
                    <th align="center">Amparo</th>
                    <th align="center">Amparo Plus</th>
                    <th align="center">Assist Express</th>
                    <th align="center">Assist Plus</th>
                    <th align="center">Hotel</th>
                    <th align="center">Grupo Familiar</th>
                    <th align="center">Tarj. Vida</th>
                    <th align="center">Fb 2012</th>
                    <th align="center">Super Promo</th>
                    <th align="center">Promo Comp.</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>	
    </div>
  </div>
</div>
<!-- MODAL DIV VER OFRECIDOS SV -->
<div id="divVerOfrecidosSV" class="modal">

  <!-- Modal content -->
  <div class="modal-content" style="width: 80%;">
    <div class="modal-header">
		<span class="close" id="close4">X</span>
		<h2>Ofrecidos "Tiene Servicio Vida"</h2>
    </div>
    <div  class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="JtablaSV" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                    <th style="text-align:center">Sanatorio</th>
                    <th style="text-align:center">Convalecencia</th>
                    <th style="text-align:center">Dom. Especial</th>
                    <th style="text-align:center">Reintegro</th>
                    <th style="text-align:center">Amparo Plus</th>
                    <th style="text-align:center">Assist Plus</th>
                    <th style="text-align:center">Hotel</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>	
    </div>
  </div>
</div>
</div> <!-- FIN DE DIV HISTORICO -->

<!-- DIV AGREGAR USUARIO--><!-- INICIO DE DIV AGREGAR USUARIO -->
<div id="agregarUsuario" style="display: none;margin-left: 30%;">
<h1 style="margin-left: -20%; margin-top: 0;color: white;">Agregar/Listar Usuarios</h1>
<form id="agregarUsu-form" method="post" name="agregarUsu-form">
    <table>
        <tr>
            <td><label>Nombre y apellido:</label></td><td><input type="text" id="nomUsu" name="nomUsu" class="hvr-border-fade" placeholder="Nombre"/></td>
        </tr>
        <tr>
            <td><label>Cedula:</label></td><td><input type="text" id="cedUsu" name="cedUsu" class="solo_numeros hvr-border-fade" placeholder="Cedula"/></td>
        </tr>
        <tr>
        <td>
    <?php
        if($tipoAdmin == 'full'){
            echo "<label>Seleccione el grupo:</label></td><td><select id='grupoUsu' name='grupoUsu' class='hvr-border-fade'>";
            echo"<option value='' style='text-align: center;'>-Seleccione un grupo-</option>";

            include('../_conexion.php');
            $q = "select * from gruposusuarios";
            $result = mysqli_query($mysqli,$q);
            $fila = 0;
            while ($reg = mysqli_fetch_array($result)) {
                echo "<option value='".$reg['id']."'>".$reg['nombre']."</option>";
                $fila++;
            }
            echo "</select></td>";
            mysqli_close($mysqli);
        }
    ?>
    </td>
    </tr>
    </table>
    <label id="lblErrorUsu" style="color: red;"></label><br />
    <input type="button" id="btnAgregarUsu" value="Agregar" onclick="agregarUsuario()" class="botonAdmin"/>
    <input type="button" id="btnListarUsu" value="Listar/Eliminar usuarios" onclick="listarUsuarios()" class="botonAdmin"/>
</form>
</div><!-- FIN DE DIV AGREGAR USUARIO -->
  <div id="listarUsuarios" style="display: none;margin-left: 12%;padding-right: 1%">
    <table width="80%" height="auto" border="0" style="margin:auto;margin-top: 2%;">
        <table id="JtablaUsu" cellpadding="0" cellspacing="0" border="0" class="display" >
            <thead>
                <tr>
                <th style="text-align:center">Id</th>
                <th style="text-align:center">Nombre</th>
                <th style="text-align:center">Cedula</th>
                <th style="text-align:center">Grupo</th>
                <th style="text-align:center">Activo</th>
                <th style="text-align:center">Eliminar</th>
                <th style="text-align:center">Suspender</th>
                <th style="text-align:center">Re-Activar</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </table>
  </div>	  



<!-- DIV AGREGAR CALL--><!-- INICIO DE DIV AGREGAR CALL -->
<div id="agregarCall" style="display: none;margin-top: 0%;margin-left: 30%;">
<h1 style="margin-left: -20%; margin-top: 0;color: white;">Agregar/Listar Calls</h1>

<div id="tblprueba">
<h2>Grupos</h2>
<table class="table-fill" id="tblLibres">
<thead>
<tr>
<th class="text-left">Grupo</th>
<th class="text-left">Disponibles</th>
<th class="text-left">Grupo</th>
<th class="text-left">Disponibles</th>
</tr>
</thead>
<tbody class="table-hover">
<tr>
<td class="text-left">A <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('A')"/></td>

<td class="text-left" id="A" style="color: blue;"></td>
<td class="text-left">B <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('B')"/></td>
<td class="text-left" id="B" style="color: blue;"></td>
</tr>
<tr>
<td class="text-left">C <input type="button" value="Limpiar" class="btnLimpiar"onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('C')"/></td>
<td class="text-left" id="C" style="color: blue;"></td>
<td class="text-left">D <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('D')"/></td>
<td class="text-left" id="D" style="color: blue;"></td>
</tr>
<tr>
<td class="text-left">E <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('E')"/></td>
<td class="text-left" id="E" style="color: blue;"></td>
<td class="text-left">F <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('F')"/></td>
<td class="text-left" id="F" style="color: blue;"></td>
</tr>
<tr>
<td class="text-left">G <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('G')"/></td>
<td class="text-left" id="G" style="color: blue;"></td>
<td class="text-left">H <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('H')"/></td>
<td class="text-left" id="H" style="color: blue;"></td>
</tr>
<tr>
<td class="text-left">I <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('I')"/></td>
<td class="text-left" id="I" style="color: blue;"></td>
<td class="text-left">J <input type="button" value="Limpiar" class="btnLimpiar" onclick="if(confirm('Seguro desea liberar el grupo?'))liberarGrupo('J')"/></td>
<td class="text-left" id="J" style="color: blue;"></td>
</tr>
</tbody>
</table>
<input type="button" value="Actualizar" onclick="numerosLibres()" style="margin-top: 10%;" class="btnActualizar"/>
</div>
<form id="agregarCall-form" method="post" name="agregarCall-form" style="margin-top:8%;">
    <label>Nombre del call:</label><input type="text" id="nomCall" name="nomCall" class="hvr-border-fade" placeholder="Nombre"/><br />
    <label id="lblErrorCall" style="color: red;"></label><br />
    <input type="button" id="btnAgregarCall" value="Agregar" onclick="agregarCall()" class="botonAdmin"/>
    <input type="button" id="btnListarCall" value="Listar Calls" onclick="listarCall()" class="botonAdmin"/>
</form>
</div><!-- FIN DE DIV AGREGAR CALL -->
<div id="listarCalls" style="display: none;margin-left: 12%;padding-right: 1%">
    <table width="80%" height="auto" border="0" style="margin:auto;margin-top: 2%;">
        <table id="JtablaCall" cellpadding="0" cellspacing="0" border="0" class="display" >
            <thead>
                <tr>
                <th style="text-align:center">Id</th>
                <th style="text-align:center">Nombre</th>
                <th style="text-align:center">Cant. Vendedores Activos</th>
                <th style="text-align:center">Grupos</th>
                <th style="text-align:center">Editar Grupos</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </table>
  </div>
  
<!-- MODAL DIV EDITAR GRUPOS -->
<div id="divEditarGrupos" class="modal">

  <!-- Modal content -->
  <div class="modal-content" style="width: 40%;">
    <div class="modal-header">
		<span class="close" id="close5">X</span>
		<h2>Editar Grupos <label id="lblCall"></label></h2>
    </div>
    <div id="modal-body-grupos" class="modal-body">
    <select id='grupoTel' name='grupoTel'>
            <option value="0">-Seleccione Grupo-</option>
            <option value="A" style="color:blue;">Grupo "A"</option>
            <option value="B" style="color:blue;">Grupo "B"</option>
            <option value="C" style="color:blue;">Grupo "C"</option>
            <option value="D" style="color:blue;">Grupo "D"</option>
            <option value="E" style="color:blue;">Grupo "E"</option>
            <option value="F" style="color:blue;">Grupo "F"</option>
            <option value="G" style="color:blue;">Grupo "G"</option>
            <option value="H" style="color:blue;">Grupo "H"</option>
            <option value="I" style="color:blue;">Grupo "I"</option>
            <option value="J" style="color:blue;">Grupo "J"</option>
        </select> 	
    </div>
    <div style="padding: 2%;">
    <label id="lblErrorAgregarGrupo" style="color: red;"></label>
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="JtablaGrupos" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th style="text-align:center">Call</th>
                <th style="text-align:center">Grupos</th>
                <th style="text-align:center">Quitar Grupo</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table></div>
  </div>
</div>


<!-- DIV AGREGAR ADMIN--><!-- INICIO DE DIV AGREGAR ADMIN -->
<div id="agregarAdmin" style="display: none;margin-top: 0%;margin-left: 30%;">
<h1 style="margin-left: -20%; margin-top: 0;color: white;">Agregar/Listar Admin</h1>
<form id="agregarAdmin-form" method="post" name="agregarAdmin-form">
<table>
    <tr>
       <td><label>Nombre y apellido:</label></td><td><input type="text" id="nomAdmin" name="nomAdmin" class="hvr-border-fade" placeholder="Nombre"/></td>
    </tr>
    <tr>
        <td><label>Cedula:</label></td><td><input type="text" id="cedAdmin" name="cedAdmin" class="solo_numeros hvr-border-fade" placeholder="Cedula"/></td>
    </tr>
    <tr>
    <td>
    <?php
        echo "<label>Seleccione el grupo:</label></td><td><select id='grupoAdmin' name='grupoAdmin' class = 'hvr-border-fade'>";
        echo"<option value='' style='text-align: center;'>-Seleccione un grupo-</option>";

        include('../_conexion.php');
        $q = "select * from gruposusuarios";
        $result = mysqli_query($mysqli,$q);
        $fila = 0;
        while ($reg = mysqli_fetch_array($result)) {
            echo "<option value='".$reg['id']."'>".$reg['nombre']."</option>";
            $fila++;
        }
        echo "</select><br />";
        mysqli_close($mysqli);
    ?>
    </td>
    </tr>
    </table>
    <label id="lblErrorAdmin" style="color: red;"></label><br />
    <input type="button" id="btnAgregarAdmin" value="Agregar Admin" onclick="agregarAdmin()" class="botonAdmin"/>
    <input type="button" id="btnListarAdmin" value="Listar/Eliminar admins" onclick="listarAdmins()" class="botonAdmin"/>

</form>
</div><!-- FIN DE DIV AGREGAR ADMIN -->
<div id="listarAdmins" style="display: none;margin-left: 12%;padding-right: 1%">
    <table width="80%" height="auto" border="0" style="margin:auto;margin-top: 2%;">
        <table id="JtablaAdmin" cellpadding="0" cellspacing="0" border="0" class="display">
            <thead>
                <tr>
                <th style="text-align:center">Id</th>
                <th style="text-align:center">Nombre</th>
                <th style="text-align:center">Cedula</th>
                <th style="text-align:center">Grupo</th>
                <th style="text-align:center">Activo</th>
                <th style="text-align:center">Eliminar</th>
                <th style="text-align:center">Suspender</th>
                <th style="text-align:center">Re-Activar</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </table>
  </div>
  
  <!-- DIV QUITAR NUMERO--><!-- INICIO DE DIV QUITAR NUMERO -->
<div id="quitarNumero" style="display: none;margin-top: 0%;margin-left: 30%;">
<h1 style="margin-left: -20%; margin-top: 0;color: white;">Quitar Numero</h1>
<form id="quitar-form" method="post" name="quitar-form">
<table style="padding: 2%;">
    <tr>
        <td><label>Numero:</label></td><td><input type="text" id="numQuitar" name="numQuitar" class="solo_numeros hvr-border-fade" placeholder="Numero" maxlength="9"/></td>
    </tr>
    <tr>
        <td><label>Observacion:</label></td><td><textarea id="obsQuitar" name="obsQuitar" class="hvr-border-fade" rows="4" cols="50" style="width: 240px; height: 90px;" placeholder="Observacion"></textarea></td>
    </tr>
    </table>
    <label id="lblErrorQuitar" style="color: red;"></label><br />
    <input type="button" id="btnQuitarNumero" value="Quitar Numero" onclick="quitarNumero()" class="botonAdmin"/>
    <input type="button" id="btnListarNumero" value="Listar Numeros Quitados" onclick="listarQuitarNumeros()" class="botonAdmin"/>
</form>
</div><!-- FIN DE DIV QUITAR NUMERO -->
<div id="listarQuitarNumeros" style="display: none;margin-left: 12%;padding-right: 1%">
    <table width="80%" height="auto" border="0" style="margin:auto;margin-top: 2%;">
        <table id="JtablaQuitarNumero" cellpadding="0" cellspacing="0" border="0" class="display" >
            <thead>
                <tr>
                <th style="text-align:center">Id</th>
                <th style="text-align:center">Numero</th>
                <th style="text-align:center">Observacion</th>
                <th style="text-align:center">Fecha</th>
                <th style="text-align:center">Grupo</th>
                <th style="text-align:center">Usuario</th>
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
var modal = document.getElementById('divVerOfrecidosNTS');

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

/////////////////MODAL 2\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal2 = document.getElementById('divVerOfrecidosTOSNIC');

// Get the <span> element that closes the modal
var span2 = document.getElementById("close2");

// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span2.onclick = function() {
    modal2.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal2) {
        modal2.style.display = "none";
    }
}

/////////////////MODAL 3\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal3 = document.getElementById('divVerOfrecidosTOSIC');

// Get the <span> element that closes the modal
var span3 = document.getElementById("close3");

// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span3.onclick = function() {
    modal3.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal3) {
        modal3.style.display = "none";
    }
}

/////////////////MODAL 4\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal4 = document.getElementById('divVerOfrecidosSV');

// Get the <span> element that closes the modal
var span4 = document.getElementById("close4");

// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span4.onclick = function() {
    modal4.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal4) {
        modal4.style.display = "none";
    }
}
/////////////////MODAL 5\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal5 = document.getElementById('divEditarGrupos');

// Get the <span> element that closes the modal
var span5 = document.getElementById("close5");

// When the user clicks the button, open the modal 

// When the user clicks on <span> (x), close the modal
span5.onclick = function() {
    modal5.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal5) {
        modal5.style.display = "none";
    }
}
</script>

</body>
</html>