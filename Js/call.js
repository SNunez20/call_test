$addServiSocio = 1;
$divpsocio = 1;

const TARJETAS_IMAGENES = {
  VISA: 'visa.png',
  MASTER: 'mastercard.png',
  OCA: 'oca.jpg',
  LIDER: 'lider.png',
  CREDITEL: 'creditel.jpg',
  ACAC: 'acac.png',
  CABAL: 'cabal.jpg',
  'CLUB DEL EST': 'club_del_este.jpg',
  'C. DIRECTOS': 'creditos_directos.png',
  ILTALCRED: 'iltalcred.png',
  MICROFIN: 'microfin.png',
  'PASS CARD': 'pass_card.png'
};

// NUMEROS DE SERVICIO DE LOS PRODUCTOS ACOTADOS
const PRODUCTOS_ACOTADOS = [
  8, 21, 22, 23, 24, 28, 30, 33, 40, 41, 44, 45, 46, 47,
  48, 49, 50, 59, 61, 62, 63, 64, 65, 66, 29, 58, 56,
  10, 18, 19, 28, 53, 54, 67, 68, 69, 12, 6, 13, 14, 39
];


// #################################### UTILS ################################################################################################
const REGX_FORMATS_OF_DATES = /^((19|20)?[0-9]{2})[\/|-](0?[1-9]|[1][012])[\/|-](0?[1-9]|[12][0-9]|3[01])$/;

function validateDate(input) {
  if (REGX_FORMATS_OF_DATES.test(input)) {
    const values = input.split('-');
    if (values.length === 3) {
      const date = new Date(values[0], Number(values[1]) - 1, values[2]);
      return date.getFullYear() === Number(values[0]) && date.getMonth() === Number(values[1]) - 1 && date.getDate() === Number(values[2]);
    }
  }
  return false;
}

function numberOfDays(month, year) {
  if (month === 1 && year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0)) {
    return 29;
  } else {
    return [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
  }
}
// #################################### UTILS ################################################################################################


// CONTABILIZA EL SUBTOTAL
function calcularSubTotal() {
  let costo = 0;
  $('.importe-servicio').each(function (i, v) {
    if ($(this).val() !== NaN) costo += Number($(this).val());
  });
  return costo;
}

function mostrarSubTotal(costo) {
  if (JSON.parse(localStorage.datos_socio).socio) $('#subtotal-price-socio').html(`${costo}`);
  else $('#subtotal-price').html(`${costo}`);
  $('.subtotal').show();
}

function limpiarSubTotal() {
  $('#subtotal-price-socio').html('');
  $('#subtotal-price').html('');
  $('.subtotal').hide();
  $('.importe-servicio').each(function (i, v) {
    if (this.previousElementSibling.id != "hrsanatorio") {
      $(this).val('');
    }
  });
}

//REVISAR ESTA FUNCION
function contabilizarSubTotal(e) {
  const idProducto = e.target.classList.contains('hservicio') ? $(this).parent().parent().parent().prev().first().find('select').val() : 1;
  const $fechaNacimiento = $('#natalicioano').val() + '-' + $('#nataliciomes').val() + '-' + $('#nataliciodia').val();
  const $localidad = $('#locBen option:selected').data('filial') ? $('#locBen option:selected').data('filial') : JSON.parse(localStorage.datos_socio).filial;
  let horasServicio = $(this).val();
  let horasSanatorio = null;
  let base = false;
  let costo = 0;
  let total = 0;

  // ##############################################################################
  if (idProducto == '1') {
    horasSanatorio = horasServicio;
    horasServicio = null;
    base = true;
    if (JSON.parse(localStorage.datos_socio).socio) {
      base = $(this).find("option:selected").data("base") == "1" ? true : false;
    }
  }

  // ##############################################################################
  // Compruebo si el producto ya lo tenia contratado para calcular solamente las nuevas horas
  let horasActuales = null;
  let importeActual = 0;
  if (JSON.parse(localStorage.datos_socio).productos_socio) {
    JSON.parse(localStorage.datos_socio).productos_socio.forEach(function (e) {
      if (idProducto == Number(e.id_servicio)) {
        horasActuales = Number(e.total_horas);
        importeActual = Number(e.importe);
      }
    });
  }

  if (horasActuales) {
    if (idProducto == '1') {
      if (horasSanatorio != horasActuales) {
        horasSanatorio = horasSanatorio - horasActuales;
        total = calcularPrecio(idProducto, horasSanatorio, horasServicio, $localidad, $fechaNacimiento, base);
        total = Number(importeActual) + Number(total[2]);
      } else {
        total = Number(importeActual);
      }
    } else if (horasServicio != horasActuales) {
      horasServicio = horasServicio - horasActuales;
      total = calcularPrecio(idProducto, horasSanatorio, horasServicio, $localidad, $fechaNacimiento, base);
      total = Number(importeActual) + Number(total[2]);
    } else {
      total = Number(importeActual);
    }
  } else {
    total = calcularPrecio(idProducto, horasSanatorio, horasServicio, $localidad, $fechaNacimiento, base);
    total = total[2];
  }

  // #################################################################################
  if (idProducto != 0) {
    if (e.target.nextElementSibling && e.target.nextElementSibling.tagName == "INPUT") {
      e.target.nextElementSibling.value = Number(total);
    } else {
      e.target.parentNode.parentNode.parentNode.previousElementSibling.querySelector(".importe-servicio").value = Number(total);
    }

    mostrarSubTotal(calcularSubTotal());
  }
}

$('document').ready(function () {
  // LLAMADA ENTRANTE
  $("#btnLlamadaEntrante").click(function (e) {
    e.preventDefault();
    $("#modalLlamadaEntrante").show();
  });

  $("#btnGuardarlLlamadaEntrante").click(llamadaEntrante);

  // CONTABILIZA EL SUBTOTAL
  $(document.body).on('change', '.hservicio', contabilizarSubTotal);
  $(document.body).on('change', '.hsanatorio', contabilizarSubTotal);

  $('.solo_numeros').keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if (
      $.inArray(e.keyCode, [46, 8, 9, 27, 13, 40]) !== -1 ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)
    ) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
    if (e.altKey) {
      return false;
    }
  });

  $('#btnCancelarVenta').click(function (e) {
    e.preventDefault();
    $('#modal-confirmacion-venta').hide();
  });

  $('#numTar').change(guessPaymentMethod);
  $('#numTar').keyup(guessPaymentMethod);

  EventoChangeProductosAdicionales();
  EventoChangeProductosAdicionalesSocio();

  //Oculta los div error de los formularios
  $('#error-datos').hide();
  $('#error-productos').hide();
  $('#error-productos-socio').hide();
  $('#error-beneficiarios').hide();
  $('#error-pago').hide();
  $('#error-convenio').hide();
  $('#error-validacion-cedula').hide();
  $('#error-nuevo-telefono').hide();

  $('#btnAgregarServicio').hide();
  $('#btnQuitarServicio').hide();
  $('#validacion_medio_pago').hide();
  $('#row_sanatorio').hide();

  $('#btnAgregarBeneficiario').click(function () {
    $('#modal-agregar-beneficiarios').modal('show');
  });

  $('#btnCancelarBeneficiarios').click(function () {
    $('#modal-agregar-beneficiarios').modal('hide');
  });

  $('#btnAddBen').click(function () {

    let nombre_ben = $('.beneficiario .nombre_ben').last().val();
    let cedula_ben = $('.beneficiario .cedula_ben').last().val();
    let telefono_ben = $('.beneficiario .telefono_ben').last().val();
    let fechan_ben = $('.beneficiario .fechan_ben').last().val();

    if (nombre_ben == '' || cedula_ben == '' || telefono_ben == '' || fechan_ben == '') {
      $('.error-beneficiarios').text('Debe completar todos los datos del beneficiario');
      $('#error-beneficiarios').show().fadeOut(10000);
    } else if (!comprobarCI(cedula_ben)) {
      $('.error-beneficiarios').text('La cédula es incorrecta');
      $('#error-beneficiarios').show().fadeOut(10000);
    } else if (calcularEdad(fechan_ben) < 18) {
      $('.error-beneficiarios').text('Los beneficiarios deben tener a partir de 18 años');
      $('#error-beneficiarios').show().fadeOut(10000);
    } else {
      agregarBeneficiario();
    }

  });

  $('#btnDelBen').click(function () {
    quitarBeneficiario();
  });

  $('#btnGuardarBeneficiarios').click(function () {

    let idproducto = $('#producto').val();
    $arrBeneficiarios.length = 0;
    let edades = $('.fechan_ben').map(function () {
      let age = $(this).val();
      if (age != '') {
        age = age = calcularEdad(age);
        return age;
      }

    }).get();

    let hayMenores = edades.some(function (val) {
      return val < 18;
    });

    if ((idproducto == '25' || idproducto == '27') && edades.length == 0) {
      $('.error-beneficiarios').text('Debe ingresar al menos 1 beneficiario');
      $('#error-beneficiarios').show().fadeOut(10000);
    } else if (hayMenores) {
      $('.error-beneficiarios').text('No pueden haber menores de edad entre los beneficiaros');
      $('#error-beneficiarios').show().fadeOut(10000);
    } else {
      guardarBeneficiarios();
    }

  });

  $('#btnAgregarServicioSocio').click(agregarServicioSocio);
  $('#btnQuitarServicioSocio').click(function () {
    if ($addServiSocio > 1) {
      $addServiSocio--;
      $('#hrserviciossocio' + $addServiSocio).remove();
      $('#hrserviciossocio' + $addServiSocio).next().val('');
      const productosSocio = $('.producto-socio').map(function () { return $(this).val(); }).get();
      if (!productosSocio.some(function (v) { return v in [2, 3, 5]; })) {
        $('#row_sanatorio_socio').html('');
      }
    }

    limpiarSubTotal();
    listarProductosServiciosSocio(JSON.parse(localStorage.datos_socio).productos_socio);

    if ($('.sprod').length) {
      $('.hservicio').change();
      $('.hsanatorio').change();
    }
  });

  $('#btnCancelarAfiliacion').click(function () {
    $('#medio_pago option:eq(0)').prop('selected', true);
  });

  $('#btnAfiliar').click(function (event) {
    confirmarVenta(event);
  });

  $('#btnSiguiente1').click(function () {
    siguiente(1, 2);
  });

  $('#btnSiguiente2').click(function () {
    siguiente(2, 3);
  });

  $('#btnSiguiente3').click(function () {
    siguiente(3, 4);
  });

  $('#btnSiguiente4').click(function () {
    //toma los productos actuales del socio
    const productosActualesSocio = JSON.parse(localStorage.datos_socio).productos_socio;

    //filtra solo los nuevos productos comparando con aquellos servicios que solo se agregaron
    const idProductosActuales = productosActualesSocio.map(function (prod) {
      return prod.id_servicio;
    });
    const arrIdIncrementos = Array.from(document.querySelectorAll('.productos_socio select.producto-socio'))
      .map(function (x) {
        return x.value;
      })
      .filter(function (x) {
        return idProductosActuales.indexOf(x) === -1;
      });

    let incrementos = 0;
    Array.from(document.querySelectorAll('.productos_socio')).forEach(function (element) {
      if (element.querySelector('.divHorasServicioSocio > select') !== null) {
        const hrServicio = element.querySelector('.divHorasServicioSocio > select').value;
        const idServicio = element.querySelector('select.producto-socio').value;
        const esIncremento = productosActualesSocio.some(function (prod) {
          return prod.id_servicio == idServicio && prod.total_horas != hrServicio;
        });
        esIncremento && ++incrementos;
      }
    });

    if (JSON.parse(localStorage.getItem('datos_socio')).socio) {
      if ($('select.producto-socio').last().val() === null) {
        $('.error-productos-socio').text('Debe seleccionar un producto');
        $('#error-productos-socio').show().fadeOut(5000);
      } else if (arrIdIncrementos.length === 0 && incrementos === 0) {
        $('.error-productos-socio').text('Debe agregar o incrementar un producto');
        $('#error-productos-socio').show().fadeOut(5000);
      } else {
        $('#divDatosConvenio').hide();
        siguiente(4, 4);
      }
    } else {
      siguiente(2, 4);
    }
  });

  $('#btnSiguienteVal').click(finalizarVenta);

  $('#btnAtras1').click(function () {
    cambiarPagina(0);
    limpiarDatosCliente();
  });

  $('#btnAtras2').click(function () {
    cambiarPagina(1);
  });

  $('#btnAtras3').click(function () {
    $('#medio_pago option:eq(0)').prop('selected', true);
    $('#divDatosConvenio').hide();
    cambiarPagina(4);
  });

  $('#btnAtras4').click(function () {
    cambiarPagina(1);
  });

  $('#btnAtrasVal').click(function () {
    // $('#pasotres').hide();
    // $('#validacion_medio_pago').hide();
    cambiarPagina(5);
  });

  $('#cerrar').click(function () {
    localStorage.clear();
    window.location = 'salir.php';
  });

  $('#medio_pago').change(function () {
    var medio = Number($('#medio_pago').val());
    $('#datos_del_cliente').html('');
    listarConvenios(medio);
    $('#divConvenios').show();
    // Comprueba si es socio, completa los datos de la tarjeta y demás.
    // JSON.parse(localStorage.datos_socio).socio && completarDatosTarjetaSocio();   
  });

  $('#convenios').change(function () {
    localStorage.setItem('convenio', 0);
    var conv = $(this).val();
    //convenios que requieren cedula y nombre de titular
    $convenios = ['2', '3', '4', '7', '8', '10', '11', '13', '15', '16'];
    if ($convenios.includes(conv)) {
      $('#divDatosConvenio').show();
      localStorage.setItem('convenio', 1);
    } else {
      $('#divDatosConvenio').hide();
    }
  });

  //validacion solo letras
  $('.solo_letras').keypress(function (e) {
    return soloLetras(e);
  });

  //llena el select delos dias
  for (let i = 1; i <= 31; i++) {
    var cero = i < 10 ? '0' : '';
    $('#nataliciodia').append(`<option value="` + cero + i + `">` + cero + i + `</option>`);
  }

  exiprationYears(2020);

  //llena el select de los años
  var hoy = new Date();
  var anioActual = hoy.getFullYear();
  for (let i = 1918; i <= anioActual; i++) {
    var cero = i < 10 ? '0' : '';
    $('#natalicioano').append(`<option value="` + cero + i + `">` + cero + i + `</option>`);
  }

  //obtiene las localidades del departamento seleccionado
  $('#depBen').change(function () {
    var dep = $(this).val();

    if (dep != '') {
      $.ajax({
        type: 'POST',
        url: 'Ajax/obtenerLocalidades.php',
        data: { departamento: dep },
        dataType: 'json',
        success: function (response) {
          if (response.result) {
            $('#locBen').empty();
            var loc = response.localidades;
            var l = `<option value="" selected>- Seleccione -</option>`;
            loc.forEach(function (val, i) {
              l += `<option value="` + val.id + `" data-filial="` + val.idFilial + `">` + val.localidad + `</option>`;
            });
            $('#locBen').append(l);
            if (document.querySelector(`#locBen option[value="${$loc}"]`)) {
              document
                .querySelector(`#locBen option[value="${$loc}"]`)
                .setAttribute('selected', true);
            }
          }
        }
      });
    } else {
      $('#locBen').empty();
      $('#locBen').append(`<option value="">- Seleccione -</option>`);
    }
    document.querySelector(`#locBen option[value="${$loc}"]`).setAttribute("selected", true);
  });

  $addServi = 2;
  $divp = 1;

  $('#btnQuitarServicio').click(function () {
    if ($addServi > 2) {
      $addServi--;
      $('#hrservicios' + $addServi).next().remove();
      $('#hrservicios' + $addServi).remove();
      $('.hservicio').change();
      $('.hsanatorio').change();
    }
  });

  $('#btnAgregarServicio').click(function () {
    var pSeleccionados = $('.produc')
      .map(function () {
        return $(this).val();
      })
      .get();
    let acotados = ['11', '22', '30', '31', '32', '33', '34', '35', '36', ' 38', '39', '40', '41'];
    let is_acotado = pSeleccionados.map(function (val) {
      return acotados.includes(val);
    });

    if ($('.produc').last().val() == null) {
      $('#error-productos').text('Debe seleccionar un producto');
      $('#error-productos').show().fadeOut(10000);
    } else if (is_acotado.includes(true)) {
      $('.error-productos').text('No puede ofrecer otro servicio');
      $('#error-productos').show().fadeOut(10000);
    } else {
      //obtenemos los id de los servicios de todos los select de servicios
      var serviSelect = $('.produc')
        .map(function () {
          return $(this).val();
        })
        .get();

      //valores de ids a chequear
      var valoresAchequear = ['1', '2', '3', '5'];

      //guardamos en una variable el resultado del chequeo
      var check = serviSelect.some((e) => valoresAchequear.includes(e));

      //definimos por defecto el div del select de horas sanatorio
      var hsanatorio =
        `<div class="col-md-4">
                        <div class="form-group">
                          <div id="divHorasSanatorio` +
        $addServi +
        `" class="divHorasServicio hrsanatorio producto` +
        $addServi +
        `" style="display:none">
                            <label for="" class="texto" >Cantidad horas sanatorio  <span class="requerido">*</span></label>
                            <select id="hrsanatorio" name="hrsanatorio" class="custom-select form-control hsanatorio">
                                <option value="0" data-base="1" disabled>Seleccione cantidad</option>
                                <option value="8" data-base="0" data-base="1">8 hs</option>
                                <option value="16" data-base="0" data-base="0">16 hs</option>
                                <option value="24" data-base="0" data-base="0">24 hs</option>
                            </select>
                            <input type="hidden" class="importe-servicio" />
                          </div>
                      </div>
                    </div>`;

      //si exite algun select que que ya tenga sanatorio entonces modificamos el valor de hsanatorio para que no agregue select horas sanatorios al div
      if (check) {
        hsanatorio = '';
      }

      //definimos el div que contendra los select del servicio adicional
      var select =
        `<div class="row pro" id="hrservicios` +
        $addServi +
        `">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto" >Servicios <span class="requerido">*</span></label>
                          <select id="producto` +
        $addServi +
        `" name="producto` +
        $addServi +
        `" class="custom-select form-control produc sprod">
                            <option value="0" selected disabled>Seleccione servicio</option>
                          </select>
                          <input type="hidden" class="importe-servicio" />
                      </div>
                    </div>` +
        hsanatorio +
        `
                    <div class="col-md-2">
                      <div class="form-group">
                        <div id="divHorasServicio` +
        $addServi +
        `" class="divHorasServicio hrservicio producto` +
        $addServi +
        `" style="display:none">
                          <label for="" class="texto">Horas servicio <span class="requerido">*</span></label>
                          <select id="hrservicio" name="hrservicio" class="custom-select form-control hservicio">
                              <option value="0"  data-base="0"selected disabled>Seleccione cantidad</option>
                              <option value="8" data-base="0">8 hs</option>
                              <option value="16" data-base="0">16 hs</option>
                              <option value="24" data-base="0">24 hs</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>`;

      var elemento = '#hservicios' + $divp;
      var s = '#producto' + $addServi;
      listarServiciosAdicionales(s);
      $('#nuevos_servicios').append(select);
      EventoChangeProductosAdicionales();
      $addServi++;
      $divp++;
    }
  });

  //cada vez que se seleccione un producto en el select se crea o no el div horas sanatorio (ALTA)
  $('.produc').change(function () {
    var id_servicio = $(this).val();
    var arrServicios = [2, 3, 4, 5, 11];

    if (arrServicios.includes(+id_servicio)) {
      var row_sanatorio = `<div class="col-md-4">
                            <div class="form-group">
                              <div id="divHorasSanatorio" class="divHorasSanatorio" >
                                <label for="" class="texto" >Cantidad horas sanatorio <span class="requerido">*</span> </label>
                                <select id="hrsanatorio" name="hrsanatorio" class="custom-select form-control hsanatorio" >
                                    <option value="0" selected disabled>Seleccione cantidad</option>
                                    <option value="8" data-base="1">8 hs</option>
                                    <option value="16" data-base="0">16 hs</option>
                                    <option value="24" data-base="0">24 hs</option>
                                </select>
                                <input type="hidden" class="importe-servicio"/>
                              </div>
                            </div>   
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <div id="divPromoSanatorio" class="divPromoSanatorio" >
                                <label for="" class="texto" >Promo</label>
                                <select id="promoSanatorio" name="promoSanatorio" class="custom-select form-control promo" ${$datoExtra == '2' ? 'disabled' : ''}>
                                    <option value="0" selected disabled>Seleccione promo</option>
                                    <option value="0">Ninguna</option>
                                    <option value="20">NP17</option>
                                </select>
                                <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
                              </div>
                            </div>   
                          </div>`;
      $('#row_sanatorio').html(row_sanatorio);
    } else {
      $('#divHorasSanatorio').hide();
    }
  });

  //consulta la cantidad de horas de los servicios
  $('#producto').change(function () {
    let servi = $(this).val();
    if ($datoExtra == '2') {
      $('#promo').prop('disabled', 'disabled');
    }

    if (servi == '25' || servi == '27') {
      $arrBeneficiarios.length = 0;
    }

    $('#divHorasServicio select option:eq(0)').prop('selected', true);
    limpiarSubTotal();
    if (servi != '') {
      $('#btnAgregarServicio').show();
      $('#btnQuitarServicio').show();
      $.ajax({
        type: 'POST',
        url: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
        data: { producto: servi },
        dataType: 'json',
        success: function (response) {
          const $fechaNacimiento = $('#natalicioano').val() + '-' + $('#nataliciomes').val() + '-' + $('#nataliciodia').val();
          const $localidad = $('#locBen option:selected').data('filial') ? $('#locBen option:selected').data('filial') : JSON.parse(localStorage.datos_socio).filial;
          if (response.result) {
            if (response.cant_horas_sanatorio && servi == '1') {
              $('#divHorasServicio').show();
              $('#divPromo').show();
              $('#divBoton').hide();
              $('#row_sanatorio').hide();
            } else if (response.cant_horas_sanatorio && servi != '1') {
              $('#row_sanatorio').show();
              $('#divPromo').hide();
            }

            if (response.cant_horas_servicio) {
              $('#divHorasServicio').show();
              if (servi == '25' || servi == '27') {
                $('#divBoton').show();
                $('#divPromo').hide();
                $('#divPromoSanatorio').hide();
              } else {
                $('#divBoton').hide();
              }
            } else if (response.cant_horas_sanatorio && !response.cant_horas_servicio && servi != 1) {
              let total = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false);
              $("#producto").next().val(total[2]);
              $('#divHorasServicio').hide();
              $('#divBoton').hide();
              mostrarSubTotal(calcularSubTotal());
            } else if (!response.cant_horas_sanatorio && !response.cant_horas_servicio) {
              $('#divHorasServicio').hide();
              $('#divBoton').hide();
              $('#divPromo').hide();
            } else if (!response.cant_horas_sanatorio || (response.cant_horas_servicio && servi != '1')) {
              $('#divHorasServicio').hide();
              $('#divPromoSanatorio').hide();
              $('#divPromo').hide();
              let total = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false);
              if (JSON.parse(localStorage.datos_socio).socio) $('#subtotal-price-socio').html(total[2]);
              else $('#subtotal-price').html(total[2]);
              $('.subtotal').show();
            }
          }
        }
      });
    } else {
      $('#divHorasServicio').hide();
      $('#row_sanatorio').hide();
      $('#divPromo').hide();
      $('#divBoton').hide();
    }
  });

  //valida que la cedula sea correcta y consulta con el padron
  $('#btnValidarCedula').click(validarCedulaPadron);

  $('body').on('keypress', 'input', function (args) {
    if (args.keyCode == 13) {
      $('#btnEntrar').click();
      return false;
    }
  });

  //muestra el modal con el form de buscar el padron
  $('#btnVerPadron').click(function (e) {
    $('#iframePadronSocio').attr('src', 'http://192.168.1.250:82/ene2');
    $('#modalPadronSocio').show();
  });

  //cierra todos los modales con la clase close
  $('.close').click(() => {
    $('.modal').hide();
  });

  $(this).on('keydown', (e) => (e.keyCode == 27 ? $('.close').click() : true));

  // Abre el modal con el call de abitab
  $('#btnCallAbitab').click((e) => {
    $('#modal-call-abitab .modal-body').html(`
        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" src="http://200.40.137.109/call_abitab/index.php" style="min-height: 100vh; width:100%;"></iframe>
        </div>
      `);
    $('#modal-call-abitab').show();
  });

  //despliega el modal ventas
  $('#btnVender').on('click', function () {
    localStorage.llamadaEntrante = "false";
    localStorage.llamadaEntranteNumero = null;
    $('#modal-venta').modal('show');
  });

  $('#btnConfirmarVenta').click(function (event) {
    confirmarVenta(event);
  });
});


