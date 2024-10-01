const produccion = true;
const app = produccion ? 'call' : 'call_test';
const url_app = `http://192.168.1.13/${app}/crediv/`;
const url_ajax = `${url_app}/php/ajax/`;


/** Abre modal con el texto completo **/
function ver_mas(div, observacion) {
  $(`#${div}`).html(observacion);
  $('#modalVerMas').modal('show');
}

/** Alerta personalizable **/
function alerta(titulo, mensaje, icono) {
  Swal.fire({ title: titulo, html: mensaje, icon: icono });
}

/** Alerta con icono de éxito **/
function correcto(mensaje) {
  Swal.fire({ title: 'Exito!', html: mensaje, icon: 'success' });
}

/** Alerta con icono de error **/
function error(mensaje) {
  Swal.fire({ title: 'Error!', html: mensaje, icon: 'error' });
}

/** Alerta con icono de advertencia **/
function warning(mensaje, titulo = "") {
  Swal.fire({ title: titulo, html: mensaje, icon: 'warning' });
}

/** Alerta con icono de información **/
function info(mensaje, titulo = "") {
  Swal.fire({ title: titulo, html: mensaje, icon: 'warning' });
}

/** Alerta que con su confirmación recarga la página **/
function alerta_ancla(titulo, mensaje, icono) {
  Swal.fire({
    icon: icono,
    title: titulo,
    html: mensaje,
  }).then((result) => {
    if (result.isConfirmed) {
      location.reload();
    }
  });
}

/** Abrir modal para mostrar archivos */
function modal_ver_archivo(ruta_archivo, id_registro, archivo_php, div) {
  document.getElementById('mostrar_imagenes_relamos').innerHTML = '';

  $.ajax({
    type: 'GET',
    url: `${url_app}${archivo_php}`,
    data: {
      "id": id_registro,
    },
    dataType: 'JSON',
    success: function (response) {
      if (response.error === false) {
        let imagenes = response.datos;

        imagenes.map((val) => {
          let separar_nombre_archivo = val.split('.');
          let extencion_archivo = separar_nombre_archivo[1];

          if (extencion_archivo != 'pdf') {
            document.getElementById(`${div}`).innerHTML += `<img src="${ruta_archivo}/${val}" style="width: 100%; height: auto"> <br> <br>`;
          } else {
            document.getElementById(`${div}`).innerHTML += `<iframe src="${ruta_archivo}/${val}" width=100% height=600></iframe>`;
          }
        });
      } else {
        error(response.mensaje);
      }
    },
  });

  $('#modalVerArchivos').modal('show');
}

/** Muestra en pantalla un loader **/
function mostrar_loader(opcion = 'M') {
  $loader =
    opcion == 'M' ?
      Swal.fire({
        title: 'Cargando...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
          swal.showLoading();
        },
      }) :
      $loader.close();
}

/** Mostrar en pantalla un mensaje pasajero **/
function alerta_pasajera(mensaje, icono) {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
  });

  Toast.fire({
    icon: icono,
    title: mensaje,
  });
}

/** Obtener fecha y hora actual **/
function fecha_hora_actual() {
  let fecha = new Date();
  let hora = fecha.getHours();
  let minutos = fecha.getMinutes();
  fecha = fecha.toJSON().slice(0, 10);
  hora = String(hora).length == 1 ? `0${hora}` : hora;
  minutos = String(minutos).length == 1 ? `0${minutos}` : minutos;

  return `${fecha} ${hora}:${minutos}`;
}

/** Válidar cédula **/
function comprobar_cedula(cedula) {
  if (cedula == "") {
    error("Debe ingresar una cédula");
  } else {
    if (cedula == '93233611' || cedula == '78183625') return true;

    let arrCoefs = [2, 9, 8, 7, 6, 3, 4, 1];
    let suma = 0;
    let difCoef = parseInt(arrCoefs.length - cedula.length);
    for (let i = cedula.length - 1; i > -1; i--) {
      let dig = cedula.substring(i, i + 1);
      let digInt = parseInt(dig);
      let coef = arrCoefs[i + difCoef];
      suma = suma + digInt * coef;
    }
    return suma % 10 == 0;
  }
}

/** Válidar celular **/
function comprobar_celular(celular) {
  if (celular == "") {
    error("Debe ingresar un celular");
  } else {
    let primeros_dos_digitos = celular.substring(0, 2);

    if (primeros_dos_digitos != 09) {
      return false;
    } else if (celular.length != 9) {
      return false;
    }

    return true;
  }
}