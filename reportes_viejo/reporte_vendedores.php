<?php
 include('general/header.php');
 include('general/sidebar.php');
 ?>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Reporte vendedores</h1>         
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Resumen</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-12 col-lg-8">
              <!-- Buscar por fecha -->
              <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Buscar por fecha</h1>
              </div>
              <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card border-left-primary shadow h-90 py-2">
                        <div class="card-body">
                            <!-- Topbar Search -->
                            <form class="form-inline" action="">
                                <div class="form-group ml-2">                                
                                  <label for="fecha_desde" class="control-label mr-2">desde: </label>
                                  <input type="text" id="fecha_desde" data-toggle="datetimepicker" class="mr-2 enter_and_submit fecha form-control bg-light small" placeholder="Fecha desde" aria-label="Buscar" aria-describedby="basic-addon2">                          
                                </div>
                                <div class="form-group ml-2">                         
                                  <label for="fecha_hasta" class="control-label mr-2">hasta: </label>
                                  <input  type="text" id="fecha_hasta" data-toggle="datetimepicker" class="mr-2 enter_and_submit fecha form-control bg-light small" placeholder="Fecha hasta" aria-label="hasta" aria-describedby="basic-addon2">                       
                                </div>
                                <div class="form-group ml-2">                         
                                  <label for="sueldo" class="control-label mr-2">Sueldo: </label>
                                  <input  type="text" id="sueldo"  class="mr-2 enter_and_submit fecha form-control bg-light small solo_numeros" placeholder="Sueldo vendedor" aria-label="sueldo" >                       
                                </div>
                                <button type="button" id="btnReporteVendedores" onClick="reporteVendedores()" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </form> 
                        </div>
                    </div>
                </div>
              </div>
          </div> 
          <div class="col-12 col-lg-1">
            <!-- <div class="small-box bg-info">
              <div class="inner">
              <h4>VENDEDORES ACTIVOS</h4>
              <h3 id="montoAltas">0</h3>    
              </div>
              <div class="icon">
              <i class="fas fa-file-invoice-dollar"></i>
              </div>
            </div> -->
          </div>
          <div class="col-12 col-lg-3 mt-5">
            <div class="small-box bg-info">
              <div class="inner">
              <h4 class="text-center">VENDEDORES ACTIVOS</h4>
              <h3 id="" class="text-center vendedoresActivos">0</h3>    
              </div>
              <div class="icon">
              <i class="fas fa-users"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h4>TOTAL ALTAS</h4>
                <h3 id="cantAltas">0</h3>
              </div>
              <div class="icon">
              <i class="ion ion-stats-bars"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
              <h4>MONTO DE VENTA ALTAS</h4>
              <h3 id="montoAltas">0</h3>    
              </div>
              <div class="icon">
              <i class="fas fa-file-invoice-dollar"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
              <h4>TOTAL INCREMENTOS</h4>
              <h3 id="cantIncrementos">0</h3>
              </div>
              <div class="icon">
              <i class="ion ion-stats-bars"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
              <h4>MONTO DE VENTA INCREMENTOS</h4>
              <h3 id="montoIncrementos">0</h3>
              </div>
              <div class="icon">
              <i class="fas fa-file-invoice-dollar"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">  
          <section class="col-lg-6 ">    
            <div class="card tablaReporte">
              <div class="card-header"> 
                <h3 class="card-title">
                  <i class="ion ion-stats-bars"></i>
                      Altas por vendedor activo
                  </h3>
              </div>
              <div class="card-body">
                <table id="tableAltasPorVendedor" class="table table-striped table-bordered table-sm text-center" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="th-sm">Vendedor
                      </th>
                      <th class="th-sm">Cédula
                      </th>
                      <th class="th-sm">Call
                      </th>
                      <th class="th-sm">Total altas
                      </th>
                      <th class="th-sm">Monto de venta altas
                      </th>
                      <th class="th-sm">Target
                      </th>
                    </tr>
                  </thead>
                  <tbody id="tbodyAltasPorFilial">
                   
                   
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
              <div class="card-footer"></div>
            </div>
          </section>
          <section class="col-lg-6 ">
            <div class="card tablaReporte">
              <div class="card-header"> 
                <h3 class="card-title">
                  <i class="ion ion-stats-bars"></i>
                      Incrementos por vendedor activo
                  </h3>
              </div>
              <div class="card-body">
                <table id="tableIncrementosPorVendedor" class="table table-striped table-bordered table-sm text-center" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                        <th class="th-sm">Vendedor</th>
                        <th class="th-sm">Cédula</th>
                        <th class="th-sm">Call</th>
                        <th class="th-sm">Total incrementos</th>
                        <th class="th-sm">Monto de venta incrementos</th>
                        <th class="th-sm">Target</th>
                        </tr>
                    </thead>
                  <tbody id="tbodyIncrementosPorFilial">
                  
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
              <div class="card-footer"></div>
            </div>
          </section>
        </div>
        <div class="row">  
          <section class="col-lg-6 ">    
            <div class="card tablaReporte">
              <div class="card-header"> 
                <h3 class="card-title">
                  <i class="ion ion-stats-bars"></i>
                      Altas por vendedor inactivo
                  </h3>
              </div>
              <div class="card-body">
                <table id="tableAltasPorVendedorInactivo" class="table table-striped table-bordered table-sm text-center" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="th-sm">Vendedor
                      </th>
                      <th class="th-sm">Cédula
                      </th>
                      <th class="th-sm">Call
                      </th>
                      <th class="th-sm">Total altas
                      </th>
                      <th class="th-sm">Monto de venta altas
                      </th>
                      <th class="th-sm">Target
                      </th>
                    </tr>
                  </thead>
                  <tbody id="tbodyAltasVendedorInactivo">
                   
                   
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
              <div class="card-footer"></div>
            </div>
          </section>
          <section class="col-lg-6">
            <div class="card tablaReporte">
              <div class="card-header"> 
                <h3 class="card-title">
                  <i class="ion ion-stats-bars"></i>
                      Incrementos por vendedor activo
                  </h3>
              </div>
              <div class="card-body">
                <table id="tableIncrementosPorVendedorInactivo" class="table table-striped table-bordered table-sm text-center" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                        <th class="th-sm">Vendedor</th>
                        <th class="th-sm">Cédula</th>
                        <th class="th-sm">Call</th>
                        <th class="th-sm">Total incrementos</th>
                        <th class="th-sm">Monto de venta incrementos</th>
                        <th class="th-sm">Target</th>
                        </tr>
                    </thead>
                  <tbody id="tbodyIncrementosVendedorInactivo">
                  
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
              <div class="card-footer"></div>
            </div>
          </section>
        </div>
       
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<?php
 include('general/footer.php');
?>