// LLAMADA ENTRANTE
function llamadaEntrante(e) {
  e.preventDefault();
  const $numero = $("#nuevoNumero").val();
  if (!/^(0[0-9]{8}|(2|4)[0-9]{7})/.test($numero)) {
    $('.error-nuevo-telefono').text('Por favor, ingrese un número telefónico valido.');
    $('#error-nuevo-telefono').show().fadeOut(10000);
  } else {
    $.ajax({
      url: "Ajax/guardarNuevoTelefono.php",
      data: { numero: $("#nuevoNumero").val() },
      dataType: "json",
      type: "POST",
      success: function (response) {
        if (response.result) {
          $("#modalLlamadaEntrante").hide();
          // Muestro el modal de ventas
          $("#modal-venta").show();
          // Registro que es una llamada entrante
          localStorage.llamadaEntrante = "true";
          localStorage.llamadaEntranteNumero = $("#nuevoNumero").val();
        }
      },
      error: function (error) {
        // console.log(error);
      }
    });
  }
}

function listarMetodosPago() {
  $.ajax({
    type: 'POST',
    url: 'Ajax/listarMetodosDePago.php',
    data: { code: localStorage.getItem('code') },
    dataType: 'json',
    success: function (response) {
      const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
      let idConveniosCentralizados = ['11', '13', '16', '22'];
      if (response.result) {
        $('#medio_pago').html('');
        var metodo = response.metodos;
        let opciones = `<option value="" selected>- Seleccione -</option>`;
        if (typeof datosSocio != undefined && datosSocio.medio_pago == 2) {
          opciones = `<option value="2">Centralizado</option>`;
        } else {
          metodo.forEach(function (val, i) {
            if (localStorage.promo == '0' || localStorage.code == '0') {
              opciones +=
                `<option value="` + val.id + `">` + val.metodo + `</option>`;
            } else if (localStorage.promo == '20' && val.id == '2') {
              opciones +=
                `<option value="` + val.id + `">` + val.metodo + `</option>`;
            }
          });
        }

        $('#medio_pago').append(opciones);

        let medio = idConveniosCentralizados.includes(datosSocio.idconvenio) ? 2 : datosSocio.medio_pago;

        if (datosSocio.socio) {
          $('#medio_pago').val(medio);
          $('#medio_pago').change();
        }
      }
    }
  });
}

function exiprationYears(aniov = 2020) {
  const maxYear = aniov + 10;
  while (aniov <= maxYear) {
    $('#anoVen').append(`<option value="` + aniov + `">` + aniov + `</option>`);
    aniov++;
  }
}

function listarConvenios(id_metodo) {
  let datosSocio = JSON.parse(localStorage.datos_socio);
  let servicios = [];
  let element = (datosSocio.socio) ? ".producto-socio" : ".produc";
  Array.prototype.slice.call(document.querySelectorAll(element)).forEach(function (e) { servicios.push(e.value) });

  $.ajax({
    type: 'POST',
    url: 'ajax/listarConvenios.php',
    data: { idMetodo: id_metodo, servicios: servicios, localidad: $loc },
    dataType: 'json',
    success: function (response) {
      var conv = response.convenios;
      $('#convenios').html('');
      let opciones = `<option value="" >- Seleccione -</option>`;

      if (typeof datosSocio != undefined && datosSocio.medio_pago == 2) {
        opciones = `<option value="20" data-idmetodo = "2" selected>TARJETA DE CREDITO</option>`;
      } else {
        conv.forEach(function (val, i) {
          if (localStorage.promo == '0' || localStorage.code == '0') {
            opciones +=
              `<option value="` +
              val.id +
              `" data-idmetodo = "` +
              val.id_metodo +
              `" ${val.id == datosSocio.idconvenio ? 'selected' : ''}>` +
              val.convenio +
              `</option>`;
          } else if (localStorage.promo == '20' && val.id == '20') {
            opciones +=
              `<option value="` +
              val.id +
              `" data-idmetodo = "` +
              val.id_metodo +
              `" ${val.id == datosSocio.idconvenio ? 'selected' : ''}>` +
              val.convenio +
              `</option>`;
          }
        });
      }

      $('#convenios').append(opciones);
      if (datosSocio.socio) { $('#convenios').change(); }
    }
  });
}

function completarDatosTarjetaSocio() {
  const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
  const telefonosSocio = datosSocio.telefono_titular.split(' ').filter(function (x) {
    return x;
  });
  exiprationYears(Number(datosSocio.anio_e));
  $('#numTar').val(datosSocio.numero_tarjeta);
  $('#cvv').val(datosSocio.cvv);
  $('#nomTit').val(datosSocio.nombre_titular);
  $('#cedTit').val(datosSocio.cedula_titular);
  $('#mailTit').val(datosSocio.email_titular);
  $('#telTit').val(telefonosSocio[0]);
  $('#celTit').val(telefonosSocio.length > 1 ? telefonosSocio[1] : '');
  $('#bancos').val(datosSocio.banco_emisor);
  $("#mesVen option[value='" + (datosSocio.mes_e.length == 1 ? '0' + datosSocio.mes_e : datosSocio.mes_e) + "']").prop('selected', true);
  $("#anoVen option[value='" + datosSocio.anio_e + "']").prop('selected', true);
  guessPaymentMethod();
}

function listarBancosEmisores() {
  $.ajax({
    type: 'POST',
    url: 'ajax/listarBancosEmisores.php',
    dataType: 'json',
    success: function (response) {
      var conv = response.bancos;
      $('#bancos').html('');
      let opciones = `<option value="" selected>- Seleccione -</option>`;
      conv.forEach(function (val, i) {
        opciones += `<option value="` + val.id + `">` + val.banco + `</option>`;
      });
      $('#bancos').append(opciones);
    }
  });
}

function EventoChangeProductosAdicionales() {
  var aValores = $('.produc')
    .map(function () {
      return $(this).val();
    })
    .get();

  //consulta la cantidad de horas de los servicios adicionales
  $(document.body).on('change', '.sprod', function (e) {
    var servi = $(this).val();
    padre = $(this).parent().parent().parent().attr('id');

    if (servi != '') {
      $('#btnAgregarServicio').show();
      $.ajax({
        type: 'POST',
        url: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
        data: { producto: servi },
        dataType: 'json',
        success: function (response) {
          if (response.result) {
            $('#' + padre + ' .hservicio option:eq(0)').prop("selected", true);

            if (servi == '1') {
              $('#' + padre + ' .hrservicio option:eq(0)').prop("selected", true);
              $('#' + padre + ' .hrservicio').next().val("");
              $('#' + padre + ' .hrservicio').show();
              $('#' + padre + ' .divComentarioSocio').show();
              // $('#' + padre + ' .divPromoSocio').show();
              $('#row_sanatorio_socio').hide();
            } else if (response.cant_horas_sanatorio) {
              $('#row_sanatorio_socio').show();
            }

            if (response.cant_horas_servicio) {
              $('#' + padre + ' .hrservicio option:eq(0)').prop("selected", true);
              $('#' + padre + ' .hrservicio').next().val("");
              $('#' + padre + ' .hrservicio').show();
              // $('#' + padre + ' .divPromoSocio').hide();
            } else if (servi != 1 && servi != '5') {
              $('#' + padre + ' .hrservicio').hide();
              $('#' + padre + ' .divComentarioSocio').hide();
              // $('#' + padre + ' .divPromoSocio').hide();
              $('#row_sanatorio_socio').hide();
            }

            const $fechaNacimiento = $('#natalicioano').val() + '-' + $('#nataliciomes').val() + '-' + $('#nataliciodia').val();
            const $localidad = $('#locBen option:selected').data('filial') ? $('#locBen option:selected').data('filial') : JSON.parse(localStorage.datos_socio).filial;

            if (!response.cant_horas_sanatorio && !response.cant_horas_servicio) {
              const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false);
              // $(this).parent().append(`<input type="hidden" class="importe-servicio" value="${importe[2]}"`); 
              $(`#${padre}`).find(".importe-servicio").first().val(importe[2]);
              mostrarSubTotal(calcularSubTotal());
              // if ($('.sprod').length) {
              //   $('.hservicio').change();
              //   $('.hsanatorio').change();
              // }
            } else if (response.cant_horas_sanatorio && !response.cant_horas_servicio) { // Sanatorio
              if (servi == "1") {
                const importe = calcularPrecio(1, 8, null, $localidad, $fechaNacimiento, true);
                $(this).parent().append(`<input type="hidden" class="importe-servicio" value="${importe[2]}"`);
                $('#row_sanatorio_socio .importe-servicio').val("")
                $(".importe-servicio").last().val("");
              } else {
                const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false);
                const element = document.createElement("INPUT");
                if (e.target.parentNode.querySelector('.importe-servicio')) {
                  e.target.parentNode.removeChild(
                    e.target.parentNode.querySelector('.importe-servicio')
                  );
                }
                element.type = "hidden";
                element.classList.add("importe-servicio");
                element.value = importe[2];
                e.target.parentNode.append(element);
              }
              mostrarSubTotal(calcularSubTotal());
            } else { // Tradicional              
              mostrarSubTotal(calcularSubTotal());
            }
          }
        }
      });
    } else {
      $('#divHorassanatorio').hide();
    }
  });
}

function tieneAcotados() {
  // Compruebo si tiene acotados para limitar las horas de sanatorio a partir de 16 horas.
  return JSON.parse(localStorage.datos_socio).productos_socio.some(function (producto) {
    // 22 - Grupo Familiar
    // 23 - 26 Grupos Familar G5
    // 26 - 28 Grupos Familar G6
    return [11, 22, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 46, 47, 23, 24, 25, 26, 27, 28].includes(Number(producto.id_servicio));
  });
}

function EventoChangeProductosAdicionalesSocio() {

  //consulta la cantidad de horas de los servicios adicionales
  $(document.body).on('change', 'select.producto-socio', function () {
    $(".importe-servicio").last().val("");
    var aValores = $('select.producto-socio')
      .map(function () {
        return $(this).val();
      })
      .get();
    const servi = $(this).val();
    const padre = $(this).parent().parent().parent().attr('id');
    const arrServiciosSocio = [2, 3, 5];

    $('#' + padre + ' .hservicio option:eq(0)').prop("selected", true);

    let tieneOchoHoras = `
      <option value='8' data-base="1">8 hs</option>
      <option value="16" data-base="0">16 hs</option>
      <option value="24" data-base="0">24 hs</option>
    `;
    let ochoHorasSanatorio = `
      <option value='8' data-base="1">8 hs</option>
      <option value="16" data-base="0">16 hs</option>
      <option value="24" data-base="0">24 hs</option>
    `;

    // Si es un tradicional limito el sanatorio
    let serviciosactuales = JSON.parse(localStorage.getItem("datos_socio")).productos_socio;
    let grupoFamiliar = serviciosactuales.filter(function (val) {
      return (val.num_servicio == '63' || val.num_servicio == '64' || val.num_servicio == '65' || val.num_servicio == '66')
    });

    //si es grupo familiar y tiene mas de 8 horas sanatorio
    if (grupoFamiliar.length > 0 && Number(grupoFamiliar[0].total_horas) >= 16) {
      let horasg = Number(grupoFamiliar[0].total_horas);
      tieneOchoHoras = `
        <option value="8" data-base="0" ${(horasg == 24) ? 'disabled' : ''}>24 hs</option>
      `;

    } else if ([2, 3, 5].includes(Number($('select.producto-socio').last().val())) && tieneAcotados()) {
      ochoHorasSanatorio = `
        <option value="8" data-base="0">8 hs</option>
        <option value="16" data-base="0">16 hs</option>
      `;
      // si es el sanatorio como servicio
    } else if ($('select.producto-socio').last().val() == "1" && tieneAcotados()) {
      tieneOchoHoras = `
        <option value="8" data-base="0">8 hs</option>
        <option value="16" data-base="0">16 hs</option>
      `;
    }

    if (!aValores.includes('1')) {
      if (arrServiciosSocio.includes(+servi) && $('#hrsanatorio').val() === undefined) {
        $('#row_sanatorio_socio').html(`<div class="col-md-4">
                                          <div class="form-group">
                                            <div id="divHorasSanatorioSocio" class="divHorasSanatorioSocio">
                                              <label for="" class="texto" >Cantidad horas sanatorio <span class="requerido">*</span> </label>
                                              <select id="hrsanatorio" name="hrsanatorio" class="custom-select form-control hsanatorio">
                                                  <option value="0" selected disabled>Seleccione cantidad</option>
                                                  ${ochoHorasSanatorio}
                                              </select>
                                              <input type="hidden" class="importe-servicio"/>
                                            </div>
                                          </div>   
                                        </div>
                                        <div class="col-md-2">
                                          <div class="form-group">
                                            <div id="divPromoSanatorioSocio" class="divPromoSanatorioSocio" style="display: none;" >
                                              <label for="" class="texto" >Promo</label>
                                              <select id="promoSanatorioSocio" name="promoSanatorioSocio" class="custom-select form-control promo">
                                                  <option value="0" selected disabled>Seleccione promo</option>
                                                  <option value="0">Ninguna</option>
                                                  <option value="20">NP17</option>
                                              </select>
                                              <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
                                            </div>
                                          </div>   
                                        </div>`);
      } else if ($('#hrsanatorio').val() === undefined) {
        $('#row_sanatorio_socio').html('');
      }
    }

    $('#' + padre + ' .importe-servicio').val("");

    if (servi != '') {
      $('#btnAgregarServicioSocio').show();
      $.ajax({
        type: 'POST',
        url: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
        data: { producto: servi },
        dataType: 'json',
        success: function (response) {
          if (response.result) {
            if (response.cant_horas_servicio || servi == '1') {
              $('#' + padre + ' .hrserviciosocio select.hservicio').html(`
                <option value="0" selected disabled>Seleccione cantidad</option>
                ${tieneOchoHoras}                
              `);
              $('#' + padre + ' .hrserviciosocio').show();
            } else if (servi != '1') {
              $('#' + padre + ' .hrserviciosocio').hide();
            }
          }
        }
      });
    } else {
      $('#divHorassanatorio').hide();
    }
  });
}

function reiniciarVariablesGlobales() {
  $cedula = '';
  $nombre = '';
  $dia = '';
  $mes = '';
  $anio = '';
  $direccion = '';
  $dep = '';
  $loc = '';
  $edad = '';
  $mail = '';
  $cel = '';
  $tel = '';
  $fecha_nacimiento = '';
  $total = 0;
  $numTarjeta = '';
  $cedulaTit = '';
  $nombreTit = '';
  $cvv = '';
  $mesVencimiento = '';
  $anoVencimiento = '';
  $mailTit = '';
  $celularTit = '';
  $arrServicios = [];
  $productos = ``;
  listaProductosSocio = '';
  $observacion = '';
  $filial = '';
  $medio_pago = '';
  $arrBeneficiarios.length = 0;
}

$cedula = '';
$nombre = '';
$dia = '';
$mes = '';
$anio = '';
$direccion = '';
$edad = '';
$dep = '';
$loc = '';
$mail = '';
$cel = '';
$tel = '';
$telAlternativo = '';
$fecha_nacimiento = '';
$total = 0;
$numTarjeta = '';
$cedulaTit = '';
$nombreTit = '';
$cvv = '';
$mesVencimiento = '';
$anoVencimiento = '';
$mailTit = '';
$celularTit = '';
$arrServicios = [];
$productos = '';
let listaProductosSocio = '';
$observacion = '';
$filial = '';
$medio_pago = '';
$arrBeneficiarios = [];

