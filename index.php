<?php
session_start();

if (!isset($_SESSION["idusuario"]))
  header('Location: login.php');

$usuario = $_SESSION["idusuario"];

date_default_timezone_set('America/Argentina/Buenos_Aires');

$fecha = date("Y-m-d");
$final = date("Y-m-d", strtotime("+1 month"));
$mayorDieciocho = date('Y-m-d', strtotime('-18 year'));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  <meta content="text/html" http-equiv="content-type">
  <meta content="lolkittens" name="author">
  <link href="Css/estilos.css?v=1.8" rel="stylesheet">
  <script type="text/javascript" language="javascript" src="tabla/media/js/jquery.js"></script>
  <script type="text/javascript" language="javascript" src="tabla/media/js/jquery.dataTables.js"></script>
  <link rel="stylesheet" type="text/css" href="tabla/media/css/jquery.dataTables.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css" media="screen" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" media="screen" />
  <meta charset="UTF-8">
  <title>Call</title>
  <script src="Js/callNew.js?v=20240820_1" type="text/javascript"></script>
  <script src="Js/conveniosEspeciales.js?v=20240820_1" type="text/javascript"></script>
  <script src="Js/callGrupos.js?v=20240820_1" type="text/javascript"></script>
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="Css/jquery.datetimepicker.css" />
  <link href="img/icon.png" type="image/png" rel="icon">
  <script src="build/jquery.datetimepicker.full.min.js"></script>
  <!-- CONFIRM -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
  <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
  <script>
    Mercadopago.setPublishableKey("APP_USR-6bda2fca-9f81-4147-90e9-e7900560a474");
  </script>
  <link rel="stylesheet" type="text/css" href="Css/styles.css?v=101010">
</head>

