	<?php
     session_start();
     if(isset($_SESSION["idusuario"])){
        $usuario = $_SESSION["idusuario"];
      }else{
         header('Location: login.php');
      }
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<meta content="text/html" http-equiv="content-type">
<meta content="lolkittens" name="author">
<link href="css/estilos.css?v=1.1" rel="stylesheet">
<link rel="stylesheet" href="tabla/css/estilos.css">
<script type="text/javascript" language="javascript" src="tabla/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="tabla/media/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="tabla/media/css/jquery.dataTables.css" media="screen" />
<meta charset="UTF-8">
<title>Call</title>
<script src="Js/call.js?v=1.18" type="text/javascript"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.js" type="text/javascript"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_es.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet"> <link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
<link href="img/icon.png" type="image/png" rel="icon">
<link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css"/ >
<script src="build/jquery.datetimepicker.full.min.js"></script>

</head>

<body onload="numeroInicial()" class="bodybackground">
<div id="primaria">
<img src="img/loading.gif" id="loading"/>
</div>

<div class="bienvenida">
	<span style="left:10px; position:absolute; color:#fff">Bienvenid@ : </span> <label style="left:110px; position:absolute; color:#fff;" id="lblBienv"></label>
	<span style="left:340px; position:absolute; color:#fff">Grupo : </span> <label style="left:400px; position:absolute; color:#fff;" id="lblGrupo"></label>
	<button type="button" class="botonsalir" id="cerrar" name="cerrar" onclick="window.location ='salir.php'" value="Salir" > <img src="img/opendoor.png" width="18px"> Salir</button>
	<br />
</div>
<div class="contenidolateral">
<button type="button" class="leftbutton" name="agendados" id="btnVerAgendados" value="Ver Agendados"><img src="img/agendados.png" width="24px"> Ver Agendados<span id="lblBadge1" class="badge"></span></button><br>
<button type="button" class="leftbutton" name="referidos" id="btnVerReferidos" value="Ver Referidos"><img src="img/referidos.png" width="24px"> Ver Referidos</button>
<button type="button" class="leftbutton" name="vendidos" id="btnVerVendidos" value="Ver Vendidos"><img src="img/vendidos.png" width="24px"> Ver Vendidos</button>
</div>
<!----///////////////// VENTANA HOVER ///////////////---->
<ul>
<li id="modalhover" style="width:50px; position:absolute; margin-left:49%; margin-top:250px; display:block; text-align:center;">
<div class="vineta" id="vineta" name="vineta"></div>
<div class="modalhover"> 		
<table style="position: absolute;width: 210%;" id="tblInfo">
<caption style="font-weight: bold; font-size: 18px; color:#81A313;"><img src="img/historial.png" width="22px"> Historial del numero:
<hr class="hrmodalhover"/>
</caption>
  <thead>
  <tr>
    <th style="font-size: 15px; text-decoration: bold;">Estado</th>
    <th style="font-size: 15px; text-decoration: bold;">Fecha y hora</th>
    <th style="font-size: 15px; text-decoration: bold;">Int. Familia</th>
    <th style="font-size: 15px; text-decoration: bold;">Direccion</th>
    <th style="font-size: 15px; text-decoration: bold;">Otro Servicio</th>
    <th style="font-size: 15px; text-decoration: bold;">Observacion</th>
  </tr>
  </thead>
  <tbody>
    
  </tbody>
</table>
</div>
	</ul>
	</li>
<form id="index-form" method="post" name="index-form">
<div style="width:100%; text-align:center; margin-top:20px;">
<img src="img/call.png" width="100px"><br><label id="lblNumero" class="callnumero"></label>
</div>
<br />
<label id="lblError" style="margin-left: 40%; margin-top:20px; color: red;font-size: 20px;"></label><br>
<div class="localidad">
<label id="lblLocalidad" style="color: green;font-size: 18px;">Canelones - Santa Lucia</label>
</div>
<br /><br />