function siguiente(seccion, pagina) {
  switch (seccion) {
    case 0:
      mostrarValidacion();
      break;
    case 1:
      $cedula = $('#cedBen').val();
      $nombre = $('#nomBen').val().toUpperCase();
      $dia = $('#nataliciodia').val();
      $mes = $('#nataliciomes').val();
      $anio = $('#natalicioano').val();
      $direccion = $('#dirBen').val().toUpperCase();
      $dep = $('#depBen').val();
      $loc = $('#locBen').val();
      $filial = $('#locBen option:selected').data('filial') ? $('#locBen option:selected').data('filial') : JSON.parse(localStorage.datos_socio).filial;
      $mail = $('#mailBen').val();
      $cel = $('#celBen').val();
      $tel = $('#telBen').val();
      $telAlternativo = $('#telAltBen').val();
      $datoExtra = $('#dato_adicional input[name="dato_adicional"]:checked').val();

      // existenNumeros = JSON.parse(localStorage.getItem("datos_socio")).socio ? validarTelefonoPadron($cel, $tel) : [false];
      existenNumeros = validarTelefonoPadron($cel, $tel);
      //si hubo un cambio en la localidad se setea en el localstorage para ser utilizado en el caso 4 para el calculo de los precios
      if (localStorage.code == '0' && $loc != '') {
        localStorage.setItem('nueva_localidad', true);
      } else {
        localStorage.setItem('nueva_localidad', false);
      }

      rejectCel = new RegExp('^09');
      rejectTel = new RegExp('^(2|4)');

      //validaciones de formulario
      if ($nombre == '') {
        $('.error-datos').text('').append('Debe ingresar el nombre del beneficiario');
        $('#error-datos').show().fadeOut(10000);
      } else if ($dia == '') {
        $('.error-datos').text('').append('Debe seleccionar un día');
        $('#error-datos').show().fadeOut(10000);
      } else if ($mes == '') {
        $('.error-datos').text('').append('Debe seleccionar un mes');
        $('#error-datos').show().fadeOut(10000);
      } else if ($anio == '') {
        $('.error-datos').text('').append('Debe seleccionar un año');
        $('#error-datos').show().fadeOut(10000);
      } else if ($direccion == '') {
        $('.error-datos').text('').append('Debe ingresar una dirección');
        $('#error-datos').show().fadeOut(10000);
      } else if ($mail != '' && !validarEmail($mail)) {
        $('.error-datos').text('').append('El correo es inválido');
        $('#error-datos').show().fadeOut(10000);
      } else if ($cel == '') {
        $('.error-datos').text('').append('Debe ingresar un número celular');
        $('#error-datos').show().fadeOut(10000);
      } else if ($cel != '' && $cel.match(rejectCel) == null) {
        $('.error-datos').text('').append('Debe ingresar un número de celular válido');
        $('#error-datos').show().fadeOut(10000);
      } else if ($tel != '' && $tel.match(rejectTel) == null) {
        $('.error-datos').text('').append('Debe ingresar un telefono fijo válido');
        $('#error-datos').show().fadeOut(10000);
      } else if (existenNumeros[0]) {
        $('.error-datos').text('').append(existenNumeros[1]);
        $('#error-datos').show().fadeOut(10000);
      } else {
        $fecha_nacimiento = $anio + '-' + $mes + '-' + $dia;

        if (!validateDate($fecha_nacimiento)) {
          $('#modal-venta .modal-content').scrollTop(0);
          $('.error-datos').text('La fecha ingresada no es valida');
          $('#error-datos').show().fadeOut(10000);
        } else {
          let fecha = new Date($fecha_nacimiento);
          $edad = calcularEdad($fecha_nacimiento);

          if ($edad < 18) {
            $('#modal-venta .modal-content').scrollTop(0);
            $('.error-datos').text('No se permite afiliar a menores de edad');
            $('#error-datos').show().fadeOut(10000);
          } else if ($dep == '') {
            $('#modal-venta .modal-content').scrollTop(0);
            $('.error-datos')
              .text('')
              .append('Debe seleccionar un departamento');
            $('#error-datos').show().fadeOut(10000);
          } else if ($loc == '') {
            $('#modal-venta .modal-content').scrollTop(0);
            $('.error-datos').text('').append('Debe seleccionar una localidad');
            $('#error-datos').show().fadeOut(10000);
          } else {
            cambiarPagina(pagina);
          }
        }
        break;
      }
    case 2:
      var precio = 0;
      var sanatorio = $('#hrsanatorio').val();
      var promo_sanatorio = $('#promoSanatorio').val() == '' || $('#promoSanatorio').val() == null ? 0 : $('#promoSanatorio').val();
      var precio_base = 0;
      var c = 0;
      var retorno = true;
      var productos = ``;
      var precios_sanatorio = '';
      var promo = $('#promo').val();
      localStorage.promo = (promo_sanatorio == '20') ? promo_sanatorio : promo;

      total_sanatorio = 0;
      $productos = ``;
      $observacion = $('#comentario').val().trim();

      //recorremos cada select de productos adicionales
      $('.pro').each(function () {
        //capturamos el id del producto
        var idProducto = $('.col-md-4>.form-group>.produc', this).val();

        //capturamos la horas sanatorio
        var hrSanatorio = $('.col-md-4>.form-group>.divHorasSanatorio>select', this).val();

        //capturamos las horas servicio contratado
        var hrServicio = $('#hrservicio', this).val();

        //nombre del producto
        var nombreProducto = $('select.produc option:selected', this).text();

        //variable que indica si hara el calculo con base o sin base
        var base = true;

        //validacion de select vacios
        if ((idProducto == '0' || idProducto == null || idProducto == undefined || idProducto == '') && (hrServicio != null || hrServicio == null)) {
          $('.error-productos').text('Debe seleccionar un servicio');
          $('#error-productos').show().fadeOut(10000);
          return (retorno = false);
        } else if ((idProducto == '2' || idProducto == '3') && (hrServicio == '0' || hrServicio == null || hrServicio == undefined)) {
          $('.error-productos').text('').append('Debe seleccionar las horas del servicio');
          $('#error-productos').show().fadeOut(10000);
          return (retorno = false);
        } else if ((idProducto == '25' || idProducto == '27') && $arrBeneficiarios.length < 1) {
          $('.error-productos').text('').append('El servicio grupo familiar debe contar con mínimo 2 beneficiarios');
          $('#error-productos').show().fadeOut(10000);
          return (retorno = false);
        }

        //validamos si de verdad hay horas sanatorio
        if (hrSanatorio == null || hrSanatorio == undefined) {
          hrSanatorio = null;
          base = false;
        }

        //validamos si de verdad hay horas servicio contrato
        if (hrServicio == null || hrServicio == undefined) {
          hrServicio = 8;
        }

        if (idProducto == '1') {
          hrSanatorio = hrServicio;
          hrServicio = null;
          base = true;
        }

        //enviamos los datos al web service para que calcule el precio del servicio
        var valores_servicios = calcularPrecio(idProducto, hrSanatorio, hrServicio, $filial, $fecha_nacimiento, base);

        if (idProducto == '1') {
          hrServicio = hrSanatorio;
          //promocion
          promo = $('#promo', this).val() != '' ? $('#promo', this).val() : '0';
        }
        $arrServicios[c] = [idProducto, hrServicio, valores_servicios[2], valores_servicios[0], valores_servicios[1], $observacion, promo];
        c++;

        //acumulamos el valor
        precio = precio + valores_servicios[2];
        $productos += `<p><span class='text-muted'>${nombreProducto} (${hrServicio} hrs): </span>  $U ${valores_servicios[2]}</p>`;
      });

      if ($('#hrsanatorio').is(':visible') && sanatorio != '0' && sanatorio != null && sanatorio != undefined) {
        precios_sanatorio = calcularPrecio(1, sanatorio, null, $filial, $fecha_nacimiento, true);
        $arrServicios[c] = ['1', sanatorio, precios_sanatorio[2], precios_sanatorio[0], precios_sanatorio[1], $observacion, promo_sanatorio];
        total_sanatorio = precios_sanatorio[2];
        $productos += `<p><span class='text-muted'>Sanatorio (${sanatorio} hrs): </span>  $U ${precios_sanatorio[2]}</p>`;
      } else if ($('#hrsanatorio').is(':visible') && sanatorio == null) {
        $('.error-productos').text('Debe seleccionar las horas sanatorio');
        $('#error-productos').show().fadeOut(10000);
        retorno = false;
      }


      if ($observacion == '') {
        $('.error-productos').text('Debe ingresar una observación');
        $('#error-productos').show().fadeOut(5000);
      } else {
        if (retorno) {
          $total = precio + total_sanatorio;
          $('#spanPrecio').text('$U ' + $total);
          cambiarPagina(3);
        }
        break;
      }

    case 3:
      $numTarjeta = $('#numTar').val();
      $cedulaTit = $('#cedTit').val();
      $nombreTit = $('#nomTit').val().toUpperCase();
      $cvv = $('#cvv').val();
      $mesVencimiento = $('#mesVen').val();
      $anoVencimiento = $('#anoVen').val();
      $mailTit = $('#mailTit').val();
      $celularTit = $('#celTit').val();
      $telefonoTit = $('#telTit').val();
      $bancoEmisor = $('#bancos').val();
      let tarjeta = $('#payment_method_id').val();
      const currentYear = new Date().getFullYear();
      const currentMonth = new Date().getMonth();


      if (!/^\d{16}$/.test($numTarjeta)) {
        $('.error-pago').text('').append('Debe ingresar un número de tarjeta valido');
        $('#error-pago').show().fadeOut(10000);
      } else if ($cedulaTit == '') {
        $('.error-pago').text('').append('Debe ingresar la cedula del titular');
        $('#error-pago').show().fadeOut(10000);
      } else if (!comprobarCI($cedulaTit)) {
        $('.error-pago').text('').append('La cédula del titular ingresada no es válida');
        $('#error-pago').show().fadeOut(10000);
      } else if ($nombreTit == '') {
        $('.error-pago').text('').append('Debe ingresar el nombre del titular d ela tarjeta');
        $('#error-pago').show().fadeOut(10000);
      } else if ($mesVencimiento == '') {
        $('.error-pago').text('').append('Debe seleccionar el mes de vencimiento');
        $('#error-pago').show().fadeOut(10000);
      } else if ($anoVencimiento == '') {
        $('.error-pago').text('').append('Debe seleccionar el año de vencimiento');
        $('#error-pago').show().fadeOut(10000);
      } else if ((Number($anoVencimiento) < currentYear) || ((Number($anoVencimiento) == currentYear) && (Number($mesVencimiento) < currentMonth))) {
        $('.error-pago').text('').append('La tarjeta se encuentra vencida');
        $('#error-pago').show().fadeOut(10000);
      } else if ($mailTit != '' && !validarEmail($mailTit)) {
        $('.error-pago').text('').append('El correo es inválido');
        $('#error-pago').show().fadeOut(10000);
      } else if ($celularTit == '') {
        $('.error-pago').text('').append('Debe ingresar un número celular');
        $('#error-pago').show().fadeOut(10000);
      } else if (tarjeta == 'debvisa') {
        $('.error-pago').text('').append('Las tarjetas debito VISA no son permitidas');
        $('#error-pago').show().fadeOut(10000);
      } else if (localStorage.mercadopago == '0' && $bancoEmisor == '') {
        $('.error-pago').text('').append('Debe seleccionar un banco emisor');
        $('#error-pago').show().fadeOut(10000);
      } else if (localStorage.tipo_tarjeta == 'CABAL' && $bancoEmisor == '12') {
        $('.error-pago').text('').append('No se aceptan tarjetas CABAL del BBVA');
        $('#error-pago').show().fadeOut(10000);
      } else if (!localStorage.mercadopago && !validarTarjeta()) {
        $('.error-pago').text('').append('La tarjeta no es válida');
        $('#error-pago').show().fadeOut(10000);
      } else {
        localStorage.setItem(
          'dataVenta',
          JSON.stringify({
            nombre: $nombre,
            cedula: $cedula,
            celular: $cel,
            telefono: $tel,
            telefonoAlternativo: $telAlternativo,
            mail: $mail,
            direccion: $direccion,
            fechaNacimiento: $anio + '-' + $mes + '-' + $dia,
            departamento: $dep,
            localidad: $loc,
            datoExtra: $datoExtra,
            filial: $filial,
            observacion: $observacion,
            numeroTarjeta: $numTarjeta,
            cvv: $cvv,
            mesVencimiento: $mesVencimiento,
            anioVencimiento: $anoVencimiento,
            nombreTitular: $nombreTit,
            cedulaTitular: $cedulaTit,
            celularTitular: $celularTit,
            telefonoTitular: $telefonoTit,
            bancoEmisor: $bancoEmisor,
            mailTitular: $mailTit,
            servicios: $arrServicios,
            medio_pago: $medio_pago,
            is_mercadopago: localStorage.getItem('mercadopago'),
            tipo_tarjeta: localStorage.getItem('tipo_tarjeta'),
            total: $total,
            beneficiarios: $arrBeneficiarios
          })
        );
        const departamento = $('#depBen').val() ? $('#depBen > option:selected').text() : '-';
        const localidad = $('#locBen').val() ? $('#locBen > option:selected').text() : '-';
        const mail = $mail ? $mail : '-';
        const mailTitular = $mailTit ? $mailTit : '-';
        const bancoEmisor = ($bancoEmisor == '') ? '  ' : $('#bancos option:selected').text();

        $('#modal-confirmacion-venta .modal-body').html('');
        $('#modal-confirmacion-venta .modal-body').html(
          `<h4>Datos personales</h4>
          <p><span class='text-muted'>Nombre: </span>
          ${$nombre} 
          </p> 
          <p><span class='text-muted'>Cédula: </span> 
          ${$cedula}
          </p> 
          <p><span class='text-muted'>Celular: </span>
          ${$cel} 
          </p> 
          <p><span class='text-muted'>Télefono: </span>
          ${$tel} 
          </p> 
          <p><span class='text-muted'>Mail: </span> 
          ${$mail} 
          </p>
          <p><span class='text-muted'>Fecha de nacimiento: </span> 
          ${$dia} 
          / 
          ${$mes}
          / 
          ${$anio} 
          </p> 
          <p><span class='text-muted'>Departamento: </span> 
          ${departamento} 
          </p> 
          <p><span class='text-muted'>Localidad: </span> 
          ${localidad}
          </p> 
          <hr> 
          <h4>Servicios</h4> 
          ${$productos} 
          <hr> 
          <h4>Datos de la tarjeta</h4> 
          <p><span class='text-muted'>Número de tarjeta: </span> 
          ${$numTarjeta}
          </p> 
          <p><span class='text-muted'>Fecha de vencimiento: </span> 
          ${$mesVencimiento}
          / 
         ${$anoVencimiento}
          </p> 
          "<p><span class='text-muted'>Nombre del titular: </span> 
          ${$nombreTit} 
          </p> 
          <p><span class='text-muted'>Cédula del titular: </span> 
         ${$cedulaTit}
          </p> 
          <p><span class='text-muted'>Celular del titular: </span> 
         ${$celularTit}
          </p> 
          <p><span class='text-muted'>Mail del titular: </span> 
         ${$mailTit}
        </p> 
        <p><span class='text-muted'>Banco emisor: </span>  
          ${bancoEmisor} 
        </p> 
          <hr> 
          <p class='text-uppercase font-weight-bold' style='font-size: 2.3rem;'>TOTAL: $UY ${$total}</p>`
        );
        $('#modal-confirmacion-venta').show();
        // cambiarPagina(pagina);
      }
      break;

    case 4:
      //capturamos el comentario
      $observacion = $('#comentarioSocio').val().trim();

      if ($observacion != '') {
        $productos = '';
        var c = 0;
        var precio = 0;
        var sanatorio_socio = $('#row_sanatorio_socio #hrsanatorio').val();
        var precio_base = 0;
        var retorno = true;
        const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
        var loc = datosSocio.localidad;
        var fecha_nacimiento = ($fecha_nacimiento != datosSocio.fecha_nacimiento) ? $fecha_nacimiento : datosSocio.fecha_nacimiento;
        var promo = 0;
        const productosActualesSocio = datosSocio.productos_socio;
        const idProductosActuales = productosActualesSocio.map(function (prod) {
          return prod.id_servicio;
        });
        let validLimite = false;
        let acotados = false;
        let PRODUCTOS_ACOTADOS = ['8', '18', '32', '43', '44', '33', '34', '35', '45', '31', '47', '30', '22', '25', '26', '27', '28', '33', '34'];

        const arrIdIncrementos = Array.from(document.querySelectorAll('.productos_socio select.producto-socio'))
          .map(function (x) { return x.value; })
          .filter(function (x) { return idProductosActuales.indexOf(x) === -1; });

        // Comprueba si se cambio la localidad, de ser así se calculan los precio en base a esta
        // Tambien se toma en cuenta si es un incremento, calcular las horas que ya contaba con la vieja localidad y las nuevas horas con la nueva localidad
        // let localidad = !localStorage.nueva_localidad ? loc : datosSocio.localidad;
        const localidad = $('#locBen option:selected').data('filial') ? $('#locBen option:selected').data('filial') : JSON.parse(localStorage.datos_socio).filial;

        $('.productos_socio').each(function (index, element) {
          let nuevoProducto = false;
          let hrSanatorio = null;

          //capturamos el id del producto
          var idProducto = $('select.producto-socio', this).val();

          //capturamos las horas servicio contratado
          var hrServicio = $('.divHorasServicioSocio>select', this).val();
          var horass = hrServicio;

          //capturamos el nombre del producto
          var nombreProducto = $('select.producto-socio option:selected', this).text();

          //capturamos el total importe del servicio
          var importe = $('.total', this).val();

          //valida que se ha hecho un incremento
          let hrServicioActuales = 0;
          var esIncremento = productosActualesSocio.some(function (prod) {
            hrServicioActuales = prod.total_horas;
            return prod.id_servicio == idProducto && prod.total_horas != hrServicio;
          });

          // evalua si el producto actual es uno de de los que tiene incremento
          if (![6, 7, 10, 13, 18, 21, 22, 23, 29, 30, 31, 32, 33, 34, 35, 38, 39, 40, 41, 42, 50, 51, 23, 24, 25, 26, 27, 28].includes(Number(idProducto)) &&
            (esIncremento || !idProductosActuales.includes(idProducto))) {
            nuevoProducto = true;
            let base = false;
            let hrSanatorio = null;
            let valores_servicios = 0;

            //validacion de select vacios
            if (idProducto == '0' || idProducto == null || idProducto == undefined) {
              $('.error-productos-socio').text('').append('Debe seleccionar un servicio');
              $('#error-productos-socio').show().fadeOut(10000);
              return (retorno = false);
            } else if ((idProducto == '2' || idProducto == '3') && (hrServicio == '0' || hrServicio == null || hrServicio == undefined)) {
              $('.error-productos-socio').text('').append('Debe seleccionar las horas del servicio');
              $('#error-productos-socio').show().fadeOut(10000);
              return (retorno = false);
            }

            //validamos si de verdad hay horas sanatorio
            if (hrSanatorio == null || hrSanatorio == undefined) {
              hrSanatorio = null;
              base = false;
            }
            //validamos si de verdad hay horas servicio contrato
            if (hrServicio == null || hrServicio == undefined) {
              hrServicio = null;
            }

            if (idProducto == '1') {
              hrSanatorio = hrServicio;
              hrServicio = null;
              base = $(this).find("option:selected").data("base") == "1" ? true : false;
            }

            if ([22, 29, 30, 31, 32, 36, 33, 34, 35, 38, 39, 40, 41, 42].includes(Number(idProducto)) &&
              (arrIdIncrementos.indexOf(idProducto) === -1 || idProductosActuales.indexOf(idProducto) !== -1) &&
              [1, 2, 3, 5].indexOf(idProducto) !== -1) {
              $productos += `<p><span class='text-muted'>${nombreProducto}: </span> $U 0</p>`;
            } else {
              //calcula el precio de las horas viejas
              if (esIncremento) {
                let horasIncrementadas = 0;
                if (idProducto != '5') {
                  if (hrServicio) {
                    hrServicio = Math.abs(hrServicio - hrServicioActuales);
                    horasIncrementadas = hrServicio;
                  } else {
                    hrSanatorio = Math.abs(hrSanatorio - hrServicioActuales);
                    horasIncrementadas = hrSanatorio;
                  }
                }

                valores_servicios = calcularPrecio(idProducto, hrSanatorio, hrServicio, localidad, fecha_nacimiento, base);
                precio += Number(importe) + Number(valores_servicios[2]);
                $productos += `
                  <div>
                    <p>
                      <span class='text-uppercase font-weight-bold text-success'>${nombreProducto} </span>
                      <span class='text-muted'>(${Number(hrServicioActuales) + Number(horasIncrementadas)} hs): </span>
                      <span class='text-uppercase font-weight-bold'>$UY ${Number(importe) + Number(valores_servicios[2])}</span>
                    </p>
                    <p class='text-muted'>Horas actuales: ${hrServicioActuales} hs</p>
                    <p class='text-muted'>Horas incrementadas: ${horasIncrementadas} hs</p>
                  </div>
                `;
              } else {
                //enviamos los datos al web service para que calcule el precio del servicio
                valores_servicios = calcularPrecio(idProducto, hrSanatorio, hrServicio, localidad, fecha_nacimiento, base);

                //acumulamos el valor
                precio += valores_servicios[2];

                let cantidadHoras = hrServicio ? `(${hrServicio} hs)` : hrSanatorio ? `(${hrSanatorio} hs)` : '';

                $productos += `
                  <p>
                    <span class='text-uppercase font-weight-bold ${nuevoProducto === true ? 'text-primary' : ''}'>${nombreProducto} ${cantidadHoras}: </span>
                    $UY ${valores_servicios[2]}
                  </p>
                `;
              }

              //array con los datos de los servicios: parametros = id,horas servicio, precio total, precio base, precio del servicio. observacion,promo
              $arrServicios[c] = [idProducto, horass, valores_servicios[2], valores_servicios[0], valores_servicios[1], $observacion, promo];
              c++;
            }
          } else {
            // validamos si de verdad hay horas sanatorio
            if (hrSanatorio == null || hrSanatorio == undefined) {
              hrSanatorio = null;
              base = false;
            }
            //validamos si de verdad hay horas servicio contrato
            if (hrServicio == null || hrServicio == undefined) {
              hrServicio = null;
            }

            if (idProducto == '1') {
              hrSanatorio = hrServicio;
              hrServicio = null;
              base = true;
            }
            // acumulamos el valor
            // valores_servicios = calcularPrecio(idProducto, hrSanatorio, hrServicio, datosSocio.localidad, fecha_nacimiento, base);
            // precio += valores_servicios[2];
            precio += Number(importe);

            //let productoEliminado = false;
            let cantidadHoras = hrSanatorio;
            cantidadHoras = cantidadHoras ? `(${cantidadHoras} hs)` : hrServicio ? `(${hrServicio} hs)` : '';
            let productoDetalles = `<span class='text-uppercase font-weight-bold text-muted'>${nombreProducto} ${cantidadHoras}: </span> $UY ${importe}`;
            /*if ([22, 30, 31, 32, 36, 33, 34, 35, 38, 39, 40, 41].includes(Number(idProducto))) {
              precio -= Number(importe);
              productoEliminado = true;
              idProductoEliminar.push(idProducto);
              productoDetalles = `
                <span class='text-uppercase font-weight-bold text-danger'>${nombreProducto} ${cantidadHoras}: </span>
                <span class="text-danger text-uppercase">ATENCIÓN - ESTE SERVICIO SERA ELIMINADO</span>
              `;
            }*/

            $productos += `
              <p>
                ${productoDetalles} 
              </p>
            `;
          }
        });

        if (($('#hrsanatorio').is(':visible')) && (sanatorio_socio !== undefined && sanatorio_socio != null)) {
          let valores_servicios = calcularPrecio('1', sanatorio_socio, null, localidad, fecha_nacimiento, true);
          let comentario_sanatorio_socio = $('#divComentarioSanatorioSocio').val();
          let promo_sanatorio_socio = $('#promoSanatorioSocio').val();
          precio += valores_servicios[2];
          $arrServicios[c] = ['1', sanatorio_socio, valores_servicios[2], valores_servicios[0], valores_servicios[1], comentario_sanatorio_socio, promo_sanatorio_socio];
          $productos += `<p><span class='text-uppercase font-weight-bold text-primary'>SANATORIO: (${sanatorio_socio} hs) </span> $U ${valores_servicios[2]}</p>`;

        } else if (($('#hrsanatorio').is(':visible')) && (sanatorio_socio == undefined || sanatorio_socio == null)) {
          $('.error-productos-socio').text('Debe seleccionar las horas sanatorio');
          $('#error-productos-socio').show().fadeOut(10000);
          return (retorno = false);
        }

        $total = precio;
        $('#spanPrecio').text('$UY ' + $total);

        validLimite = $arrServicios.filter(function (x) { return x[0] == '1' });

        acotados = $arrServicios.filter(function (x) { return (PRODUCTOS_ACOTADOS.includes(x[0])) });
        let horas_sanatorio = (validLimite.length > 0) ? Number(validLimite[0][1]) : false;

        //validamos si hay acotados y horas sanatorio que no exedan el limite de 24 hrs (en caso de acotados que cuententen como sanatorio)
        if (validLimite != false && acotados.length > 0 && horas_sanatorio > 16) {
          $('.error-productos-socio').html('Ya llegó al limite de horas sanatorio permitidas');
          $('#error-productos-socio').show().fadeOut(10000);
          retorno = false;
        } else if (retorno) {
          localStorage.setItem('total', precio);
          localStorage.setItem('servicios', JSON.stringify($arrServicios));
          cambiarPagina(pagina);
        }


      } else {
        $('.error-productos-socio').html('Debe ingresar una observación');
        $('#error-productos-socio').show().fadeOut(10000);
      }
  }
}

