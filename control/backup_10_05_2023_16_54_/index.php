<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta content="VidaUY" name="author">
  <meta content="text/html" http-equiv="content-type">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <!--
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css"> -->

  <!-- DATETIME PICKER -->
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../Css/jquery.datetimepicker.css" />
  <link rel="stylesheet" type="text/css" href="../build/jquery.datetimepicker.min.css" />
  <script src="../build/jquery.datetimepicker.full.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css"> -->

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <!-- DATETIME PICKER -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../Css/jquery.datetimepicker.css" />
  <link rel="stylesheet" type="text/css" href="../build/jquery.datetimepicker.min.css" />
  <script src="../build/jquery.datetimepicker.full.js"></script>
  <link rel="stylesheet" href="css/styles.css?v=1.2">
  <!-- CONFIRM -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

  <!-- SWEETALERT -->
  <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
  <script src="js/sweetalert.min.js"></script>


  <title>Control Comercial | Dashboard</title>
</head>

<body class="">
  <iframe id="iframeDePago" src="https://vida-apps.com/call_pagos/iframe/index.php" style="display: none;"></iframe>
  <div class="loader" id="loader">
    <img src="../img/loading.gif" id="loading" class="loading" />
  </div>
  <!-- HEADER -->
  <div class="container-fluid pt-3 navbar-light bg-light">
    <div class="row">
      <div class="col-6 col-sm-5 col-md-5 col-lg-4 col-xl-4">
        <nav class="navbar navbar-light navbar-toggleable-sm navbar-light bg-light">
          <!-- TOGGLE -->
          <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <!-- MENU -->
          <div class="navbar-collapse collapse flex-column mt-md-0 mt-4 pt-md-0 pt-4" id="nav">
            <ul class="navbar-nav">
              <li class="nav-item pt-2">
                <a href="" class="nav-link" id="btnDashBoard">Dashboard</a>
              </li>
              <li class="nav-item pt-2">
                <a href="" class="nav-link" id="btnHistory">Historial</a>
              </li>
              <li class="nav-item pt-2">
                <a href="" class="nav-link" id="btnAfiliaciones">Afiliaciones aprobadas</a>
              </li>
              <li class='nav-item pt-2' style="display: none;" id="op_control_abm">
                <a href='' class='nav-link' id='btnControlAbm'>Control ABM</a>
              </li>
              <li class='nav-item pt-2' style="display: none;" id="op_control_abm_piscina">
                <a href='' class='nav-link' id='btnControlAbmPiscina'>Control Piscina ABM</a>
              </li>
              <li class="nav-item pt-2">
                <a href="#!" class="nav-link" id="btnCloseSession">Cerrar sesión</a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
      <div class="col-6 col-sm-7 col-md-7 col-lg-7 col-xl-7">
        <h4 class="text-right">Bienvenid@ <span class="nav-username"></span></h4>
      </div>
    </div>
  </div>
  <!-- DASHBOARD -->
  <div class="container-fluid" style="margin: 2rem auto; padding: 2rem;" id="dashboard">
    <div class="row pt-3 pb-3">
      <div class="col">
        <h2>DASHBOARD</h2>
      </div>
    </div>
    <div class="row pt-3 pb-3" id="mis-rechazos" style="display: none;">
      <div class="col">
        <button class="btn btn-primary text-uppercase" id="btnMisRechazos">Ver mis rechazados</button>
      </div>
    </div>
    <div class="row pt-3 pb-3">
      <div class="col">
        <form action="" class="form-inline">
          <div class="form-group mxr-sm-3 mb-2">
            <select class="form-control" name="select-radio" id="select-filter-1">
              <option value="">RUTA</option>
            </select>
          </div>
          <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="select-sucursal" id="select-filter-2">
              <option value="">SUCURSAL</option>
            </select>
          </div>
          <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="select-metodopago" id="select-filter-3">
              <option value="">MÉTODO DE PAGO</option>
            </select>
          </div>
          <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="select-estado" id="select-filter-4">
              <option value="">ESTADO</option>
              <option value="Pendiente revisión">Pendiente de revisión</option>
              <option value="Pendiente morosidad">Pendiente Morosidad</option>
              <option value="En proceso de bienvenida">Pendiente Bienvenida</option>
              <option value="Pendiente Nicolas">Pendiente Nicolas</option>
              <option value="Pendiente de llamar">Pendiente de llamar</option>
              <option value="Aprobado por bienvenida">Aprobado Bienvenida</option>
              <option value="Aprobado morosidad">Aprobado Morosidad</option>
              <option value="Rechazo por bienvenida">Rechazado Bienvenida</option>
              <option value="Rechazado por morosidad">Rechazado Morosidad</option>
            </select>
          </div>
          <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="select-metodopago" id="select-filter-5">
              <option value="">TIPO DE AFILIACIÓN</option>
            </select>
          </div>
          <div class="form-group mx-sm-3 mb-2">
            <button id="btn-clear" class="btn btn-primary">Limpiar filtros</button>
          </div>
        </form>
      </div>
    </div>
    <div class="row pt-3 pb-3">
      <div class="col-md-2">
        <div class="form-group">
          <label for="desde">Fecha desde</label>
          <input type="datetime" class="form-control fechas" name="desde" id="desde">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="hasta">Fecha hasta</label>
          <input type="datetime" class="form-control fechas" name="hasta" id="hasta">
        </div>
      </div>
      <div class="form-group mx-sm-3 mb-2" style="margin-top: 2rem !important;">
        <div></div>
        <button id="btn-filtrar" class="btn btn-primary">Filtrar</button>
      </div>
    </div>
    <div class="row">
      <div class="radio_adelantado"></div><span>Radio Adelantado (Promo internados)</span>
      <div class="col">
        <table class="table table-strip display responsive nowrap" id="table-dashboard" style="width:100%">
          <thead>
            <tr>
              <th class="text-left">Nombre</th>
              <th class="text-left">Cédula</th>
              <th class="text-left">Radio</th>
              <th class="text-left">Ruta</th>
              <th class="text-left">Sucursal</th>
              <th class="text-left">Método de pago</th>
              <th class="text-left">Estado</th>
              <th class="text-left">Fecha de carga</th>
              <th class="text-left">Datos</th>
              <th class="text-left" id="thead-servicios"></th>
              <th class="text-left">Acción</th>
              <th class="text-left" id="thhead-ruta"></th>
              <th class="text-left">Observación</th>
              <th class="text-left">Historico</th>
              <th class="text-left"></th>
              <th class="text-left"></th>
              <th class="text-left"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- HISTORICO -->
  <div class="container-fluid" style="margin: 2rem auto; padding: 2rem; display: none;" id="history">
    <div class="row pt-3 pb-3">
      <div class="col">
        <h2>HISTORICO</h2>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <table class="table table-strip display responsive nowrap text-center" id="table-history" style="width:100%">
          <thead>
            <tr>
              <th class="">Usuario</th>
              <th class="">Nombre</th>
              <th class="">Cédula</th>
              <th class="">Estado</th>
              <th class="">Motivo de rechazo</th>
              <th class="">Observación</th>
              <th class="">Fecha</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- AFILIACIONES APROBADAS -->
  <div class="container-fluid" style="margin: 2rem auto; padding: 2rem; display: none;" id="afiliaciones">
    <div class="row pt-3 pb-3">
      <div class="col">
        <h2>AFILIACIONES EN PADRON</h2>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <table class="table table-strip display responsive nowrap text-center" id="tabla-afiliaciones" style="width:100%">
          <thead>
            <tr>
              <th class="">Nombre</th>
              <th class="">Cédula</th>
              <th class="">Estado</th>
              <th class="">Observación</th>
              <th class="">Fecha de aprobación</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MODAL HISTORICO AFILIADO -->
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalHistorico" style="overflow-y: scroll;">
    <div class="modal-dialog" style="margin: 2rem auto; max-width: 1400px;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Historial</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-strip display responsive nowrap text-center" id="table-historial-afiliado" style="width:100%">
            <thead>
              <tr>
                <th class="">Usuario</th>
                <th class="">Nombre</th>
                <th class="">Cédula</th>
                <th class="">Estado</th>
                <th class="">Motivo de rechazo</th>
                <th class="">Observación</th>
                <th class="">Fecha</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- CONTROL ABM -->
  <div class="container-fluid" style="margin: 2rem auto; padding: 2rem; display: none;" id="control_abm">
    <div class="row pt-3 pb-3">
      <div class="col">
        <h2>CONTROL ABM</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="">Exportar datos</label><br>
          <button type="button" id="exportarPadronDatos" class="btn btn-primary" style="margin-top: 10px;">Corte padron datos</button>
          <button type="button" id="exportarPadronProducto" class="btn btn-primary" style="margin-top: 10px;">Corte padron detalle</button>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="">Bajas</label><br>
          <button type="button" id="verMas" class="btn btn-warning" style="margin-top: 10px;">Ver más</button>
        </div>
      </div>
    </div>
    <div class="row" style="display:none" id="divEliminar">
      <form class="form-inline">
        <div class="form-group mx-sm-3 mb-2">
          <label for="" class="">Cedula: </label>
        </div>
        <div class="form-group mx-sm-3 mb-2">
          <label for="inputPassword2" class="sr-only">Cédula</label>
          <input type="text" class="form-control" id="cedula_eliminar" placeholder="cédula">
        </div>
        <button type="button" id="btnEliminarSocioPadron" class="btn btn-danger mb-2">Eliminar</button>
      </form>
    </div>
    <hr>
    <div class="row text-center" style="justify-content: center;">
      <div class="col-md-3">
        <div class="form-group">
          <label for="">Buscar cédula</label>
          <input type="text" name="" class="form-control solo_numeros" id="cedula_socio" placeholder="cédula" maxlength="8">
          <button type="button" id="buscarInfoSocio" class="btn btn-primary" style="margin-top: 10px;">Buscar</button>
        </div>
      </div>
    </div>
    <br>
    <h2 class="text-center" style="margin-bottom: 30px;">Datos personales</h2>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Cédula</label>
          <input type="text" class="form-control abm_input" name="" id="abm_cedula" readonly>
          <input type="hidden" name="" id="abm_idsocio">
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Nombre</label>
          <input type="text" class="form-control abm_input" name="" id="abm_nombre" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Dirección</label>
          <input type="text" class="form-control abm_input" name="" id="abm_direccion" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Teléfono</label>
          <input type="text" class="form-control abm_input" name="" id="abm_tel" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Sucursal</label>
          <input type="text" class="form-control abm_input" name="" id="abm_suc" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Fecha nacimiento</label>
          <input type="text" class="form-control abm_input" name="" id="abm_fechan" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Radio</label>
          <input type="text" class="form-control abm_input" name="" id="abm_radio" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Ruta</label>
          <input type="text" class="form-control abm_input" name="" id="abm_ruta" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Numero tarjeta</label>
          <input type="text" class="form-control abm_input" name="" id="abm_numtar" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Nombre titular</label>
          <input type="text" class="form-control abm_input" name="" id="abm_nomtit" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Cédula titular</label>
          <input type="text" class="form-control abm_input" name="" id="abm_cedtit" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Movimiento</label>
          <input type="text" class="form-control" name="" id="abm_movimiento" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Fecha de afiliación</label>
          <input type="text" class="form-control abm_input" name="" id="abm_fechafil" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Total importe</label>
          <input type="text" class="form-control" name="" id="abm_totalimporte" readonly>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label for="">Observaciones</label>
          <textarea name="abm_observaciones" class="form-control abm_input" id="abm_observaciones" cols="30" rows="5" readonly></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <div class="form-group">
          <button id="btnEditarDatos" type="button" class="btn btn-primary" disabled>Editar</button>
          <button id="btnLimpiarDatosAbm" type="button" class="btn btn-primary">Limpiar campos</button>
          <button id="btnActualizarDatosSocio" type="button" class="btn btn-success" style="display: none;">Actualizar</button>
        </div>
      </div>
    </div>

    <hr>
    <h2 class="text-center" style="margin-bottom: 30px;">Servicios</h2>
    <div class="row">
      <div class="col">
        <table class="table table-strip display responsive nowrap text-center" id="table-servicios" style="width:100%">
          <thead>
            <tr>
              <th class="">Servicio</th>
              <th class="">Horas</th>
              <th class="">Importe</th>
              <th class="">Cod promo</th>
              <th class="">Fecha afiliación</th>
              <th class="">Fecha registro</th>
              <th class="">ABM</th>
              <th class="">Observación</th>
              <th class="">Número vendedor</th>
              <th class="">Acción</th>
              <th class="">Acción</th>
              <th class="">id</th>
              <th class="">keepprice</th>

            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- CONTROL PISCINA ABM -->
  <div class="container-fluid" style="margin: 2rem auto; padding: 2rem; display: none;" id="control_piscina">
    <div class="row pt-3 pb-3">
      <div class="col">
        <h2>CONTROL PISCINA ABM</h2>
      </div>
    </div>
    <div class="row text-center" style="justify-content: center;">
      <div class="col-md-3">
        <div class="form-group">
          <label for="">Buscar cédula</label>
          <input type="text" name="" class="form-control solo_numeros" id="cedula_sociop" placeholder="cédula" maxlength="8">
          <button type="button" id="buscarInfoPiscina" class="btn btn-primary" style="margin-top: 10px;">Buscar</button>
        </div>
      </div>
    </div>
    <br>
    <h2 class="text-center" style="margin-bottom: 30px;">Datos personales</h2>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Cédula</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_cedula" readonly>
          <input type="hidden" name="" id="abmp_idsocio">
          <input type="hidden" name="" id="abmp_metodo_pago">
          <input type="hidden" name="" id="abmp_localidad">
          <!--//cambio -->
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Nombre</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_nombre" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Dirección</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_direccion" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Teléfono</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_tel" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Sucursal</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_suc" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Fecha nacimiento</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_fechan" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Radio</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_radio" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Ruta</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_ruta" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Numero tarjeta</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_numtar" readonly>
          <input type="hidden" class="form-control " name="" id="abmp_tipotarjeta">
          <!--//cambio -->
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Nombre titular</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_nomtit" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Cédula titular</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_cedtit" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Movimiento</label>
          <input type="text" class="form-control" name="" id="abmp_movimiento" readonly>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Fecha de afiliación</label>
          <input type="text" class="form-control abmp_input" name="" id="abmp_fechafil" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Total importe</label>
          <input type="text" class="form-control" name="" id="abmp_totalimporte" readonly>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Estado</label>
          <select class="form-control abmp_input" name="" id="abmp_estado" readonly></select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label for="">Origen venta</label>
          <select class="form-control abmp_input" name="" id="abmp_origenventa" readonly></select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label for="">Observaciones</label>
          <textarea name="abmp_observaciones" class="form-control abmp_input" id="abmp_observaciones" cols="30" rows="5" readonly></textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <div class="form-group">
          <button id="btnVerHistoricoPiscina" type="button" class="btn btn-primary">Ver histórico</button>
          <button id="btnEditarDatosPiscina" type="button" class="btn btn-primary" disabled>Editar</button>
          <button id="btnLimpiarDatosAbmPiscina" type="button" class="btn btn-primary">Limpiar campos</button>
          <button id="btnActualizarDatosSocioPiscina" type="button" class="btn btn-success" style="display: none;">Actualizar</button>
        </div>
      </div>
    </div>
    <hr>
    <h2 class="text-center" style="margin-bottom: 30px;">Servicios</h2>
    <div class="row">
      <div class="col">
        <table class="table table-strip display responsive nowrap text-center" id="table-servicios-piscina" style="width:100%">
          <thead>
            <tr>
              <th class="">Servicio</th>
              <th class="">Horas</th>
              <th class="">Importe</th>
              <th class="">Cod promo</th>
              <th class="">Fecha afiliación</th>
              <th class="">Fecha registro</th>
              <th class="">ABM</th>
              <th class="">Observación</th>
              <th class="">Número vendedor</th>
              <th class="">Acción</th>
              <th class="">Acción</th>
              <th class="">id</th>
              <th class="">keepprice</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR DATOS SOCIO -->
  <div class="modal fade" id="modal_editar_datos_socio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Editar servicio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="contenido">
          <form id="editar_form" role="form">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Servicio</label>
                  <input type="text" class="form-control" name="" id="e_servicio">
                  <input type="hidden" class="form-control" name="" id="abm_idservicio">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Horas</label>
                  <input type="text" class="form-control" name="" id="e_horas">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Importe</label>
                  <input type="text" class="form-control" name="" id="e_importe">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Cod promo</label>
                  <input type="text" class="form-control" name="" id="e_codpromo">
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Abm</label>
                  <input type="text" class="form-control" name="" id="e_abm">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Observación</label>
                  <input type="text" class="form-control" name="" id="e_observacion">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Número vendedor</label>
                  <input type="text" class="form-control" name="" id="e_numven">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Fecha afiliacion</label>
                  <input type="text" class="form-control" name="" id="e_fechafil">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Fecha registro</label>
                  <input type="text" class="form-control" name="" id="e_fechareg">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Keepprice</label>
                  <input type="text" class="form-control" name="" id="e_keepprice">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" id="btn_editar_servicios" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR DATOS SOCIO PISCINA -->
  <div class="modal fade" id="modal_editar_datos_socio_piscina" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Editar servicio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="contenido">
          <form id="editar_form" role="form">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Servicio</label>
                  <input type="text" class="form-control" name="" id="ep_servicio">
                  <input type="hidden" class="form-control" name="" id="abmp_idservicio">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Horas</label>
                  <input type="text" class="form-control" name="" id="ep_horas">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Importe</label>
                  <input type="text" class="form-control" name="" id="ep_importe">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Cod promo</label>
                  <input type="text" class="form-control" name="" id="ep_codpromo">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Abm</label>
                  <input type="text" class="form-control" name="" id="ep_abm">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Observación</label>
                  <input type="text" class="form-control" name="" id="ep_observacion">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Número vendedor</label>
                  <input type="text" class="form-control" name="" id="ep_numven">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Fecha afiliación</label>
                  <input type="text" class="form-control" name="" id="ep_fechafil">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Fecha registro</label>
                  <input type="text" class="form-control" name="" id="ep_fechareg">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="">Keepprice</label>
                  <input type="text" class="form-control" name="" id="ep_keepprice">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" id="btn_editar_servicios_piscina" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL DATOS SOCIO - COMERCIAL -->
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalDatosSocioComercial" style="overflow-y: scroll;">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Datos personales</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
      </div>
    </div>
  </div>

  <!-- MODAL RECHAZOS -->
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalRechazos" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Indica el motivo del rechazo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button id="btnConfirmarRechazo" type="button" class="btn btn-danger">Rechazar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL SELECCION DE RUTA -->
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalRutas" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccione un ruta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button id="btnActulizarRuta" type="button" class="btn btn-primary">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL PRODUCTOS -->
  <div class="modal fade" tabindex="-1" role="dialog" id="modal-datos-productos" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Productos del cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger alert-dismissable" id="error-productos">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error: </strong><span class="error-productos"></span>
          </div>
          <div id="datos_productos"></div>
          <div id="resumen-productos"></div>
          <div class="">
            <div class="divComentario" id="divComentario">
              <label for="" class="texto">Observación *</label>
              <textarea class="form-control" name="comentario" id="comentario" rows="4" placeholder=""></textarea>
            </div>
            <hr>
            <p class='text-uppercase' style="font-size: 1.8rem;">TOTAL: $UY <span class="text-muted" style="font-size: 1.6rem;" id="total"></span></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button id="btnActualizarProductos" type="button" class="btn btn-primary">Actualizar</button>
          <!-- Datos para registro en VidaShop -->
          <input type="hidden" class="inputVidaShop" id="_nombreSocio">
          <input type="hidden" class="inputVidaShop" id="_cedulaSocio">
          <input type="hidden" class="inputVidaShop" id="_telefonoSocio">
          <input type="hidden" class="inputVidaShop" id="_emailSocio">
          <input type="hidden" class="inputVidaShop" id="_vidaPesos">
          <input type="hidden" class="inputVidaShop" id="_countSocio">
          <input type="hidden" class="inputVidaShop" id="_idsocio">
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL SOLICITUD EMAIL VIDASHOP -->
  <div class="modal fade" tabindex="-1" role="dialog" id="modal-solictud-email" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registro en VidaShop</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <p>Ingresar la cuenta de correo electrónico para crear una cuenta en VidaShop.</p>
              <p>En caso de no ingresar la misma, se creara una cuenta con el email <b>cedula@sinmail.com.uy</b> y la contraseña será la <b>cedula</b>.</p>
            </div>
            <div class="form-group">
              <label for="_emailSocio">Email</label>
              <input class="form-control inputVidaShop" type="text" id="_emailSocio" placeholder="">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button id="btnRegistroVidaShop" type="button" class="btn btn-primary" onclick="cargarEmail(event)">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- FIN MODAL PRODUCTOS -->

  <!-- MODAL RED DE COBRANZA-->
  <div class="modal fade" tabindex="-1" role="dialog" id="modal-red-cobranza" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Adelanto de cuota - redes de cobranza</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="rcForm">
            <div class="form-group">
              <label for="rcCedula">Cédula</label>
              <input type="text" class="form-control" name="rcCedula" id="rcCedula" readonly>
            </div>
            <div class="form-group">
              <label for="rcMail">Correo electrónico</label>
              <input type="text" class="form-control" name="rcMail" id="rcMail" readonly>
            </div>
            <div class="form-group">
              <label for="rcTipo">Seleccione la red de cobranza</label>
              <select class="form-control" name="rcTipo" id="rcTipo">
                <option value="">- Seleccione -</option>
                <option value="abitab">Abitab</option>
                <option value="redpagos">Red pagos</option>
              </select>
              <input type="hidden" name="" id="tokenValidador">
              <input type="hidden" name="" id="rcIdSocio">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button id="btnRedCobranza" type="button" class="btn btn-primary" onclick="comprobarMetodoPagoRC()">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL FORMULARIO DE COBRO -->
  <div class="modal fade" tabindex="-1" role="dialog" id="modal-formulario-cobro" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Productos del cliente</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger alert-dismissable" id="error-cobro">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error: </strong><span class="error-cobro"></span>
          </div>
          <div id="divDatosPago">

          </div>
          <div>
            <div class="divComentario" id="divComentario">
              <label for="" class="texto">Observación *</label>
              <textarea class="form-control" name="observacionCobro" id="observacionCobro" rows="4" placeholder=""></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button id="btnConfirmarCobro" type="button" class="btn btn-success">Confirmar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL BENEFICIARIOS -->
  <div class="modal fade" id="modalBeneficiarios">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Beneficiarios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button id="btnActualizarBeneficiarios" type="button" class="btn btn-success">Actualizar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" tabindex="-1" role="dialog" id="modal_registrar_vidaapp">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar en VidaApp</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <span style="color:red; font-size:12px;padding:5px">*Para ingresar en VidaApp la persona deberá usar su cédula tanto de usuario como de contraseña.</span>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="cedula_vidaapp">Cédula</label>
              <input type="text" class="form-control" id="cedula_vidaapp" aria-describedby="cedula" placeholder="Cédula" readonly>
            </div>
            <div class="form-group mt-3">
              <label for="nombre_vidaapp">Nombre Completo</label>
              <input type="text" class="form-control" id="nombre_vidaapp" aria-describedby="nombre completo" placeholder="Nombre Completo" readonly>
            </div>
            <div class="form-group mt-3">
              <label for="mail_vidaapp">Email </label><span style="color:red;font-size:12px"> (no obligatorio, en caso de no llenarlo la persona no recibirá el manual, ni podrá reestablecer su contraseña)</span>
              <input type="email" class="form-control" id="mail_vidaapp" aria-describedby="mail" placeholder="Email">
            </div>
            <div class="form-group mt-3">
              <label for="llenar_contactos_vidaapp">Llenar contactos de emergencia?</label>
              <input type="checkbox" id="llenar_contactos_vidaapp" aria-describedby="llenar_contactos_vidaapp">
            </div>
            <div id="div_telefonos_emergencia" style="display:none">
              <div class="form-group mt-3">
                <label for="mceTC1">Teléfono contacto 1</label>
                <input type="text" class="form-control" id="mceTC1" aria-describedby="mceTC1" placeholder="Teléfono contacto 1">
              </div>
              <div class="form-group mt-3">
                <label for="mceNC1">Nombre contacto 1</label>
                <input type="text" class="form-control" id="mceNC1" aria-describedby="mceNC1" placeholder="Nombre contacto 1">
              </div><br>
              <div class="form-group mt-3">
                <label for="mceTC2">Teléfono contacto 2</label>
                <input type="text" class="form-control" id="mceTC2" aria-describedby="mceTC2" placeholder="Teléfono contacto 2">
              </div>
              <div class="form-group mt-3">
                <label for="mceNC2">Nombre contacto 2</label>
                <input type="text" class="form-control" id="mceNC2" aria-describedby="mceNC2" placeholder="Nombre contacto 2">
              </div><br>
              <div class="form-group mt-3">
                <label for="mceTC3">Teléfono contacto 3</label>
                <input type="text" class="form-control" id="mceTC3" aria-describedby="mceTC3" placeholder="Teléfono contacto 3">
              </div>
              <div class="form-group mt-3">
                <label for="mceNC3">Nombre contacto 3</label>
                <input type="text" class="form-control" id="mceNC3" aria-describedby="mceNC3" placeholder="Nombre contacto 3">
              </div><br>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="btn_registrar_vidaapp">Registrar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR DIRECCION //web -->
  <div class="modal fade" id="modalEditarDireccion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Editar dirección</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="contenido">
          <form id="editar_form" role="form">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="" class="texto">Calle <span class="requerido">*</span></label>
                  <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="calle" id="calle" required>
                  <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-froup">
                  <label class="texto">Elige una opción <span class="requerido">*</span></label>
                  <fieldset id="puertaRadio">
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
              <div class="col-md-2" style="display:none;" id="divPuerta">
                <div class="form-group">
                  <label for="" class="texto">Puerta <span class="requerido">*</span></label>
                  <input type="text" class="form-control calcularCaracteresDisponibles solo_numeros input-error" limitecaracteres="4" maxlength="4" name="puerta" id="puerta" required>
                  <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                </div>
              </div>
              <div class="col-md-2" style="display:none;" id="divSolar">
                <div class="form-group">
                  <label for="" class="texto">Solar <span class="requerido">*</span></label>
                  <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="solar" id="solar" required>
                  <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                </div>
              </div>
              <div class="col-md-2" style="display:none;" id="divManzana">
                <div class="form-group">
                  <label for="" class="texto">Manzana <span class="requerido">*</span></label>
                  <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="manzana" id="manzana" required>
                  <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="" class="texto">Esquina <span class="requerido">*</span></label>
                  <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="esquina" id="esquina" required>
                  <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="" class="texto">Apartamento</label>
                  <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="4" name="apto" id="apto" required>
                  <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <label for="" class="texto">Referencia <span class="requerido">*</span></label>
                  <input type="text" class="form-control input-error" name="referencia" id="referencia" required>
                  <input type="hidden" class="form-control" name="" id="idDir" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" id="btnGuardarDireccion" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
  <!-- FIN MODAL FORMULARIO COBRO -->
  <script>
    $.datetimepicker.setLocale('es');

    $('.fn_beneficiario').datetimepicker({
      format: 'Y-m-d',
      timepicker: false
    });

    $('#desde').datetimepicker({
      format: 'Y-m-d H:i'
    });

    $('#hasta').datetimepicker({
      format: 'Y-m-d H:i'
    });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
  <script src="js/funciones.js?v=1.1.23"></script>
  <script src="js/index.js?v=1.1.25"></script>
</body>

</html>