<body onload="numeroInicial()" class="bodybackground">
  <!-- MODAL CALL ABITAB -->
  <div class="modal" id="modal-call-abitab" style="padding: 0;">
    <div class="modal-content" style="min-height: 100vh; margin: 0; width: 100%; padding: 0;">
      <div class="modal-header">
        <h3>Call Abitab <span class="close" style="color: #fff; opacity: 1;">&times;</span></h3>
      </div>
      <div class="modal-body" style="min-height: 100vh; padding: 0;"></div>
    </div>
  </div>

  <!-- MODAL DIV VIDASHOP -->
  <div id="divVidaShop" class="modal">

    <div class="modal-content">
      <div class="modal-header">
        <span class="close" id="close17">X</span>
        <h2><img alt="alt" src="" width="22px"> VidaShop</h2>
      </div>
      <div class="modal-body">
        <span>*La persona recibira 300 Vidapesos al momento de registrarse</span><br><br>
        <table width="80%" height="300px" border="0" style="margin:auto">
          <tr>
            <div id="divdeVidaShop">
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/numero.png"><br>Celular</p><input maxlength="9" name="celularVidaShop" type="text" class="hvr-border-fade solo_numeros" style="width:70%" placeholder="Numero" id="celularVidaShop" />

                <input type="button" class="mainbuttonreferido" id="btnAgregarVidaShop" value="Enviar Vidapesos" onclick="vidashop()" style="position:absolute; bottom:12px; left:20px;" />

                <label id="lblErrorVidaShop" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:200px; font-size:15px;"></label>
            </div>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <!-- FIN MODAL CALL ABITAB -->
  <div id="primaria">
    <img alt="alt" src="img/loading.gif" id="loading" />
  </div>

  <!-- ################################# Modal padron socio ################################# -->
  <div class="modal" id="modalPadronSocio">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close">X</span>
        <h2>Padron socio</h2>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col">
            <iframe src="" frameborder="0" id="iframePadronSocio" style="width: 100%; height: 100vh"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ################################# Modal patron socio ################################# -->

  <div class="bienvenida">
    <span style="left:10px; position:absolute; color:#fff">Bienvenid@ : </span> <label style="left:110px; position:absolute; color:#fff;" id="lblBienv"></label>
    <span style="left:400px; position:absolute; color:#fff">Grupo : </span> <label style="left:460px; position:absolute; color:#fff;" id="lblGrupo"></label>
    <button type="button" class="botonsalir" id="cerrar" name="cerrar" value="Salir"> <img alt="alt" src="img/opendoor.png" width="18px"> Salir</button>
    <br />
  </div>
  <div class="contenidolateral">
    <button type="button" class="leftbutton" id="btnVerAgendados">
      <img alt="alt" src="img/agendados.png" width="24px">
      <label for="btnVerAgendados">Ver Agendados</label>
      <span id="lblBadge1" class="badge"></span>
    </button>
    <button type="button" class="leftbutton" id="btnVerReferidos">
      <img alt="alt" src="img/referidos.png" width="24px">
      <label for="btnVerReferidos">Ver Referidos</label>
    </button>
    <button type="button" class="leftbutton" id="btnAgregarReferidosCuaderno">
      <img alt="alt" src="img/vendidosrojo.png" width="24px">
      <label for="btnAgregarReferidosCuaderno">Agregar Ref. Cuaderno</label>
    </button>
    <button type="button" class="leftbutton" id="btnVerReferidosCuaderno">
      <img alt="alt" src="img/referidoscuaderno.png" width="24px">
      <label for="btnVerReferidosCuaderno">Ref. Cuaderno</label>
    </button>
    <button type="button" class="leftbutton" id="btnVerReferidosCuadernoPendientes">
      <img alt="alt" src="img/referidoscuaderno.png" width="24px">
      <label for="btnVerReferidosCuadernoPendientes">Ref. Cuaderno Pendientes</label>
    </button>
    <button type="button" class="leftbutton" id="btnAnotacionesCuaderno">
      <img alt="alt" src="img/cuadernoicon.png" width="24px">
      <label for="btnAnotacionesCuaderno">Anotaciones Cuaderno</label>
    </button>
    <button type="button" class="leftbutton" id="btnVerVendidos">
      <img alt="alt" src="img/vendidos.png" width="24px">
      <label for="btnVerVendidos">Ver Vendidos</label>
    </button>
    <button type="button" class="leftbutton" id="btnVerBajas">
      <img alt="alt" src="img/bajasocioicon.png" width="24px">
      <label for="btnVerBajas">Chequear Baja</label>
    </button>
    <button type="button" class="leftbutton" id="btnVerPadron">
      <img alt="alt" src="img/agendados.png" width="24px">
      <label for="btnVerPadron">Padron</label>
    </button>
    <button type="button" class="leftbutton" id="btnCallAbitab">
      <label for="btnCallAbitab">Call Abitab</label>
    </button>
    <button type="button" class="leftbutton" id="btnLlamadaEntrante">
      <label for="btnLlamadaEntrante">Cargar contrato</label>
    </button>
    <button type="button" class="leftbutton" id="btnAyudaCompetencia">
      <label for="btnAyudaCompetencia">Ayuda Competencia</label>
    </button>
    <button type="button" class="leftbutton" id="btnLlamadaEntranteComepa" soloComepa>
      <label for="btnLlamadaEntranteComepa">Contrato COMEPA</label>
    </button>
    <button type="button" class="leftbutton" id="btnCrediv" style="display:none">
      <label for="btnCrediv">Crediv</label>
    </button>
    <button type="button" class="leftbutton" id="btnBilletera">
      <label for="btnBilletera">Registro billetera</label>
    </button>
  </div>
  <!----///////////////// VENTANA HOVER ///////////////---->
  <ul>
    <li id="modalhover" style="width:50px; position:absolute; margin-left:49%; margin-top:250px; display:block; text-align:center;">
      <div class="vineta" id="vineta" name="vineta"></div>
      <div class="modalhover">
        <table style="position: absolute;width: 210%;" id="tblInfo">
          <caption style="font-weight: bold; font-size: 18px; color:#81A313;"><img alt="alt" src="img/historial.png" width="22px"> Historial del numero:
            <hr class="hrmodalhover" />
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
      <img alt="alt" src="img/call.png" width="100px"><br><label id="lblNumero" class="callnumero"></label>
    </div>
    <br />
    <label id="lblError" style="margin-left: 40%; margin-top:20px; color: red;font-size: 20px;"></label><br>
    <div class="localidad">
      <label id="lblLocalidad" style="color: green;font-size: 18px;">Canelones - Santa Lucia</label>
    </div>
    <br /><br />

    <div class="contenedorbtn">
      <button type="button" class="mainbuttonvendido" id="btnAtendio" value="Atendió"> <img alt="alt" src="img/telefono.png" width="16px"> Atendió</button>
      <button type="button" class="mainbuttonvendido btn-primary" id="btnReagendar" style="display: none;" onclick="verReagendar();" value="Atendió"> <img alt="alt" src="img/agendados.png" width="16px"> Reagendar</button>
      <button type="button" class="mainbutton" id="btnNoContesta" value="No Contesta" onclick="noContesta()"> <img alt="alt" src="img/telefononocontesta.png" width="16px"> No Contesta</button>
      <button type="button" class="mainbuttonreferido" id="btnReferido" value="Referido"> <img alt="alt" src="img/telefonoreferido.png" width="16px"> Referido</button>
      <button type="button" class="mainbuttonnollamar" id="btnNoLLamarMas" value="No Llamar Mas"> <img alt="alt" src="img/telefononoatendio.png" width="16px"> No Llamar Más </button>
      <button style="display:none" type="button" class="mainbuttonsumaygana" id="btnVidaShop" value="VidaShop"> <img alt="alt" src="img/logovidashopcall.png" width="16px"> VidaShop </button>

    </div>
    <!-- MODAL DIV NO LLAMAR -->
    <div id="divNoLlamarMas" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close3">&times;</span>
          <h2><img alt="alt" src="img/telefononoatendio.png" width="22px"> No Llamar Mas</h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="300px" border="0" style="margin:auto">
            <tr>
              <div id="divEliminar">

                <td style="text-align:center; padding-bottom:10px; vertical-align:text-top"> <img alt="alt" src="img/comentarios.png"><br>Observación<br><textarea rows="4" cols="50" id="observacion" class="hvr-border-fade" style="width:80%" name="observacion" placeholder="Observacion"></textarea></td>

                <input type="button" class="mainbuttonnollamar" id="btnListaNegra" value="Quitar Permanentemente" onclick="quitarPermanente()" style="position:absolute; bottom:12px" />

                <label id="lblErrorListaNegra" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; margin-left:240px; font-size:15px;"></label>

              </div>
            </tr>
          </table>

        </div>
      </div>
    </div>

    <!-- MODAL DIV ALERT BIENVENIDA -->
    <div id="divAlert" class="modalalert">
      <div class="modal-content">
        <div class="modal-alertheader">
          <span class="close" id="close1">X</span>
          <h2 style="font-size:42px">Bienvenid@!</h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="150px" border="0" style="margin:auto">
            <tr>
              <div id="divAlert">
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


    <!-- MODAL VENTA - SOLICITUD NÚMERO -->
    <div class="modal" tabindex="-1" role="dialog" id="modalLlamadaEntrante">
      <div class="" style="max-width: 900px; margin: 0 auto;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">X</span>
            </button>
            <h2 class="modal-title">Llamada entrante</h2>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger alert-dismissable" id="error-nuevo-telefono">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <strong>Error: </strong><span class="error-nuevo-telefono"></span>
            </div>
            <form action="#!">
              <div class="form-group">
                <label for="nuevoNumero">Digita el nuevo télefono</label>
                <input type="text" name="nuevoNumero" id="nuevoNumero" class="solo_numeros form-control" placeholder="Ingresa el número de télefono" maxlength="9" />
              </div>
              <div class="form-group">
                <button class="btn btn-primary" id="btnGuardarlLlamadaEntrante">Agregar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


    <!-- MODAL DIV VER AGENDADOS -->
    <div id="divVerAgendados" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close4">X</span>
          <h2> <img alt="alt" src="img/agendados.png" width="36px"> Agendados </h2>
          <div class="referencias">
            <img alt="alt" src="img/Rojo.png" /> Agendados Vencidos <img alt="alt" src="img/Verde.png" /> Agendados para el día <img alt="alt" src="img/Blanco.png" /> Agendados a futuro
          </div>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Fecha desde</label>
                <input type="text" id="FechaDesdeAgendado" class="hvr-border-fade fechasAgendado" placeholder="fecha desde">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Fecha hasta</label>
                <input type="text" id="FechaHastaAgendado" class="hvr-border-fade fechasAgendado" placeholder="fecha hasta">
              </div>
            </div>


            <button type="button" onclick="buscarAgendados($('#FechaDesdeAgendado').val(),$('#FechaHastaAgendado').val())" id="btnBuscarAgendado" class="mainbuttonvendido">Buscar</button>


          </div>
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display">
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

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close7">X</span>
          <h2> <img alt="alt" src="img/referidos.png" width="36px"> Referidos</h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla2" cellpadding="0" cellspacing="0" border="0" class="display">
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

    <!-- MODAL DIV VER REFERIDOS CUADERNO-->
    <div id="divVerReferidosCuaderno" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close12">X</span>
          <h2> <img alt="alt" src="img/referidoscuaderno.png" width="36px"> Referidos Cuaderno</h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla6" cellpadding="0" cellspacing="0" border="0" class="display">
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

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close8">X</span>
          <h2> <img alt="alt" src="img/vendidos.png" width="36px"> Vendidos</h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla3" cellpadding="0" cellspacing="0" border="0" class="display">
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
    <div id="divAtendio" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close5">X</span>
          <div class="atendiotitulo">
            <h2 id="tit"><img alt="alt" src="img/telefono.png" width="26px">Atendió</h2>
          </div>
          <ul id="ulBotones" style="margin-right:20px;">
            <li style="list-style:none;"><a id="btnTosic" class="botoncartilla" onclick="mostrarTosic()">Tiene otro servicio y le interesa cambiar.</a></li>
            <!-- <li style="list-style:none;"><a id="btnTosnic" class="botoncartilla" onclick="mostrarTosnic()">Tiene otro servicio y no le interesa cambiar.</a></li> -->
            <li style="list-style:none;"><a id="btnSv" class="botoncartilla" onclick="mostrarSv()">Socio Vida.</a></li>
            <li style="list-style:none;"><a id="btnNts" class="botoncartillaseleccionado" onclick="mostrarNts()">No tiene servicio.</a></li>
          </ul>
        </div>
        <div class="modal-body">
          <div id="tabs">
            <br />
            <div id="nts" style="display:block; font-size:10px;">
              <table style="width:22%; border:0; float:left; position:absolute; top:15px; vertical-align:top;">
                <tbody>
                  <tr>
                    <td valign="top">
                      <input type="checkbox" style="margin:0; padding:0; display:none" id="chknts" name="chknts" checked="true">
                      <label for="ntsa"><input type="checkbox" style="margin:0; padding:0" id="ntsa" name="ntsa"><span class="checkboxst">Sanatorio.</span></label><br />
                      <label for="ntsb"><input type="checkbox" style="margin:0; padding:0" id="ntsb" name="ntsb"><span class="checkboxst"> Convalecencia.</span></label><br />
                      <label for="ntsc"><input type="checkbox" style="margin:0; padding:0" id="ntsc" name="ntsc"><span class="checkboxst"> Domicilio Especial.</span></label><br />
                      <label for="ntsd"><input type="checkbox" style="margin:0; padding:0" id="ntsd" name="ntsd"><span class="checkboxst"> Reintegro.</span></label><br />
                      <label for="ntse"><input type="checkbox" style="margin:0; padding:0" id="ntse" name="ntse"><span class="checkboxst"> Amparo.</span></label><br />
                      <label for="ntsf"><input type="checkbox" style="margin:0; padding:0" id="ntsf" name="ntsf"><span class="checkboxst"> Amparo Plus.</span></label><br />
                      <label for="ntsg"><input type="checkbox" style="margin:0; padding:0" id="ntsg" name="ntsg"><span class="checkboxst"> Assist Express.</span></label><br />
                    </td>
                    <td valign="top">
                      <label for="ntsh"><input type="checkbox" style="margin:0; padding:0" id="ntsh" name="ntsh"><span class="checkboxst"> Assist Plus.</span></label><br />
                      <label for="ntsi"><input type="checkbox" style="margin:0; padding:0" id="ntsi" name="ntsi"><span class="checkboxst"> Hotel.</span></label><br />
                      <label for="ntsj"><input type="checkbox" style="margin:0; padding:0" id="ntsj" name="ntsj"><span class="checkboxst"> Grupo Familiar.</span></label><br />
                      <label for="ntsk"><input type="checkbox" style="margin:0; padding:0" id="ntsk" name="ntsk"><span class="checkboxst"> Tarjeta Vida.</span></label><br />
                      <label for="ntsl"><input type="checkbox" style="margin:0; padding:0" id="ntsl" name="ntsl"><span class="checkboxst"> FB2012.</span></label><br />
                      <label for="ntsm"><input type="checkbox" style="margin:0; padding:0" id="ntsm" name="ntsm"><span class="checkboxst"> Super Promo.</span></label><br />
                      <label for="ntsn"><input type="checkbox" style="margin:0; padding:0" id="ntsn" name="ntsn"><span class="checkboxst"> Promo Competencia.</span></label><br />
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
                      <label for="sva"><input type="checkbox" style="margin:0; padding:0" id="sva" name="sva"><span class="checkboxst">Sanatorio.</span></label><br />
                      <label for="svb"><input type="checkbox" style="margin:0; padding:0" id="svb" name="svb"><span class="checkboxst"> Convalecencia.</span></label><br />
                      <label for="svc"><input type="checkbox" style="margin:0; padding:0" id="svc" name="svc"><span class="checkboxst"> Domicilio Especial.</span></label><br />
                      <label for="svd"><input type="checkbox" style="margin:0; padding:0" id="svd" name="svd"><span class="checkboxst"> Reintegro.</span></label><br />
                      <label for="sve"><input type="checkbox" style="margin:0; padding:0" id="sve" name="sve"><span class="checkboxst"> Amparo Plus.</span></label><br />
                      <label for="svf"><input type="checkbox" style="margin:0; padding:0" id="svf" name="svf"><span class="checkboxst"> Assist Plus.</span></label><br />
                      <label for="svg"><input type="checkbox" style="margin:0; padding:0" id="svg" name="svg"><span class="checkboxst"> Hotel.</span></label><br />
                    </td>
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
                      <label for="tosnica"><input type="checkbox" style="margin:0; padding:0" id="tosnica" name="tosnica"><span class="checkboxst">Sanatorio.</span></label><br />
                      <label for="tosnicb"><input type="checkbox" style="margin:0; padding:0" id="tosnicb" name="tosnicb"><span class="checkboxst"> Convalecencia.</span></label><br />
                      <label for="tosnicc"><input type="checkbox" style="margin:0; padding:0" id="tosnicc" name="tosnicc"><span class="checkboxst"> Domicilio Especial.</span></label><br />
                      <label for="tosnicd"><input type="checkbox" style="margin:0; padding:0" id="tosnicd" name="tosnicd"><span class="checkboxst"> Reintegro.</span></label><br />
                      <label for="tosnice"><input type="checkbox" style="margin:0; padding:0" id="tosnice" name="tosnice"><span class="checkboxst"> Amparo.</span></label><br />
                      <label for="tosnicf"><input type="checkbox" style="margin:0; padding:0" id="tosnicf" name="tosnicf"><span class="checkboxst"> Amparo Plus.</span></label><br />
                      <label for="tosnicg"><input type="checkbox" style="margin:0; padding:0" id="tosnicg" name="tosnicg"><span class="checkboxst"> Assist Express.</span></label><br />
                    </td>
                    <td valign="top">
                      <label for="tosnich"><input type="checkbox" style="margin:0; padding:0" id="tosnich" name="tosnich"><span class="checkboxst"> Assist Plus.</span></label><br />
                      <label for="tosnici"><input type="checkbox" style="margin:0; padding:0" id="tosnici" name="tosnici"><span class="checkboxst"> Hotel.</span></label><br />
                      <label for="tosnicj"><input type="checkbox" style="margin:0; padding:0" id="tosnicj" name="tosnicj"><span class="checkboxst"> Grupo Familiar.</span></label><br />
                      <label for="tosnick"><input type="checkbox" style="margin:0; padding:0" id="tosnick" name="tosnick"><span class="checkboxst"> Tarjeta Vida.</span></label><br />
                      <label for="tosnicl"><input type="checkbox" style="margin:0; padding:0" id="tosnicl" name="tosnicl"><span class="checkboxst"> FB2012.</span></label><br />
                      <label for="tosnicm"><input type="checkbox" style="margin:0; padding:0" id="tosnicm" name="tosnicm"><span class="checkboxst"> Super Promo.</span></label><br />
                      <label for="tosnicn"><input type="checkbox" style="margin:0; padding:0" id="tosnicn" name="tosnicn"><span class="checkboxst"> Promo Competencia.</span></label><br />
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
                      <label for="tosica"><input type="checkbox" style="margin:0; padding:0" id="tosica" name="tosica"><span class="checkboxst">Sanatorio.</span></label><br />
                      <label for="tosicb"><input type="checkbox" style="margin:0; padding:0" id="tosicb" name="tosicb"><span class="checkboxst"> Convalecencia.</span></label><br />
                      <label for="tosicc"><input type="checkbox" style="margin:0; padding:0" id="tosicc" name="tosicc"><span class="checkboxst"> Domicilio Especial.</span></label><br />
                      <label for="tosicd"><input type="checkbox" style="margin:0; padding:0" id="tosicd" name="tosicd"><span class="checkboxst"> Reintegro.</span></label><br />
                      <label for="tosice"><input type="checkbox" style="margin:0; padding:0" id="tosice" name="tosice"><span class="checkboxst"> Amparo.</span></label><br />
                      <label for="tosicf"><input type="checkbox" style="margin:0; padding:0" id="tosicf" name="tosicf"><span class="checkboxst"> Amparo Plus.</span></label><br />
                      <label for="tosicg"><input type="checkbox" style="margin:0; padding:0" id="tosicg" name="tosicg"><span class="checkboxst"> Assist Express.</span></label><br />
                    </td>
                    <td valign="top">
                      <label for="tosich"><input type="checkbox" style="margin:0; padding:0" id="tosich" name="tosich"><span class="checkboxst"> Assist Plus.</span></label><br />
                      <label for="tosici"><input type="checkbox" style="margin:0; padding:0" id="tosici" name="tosici"><span class="checkboxst"> Hotel.</span></label><br />
                      <label for="tosicj"><input type="checkbox" style="margin:0; padding:0" id="tosicj" name="tosicj"><span class="checkboxst"> Grupo Familiar.</span></label><br />
                      <label for="tosick"><input type="checkbox" style="margin:0; padding:0" id="tosick" name="tosick"><span class="checkboxst"> Tarjeta Vida.</span></label><br />
                      <label for="tosicl"><input type="checkbox" style="margin:0; padding:0" id="tosicl" name="tosicl"><span class="checkboxst"> FB2012.</span></label><br />
                      <label for="tosicm"><input type="checkbox" style="margin:0; padding:0" id="tosicm" name="tosicm"><span class="checkboxst"> Super Promo.</span></label><br />
                      <label for="tosicn"><input type="checkbox" style="margin:0; padding:0" id="tosicn" name="tosicn"><span class="checkboxst"> Promo Competencia.</span></label><br />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <table width="80%" height="auto" border="0" style="margin:auto">
            <tr>
              <div id="divSiAtendio">
                <table width="50%" border="0" style="float:right; right:20px; top:10px;">
                  <tr>
                    <div class="celda"><img alt="alt" src="img/comentarios.png" /><br>Observación<br><textarea tabindex="4" rows="4" cols="50" id="observacion2" class="hvr-border-fade descripcion tamañoinputx4" style="width:100%;height:77px" name="observacion2" placeholder="Observacion"></textarea></div>

                    <!-- <div class="celda"><img alt="alt" src="img/otroservicio.png"/><br><span> Tiene otro servicio <input tabindex="3" type="checkbox" id="checkServicio" onchange="desbloquear()"/></span><br><input maxlength="100" type="text" id="servicio" class="hvr-border-fade tamañoinputx4" name="servicio" placeholder="Servicio" disabled="true" style="background-color: #BCBCBC; width:100%"/><br></div> -->

                    <div class="celda"><img alt="alt" src="img/otroservicio.png" /><br><span> Tiene otro servicio <input tabindex="3" type="checkbox" id="checkServicio" onchange="desbloquear()" /></span><br>
                      <select name="servicio" id="servicio" class="hvr-border-fade tamañoinputx4" disabled>
                        <option value="" selected disabled>- Seleccione -</option>
                        <option value="VIGILIA">VIGILIA</option>
                        <option value="AMEC">AMEC</option>
                        <option value="ALCANCE">ALCANCE</option>
                        <option value="SECOM">SECOM</option>
                        <option value="ACOMPAÑA">ACOMPAÑA</option>
                        <option value="DAME">DAME</option>
                        <option value="PULSO">PULSO</option>
                        <option value="FAMILIA ACOMPAÑANTES">FAMILIA ACOMPAÑANTES</option>
                        <option value="OTRO">OTRO</option>
                      </select>

                      <br>
                    </div>

                    <div class="celda"><img alt="alt" src="img/direccion.png" /><br>Dirección<br><input tabindex="2" maxlength="255" type="text" id="direccion" class="hvr-border-fade tamañoinputx4" style="width:100%" name="direccion" placeholder="Direccion" /></div>

                    <div class="celda"><img alt="alt" src="img/integrantes.png" /><br>Integrantes Familia<br><input tabindex="1" maxlength="10" type="text" id="integrantesFamilia" class="hvr-border-fade solo_numeros tamañoinputx4" style="width:100%" name="integrantesFamilia" placeholder="Integrantes Familia" /></div>

                    <label id="lblErrorVender" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; padding-left:350px; font-size:15px;"></label>
                  </tr>
                </table>


                <div id="divSiAtendio2" style="height:120px;display:none;">
                  <h2 style="font-size:22px; background-color:#009cfd; color:#fff;height:40px; padding:5px; margin-top:170px; ">Agendar</h2>
                  <table width="60%" border="0" style="float:right; margin-right:20px; margin-top:00px;">
                    <tr>
                      <div class="celda"> <img alt="alt" src="img/comentarios.png"><br>Comentarios<textarea tabindex="7" rows="4" cols="50" name="com" type="textarea" class="hvr-border-fade" placeholder="Comentarios" id="com" style="width:100%; height:77px;"></textarea>
                        <br><input type="button" class="mainbuttonagendar" id="guardar" value="Agendar" onclick="agendado()" />
                      </div>
                      <div class="celda"> <img alt="alt" src="img/fecha.png"><br>Fecha y Hora<input tabindex="6" name="fec_hor" type="text" placeholder="Fecha y Hora" id="datetimepicker" class="hvr-border-fade" style="width:90%" /></div>
                      <div class="celda"> <img alt="alt" src="img/nombre.png"><br>Nombre<input tabindex="5" maxlength="100" name="nom" type="text" class="hvr-border-fade" placeholder="Nombre" id="nom" style="width:90%" /></div>
                    </tr>
                  </table>
            </tr>
          </table>
        </div>
        <hr style="border:solid 1px; color:#999; margin-top:170px;">
        <!-- <input type="button" class="mainbuttonvendido" id="btnVender"  value="Vendido" onclick="vendido()" /> -->
        <input type="button" class="mainbuttonvendido" id="btnVender" value="Vendido" />
        <input type="button" class="agendarbutton" id="btnAgendar" value="Agendar" onclick="verAgendar()" />
        <input type="button" class="mainbutton" id="btnNoLeInteresa" value="No Le Interesa" onclick="noInteresado()" />
      </div>


    </div>
    </div>
    </div>

    <!-- MODAL SELECCION TIPO DE AFILIACION -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-tipo-afiliacion">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">X</span>
            </button>
            <h2 class="modal-title">Elegir tipo de afiliación</h2>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger alert-dismissable" id="error-tipo-afiliacion">
              <strong>Error: </strong><span class="error-tipo-afiliacion"></span>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="">Tipo de afiliación</label>
                  <select name="tipo-afiliaciones" id="tipo-afiliaciones" class="custom-select form-control">
                    <option value="">- Seleccionar -</option>
                    <option value="1">Afiliación individual</option>
                    <option value="2">Afiliación de grupo</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="background-color: #fff;">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnSeleccionSiguiente">Siguiente</button>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN SELECCION TIPO DE AFILIACION -->

    <!-- MODAL VENTA -->
    <div class="modal" tabindex="-1" role="dialog" id="modal-venta">
      <div class=" modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">X</span>
            </button>
            <h2 class="modal-title">Datos de la venta</h2>
          </div>
          <div class="modal-body">
            <form id="pay" name="pay">
              <div class="alert alert-danger alert-dismissable" id="error-validacion-cedula">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error: </strong><span class="error-validacion-cedula"></span>
              </div>
              <!-- div validacion cedula -->
              <div class="centrado" id="validacion_cedula">
                <div class="form-group">
                  <h3 class="texto">Validar cédula del beneficiario en el padron</h3>
                </div>
                <div class="form-group">
                  <!--//seba-->
                  <h5 id="titleTarjetas12cuotas"></h5>
                </div>
                <div class="form-group">
                  <!--//seba-->
                  <h5>Tarj. Mercadopago: VISA, MASTER, OCA, LIDER, AMERICAN EXPRESS, DINERS</h5>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="" class="texto">Cédula <span class="requerido">*</span></label>
                      <input type="text" class="form-control solo_numeros" name="cedBen" id="cedBen" required>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="btnValidarCedula">Validar</button>
                  </div>
                </div>
              </div>
              <!-- fin div validacion cedula -->
              <!-- div paso uno -->
              <div class="centrado" id="pasouno" style="display: none;">
                <nav area-label="Page navigation example">
                  <ul class="paginacion d-flex justify-content-center modal-3">
                    <li class="page-item activo"><a href="#" class="item-paginacion">1</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">2</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">3</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                  </ul>
                </nav>
                <div class="alert alert-danger alert-dismissable" id="error-datos">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error: </strong><span class="error-datos"></span>
                </div>
                <div class="form-group">
                  <h3 class="texto text-center" id="pasouno-title">NUEVA ALTA</h3>
                </div>
                <div class="form-group">
                  <h3 class="texto">Complete los datos del beneficiario del servicio</h3>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="" class="texto">Cédula</label>
                      <input type="text" class="form-control disabled" name="cedBen2" id="cedBen2">
                    </div>
                    <div class="form-group">
                      <label for="" class="texto">Nombre completo <span class="requerido">*</span></label>
                      <input type="text" class="form-control solo_letras input-error" name="nomBen" id="nomBen" required>
                    </div>
                    <div class="form-group">
                      <label for="" class="texto">Fecha de nacimiento <span class="requerido">*</span></label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <select name="nataliciodia" id="nataliciodia" class="custom-select form-control input-error">
                        <option value="">- Dia -</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <select name="nataliciomes" id="nataliciomes" class="custom-select form-control input-error">
                        <option value="">- Mes -</option>
                        <option value="01">Enero</option>
                        <option value="02">Febrero</option>
                        <option value="03">Marzo</option>
                        <option value="04">Abril</option>
                        <option value="05">Mayo</option>
                        <option value="06">Junio</option>
                        <option value="07">Julio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <select name="natalicioano" id="natalicioano" class="custom-select form-control input-error">
                        <option value="">- Año -</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <!-- ACTUALIZACION DIRECCION-->
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="" class="texto">Calle <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="calle" id="calle" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-froup">
                      <label class="texto">Elige una opción <span class="requerido">*</span></label>
                      <fieldset id="radioPuerta">
                        <div class="radio">
                          <label>
                            <input type="radio" id="puertaChecked" name="checkPuerta" class="checkPuerta" value="0">
                            Puerta
                          </label>
                          <label>
                            <input type="radio" id="solarChecked" name="checkPuerta" class="checkPuerta" value="1">
                            Solar/manzana
                          </label>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                  <div class="col-md-1" style="display:none;" id="divPuerta">
                    <div class="form-group">
                      <label for="" class="texto">Puerta <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles solo_numeros input-error" limitecaracteres="4" maxlength="4" name="puerta" id="puerta" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                    </div>
                  </div>
                  <div class="col-md-1" style="display:none;" id="divSolar">
                    <div class="form-group">
                      <label for="" class="texto">Solar <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="solar" id="solar" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                    </div>
                  </div>
                  <div class="col-md-1" style="display:none;" id="divManzana">
                    <div class="form-group">
                      <label for="" class="texto">Manzana <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="manzana" id="manzana" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="" class="texto">Esquina <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="esquina" id="esquina" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                    </div>
                  </div>
                  <div class="col-md-1">
                    <div class="form-group">
                      <label for="" class="texto">Apartamento</label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="4" name="apto" id="apto" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="" class="texto">Referencia <span class="requerido">*</span></label>
                      <input type="text" class="form-control input-error" name="referencia" id="referencia" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="" class="texto">Departamento <span class="requerido">*</span></label>
                      <select name="depBen" id="depBen" class="custom-select form-control input-error">
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2" id="select-localidad">
                    <div class="from-group">
                      <label for="" class="texto">Localidad <span class="requerido">*</span></label>
                      <select name="locBen" id="locBen" class="custom-select form-control input-error">
                        <option value="">- Seleccione -</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="" class="texto">Correo electrónico</label>
                      <input type="text" class="form-control input-error" name="mailBen" id="mailBen" required>
                    </div>
                    <div class="form-group">
                      <label for="" class="texto">Celular <span class="requerido">*</span> </label>
                      <input type="text" class="form-control solo_numeros input-error" maxlength="9" name="celBen" id="celBen" required>
                    </div>
                    <div class="form-group">
                      <label for="" class="texto">Teléfono fijo</label>
                      <input type="text" class="form-control solo_numeros input-error" maxlength="9" name="telBen" id="telBen" required>
                    </div>
                    <div class="form-group">
                      <label for="" class="texto">Teléfono alternativo</label>
                      <input type="text" class="form-control solo_numeros input-error" name="telAltBen" id="telAltBen" required>
                    </div>
                  </div>
                </div>
                <div class="row" id="divDatosAdicionales">
                  <div class="col-md-12">
                    <fieldset id="dato_adicional">
                      <label class="texto">Elige una opción en caso de aplicar a alguna</label>
                      <div class="row">
                        <div class="col-md-3">
                          <label>
                            <input type="radio" name="dato_adicional" value="1"> Competencia (aplica promo para servicios tradicionales)
                          </label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>
                            <input type="radio" name="dato_adicional" value="5"> Competencia 2023
                          </label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>
                            <input type="radio" name="dato_adicional" value="2"> Herencia
                          </label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label>
                            <input type="radio" name="dato_adicional" value="3" checked> No aplica
                          </label>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="" class="texto">IMPORTANTE: Los campos con <span class="requerido">*</span> son obligatorios</label>
                    </div>
                  </div>
                </div>
                <div class="modal-footer-venta">
                  <button type="button" id="btnAtras1" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguiente1" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                </div>
              </div>
              <!-- fin div paso uno -->
              <!-- div paso dos -->
              <div class="centrado" id="pasodos" style="display:none">
                <div class="texto" id="servis">
                  <nav area-label="Page navigation example">
                    <ul class="paginacion d-flex justify-content-center modal-3">
                      <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                      <li class="page-item activo"> <a href="#" class="item-paginacion">2</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">3</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                    </ul>
                  </nav>
                  <div class="alert alert-danger alert-dismissable" id="error-productos">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error: </strong><span class="error-productos"></span>
                  </div>
                  <div class="form-group">
                    <h3 class="texto">Seleccione los servicios</h3>
                  </div>
                  <div class="row" id="row_sanatorio"></div>
                  <div class="row pro" id="producto1">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Servicios <span class="requerido">*</span></label>
                        <select id="producto" name="producto" class="custom-select form-control produc ">
                          <option value="0" selected>Seleccione servicio</option>
                        </select>
                        <input type="hidden" class="importe-servicio" id="importe-servicio">
                        <input type="hidden" class="importe-servicio" id="importe-omt">
                        <input type="hidden" id="nro_servicio" name="nro_servicio">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <div id="divHorasServicio" class="divHorasServicio" style="display:none">
                          <label for="" class="texto">Horas servicio <span class="requerido">*</span></label>
                          <select id="hrservicio" name="hrservicio" class="custom-select form-control hservicio">
                            <option value="0" selected disabled>Seleccione cantidad</option>
                            <option value="8" data-base="1">8 hs</option>
                            <option value="16" data-base="1">16 hs</option>
                            <option value="24" data-base="1">24 hs</option>
                          </select>
                          <input type="hidden" class="importe-servicio" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <div id="divPromo" class="divPromo" style="display: none;">
                          <label for="" class="texto">Promo</label>
                          <select id="promo" name="promo" class="custom-select form-control promo">
                            <option value="0" selected>Seleccione promo</option>
                            <option value="20">NP17</option>
                          </select>
                          <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
                        </div>
                        <div id="divBoton" class="divBoton" style="display: none; ">
                          <div class="form-group">
                            <button type="button" id="btnAgregarBeneficiario" class="btn btn-primary form-control btnbeneficiario">Agregar beneficiarios</button>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div id="nuevos_servicios"></div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="button" id="btnAgregarServicio" class="btn btn-primary form-control">Agregar servicio</button>
                    </div>

                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="button" id="btnQuitarServicio" class="btn btn-danger form-control">Quitar servicio</button>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group" id="btnOmt">
                      <button type="button" id="btnAgregarOmt" class="btn btn-success form-control btnAgregarOmt">Agregar OMT</button>
                    </div>
                  </div>
                  <div class="col-md-2" style="display:none">
                    <div class="form-group btnMama">
                      <button type="button" class="btn btn-success form-control btnAgregarMama">Agregar Promo mamá</button>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="divComentario" id="divComentario">
                        <label for="" class="texto">Observación <span class="requerido">*</span> </label>
                        <textarea class="form-control" name="comentario" id="comentario" rows="3" placeholder=""></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-8 container-subtotal" style="margin-top:30px">
                    <div class="subtotal" style="display: none;">
                      <p class="subtotal-price font-weight-bold">Total $UY: <span id="subtotal-price"></span></p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="" class="texto">IMPORTANTE: Los campos con <span class="requerido">*</span> son obligatorios</label>
                    </div>
                  </div>
                </div>

                <div class="row" solocomepa>
                  <div class="col-lg-6">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <input type="checkbox" id="ingresar_socio_comepa_afiliacion" ingresar_socio_comepa aria-label="Ingresar también a COMEPA">
                      </span>
                      <label for="ingresar_socio_comepa_afiliacion" class="form-control">Ingresar también a COMEPA</label>
                    </div>
                  </div>
                </div>

                <div class="modal-footer-venta">
                  <button type="button" id="btnAtras2" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguiente2" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                </div>
              </div>
              <!-- fin div paso dos -->
              <!-- div paso uno (socio) -->
              <div class="centrado" id="pasodossocio" style="display:none;">
                <div class="texto" id="servis-socio">
                  <nav area-label="Page navigation example">
                    <ul class="paginacion d-flex justify-content-center modal-3">
                      <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                      <li class="page-item activo"> <a href="#" class="item-paginacion">2</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">3</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                    </ul>
                  </nav>
                  <div class="alert alert-danger alert-dismissable" id="error-productos-socio">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error: </strong><span class="error-productos-socio"></span>
                  </div>
                  <div class="form-group">
                    <h3 class="texto text-center">NUEVO INCREMENTO</h3>
                  </div>
                  <div class="form-group">
                    <h3 class="texto">Seleccione los servicios</h3>
                    <h4 class="texto">SERVICIOS ACTUALES</h4>
                  </div>
                  <hr>
                  <div class="" id="producto_socio"></div>
                </div>
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="button" id="btnAgregarServicioSocio" class="btn btn-primary form-control">Agregar servicio</button>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="button" id="btnQuitarServicioSocio" class="btn btn-danger form-control">Quitar servicio</button>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group btnMama">
                      <button type="button" class="btn btn-success form-control btnAgregarMama">Agregar Promo mamá</button>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="divComentarioSocio" id="divComentarioSocio">
                        <label for="" class="texto">Observación <span class="requerido">*</span> </label>
                        <textarea class="form-control" name="comentarioSocio" id="comentarioSocio" rows="3" placeholder=""></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-8 container-subtotal" style="margin-top: 3rem">
                    <div class="subtotal" style="display: none;">
                      <p class="subtotal-price font-weight-bold">Total $UY <span id="subtotal-price-socio"></span></p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="" class="texto">IMPORTANTE: Los campos con <span class="requerido">*</span> son obligatorios</label>
                    </div>
                  </div>


                  <div class="row" solocomepa>
                    <div class="col-lg-6">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <input type="checkbox" id="ingresar_socio_comepa_incremento" ingresar_socio_comepa aria-label="Ingresar también a COMEPA">
                        </span>
                        <label for="ingresar_socio_comepa_incremento" class="form-control">Ingresar también a COMEPA</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer-venta">
                  <button type="button" id="btnAtras4" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguiente4" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                </div>
              </div>
              <!-- fin paso uno (socio) -->
              <!-- div validacion tipo de pago -->
              <div class="centrado" id="validacion_medio_pago">
                <nav area-label="Page navigation example">
                  <ul class="paginacion d-flex justify-content-center modal-3">
                    <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">2</a></li>
                    <li class="page-item activo"> <a href="#" class="item-paginacion">3</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                  </ul>
                </nav>
                <div class="alert alert-danger alert-dismissable" id="error-metodo-pago" style="display: none;">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error: </strong><span class="error-metodo-pago"></span>
                </div>
                <div class="form-group">
                  <h3 class="texto">Método de pago</h3>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="" class="texto">Método de pago</label>
                      <select id="medio_pago" name="medio_pago" class="custom-select form-control">
                        <option value="" selected>- Seleccione -</option>

                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group" id="divConvenios" style="display: none;">
                      <label for="" class="texto">Seleccione un medio</label>
                      <select id="convenios" name="convenios" class="custom-select form-control">
                        <option value="" selected>- Seleccione -</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="convenio_especial" class="texto">Convenios especiales disponibles:</label>
                      <select id="convenio_especial" name="convenio_especial" class="custom-select form-control">
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row" id="divDatosConvenio" style="display: none;">
                  <div class="alert alert-danger alert-dismissable" id="error-convenio">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error: </strong><span class="error-convenio"></span>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <h3 class="texto">Complete los datos del titular</h3>
                    </div>
                  </div>
                  <div class="form-group" id="divDatosConvenio">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Cédula del titular del convenio <span class="requerido">*</span></label>
                        <input type="text" pattern="\d*" min="1" maxlength="8" id="cedTitConvenio" class="solo_numeros form-control" data-checkout="docNumber" oninput="maxLengthCheck(this)">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="from-group">
                        <label for="" class="texto">Nombre del titular del convenio <span class="requerido">*</span></label>
                        <input type="text" maxlength="200" id="nomTitConvenio" class="form-control solo_letras" data-checkout="cardholderName">
                      </div>
                    </div>
                  </div>

                </div>
                <div class="modal-footer-venta">
                  <button type="button" id="btnAtrasVal" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguienteVal" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>

                </div>
              </div>
              <!-- fin div validacion tipo de pago  -->
              <!-- div paso tres -->
              <div class="centrado" id="pasotres" style="display:none">
                <div class="texto">
                  <nav area-label="Page navigation example">
                    <ul class="paginacion d-flex justify-content-center modal-3">
                      <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">2</a></li>
                      <li class="page-item activo"> <a href="#" class="item-paginacion">3</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                    </ul>
                  </nav>
                  <div class="alert alert-danger alert-dismissable" id="error-pago">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error: </strong><span class="error-pago"></span>
                  </div>
                  <div class="form-group">
                    <h3 class="texto">Complete los datos de la tarjeta</h3>
                  </div>
                  <div class="form-group">
                    <img alt="alt" src="img/visa.png " style="width:50px;">
                    <img alt="alt" src="img/mastercard.png " style="width:50px;">
                    <img alt="alt" src="img/oca.png " style="width:50px;">
                    <img alt="alt" src="img/lider.png " style="width:50px;">
                    <img alt="alt" src="img/creditel.jpg " style="width:50px;">
                  </div>
                  <div class="form-group">
                    <span style="font-size:20px">Total a pagar: <span class="text-bold" style="color:#407cdd" id="spanPrecio">$ </span></span>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Número de tarjeta <span class="requerido">*</span></label>
                        <input type="text" pattern="\d*" min="1" maxlength="16" id="numTar" class="solo_numeros form-control" data-checkout="cardNumber" oninput="maxLengthCheck(this)">
                        <input type="hidden" id="payment_method_id" name="payment_method_id">
                        <input type="hidden" id="is_mercadopago" name="is_mercadopago">
                      </div>
                    </div>

                    <!-- IMAGEN DE LA TARJETA INGRESADA -->
                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="" class="texto"></label>
                        <br>
                        <span><img alt="alt" src="" style="width:65px; margin-left:5px;" id="img-tipo_tarjeta"></span>
                      </div>
                    </div>

                    <div class="col-md-1" style="display:none" id="seccionCuotas">
                      <!--//seba-->
                      <div class="form-group">
                        <label for="" class="texto">Cuotas <span class="requerido">*</span></label>
                        <select id="cuotas" name="cuotas" class="custom-select form-control">
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3" style="display:none">
                      <div class="form-group">
                        <label for="" class="texto">CVV (tres dígitos de atrás)</label>
                        <input type="password" pattern="\d*" maxlength="4" id="cvv" class="solo_numeros form-control" style="float:left;" data-checkout="securityCooninput=" maxLengthCheck(this)">
                        <input type="hidden" name="tipo_tarjeta" id="tipo_tarjeta">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Banco emisor <span class="requerido">*</span></label>
                        <select id="bancos" name="bancos" class="custom-select form-control">
                          <option value="0" selected>Seleccione el banco</option>
                        </select>
                        <input type="hidden" id="banco" name="banco">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Cédula del titular de la tarjeta <span class="requerido">*</span></label>
                        <input type="text" pattern="\d*" min="1" maxlength="8" id="cedTit" class="solo_numeros form-control" data-checkout="docNumber" oninput="maxLengthCheck(this)">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="from-group">
                        <label for="" class="texto">Nombre del titular de la tarjeta <span class="requerido">*</span></label>
                        <input type="text" maxlength="200" id="nomTit" class="form-control solo_letras" data-checkout="cardholderName">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label for="" class="texto">Vencimiento de la tarjeta <span class="requerido">*</span></label>
                      <div class="row">

                        <div class="from-group">
                          <div class="col-md-4">
                            <select id="mesVen" class="quinceporciento form-control" data-checkout="cardExpirationMonth">
                              <option value="0" selected disabled>Mes</option>
                              <option value="01">01</option>
                              <option value="02">02</option>
                              <option value="03">03</option>
                              <option value="04">04</option>
                              <option value="05">05</option>
                              <option value="06">06</option>
                              <option value="07">07</option>
                              <option value="08">08</option>
                              <option value="09">09</option>
                              <option value="10">10</option>
                              <option value="11">11</option>
                              <option value="12">12</option>
                            </select>
                          </div>
                          <div class="col-md-4">
                            <select id="anoVen" class="quinceporciento form-control" data-checkout="cardExpirationYear">
                              <option value="0" selected disabled>Año</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Correo electrónico del titular de la tarjeta </label>
                        <input type="email" maxlength="250" class="form-control" id="mailTit">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Celular del titular de la tarjeta <span class="requerido">*</span></label>
                        <input type="tel" pattern="\d*" maxlength="9" id="celTit" class="solo_numeros form-control">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Télefono del titular de la tarjeta </label>
                        <input type="tel" pattern="\d*" maxlength="8" id="telTit" class="solo_numeros form-control">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="" class="texto">IMPORTANTE: Los campos con <span class="requerido">*</span> son obligatorios</label>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="margensuperiorcelular"></div> -->
                  <input type="hidden" name="paymentMethodId" id="paymentMethodId" value="">
                  <input type="hidden" name="token" id="tokenValidador" value="">
                  <select id="docType" data-checkout="docType" style="display: none;">
                    <option value="CI" selected="true">CI</option>
                  </select>
                  <!-- <div style="height:100px;"></div> -->
                  <div class="modal-footer-venta">
                    <button type="button" id="btnAtras3" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                    <button type="button" id="btnAtrasInt4" class="btn btn-primary btn-lg float-left" style="display: none;">&larr; Atrás</button>
                    <button type="button" id="btnSiguiente3" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                    <button type="button" id="btnSiguienteInt4" class="btn btn-primary btn-lg float-right btnsiguiente" style="display: none;">Siguiente &rarr;</button>

                  </div>
                </div>
              </div>
              <!--fin div paso tres -->
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL VENTA -->

    <!-- MODAL CONFIRMACIÓN DE VENTA -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-confirmacion-venta">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Detalles de la venta</h2>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer" style="background-color: #fff;">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnConfirmarVenta">Aceptar</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnCancelarVenta">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL CONFIRMACIÓN DE VENTA -->

    <!-- MODAL DATOS CLIENTE -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-datos-cliente">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Confirmar datos del cliente para la afiliación</h2>
          </div>
          <div class="modal-body" id="datos_del_cliente"></div>
          <div class="modal-footer-venta">
            <button id="btnCancelarAfiliacion" type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            <button id="btnAfiliar" type="button" class="btn btn-primary">Afiliar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL DATOS CLIENTE -->

    <!-- MODAL VENTA DE GRUPO -->
    <div class="modal" tabindex="-1" role="dialog" id="modal-venta-grupo" style="overflow-y: scroll;">
      <div class="modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">X</span>
            </button>
            <h2 class="modal-title">Datos de la venta</h2>
          </div>
          <div class="modal-body">
            <form id="pay_grupo" name="pay" class="pay_grupo">
              <div class="alert alert-danger alert-dismissable" id="error-validacion-cedula-grupo" style="display: none;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error: </strong><span class="error-validacion-cedula-grupo"></span>
              </div>
              <!-- div validacion cedula -->
              <div class="centrado" id="validacion_cedula_grupo">
                <div class="form-group">
                  <h3 class="texto">Validar cédula de los integrantes del grupo</h3>
                </div>
                <div id="cedulas-integrantes">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Cédula <span class="requerido">*</span></label>
                        <input type="text" class="form-control solo_numeros valced_integrante" name="" required>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <div class="row form-group">
                    <div class="form-group">
                      <div class="col-md-2">
                        <button type="button" class="btn btn-success form-control" id="btnAgregarPersona">Agregar persona</button>
                      </div>
                      <div class="col-md-2">
                        <button type="button" class="btn btn-danger form-control" id="btnQuitarPersona">Quitar persona</button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group">
                      <div class="col-md-1">
                        <button type="button" class="btn btn-primary form-control" id="btnValidarCedulaGrupo">Validar</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- fin div validacion cedula -->
              <!-- div paso uno -->
              <div class="centrado" id="pasouno-grupo" style="display: none;">
                <nav area-label="Page navigation example">
                  <ul class="paginacion d-flex justify-content-center modal-3">
                    <li class="page-item activo"><a href="#" class="item-paginacion">1</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">2</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">3</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                  </ul>
                </nav>
                <div class="">
                  <h3 class="texto text-center" id="pasouno-title">NUEVA ALTA</h3>
                </div>
                <div class="">
                  <h3 class="texto">Complete los datos de los beneficiarios</h3>
                </div>
                <div id="datosintegrantes"></div>
                <div class="modal-footer-venta">
                  <button type="button" id="btnAtrasInt1" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguienteInt1" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                </div>
              </div>
              <!-- fin div paso uno -->
              <!-- div paso dos -->
              <div class="centrado" id="pasodos-grupo" style="display:none">
                <div class="texto" id="servicios-grupo">
                  <nav area-label="Page navigation example">
                    <ul class="paginacion d-flex justify-content-center modal-3">
                      <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                      <li class="page-item activo"> <a href="#" class="item-paginacion">2</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">3</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                    </ul>
                  </nav>
                  <!-- <div class="alert alert-danger alert-dismissable" id="error-productos-grupo">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error: </strong><span class="error-productos"></span>
                </div> -->
                  <div id="servicios_integrantes">
                    <div class="alert alert-danger alert-dismissable" id="error-servicios-integrantes" style="display: none; text-align: left; height: auto;">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <h4><strong>Errores: </strong></h4>
                      <div class="error-servicios-integrantes"></div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="" class="texto">IMPORTANTE: Los campos con <span class="requerido">*</span> son obligatorios</label>
                    </div>
                  </div>
                  <!-- SUBTOTAL GENERAL -->
                  <div class="col-md-4 col-md-offset-4 container-subtotal" style="margin-bottom: 2rem;">
                    <div class="subtotal" style="display: none;">
                      <p class="subtotal-price font-weight-bold">Total $UY: <span id="subtotal-price-grupo"></span></p>
                    </div>
                  </div>
                </div>

                <div class="modal-footer-venta">
                  <button type="button" id="btnAtrasInt2" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguienteInt2" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                </div>
              </div>
              <!-- fin div paso dos -->
              <!-- div validacion tipo de pago -->
              <div class="centrado" id="validacion_medio_pago_grupo" style="display: none;">
                <nav area-label="Page navigation example">
                  <ul class="paginacion d-flex justify-content-center modal-3">
                    <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">2</a></li>
                    <li class="page-item activo"> <a href="#" class="item-paginacion">3</a></li>
                    <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                  </ul>
                </nav>
                <div class="alert alert-danger alert-dismissable" id="error-metodo-pago" style="display: none;">
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error: </strong><span class="error-metodo-pago"></span>
                </div>
                <div class="form-group">
                  <h3 class="texto">Método de pago</h3>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="" class="texto">Seleccionar método de pago</label>
                      <select id="medio_pago_grupo" name="medio_pago" class="custom-select form-control medio_pago_grupo">
                        <option value="" selected>- Seleccione -</option>
                        <option value="2">Centralizado</option>
                        <!--<option value="3">Convenio</option>-->
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group" id="divConveniosGrupo" style="display: none;">
                      <label for="" class="texto">Seleccione un medio</label>
                      <select id="conveniosGrupo" name="convenios" class="custom-select form-control convenios">
                        <option value="" selected>- Seleccione -</option>
                        <option value="20" data-idmetodo="2">TARJETA DE CREDITO</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row" id="divDatosConvenioGrupo" style="display: none;">
                  <div class="alert alert-danger alert-dismissable" id="error-convenio">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error: </strong><span class="error-convenio"></span>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <h3 class="texto">Complete los datos del titular</h3>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Cédula del titular del convenio <span class="requerido">*</span></label>
                        <input type="text" pattern="\d*" min="1" maxlength="8" id="cedTitConvenioGrupo" class="solo_numeros form-control" data-checkout="docNumber" oninput="maxLengthCheck(this)">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="from-group">
                        <label for="" class="texto">Nombre del titular del convenio <span class="requerido">*</span></label>
                        <input type="text" maxlength="200" id="nomTitConvenioGrupo" class="form-control solo_letras" data-checkout="cardholderName">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer-venta">
                  <button type="button" id="btnAtrasInt3" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                  <button type="button" id="btnSiguienteInt3" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                </div>
              </div>
              <!-- fin div validacion tipo de pago  -->
              <div class="centrado" id="pasocuatro-grupo" style="display:none">
                <div class="texto">
                  <nav area-label="Page navigation example">
                    <ul class="paginacion d-flex justify-content-center modal-3">
                      <li class="page-item"><a href="#" class="item-paginacion">1</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">2</a></li>
                      <li class="page-item activo"> <a href="#" class="item-paginacion">3</a></li>
                      <li class="page-item"> <a href="#" class="item-paginacion">4</a></li>
                    </ul>
                  </nav>
                  <div class="alert alert-danger alert-dismissable" id="error-pago-grupo" style="display: block;">
                    <button type="button" class="" data-dismiss="alert"></button>
                    <strong>Error: </strong><span class="error-pago-grupo"></span>
                  </div>
                  <div class="form-group">
                    <h3 class="texto">Complete los datos de la tarjeta</h3>
                  </div>
                  <div class="form-group">
                    <img alt="alt" src="img/visa.png " style="width:50px;">
                    <img alt="alt" src="img/mastercard.png " style="width:50px;">
                    <img alt="alt" src="img/oca.png " style="width:50px;">
                    <img alt="alt" src="img/lider.png " style="width:50px;">
                    <img alt="alt" src="img/creditel.jpg " style="width:50px;">
                  </div>
                  <div class="form-group">
                    <span style="font-size:20px">Total a pagar: <span class="text-bold" style="color:#407cdd" id="spanPrecio-grupo">$ </span></span>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Número de tarjeta <span class="requerido">*</span></label>
                        <input type="text" pattern="\d*" min="1" maxlength="16" id="numTarGrupo" class="solo_numeros form-control numTar" data-checkout="cardNumber" oninput="maxLengthCheck(this)">
                        <input type="hidden" id="payment_method_id_grupo" name="payment_method_id">
                        <input type="hidden" id="is_mercadopago" name="is_mercadopago">
                      </div>
                    </div>
                    <!-- IMAGEN DE LA TARJETA INGRESADA -->
                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="" class="texto"></label>
                        <br>
                        <span><img alt="alt" src="" style="width:65px; margin-left:5px;" id="img-tipo_tarjeta"></span>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">CVV (tres dígitos de atrás)</label>
                        <input type="password" pattern="\d*" maxlength="4" id="cvvGrupo" class="solo_numeros form-control" style="float:left;" data-checkout="securityCooninput=" maxLengthCheck(this)">
                        <input type="hidden" name="tipo_tarjeta_grupo" id="tipo_tarjeta_grupo">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto">Banco emisor <span class="requerido">*</span></label>
                        <select id="bancos" name="bancos" class="custom-select form-control bancos">
                          <option value="0" selected>Seleccione el banco</option>
                        </select>
                        <input type="hidden" id="banco" class="banco" name="banco">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Cédula del titular de la tarjeta <span class="requerido">*</span></label>
                        <input type="text" pattern="\d*" min="1" maxlength="8" id="cedTitGrupo" class="solo_numeros form-control" data-checkout="docNumber" oninput="maxLengthCheck(this)">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="from-group">
                        <label for="" class="texto">Nombre del titular de la tarjeta <span class="requerido">*</span></label>
                        <input type="text" maxlength="200" id="nomTitGrupo" class="form-control solo_letras" data-checkout="cardholderName">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label for="" class="texto">Vencimiento de la tarjeta <span class="requerido">*</span></label>
                      <div class="row">

                        <div class="from-group">
                          <div class="col-md-4">
                            <select id="mesVenGrupo" class="quinceporciento form-control" data-checkout="cardExpirationMonth">
                              <option value="0" selected disabled>Mes</option>
                              <option value="01">01</option>
                              <option value="02">02</option>
                              <option value="03">03</option>
                              <option value="04">04</option>
                              <option value="05">05</option>
                              <option value="06">06</option>
                              <option value="07">07</option>
                              <option value="08">08</option>
                              <option value="09">09</option>
                              <option value="10">10</option>
                              <option value="11">11</option>
                              <option value="12">12</option>
                            </select>
                          </div>
                          <div class="col-md-4">
                            <select id="anoVenGrupo" class="quinceporciento form-control anoVen" data-checkout="cardExpirationYear">
                              <option value="0" selected disabled>Año</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Correo electrónico del titular de la tarjeta </label>
                        <input type="email" maxlength="250" class="form-control" id="mailTitGrupo">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Celular del titular de la tarjeta <span class="requerido">*</span></label>
                        <input type="tel" pattern="\d*" maxlength="9" id="celTitGrupo" class="solo_numeros form-control">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto">Télefono del titular de la tarjeta </label>
                        <input type="tel" pattern="\d*" maxlength="8" id="telTitGrupo" class="solo_numeros form-control">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="" class="texto">IMPORTANTE: Los campos con <span class="requerido">*</span> son obligatorios</label>
                      </div>
                    </div>
                  </div>
                  <!-- <div class="margensuperiorcelular"></div> -->
                  <input type="hidden" name="paymentMethodIdGrupo" id="paymentMethodIdGrupo" value="">
                  <input type="hidden" name="token" id="tokenValidadorGrupo" value="">
                  <select id="docType" data-checkout="docType" style="display: none;">
                    <option value="CI" selected="true">CI</option>
                  </select>
                  <!-- <div style="height:100px;"></div> -->
                  <div class="modal-footer-venta">
                    <button type="button" id="btnAtrasInt4" class="btn btn-primary btn-lg float-left">&larr; Atrás</button>
                    <button type="button" id="btnMostrarConfirmacionVenta" class="btn btn-primary btn-lg float-right btnsiguiente">Siguiente &rarr;</button>
                  </div>
                </div>
              </div>
              <!--fin div paso tres -->
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL VENTA DE GRUPO -->

    <!-- MODAL CONFIRMACIÓN DE VENTA DEL GRUPO -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-confirmacion-venta-grupo">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Detalles de la venta</h2>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer" style="background-color: #fff;">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnConfirmarVentaGrupo">Aceptar</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnCancelarVentaGrupo">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL CONFIRMACIÓN DE VENTA -->

    <!-- MODAL BENEFICIARIOS -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-agregar-beneficiarios">
      <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Agregar beneficiarios</h2>
          </div>
          <div class="modal-body" id="datos_beneficiarios">
            <div class="alert alert-danger alert-dismissable" id="error-beneficiarios">
              <strong>Error: </strong><span class="error-beneficiarios"></span>
            </div>
            <form action="" id="beneficiarios_form">
              <div class="row beneficiario" id="beneficiario1">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control nombre_ben solo_letras" value="" name="">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Cédula</label>
                    <input type="text" class="form-control cedula_ben solo_numeros" value="" name="">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Teléfono</label>
                    <input type="text" class="form-control telefono_ben solo_numeros" value="" name="">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="">Fecha de nacimiento</label>
                    <input type="text" class="form-control fn_beneficiario fechan_ben" value="" name="">
                  </div>
                </div>
              </div>
              <div class="beneficiarios-extra"></div>
            </form>
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                  <button type="button" id="btnAddBen" class="btn btn-primary form-control">Agregar</button>
                </div>

              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <button type="button" id="btnDelBen" class="btn btn-danger form-control">Quitar</button>
                </div>
              </div>
            </div>

          </div>
          <div class="modal-footer-venta">
            <button id="btnCancelarBeneficiarios" type="button" class="btn btn-secondary mainbutton" data-dismiss="modal">Cancelar</button>
            <button id="btnGuardarBeneficiarios" type="button" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL BENEFICIARIOS -->

    <!-- MODAL BENEFICIARIO OMT -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-agregar-omt">
      <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Datos del beneficiario OMT</h2>
          </div>
          <div class="modal-body" id="datos_omt">
            <div class="alert alert-danger alert-dismissable" id="error-benomt">
              <strong>Error: </strong><span class="error-benomt"></span>
            </div>
            <form action="" id="beneficiario_omt">
              <div class="row" id="omtBen">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control  solo_letras" value="" name="" id="nombre_omtben">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Cédula</label>
                    <input type="text" class="form-control solo_numeros" value="" name="" id="cedula_omtben">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Teléfono</label>
                    <input type="text" class="form-control solo_numeros" value="" name="" id="telefono_omtben">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="">Fecha de nacimiento</label>
                    <input type="text" class="form-control fn_beneficiario" value="" name="" id="fechan_omtben">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="" class="texto">Calle <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="calle_omtben" id="calle_omtben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-froup">
                    <label class="texto">Elige una opción <span class="requerido">*</span></label>
                    <fieldset id="puertaOmtBen">
                      <div class="radio">
                        <label>
                          <input type="radio" id="puertaCheckedOmt" name="checkPuerta" class="checkPuertaOmt" value="0">
                          Puerta
                        </label>
                        <label>
                          <input type="radio" id="solarCheckedOmt" name="checkPuerta" class="checkPuertaOmt" value="1">
                          Solar/manzana
                        </label>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="col-md-1" style="display:none;" id="divPuertaOmt">
                  <div class="form-group">
                    <label for="" class="texto">Puerta <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles solo_numeros input-error" limitecaracteres="4" maxlength="4" name="puerta_omtben" id="puerta_omtben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-1" style="display:none;" id="divSolarOmt">
                  <div class="form-group">
                    <label for="" class="texto">Solar <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="solar_omtben" id="solar_omtben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-1" style="display:none;" id="divManzanaOmt">
                  <div class="form-group">
                    <label for="" class="texto">Manzana <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="manzana_omtben" id="manzana_omtben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="" class="texto">Esquina <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="esquina_omtben" id="esquina_omtben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="form-group">
                    <label for="" class="texto">Apartamento</label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="4" name="apto_omtben" id="apto_omtben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="" class="texto">Referencia <span class="requerido">*</span></label>
                    <input type="text" class="form-control input-error" name="referencia_omtben" id="referencia_omtben" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Departamento</label>
                    <select name="depOmtBen" id="depOmtBen" class="custom-select form-control input-error">
                      <option value="">- Seleccione -</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">localidad</label>
                    <select name="locOmtBen" id="locOmtBen" class="custom-select form-control input-error">
                      <option value="">- Seleccione -</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="" class="texto"><span class="requerido">NOTA:</span> el único medio de pago para éste servicio es tarjeta de crédito</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer-venta">
            <input type="hidden" value="">
            <button id="btnCancelarOmt" type="button" class="btn btn-secondary" style=" background:#666;" data-dismiss="modal">Cancelar</button>
            <button id="btnGuardarOmtBen" type="button" class="btn btn-primary">Guardar</button>
            <button id="btnEliminarOmt" type="button" class="btn btn-danger" style="display: none;">Eliminar beneficiario</button>
          </div>
        </div>
      </div>
    </div>
    <!-- FIN MODAL BENEFICIARIO OMT -->

    <!-- #region PROMO MES DE MAMÁ -->
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-agregar-mama">
      <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Datos de la madre</h2>
          </div>
          <div class="modal-body" id="datos_mama">
            <div class="alert alert-danger alert-dismissable" id="error-benmama">
              <strong>Error: </strong><span class="error-benmama"></span>
            </div>
            <form action="" id="beneficiario_mama">
              <div class="row" id="mamaBen">
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="">Nombre <span class="requerido">*</span></label>
                    <input type="text" class="form-control solo_letras" id="nombre_mamaben">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="">Cédula <span class="requerido">*</span></label>
                    <input type="text" class="form-control solo_numeros" id="cedula_mamaben">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="">Teléfono <span class="requerido">*</span></label>
                    <input type="text" class="form-control solo_numeros" id="telefono_mamaben">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Fecha de nacimiento <span class="requerido">*</span></label>
                    <input type="text" class="form-control fn_beneficiario" id="fechan_mamaben">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="" class="texto">Calle <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="calle_mamaben" id="calle_mamaben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-froup">
                    <label class="texto">Elige una opción <span class="requerido">*</span></label>
                    <fieldset id="puertaMamaBen">
                      <div class="radio">
                        <label>
                          <input type="radio" id="puertaCheckedMama" name="checkPuerta" class="checkPuertaMama" value="0">
                          Puerta
                        </label>
                        <label>
                          <input type="radio" id="solarCheckedMama" name="checkPuerta" class="checkPuertaMama" value="1">
                          Solar/manzana
                        </label>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="col-md-1" style="display:none;" id="divPuertaMama">
                  <div class="form-group">
                    <label for="" class="texto">Puerta <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles solo_numeros input-error" limitecaracteres="4" maxlength="4" name="puerta_mamaben" id="puerta_mamaben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-1" style="display:none;" id="divSolarMama">
                  <div class="form-group">
                    <label for="" class="texto">Solar <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="solar_mamaben" id="solar_mamaben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-1" style="display:none;" id="divManzanaMama">
                  <div class="form-group">
                    <label for="" class="texto">Manzana <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="manzana_mamaben" id="manzana_mamaben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="" class="texto">Esquina <span class="requerido">*</span></label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="esquina_mamaben" id="esquina_mamaben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                  </div>
                </div>
                <div class="col-md-1">
                  <div class="form-group">
                    <label for="" class="texto">Apartamento</label>
                    <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="4" name="apto_mamaben" id="apto_mamaben" required>
                    <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="" class="texto">Referencia <span class="requerido">*</span></label>
                    <input type="text" class="form-control input-error" name="referencia_mamaben" id="referencia_mamaben" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">Departamento</label>
                    <select name="depMamaBen" id="depMamaBen" class="custom-select form-control input-error">
                      <option value="">- Seleccione -</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="">localidad</label>
                    <select name="locMamaBen" id="locMamaBen" class="custom-select form-control input-error">
                      <option value="">- Seleccione -</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="" class="texto"><span class="requerido">NOTA:</span> el único medio de pago para éste servicio es tarjeta de crédito</label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer-venta">
            <input type="hidden" value="">
            <button id="btnCancelarMama" type="button" class="btn btn-secondary" style=" background:#666;" data-dismiss="modal">Cancelar</button>
            <button id="btnGuardarMamaBen" type="button" class="btn btn-primary">Guardar</button>
            <button id="btnEliminarMama" type="button" class="btn btn-danger" style="display: none;">Eliminar beneficiario</button>
          </div>
        </div>
      </div>
    </div>
    <!-- #endregion -->

    <!-- MODAL REAGENDAR -->
    <div id="divReagendar" class="modal" style="display: none;">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close17">X</span>
          <h2> <img alt="alt" src="img/agendados.png" width="24px"> Reagendar</h2>
        </div>
        <div class="modal-body">
          <table width="100%" height="200px" border="0">
            <div id="divReagendar" style="width:100%;">
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/nombre.png"><br>Fecha</p><input maxlength="100" name="nueva_fecha" type="text" class="hvr-border-fade" style="width:70%" placeholder="Fecha" id="fechaReagendar" />
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/comentarios.png"><br>Observación</p><textarea id="obs" class="hvr-border-fade" name="obsRef" placeholder="Observacion" style="width:70%"></textarea>
                <input type="hidden" name="num_agendado" id="num_agendado" />
                <input type="button" class="mainbuttonreferido btn-primary" id="btnReagendar" value="Reagendar" onclick="reagendar();" style="position:absolute; bottom:12px; left:20px;" />
            </div>
          </table>
          <label id="lblErrorReagendar" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; padding-left:350px; font-size:15px;"></label>
          <hr style="border:solid 1px; color:#999; position:relative; bottom:30px;" />
        </div>

      </div>
    </div>

    <!-- MODAL DIV REFERIDO -->
    <div id="divReferido" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close6">X</span>
          <h2> <img alt="alt" src="img/telefonoreferido.png" width="24px"> Referido</h2>
        </div>
        <div class="modal-body">
          <table width="100%" height="200px" border="0">
            <div id="divReferido2" style="width:100%;">

              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/nombre.png"><br>Nombre</p><input maxlength="100" name="nomRef" type="text" class="hvr-border-fade" style="width:70%" placeholder="Nombre" id="nomRef" />

              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/numero.png"><br>Numero</p><input maxlength="9" name="numRef" type="text" class="hvr-border-fade solo_numeros" style="width:70%" placeholder="Numero" id="numRef" />

              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/comentarios.png"><br>Observación</p><textarea id="obsRef" class="hvr-border-fade" name="obsRef" placeholder="Observacion" style="width:70%"></textarea>

                <input type="button" class="mainbuttonreferido" id="btnAgregarReferido" value="Agregar Referido" onclick="referido()" style="position:absolute; bottom:12px; left:20px;" />

                <label id="lblErrorReferido" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:200px; font-size:15px;"></label>

            </div>
          </table>
          <hr style="border:solid 1px; color:#999; position:relative; bottom:30px;" />
        </div>

      </div>
    </div>

    <!-- MODAL DIV SUMA Y GANA -->
    <div id="divSumaygana" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close9">X</span>
          <h2> <img alt="alt" src="img/telefonojugar.png" width="24px"> Desafio</h2>
        </div>
        <div class="modal-body">
          <table width="100%" height="200px" border="0">
            <div id="divSumaygana2" style="width:100%;">
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top;">
                <p><img alt="alt" src="img/cedula.png"><br>Cedula</p><input maxlength="8" name="cedSum" type="text" class="hvr-border-fade solo_numeros" style="width:50%" placeholder="Cedula" id="cedSum" />
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/numero.png"><br>Telefono/Celular</p><input maxlength="9" name="telSum" type="text" class="hvr-border-fade solo_numeros" style="width:50%" placeholder="Telefono/Celular" id="telSum" />
                <input type="button" class="mainbuttonsumaygana" id="btnAgregarSumayGana" value="Agregar Posible Participante" onclick="sumaygana()" style="position:absolute; bottom:12px; left:20px;" />

                <label id="lblErrorSumayGana" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:300px; font-size:15px;"></label>

            </div>
          </table>
          <hr style="border:solid 1px; color:#999; position:relative; bottom:30px;" />
        </div>

      </div>
    </div>

    <!-- MODAL DIV VER SUMA Y GANA GANANCIAS -->
    <div id="divVerSumayGana" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close10">X</span>
          <h2> <img alt="alt" src="img/sumaygana.png" width="36px"> Resumen Desafio </h2>
        </div>
        <div class="modal-body">
          <span style="font-size: 20px;">Su Saldo Acumulado es: <span id="saldo_vendedor" style="font-weight: bold;font-size: 25px;color: green;">$0</span></span>
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla4" cellpadding="0" cellspacing="0" border="0" class="display">
              <thead>
                <tr>
                  <th width="58" align="left">Cedula Jugador</th>
                  <th width="58" align="left">Telefono</th>
                  <th width="154" align="left">Monto</th>
                  <th width="63" align="left">Fecha</th>
                </tr>
              </thead>
            </table>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL DIV VER SUMA Y GANA REFERIDOS -->
    <div id="divRefSumayGana" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close11">X</span>
          <h2> <img alt="alt" src="img/sumaygana.png" width="36px"> Referidos Desafio </h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla5" cellpadding="0" cellspacing="0" border="0" class="display">
              <thead>
                <tr>
                  <th width="58" align="left">Cedula</th>
                  <th width="58" align="left">Telefono</th>
                  <th width="154" align="left">Vencido</th>
                  <th width="63" align="left">Fecha</th>
                </tr>
              </thead>
            </table>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL DIV REFERIDO -->
    <div id="divReferidoCuaderno" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close13">X</span>
          <h2> <img alt="alt" src="img/vendidosrojo.png" width="36px"> Referido Cuaderno</h2>
        </div>
        <div class="modal-body">
          <table width="100%" height="200px" border="0">
            <div id="divReferido2" style="width:100%;">

              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/nombre.png"><br>Nombre</p><input maxlength="100" name="nomRefCuaderno" type="text" class="hvr-border-fade" style="width:70%" placeholder="Nombre" id="nomRefCuaderno" />

              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/numero.png"><br>Numero</p><input maxlength="9" name="numRefCuaderno" type="text" class="hvr-border-fade solo_numeros" style="width:70%" placeholder="Numero" id="numRefCuaderno" />

              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top">
                <p><img alt="alt" src="img/comentarios.png"><br>Observación</p><textarea id="obsRefCuaderno" class="hvr-border-fade" name="obsRefCuaderno" placeholder="Observacion" style="width:70%"></textarea>

                <input type="button" class="mainbuttonreferido" id="btnAgregarReferido" value="Agregar Referido" onclick="referidoCuaderno()" style="position:absolute; bottom:12px; left:20px;" />

                <label id="lblErrorReferidoCuaderno" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:200px; font-size:15px;"></label>

            </div>
          </table>
          <hr style="border:solid 1px; color:#999; position:relative; bottom:30px;" />
        </div>
      </div>
    </div>

    <!-- MODAL DIV VER REFERIDOS PENDIENTES-->
    <div id="divVerReferidosPendientes" class="modal">

      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close14">X</span>
          <h2> <img alt="alt" src="img/referidoscuaderno.png" width="36px"> Referidos Cuaderno Pendientes</h2>
        </div>
        <div class="modal-body">
          <table width="80%" height="auto" border="0" style="margin:auto">
            <table id="Jtabla7" cellpadding="0" cellspacing="0" border="0" class="display">
              <thead>
                <tr>
                  <th width="58" align="left">Numero</th>
                  <th width="108" align="left">Nombre</th>
                  <th width="124" align="left">Fecha</th>
                  <th width="241" align="left">Observacion</th>
                  <th width="241" align="left">Traspasar</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </table>
        </div>
        <br>
        <label id="lblErrorReferidosPendientes" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:350px; font-size:15px;"></label>
      </div>
    </div>

    <!-- MODAL ANOTACIONES CUADERNO-->
    <div id="divAnotacionesCuaderno" class="modal">
      <div class="modal-content scrolleable">
        <div class="modal-header">
          <span class="close" id="close15">X</span>
          <h2> <img alt="alt" src="img/cuadernoicon.png" width="36px"> Anotaciones Cuaderno</h2>
        </div>
        <div class="modal-body fondocuaderno">
          <table style="width:80%; height:120px; border:0; margin-left:5%;">
            <tr>
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top; width:40%;">
                <p><img alt="alt" src="img/fecha.png"><br>Fecha</p><input maxlength="100" name="fechaAnotacion" type="text" class="hvr-border-fade" style="width:70%" placeholder="Fecha" id="fechaAnotacion" />
              </td>
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top; width:40%;">
                <p><img alt="alt" src="img/buscar.png"><br>Buscar</p><input maxlength="100" name="buscarAnotacion" type="text" class="hvr-border-fade" style="width:70%" placeholder="Buscar" id="buscarAnotacion" />
              </td>
              <td style="text-align:left; padding-bottom:10px; vertical-align:bottom; width:10%;"><input type="button" class="mainbuttonvendido" id="btnBuscarAnotacion" value="Buscar" onclick="listarAnotaciones($('#fechaAnotacion').val(),$('#buscarAnotacion').val())" style="width:100%;"></td>
            </tr>
          </table>
          <table style="width:100%; height:100px; border:0">
            <tr>
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top;"> <img alt="alt" src="img/comentariosanteriores.png"><br>Anotaciones Anteriores<br><br><textarea rows="5" cols="20" id="anotacionesAnteriores" class="hvr-border-fade" style="width:80%; max-width:80%;background-color:#ece9e8" name="anotacionesAnteriores" placeholder="Anotaciones Anteriores" readonly></textarea></td>
            </tr>
            <tr><br>
              <td style="text-align:center; padding-bottom:10px; vertical-align:text-top"> <img alt="alt" src="img/comentarios.png"><br>Ingrese Anotacion<br><br><textarea rows="4" cols="50" id="anotacionActual" class="hvr-border-fade" style="width:80%; max-width:80%" name="anotacionActual" placeholder="Ingrese Anotacion"></textarea></td>
            </tr>
            <tr>
              <td style="width:100%; text-align:center; margin-top:10px; ">
                <hr style="width:80%; margin-left:10%;"><input type="button" class="agendarbutton" id="btnAgendar" value="Agregar Anotacion" onclick="agregarAnotacion()">
              </td>
            </tr>
          </table>
        </div>
        <br>
        <label id="lblErrorAnotacionesCuaderno" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:10%; font-size:15px;"></label>
      </div>
    </div>

    <!-- MODAL CHEQUEAR BAJA-->
    <div id="divChequearBaja" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close" id="close16">X</span>
          <h2> <img alt="alt" src="img/bajasocioicon.png" width="36px"> Chequear Baja</h2>
        </div><br>
        <div class="semaforo" id="semaforo">
          <p id="txtResultadoBaja"></p>
        </div><br>
        <table style="width:100%; height:100px; border:0">
          <tr>
            <td style="text-align:center; padding-bottom:10px; vertical-align:text-top;">
              <p><img alt="alt" src="img/cedula.png"><br>Cedula</p><input maxlength="8" name="cedBaja" type="text" class="hvr-border-fade solo_numeros" style="width:30%" placeholder="Cedula" id="cedBaja" />
          </tr>
          <tr>
            <td><input type="button" class="mainbuttonnollamar" id="btnBuscarBaja" value="Buscar" onclick="buscarBaja()" style="bottom:12px; left:48%;margin-top:10px;" /></td>
          </tr>
        </table>
        <br><br>
        <label id="lblErrorBuscarBaja" style="color:red; width:100%; position:absolute; bottom:10px; text-align:left; left:46%; font-size:15px;"></label>
      </div>
    </div>


    <div style="position:absolute; right:35px; bottom:35px; color:#000; text-align:center; " class="hvr-bounce-in">
      <a href="manual.pdf" target="_blank" style="text-decoration:none;"><img alt="alt" src="img/manualdelusuario2.png" width="100px"><br /></a>
    </div>
  </form>

  <!-- #region Modals -->

  <!-- MODAL PAGO COMEPA-->
  <div id="modal_pagoComepa" class="modal">

    <div class="modal-content">

      <div class="modal-header">
        <span style="float: right;cursor: pointer;" onclick="$('#modal_pagoComepa').modal('hide')">X</span>
        <h2> <img alt="alt" src="img/bajasocioicon.png" width="36px"> Guardar datos COMEPA</h2>
      </div>

      <div class="modal-body">
        <form id="modal_pagoComepa_form" style="display: grid;">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="modal_pagoComepa_nombre">Nombre completo <span class="requerido">*</span></label>
              <input class="form-control" id="modal_pagoComepa_nombre" aria-describedby="nombre" placeholder="Enrique Juarez">
            </div>
            <div class="form-group col-md-2">
              <label for="modal_pagoComepa_cedula">Cédula <span class="requerido">*</span></label>
              <input type="tel" class="form-control" id="modal_pagoComepa_cedula" aria-describedby="cedula" placeholder="61636367" maxlength="8">
            </div>
            <div class="form-group col-md-2">
              <label for="modal_pagoComepa_fechaNacimiento">Nacimiento <span class="requerido">*</span></label>
              <input type="date" class="form-control" id="modal_pagoComepa_fechaNacimiento" aria-describedby="fechaNacimiento" placeholder="23/09/1995">
            </div>
            <div class="form-group col-md-2">
              <label for="modal_pagoComepa_telefono">Teléfono <span class="requerido"></span></label>
              <input type="tel" class="form-control" id="modal_pagoComepa_telefono" aria-describedby="telefono" placeholder="23452345" maxlength="8">
            </div>
            <div class="form-group col-md-2">
              <label for="modal_pagoComepa_celular">Celular <span class="requerido"></span></label>
              <input type="tel" class="form-control" id="modal_pagoComepa_celular" aria-describedby="celular" placeholder="090234523" maxlength="9">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="modal_pagoComepa_direccion">Dirección <span class="requerido">*</span></label>
              <input class="form-control" id="modal_pagoComepa_direccion" aria-describedby="direccion" placeholder="Dr. Martín C. Martínez 2461 esq Amézaga">
            </div>
            <div class="form-group col-md-6">
              <label for="modal_pagoComepa_observacion">Observación <span class="requerido"></span></label>
              <input class="form-control" id="modal_pagoComepa_observacion" aria-describedby="observacion" placeholder="La persona prefiere que la llamen a las 14">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="modal_pagoComepa_metodo_pago">Forma de pago <span class="requerido">*</span></label>
              <select class="form-control" id="modal_pagoComepa_metodo_pago">
                <option selected disabled>Seleccione una opción</option>
                <option>Cajas COMEPA</option>
                <option>Tarjeta de débito/crédito</option>
                <option>Débito BBVA (tramitar en banco)</option>
              </select>
            </div>
            <div class="form-group col-md-6" id="modal_pagoComepa_cobrador_div">
              <label for="modal_pagoComepa_caja">Cajas COMEPA disponibles <span class="requerido">*</span></label>
              <select class="form-control" id="modal_pagoComepa_caja">
                <option selected disabled>Seleccione una opción</option>
                <option>Cons. Centralizados</option>
                <option>Zona Norte</option>
                <option>Zona Oeste</option>
                <option>Pol. Guichón</option>
                <option>Pol. Quebracho</option>
              </select>
            </div>
          </div>

          <div id="modal_pagoComepa_tarjeta_datos" style="display: grid;">
            <h2>Datos tarjeta</h2>
            <div class="form-row">
              <div class="form-group col-md-2">
                <label for="modal_pagoComepa_tarjeta_emisor">Emisor <span class="requerido">*</span></label>
                <select class="form-control" id="modal_pagoComepa_tarjeta_emisor">
                  <option selected disabled>Seleccione una opción</option>
                  <option>VISA</option>
                  <option>CABAL</option>
                  <option>OCA</option>
                  <option>MASTER</option>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="modal_pagoComepa_tarjeta_nombre">Nombre <span class="requerido">*</span></label>
                <input class="form-control" id="modal_pagoComepa_tarjeta_nombre" aria-describedby="nombre" placeholder="Enrique Juarez">
              </div>
              <div class="form-group col-md-2">
                <label for="modal_pagoComepa_tarjeta_documento">Documento <span class="requerido">*</span></label>
                <input class="form-control" id="modal_pagoComepa_tarjeta_documento" aria-describedby="documento" placeholder="61636367" maxlength="8">
              </div>
              <div class="form-group col-md-2">
                <label for="modal_pagoComepa_tarjeta_numero">Número <span class="requerido">*</span></label>
                <input class="form-control" id="modal_pagoComepa_tarjeta_numero" aria-describedby="numero" placeholder="4321432143214321" maxlength="16">
              </div>
              <div class="form-group col-md-2">
                <label for="modal_pagoComepa_tarjeta_vencimiento_mes">Mes vcto. <span class="requerido">*</span></label>
                <input type="number" class="form-control" id="modal_pagoComepa_tarjeta_vencimiento_mes" aria-describedby="vencimiento" placeholder="04" min="01" max="12">
              </div>
              <div class="form-group col-md-2">
                <label for="modal_pagoComepa_tarjeta_vencimiento_ano">Año vcto. <span class="requerido">*</span></label>
                <input type="number" class="form-control" id="modal_pagoComepa_tarjeta_vencimiento_ano" aria-describedby="vencimiento" placeholder="2025" min="2022">
              </div>
            </div>
          </div>

          <div class="form-row">
            <button type="button" class="btn btn-danger" onclick="$('#modal_pagoComepa').modal('hide')">Cerrar</button>
            <button type="submit" class="btn btn-primary">Registrar</button>
          </div>

        </form>
      </div>

    </div>
  </div>

  <!-- MODAL CREDIV -->
  <div class="modal" tabindex="-1" role="dialog" id="modalCrediv">
    <div class="" style="max-width: 900px; margin: 0 auto;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">X</span>
          </button>
          <h2 class="modal-title">Crediv</h2>
        </div>
        <div class="modal-body">

          <form id="modal_crediv_form" style="display: grid;">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="txt_cedula_crediv">Cédula: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_cedula_crediv" aria-describedby="cedula" placeholder="61636367" maxlength="8">
              </div>
              <div class="form-group col-md-4">
                <label for="txt_nombre_crediv">Nombre: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_nombre_crediv" aria-describedby="nombre" placeholder="Enrique Juarez">
              </div>
              <div class="form-group col-md-4">
                <label for="txt_telefono_crediv">Teléfono: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_telefono_crediv" aria-describedby="telefono" placeholder="23452345" maxlength="8">
              </div>
            </div>

            <div class="form-row">
              <button type="button" class="btn btn-danger" onclick='$("#modalCrediv").css("display", "none");'>Cerrar</button>
              <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- MODAL BILLETERA -->
  <div class="modal" tabindex="-1" role="dialog" id="modalBilletera">
    <div class="" style="max-width: 900px; margin: 0 auto;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">X</span>
          </button>
          <h2 class="modal-title">Registro billetera</h2>
        </div>
        <div class="modal-body">

          <form id="modal_billetera_form" style="display: grid;">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="txt_cedula_billetera">Cédula: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_cedula_billetera" aria-describedby="cedula" placeholder="61636367" maxlength="8">
              </div>
              <div class="form-group col-md-4">
                <label for="txt_nombre_billetera">Nombre completo: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_nombre_billetera" aria-describedby="nombre" placeholder="Enrique Juarez">
              </div>
              <div class="form-group col-md-4">
                <label for="txt_celular_billetera">Celular: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_celular_billetera" aria-describedby="celular" placeholder="099888777" maxlength="9">
              </div>
              <div class="form-group col-md-4">
                <label for="txt_celular_billetera2">Repita celular: <span class="requerido">*</span></label>
                <input class="form-control" id="txt_celular_billetera2" aria-describedby="celular" placeholder="099888777" maxlength="9">
              </div>
            </div>

            <div class="form-row">
              <button type="button" class="btn btn-danger" onclick='$("#modalBilletera").css("display", "none");'>Cerrar</button>
              <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
  <!-- #endregion -->

  <script>
    $(".close").click(() => $(".modal").hide());

    /////////////////MODAL ALERT\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal1 = document.getElementById('divAlert');
    var span1 = document.getElementById("close1");

    span1.onclick = function() {
      modal1.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal1) {
        modal1.style.display = "none";
      }
    }

    /////////////////MODAL 3\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal3 = document.getElementById('divNoLlamarMas');
    var btn3 = document.getElementById("btnNoLLamarMas");
    var span3 = document.getElementById("close3");

    btn3.onclick = function() {
      modal3.style.display = "block";
    }

    span3.onclick = function() {
      modal3.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal3) {
        modal3.style.display = "none";
      }
    }
    /////////////////MODAL 4\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal4 = document.getElementById('divVerAgendados');
    var btn4 = document.getElementById("btnVerAgendados");
    var span4 = document.getElementById("close4");

    btn4.onclick = function() {
      modal4.style.display = "block";
    }

    span4.onclick = function() {
      modal4.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal4) {
        modal4.style.display = "none";
      }
    }
    /////////////////MODAL 5\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal5 = document.getElementById('divAtendio');
    var btn5 = document.getElementById("btnAtendio");
    var span5 = document.getElementById("close5");

    btn5.onclick = function() {
      modal5.style.display = "block";
    }

    span5.onclick = function() {
      modal5.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal5) {
        modal5.style.display = "none";
      }
    }

    /////////////////MODAL 6\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal6 = document.getElementById('divReferido');
    var btn6 = document.getElementById("btnReferido");
    var span6 = document.getElementById("close6");

    btn6.onclick = function() {
      modal6.style.display = "block";
    }

    span6.onclick = function() {
      modal6.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal6) {
        modal6.style.display = "none";
      }
    }

    /////////////////MODAL 7\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal7 = document.getElementById('divVerReferidos');
    var btn7 = document.getElementById("btnVerReferidos");
    var span7 = document.getElementById("close7");

    btn7.onclick = function() {
      modal7.style.display = "block";
    }

    span7.onclick = function() {
      modal7.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal7) {
        modal7.style.display = "none";
      }
    }

    /////////////////MODAL 8\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal8 = document.getElementById('divVerVendidos');
    var btn8 = document.getElementById("btnVerVendidos");
    var span8 = document.getElementById("close8");

    btn8.onclick = function() {
      modal8.style.display = "block";
    }

    span8.onclick = function() {
      modal8.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal8) {
        modal8.style.display = "none";
      }
    }

    /////////////////MODAL 12\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal12 = document.getElementById('divVerReferidosCuaderno');
    var btn12 = document.getElementById("btnVerReferidosCuaderno");
    var span12 = document.getElementById("close12");

    btn12.onclick = function() {
      modal12.style.display = "block";
    }

    span12.onclick = function() {
      modal12.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal12) {
        modal12.style.display = "none";
      }
    }

    /////////////////MODAL 13\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal13 = document.getElementById('divReferidoCuaderno');
    var btn13 = document.getElementById("btnAgregarReferidosCuaderno");
    var span13 = document.getElementById("close13");

    btn13.onclick = function() {
      modal13.style.display = "block";
    }

    span13.onclick = function() {
      modal13.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal13) {
        modal13.style.display = "none";
      }
    }

    /////////////////MODAL 14\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal14 = document.getElementById('divVerReferidosPendientes');
    var btn14 = document.getElementById("btnVerReferidosCuadernoPendientes");
    var span14 = document.getElementById("close14");

    btn14.onclick = function() {
      modal14.style.display = "block";
    }

    span14.onclick = function() {
      modal14.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal14) {
        modal14.style.display = "none";
      }
    }

    /////////////////MODAL 15\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal15 = document.getElementById('divAnotacionesCuaderno');
    var btn15 = document.getElementById("btnAnotacionesCuaderno");
    var span15 = document.getElementById("close15");

    btn15.onclick = function() {
      modal15.style.display = "block";
    }

    span15.onclick = function() {
      modal15.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal15) {
        modal15.style.display = "none";
      }
    }

    /////////////////MODAL 16\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal16 = document.getElementById('divChequearBaja');
    var btn16 = document.getElementById("btnVerBajas");
    var span16 = document.getElementById("close16");

    btn16.onclick = function() {
      modal16.style.display = "block";
    }

    span16.onclick = function() {
      modal16.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal16) {
        modal16.style.display = "none";
      }
    }

    document.querySelector('#close17').addEventListener('click', () => {
      document.querySelector('#divReagendar').style.display = 'none';

    })

    /////////////////MODAL 17\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    var modal17 = document.getElementById('divVidaShop');
    var btn17 = document.getElementById("btnVidaShop");
    var span17 = document.getElementById("close17");

    btn17.onclick = function() {
      modal17.style.display = "block";
    }

    span17.onclick = function() {
      modal17.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal17) {
        modal17.style.display = "none";
      }
    }

    $.datetimepicker.setLocale('es');

    $('.fn_beneficiario').datetimepicker({
      format: 'Y-m-d',
      timepicker: false,
      minDate: '1900-01-01',
      startDate: '<?= $mayorDieciocho; ?>',
      defaultDate: '<?= $mayorDieciocho; ?>',
      maxDate: '<?= $mayorDieciocho; ?>',
    });

    $('#datetimepicker').keypress(function(event) {
      event.preventDefault();
    });
    $('#datetimepicker').datetimepicker({
      minDate: '<?= $fecha; ?>',
      startDate: '<?= $fecha; ?>',
      defaultDate: '<?= $fecha; ?>',
      maxDate: '<?= $final; ?>',
      allowTimes: ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
        '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
      ],
      format: 'Y-m-d H:i',
    });

    $('.fechas').keypress(function(event) {
      event.preventDefault();
    });
    $('.fechas').datetimepicker({
      startDate: '<?php echo $fecha; ?>',
      defaultDate: '<?php $fecha; ?>',
      maxDate: '<?php echo $fecha; ?>',
      format: 'Y-m-d',
      timepicker: false,
      ignoreReadonly: true
    });

    $('.fechasAgendado').keypress(function(event) {
      event.preventDefault();
    });
    $('.fechasAgendado').datetimepicker({
      startDate: '<?php echo $fecha; ?>',
      defaultDate: '<?php $fecha; ?>',
      maxDate: '<?php echo $fecha; ?>',
      format: 'Y-m-d',
      timepicker: false,
      ignoreReadonly: true
    });

    $('#fechaReagendar').keypress(function(event) {
      event.preventDefault();
    });
    $('#fechaReagendar').datetimepicker({
      minDate: '<?php echo $fecha; ?>',
      startDate: '<?php echo $fecha; ?>',
      maxDate: '<?php echo $final; ?>',
      defaultDate: '<?php $fecha; ?>',
      format: 'Y-m-d',
      timepicker: false,
      ignoreReadonly: true
    });

    $('#fechaAnotacion').keypress(function(event) {
      event.preventDefault();
    });
    $('#fechaAnotacion').datetimepicker({
      startDate: '<?php echo $fecha; ?>',
      defaultDate: '<?php echo $fecha; ?>',
      maxDate: '<?php echo $fecha; ?>',
      format: 'Y-m-d',
      timepicker: false,
      ignoreReadonly: true
    });
  </script>
  <script src="../cdns/js/llamada_automatica.js?v=20240820_1" type="text/javascript"></script>
</body>

</html>