function validarFuncionario(titularConvenio) {
  let retorno = [true, ''];

  $.ajax({
    type: "POST",
    url: "Ajax/validarFuncionario.php",
    data: { cedula: titularConvenio },
    dataType: "json",
    async: false,
    success: function (response) {
      if (!response.result) {
        retorno[0] = false;
        retorno[1] = response.message;

      } else {
        retorno[1] = response.message;

      }
    }
  });

  return retorno;
}

function finalizarVenta(e) {
  e.preventDefault();
  $medio_pago = $('#convenios option:selected').data('idmetodo');
  const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
  const convenioActual = Number(datosSocio.idconvenio);
  const medioPago = Number($('#convenios option:selected').data('idmetodo'));
  const idconvenio = Number($('#convenios').val());
  const titularConvenio = $('#cedTitConvenio').val();
  let error = false;

  let funcionario_valido = (titularConvenio != '' || titularConvenio != null) ? validarFuncionario(titularConvenio) : [true, ''];

  if (!medioPago) {
    $('#error-metodo-pago').text('Error: Selecciona un método de pago').show().fadeOut(10000);
    error = true;
  } else if (idconvenio == 16 && $total > 3000) {
    $('#error-metodo-pago').text('Error: El límite de cuenta para éste método de pago es de 3000 pesos').show().fadeOut(10000);
    error = true;
  } else if (datosSocio.socio && (medioPago == 3 && convenioActual == 16) && idconvenio != 20) {

    let medioPagoActual = Number(datosSocio.medio_pago);
    $('#error-metodo-pago').text('Error: Solo puede incrementar con trajeta de crédito').show().fadeOut(10000);
    error = true;
  } else if (datosSocio.socio == false && idconvenio == 13 && !funcionario_valido[0]) {
    $('#error-metodo-pago').text(funcionario_valido[1]).show().fadeOut(10000);
    error = true;
  } else if ((medioPago == 3 && idconvenio == 22) && !validarTelefonoAntel($tel)) {
    $('#error-metodo-pago').text('Error: Debe ingresar un número de teléfono fijo ANTEL válido').show().fadeOut(10000);
    error = true;
  } else {

    const $departamento = $dep ? $('#depBen > option:selected').text() : '-';
    const $localidad = $loc ? $('#locBen > option:selected').text() : '-';
    const $error = $('#error-convenio');

    if (idconvenio == 13 && datosSocio.socio == false) {
      $total = 0;
      $arrServicios.forEach(function (val, index) {

        let total_anterior = val[1];
        if (val[0] == '1') {
          $arrServicios[index][2] = Math.ceil((val[2] - (val[2] * 0.10)));
          $arrServicios[index][3] = Math.ceil((val[3] - (val[3] * 0.10)));
          $productos += `<p><span class='text-primary'>Sanatorio (10% descuento funcionario): </span>  $U ${$arrServicios[index][2]}</p>`;
        }

        $total += Number(val[2]);
      });
    }



    let datos = `<h4>DATOS PERSONALES</h4>
    <p><span class='text-muted'>Nombre: </span>${$nombre}</p>
    <p><span class='text-muted'>Cédula: </span>${$cedula}</p>
    <p><span class='text-muted'>Fecha de nacimiento: </span>${$dia}/${$mes}/${$anio}</p>
    <p><span class='text-muted'>Dirección: </span>${$direccion}</p>
    <p><span class='text-muted'>Celular: </span>${$cel}</p>
    <p><span class='text-muted'>Télefono fijo: </span>${$tel}</p>
    <p><span class='text-muted'>Télefono alternativo: </span>${$telAlternativo}</p>
    <p><span class='text-muted'>Correo electronico: </span>${$mail ? $mail : '-'}</p>
    <p><span class='text-muted'>Departamento: </span>${$departamento}</p>
    <p><span class='text-muted'>Localidad: </span>${$localidad}</p>`;
    localStorage.setItem(
      'dataVenta',
      JSON.stringify({
        nombre: $nombre,
        cedula: $cedula,
        celular: $cel,
        telefono: $tel,
        telefonoAlternativo: $telAlternativo,
        mail: $mail,
        mailTitular: $mailTit,
        direccion: $direccion,
        fechaNacimiento: $anio + '-' + $mes + '-' + $dia,
        departamento: $dep,
        localidad: $loc ? $loc : datosSocio.localidad,
        datoExtra: $datoExtra,
        filial: $filial,
        observacion: $observacion,
        numeroTarjeta: $numTarjeta,
        cvv: $cvv,
        mesVencimiento: $mesVencimiento,
        anioVencimiento: $anoVencimiento,
        nombreTitular: $nombreTit,
        cedulaTitular: $cedulaTit,
        celularTitular: $celularTit,
        servicios: $arrServicios,
        medio_pago: $('#convenios option:selected').data('idmetodo'),
        is_mercadopago: localStorage.getItem('mercadopago'),
        tipo_tarjeta: localStorage.getItem('tipo_tarjeta'),
        total: $total,
        cedulaTitularConvenio: '',
        nombreTitularConvenio: '',
        beneficiarios: $arrBeneficiarios
      })
    );

    // COBRADOR
    if (medioPago === 1) {
      $('#is_mercadopago').val('0');
      $('#pasotres').hide();
      limpiarInputsDatosTarjeta();
      // CONVENIO
    } else if (medioPago === 3) {
      //convenios que requieren datos personales del titular
      let convenios = [2, 3, 4, 7, 8, 10, 11, 13, 15, 16];
      if (!$('#convenios').val()) {
        $('#error-metodo-pago .error-metodo-pago').html('Selecciona un convenio');
        $('#error-metodo-pago').show().fadeOut(5000);
        error = true;
      } else {

        // convenio que requiere cédula y nombre del titular
        if (convenios.includes(idconvenio)) {
          const $cedulaTitularConvenio = $('#cedTitConvenio').val().toUpperCase();
          const $nombreTitularConvenio = $('#nomTitConvenio').val().toUpperCase();
          let message = '';

          if ($cedulaTitularConvenio === '') {
            message += 'Cédula del titular requerida - ';
          } else if (!comprobarCI($cedulaTitularConvenio)) {
            message += 'Cédula incorrecta - ';
          }
          if (!$nombreTitularConvenio) {
            message += 'Nombre del titular requerido';
          }

          if (message) {
            $('#error-convenio .error-convenio').html(message);
            $('#error-convenio').show().fadeOut(10000);
            error = true;
          } else {
            localStorage.cedulaTitularConvenio = $cedulaTitularConvenio;
            localStorage.nombreTitularConvenio = $nombreTitularConvenio;
            datos += `
                <p><span class='text-muted'>Cédula del titular convenio: </span>${$cedulaTitularConvenio}</p>
                <p><span class='text-muted'>Nombre del titular convenio: </span>${$nombreTitularConvenio}</p>
              `;
          }
        }
      }
    } else if (medioPago == 2 && idconvenio === 20) {
      // JSON.parse(localStorage.datos_socio).socio && completarDatosTarjetaSocio();
      listarBancosEmisores();
      limpiarDatosConvenio();
      $('#pasotres').show();
      $('#modal-datos-cliente').modal('hide');
      $('#validacion_medio_pago').hide();
      $('#divConvenios').hide();
      listarBancosEmisores();


    }

    if (!error && medioPago !== 2) {

      if (medioPago === 5) {
        message = `<hr>
          <h4>MEDIO DE PAGO SELECCIONADO</h4>
          <p>Nueva Tarjeta D</p>
        <hr>`;
      }

      $('#modal-confirmacion-venta .modal-body').html(
        `${datos}
          <hr>
          <h4>SERVICIOS</h4>
          ${$productos}
          <p><span class='text-success'>* INCREMENTOS</span></p>
          <p><span class='text-primary'>* NUEVOS SERVICIOS</span></p>
          <hr>
          ${message}
          <p class='text-uppercase font-weight-bold' style='font-size: 2.3rem;'>TOTAL: $UY ${$total}</p>
          `
      );
      $('#modal-confirmacion-venta').show();
    }
  }
}

function confirmarVenta(e) {
  e.preventDefault();
  let data = {};
  const dataVenta = JSON.parse(localStorage.getItem('dataVenta'));
  data = {
    socio: false,
    nombre: dataVenta.nombre,
    cedula: dataVenta.cedula,
    celular: dataVenta.celular,
    telefono: dataVenta.telefono,
    telefonoAlternativo: dataVenta.telefonoAlternativo,
    mail: dataVenta.mail,
    direccion: dataVenta.direccion,
    fechaNacimiento: dataVenta.fechaNacimiento,
    departamento: dataVenta.departamento,
    localidad: dataVenta.localidad,
    datoExtra: dataVenta.datoExtra,
    filial: dataVenta.filial,
    observacion: dataVenta.observacion,
    numeroTarjeta: dataVenta.numeroTarjeta,
    cvv: dataVenta.cvv,
    anioVencimiento: dataVenta.anioVencimiento,
    nombreTitular: dataVenta.nombreTitular ? dataVenta.nombreTitular : '',
    bancoEmisor: dataVenta.bancoEmisor ? dataVenta.bancoEmisor : '',
    cedulaTitular: dataVenta.cedulaTitular,
    celularTitular: dataVenta.celularTitular,
    telefonoTitular: dataVenta.telefonoTitular ? dataVenta.telefonoTitular : '',
    mailTitular: dataVenta.mailTitular,
    servicios: JSON.stringify($arrServicios),
    medio_pago: dataVenta.medio_pago,
    total: dataVenta.total,
    mesVencimiento: dataVenta.mesVencimiento,
    is_mercadopago: dataVenta.is_mercadopago,
    tipo_tarjeta: dataVenta.tipo_tarjeta,
    email_titular: dataVenta.mailTitular,
    cedulaTitularConvenio: dataVenta.cedulaTitularConvenio ? dataVenta.cedulaTitularConvenio : $('#cedTitConvenio').val().toUpperCase(),
    nombreTitularConvenio: dataVenta.nombreTitularConvenio ? dataVenta.nombreTitularConvenio : $('#nomTitConvenio').val().toUpperCase(),
    idConvenio: $('#convenios').val(),
    llamadaEntrante: localStorage.llamadaEntrante,
    beneficiarios: JSON.stringify($arrBeneficiarios)
  };

  if (JSON.parse(localStorage.getItem('datos_socio')).socio) {
    const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
    (data.id_padron = datosSocio.id_padron), (data.socio = true), (data.total = localStorage.getItem('total'));
    data.serviciosActuales = datosSocio.productos_detallados; //Servicios actuales del socio
    data.edad = datosSocio.edad;
    data.direccion = datosSocio.direccion;
    data.empresa_rut = datosSocio.empresa_rut;
    data.total_importe = datosSocio.total_importe;
    data.ruta = datosSocio.ruta;
    data.radio = datosSocio.radio;
    data.filial = dataVenta.filial;
    data.fechaFil = datosSocio.fechaFil;
    data.activo = datosSocio.activo;
    data.tarjeta = datosSocio.tarjeta;
    data.anio_e = datosSocio.anio_e;
    data.mes_e = datosSocio.mes_e;
    data.sucursal_cobranzas = datosSocio.sucursal_cobranzas;
    data.sucursal_cobranzas_num = datosSocio.sucursal_cobranzas_num;
    data.empresa_marca = datosSocio.empresa_marca;
    data.flag = datosSocio.flag;
    data.count = datosSocio.count;
    data.observaciones = dataVenta.observacion;
    data.grupo = datosSocio.grupo;
    data.idrelacion = datosSocio.idrelacion;
    data.total_importe = datosSocio.total_importe;
    data.nactual = datosSocio.nactual;
    data.version = datosSocio.version;
    data.flagchange = datosSocio.flagchange;
    data.rutcentralizado = datosSocio.rutcentralizado;
    data.print = datosSocio.print;
    data.emitido = datosSocio.emitido;
    data.movimientoabm = datosSocio.movimientoabm;
    data.abm = datosSocio.abm;
    data.abmactual = datosSocio.abmactual;
    data.no_modifica = datosSocio.no_modifica;
    data.check = datosSocio.check;
    data.usuario = datosSocio.usuario;
    data.usuariod = datosSocio.usuariod;
    data.radioViejo = datosSocio.radioViejo;
    data.extra = datosSocio.extra;
    data.nomodifica = datosSocio.nomodifica;
    data.localidadAnterior = datosSocio.localidad;
    data.email = datosSocio.email;
    data.nombreTitularAnterior = datosSocio.nombre_titular;
    data.numero_tarjeta = dataVenta.numeroTarjeta ? dataVenta.numeroTarjeta : datosSocio.numero_tarjeta;
    data.tipo_tarjeta = dataVenta.tipo_tarjeta ? dataVenta.tipo_tarjeta : datosSocio.tipo_tarjeta;
    data.tarjeta = dataVenta.tipo_tarjeta ? dataVenta.tipo_tarjeta : datosSocio.tipo_tarjeta;
    data.medioPagoActual = datosSocio.medio_pago;
    data.medio_pago = dataVenta.medio_pago;
  }

  $.ajax({
    url: 'Ajax/procesoVenta.php',
    data: data,
    dataType: 'json',
    method: 'POST',
    beforeSend: function () {
      $('#primaria').show();
      $('#btnConfirmarVenta').prop('disabled', true);
    },
    success: function (response) {
      $('#primaria').hide();
      if (response.result) {
        limpiarDatosCliente();
        limpiarInputsDatosTarjeta();
        limpiarInputsProductos();
        limpiarDatosConvenio();
        limpiarModalBeneficiarios();
        $('#modal-datos-cliente').hide();
        $('#modal-confirmacion-venta').hide();
        $('#validacion_medio_pago').hide();
        $('#pasotres').hide();
        $('#modal-venta').hide();
        $('.modal').modal('hide');
        $('#producto_socio').html('');
        reiniciarVariablesGlobales();
        mostrarValidacion();
        $(".importe-servicio").remove();
        // $("#primaria").hide();
        vendido();
        alert(response.message);
        localStorage.clear();
        $('#btnConfirmarVenta').prop('disabled', false);
      } else {
        alert(response.message);
      }
    },
    error: function (error) {
      console.log(error.responseText);
      alert('Ha ocurrido un error inesperado, para evitar errores NO vuelva a intentarlo y realice el reclamo correspondiente');
      $('#primaria').hide();
    }
  });
}

function recargarPagina() {
  limpiarDatosCliente();
  limpiarInputsDatosTarjeta();
  limpiarInputsProductos();
  localStorage.clear();
  location.reload();
}

function llenarCampos() {
  $('#nomBen').val('prueba prueba');
  $('#dirBen').val('direccion xxxxxxx');
  $('#mailBen').val('');
  $('#celBen').val('097612636');
  $('#telBen').val('2245454');
  $('#nataliciodia option:eq(1)').prop('selected', true);
  $('#nataliciomes option:eq(1)').prop('selected', true);
  $('#natalicioano option:eq(1)').prop('selected', true);
  $('#depBen option:eq(1)').prop('selected', true);
  $('#depBen').change();
  $('#locBen option:eq(1)').prop('selected', true);
}

function llenarDatosTarjeta() {
  $('#numTar').val('5896572016441234');
  $('#cedTit').val('89898989');
  $('#nomTit').val('petrona concha');
  $('#mesVen option:eq(1)').prop('selected', true);
  $('#anoVen option:eq(5)').prop('selected', true);
  $('#bancos option:eq(1)').prop('selected', true);
  $('#celTit').val('097612636');

}

function listarServiciosAdicionales(select) {
  var aValores = $('.produc')
    .map(function () {
      return $(this).val();
    })
    .get();

  $.ajax({
    type: 'POST',
    url: 'Ajax/listarServiciosAdicionales.php',
    data: { array: aValores, code: localStorage.getItem('code'), localidad: $filial },
    dataType: 'json',
    success: function (response) {
      var serv = response.servicios;
      serv.forEach(function (val, i) {
        if (val.id != 4 || (val.id == 4 && [8, 7, 17, 3, 16, 5, 2, 9, 6, 14].includes(Number($("#depBen").val())))) {
          $(select).append(`<option value="` + val.id + `">` + val.servicio + `</option>`);
        }
      });
    }
  });
}

function listarServiciosAdicionalesSocio(select) {
  var aValores = $('select.producto-socio')
    .map(function () {
      return $(this).val();
    })
    .get();

  $.ajax({
    type: 'POST',
    url: 'Ajax/listarServiciosAdicionales.php',
    data: {
      array: aValores,
      code: localStorage.getItem('code'),
      localidad: $filial
    },
    dataType: 'json',
    success: function (response) {
      var opciones = "";
      var serv = response.servicios;
      serv.forEach(function (val, i) {
        if (val.id != 4 || (val.id == 4 && [8, 7, 17, 3, 16, 5, 2, 9, 6, 14].includes(Number($("#depBen").val())))) {
          opciones += `<option value="` + val.id + `">` + val.servicio + `</option>`;
        }
      });
      $(select).append(opciones);
    }
  });

}

function maxLengthCheck(object) {
  if (object.value.length > object.maxLength) object.value = object.value.slice(0, object.maxLength);
}

function calcularPrecio(servicio, hrs_sanatorio, hrs_servicio, localidad, fecha_nacimiento, base) {
  $data = 'servicio=' + servicio + '&hrs_sanatorio=' + hrs_sanatorio + '&hrs_servicio=' + hrs_servicio + '&localidad=' + localidad + '&fecha_nacimiento=' + fecha_nacimiento + '&base=' + base;
  $arrPrecios = [];
  $.ajax({
    url: 'Ajax/calcularPrecio.php',
    data: $data,
    method: 'POST',
    dataType: 'json',
    async: false,
    success: function (content) {
      if (content.result) {
        $precio = content.precio;
        $arrPrecios[0] = content.precio_base;
        $arrPrecios[1] = content.precio_servicio;
        $arrPrecios[2] = content.precio;
      }
    },
    error: function () {
      $precio = false;
    }
  });
  return $arrPrecios;
}

function cambiarPagina(pagina) {
  const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
  switch (pagina) {
    case 0:
      mostrarValidacion();
      break;
    case 1:
      if (datosSocio.socio) {
        if ($(".sprod").last().val() == null) $("#btnQuitarServicioSocio").click();
        // listarProductosServiciosSocio(datosSocio.productos_socio);
        mostrarPasoUnoSocio();
      } else {
        mostrarPasoUno();
      }
      break;
    case 2:
      if (datosSocio.socio) {
        listarProductosServiciosSocio(datosSocio.productos_socio);
        mostrarPasoDosSocio();
      } else {
        mostrarPasoDos();
      }
      break;
    case 3:
      mostrarPasoTres();
      break;
    case 4:
      mostrarEleccionMedioPago();
      break;
    case 5:
      mostrarPasoDosAtras();
      break;
  }
}

