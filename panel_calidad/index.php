<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta content="VidaUY" name="author">
  <meta content="text/html" http-equiv="content-type">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

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
  <link rel="stylesheet" href="css/styles.css">
  <!-- CONFIRM -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

  <!-- SWEETALERT -->
  <script src="js/sweetalert.min.js"></script>


  <title>Panel Calidad | Dashboard</title>
</head>

<body class="">

  <div class="loader" id="loader">
    <img src="../img/loading.gif" id="loading" class="loading" alt="Loader" />
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
                <a href="#historico" class="nav-link" id="btnHistorico">Histórico</a>
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

    <div class="row">
      <div class="col">
        <table class="table table-strip display responsive nowrap text-center" id="table-panelCalidad" style="width:100%">
          <thead>
            <tr>
              <th class="text-left">Nombre</th>
              <th class="text-left">Cédula</th>
              <th class="text-left">Teléfono</th>
              <th class="text-left">Estado</th>
              <th class="text-left">Fecha de carga</th>
              <th class="text-left">Ver comprobante</th>
              <th class="text-left">Acción</th>
              <th class="text-left none">Cedula vendedor</th>
              <th class="text-left none">Nombre vendedor</th>
              <th class="text-left none">Call</th>
              <th class="text-left none">Nombre titular</th>
              <th class="text-left none">Cédula titular</th>
              <th class="text-left none">Departamento</th>
              <th class="text-left none">Ciudad</th>
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
        <h2>HISTÓRICO</h2>
      </div>
    </div>
    <div class="row pt-3 pb-3">
      <div class="col">
        <form action="" class="form-inline">
          <div class="form-group mx-sm-3 mb-2">
            <select class="form-control" name="select-estado" id="select-filter-3">
              <option value="">ESTADO</option>
            </select>
          </div>
          <div class="form-group mx-sm-3 mb-2">
            <button id="btn-clear" class="btn btn-primary">Limpiar filtros</button>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <table class="table table-strip display responsive nowrap text-center" id="table-history" style="width:100%">
          <thead>
            <tr>
              <th class="text-left">Nombre</th>
              <th class="text-left">Cédula</th>
              <th class="text-left">Teléfono</th>
              <th class="text-left">Estado</th>
              <th class="text-left">Fecha de carga</th>
              <th class="text-left">Ver comprobante</th>
              <th class="text-left">Observación</th>
              <th class="text-left">Fecha historico</th>
              <th class="text-left none">Cedula vendedor</th>
              <th class="text-left none">Nombre vendedor</th>
              <th class="text-left none">Call</th>
              <th class="text-left none">Nombre titular</th>
              <th class="text-left none">Cédula titular</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
  <!-- FIN MODAL FORMULARIO COBRO -->

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
  <script src="js/index.js?v=20230915_1"></script>
</body>

</html>