<div class="contenedorbtn">
<button type="button" class="mainbuttonvendido" id="btnAtendio" value="Atendió"> <img src="img/telefono.png" width="16px"> Atendió</button>
<button type="button" class="mainbutton" id="btnNoContesta" value="No Contesta" onclick="noContesta()"> <img src="img/telefononocontesta.png" width="16px"> No Contesta</button>
<button type="button" class="mainbuttonreferido" id="btnReferido" value="Referido" > <img src="img/telefonoreferido.png" width="16px"> Referido</button>
<button type="button" class="mainbuttonnollamar" id="btnNoLLamarMas" value="No Llamar Mas"> <img src="img/telefononoatendio.png" width="16px"> No Llamar Más </button>

</div>
<!-- MODAL DIV NO LLAMAR -->
<div id="divNoLlamarMas" class="modal" >

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close3">X</span>
		<h2><img src="img/telefononoatendio.png" width="22px"> No Llamar Mas</h2>
    </div>
    <div class="modal-body">
		<table width="80%" height="300px" border="0" style="margin:auto">
			<tr>
					<div id="divEliminar" >

					<td style="text-align:center; padding-bottom:10px; vertical-align:text-top"> <img src="img/comentarios.png"><br>Observación<br><textarea rows="4" cols="50" id="observacion" class="hvr-border-fade" style="width:80%" name="observacion" placeholder="Observacion"></textarea></td>

					<input type="button" class="mainbuttonnollamar" id="btnListaNegra" value="Quitar Permanentemente" onclick="quitarPermanente()" style="position:absolute; bottom:12px"/>

					<label id="lblErrorListaNegra" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; margin-left:240px; font-size:15px;"></label>

					</div>
			</tr>
		</table>
		
    </div>
  </div>
</div>

<!-- MODAL DIV ALERT BIENVENIDA -->
<div id="divAlert" class="modalalert" >
  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-alertheader">
		<span class="close" id="close1">X</span>
		<h2 style="font-size:42px">Bienvenid@!</h2>
    </div>
    <div class="modal-body">
		<table width="80%" height="150px" border="0" style="margin:auto">
			<tr>
					<div id="divAlert" >
					</br>
					</br>
					</br>
					</br>
					¡Usted tiene numeros agendados para hoy!
					</div>
			</tr>
		</table>
    </div>
  </div>
</div>

<!-- MODAL DIV VER AGENDADOS -->
<div id="divVerAgendados" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close4">X</span>
		<h2> <img src="img/agendados.png" width="36px"> Agendados </h2>
		<div class="referencias">
		<img src="img/Rojo.png"/> Agendados Vencidos <img src="img/Verde.png"/> Agendados para el día <img src="img/Blanco.png"/> Agendados a futuro</div>
    </div>
    <div class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th width="58" align="left">Numero</th>
                <th width="108" align="left">Nombre</th>
                <th width="119" align="left">Fecha de Agendado</th>
                <th width="124" align="left">Fecha</th>
                <th width="241" align="left">Comentario</th>
                <th width="53" align="left">Llamar</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>	
    </div>
  </div>
</div>

<!-- MODAL DIV VER REFERIDOS -->
<div id="divVerReferidos" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close7">X</span>
		<h2> <img src="img/referidos.png" width="36px"> Referidos</h2>
    </div>
    <div class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla2" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th width="58" align="left">Numero</th>
                <th width="108" align="left">Nombre</th>
                <th width="124" align="left">Fecha</th>
                <th width="241" align="left">Observacion</th>
                <th width="53" align="left">Llamar</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </table>	
    </div>
  </div>
</div>
<!-- MODAL DIV VER VENDIDOS -->
<div id="divVerVendidos" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close8">X</span>
		<h2> <img src="img/vendidos.png" width="36px"> Vendidos</h2>
    </div>
    <div class="modal-body">
        <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla3" cellpadding="0" cellspacing="0" border="0" class="display" >
                <thead>
                <tr>
                <th width="58" align="left">Numero</th>
                <th width="58" align="left">Int. Familia</th>
                <th width="154" align="left">Direccion</th>
                <th width="63" align="left">Otro Servicio</th>
                <th width="251" align="left">Observacion</th>
                <th width="58" align="left">Fecha</th>
                </tr>
                </thead>
            </table>
        </table>	
    </div>
  </div>
