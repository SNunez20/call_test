 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Inicio</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
    
      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li> -->

      <li class="nav-item dropdown no-arrow">
          <a class="nav-link " href="#" id="logOutUser" role="button" data-toggle="modal" data-target="#logoutModal">
          <span class="mr-2 d-none d-lg-inline text-gray-800 small font-weight-bold" id="navUsername">Cerrar sesión</span>
          <i class="fas fa-sign-out-alt text-danger"></i>
          </a>
      </li>
     
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="css/img/logovida.png" alt="logo vida" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Reporte ventas</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="css/img/user.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" id="nombreUser" class="d-block"></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" id="pills-tab" role="tablist" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="index.php?reporte=1" class="nav-link reportePadron">
              <i class="fas fa-file-powerpoint"></i>
              <p>
                Reporte padron
                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="reporte_piscina.php?reporte=2" class="nav-link reportePiscina">
              <i class="fas fa-file-alt"></i>
              <p>
                Reporte piscina
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="reporte_vendedores?reporte=3" class="nav-link reporteVendedores">
              <i class="fas fa-file-contract"></i>
              <p>
                Reporte vendedores
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="historico_vendedores?reporte=4" class="nav-link historicoVendedores">
            <i class="fas fa-address-card"></i>
              <p>
                Historico vendedores
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="productos_filiales?reporte=5" class="nav-link productosFiliales">
            <i class="fas fa-money-check-alt"></i>
              <p>
                Productos vendidos por filial
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="historico_altas?reporte=6" class="nav-link historicoAltas">
            <i class="fas fa-chart-line"></i>
              <p>
                Historico de altas 
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="graficos_comparativos?reporte=7" class="nav-link graficosComparativos">
            <i class="fas fa-chart-bar"></i>
              <p>
                Gráficos comparativos
              </p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>