function agregarServicioSocio(e) {
  e.preventDefault();
  ultimoServicio = $('select.producto-socio').last().val();
  if (ultimoServicio == null || ultimoServicio == '') {

    $('.error-productos-socio').text('Debe seleccionar un servicio');
    $('#error-productos-socio').show().fadeOut(10000);
  } else {
    //obtenemos los id de los servicios de todos los select de servicios
    var serviSelect = $('select.producto-socio').map(function () { return $(this).val(); }).get();

    //valores de ids a chequear
    var valoresAchequear = ['1', '2', '3', '5'];

    // let tieneOchoHoras = "<option value='8'>8 hs</option>";
    // // Compruebo si selecciono sanatorio
    // if ($('select.producto-socio').last().val() == "1" && tieneAcotados()) tieneOchoHoras = '';

    //guardamos en una variable el resultado del chequeo
    // var check= serviSelect.some(e => valoresAchequear.includes(e));

    //definimos el div que contendra los select del servicio adicional
    var select =
      `<div class="row" id="row_sanatorio_socio"></div>
      <div class="row productos_socio" id="hrserviciossocio` +
      $addServiSocio +
      `">
        <div class="col-md-4">
          <div class="form-group">
            <label for="" class="texto" >Servicios <span class="requerido">*</span></label>
              <select id="productosocio` +
      $addServiSocio +
      `" name="productosocio` +
      $addServiSocio +
      `" class="custom-select form-control producto-socio sprod">
                <option value="" selected disabled>Seleccione servicio</option>
              </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <div id="divHorasServiciosocio` +
      $addServiSocio +
      `" class="divHorasServicioSocio hrserviciosocio productosocio` +
      $addServiSocio +
      `" style="display:none">
              <label for="" class="texto">Horas servicio <span class="requerido">*</span></label>
              <select id="hrservicio" name="hrservicio" class="custom-select form-control hservicio">
                  <option value="0" selected disabled>Seleccione cantidad</option>
                  <option value="8" data-base="0">8 hs</option>
                  <option value="16" data-base="0">16 hs</option>
                  <option value="24" data-base="0">24 hs</option>
              </select>
              <input type="hidden" class="importe-servicio"/>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <div id="divPromoSocio" class="divPromoSocio" style="display: none;>
              <label for="" class="texto" >Promo</label>
              <select id="promoSocio" name="promoSocio" class="custom-select form-control promo">
                  <option value="0" selected disabled>Seleccione promo</option>
                  <option value="20">NP17</option>
              </select>
              <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
            </div>
          </div>   
        </div>
      </div>`;

    $('#servis-socio').append(select);
    var elemento = '#hserviciossocio' + $divp;
    var s = '#productosocio' + $addServiSocio;
    listarServiciosAdicionalesSocio(s);
    EventoChangeProductosAdicionalesSocio();
    $addServiSocio++;
    $divp++;
  }
}
/* ############################################################################################### */
$addBen = 1;

function agregarBeneficiario() {

  let idproducto = $('#producto').val();

  if ((idproducto == '25' && $addBen < 4) || (idproducto == '27' && $addBen < 5)) {
    $addBen++;
    let row_ben = `
    <div class="row beneficiario" id="beneficiario${$addBen}">
    <hr>
    <div class="col-md-4">
      <div class="form-group">
      <label for="">Nombre</label>
        <input type="text" class="form-control nombre_ben solo_letras" value="" name="" id="">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
      <label for="">Cédula</label>
        <input type="text" class="form-control cedula_ben solo_numeros" maxlength="8" value="" name="" id="">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
      <label for="">Teléfono</label>
        <input type="text" class="form-control telefono_ben solo_numeros" value="" name="" id="">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
      <label for="">Fecha de nacimiento</label>
        <input type="text" class="form-control fn_beneficiario fechan_ben" value="" name="" id="">
      </div>
    </div>
    </div>`;

    $('.beneficiarios-extra').append(row_ben);
    $('#beneficiario' + $addBen + ' .fn_beneficiario').datetimepicker({ format: 'Y-m-d', timepicker: false });

  } else {
    $('.error-beneficiarios').text('Ha llegado al máximo de beneficiarios para este servicio');
    $('#error-beneficiarios').show().fadeOut(10000);
  }

}

function quitarBeneficiario() {
  if ($addBen > 1) {
    $('#beneficiario' + $addBen).remove();
    $addBen--;
  }
}

function guardarBeneficiarios() {
  let error = false;
  let socio = null;
  let idproducto = $('#producto').val();
  let mensaje = '';
  $('.beneficiario').each(function (index) {
    let nombre_ben = $('.nombre_ben', this).val().toUpperCase();
    let cedula_ben = $('.cedula_ben', this).val();
    let telefono_ben = $('.telefono_ben', this).val();
    let fechan_ben = $('.fechan_ben', this).val();
    let edad = calcularEdad(fechan_ben);

    socio = existeEnPadron(cedula_ben);

    if (nombre_ben == '' || cedula_ben == '' || telefono_ben == '' || fechan_ben == '') {
      error = true;
      mensaje = 'Debe completar todos los datos del beneficiario con la cedula ' + cedula_ben;
    } else if (!comprobarCI(cedula_ben)) {
      error = true;
      mensaje = 'La cédula ' + cedula_ben + ' es incorrecta';
    } else if (edad < 18) {
      error = true;
      mensaje = 'El beneficiario con la cédula ' + cedula_ben + ' es menor de edad';
    } else if (socio[0]) {
      error = true;
      mensaje = socio[1];
    } else {
      $arrBeneficiarios[index] = [nombre_ben, cedula_ben, telefono_ben, fechan_ben, edad];
    }
  });

  let num_servicio = $('#producto').val();
  let valEdades = validarEdades(num_servicio);

  if (valEdades[0]) {
    $('.error-beneficiarios').text(valEdades[1]);
    $('#error-beneficiarios').show().fadeOut(10000);
    $arrBeneficiarios.length = 0;
  } else if (error) {
    $('.error-beneficiarios').text(mensaje);
    $('#error-beneficiarios').show().fadeOut(10000);
    $arrBeneficiarios.length = 0;
  } else {
    // limpiarModalBeneficiarios();
    $('#modal-agregar-beneficiarios').modal('hide');
  }
}

function validarEdades(num_servicio) {
  //G5                          //G6
  //5 pers. entre 18 - 49       //5 pers. entre 18 - 49
  //2 pers. entre 50 - 65       //2 pers. entre 50 - 65
  //1 pers>65                   //2 pers>65

  let edades = $('.fechan_ben').map(function () {
    return calcularEdad($(this).val());
  }).get();

  edades.push($edad);

  let franja1 = 0;
  let franja2 = 0;
  let franja3 = 0;
  let error = false;
  let message = '';

  edades.forEach(function (val) {
    if (val >= 18 && val < 49) {
      franja1++;
    } else if (val > 49 && val < 66) {
      franja2++;
    } else if (val > 65) {
      franja3++;
    }
  });

  if (num_servicio == '25' && franja3 > 1) {
    message = 'El grupo familiar G5 solo admite 1 persona mayor a 65 años';
    error = true;
  } else if (num_servicio == '25' && franja2 > 2) {
    message = 'El grupo familiar G5 solo admite 2 personas con edades entre 50 y 65 años';
    error = true;
  } else if (num_servicio == '27' && franja2 > 2) {
    message = 'El grupo familiar G6 solo admite 2 personas con edades entre 50 y 65 años';
    error = true;
  } else if (num_servicio == '27' && franja3 > 2) {
    message = 'El grupo familiar G6 solo admite 2 personas mayor a 65 años';
    error = true;
  }

  return [error, message];
}

function existeEnPadron(cedula) {
  let retorno = [];
  $.ajax({
    type: "POST",
    url: "Ajax/validarExisteEnPadron.php",
    data: { cedula: cedula },
    dataType: "json",
    async: false,
    success: function (response) {
      if (response.result) {
        retorno = [response.existe, response.message];
      }
    }
  });

  return retorno;
}

function calcularEdad(fecha_nacimiento) {
  let fecha = new Date(fecha_nacimiento);
  let hoy = new Date();
  let mes = hoy.getMonth();
  let dia = hoy.getDate();
  let año = hoy.getFullYear();
  hoy.setDate(dia);
  hoy.setMonth(mes);
  hoy.setFullYear(año);
  return Math.floor((hoy - fecha) / (1000 * 60 * 60 * 24) / 365);
}

/* ############################################################################################### */

function listarProductosServiciosSocio(productosSocio) {
  let template = '';

  $(".importe-servicio").val("");

  for (let i = 0; i < productosSocio.length; i++) {
    let divhorasServicio = '';
    if (!PRODUCTOS_ACOTADOS.includes(+productosSocio[i].num_servicio)) {
      let horasServicio = '';
      let importe = 0;
      for (var x = +productosSocio[i].total_horas; x <= 24; x += 8) {
        horasServicio += `
          <option value="${x}" ${x === productosSocio[i].$total_horas ? 'selected' : ''}>${x}hs</option>
        `;
      }

      divhorasServicio = `<div class="col-md-4">
                            <div class="form-group">
                              <div id="divHorasServicioSocio" class="divHorasServicioSocio">
                                <label for="" class="texto" >Horas servicio <span class="requerido">*</span> </label>
                                  <select id="hrservicio" name="hrservicio" class="custom-select form-control hservicio">
                                      ${horasServicio}
                                  </select>
                              </div>
                            </div>
                          </div>`;
    }

    template += `<div class='row productos_socio'>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="" class="texto" >Servicio </label>
                        <select name="producto" class="custom-select form-control producto-socio">
                        <option value="${productosSocio[i].id_servicio}" selected>${productosSocio[i].servicio}</option>
                        </select>
                        <input type="hidden" class="importe-servicio"  value="${productosSocio[i].importe}"/>
                      </div>
                    </div>
                    ${divhorasServicio}
                    <input type="hidden" class="total" name="total_importe" id="" value="${productosSocio[i].importe}"/>
                  </div>`;

  }

  $('#producto_socio').html(`${template} <hr><h4>NUEVOS SERVICIOS</h4>`);
  mostrarSubTotal(calcularSubTotal());
  if ($('.sprod').length) {
    $('.hservicio').change();
    $('.hsanatorio').change();
  }
}

function validarCedulaPadron() {
  var cedula = $.trim($('#cedBen').val());
  $('#btnSiguiente1').show();
  $('#error-datos').hide();
  if (!cedula) {
    alert('Debe ingresar una cédula');
  } else if (comprobarCI(cedula)) {
    $.ajax({
      type: 'POST',
      url: 'Ajax/validarPadron.php',
      data: { cedula: cedula },
      dataType: 'json',
      success: function (response) {
        if (response.result) {
          // Este codigo permite comprobar cual servicio/producto podra ser ofrecido
          localStorage.setItem('code', response.code);
          if (response.code == 2) {
            $('.error-validacion-cedula').text('La persona se encuentra en Clering y no se puede afiliar');
            $('#error-validacion-cedula').show().fadeOut(10000);
          } else if (response.socio) {
            localStorage.setItem(
              'datos_socio',
              JSON.stringify({
                departamento: response.datos_socio.departamento,
                id_padron: response.datos_socio.id_padron,
                nombre: response.datos_socio.nombre,
                cedula: response.datos_socio.cedula,
                fecha_nacimiento: response.datos_socio.fechaNacimiento,
                telefono: response.datos_socio.telefono,
                edad: response.datos_socio.edad,
                direccion: response.datos_socio.direccion,
                filial: response.datos_socio.filial,
                fechaFil: response.datos_socio.fechaFil,
                empresa_rut: response.datos_socio.empresa_rut,
                ruta: response.datos_socio.ruta,
                radio: response.datos_socio.radio,
                activo: response.datos_socio.activo,
                tarjeta: response.datos_socio.tarjeta,
                banco: response.datos_socio.banco,
                numero_tarjeta: response.datos_socio.numero_tarjeta,
                nombre_titular: response.datos_socio.nombre_titular,
                email_titular: response.datos_socio.email_titular,
                cedula_titular: response.datos_socio.cedula_titular,
                tipo_tarjeta: response.datos_socio.tipo_tarjeta,
                telefono_titular: response.datos_socio.telefono_titular,
                anio_e: response.datos_socio.anio_e,
                mes_e: response.datos_socio.mes_e,
                sucursal_cobranzas: response.datos_socio.sucursal_cobranzas,
                sucursal_cobranzas_num: response.datos_socio.sucursal_cobranza_num,
                empresa_marca: response.datos_socio.empresa_marca,
                flag: response.datos_socio.flag,
                count: response.datos_socio.count,
                observaciones: response.datos_socio.observaciones,
                grupo: response.datos_socio.grupo,
                idrelacion: response.datos_socio.idrelacion,
                total_importe: response.datos_socio.total_importe,
                nactual: response.datos_socio.nactual,
                version: response.datos_socio.version,
                flagchange: response.datos_socio.flagchange,
                rutcentralizado: response.datos_socio.rutcentralizado,
                print: response.datos_socio.print,
                emitido: response.datos_socio.emitido,
                movimientoabm: response.datos_socio.movimientoabm,
                abm: response.datos_socio.abm,
                abmactual: response.datos_socio.abmactual,
                check: response.datos_socio.check,
                usuario: response.datos_socio.usuario,
                usuariod: response.datos_socio.usuariod,
                radioViejo: response.datos_socio.radioViejo,
                extra: response.datos_socio.extra,
                nomodifica: response.datos_socio.nomodifica,
                productos_socio: response.productos_socio,
                productos_detallados: response.productos_socio_detallados,
                localidad: response.datos_socio.localidad,
                email: response.datos_socio.email,
                medio_pago: response.datos_socio.medio_pago,
                socio: response.socio,
                idconvenio: response.datos_socio.idconvenio
              })
            );
            $('#divDatosAdicionales').hide();
            mostrarPasoUnoSocio();

            const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
            if (datosSocio.productos_socio[0].cod_promo == 20) {
              $('#btnSiguiente1').hide();
              $('.error-datos').prev().text(`ATENCIÓN: `);
              $('.error-datos').text(
                `El socio con CI: ${cedula} no puede realizar incrementos.`
              );
              $('#error-datos').show();
            }
          } else {
            localStorage.setItem(
              'datos_socio',
              JSON.stringify({
                socio: response.socio
              })
            );
            mostrarPasoUno();
            $('#divDatosAdicionales').show();

            // $('#pasouno').show()
            // $('#validacion_cedula').hide();
            $('#cedBen2').val(cedula);
            $('#cedBen2').prop('disabled', true);
          }
        } else if (!response.result) {
          $('.error-validacion-cedula').text(response.message);
          $('#error-validacion-cedula').show().fadeOut(10000);
        }
      }
    });
  } else {
    $('.error-validacion-cedula').text('La cedula es incorrecta');
    $('#error-validacion-cedula').show().fadeOut(10000);

  }
}

function validarTelefonoPadron(cel, tel = '') {
  let result = false;
  let message = '';
  $.ajax({
    type: 'POST',
    url: 'Ajax/validarTelefonosPadron.php',
    data: { celu: cel, telefono: tel, cedula: $cedula },
    async: false,
    dataType: 'json',
    success: function (response) {
      if (!response.celular) {
        message = 'El celular ya pertenece a otro socio';
        result = true;
      } else if (!response.telefono) {
        message = 'El teléfono fijo ya pertenece a otro socio';
        result = true;
      }
    }
  });
  return [result, message];
}

function validarTelefonoAntel(telefono) {
  let regex = new RegExp('^((2|4)[0-9]{7})$');
  return telefono.match(regex);
}

function obtenerDepartamentos() {
  if (!$('#depBen').val() && !$('#locBen').val()) {
    $.ajax({
      type: 'POST',
      url: 'Ajax/obtenerDepartamentos.php',
      dataType: 'json',
      success: function (response) {
        if (response.result) {
          var depas = response.departamentos;
          depas.forEach(function (val, i) {
            $('#depBen').append(`<option value="` + val.id + `">` + val.departamento + `</option>`);
          });
          const datosSocio = JSON.parse(localStorage.datos_socio)
          if (datosSocio.socio) {
            $(`#depBen option[value="${datosSocio.departamento}"]`).prop('selected', true);
            $('#depBen').change();
          }
          // Localidad
          if ($loc) {
            const localidad = document.querySelector(`#locBen option[value="${$loc}"]`);
            if (localidad) localidad.setAttribute("selected", true);
          }
        }
      }
    });
  }
}