</div>

<!-- MODAL DIV ATENDIO -->
<div id="divAtendio" class="modal" >

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close5">X</span>
		<div class="atendiotitulo"><h2 id="tit"><img src="img/telefono.png" width="26px"> Atendió</h2></div> 
		<ul id="ulBotones" style="margin-right:20px;">
		<li style="list-style:none;"><a id="btnTosic" class="botoncartilla" onclick="mostrarTosic()">Tiene otro servicio y le interesa cambiar.</a></li>
		<li style="list-style:none;"><a id="btnTosnic" class="botoncartilla" onclick="mostrarTosnic()">Tiene otro servicio y no le interesa cambiar.</a></li>
		<li style="list-style:none;"><a id="btnSv" class="botoncartilla" onclick="mostrarSv()">Socio Vida.</a></li> 
		<li style="list-style:none;"><a id="btnNts" class="botoncartillaseleccionado" onclick="mostrarNts()">No tiene servicio.</a></li>
		</ul>
    </div>
    <div class="modal-body">
	<div id="tabs">
	<br/>
		<div id="nts" style="display:block; font-size:10px;">
				<table style="width:22%; border:0; float:left; position:absolute; top:15px; vertical-align:top;">
					<tbody>
						<tr>
							<td valign="top">
                                <input type="checkbox" style="margin:0; padding:0; display:none" id="chknts" name="chknts" checked="true">
								<label for="ntsa"><input type="checkbox" style="margin:0; padding:0" id="ntsa" name="ntsa" ><span class="checkboxst">Sanatorio.</span></label><br/>
								<label for="ntsb"><input type="checkbox" style="margin:0; padding:0" id="ntsb" name="ntsb" ><span class="checkboxst"> Convalecencia.</span></label><br/>
								<label for="ntsc"><input type="checkbox" style="margin:0; padding:0" id="ntsc" name="ntsc" ><span class="checkboxst"> Domicilio Especial.</span></label><br/>
								<label for="ntsd"><input type="checkbox" style="margin:0; padding:0" id="ntsd" name="ntsd" ><span class="checkboxst"> Reintegro.</span></label><br/>
								<label for="ntse"><input type="checkbox" style="margin:0; padding:0" id="ntse" name="ntse" ><span class="checkboxst"> Amparo.</span></label><br/>
								<label for="ntsf"><input type="checkbox" style="margin:0; padding:0" id="ntsf" name="ntsf" ><span class="checkboxst"> Amparo Plus.</span></label><br/>
								<label for="ntsg"><input type="checkbox" style="margin:0; padding:0" id="ntsg" name="ntsg" ><span class="checkboxst"> Assist Express.</span></label><br/>
							</td>
							<td valign="top">
								<label for="ntsh"><input type="checkbox" style="margin:0; padding:0" id="ntsh" name="ntsh" ><span class="checkboxst"> Assist Plus.</span></label><br/>
								<label for="ntsi"><input type="checkbox" style="margin:0; padding:0" id="ntsi" name="ntsi" ><span class="checkboxst"> Hotel.</span></label><br/>
								<label for="ntsj"><input type="checkbox" style="margin:0; padding:0" id="ntsj" name="ntsj" ><span class="checkboxst"> Grupo Familiar.</span></label><br/>
								<label for="ntsk"><input type="checkbox" style="margin:0; padding:0" id="ntsk" name="ntsk" ><span class="checkboxst"> Tarjeta Vida.</span></label><br/>
								<label for="ntsl"><input type="checkbox" style="margin:0; padding:0" id="ntsl" name="ntsl" ><span class="checkboxst"> FB2012.</span></label><br/>
								<label for="ntsm"><input type="checkbox" style="margin:0; padding:0" id="ntsm" name="ntsm" ><span class="checkboxst"> Super Promo.</span></label><br/>
								<label for="ntsn"><input type="checkbox" style="margin:0; padding:0" id="ntsn" name="ntsn" ><span class="checkboxst"> Promo Competencia.</span></label><br/>
							</td>
						</tr>
					</tbody>
				</table>
		</div>
		<div id="sv" style="display:none; font-size:10px;">
				<table style="width:20%; border:0; float:left; position:absolute; top:15px; vertical-align:top;">
					<tbody>
						<tr>
							<td valign="top">
                                <input type="checkbox" style="margin:0; padding:0; display:none" id="chksv" name="chksv">
								<label for="sva"><input type="checkbox" style="margin:0; padding:0" id="sva" name="sva" ><span class="checkboxst">Sanatorio.</span></label><br/>
								<label for="svb"><input type="checkbox" style="margin:0; padding:0" id="svb" name="svb" ><span class="checkboxst"> Convalecencia.</span></label><br/>
								<label for="svc"><input type="checkbox" style="margin:0; padding:0" id="svc" name="svc" ><span class="checkboxst"> Domicilio Especial.</span></label><br/>
								<label for="svd"><input type="checkbox" style="margin:0; padding:0" id="svd" name="svd" ><span class="checkboxst"> Reintegro.</span></label><br/>
								<label for="sve"><input type="checkbox" style="margin:0; padding:0" id="sve" name="sve" ><span class="checkboxst"> Amparo Plus.</span></label><br/>
								<label for="svf"><input type="checkbox" style="margin:0; padding:0" id="svf" name="svf" ><span class="checkboxst"> Assist Plus.</span></label><br/>
								<label for="svg"><input type="checkbox" style="margin:0; padding:0" id="svg" name="svg" ><span class="checkboxst"> Hotel.</span></label><br/></td>
						</tr>
					</tbody>
				</table>
		</div>
  <div id="tosnic" style="display:none; font-size:10px;">
			<table style="width:20%; border:0; float:left; position:absolute; top:15px; vertical-align:top;">
				<tbody>
					<tr>
						<td valign="top">
                            <input type="checkbox" style="margin:0; padding:0; display:none" id="chktosnic" name="chktosnic">
							<label for="tosnica"><input type="checkbox" style="margin:0; padding:0" id="tosnica" name="tosnica" ><span class="checkboxst">Sanatorio.</span></label><br/>
							<label for="tosnicb"><input type="checkbox" style="margin:0; padding:0" id="tosnicb" name="tosnicb" ><span class="checkboxst"> Convalecencia.</span></label><br/>
							<label for="tosnicc"><input type="checkbox" style="margin:0; padding:0" id="tosnicc" name="tosnicc" ><span class="checkboxst"> Domicilio Especial.</span></label><br/>
							<label for="tosnicd"><input type="checkbox" style="margin:0; padding:0" id="tosnicd" name="tosnicd" ><span class="checkboxst"> Reintegro.</span></label><br/>
							<label for="tosnice"><input type="checkbox" style="margin:0; padding:0" id="tosnice" name="tosnice" ><span class="checkboxst"> Amparo.</span></label><br/>
							<label for="tosnicf"><input type="checkbox" style="margin:0; padding:0" id="tosnicf" name="tosnicf" ><span class="checkboxst"> Amparo Plus.</span></label><br/>
							<label for="tosnicg"><input type="checkbox" style="margin:0; padding:0" id="tosnicg" name="tosnicg" ><span class="checkboxst"> Assist Express.</span></label><br/>
						</td>
						<td valign="top">
							<label for="tosnich"><input type="checkbox" style="margin:0; padding:0" id="tosnich" name="tosnich" ><span class="checkboxst"> Assist Plus.</span></label><br/>
							<label for="tosnici"><input type="checkbox" style="margin:0; padding:0" id="tosnici" name="tosnici" ><span class="checkboxst"> Hotel.</span></label><br/>
							<label for="tosnicj"><input type="checkbox" style="margin:0; padding:0" id="tosnicj" name="tosnicj" ><span class="checkboxst"> Grupo Familiar.</span></label><br/>
							<label for="tosnick"><input type="checkbox" style="margin:0; padding:0" id="tosnick" name="tosnick" ><span class="checkboxst"> Tarjeta Vida.</span></label><br/>
							<label for="tosnicl"><input type="checkbox" style="margin:0; padding:0" id="tosnicl" name="tosnicl" ><span class="checkboxst"> FB2012.</span></label><br/>
							<label for="tosnicm"><input type="checkbox" style="margin:0; padding:0" id="tosnicm" name="tosnicm" ><span class="checkboxst"> Super Promo.</span></label><br/>
							<label for="tosnicn"><input type="checkbox" style="margin:0; padding:0" id="tosnicn" name="tosnicn" ><span class="checkboxst"> Promo Competencia.</span></label><br/>
						</td>
					</tr>
				</tbody>
			</table>
  </div>
  <div id="tosic" style="display:none; font-size:10px;">
			<table style="width:20%; border:0; float:left; position:absolute; top:15px;">
				<tbody>
					<tr>
						<td valign="top">
                            <input type="checkbox" style="margin:0; padding:0; display:none" id="chktosic" name="chktosic">
                            <label for="tosica"><input type="checkbox" style="margin:0; padding:0" id="tosica" name="tosica" ><span class="checkboxst">Sanatorio.</span></label><br/>
							<label for="tosicb"><input type="checkbox" style="margin:0; padding:0" id="tosicb" name="tosicb" ><span class="checkboxst"> Convalecencia.</span></label><br/>
							<label for="tosicc"><input type="checkbox" style="margin:0; padding:0" id="tosicc" name="tosicc" ><span class="checkboxst"> Domicilio Especial.</span></label><br/>
							<label for="tosicd"><input type="checkbox" style="margin:0; padding:0" id="tosicd" name="tosicd" ><span class="checkboxst"> Reintegro.</span></label><br/>
							<label for="tosice"><input type="checkbox" style="margin:0; padding:0" id="tosice" name="tosice" ><span class="checkboxst"> Amparo.</span></label><br/>
							<label for="tosicf"><input type="checkbox" style="margin:0; padding:0" id="tosicf" name="tosicf" ><span class="checkboxst"> Amparo Plus.</span></label><br/>
							<label for="tosicg"><input type="checkbox" style="margin:0; padding:0" id="tosicg" name="tosicg" ><span class="checkboxst"> Assist Express.</span></label><br/>
						</td>
						<td valign="top">
                            <label for="tosich"><input type="checkbox" style="margin:0; padding:0" id="tosich" name="tosich" ><span class="checkboxst"> Assist Plus.</span></label><br/>
							<label for="tosici"><input type="checkbox" style="margin:0; padding:0" id="tosici" name="tosici" ><span class="checkboxst"> Hotel.</span></label><br/>
							<label for="tosicj"><input type="checkbox" style="margin:0; padding:0" id="tosicj" name="tosicj" ><span class="checkboxst"> Grupo Familiar.</span></label><br/>
							<label for="tosick"><input type="checkbox" style="margin:0; padding:0" id="tosick" name="tosick" ><span class="checkboxst"> Tarjeta Vida.</span></label><br/>
							<label for="tosicl"><input type="checkbox" style="margin:0; padding:0" id="tosicl" name="tosicl" ><span class="checkboxst"> FB2012.</span></label><br/>
							<label for="tosicm"><input type="checkbox" style="margin:0; padding:0" id="tosicm" name="tosicm" ><span class="checkboxst"> Super Promo.</span></label><br/>
							<label for="tosicn"><input type="checkbox" style="margin:0; padding:0" id="tosicn" name="tosicn" ><span class="checkboxst"> Promo Competencia.</span></label><br/>
						</td>
					</tr>
				</tbody>
			</table>
  </div>
