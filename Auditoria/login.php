<!DOCTYPE html>
	<html lang="en">
	<head>
    <meta content="text/html" http-equiv="content-type">
    <meta content="lolkittens" name="author">
    <title>Logueo</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="Js/auditoriacall.js?v=1.1" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.js" type="text/javascript"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_es.js"></script>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../css/estilos.css?v=1.1">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="../img/icon.png" type="image/png" rel="icon">
	</head>
	<body>
	
	<header>
	<div class="logocall"><img src="../img/logocall.png"></div>
	<h1><div class="logotipocall"><img src="../img/logocall.png">	<span style="top:10px; margin-left:50px;">Auditoria </span></div></h1>
	</header>
<form id="login-form" method="post" name="buscar-form">
<div class="imagenlogin"></div>
		<div class="content">
			<table width="100%" border="0">
				<tbody>
					<tr>
						<td style="text-align:left;"><span class="iconoslogin"><img src="../img/usuario2.png"></span></td>
						<td style="text-align:left;"> <input name="usuario" type="text" class="hvr-border-fade" placeholder="Nombre de Usuario" id="usuario" style="width:250px"/></td>
					</tr>
					<tr>
						<td style="text-align:left;"><span class="iconoslogin"><img src="../img/contrasena.png"/></span></td>
						<td style="text-align:left;"><input name="contrasena" type="password" class="hvr-border-fade" placeholder="Contraseña" id="contrasena" style="width:250px"/></td>
					</tr>
				</tbody>
			</table>		
			<br>
			<div class="alert">
				<label  style="color: red;" id="lblLogin"></label>
			</div>
			<div class="footer">
				<input type="button" name="btnEntrar" value="Ingresar" class="hvr-border-fadebtn" id="btnEntrar" onclick="loginAuditoria()"/>
			</div>
		</div>
     </form>
	</body>