function listarServicios() {
  $.ajax({
    type: 'POST',
    url: 'Ajax/listarServicios.php',
    dataType: 'json',
    data: { code: localStorage.getItem('code'), localidad: $filial },
    success: function (response) {
      $('#producto').html("");
      var opciones = `<option value="" selected>- Seleccione -</option>`;
      var serv = response.servicios;
      serv.forEach(function (val, i) {
        if (val.id != 4 || (val.id == 4 && [8, 7, 17, 3, 16, 5, 2, 9, 6, 14].includes(Number($("#depBen").val())))) {
          opciones += `<option value="` + val.id + `">` + val.servicio + `</option>`;
        }
      });
      $('#producto').append(opciones);
    }
  });
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function login() {
  $loginForm = $('#login-form');
  $usuario = $('#usuario').val();
  $contrasena = $('#contrasena').val();
  if ($usuario == '') {
    $('#lblLogin').html("<img src='img/error.png' width='15px'> Ingrese Usuario !");
    $('#usuario').css('background-color', '#FFCECF');
  } else if ($contrasena == '') {
    $('#lblLogin').html("<img src='img/error.png' width='15px'> Ingrese Contrasena !");
    $('#contrasena').css('background-color', '#FFCECF');
    $('#usuario').css('background-color', 'white');
  } else {
    $('#lblLogin').html('');
    var $form = $loginForm;
    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoLogin.php',
      data: $data,
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          window.location.href = 'index.php';
        } else {
          $('#lblLogin').html('<img src="img/error.png" width="15px"> Usuario y/o contrasena incorrectos !!');
          $('#usuario').css('background-color', '#FFCECF');
          $('#contrasena').css('background-color', '#FFCECF');
        }
      },
      error: function () {
        $('#lblLogin').html("<img src='img/error.png' width='15px'> Ocurrió un error,<br> intente en un momento.");
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function numeroInicial() {
  $('#primaria').css('display', 'block');
  $indexForm = $('#index-form');
  var $form = $indexForm;

  var $data = $form.serialize();
  var ua = navigator.userAgent.toLowerCase();

  $.ajax({
    url: 'ajax/procesoInicial.php',
    data: $data,
    method: 'POST',
    dataType: 'json',
    beforeSend: function () { },
    success: function (content) {
      $('#lblBienv').html(content.nombre);
      $('#lblGrupo').html(content.grupo);
      if (content.result) {
        if (content.flag && content.flag == 'agendado') {
          $('#btnReagendar').show();
          $('#num_agendado').val(content.numero);
          $('#btnReagendar').css('display', 'inline-block');
          $('#btnNoContesta').css('display', 'none');
        }
        habilitarBotones();
        buscarVendidos();
        // buscarAgendados();
        // buscarReferidos();
        // buscarReferidosCuaderno();
        // buscarReferidosPendientes();
        //buscarSumayGana();
        //refSumayGana();
        // buscarInfo();
        // listarAnotaciones();
        // limpiarLabels();
        inicializarElementos();
        $('#lblNumero').html(content.numero);
        $('#lblLocalidad').html(content.localidad);
        $('#primaria').css('display', 'none');
        if (content.agendado) {
          $('#divAlert').css('display', 'block');
        }
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else {
          buscarVendidos();
          deshabilitarBotones();
          inicializarElementos();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // buscarSumayGana();
          // refSumayGana();
          // buscarInfo();
          // limpiarLabels();
          $('#lblNumero').html('');
          $('#lblLocalidad').html('');
          $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
          $('#primaria').css('display', 'none');
          if (content.agendado) {
            $('#divAlert').css('display', 'block');
          }
        }
      }
    },
    error: function () {
      deshabilitarBotones();
      limpiarLabels();
      $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function vendido() {
  const data = {
    integrantesFamilia: $("#integrantesFamilia").val(),
    direccion: $("#direccion").val(),
    observacion2: $("#observacion2").val(),
    numero: localStorage.llamadaEntranteNumero,
    llamadaEntrante: localStorage.llamadaEntrante
  };

  $.ajax({
    url: 'ajax/procesoVendido.php',
    data: data,
    method: 'POST',
    dataType: 'json',
    beforeSend: function () { },
    success: function (content) {
      if (content.result) {
        buscarVendidos();
        $('#btnReagendar').hide();
        $('#btnNoContesta').css('display', 'inline-block');
        habilitarBotones();
        inicializarElementos();
        LimpiarInputs();
        $('#lblNumero').html(content.numero);
        $('#lblLocalidad').html(content.localidad);
        $('#divAtendio').hide();
        $('#integrantesFamilia').val('');
        $('#direccion').val('');
        $('#checkServicio').prop('checked', false);
        $('#servicio').val('');
        $('#observacion2').val('');
        $('#servicio').attr('disabled', true);
        $('#servicio').css('background-color', '#BCBCBC');
        $('#divAtendio').css('display', 'none');
        $('#primaria').css('display', 'none');
        $('.modal').modal('hide');
        return true;
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else {
          buscarAgendados();
          buscarReferidos();
          buscarReferidosCuaderno();
          buscarReferidosPendientes();
          limpiarNts();
          limpiarSv();
          limpiarTosnic();
          limpiarTosic();
          mostrarNts();
          buscarInfo();
          limpiarLabels();
          inicializarElementos();
          $('#lblNumero').html('');
          $('#lblLocalidad').html('');
          $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
          deshabilitarBotones();
          $('#divAtendio').hide();
          $('#integrantesFamilia').val('');
          $('#direccion').val('');
          $('#checkServicio').prop('checked', false);
          $('#servicio').val('');
          $('#observacion2').val('');
          $('#servicio').attr('disabled', true);
          $('#servicio').css('background-color', '#BCBCBC');
          $('#primaria').css('display', 'none');
        }
      }
    },
    error: function (error) {
      console.log(error);
      limpiarLabels();
      deshabilitarBotones();
      $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
  //}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function noInteresado() {
  $('#primaria').css('display', 'block');

  $integrantes = $('#integrantesFamilia').val();
  $direccion = $('#direccion').val();
  $servicio = $('#servicio').val();
  $observacion2 = $('#observacion2').val();

  if ($integrantes == '' || $direccion == '' || $observacion2 == '' || ($('#checkServicio').is(':checked') && $servicio == '')) {
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Complete todos los campos');
    $('#primaria').css('display', 'none');
  } else if (!/^([0-9])*$/.test($integrantes)) {
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Integrantes de familia debe ser un numero !!!');
    $('#primaria').css('display', 'none');
  } else {
    // $indexForm = $('#index-form');
    // var $form = $indexForm;

    // var $data = $form.serialize();
    // var ua = navigator.userAgent.toLowerCase();

    var $data = {
      integrantesFamilia: $integrantes,
      direccion: $direccion,
      servicio: $servicio,
      observacion2: $observacion2
    }
    $.ajax({
      url: 'ajax/procesoNoInteresado.php',
      data: $data,
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          $('#btnReagendar').hide();
          $('#btnNoContesta').show();
          habilitarBotones();
          inicializarElementos();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // limpiarNts();
          // limpiarSv();
          // limpiarTosnic();
          // limpiarTosic();
          // mostrarNts();
          // buscarInfo();
          // limpiarLabels();
          $('#lblNumero').html(content.numero);
          $('#lblLocalidad').html(content.localidad);
          $('#divAtendio').hide();
          $('#integrantesFamilia').val('');
          $('#direccion').val('');
          $('#checkServicio').prop('checked', false);
          $('#servicio').val('');
          $('#observacion2').val('');
          $('#servicio').attr('disabled', true);
          $('#servicio').css('background-color', '#BCBCBC');
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else {
            inicializarElementos();
            // buscarAgendados();
            // buscarReferidos();
            // buscarReferidosCuaderno();
            // buscarReferidosPendientes();
            // limpiarNts();
            // limpiarSv();
            // limpiarTosnic();
            // limpiarTosic();
            // mostrarNts();
            // buscarInfo();
            // limpiarLabels();
            $('#lblNumero').html('');
            $('#lblLocalidad').html('');
            $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
            deshabilitarBotones();
            $('#divAtendio').hide();
            $('#integrantesFamilia').val('');
            $('#direccion').val('');
            $('#checkServicio').prop('checked', false);
            $('#servicio').val('');
            $('#observacion2').val('');
            $('#servicio').attr('disabled', true);
            $('#servicio').css('background-color', '#BCBCBC');
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function () {
        limpiarLabels();
        $('#primaria').css('display', 'none');
        $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
        deshabilitarBotones();
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function noContesta() {
  $('#primaria').css('display', 'block');

  $indexForm = $('#index-form');
  var $form = $indexForm;

  var $data = $form.serialize();
  var ua = navigator.userAgent.toLowerCase();

  $.ajax({
    url: 'ajax/procesoNoContesta.php',
    data: $data,
    method: 'POST',
    dataType: 'json',
    beforeSend: function () { },
    success: function (content) {
      if (content.result) {
        $('#btnReagendar').hide();
        inicializarElementos();
        // buscarAgendados();
        // buscarReferidos();
        // buscarReferidosCuaderno();
        // buscarReferidosPendientes();
        // habilitarBotones();
        // buscarInfo();
        // limpiarLabels();
        $('#lblNumero').html(content.numero);
        $('#lblLocalidad').html(content.localidad);
        $('#primaria').css('display', 'none');
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else {
          inicializarElementos();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // deshabilitarBotones();
          // buscarInfo();
          // limpiarLabels();
          $('#lblNumero').html('');
          $('#lblLocalidad').html('');
          $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
          $('#primaria').css('display', 'none');
        }
      }
    },
    error: function () {
      deshabilitarBotones();
      limpiarLabels();
      $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function quitarPermanente() {
  $('#primaria').css('display', 'block');

  $obs = $('#observacion').val();
  if ($obs == '') {
    limpiarLabels();
    $('#lblErrorListaNegra').html('<img src="img/error.png" width="15px"> Ingrese observacion');
    $('#primaria').css('display', 'none');
  } else {
    $indexForm = $('#index-form');
    var $form = $indexForm;

    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoQuitarPermanente.php',
      data: { observacion: $obs },
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          $('#btnReagendar').hide();
          $('#btnNoContesta').show();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // buscarInfo();
          // limpiarLabels();
          habilitarBotones();
          buscarInfo();
          limpiarLabels();
          habilitarBotones();
          inicializarElementos();
          $('#lblNumero').html(content.numero);
          $('#lblLocalidad').html(content.localidad);
          $('#observacion').val('');
          $('#divNoLlamarMas').hide();
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else {
            inicializarElementos();
            // buscarAgendados();
            // buscarReferidos();
            // buscarReferidosCuaderno();
            // buscarReferidosPendientes();
            // deshabilitarBotones();
            // buscarInfo();
            // limpiarLabels();
            $('#lblNumero').html('');
            $('#lblLocalidad').html('');
            $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
            $('#observacion').val('');
            $('#divNoLlamarMas').hide();
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function () {
        deshabilitarBotones();
        limpiarLabels();
        $('#lblErrorListaNegra').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
        $('#primaria').css('display', 'none');
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agendado() {
  $('#primaria').css('display', 'block');
  $integrantes = $('#integrantesFamilia').val();
  $direccion = $('#direccion').val();
  $servicio = $('#servicio').val();
  $observacion2 = $('#observacion2').val();
  $nombre = $('#nom').val();
  $fecha = $('#datetimepicker').val();
  $horas = new Date($fecha);
  $horas = $horas.getHours();
  $comentario = $('#com').val();
  if ($nombre == '' || $fecha == '' || $comentario == '' || $integrantes == '' || $direccion == '' || $observacion2 == '' || ($('#checkServicio').is(':checked') && $servicio == '')) {
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Complete todos los campos');
    $('#primaria').css('display', 'none');
  } else if (!/^([0-9])*$/.test($integrantes)) {
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Integrantes de familia debe ser un numero !!!');
    $('#primaria').css('display', 'none');
  } else if ($horas < 9 || $horas > 21) {
    limpiarLabels();
    $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Seleccione una hora de agendado entre las 9hs y las 21:30hs !!!');
    $('#primaria').css('display', 'none');
  } else {
    $indexForm = $('#index-form');
    var $form = $indexForm;

    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoAgendar.php',
      data: {
        integrantesFamilia: $integrantes,
        direccion: $direccion,
        servicio: $servicio,
        observacion2: $observacion2,
        nom: $nombre,
        fec_hor: $fecha,
        com: $comentario
      },
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          inicializarElementos();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // habilitarBotones();
          // limpiarNts();
          // limpiarSv();
          // limpiarTosnic();
          // limpiarTosic();
          // mostrarNts();
          // buscarInfo();
          // limpiarLabels();
          $('#lblNumero').html(content.numero);
          $('#lblLocalidad').html(content.localidad);
          $('#divAtendio').hide();
          $('#nom').val('');
          $('#datetimepicker').val('');
          $('#com').val('');
          $('#integrantesFamilia').val('');
          $('#direccion').val('');
          $('#divSiAtendio2').hide();
          $('#checkServicio').prop('checked', false);
          $('#servicio').val('');
          $('#observacion2').val('');
          $('#servicio').attr('disabled', true);
          $('#servicio').css('background-color', '#BCBCBC');
          $('#primaria').css('display', 'none');
          $('#btnNoContesta').show();
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else if (content.fecha_mal) {
            limpiarLabels();
            $('#lblErrorVender').html('<img src="img/error.png" width="15px"> No puede agendar fechas mayores a un mes o menores al dia de hoy !!!');
            $('#primaria').css('display', 'none');
          } else {
            inicializarElementos();
            // buscarAgendados();
            // buscarReferidos();
            // buscarReferidosCuaderno();
            // buscarReferidosPendientes();
            // deshabilitarBotones();
            // limpiarNts();
            // limpiarSv();
            // limpiarTosnic();
            // limpiarTosic();
            // mostrarNts();
            // buscarInfo();
            // limpiarLabels();
            $('#lblNumero').html('');
            $('#lblLocalidad').html('');
            $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
            $('#divAtendio').hide();
            $('#nom').val('');
            $('#datetimepicker').val('');
            $('#com').val('');
            $('#integrantesFamilia').val('');
            $('#direccion').val('');
            $('#divSiAtendio2').hide();
            $('#checkServicio').prop('checked', false);
            $('#servicio').val('');
            $('#observacion2').val('');
            $('#servicio').attr('disabled', true);
            $('#servicio').css('background-color', '#BCBCBC');
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function () {
        deshabilitarBotones();
        limpiarLabels();
        $('#lblErrorVender').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
        $('#primaria').css('display', 'none');
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function referido() {
  $('#primaria').css('display', 'block');

  $nombre = $('#nomRef').val();
  $numero = $('#numRef').val();
  let $observacion = $('#obsRef').val();
  if ($nombre == '' || $numero == '' || $observacion == '') {
    limpiarLabels();
    $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> Complete todos los campos');
    $('#primaria').css('display', 'none');
  } else if (!/^([0-9])*$/.test($numero)) {
    limpiarLabels();
    $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> Numero debe ser un numero !!!');
    $('#primaria').css('display', 'none');
  } else if ((!/^(09)/.test($numero) || $numero.length != 9) && (!/^4/.test($numero) || $numero.length != 8) && (!/^2/.test($numero) || $numero.length != 8)) {
    limpiarLabels();
    $('#lblErrorReferido').html('<img src="img/error.png" width="15px"> El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado!!!');
    $('#primaria').css('display', 'none');
  } else {
    $indexForm = $('#index-form');
    var $form = $indexForm;
    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoReferido.php',
      data: {
        nomRef: $nombre,
        numRef: $numero,
        obsRef: $observacion
      },
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          // $('#btnReagendar').hide();
          habilitarBotones();
          inicializarElementos();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // limpiarLabels();
          $('#nomRef').val('');
          $('#numRef').val('');
          $('#obsRef').val('');
          $('#divReferido').hide();
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else if (content.repetido) {
            limpiarLabels();
            $('#nomRef').val('');
            $('#numRef').val('');
            $('#obsRef').val('');
            $('#lblErrorReferido').html(
              '<img src="img/error.png" width="15px"> El numero esta en uso o ya fue usado !!!'
            );
            $('#primaria').css('display', 'none');
          } else {
            deshabilitarBotones();
            inicializarElementos();
            // buscarAgendados();
            // buscarReferidos();
            // buscarReferidosCuaderno();
            // buscarReferidosPendientes();
            // buscarInfo();
            // limpiarLabels();
            $('#lblNumero').html('');
            $('#lblLocalidad').html('');
            $('#lblError').html(
              '<img src="img/error.png" width="20px"> No existe ningun numero asociado'
            );
            $('#nomRef').val('');
            $('#numRef').val('');
            $('#obsRef').val('');
            $('#divReferido').hide();
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function (error) {
        console.log(error);
        deshabilitarBotones();
        limpiarLabels();
        $('#lblErrorReferido').html(
          '<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.'
        );
        $('#primaria').css('display', 'none');
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function sumaygana() {
  $('#primaria').css('display', 'block');
  $cedula = $('#cedSum').val();
  $telefono = $('#telSum').val();
  if ($telefono == '') {
    limpiarLabels();
    $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> Complete telefono');
    $('#primaria').css('display', 'none');
  } else if ((!/^(09)/.test($telefono) || $telefono.length != 9) && (!/^4/.test($telefono) || $telefono.length != 8) && (!/^2/.test($telefono) || $telefono.length != 8)) {
    limpiarLabels();
    $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado!!!');
    $('#primaria').css('display', 'none');
  } else if ($cedula != '' && $cedula.length < 7) {
    limpiarLabels();
    $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> La cedula debe tener 7 o mas numeros');
    $('#primaria').css('display', 'none');
  } else if ($cedula != '' && !comprobarCI($cedula)) {
    limpiarLabels();
    $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> ¡Cedula incorrecta!');
    $('#primaria').css('display', 'none');
  } else {
    $indexForm = $('#index-form');
    var $form = $indexForm;

    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoSumayGana.php',
      data: $data,
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          habilitarBotones();
          limpiarLabels();
          //refSumayGana();
          $('#cedSum').val('');
          $('#telSum').val('');
          $('#divSumaygana').hide();
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else if (content.bloqueo) {
            limpiarLabels();
            $('#cedSum').val('');
            $('#telSum').val('');
            $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> Bloqueado hasta sucedido el sorteo de hoy.');
            $('#primaria').css('display', 'none');
          } else if (content.repetido) {
            limpiarLabels();
            $('#cedSum').val('');
            $('#telSum').val('');
            $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> La persona ya fue referida o ya es jugador');
            $('#primaria').css('display', 'none');
          } else if (content.usuario_call) {
            limpiarLabels();
            $('#cedSum').val('');
            $('#telSum').val('');
            $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> Los usuarios de AppCall no se pueden referir');
            $('#primaria').css('display', 'none');
          } else if (content.tel_repetido) {
            limpiarLabels();
            $('#cedSum').val('');
            $('#telSum').val('');
            $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> El telefono ya fue usado.');
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function () {
        deshabilitarBotones();
        limpiarLabels();
        $('#lblErrorSumayGana').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
        $('#primaria').css('display', 'none');
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function desbloquear() {
  if ($('#checkServicio').is(':checked')) {
    $('#servicio').css('background-color', 'white');
    $('#servicio').prop('disabled', false);
  } else {
    $('#servicio').css('background-color', '#BCBCBC');
    $('#servicio').prop('disabled', true);
    $('#servicio').val('');
    $('#campo_hora').hide();
    $('#campo_precio').hide();
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function verAgendar() {
  $('#divSiAtendio2').toggle();
  $('#btnReagendar').hide();
}

function verReagendar() {
  $('#divReagendar').show();
}

function reagendar() {
  var fecha = $('#fechaReagendar').val();
  var observacion = $('#obs').val();
  var num = $('#num_agendado').val();

  if (!fecha) {
    $('#lblErrorReagendar').text('Debe seleccionar la fecha');
  } else if (!observacion) {
    $('#lblErrorReagendar').text('Debe ingresar una observacion');
  } else {
    $.ajax({
      type: 'POST',
      url: 'ajax/procesoReagendar.php',
      data: { fecha, observacion, num },
      dataType: 'json',
      beforeSend: function () {
        $('#primaria').css('display', 'block');
      },
      success: function (content) {
        $('#primaria').css('display', 'none');
        $('#fechaReagendar').val('');
        $('#obs').val('');
        $('#num_agendado').val('');
        if (content.result) {
          $('#divReagendar').css('display', 'none');
          $('#btnReagendar').hide();
          $('#btnNoContesta').css('display', 'inline-block');
          inicializarElementos();
          // habilitarBotones();
          // buscarAgendados();
          // buscarReferidos();
          // buscarReferidosCuaderno();
          // buscarReferidosPendientes();
          // limpiarNts();
          // limpiarSv();
          // limpiarTosnic();
          // limpiarTosic();
          // mostrarNts();
          // buscarInfo();
          // limpiarLabels();
          $('#lblNumero').html(content.numero);
          $('#lblLocalidad').html(content.localidad);
          $('#divAtendio').hide();
          $('#nom').val('');
          $('#datetimepicker').val('');
          $('#com').val('');
          $('#integrantesFamilia').val('');
          $('#direccion').val('');
          $('#divSiAtendio2').hide();
          $('#checkServicio').prop('checked', false);
          $('#servicio').val('');
          $('#observacion2').val('');
          $('#servicio').attr('disabled', true);
          $('#servicio').css('background-color', '#BCBCBC');
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else if (content.fecha_mal) {
            limpiarLabels();
            $('#lblErrorVender').html('<img src="img/error.png" width="15px"> No puede agendar fechas mayores a un mes o menores al dia de hoy !!!');
            $('#primaria').css('display', 'none');
          } else {
            $('#divReagendar').css('display', 'none');
            $('#btnReagendar').hide();
            deshabilitarBotones();
            inicializarElementos();
            // buscarAgendados();
            // buscarReferidos();
            // buscarReferidosCuaderno();
            // buscarReferidosPendientes();
            // limpiarNts();
            // limpiarSv();
            // limpiarTosnic();
            // limpiarTosic();
            // mostrarNts();
            // buscarInfo();
            // limpiarLabels();
            $('#lblNumero').html('');
            $('#lblLocalidad').html('');
            $('#lblError').html('<img src="img/error.png" width="20px"> No existe ningun numero asociado');
            $('#divAtendio').hide();
            $('#nom').val('');
            $('#datetimepicker').val('');
            $('#com').val('');
            $('#integrantesFamilia').val('');
            $('#direccion').val('');
            $('#divSiAtendio2').hide();
            $('#checkServicio').prop('checked', false);
            $('#servicio').val('');
            $('#observacion2').val('');
            $('#servicio').attr('disabled', true);
            $('#servicio').css('background-color', '#BCBCBC');
            $('#primaria').css('display', 'none');
          }
        }
      }
    });
  }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function deshabilitarBotones() {
  $('#btnAtendio').attr('disabled', true);
  $('#btnNoContesta').attr('disabled', true);
  $('#btnReferido').attr('disabled', true);
  $('#btnNoLLamarMas').attr('disabled', true);
  $('#lblLocalidad').css('display', 'none');
  $('#vineta').css('display', 'none');
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function habilitarBotones() {
  $('#btnAtendio').attr('disabled', false);
  $('#btnNoContesta').attr('disabled', false);
  $('#btnReferido').attr('disabled', false);
  $('#btnNoLLamarMas').attr('disabled', false);
  $('#btnVerAgendados').attr('disabled', false);
  $('#referidos').attr('disabled', false);
  $('#lblLocalidad').css('display', 'block');
  $('#vineta').css('display', 'block');
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarAgendados(desde = '', hasta = '') {
  $('#Jtabla').DataTable().destroy();
  var url = 'Ajax/listarAgendados.php?desde=' + desde + '&hasta=' + hasta;
  $('#Jtabla tbody').html('');
  $.getJSON(url, function (agendados) {
    $.each(agendados, function (i, agendados) {
      if (agendados.estado == 'vencido') {
        var tr = "<tr class = 'vencido'>";
      }
      if (agendados.estado == 'del dia') {
        var tr = "<tr class = 'dehoy'>";
      }
      if (agendados.estado == 'futuro') {
        var tr = '<tr>';
      }
      var newRow =
        '</tbody>' +
        tr +
        "<td style='color:red;font-weight: bold;width:14%'>" +
        agendados.numero +
        '</td>' +
        "<td style='width:14%'>" +
        agendados.nombre +
        '</td>' +
        "<td style='font-weight: bold;width:14%;'>" +
        agendados.fecha_agendado +
        '</td>' +
        "<td style='width:14%'>" +
        agendados.fecha +
        '</td>' +
        '<td>' +
        agendados.comentario +
        '</td>' +
        "<td style='width:10%'><input type='button' class='llamar' id='llamar' value='llamar' onclick='llamarAgendado(" +
        '`' +
        agendados.numero +
        '`' +
        ")' /></td>" +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla tbody');
    });

    if ($(window).width() <= 1400) {
      tam = 2;
      search = false;
    } else {
      tam = 3;
      search = true;
    }
    $('.vencido').css('background-color', '#FEDEE7');
    $('.vencido td').css('background-color', '#FEDEE7');

    $('.dehoy').css('background-color', '#B0FFBA');
    $('.dehoy td').css('background-color', '#B0FFBA');
    $('#Jtabla').DataTable({
      lengthMenu: [tam],
      searching: search,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      order: [2, 'asc'],
      language: {
        decimal: '',
        emptyTable: 'No hay información',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ Registros',
        infoEmpty: 'Mostrando 0 to 0 of 0 Entradas',
        infoFiltered: '(Filtrado de _MAX_ total Registros)',
        infoPostFix: '',
        thousands: ',',
        lengthMenu: 'Mostrar _MENU_ Entradas',
        loadingRecords: 'Cargando...',
        processing: 'Procesando...',
        search: 'Buscar:',
        zeroRecords: 'Sin resultados encontrados',
        paginate: {
          first: 'Primero',
          last: 'Ultimo',
          next: 'Siguiente',
          previous: 'Anterior'
        }
      },

      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],
          orderData: [1, 0]
        },
        {
          targets: [3],

          orderData: [3, 0]
        }
      ]
    });
    true;
  });
  llenarBadge();
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llamarAgendado(numero) {
  $('#primaria').css('display', 'block');
  $('#btnReagendar').css('display', 'inline-block');
  $('#btnNoContesta').css('display', 'none');
  $('#num_agendado').val(numero);

  $.ajax({
    url: 'ajax/procesoLlamarAgendado.php?numero=' + numero,
    method: 'GET',
    dataType: 'json',
    beforeSend: function () { },
    success: function (content) {
      if (content.result) {
        $('#lblNumero').html(numero);
        $('#lblLocalidad').html(content.localidad);
        habilitarBotones();
        buscarInfo();
        limpiarLabels();
        $('#divVerAgendados').hide();
        $('#primaria').css('display', 'none');
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else {
          $('#primaria').css('display', 'none');
          $('#divVerAgendados').hide();
        }
      }
    },
    error: function () {
      deshabilitarBotones();
      limpiarLabels();
      $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llamarReferido(numero) {
  $('#primaria').css('display', 'block');
  $.ajax({
    url: 'ajax/procesoLlamarReferido.php?numero=' + numero,
    method: 'GET',
    dataType: 'json',
    beforeSend: function () { },
    success: function (content) {
      if (content.result) {
        $('#lblNumero').html(numero);
        $('#lblLocalidad').html(content.localidad);
        habilitarBotones();
        buscarInfo();
        limpiarLabels();
        $('#divVerReferidos').hide();
        $('#primaria').css('display', 'none');
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else {
          $('#primaria').css('display', 'none');
          $('#divVerReferidos').hide();
        }
      }
    },
    error: function () {
      deshabilitarBotones();
      limpiarLabels();
      $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llamarReferidoCuaderno(numero) {
  $.ajax({
    url: 'ajax/procesoLlamarReferidoCuaderno.php?numero=' + numero,
    method: 'GET',
    dataType: 'json',
    beforeSend: function () {
      $('#primaria').css('display', 'block');
    },
    success: function (content) {
      if (content.result) {
        $('#lblNumero').html(numero);
        $('#lblLocalidad').html(content.localidad);
        habilitarBotones();
        buscarInfo();
        limpiarLabels();
        $('#divVerReferidosCuaderno').hide();
        $('#primaria').css('display', 'none');
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else {
          $('#primaria').css('display', 'none');
          $('#divVerReferidosCuaderno').hide();
        }
      }
    },
    error: function () {
      deshabilitarBotones();
      limpiarLabels();
      $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarVendidos() {
  $('#Jtabla3').DataTable().destroy();
  var url = 'Ajax/listarVendidos.php';
  $('#Jtabla3 tbody').html('');
  $.getJSON(url, function (vendidos) {
    $.each(vendidos, function (i, vendidos) {
      var newRow =
        '</tbody>' +
        '<tr>' +
        "<td style='color:red;font-weight: bold;width:14%'>" +
        vendidos.numero +
        '</td>' +
        "<td style='width:5%'>" +
        vendidos.int_familia +
        '</td>' +
        "<td style='width:20%'>" +
        vendidos.direccion +
        '</td>' +
        "<td style='width:10%'>" +
        vendidos.otro_servicio +
        '</td>' +
        "<td style='width:25%'>" +
        vendidos.observaciones +
        '</td>' +
        "<td style='width:15%'>" +
        vendidos.fecha +
        '</td>' +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla3 tbody');
    });

    if ($(window).width() <= 1400) {
      tam = 2;
      search = false;
    } else {
      tam = 3;
      search = true;
    }
    $('#Jtabla3').DataTable({
      lengthMenu: [tam],
      searching: search,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      order: [5, 'asc'],
      language: {
        decimal: '',
        emptyTable: 'No hay información',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ Registros',
        infoEmpty: 'Mostrando 0 to 0 of 0 Entradas',
        infoFiltered: '(Filtrado de _MAX_ total registros)',
        infoPostFix: '',
        thousands: ',',
        lengthMenu: 'Mostrar _MENU_ Entradas',
        loadingRecords: 'Cargando...',
        processing: 'Procesando...',
        search: 'Buscar:',
        zeroRecords: 'Sin resultados encontrados',
        paginate: {
          first: 'Primero',
          last: 'Ultimo',
          next: 'Siguiente',
          previous: 'Anterior'
        }
      },
      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],

          orderData: [1, 0]
        },
        {
          targets: [3],

          orderData: [3, 0]
        }
      ]
    });
    true;
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarInfo() {
  var url = 'Ajax/listarInfo.php';
  $('#tblInfo tbody').html('');
  $.getJSON(url, function (info) {
    if (info != '') {
      $('#modalhover').css('pointer-events', 'all');
      $('#vineta').removeClass('vineta2');
      $('#vineta').addClass('vineta');
      $.each(info, function (i, info) {
        var newRow2 =
          '</tbody>' +
          '<tr>' +
          "<td style='color:#004681'><img src='img/right-arrow.png' width='14px'>" +
          info.estado +
          '</td>' +
          '<td>' +
          info.fecha +
          '</td>' +
          '<td>' +
          info.integrantes +
          '</td>' +
          '<td>' +
          info.direccion +
          '</td>' +
          '<td>' +
          info.otro_servicio +
          '</td>' +
          '<td>' +
          info.observaciones +
          '</td>' +
          '</tr>' +
          '</tbody>';
        $(newRow2).appendTo('#tblInfo tbody');
      });
    } else {
      $('#modalhover').css('pointer-events', 'none');
      $('#vineta').removeClass('vineta');
      $('#vineta').addClass('vineta2');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarSumayGana() {
  $('#Jtabla4').DataTable().destroy();
  var url = 'Ajax/listarSumayGana.php';
  $('#Jtabla4 tbody').html('');
  $.getJSON(url, function (suma) {
    $.each(suma, function (i, suma) {
      $saldo = '$' + suma.saldo;
      $('#saldo_vendedor').html($saldo);
      var newRow =
        '</tbody>' +
        '<tr>' +
        "<td style='color:red;font-weight: bold;width:30%'>" +
        suma.cedula +
        '</td>' +
        "<td style='width:30%'>" +
        suma.telefono +
        '</td>' +
        "<td style='width:20%'>$" +
        suma.monto +
        '</td>' +
        "<td style='width:20%'>" +
        suma.fecha +
        '</td>' +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla4 tbody');
    });
    if ($(window).width() <= 1400) {
      tam = 5;
      search = false;
    } else {
      tam = 8;
      search = true;
    }
    $('#Jtabla4').DataTable({
      lengthMenu: [tam],
      searching: search,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      order: [2, 'desc'],

      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],

          orderData: [1, 0]
        },
        {
          targets: [2],

          orderData: [2, 0]
        }
      ]
    });
    true;
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function refSumayGana() {
  $('#Jtabla5').DataTable().destroy();
  var url = 'Ajax/listarRefSumayGana.php';
  $('#Jtabla5 tbody').html('');
  $.getJSON(url, function (suma2) {
    $.each(suma2, function (i, suma2) {
      var newRow =
        '</tbody>' +
        '<tr>' +
        "<td style='width:5%'>" +
        suma2.cedula +
        '</td>' +
        "<td style='width:5%'>" +
        suma2.telefono +
        '</td>' +
        "<td style='width:20%'>" +
        suma2.vencido +
        '</td>' +
        "<td style='width:10%'>" +
        suma2.fecha +
        '</td>' +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla5 tbody');
    });

    if ($(window).width() <= 1400) {
      tam = 3;
      search = false;
    } else {
      tam = 5;
      search = true;
    }
    $('#Jtabla5').DataTable({
      lengthMenu: [tam],
      searching: search,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      order: [0, 'asc'],

      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],

          orderData: [1, 0]
        },
        {
          targets: [2],

          orderData: [2, 0]
        }
      ]
    });
    true;
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function llenarBadge() {
  $.ajax({
    url: 'ajax/procesoLlenarBadge.php',
    method: 'POST',
    dataType: 'json',
    beforeSend: function () { },
    success: function (content) {
      if (content.result) {
        $('#lblBadge1').html(content.hoy);
      }
    },
    error: function () {
      $('#lblError').html('<img src="img/error.png" width="20px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarReferidos() {
  $('#Jtabla2').DataTable().destroy();
  var url = 'Ajax/listarReferidos.php';
  $('#Jtabla2 tbody').html('');
  $.getJSON(url, function (referidos) {
    $.each(referidos, function (i, referidos) {
      var newRow =
        '</tbody>' +
        '<tr>' +
        "<td style='color:red;font-weight: bold;width:14%'>" +
        referidos.numero +
        '</td>' +
        "<td style='width:14%'>" +
        referidos.nombre +
        '</td>' +
        "<td style='width:14%'>" +
        referidos.fecha +
        '</td>' +
        '<td>' +
        referidos.observacion +
        '</td>' +
        "<td style='width:10%'><input type='button' class='llamar' id='llamar2' value='llamar' onclick='llamarReferido(" +
        '`' +
        referidos.numero +
        '`' +
        ")' /></td>" +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla2 tbody');
    });

    if ($(window).width() <= 1400) {
      tam = 2;
      search = false;
    } else {
      tam = 3;
      search = true;
    }
    $('#Jtabla2').DataTable({
      searching: search,
      paging: true,
      lengthMenu: [tam],
      lengthChange: false,
      ordering: true,
      info: true,
      order: [2, 'asc'],
      language: {
        decimal: '',
        emptyTable: 'No hay información',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ Entradas',
        infoEmpty: 'Mostrando 0 to 0 of 0 Entradas',
        infoFiltered: '(Filtrado de _MAX_ total entradas)',
        infoPostFix: '',
        thousands: ',',
        lengthMenu: 'Mostrar _MENU_ Entradas',
        loadingRecords: 'Cargando...',
        processing: 'Procesando...',
        search: 'Buscar:',
        zeroRecords: 'Sin resultados encontrados',
        paginate: {
          first: 'Primero',
          last: 'Ultimo',
          next: 'Siguiente',
          previous: 'Anterior'
        }
      },
      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],

          orderData: [1, 0]
        },
        {
          targets: [3],

          orderData: [3, 0]
        }
      ]
    });
    true;
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarReferidosCuaderno() {
  $('#Jtabla6').DataTable().destroy();
  var url = 'Ajax/listarReferidosCuaderno.php';
  $('#Jtabla6 tbody').html('');
  $.getJSON(url, function (referidos) {
    $.each(referidos, function (i, referidos) {
      var newRow =
        '</tbody>' +
        '<tr>' +
        "<td style='color:red;font-weight: bold;width:14%'>" +
        referidos.numero +
        '</td>' +
        "<td style='width:14%'>" +
        referidos.nombre +
        '</td>' +
        "<td style='width:14%'>" +
        referidos.fecha +
        '</td>' +
        '<td>' +
        referidos.observacion +
        '</td>' +
        "<td style='width:10%'><input type='button' class='llamar' id='llamar2' value='llamar' onclick='llamarReferidoCuaderno(" +
        '`' +
        referidos.numero +
        '`' +
        ")' /></td>" +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla6 tbody');
    });

    if ($(window).width() <= 1400) {
      tam = 2;
      search = false;
    } else {
      tam = 3;
      search = true;
    }
    $('#Jtabla6').DataTable({
      searching: search,
      paging: true,
      lengthMenu: [tam],
      lengthChange: false,
      ordering: true,
      info: true,
      order: [2, 'asc'],
      language: {
        decimal: '',
        emptyTable: 'No hay información',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ Registros',
        infoEmpty: 'Mostrando 0 to 0 of 0 Entradas',
        infoFiltered: '(Filtrado de _MAX_ total registros)',
        infoPostFix: '',
        thousands: ',',
        lengthMenu: 'Mostrar _MENU_ Entradas',
        loadingRecords: 'Cargando...',
        processing: 'Procesando...',
        search: 'Buscar:',
        zeroRecords: 'Sin resultados encontrados',
        paginate: {
          first: 'Primero',
          last: 'Ultimo',
          next: 'Siguiente',
          previous: 'Anterior'
        }
      },
      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],

          orderData: [1, 0]
        },
        {
          targets: [3],

          orderData: [3, 0]
        }
      ]
    });
    true;
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function referidoCuaderno() {
  $('#primaria').css('display', 'block');
  $nombre = $('#nomRefCuaderno').val();
  $numero = $('#numRefCuaderno').val();
  let $observacion = $('#obsRefCuaderno').val();
  if ($nombre == '' || $numero == '' || $observacion == '') {
    $('#lblErrorReferidoCuaderno').html('<img src="img/error.png" width="15px"> Complete todos los campos');
    $('#primaria').css('display', 'none');
  } else if (!/^([0-9])*$/.test($numero)) {
    $('#lblErrorReferidoCuaderno').html('<img src="img/error.png" width="15px"> Numero debe ser un numero !!!');
    $('#primaria').css('display', 'none');
  } else if ((!/^(09)/.test($numero) || $numero.length != 9) && (!/^4/.test($numero) || $numero.length != 8) && (!/^2/.test($numero) || $numero.length != 8)) {
    $('#lblErrorReferidoCuaderno').html('<img src="img/error.png" width="15px"> El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado!!!');
    $('#primaria').css('display', 'none');
  } else {
    $indexForm = $('#index-form');
    var $form = $indexForm;

    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoReferidoCuaderno.php',
      data: {
        nomRefCuaderno: $nombre,
        numRefCuaderno: $numero,
        obsRefCuaderno: $observacion
      },
      method: 'POST',
      dataType: 'json',
      beforeSend: function () { },
      success: function (content) {
        if (content.result) {
          buscarReferidosCuaderno();
          $('#nomRefCuaderno').val('');
          $('#numRefCuaderno').val('');
          $('#obsRefCuaderno').val('');
          $('#lblErrorReferidoCuaderno').html('');
          $('#divReferidoCuaderno').hide();
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else if (content.repetido) {
            buscarReferidosPendientes();
            $('#nomRefCuaderno').val('');
            $('#numRefCuaderno').val('');
            $('#obsRefCuaderno').val('');
            $('#lblErrorReferidoCuaderno').html(
              '<img src="img/error.png" width="15px"> El numero esta en uso o ya fue usado, se agrego a sus referidos de cuaderno pendientes.'
            );
            $('#primaria').css('display', 'none');
          } else if (content.repetido_cuaderno_pendiente) {
            $('#nomRefCuaderno').val('');
            $('#numRefCuaderno').val('');
            $('#obsRefCuaderno').val('');
            $('#lblErrorReferidoCuaderno').html(
              '<img src="img/error.png" width="15px"> Usted ya tiene ese numero.'
            );
            $('#primaria').css('display', 'none');
          } else {
            $('#lblErrorReferidoCuaderno').html(
              '<img src="img/error.png" width="15px"> Error'
            );
            $('#nomRefCuaderno').val('');
            $('#numRefCuaderno').val('');
            $('#obsRefCuaderno').val('');
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function () {
        $('#lblErrorReferidoCuaderno').html(
          '<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.'
        );
        $('#primaria').css('display', 'none');
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarReferidosPendientes() {
  $('#Jtabla7').DataTable().destroy();
  var url = 'Ajax/listarReferidosPendientes.php';
  $('#Jtabla7 tbody').html('');
  $.getJSON(url, function (referidos) {
    $.each(referidos, function (i, referidos) {
      var newRow =
        '</tbody>' +
        '<tr>' +
        "<td style='color:red;font-weight: bold;width:14%'>" +
        referidos.numero +
        '</td>' +
        "<td style='width:14%'>" +
        referidos.nombre +
        '</td>' +
        "<td style='width:14%'>" +
        referidos.fecha +
        '</td>' +
        '<td>' +
        referidos.observacion +
        '</td>' +
        "<td style='width:10%'><input type='button' class='llamar' value='Traspasar' onclick='traspasarReferido(" +
        '`' +
        referidos.id +
        '`' +
        ",$(this).parents(`tr`))' /></td>" +
        '</tr>' +
        '</tbody>';
      $(newRow).appendTo('#Jtabla7 tbody');
    });

    if ($(window).width() <= 1400) {
      tam = 2;
      search = false;
    } else {
      tam = 3;
      search = true;
    }
    $('#Jtabla7').DataTable({
      searching: search,
      paging: true,
      lengthMenu: [tam],
      lengthChange: false,
      ordering: true,
      info: true,
      order: [2, 'asc'],
      language: {
        decimal: '',
        emptyTable: 'No hay información',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ Registros',
        infoEmpty: 'Mostrando 0 to 0 of 0 Entradas',
        infoFiltered: '(Filtrado de _MAX_ total registros)',
        infoPostFix: '',
        thousands: ',',
        lengthMenu: 'Mostrar _MENU_ Entradas',
        loadingRecords: 'Cargando...',
        processing: 'Procesando...',
        search: 'Buscar:',
        zeroRecords: 'Sin resultados encontrados',
        paginate: {
          first: 'Primero',
          last: 'Ultimo',
          next: 'Siguiente',
          previous: 'Anterior'
        }
      },
      columnDefs: [
        {
          targets: [0],

          orderData: [0, 1]
        },
        {
          targets: [1],

          orderData: [1, 0]
        },
        {
          targets: [3],

          orderData: [3, 0]
        }
      ]
    });
    true;
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function traspasarReferido(id, rowActual) {
  $('#primaria').css('display', 'block');
  $.ajax({
    url: 'ajax/procesoTraspasoReferido.php',
    data: {
      id_referido: id
    },
    method: 'POST',
    dataType: 'json',
    success: function (content) {
      if (content.result) {
        $('#lblErrorReferidosPendientes').html('<img src="img/error.png" width="15px"> Traspasado correctamente');
        $('#Jtabla7').DataTable().row(rowActual).remove().draw(true);
        buscarReferidosCuaderno();
        $('#primaria').css('display', 'none');
      } else {
        if (content.message == 'Sin Sesion') {
          window.location.href = 'login.php';
        } else if (content.repetido) {
          $('#lblErrorReferidosPendientes').html('<img src="img/error.png" width="15px"> El numero esta en uso o ya fue usado.');
          $('#primaria').css('display', 'none');
        } else {
          $('#lblErrorReferidosPendientes').html('<img src="img/error.png" width="15px"> Error.');
          $('#primaria').css('display', 'none');
        }
      }
    },
    error: function () {
      $('#lblErrorReferidosPendientes').html('<img src="img/error.png" width="15px"> Ocurrio un error. Por favor vuelva a intentar en instantes.');
      $('#primaria').css('display', 'none');
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agregarAnotacion() {
  $anotacion = $("#anotacionActual").val();

  if ($anotacion == "") {
    $("#lblErrorAnotacionesCuaderno").html("Complete la anotacion");
  } else {
    $indexForm = $('#index-form');
    var $form = $indexForm;
    var $data = $form.serialize();
    var ua = navigator.userAgent.toLowerCase();

    $.ajax({
      url: 'ajax/procesoAnotacion.php',
      data: { anotacionActual: $("#anotacionActual").val() },
      method: 'POST',
      dataType: 'json',
      beforeSend: function () {
        $('#primaria').css("display", "block");
      },
      success: function (content) {
        $('#primaria').css("display", "none");
        if (content.result) {
          listarAnotaciones();
          $("#anotacionActual").val("");
          $("#lblErrorAnotacionesCuaderno").html("");
        } else {
          if (content.message == "Sin Sesion") {
            window.location.href = "login.php";
          } else {
            $("#lblErrorAnotacionesCuaderno").html("Error");
          }
        }
      },
      error: function () {
        $("#lblErrorAnotacionesCuaderno").html("Ha ocurrido un error, intente mas tarde.");
        $('#primaria').css("display", "none");
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
function listarAnotaciones(fechaDesde = '', fechaHasta = '') {
  $tabla = $('#dataTableAnotaciones').DataTable({
    ajax: 'ajax/listarAnotaciones.php?desde=' + fechaDesde + '&hasta=' + fechaHasta,
    initComplete: function (settings, json) { },
    responsive: {
      details: {
        display: $.fn.dataTable.Responsive.display.modal({
          header: function (row) {
            var data = row.data();
            return 'Anotación  ';
          }
        }),
        //renderer: $.fn.dataTable.Responsive.renderer.tableAll()
        renderer: function (api, rowIdx, columns) {
          var data = $.map(columns, function (col, i) {
            return col.hidden ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' + '<td> ' + ' ' + col.data + ' </td>' + '</tr>' : '';
          }).join('');

          return data ? $('<table/>').append(data) : false;
        }
      }
    },
    language: {
      sProcessing: 'Procesando...',
      sLengthMenu: 'Mostrar _MENU_ registros',
      sZeroRecords: 'No se encontraron resultados',
      sEmptyTable: 'No se encontraron resultados.',
      sInfo: 'Mostrando _START_ al _END_ de _TOTAL_ registros',
      sInfoEmpty: 'Mostrando del 0 al 0 de 0 registros',
      sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
      sInfoPostFix: '',
      sSearch: 'Buscar:',
      sUrl: '',
      sInfoThousands: ',',
      sLoadingRecords: 'Cargando...',
      oPaginate: {
        sFirst: 'Primero',
        sLast: 'Último',
        sNext: 'Siguiente',
        sPrevious: 'Anterior'
      },
      oAria: {
        sSortAscending: ': Activar para ordenar la columna de manera ascendente',
        sSortDescending: ': Activar para ordenar la columna de manera descendente'
      }
    },
    destroy: true
  });
}*/

function listarAnotaciones(f = "", p = "") {
  var fecha = f;
  var palabra = p;
  $("#anotacionesAnteriores").scrollTop(0);
  $.ajax({
    url: 'ajax/listarAnotaciones.php?palabra=' + palabra + '&fecha=' + fecha,
    method: 'GET',
    dataType: 'json',
    success: function (content) {
      if (content.result) {
        $("#anotacionesAnteriores").val(content.anotacion);
      } else {
        if (content.message == "Sin Sesion") {
          window.location.href = "login.php";
        } else {
          $("#lblErrorAnotacionesCuaderno").html("Error");
          $('#primaria').css("display", "none");
        }
      }
    },
    error: function () {
      $("#lblErrorAnotacionesCuaderno").html("Ha ocurrido un error, intente mas tarde.");
      $('#primaria').css("display", "none");
    }
  });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function buscarBaja() {
  $cedula = $('#cedBaja').val();
  $('#primaria').css('display', 'block');
  if ($cedula == '') {
    $('#semaforo').css('display', 'none');
    $('#lblErrorBuscarBaja').html('<img src="img/error.png" width="15px"> Complete cedula.');
    $('#primaria').css('display', 'none');
  } else if (!comprobarCI($cedula)) {
    $('#semaforo').css('display', 'none');
    $('#lblErrorBuscarBaja').html('<img src="img/error.png" width="15px"> Cedula erronea.');
    $('#primaria').css('display', 'none');
  } else {
    $.ajax({
      url: 'ajax/buscarBaja.php',
      data: {
        cedula: $cedula
      },
      method: 'POST',
      dataType: 'json',
      success: function (content) {
        if (content.result) {
          $('#semaforo').css('background-color', content.color_code);
          $('#txtResultadoBaja').css('color', content.font_color);
          $('#txtResultadoBaja').html(content.texto);
          $('#semaforo').css('display', 'block');
          $('#lblErrorBuscarBaja').html('');
          $('#primaria').css('display', 'none');
        } else {
          if (content.message == 'Sin Sesion') {
            window.location.href = 'login.php';
          } else {
            $('#semaforo').css('display', 'none');
            $('#lblErrorBuscarBaja').html('<img src="img/error.png" width="15px"> Error.');
            $('#primaria').css('display', 'none');
          }
        }
      },
      error: function () {
        $('#semaforo').css('display', 'none');
        $('#lblErrorBuscarBaja').html('<img src="img/error.png" width="15px"> Ha ocurrido un error, intente mas tarde.');
        $('#primaria').css('display', 'none');
      }
    });
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarSv() {
  $('#btnSv').removeClass('botoncartilla');
  $('#btnSv').addClass('botoncartillaseleccionado');

  $('#btnNts').addClass('botoncartilla');
  $('#btnNts').removeClass('botoncartillaseleccionado');

  $('#btnTosic').addClass('botoncartilla');
  $('#btnTosic').removeClass('botoncartillaseleccionado');

  $('#btnTosnic').addClass('botoncartilla');
  $('#btnTosnic').removeClass('botoncartillaseleccionado');

  $('#sv').css('display', 'block');
  $('#nts').css('display', 'none');
  $('#tosic').css('display', 'none');
  $('#tosnic').css('display', 'none');

  $('#chksv').prop('checked', true);
  $('#chknts').prop('checked', false);
  $('#chktosic').prop('checked', false);
  $('#chktosnic').prop('checked', false);

  limpiarNts();
  limpiarTosic();
  limpiarTosnic();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarNts() {
  $('#btnNts').removeClass('botoncartilla');
  $('#btnNts').addClass('botoncartillaseleccionado');

  $('#btnSv').addClass('botoncartilla');
  $('#btnSv').removeClass('botoncartillaseleccionado');

  $('#btnTosic').addClass('botoncartilla');
  $('#btnTosic').removeClass('botoncartillaseleccionado');

  $('#btnTosnic').addClass('botoncartilla');
  $('#btnTosnic').removeClass('botoncartillaseleccionado');

  $('#nts').css('display', 'block');
  $('#sv').css('display', 'none');
  $('#tosic').css('display', 'none');
  $('#tosnic').css('display', 'none');

  $('#chksv').prop('checked', false);
  $('#chknts').prop('checked', true);
  $('#chktosic').prop('checked', false);
  $('#chktosnic').prop('checked', false);

  limpiarSv();
  limpiarTosic();
  limpiarTosnic();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarTosic() {
  $('#btnTosic').removeClass('botoncartilla');
  $('#btnTosic').addClass('botoncartillaseleccionado');

  $('#btnSv').addClass('botoncartilla');
  $('#btnSv').removeClass('botoncartillaseleccionado');

  $('#btnNts').addClass('botoncartilla');
  $('#btnNts').removeClass('botoncartillaseleccionado');

  $('#btnTosnic').addClass('botoncartilla');
  $('#btnTosnic').removeClass('botoncartillaseleccionado');

  $('#tosic').css('display', 'block');
  $('#sv').css('display', 'none');
  $('#nts').css('display', 'none');
  $('#tosnic').css('display', 'none');

  $('#chksv').prop('checked', false);
  $('#chknts').prop('checked', false);
  $('#chktosic').prop('checked', true);
  $('#chktosnic').prop('checked', false);

  limpiarNts();
  limpiarSv();
  limpiarTosnic();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mostrarTosnic() {
  $('#btnTosnic').removeClass('botoncartilla');
  $('#btnTosnic').addClass('botoncartillaseleccionado');

  $('#btnSv').addClass('botoncartilla');
  $('#btnSv').removeClass('botoncartillaseleccionado');

  $('#btnNts').addClass('botoncartilla');
  $('#btnNts').removeClass('botoncartillaseleccionado');

  $('#btnTosic').addClass('botoncartilla');
  $('#btnTosic').removeClass('botoncartillaseleccionado');

  $('#tosnic').css('display', 'block');
  $('#sv').css('display', 'none');
  $('#nts').css('display', 'none');
  $('#tosic').css('display', 'none');

  $('#chksv').prop('checked', false);
  $('#chknts').prop('checked', false);
  $('#chktosic').prop('checked', false);
  $('#chktosnic').prop('checked', true);

  limpiarNts();
  limpiarTosic();
  limpiarSv();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarSv() {
  $('#sva').prop('checked', false);
  $('#svb').prop('checked', false);
  $('#svc').prop('checked', false);
  $('#svd').prop('checked', false);
  $('#sve').prop('checked', false);
  $('#svf').prop('checked', false);
  $('#svg').prop('checked', false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarNts() {
  $('#ntsa').prop('checked', false);
  $('#ntsb').prop('checked', false);
  $('#ntsc').prop('checked', false);
  $('#ntsd').prop('checked', false);
  $('#ntse').prop('checked', false);
  $('#ntsf').prop('checked', false);
  $('#ntsg').prop('checked', false);
  $('#ntsh').prop('checked', false);
  $('#ntsi').prop('checked', false);
  $('#ntsj').prop('checked', false);
  $('#ntsk').prop('checked', false);
  $('#ntsl').prop('checked', false);
  $('#ntsm').prop('checked', false);
  $('#ntsn').prop('checked', false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarTosic() {
  $('#tosica').prop('checked', false);
  $('#tosicb').prop('checked', false);
  $('#tosicc').prop('checked', false);
  $('#tosicd').prop('checked', false);
  $('#tosice').prop('checked', false);
  $('#tosicf').prop('checked', false);
  $('#tosicg').prop('checked', false);
  $('#tosich').prop('checked', false);
  $('#tosici').prop('checked', false);
  $('#tosicj').prop('checked', false);
  $('#tosick').prop('checked', false);
  $('#tosicl').prop('checked', false);
  $('#tosicm').prop('checked', false);
  $('#tosicn').prop('checked', false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarTosnic() {
  $('#tosnica').prop('checked', false);
  $('#tosnicb').prop('checked', false);
  $('#tosnicc').prop('checked', false);
  $('#tosnicd').prop('checked', false);
  $('#tosnice').prop('checked', false);
  $('#tosnicf').prop('checked', false);
  $('#tosnicg').prop('checked', false);
  $('#tosnich').prop('checked', false);
  $('#tosnici').prop('checked', false);
  $('#tosnicj').prop('checked', false);
  $('#tosnick').prop('checked', false);
  $('#tosnicl').prop('checked', false);
  $('#tosnicm').prop('checked', false);
  $('#tosnicn').prop('checked', false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarLabels() {
  $('#lblErrorVender').html('');
  $('#lblErrorReferido').html('');
  $('#lblErrorListaNegra').html('');
  $('#lblError').html('');
  $('#lblErrorSumayGana').html('');
}

//limpia inputs div atendio
function LimpiarInputs() {
  $('#integrantesFamilia').val('');
  $('#direccion').val('');
  $('#checkServicio').prop('checked', false);
  $('#servicio').val('');
  $('#observacion2').val('');
  $('#servicio').attr('disabled', true);
  $('#servicio').css('background-color', '#BCBCBC');
}

//inicializa las tablas y limpia los labels de las divs de la vista de usuarios
function inicializarElementos() {
  buscarAgendados();
  buscarReferidos();
  buscarReferidosCuaderno();
  buscarReferidosPendientes();
  //buscarSumayGana();
  //refSumayGana();
  buscarInfo();
  listarAnotaciones();
  limpiarLabels();
  limpiarNts();
  limpiarSv();
  limpiarTosnic();
  limpiarTosic();
  mostrarNts();
}

//FUNCIONES PARA MOSTRAR Y OCULTAR CADA FORMULARIO DE CADA PASO
function mostrarValidacion() {
  $('#validacion_cedula').show();
  $('#pasouno').hide();
  $('#pasodossocio').hide();
  $('#pasodos').hide();
}

function mostrarPasoUno() {
  $('#pasouno-title').text('NUEVA ALTA');
  $('#validacion_cedula').hide();
  $('#pasodos').hide();
  $('#pasodossocio').hide();
  $('#pasouno').show();
  obtenerDepartamentos();
}

/*function mostrarPasoUnoSocio() {
  const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
  const fechaNacimiento = datosSocio.fecha_nacimiento.split("-");
  const numerosDeTelefonos = datosSocio.telefono.split(" ").filter(function(x) {return x}).sort();
  
  obtenerDepartamentos();  
  $("#cedBen2").val(datosSocio.cedula).attr("disabled", "disabled");
  $("#nomBen").val(datosSocio.nombre).attr("disabled", "disabled");
  $("#dirBen").val(datosSocio.direccion);
  $("#mailBen").val(datosSocio.email);
  $("#celBen").val(numerosDeTelefonos[0]);
  $("#telBen").val(typeof numerosDeTelefonos[1] === undefined ? '' : numerosDeTelefonos[1]);
  $("#telAltBen").val(typeof numerosDeTelefonos[2] === undefined ? '' : numerosDeTelefonos[2]);
  $("#nataliciodia").val(fechaNacimiento[2]).attr("disabled", "disabled");
  $("#nataliciomes").val(fechaNacimiento[1].length > 1 ? fechaNacimiento[1] : '0'+fechaNacimiento[1]).attr("disabled", "disabled");
  $("#natalicioano").val(fechaNacimiento[0].length > 1 ? fechaNacimiento[0] : '0' + fechaNacimiento[0]).attr("disabled", "disabled");
  $('#pasouno').show();
  $('#pasodos').hide();
  $('#validacion_cedula').hide();
  $('#pasodossocio').hide();
  $("#pasouno-title").text("NUEVO INCREMENTO");
}*/

function mostrarPasoUnoSocio() {
  const datosSocio = JSON.parse(localStorage.getItem('datos_socio'));
  const fechaNacimiento = datosSocio.fecha_nacimiento.split('-');
  const numerosDeTelefonos = datosSocio.telefono.split(' ').filter(function (x) { return x; }).sort();

  obtenerDepartamentos();

  // telefonos
  if (numerosDeTelefonos[0] == 0) {
    $('#celBen').val(numerosDeTelefonos[1]);
  } else {
    $('#celBen').val(numerosDeTelefonos[0]);
  }

  $('#telBen').val(typeof numerosDeTelefonos[1] !== undefined && /^(2|4)/.test(numerosDeTelefonos[1]) ? numerosDeTelefonos[1] : '');
  $('#telAltBen').val(typeof numerosDeTelefonos[2] === undefined ? '' : numerosDeTelefonos[2]);

  $('#cedBen2').val(datosSocio.cedula).attr('disabled', 'disabled');
  $('#nomBen').val(datosSocio.nombre).attr('disabled', 'disabled');
  $('#dirBen').val(datosSocio.direccion);
  $('#mailBen').val(datosSocio.email);
  $('#nataliciodia').val(fechaNacimiento[2]);
  $('#nataliciomes').val(fechaNacimiento[1].length > 1 ? fechaNacimiento[1] : '0' + fechaNacimiento[1]);
  $('#natalicioano').val(fechaNacimiento[0].length > 1 ? fechaNacimiento[0] : '0' + fechaNacimiento[0]);
  $('#pasouno').show();
  $('#pasodos').hide();
  $('#validacion_cedula').hide();
  $('#pasodossocio').hide();
  $('#pasouno-title').text('NUEVO INCREMENTO');
}

function mostrarPasoDosSocio() {
  $('#pasouno').hide();
  $('#pasodos').hide();
  $('#validacion_cedula').hide();
  $('#pasodossocio').show();
}

function mostrarEleccionMedioPago() {
  listarMetodosPago();
  $('#validacion_medio_pago').show();
  $('#pasotres').hide();
  $('#pasodossocio').hide();
}

function mostrarPasoDos() {
  $('#pasodos').show();
  $('#pasouno').hide();
  $('#pasotres').hide();
  $('#validacion_medio_pago').hide();

  if ($(".sprod").length || $(".hservicio").val() > 0) {
    $(".hservicio").change();
    $(".hsanatorio").change();
  } else if (!$("#producto").val() || $("#producto option:eq(0)").prop("selected") == true) {
    listarServicios()
  };
}

function mostrarPasoDosAtras() {
  if (localStorage.code == '1') {
    $('#pasodos').show();
    $('#validacion_medio_pago').hide();
  } else {
    $('#pasodossocio').show();
    $('#validacion_medio_pago').hide();
  }

}

function mostrarPasoTres() {
  listarMetodosPago();
  $('#validacion_medio_pago').show();
  $('#pasodos').hide();
  //si se selcciono promo np17 solo permitir medio d epago con tarjeta


}

//limpia los campos del formulario de datos del beneficiario del modal venta
function limpiarDatosCliente() {
  $('#cedBen2').val('');
  $('#cedBen').val('');
  $('#nomBen').val('');
  $('#dirBen').val('');
  $('#mailBen').val('');
  $('#celBen').val('');
  $('#telBen').val('');
  $('#nataliciodia option:eq(0)').prop('selected', true);
  $('#nataliciomes option:eq(0)').prop('selected', true);
  $('#natalicioano option:eq(0)').prop('selected', true);
  $('#depBen option:eq(0)').prop('selected', true);
  $('#locBen').html('');
  $('#nomBen').val('').attr('disabled', false);
  $('#natalicioano').attr('disabled', false);
  $('#nataliciomes').attr('disabled', false);
  $('#nataliciodia').attr('disabled', false);
}

function limpiarModalBeneficiarios() {
  $('#beneficiarios_form').trigger('reset');
  $('.beneficiarios-extra').html('');
}

function limpiarInputsDatosTarjeta() {
  $('#numTar').val('');
  $('#cvv').val('');
  $('#cedTit').val('');
  $('#nomTit').val('');
  $('#mailTit').val('');
  $('#celTit').val('');
  $('#bancos').html('');
  $('#mesVen option:eq(0)').prop('selected', true);
  $('#anoVen option:eq(0)').prop('selected', true);
}
function limpiarDivMetodosPago() {
  $('#medio_pago').html('');
  $('#convenios').html();
  $('#divConvenios').hide();
  $('#divDatosConvenio').hide();
}

function limpiarInputsProductos() {
  $('#producto option:eq(0)').prop('selected', true);
  $('#comentario').val('');
  $('#row_sanatorio').html('').hide();
  $('#hrservicio option:eq(0)').prop('selected', true);
  $('#divHorasServicio').hide();
  $('#nuevos_servicios').html('');
  $('#divPromo').hide();
  $('#divBoton').hide();
  $('#promo option:eq(0)').prop('selected', true);
}

function limpiarDatosConvenio() {
  $('#cedTitConvenio').val('');
  $('#nomTitConvenio').val('');
  $('#convenios').html('');
  $('#divConvenios').hide();
  $('#divDatosConvenio').hide();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//compueba que la cedula sea una cedula correcta
function comprobarCI(cedi) {
  if (cedi.length >= 7) {
    //Inicializo los coefcientes en el orden correcto
    var arrCoefs = [2, 9, 8, 7, 6, 3, 4, 1];
    var suma = 0;
    //Para el caso en el que la CI tiene menos de 8 digitos
    //calculo cuantos coeficientes no voy a usar
    var difCoef = parseInt(arrCoefs.length - cedi.length);
    //var difCoef = parseInt(arrCoefs.length – ci.length);
    //recorro cada digito empezando por el de más a la derecha
    //o sea, el digito verificador, el que tiene indice mayor en el array
    for (var i = cedi.length - 1; i > -1; i--) {
      //for (var i = ci.length – 1; i > -1; i–) {
      //ooObtengo el digito correspondiente de la ci recibida
      var dig = cedi.substring(i, i + 1);
      //Lo tenía como caracter, lo transformo a int para poder operar
      var digInt = parseInt(dig);
      //Obtengo el coeficiente correspondiente al ésta posición del digito
      var coef = arrCoefs[i + difCoef];
      //Multiplico dígito por coeficiente y lo acumulo a la suma total
      suma = suma + digInt * coef;
    }
    // si la suma es múltiplo de 10 es una ci válida
    if (suma % 10 == 0) {
      return true;
    } else {
      return false;
    }
  }
  return false;
}

//no permite el ingresen letras
function soloLetras(e) {
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = 'áéíóúabcdefghijklmnñopqrstuvwxyz ';
  especiales = '8-37-39-46';

  //  tecla_especial = true;
  //    for(var i in especiales){
  //         if(key == especiales[i]){
  //             tecla_especial = true;
  //             break;
  //         }
  //     }

  if (letras.indexOf(tecla) == -1) {
    return false;
  }
}

function validarEmail(mail) {
  var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(mail) ? true : false;
}

function removerInputError() {
  $('.input-error').css('border-color', 'none');
}

function addEvent(el, eventName, handler) {
  if (el.addEventListener) {
    el.addEventListener(eventName, handler);
  } else {
    el.attachEvent('on' + eventName, function () {
      handler.call(el);
    });
  }
}

function validarTarjeta() {
  const cardnumber = $('#numTar').val();
  const tipo_tarjeta = localStorage.getItem('tipo_tarjeta');
  const is_mercadopago = localStorage.getItem('mercadopago');
  let tarjetaValida = false;

  if ((tipo_tarjeta == '' || tipo_tarjeta == null) && (is_mercadopago == '' || is_mercadopago == null)) {
    $.ajax({
      type: 'POST',
      url: 'Ajax/validarTarjetas.php',
      data: { numeroTarjeta: cardnumber },
      dataType: 'json',
      async: false,
      success: function (response) {
        if (response.result) {
          localStorage.setItem('mercadopago', '0');
          localStorage.setItem('tipo_tarjeta', response.tipo_tarjeta);
          $('#payment_method_id').val(response.tipo_tarjeta);
          $('#is_mercadopago').val('0');
          $('#img-tipo_tarjeta').attr('src', `img/${TARJETAS_IMAGENES[response.tipo_tarjeta.toUpperCase()]}`);
          tarjetaValida = true;
        }
      }
    });
  }
  return tarjetaValida;
}

//#################################### MERCADO PAGO
function guessPaymentMethod() {
  const cardnumber = document.getElementById('numTar').value;
  $('#img-tipo_tarjeta').attr('src', '');

  if (cardnumber.length >= 6) {
    const bin = cardnumber.substring(0, 6);
    window.Mercadopago.getPaymentMethod(
      {
        bin: bin
      },
      setPaymentMethod
    );
  }
  return true;
}

function setPaymentMethod(status, response) {
  if (status == 200) {
    const paymentMethodId = response[0].id;
    const element = document.getElementById('payment_method_id');
    element.value = paymentMethodId;
    $('#is_mercadopago').val('1');
    localStorage.setItem('mercadopago', '1');
    localStorage.setItem('tipo_tarjeta', paymentMethodId);
    $('#img-tipo_tarjeta').attr('src', `img/${TARJETAS_IMAGENES[paymentMethodId.toUpperCase()]}`);
  } else {
    localStorage.setItem('mercadopago', '');
    localStorage.setItem('tipo_tarjeta', '');
    validarTarjeta();
  }
}

function vidashop() {
  $celular = $('#celularVidaShop').val();

  if ($celular == "") {
    $('#lblErrorVidaShop').html('<img src="img/error.png" width="15px"> Complete celular');
  } else if ((!/^(09)/.test($celular) || $celular.length != 9)) {
    $('#lblErrorVidaShop').html('<img src="img/error.png" width="15px"> El celular debe empezar con (09) y tener el largo adecuado');
  } else {
    $.ajax({
      url: 'ajax/procesoVidaShop.php',
      data: {
        celular: $celular
      },
      method: 'POST',
      dataType: 'json',
      beforeSend: function () {
        $('#primaria').css("display", "block");
      },
      complete: function () {
        $('#primaria').css("display", "none");
      },
      success: function (content) {
        if (content.result) {
          $('#celularVidaShop').val('');
          $('#lblErrorVidaShop').html('');
          $('#divVidaShop').hide();
        } else {
          if (content.message == "Sin Sesion") {
            window.location.href = "login.php";
          }
        }
      },
      error: function () {
        $("#lblErrorVidaShop").html('<img src="img/error.png" width="15px"> Ha ocurrido un error, intente mas tarde.');
        $('#primaria').css("display", "none");
      }
    });
  }
}