</div>
		<table width="80%" height="auto" border="0" style="margin:auto">
			<tr>
				<div id="divSiAtendio" >
					<table width="50%" border="0" style="float:right; right:20px; top:10px;">
						<tr>
      
							<div class="celda"><img src="img/comentarios.png"/><br>Observación<br><textarea tabindex="4" rows="4" cols="50" id="observacion2" class="hvr-border-fade descripcion tamañoinputx4" style="width:100%;height:77px" name="observacion2" placeholder="Observacion" ></textarea></div>
							
							<div class="celda"><img src="img/otroservicio.png"/><br><span> Tiene otro servicio <input tabindex="3" type="checkbox" id="checkServicio" onchange="desbloquear()"/></span><br><input maxlength="100" type="text" id="servicio" class="hvr-border-fade tamañoinputx4" name="servicio" placeholder="Servicio" disabled="true" style="background-color: #BCBCBC; width:100%"/><br></div>
							
							<div class="celda"><img src="img/direccion.png"/><br>Dirección<br><input tabindex="2" maxlength="255" type="text" id="direccion" class="hvr-border-fade tamañoinputx4" style="width:100%" name="direccion" placeholder="Direccion"/></div>

							<div class="celda"><img src="img/integrantes.png"/><br>Integrantes Familia<br><input tabindex="1" maxlength="10" type="text" id="integrantesFamilia" class="hvr-border-fade solo_numeros tamañoinputx4" style="width:100%" name="integrantesFamilia" placeholder="Integrantes Familia" /></div>

							<label id="lblErrorVender" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; padding-left:350px; font-size:15px;"></label>
						</tr>
					</table>

					
					<div id="divSiAtendio2" style="height:120px;display:none;">
						<h2 style="font-size:22px; background-color:#009cfd; color:#fff;height:40px; padding:5px; margin-top:170px; ">Agendar</h2>
							<table width="60%" border="0" style="float:right; margin-right:20px; margin-top:00px;">
								<tr>
								<div class="celda"> <img src="img/comentarios.png"><br>Comentarios<textarea tabindex="7" rows="4" cols="50" name="com" type="textarea" class="hvr-border-fade" placeholder="Comentarios" id="com" style="width:100%; height:77px;" ></textarea>
								<br><input type="button" class="mainbuttonagendar" id="guardar" value="Agendar" onclick="agendado()" />
								</div>
								<div class="celda"> <img src="img/fecha.png"><br>Fecha y Hora<input tabindex="6" name="fec_hor" type="text" placeholder="Fecha y Hora" id="datetimepicker" class="hvr-border-fade" style="width:90%"/></div>
								<div class="celda"> <img src="img/nombre.png"><br>Nombre<input tabindex="5" maxlength="100" name="nom" type="text" class="hvr-border-fade" placeholder="Nombre" id="nom" style="width:90%"/></div>
								</tr>
							</table>
			</tr>
			</table>
					</div>
					<hr style="border:solid 1px; color:#999; margin-top:144px;">
					<input type="button" class="mainbuttonvendido" id="btnVender"  value="Vendido" onclick="vendido()" />
					<input type="button" class="agendarbutton" id="btnAgendar" value="Agendar" onclick="verAgendar()" />
					<input type="button" class="mainbutton" id="btnNoLeInteresa" value="No Le Interesa" onclick="noInteresado()"/>
			</div>
		
			
    </div>
  </div>
