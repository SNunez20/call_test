/*    SWEET ALERT FUNCIONES */
function correcto(mensaje = false) {
    Swal.fire('Correcto', !mensaje ? 'La operación se realizó correctamente' : mensaje, 'success');
  }
  function success(mensaje = false) {
    correcto(mensaje);
  }
  function error(mensaje = 'Ha ocurrido un error, contacte al administrador', titulo = 'Error!') {
    Swal.fire(titulo, mensaje, 'error');
  }
  
  function warning(mensaje = false) {
    Swal.fire('', !mensaje ? 'Ha ocurrido un error, contacte al administrador' : mensaje, 'warning');
  }
  
  function cargando(opcion = 'M', mensaje = null) {
    let titulo = 'Cargando ...';
    if (mensaje != null) titulo = mensaje;
    else titulo = 'Cargando... ';
    if (opcion === 'M') {
      $loader = Swal.fire({
        title: titulo,
        allowEscapeKey: false,
        allowOutsideClick: false
      });
      Swal.showLoading();
    } else {
      Swal.hideLoading();
      Swal.close();
    }
  }
  
  function showLoading(title = 'Cargando...') {
    Swal.fire({
      title,
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });
  }
  
  function hideLoading() {
    Swal.close();
  }
  
  function confirmar(mensaje) {
    let conf = Swal.fire({
      title: mensaje,
      showDenyButton: true,
      confirmButtonText: 'Aceptar',
      denyButtonText: `Cancelar`,
      icon : 'warning',
    });
    return conf;
  }

  /*    SWEET ALERT FUNCIONES */