</div>

<!-- MODAL DIV REFERIDO -->
<div id="divReferido" class="modal" >

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
		<span class="close" id="close6">X</span>
		<h2> <img src="img/telefonoreferido.png" width="24px"> Referido</h2>
    </div>
    <div class="modal-body">
		<table width="100%" height="200px" border="0" >
					<div id="divReferido2" style="width:100%;">		

					<td style="text-align:center; padding-bottom:10px; vertical-align:text-top"><p><img src="img/nombre.png"><br>Nombre</p><input maxlength="100" name="nomRef" type="text" class="hvr-border-fade" style="width:70%" placeholder="Nombre" id="nomRef" />

					<td style="text-align:center; padding-bottom:10px; vertical-align:text-top"><p><img src="img/numero.png"><br>Numero</p><input maxlength="9" name="numRef" type="text" class="hvr-border-fade solo_numeros" style="width:70%" placeholder="Numero" id="numRef" />
						
					<td style="text-align:center; padding-bottom:10px; vertical-align:text-top"><p><img src="img/comentarios.png"><br>Observación</p><textarea id="obsRef" class="hvr-border-fade" name="obsRef" placeholder="Observacion" style="width:70%" ></textarea>	
										
					<input type="button" class="mainbuttonreferido" id="btnAgregarReferido" value="Agregar Referido" onclick="referido()" style="position:absolute; bottom:12px; left:20px;"/>

					<label id="lblErrorReferido" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:200px; font-size:15px;"></label>

					</div>
		</table>
		<hr style="border:solid 1px; color:#999; position:relative; bottom:30px;"/>
    </div>

  </div>
</div>

<div style="position:absolute; right:35px; bottom:35px; color:#000; text-align:center; " class="hvr-bounce-in">
<a href="manual.pdf" target="_blank" style="text-decoration:none;"><img src="img/manualdelusuario2.png" width="100px"><br/></a></div>
</form>

<script>
/////////////////MODAL ALERT\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal1 = document.getElementById('divAlert');

// Get the button that opens the modal
//var btn1 = document.getElementById("referidos");

// Get the <span> element that closes the modal
//var span2 = document.getElementsByClassName("close3")[0];
var span1 = document.getElementById("close1");

// When the user clicks the button, open the modal 
//btn1.onclick = function() {
    //modal1.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
span1.onclick = function() {
    modal1.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal1) {
        modal1.style.display = "none";
    }
}

/////////////////MODAL 3\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal3 = document.getElementById('divNoLlamarMas');

// Get the button that opens the modal
var btn3 = document.getElementById("btnNoLLamarMas");

// Get the <span> element that closes the modal
//var span2 = document.getElementsByClassName("close3")[0];
var span3 = document.getElementById("close3");

// When the user clicks the button, open the modal 
btn3.onclick = function() {
    modal3.style.display = "block";
}

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
var modal4 = document.getElementById('divVerAgendados');

// Get the button that opens the modal
var btn4 = document.getElementById("btnVerAgendados");

// Get the <span> element that closes the modal
//var span5 = document.getElementsByClassName("close5")[0];
var span4 = document.getElementById("close4");

// When the user clicks the button, open the modal 
btn4.onclick = function() {
    modal4.style.display = "block";
}

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
var modal5 = document.getElementById('divAtendio');

// Get the button that opens the modal
var btn5 = document.getElementById("btnAtendio");

// Get the <span> element that closes the modal
//var span5 = document.getElementsByClassName("close5")[0];
var span5 = document.getElementById("close5");

// When the user clicks the button, open the modal 
btn5.onclick = function() {
    modal5.style.display = "block";
}

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

 /////////////////MODAL 6\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal6 = document.getElementById('divReferido');

// Get the button that opens the modal
var btn6 = document.getElementById("btnReferido");

// Get the <span> element that closes the modal
//var span5 = document.getElementsByClassName("close5")[0];
var span6 = document.getElementById("close6");

// When the user clicks the button, open the modal 
btn6.onclick = function() {
    modal6.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span6.onclick = function() {
    modal6.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal6) {
        modal6.style.display = "none";
    }
}

/////////////////MODAL 7\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal7 = document.getElementById('divVerReferidos');

// Get the button that opens the modal
var btn7 = document.getElementById("btnVerReferidos");

// Get the <span> element that closes the modal
//var span5 = document.getElementsByClassName("close5")[0];
var span7 = document.getElementById("close7");

// When the user clicks the button, open the modal 
btn7.onclick = function() {
    modal7.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span7.onclick = function() {
    modal7.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal7) {
        modal7.style.display = "none";
    }
}

/////////////////MODAL 8\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Get the modal
var modal8 = document.getElementById('divVerVendidos');

// Get the button that opens the modal
var btn8 = document.getElementById("btnVerVendidos");

// Get the <span> element that closes the modal
//var span5 = document.getElementsByClassName("close5")[0];
var span8 = document.getElementById("close8");

// When the user clicks the button, open the modal 
btn8.onclick = function() {
    modal8.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span8.onclick = function() {
    modal8.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal8) {
        modal8.style.display = "none";
    }
}


</script>
<script>

$.datetimepicker.setLocale('es');
$('#datetimepicker').keypress(function(event) {event.preventDefault();});
$('#datetimepicker').datetimepicker({
  minDate: 'today',
  allowTimes:
  ['06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30','10:00', '10:30', '11:00', '11:30',
  '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30','16:00', '16:30', '17:00', '17:30',
  '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30','22:00', '22:30', '23:00', '23:30'],
   format:'Y-m-d H:i',
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