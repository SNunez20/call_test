let $add_integrante = 1;
let $arrGrupo = [];
let cedulasGrupo = [];
let $datos_integrante = 0;
let arrDatosTarjeta = [];

let $omtGrupo = false; //omtgrupo
let $comision_omtGrupo = 0;
let $arrBenOmtGrupo = [0, 0, 0, 0, 0];
let $incrementoOmtGrupo = false;

/**
 * datosSocios.push([
    CEDULA
    NOMBRE,
    FECHA_NACIMIENTO,
    DIRECCION,
    DEPARTAMENTO,
    LOCALIDAD,
    FILIAL,
    MAIL,
    CELULAR,
    TELEFONO,
    TELEFONO_ALTERNATIVO,
    DATO_EXTRA,
    NOMBRE_DEPARTAMENTO,
    NOMBRE_LOCALIDAD
  ]);
 */
let $datosSocios = [];
let SOCIOS = [];
let totalImporteGrupo = 0;

function zip(a, b) {
  let x = [];
  if (
    a.constructor !== Array ||
    b.constructor !== Array ||
    a.length != b.length
  )
    return false;
  for (let i = 0; i < a.length; i++) x.push([a[i], b[i]]);
  return x;
}

$('document').ready(function () {
  // CONTABILIZA EL SUBTOTAL
  $(document.body).on('change', '.hservicio_int', contabilizarSubTotalGrupo);
  $(document.body).on('change', '.hsanatorio_int', contabilizarSubTotalGrupo);
  // $(document.body).on('change', '.hrservicioadi_int', contabilizarSubTotalGrupo);

  //Oculta los div error de los formularios
  $('#error-tipo-afiliacion').hide();
  $('#error-validacion-cedula-grupo').hide();
  $('.error-datos-grupo').hide();
  $('#error-pago-grupo').hide();

  //validacion solo letras
  $('.solo_letras').keypress(function (e) {
    return soloLetras(e);
  });

  $('#btnQuitarPersona').click(quitarPersona);
  $('#btnValidarCedulaGrupo').click(validarCedulaPadronGrupo);

  $('#btnAgregarPersona').click(function (e) {
    let ced_integrante = $('.valced_integrante').last().val();
    if (ced_integrante == '') {
      $('.error-validacion-cedula-grupo').text('Debe ingresar la cédula');
      $('#error-validacion-cedula-grupo').show().fadeOut(10000);
    } else {
      agregarPersona(e);
    }
  });

  $('#medio_pago_grupo').change(function () {
    var medio = Number($('#medio_pago_grupo').val());
    // $('#datos_del_cliente').html('');
    listarConveniosGrupo(medio);
    $('#divConveniosGrupo').show();
  });

  // BOTONES ATRAS / SIGUIENTE
  $('#btnSiguienteInt1').click(function () {
    siguienteGrupo(1, 2);
  });

  $('#btnAtrasInt1').click(function () {
    cambiarPaginaGrupo(0);
  });

  $('#btnSiguienteInt2').click(function () {
    siguienteGrupo(2, 3);
  });

  $('#btnAtrasInt2').click(function (e) {
    e.preventDefault();
    cambiarPaginaGrupo(1);
  });

  $('#btnSiguienteInt3').click(function () {

    $('#spanPrecio').append(totalImporteGrupo);
    listarBancosEmisores();
    mostrarModalDatosTarjeta();
  });

  $('#btnAtrasInt3').click(function (e) {
    e.preventDefault();
    cambiarPaginaGrupo(2);
  });

  $('#btnAtrasInt4').click(function (e) {
    e.preventDefault();
    mostrarValidacionMedioPagoGrupo();
  });

  $('#btnConfirmarVentaGrupo').click(confirmarVentaGrupo);
  $('#btnCancelarVentaGrupo').click(function (e) {
    e.preventDefault();
    $('#modal-confirmacion-venta-grupo').modal('hide');
  });

  $(document.body).on('change', '.dep_integrante', function (e) {
    let dep = $(this).val();
    let parent = $(this).parent().parent().parent().parent().attr('id');

    if (dep != '') {
      $.ajax({
        type: 'POST',
        url: 'Ajax/obtenerLocalidades.php',
        data: { departamento: dep },
        dataType: 'json',
        success: function (response) {
          if (response.result) {
            $('#' + parent + ' .loc_integrante').empty();
            let loc = response.localidades;
            let l = `<option value="" selected>- Seleccione -</option>`;
            loc.forEach(function (val, i) {
              l +=
                `<option value="` +
                val.id +
                `" data-filial-grupo="` +
                val.idFilial +
                `">` +
                val.localidad +
                `</option>`;
            });
            $('#' + parent + ' .loc_integrante').append(l);
          }
        }
      });
    } else {
      $('#' + parent + ' .loc_integrante').empty();
      $('#' + parent + ' .loc_integrante').append(
        `<option value="">- Seleccione -</option>`
      );
    }
    // document.querySelector(`#locBen option[value="${$loc}"]`).setAttribute("selected", true);
  });

  $(document.body).on('change', '.select_principal', function (e) {
    let servi = $(this).val();
    let parent = $(this).parent().parent().parent().parent().attr('id');
    let padre = $(this).parent().parent().parent().parent();
    console.log(padre);
    let index_integrante = Number($(`#${parent} .index_integrante`).val());
    let dato_extra = $(`#${parent} .dato_extra_integrante`).val();
    let arrServicios = [2, 3, 4, 5, 11];
    if (dato_extra == '2') {
      $(`#${parent} .promo`).prop('disabled', 'disabled');
    }

    if (arrServicios.includes(Number(servi))) {
      let row_sanatorio_integrante = `<div class="col-md-4">
                            <div class="form-group">
                              <div id="divHorasSanatorioIntegrante${index_integrante + 1
        }" class="divHorasSanatorioIntegrante" >
                                <label for="" class="texto" >Cantidad horas sanatorio <span class="requerido">*</span> </label>
                                <select id="hrsanatorio_int${index_integrante}" name="hrsanatorio" class="custom-select form-control hsanatorio_int" >
                                    <option value="0" selected disabled>Seleccione cantidad</option>
                                    <option value="8" data-base="1">8 hs</option>
                                    <option value="16" data-base="0">16 hs</option>
                                    <option value="24" data-base="0">24 hs</option>
                                </select>
                                <input type="hidden" class="importe-servicio-integrante"/>
                                <input type="hidden" class="importe-base-servicio-integrante"/>
                                <input type="hidden" class="importe-total-servicio-integrante"/>
                              </div>
                            </div>   
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <div id="divPromoSanatorioIntegrante${index_integrante + 1
        }" class="divPromoSanatorioIntegrante">
                                <label for="" class="texto" >Promo</label>
                                <select id="promoSanatorio_int${index_integrante + 1
        }" name="promoSanatorio" class="custom-select form-control promo_integrante" ${$datosSocios[index_integrante][11] == '2' ? 'disabled' : ''
        }>
                                    <option value="0" selected >Seleccione promo</option>
                                    <option value="0">Ninguna</option>
                                    <option value="20">NP17</option>
                                </select>
                                <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
                              </div>
                            </div>   
                          </div>`;
      $(`#row_sanatorio_integrante${index_integrante + 1}`).html(
        row_sanatorio_integrante
      );
    } else {
      $(`#divHorasSanatorioIntegrante${index_integrante + 1}`).hide();
      $(`#divPromoSanatorioIntegrante${index_integrante + 1}`).hide();
    }

    $('#divHorasServicio select option:eq(0)').prop('selected', true);
    // limpiarSubTotalGrupo();

    if (servi != '') {
      $(`#${parent} #btnAgregarServicioIntegrante`).show();
      $(`#${parent} #btnQuitarServicioIntegrante`).show();
      $.ajax({
        type: 'POST',
        url:
          'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
        data: { producto: servi },
        dataType: 'json',
        success: function (response) {
          let fechaNacimiento = $datosSocios[index_integrante][2];
          let localidad = $datosSocios[index_integrante][6];

          if (response.result) {
            if (response.cant_horas_sanatorio && servi == '1') {
              console.log('1');
              $(`#${parent} #divHorasServicioIntegrante`).show();
              $(`#${parent} #divPromoIntegrante`).show();
              $(`#${parent} #divBotonIntegrante`).hide();
            } else if (response.cant_horas_sanatorio && servi != '1') {
              console.log('2');
              $(`#${parent} #row_sanatorio_integrante`).show();
              $(`#${parent} #divPromoIntegrante`).hide();
            }

            if (response.cant_horas_servicio) {
              console.log('3');
              $(`#${parent} #divHorasServicioIntegrante`).show();
              if (servi == '25' || servi == '27') {
                $(`#${parent} #divBotonIntegrante`).show();
                $(`#${parent} #divPromoIntegrante`).hide();
                // $(`#${parent} #divPromoSanatorioIntegrante${(index_integrante+1)}`).hide();
              } else {
                $(`#${parent} #divBotonIntegrante`).hide();
              }
            } else if (
              response.cant_horas_sanatorio &&
              !response.cant_horas_servicio &&
              servi != 1
            ) {
              console.log('4');
              let total = calcularPrecio(
                servi,
                8,
                null,
                localidad,
                fechaNacimiento,
                false
              );

              $(`#${parent} .importe-servicio-integrante`).last().val(total[2]);
              $(`#${parent} .importe-base-servicio-integrante`)
                .last()
                .val(total[0]);
              $(`#${parent} .importe-total-servicio-integrante`)
                .last()
                .val(total[1]);

              $(`#${parent} #divHorasServicioIntegrante`).hide();
              $(`#${parent} #divBotonIntegrante`).hide();
              // $(`#${parent} #divPromoSanatorioIntegrante${(index_integrante+1)}`).hide();
              mostrarSubTotalGrupo(calcularSubTotalGrupo(padre), padre);
              //   mostrarSubTotal(calcularSubTotal());
            } else if (
              !response.cant_horas_sanatorio &&
              !response.cant_horas_servicio
            ) {
              console.log('5');
              let total = calcularPrecio(
                servi,
                null,
                8,
                localidad,
                fechaNacimiento,
                false
              );

              $(`#${parent} .importe-servicio-integrante`).last().val(total[2]);
              $(`#${parent} .importe-base-servicio-integrante`)
                .last()
                .val(total[0]);
              $(`#${parent} .importe-total-servicio-integrante`)
                .last()
                .val(total[1]);

              $(`#${parent} divHorasServicioIntegrante`).hide();
              $(
                `#${parent} divHorasSanatorioIntegrante${index_integrante + 1}`
              ).hide();
              $(`#${parent} #divBotonIntegrante`).hide();
              $(`#${parent} #divPromoIntegrante${index_integrante + 1}`).hide();
              // $(`#${parent} #divPromoSanatorioIntegrante${(index_integrante+1)}`).hide();
            } else if (
              !response.cant_horas_sanatorio ||
              (response.cant_horas_servicio && servi != '1')
            ) {
              console.log('6');
              $(`#${parent} #divHorasServicioIntegrante`).hide();
              $(`#${parent} #divPromoSanatorioIntegrante`).hide();
              $(`#${parent} #divPromoIntegrante`).hide();
              let total = calcularPrecio(
                servi,
                null,
                8,
                localidad,
                fechaNacimiento,
                false
              );
              //   if (JSON.parse(localStorage.datos_socio).socio) $('#subtotal-price-socio').html(total[2]);
              //   else $('#subtotal-price').html(total[2]);
              $('#subtotal-price-grupo').html(total[2]);
              $('.subtotal').show();
            }
          }
        }
      });
    } else {
      $(`#${parent} #divHorasServicioIntegrante`).hide();
      $(`#${parent} #row_sanatorio_integrante`).hide();
      $(`#${parent} #divPromoIntegrante`).hide();
      $(`#${parent} #divBotonIntegrante`).hide();
    }
  });
});

//llena el select de los dias
function fillDays(element) {
  for (let i = 1; i <= 31; i++) {
    var cero = i < 10 ? '0' : '';
    $(element).append(
      `<option value="` + cero + i + `">` + cero + i + `</option>`
    );
  }
}

//llena el select de los años
function fillYear(element) {
  var hoy = new Date();
  var anioActual = hoy.getFullYear();
  for (let i = 1918; i <= anioActual; i++) {
    var cero = i < 10 ? '0' : '';
    $(element).append(`<option value="${cero + i}">${cero + i}</option>`);
  }
}

function agregarEventoRadio() {
  $('.checkPuertaIntegrante').click(function () { //dir2

    if ($(this).val() == '0') {
      $(this).parent().parent().parent().parent().parent().parent().find('.divPuerta').show();
      $(this).parent().parent().parent().parent().parent().parent().find('.divSolar').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divManzana').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.solar_integrante').val('');//seba
      $(this).parent().parent().parent().parent().parent().parent().find('.manzana_integrante').val('');//seba
    } else if ($(this).val() == '1') {
      $(this).parent().parent().parent().parent().parent().parent().find('.divPuerta').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divSolar').show();
      $(this).parent().parent().parent().parent().parent().parent().find('.divManzana').show();
      $(this).parent().parent().parent().parent().parent().parent().find('.puerta_integrante').val('');//seba
    } else {
      $(this).parent().parent().parent().parent().parent().parent().find('.divPuerta').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divSolar').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divManzana').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.solar_integrante').val('');//seba
      $(this).parent().parent().parent().parent().parent().parent().find('.manzana_integrante').val('');//seba
      $(this).parent().parent().parent().parent().parent().parent().find('.puerta_integrante').val('');//seba
    }

  });
}

function agregarEventoRadioOmt() {
  $('.checkPuertaIntOmt').click(function () { //dir2

    if ($(this).val() == '0') {
      $(this).parent().parent().parent().parent().parent().parent().find('.divPuerta').show();
      $(this).parent().parent().parent().parent().parent().parent().find('.divSolar').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divManzana').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('#solar_omtben').val('');//seba
      $(this).parent().parent().parent().parent().parent().parent().find('#manzana_omtben').val('');//seba
    } else if ($(this).val() == '1') {
      $(this).parent().parent().parent().parent().parent().parent().find('.divPuerta').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divSolar').show();
      $(this).parent().parent().parent().parent().parent().parent().find('.divManzana').show();
      $(this).parent().parent().parent().parent().parent().parent().find('#puerta_omtben').val('');//seba
    } else {
      $(this).parent().parent().parent().parent().parent().parent().find('.divPuerta').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divSolar').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('.divManzana').hide();
      $(this).parent().parent().parent().parent().parent().parent().find('#solar_omtben').val('');//seba
      $(this).parent().parent().parent().parent().parent().parent().find('#manzana_omtben').val('');//seba
      $(this).parent().parent().parent().parent().parent().parent().find('#puerta_omtben').val('');//seba
    }

  });
}

function agregarEventoChangeDep() {
  $('.depOmtBen').change(function () { //omtgrupo
    let dep = $(this).val();
    let integrante = $(this).attr('integrante');
    console.log('integrante ', integrante);

    if (dep != '') {
      $.ajax({
        type: 'POST',
        url: 'Ajax/obtenerLocalidades.php',
        data: { departamento: dep },
        dataType: 'json',
        success: function (response) {
          if (response.result) {
            $(`#modal-agregar-omt${integrante} .locOmtBen`).empty();
            let loc = response.localidades;
            let element = `<option value="" selected>- Seleccione -</option>`;
            loc.forEach(function (val, i) {
              element +=
                `<option value="` +
                val.id +
                `" data-filial="` +
                val.idFilial +
                `">` +
                val.localidad +
                `</option>`;
            });
            $(`#modal-agregar-omt${integrante} .locOmtBen`).append(element);
          }
        }
      });
    } else {
      $('#locOmtBen').empty();
      $('#locOmtBen').append(`<option value="">- Seleccione -</option>`);
    }
    document
      .querySelector(`#locBen option[value="${$loc}"]`)
      .setAttribute('selected', true);
  });
}

function agregarOmtIntegrante(integrante) { //omtgrupo

  if ($(`#modal-agregar-omt${integrante}`).val() == undefined) {
    let ModalOmtInt = `
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-agregar-omt${integrante}">
        <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
            <h2 class="modal-title">Datos del beneficiario OMT</h2>
            </div>
            <div class="modal-body" id="datos_omt">
            <div class="alert alert-danger alert-dismissable error-benomt-contenedor" id="error-benomt" style="display:none;">
                <strong >Error: </strong><span class="error-benomt"></span>
            </div>
            <form action="" id="beneficiario_omt${integrante}">
                <div class="row" id="omtBen${integrante}">
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control nombre_omtben  solo_letras" value="" name="" id="nombre_omtben">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <label for="">Cédula</label>
                    <input type="text" class="form-control cedula_omtben solo_numeros" value="" name="" id="cedula_omtben">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <label for="">Teléfono</label>
                    <input type="text" class="form-control telefono_omtben solo_numeros" value="" name="" id="telefono_omtben">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                    <label for="">Fecha de nacimiento</label>
                    <input type="date"  placeholder="YYYY-MM-DD"  pattern="(?:19|20)\[0-9\]{2}-(?:(?:0\[1-9\]|1\[0-2\])-(?:0\[1-9\]|1\[0-9\]|2\[0-9\])|(?:(?!02)(?:0\[1-9\]|1\[0-2\])-(?:30))|(?:(?:0\[13578\]|1\[02\])-31))" class="form-control fechan_omtben fn_beneficiario" value="" name="" id="fechan_omtben">
                    </div>
                </div>
                </div>
                <div class="row">
                  <div class="col-md-2"> 
                    <div class="form-group">
                      <label for="" class="texto" >Calle <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error"  maxlength="20" name="calle_omtben" id="calle_omtben" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p> 
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-froup">
                    <label class="texto">Elige una opción <span class="requerido">*</span></label>
                      <fieldset class="radioPuertaOmt">
                        <div class="radio">
                          <label>
                            <input type="radio" id="puertaChecked" name="checkPuerta" class="checkPuertaIntOmt puertaCheckedOmt" value="0">
                            Puerta
                          </label>
                          <label>
                            <input type="radio" id="solarChecked" name="checkPuerta" class="checkPuertaIntOmt solarCheckedOmt" value="1">
                            Solar/manzana
                          </label>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                  <div class="col-md-1 divPuerta" style="display:none;" id="divPuerta">
                    <div class="form-group">
                      <label for="" class="texto" >Puerta <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error" limitecaracteres="4" maxlength="4" name="puerta_omtben" id="puerta_omtben" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
                    </div>
                  </div>
                  <div class="col-md-1 divSolar" style="display:none;" id="divSolar">
                    <div class="form-group">
                      <label for="" class="texto" >Solar <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="solar_omtben" id="solar_omtben" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
                    </div>
                  </div>
                  <div class="col-md-1 divManzana" style="display:none;" id="divManzana">
                    <div class="form-group">
                      <label for="" class="texto" >Manzana <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros" limitecaracteres="4" maxlength="4" name="manzana_omtben" id="manzana_omtben" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="" class="texto" >Esquina <span class="requerido">*</span></label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error" maxlength="20" name="esquina_omtben" id="esquina_omtben" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p> 
                    </div>
                  </div>
                  <div class="col-md-1">
                    <div class="form-group">
                      <label for="" class="texto" >Apartamento</label>
                      <input type="text" class="form-control calcularCaracteresDisponibles input-error"  maxlength="4" name="apto_omtben" id="apto_omtben" required>
                      <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="" class="texto" >Referencia <span class="requerido">*</span></label>
                      <input type="text" class="form-control input-error"  name="referencia_omtben" id="referencia_omtben" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                    <label for="">Departamento</label>
                    <select name="depOmtBen" id="depOmtBen" integrante = ${integrante} class="custom-select form-control depOmtBen input-error"><option value="">- Seleccione -</option></select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="">localidad</label>
                      <select name="locOmtBen" id="locOmtBen" class="custom-select form-control locOmtBen input-error"><option value="">- Seleccione -</option></select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                    <label for="" class="texto" ><span class="requerido">NOTA:</span> el único medio de pago para éste servicio es tarjeta de crédito</label>
                    </div>
                  </div>
                </div>
            </form>
            </div>
            <div class="modal-footer">
            <input type="hidden" value="">
            <button id="btnCancelarOmt" type="button" onclick="$('#modal-agregar-omt${integrante}').modal('hide');" class="btn btn-secondary">Cancelar</button>
            <button id="btnGuardarOmtBen" onclick="guardarDatosOmtGrupo(${integrante})" type="button" class="btn btn-primary">Guardar</button>
            <button id="btnEliminarOmt" type="button" class="btn btn-danger btnEliminarOmt" onclick="eliminarBeneficiario(${integrante})" style="display: none;">Eliminar beneficiario</button>
            </div>
        </div>
        </div>
    </div>`;
    $(`#divModalInt${integrante}`).append(ModalOmtInt);
    obtenerDepartamentosOmtGrupo(integrante);
    agregarEventoChangeDep();
    agregarEventoRadioOmt(); //dir2
    $(`#modal-agregar-omt${integrante}`).modal('show');
  } else {

    if ($arrBenOmtGrupo[Number(integrante) - 1] && $arrBenOmtGrupo[Number(integrante) - 1].length != 0 || $arrBenOmtGrupo[Number(integrante) - 1] != undefined) {
      $(`#modal-agregar-omt${integrante} .btnEliminarOmt`).show();
    }
    $(`#modal-agregar-omt${integrante}`).modal('show');
  }

}

function agregarPersona(e) {
  e.preventDefault();

  if ($add_integrante >= 1) {
    $add_integrante++;
    $('#cedulas-integrantes').append(`
    <div class="row cedula_adi"  id="integrante${$add_integrante}" >
      <div class="col-md-3">
        <div class="form-group">
          <label for=""class="texto" >Cédula <span class="requerido">*</span></label>
          <input type="text" class="form-control solo_numeros valced_integrante" required>
        </div>
      </div>
    </div>
  `);
  }
}

function quitarPersona(e) {
  e.preventDefault();

  if ($add_integrante > 1) {
    $('#integrante' + $add_integrante).remove();
    $add_integrante--;
  }
}

function eliminarBeneficiario(integrante) { //omtgrupo
  limpiarDatosOmt(integrante);
  $arrBenOmtGrupo[Number(integrante) - 1] = 0;
  if ($arrBenOmt.length == 0) {
    $omtGrupo = false;
  }
  $(`#importe-omt-integrante${integrante}`).val(0);
  let padre = $(`#servicios_integrante${integrante}`);
  let subtotal = calcularSubTotalGrupo(padre);
  mostrarSubTotalGrupo(subtotal, padre);
  calcularSubTotalGeneral()
  $(`#modal-agregar-omt${integrante}`).modal('hide');
  $(`#servicios_integrante${integrante} .span-omtGrupo`).remove();
  $(`#servicios_integrante${integrante} .btnEliminarOmt`).hide();
  $(`#servicios_integrante${integrante} .btnAgregarOmtInt`).text(`Agregar OMT`);
}

/**
 * [limpia todos los campos del formulario de datos del beneficiario omt
 *
 * @return  {undefined}
 */
function limpiarDatosOmt(integrante) { //omtgrupo
  $(`#modal-agregar-omt${integrante} #nombre_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #cedula_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #telefono_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #calle_omtben`).val(''); //dir2
  $(`#modal-agregar-omt${integrante} #puerta_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #apto_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #manzana_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #solar_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #esquina_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #referencia_omtben`).val('');
  $(`#modal-agregar-omt${integrante} #fechan_omtben`).val('');
  $(`#modal-agregar-omt${integrante} .depOmtBen option:eq(0)`).prop('selected', true);
  $(`#modal-agregar-omt${integrante} .locOmtBen`).html('');
  $(`#servicios_integrante${integrante} .btnAgregarOmtInt`).text(`Agregar OMT`);
  $(`#servicios_integrante${integrante} #span-omtGrupo`).remove();
}

/**
 * guardarDatosOmt
 * Guarda los datos del beneficiario OMT en un array
 * @return {void}
 */
function guardarDatosOmtGrupo(integrante) { //omtgrupo
  let error = false;
  let socio = null;
  let mensaje = '';
  let indice = Number(integrante) - 1;

  let nombre_ben = $(`#modal-agregar-omt${integrante} #nombre_omtben`).val().toUpperCase();
  let cedula_ben = $(`#modal-agregar-omt${integrante} #cedula_omtben`).val();
  let telefono_ben = $(`#modal-agregar-omt${integrante} #telefono_omtben`).val();
  let calle_ben = $(`#modal-agregar-omt${integrante} #calle_omtben`).val().toUpperCase(); //dir2
  let puerta_ben = $(`#modal-agregar-omt${integrante} #puerta_omtben`).val().toUpperCase();
  let apto_ben = $(`#modal-agregar-omt${integrante} #apto_omtben`).val().toUpperCase();
  let esquina_ben = $(`#modal-agregar-omt${integrante} #esquina_omtben`).val().toUpperCase();
  let referencia_ben = $(`#modal-agregar-omt${integrante} #referencia_omtben`).val().toUpperCase();
  let manzana_ben = $(`#modal-agregar-omt${integrante} #manzana_omtben`).val().toUpperCase();
  let solar_ben = $(`#modal-agregar-omt${integrante} #solar_omtben`).val().toUpperCase();
  let fechan_ben = $(`#modal-agregar-omt${integrante} #fechan_omtben`).val();
  let localidad = $(`#modal-agregar-omt${integrante} #locOmtBen`).val();
  let filial = $(`#modal-agregar-omt${integrante} #locOmtBen option:selected`).data('filial');
  let edad = calcularEdad(fechan_ben);
  socio = existeEnPadron(cedula_ben);

  if (
    nombre_ben == '' ||
    cedula_ben == '' ||
    telefono_ben == '' ||
    fechan_ben == '' ||
    filial == ''
  ) {
    error = true;
    mensaje = 'Debe completar todos los datos del beneficiario con la cedula';
  } else if (!comprobarCI(cedula_ben)) {
    error = true;
    mensaje = 'La cédula es incorrecta';
  }
  else if (calle_ben == '') {//seba
    error = true;
    mensaje = 'Debe ingresar la calle';
  }
  else if (esquina_ben == '') {//seba
    error = true;
    mensaje = 'Debe ingresar la esquina';
  }
  else if (referencia_ben == '') {//seba
    error = true;
    mensaje = 'Debe ingresar una refencia';
  }
  else if (!$(`#modal-agregar-omt${integrante} .puertaCheckedOmt`).is(':checked') && !$(`#modal-agregar-omt${integrante} .solarCheckedOmt`).is(':checked')) {//seba
    error = true;
    mensaje = 'Debe seleccionar para ingresar nro de puerta o solar/manzana';
  }
  else if ($(`#modal-agregar-omt${integrante} .puertaCheckedOmt`).is(':checked') && (puerta_ben == '' || puerta_ben == 0)) { //seba //dir2
    error = true;
    mensaje = 'Debe ingresar el numero de puerta(No puede ser 0)';
  } else if ($(`#modal-agregar-omt${integrante} .solarCheckedOmt`).is(':checked') && (solar_ben == '' || manzana_ben == '' || solar_ben == 0 || manzana_ben == 0)) {//seba
    error = true;
    mensaje = 'Debe ingresar solar y manzana(No puede ser 0)';
  } else if (edad < 18) {
    error = true;
    mensaje = 'El beneficiario es menor de edad';
  } else if (socio[0]) {
    error = true;
    mensaje = socio[1];
  } else if (cedula_ben == $cedula) {
    error = true;
    mensaje = 'La cédula es igual a la del socio actual';
  } else {
    if ($(this).find(`#modal-agregar-omt${integrante} .puertaCheckedOmt`).is(':checked')) {
      direccion_ben = (apto_ben != '') ? calle_ben.substr(0, 14) + ' ' + puerta_ben + '/' + apto_ben + ' E:' : calle_ben.substr(0, 17) + ' ' + puerta_ben + ' E:';
      direccion_ben += esquina_ben.substr(0, (36 - direccion_ben.length)); //di
    } else {
      direccion_ben = (apto_ben != '') ? calle_ben.substr(0, 14) + ' M:' + manzana_ben + ' S:' + solar_ben + '/' + apto_ben : calle_ben.substr(0, 14) + ' M:' + manzana_ben + ' S:' + solar_ben + ' E:';
      direccion_ben += (apto_ben == '') ? esquina_ben.substr(0, (36 - direccion_ben.length)) : ''; //di
    }
    $omtGrupo = true;
    $arrBenOmtGrupo[indice] = 0;
    $arrBenOmtGrupo[indice] = [
      nombre_ben,
      cedula_ben,
      telefono_ben,
      fechan_ben,
      direccion_ben,
      filial,
      edad,
      localidad,
      calle_ben, //dir2
      puerta_ben,
      apto_ben,
      manzana_ben,
      solar_ben,
      esquina_ben,
      referencia_ben
    ];
  }
  if (calle_ben.substr(4).match(/\d+/) != null) {//seba
    alert('Por favor no ingrese el número de puerta en "Calle", ingréselo en el campo correspondiente');
  }

  if (error) {
    $('.error-benomt').text(mensaje);
    $('.error-benomt-contenedor').show().fadeOut(10000);//seba AGREGAR LA CLASE AL CONTENEDOR DEL HTML
    $arrBenOmtGrupo[indice] = 0;
  } else {
    let precio_omt = calcularPrecio(
      106,
      null,
      8,
      $datosSocios[indice][6],
      $datosSocios[indice][2],
      false
    );
    let padre = $(`#servicios_integrante${integrante}`);
    $comision_omtGrupo = precio_omt[2];
    $arrBenOmtGrupo[indice].push($comision_omtGrupo);
    $(`#importe-omt-integrante${integrante}`).val($comision_omtGrupo);
    let subtotal = calcularSubTotalGrupo(padre);
    mostrarSubTotalGrupo(subtotal, padre);
    calcularSubTotalGeneral()
    $(`#btnOmtInt${integrante}`).append(
      `<span id="span-omtGrupo" class="badge badge-success span-omtGrupo" style="position:absolute;">1</span>`
    );
    $(`#servicios_integrante${integrante} #btnAgregarOmtGrupo${integrante}`).text('Modificar OMT');
    $(`#modal-agregar-omt${integrante}`).modal('hide');
  }
}

/**
 * obtenerDepartamentosOmt
 * Llena los select de departamentos del modal agregar omt
 * @param void
 * @return {void}
 */
function obtenerDepartamentosOmtGrupo(integrante) { //omtgrupo
  console.log(integrante);
  if (!$(`#modal-agregar-omt${integrante} .depOmtBen`).val() && !$(`#modal-agregar-omt${integrante} .locOmtBen`).val()) {
    $.ajax({
      type: 'POST',
      url: 'Ajax/obtenerDepartamentos.php',
      dataType: 'json',
      success: function (response) {
        if (response.result) {
          var depas = response.departamentos;
          depas.forEach(function (val, i) {
            $(`#modal-agregar-omt${integrante} .depOmtBen`).append(
              `<option value="` + val.id + `">` + val.departamento + `</option>`
            );
          });
        }
      }
    });
  }
}

function agregarServicioIntegrante(integrante) {
  var pSeleccionados = $(
    `#servicios_integrante${integrante} .servicio_integrante`
  )
    .map(function () {
      return $(this).val();
    })
    .get();
  let acotados = [
    '11',
    '22',
    '30',
    '31',
    '32',
    '33',
    '34',
    '35',
    '36',
    ' 38',
    '39',
    '40',
    '41'
  ];
  let is_acotado = pSeleccionados.map(function (val) {
    return acotados.includes(val);
  });

  if (
    $(`#servicios_integrante${integrante} .servicio_integrante`).last().val() ==
    null
  ) {
    $('#error-productos').text('Debe seleccionar un producto');
    $('#error-productos').show().fadeOut(10000);
  } else if (is_acotado.includes(true)) {
    $('.error-productos').text('No puede ofrecer otro servicio');
    $('#error-productos').show().fadeOut(10000);
  } else if (
    $(`#servicios_integrante${integrante} .servicio_integrante`).last().val() ==
    '25' ||
    $('.produc').last().val() == '27'
  ) {
    $('.error-productos').text('No puede ofrecer otros servicios');
    $('#error-productos').show().fadeOut(10000);
  } else {
    //obtenemos los id de los servicios de todos los select de servicios
    var serviSelect = $(
      `#servicios_integrante${integrante} .servicio_integrante`
    )
      .map(function () {
        return $(this).val();
      })
      .get();

    //valores de ids a chequear
    var valoresAchequear = ['1', '2', '3', '5'];

    //guardamos en una variable el resultado del chequeo
    var check = serviSelect.some((e) => valoresAchequear.includes(e));

    //definimos por defecto el div del select de horas sanatorio
    var hsanatorio = `<div class="col-md-4">
            <div class="form-group">
              <div id="divHorasSanatorio${integrante}" class="divHorasServicioIntegrante " style="display:none">
                <label for="" class="texto" >Cantidad horas sanatorio  <span class="requerido">*</span></label>
                <select id="hrsanatorio_int${integrante}" name="hrsanatorio" class="custom-select form-control hsanatorio_int">
                    <option value="0" data-base="1" disabled>Seleccione cantidad</option>
                    <option value="8" data-base="0" data-base="1">8 hs</option>
                    <option value="16" data-base="0" data-base="0">16 hs</option>
                    <option value="24" data-base="0" data-base="0">24 hs</option>
                </select>
              </div>
          </div>
        </div>`;

    //si exite algun select que que ya tenga sanatorio entonces modificamos el valor de hsanatorio para que no agregue select horas sanatorios al div
    if (check) {
      hsanatorio = '';
    }

    //definimos el div que contendra los select del servicio adicional
    var select = `<div class="row hrservicios_int${integrante}" id="hrservicios_int${integrante}">
            <div class="col-md-4" >
                <div class="form-group">
                    <label for="" class="texto" >Servicios <span class="requerido">*</span></label>
                    <select id="servicioadi_int${integrante}" name="" class="custom-select form-control servicioadi_int servicio_integrante">
                        <option value="" selected disabled>Seleccione servicio</option>
                    </select>
                    <input type="hidden" class="importe-servicio-int" />
                    <input type="hidden" class="importe-base-servicio-int"/>
                    <input type="hidden" class="importe-total-servicio-int"/>      
                    <input type="hidden" class="index_integrante" value="${integrante - 1
      }" />
                </div>
            </div>
            ${hsanatorio} 
            <div class="col-md-2">
                <div class="form-group">
                    <div id="divHorasServicio${integrante}" class="divHorasServicioIntegrante" style="display:none">
                        <label for="" class="texto">Horas servicio <span class="requerido">*</span></label>
                        <select id="hrservicio_int${integrante}" name="hrservicio" class="custom-select form-control hservicio_int">
                            <option value="0"  data-base="0"selected disabled>Seleccione cantidad</option>
                            <option value="8" data-base="0">8 hs</option>
                            <option value="16" data-base="0">16 hs</option>
                            <option value="24" data-base="0">24 hs</option>
                        </select>
                        <input type="hidden" class="importe-servicio-integrante"/>
                        <input type="hidden" class="importe-base-servicio-integrante"/>
                        <input type="hidden" class="importe-total-servicio-integrante"/>
                    </div>
                </div>
            </div>
        </div>`;

    var s = `#hrservicio_int${integrante}`;
    let padre = `#servicios_integrante${integrante}`;
    listarServiciosAdicionalesIntegrante(padre, integrante);
    $(`#nuevos_servicios_integrante${integrante}`).append(select);
    EventoChangeProductosAdicionalesIntegrante(integrante);
  }
}

function quitarServicioIntegrante(integrante) {
  $(`#nuevos_servicios_integrante${integrante} .hrservicios_int${integrante}`)
    .last()
    .remove();
  let padre = $(`#servicios_integrante${integrante}`);
  mostrarSubTotalGrupo(calcularSubTotalGrupo(padre), padre);
  calcularSubTotalGeneral();
}

function listarServiciosAdicionalesIntegrante(padre, integrante) {
  let aValoresInt = $(`${padre} .servicio_integrante`)
    .map(function () {
      return $(this).val();
    })
    .get();
  let code = $arrGrupo[integrante - 1].code;
  let filial = $datosSocios[integrante - 1][6];
  let dep = Number($datosSocios[integrante - 1][4]);
  $.ajax({
    type: 'POST',
    url: 'Ajax/listarServiciosAdicionales.php',
    data: { array: aValoresInt, code: code, localidad: filial, esGrupo: true },
    dataType: 'json',
    success: function (response) {
      var serv = response.servicios;
      serv.forEach(function (val, i) {
        if (
          val.id != 4 ||
          (val.id == 4 && [8, 7, 17, 3, 16, 5, 2, 9, 6, 14].includes(dep))
        ) {
          $(`#nuevos_servicios_integrante${integrante} .servicio_integrante`)
            .last()
            .append(
              `<option value="` + val.id + `">` + val.servicio + `</option>`
            );
        }
      });
    }
  });
}

/*function EventoChangeProductosAdicionalesIntegrante(integrante) {
    let aValores = $(`#servicios_integrante${integrante} .servicio_integrante`).map(function () {return $(this).val();}).get();
  
    // consulta la cantidad de horas de los servicios adicionales
    $(document.body).on('change', '.servicioadi_int', function (e) {
      let servi = $(this).val();
      let padre = $(this).parent().parent().parent().parent().attr('id');
      let padre2 = $(this).parent().parent().parent().parent();
      let padre_principal2 =$(this).parent().parent().parent().parent().parent();
      let padre_principal =$(this).parent().parent().parent().parent().attr('id');

      console.log(padre_principal2);
    
      if (servi != '') {
        $('#btnAgregarServicio').show();
        $.ajax({
          type: 'POST',
          url: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
          data: { producto: servi },
          dataType: 'json',
          success: function (response) {
            if (response.result) {
              $(`${padre} .hrservicioadi_int option:eq(0)`).prop("selected", true);
  
              if (servi == '1') {
                console.log('1');
                // const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, true); 
      
                // $(this).next().val(importe[2]);
                $(`#${padre} .hrservicioadi_int option:eq(0)`).prop("selected", true);
                $(`#${padre} .hrservicioadi_int`).next().val("");
                $(`#${padre} .divHorasServicioIntegrante`).show();
                $(`#${padre} .divComentarioIntegrante`).show();
                // $('#' + padre + ' .divPromoSocio').show();
                $(`#row_sanatorio_integrante${integrante}`).hide();
              } else if (response.cant_horas_sanatorio) {
                console.log('2b');
                $(`#row_sanatorio_integrante${integrante}`).show();
              }
              
              if (response.cant_horas_servicio) {
                console.log('3b');
                // const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, true); 
                 // $(this).next().val(importe[2]);
                $(`#${padre} .hrservicioadi_int option:eq(0)`).prop("selected", true);
                $(`#${padre} .hrservicioadi_int`).next().val("");
                $(`#${padre} .divHorasServicioIntegrante`).show();
                // $('#' + padre + ' .divPromoSocio').hide();
              } else if (servi != 1 && servi != '5') {
                console.log('4b');
                // const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false); 
                // $(this).next().val(importe[2]);
                // $(`#${padre} .hrservicioadi_int`).hide();
                // $('#' + padre + ' .divComentarioSocio').hide();
                // $('#' + padre + ' .divPromoSocio').hide();
                $(`#row_sanatorio_integrante${integrante}`).hide();
              }
  
              // const $fechaNacimiento = $('#natalicioano').val() + '-' + $('#nataliciomes').val() + '-' + $('#nataliciodia').val();
              // const $localidad = $('#locBen option:selected').data('filial') ? $('#locBen option:selected').data('filial') : JSON.parse(localStorage.datos_socio).filial;

              const indexIntegrantes = $(`#${padre} .index_integrante`).val()
              const $fechaNacimiento = $datosSocios[indexIntegrantes][2];
              const $localidad = $datosSocios[indexIntegrantes][6];
              
              if (!response.cant_horas_sanatorio && !response.cant_horas_servicio) {

                // const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false);                         
                // $(this).parent().append(`<input type="hidden" class="importe-servicio" value="${importe[2]}"`); 
                // $(`#${padre}`).find(".importe-servicio").first().val(importe[2]);
                // mostrarSubTotalGrupo(calcularSubTotalGrupo(padre_principal),padre_principal);
              } else if (response.cant_horas_sanatorio && !response.cant_horas_servicio) { // Sanatorio
                if (servi == "1") {
                  const importe = calcularPrecio(1, 8, null, $localidad, $fechaNacimiento, true);
       
                
                  $('#row_sanatorio_socio .importe-servicio').val("")
                  $(".importe-servicio").last().val("");
                } else {    
     
                  $(`#${padre} .divHorasServicioIntegrante`).hide();           
                  const importe = calcularPrecio(servi, null, 8, $localidad, $fechaNacimiento, false);
                  const element = document.createElement("INPUT");
                  if (e.target.parentNode.querySelector('.importe-servicio-integrante')) {
                    e.target.parentNode.removeChild(
                      e.target.parentNode.querySelector('.importe-servicio-integrante') //heeeeee
                    );
                  }
                  element.type = "hidden";
                  element.classList.add("importe-servicio-integrante");
                  element.value = importe[2];
                  e.target.parentNode.append(element);
                  mostrarSubTotalGrupo(calcularSubTotalGrupo(padre2),padre_principal2);
                }
                // mostrarSubTotalGrupo(calcularSubTotalGrupo(padre_principal),padre_principal);
              } else { // Tradicional              
                // mostrarSubTotalGrupo(calcularSubTotalGrupo(padre_principal),padre_principal);          
              }
            }
          }
        });
      } else {
        $('#divHorassanatorio').hide();
      }
    });
}*/

function EventoChangeProductosAdicionalesIntegrante(integrante) {
  // consulta la cantidad de horas de los servicios adicionales
  $(document.body).on('change', '.servicioadi_int', function (e) {
    let servi = $(this).val();
    let $parent = $(this).parent().parent().parent();
    let parentElement = $(this).parent().parent().parent().parent().parent();
    if (servi != '') {
      $('#btnAgregarServicio').show();
      $.ajax({
        type: 'POST',
        url:
          'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
        data: { producto: servi },
        dataType: 'json',
        success: function (response) {
          const index_integrante = $parent.find(`.index_integrante`).val();
          const fechaNacimiento = $datosSocios[index_integrante][2];
          const localidad = $datosSocios[index_integrante][6];

          // LIMPIA LOS VALORES
          $parent.find(`.importe-servicio-integrante`).last().val('');
          $parent.find(`.importe-base-servicio-integrante`).last().val('');
          $parent.find(`.importe-total-servicio-integrante`).last().val('');
          mostrarSubTotalGrupo(
            calcularSubTotalGrupo(parentElement),
            parentElement
          );
          $('#subtotal-price-grupo').html('');
          $('.subtotal').hide();

          if (response.result) {
            if (response.cant_horas_sanatorio && servi == '1') {
              $parent.find(`.divHorasServicioIntegrante`).show();
              $parent.find(`.divPromoIntegrante`).show();
              $parent.find(`.divBotonIntegrante`).hide();
            } else if (response.cant_horas_sanatorio && servi != '1') {
              $parent.find(`.row_sanatorio_integrante`).show();
              $parent.find(`.divPromoIntegrante`).hide();
            }

            if (response.cant_horas_servicio) {
              $parent.find(`.divHorasServicioIntegrante`).show();

              if (servi == '25' || servi == '27') {
                $parent.find(`.divBotonIntegrante`).show();
                $parent.find(`.divPromoIntegrante`).hide();
              } else {
                $parent.find(`#divBotonIntegrante`).hide();
              }
            } else if (
              response.cant_horas_sanatorio &&
              !response.cant_horas_servicio &&
              servi != 1
            ) {
              let total = calcularPrecio(
                servi,
                8,
                null,
                localidad,
                fechaNacimiento,
                false
              );

              $parent.find(`.divHorasServicioIntegrante .importe-servicio-integrante`).val(total[2]);
              $parent.find(`.divHorasServicioIntegrante .importe-base-servicio-integrante`).val(total[0]);
              $parent.find(`.divHorasServicioIntegrante .importe-total-servicio-integrante`).val(total[1]);
              $parent.find(`.importe-servicio-int`).val(total[2]);
              $parent.find(`.importe-base-servicio-int`).val(total[0]);
              $parent.find(`.importe-total-servicio-int`).val(total[1]);
              $parent.find(`.divHorasServicioIntegrante`).hide();
              $parent.find(`.divBotonIntegrante`).hide();

              calcularSubTotalGeneral();
              mostrarSubTotalGrupo(
                calcularSubTotalGrupo(parentElement),
                parentElement
              );
            } else if (
              !response.cant_horas_sanatorio &&
              !response.cant_horas_servicio
            ) {
              let total = calcularPrecio(
                servi,
                null,
                8,
                localidad,
                fechaNacimiento,
                false
              );

              $parent.find(`.importe-servicio-integrante`).last().val(total[2]);
              $parent
                .find(`.importe-base-servicio-integrante`)
                .last()
                .val(total[0]);
              $parent
                .find(`.importe-total-servicio-integrante`)
                .last()
                .val(total[1]);
              $parent.find(`.divHorasServicioIntegrante`).hide();
              $parent.find(`.divHorasSanatorioIntegrante`).hide();
              $parent.find(`.divBotonIntegrante`).hide();
              $parent.find(`.divPromoIntegrante`).hide();

              mostrarSubTotalGrupo(
                calcularSubTotalGrupo(parentElement),
                parentElement
              );
              calcularSubTotalGeneral();
            } else if (
              !response.cant_horas_sanatorio ||
              (response.cant_horas_servicio && servi != '1')
            ) {
              let total = calcularPrecio(
                servi,
                null,
                8,
                localidad,
                fechaNacimiento,
                false
              );

              $parent.find(`.divHorasServicioIntegrante`).hide();
              $parent.find(`.divPromoSanatorioIntegrante`).hide();
              $parent.find(`.divPromoIntegrante`).hide();

              calcularSubTotalGeneral();
              mostrarSubTotalGrupo(
                calcularSubTotalGrupo(parentElement),
                parentElement
              );
            }
          }
        }
      });
    } else {
      $('#divHorassanatorio').hide();
    }
  });
}

function eventoSiguienteAtrasBotones() {
  $('#btnSiguienteInt4').click(function () {
    siguienteGrupo(3, 4);
  });

  $('#btnAtrasInt4').click(function (e) {
    mostrarValidacionMedioPagoGrupo();
  });
}

// Valida contra padron el grupo de cedulas
function validarCedulaPadronGrupo(e) {
  e.preventDefault();
  let error = false;
  cedulasGrupo = [];

  $('#cedulas-integrantes .valced_integrante').each(function () {
    const $cedula = $(this).val();
    if (!$cedula) {
      $('.error-validacion-cedula-grupo').text(
        'Debe completar todas las cédulas'
      );
      $('#error-validacion-cedula-grupo').show().fadeOut(10000);
      error = true;
    } else if (!comprobarCI($cedula)) {
      $('.error-validacion-cedula-grupo').text(
        `La cedula ${$cedula} es incorrecta`
      );
      $('#error-validacion-cedula-grupo').show().fadeOut(10000);
      error = true;
    } else {
      cedulasGrupo.push($cedula);
    }
  });

  if (!error) {
    $.ajax({
      url: 'Ajax/validarPadronGrupo.php',
      type: 'POST',
      data: { cedulasAfiliados: cedulasGrupo },
      dataType: 'json',
      success: function (response) {
        if (response.result) {
          mostrarPasoUnoGrupo();
          $arrGrupo = response.data_socios;
        } else {
          $('.error-validacion-cedula-grupo').text(response.message);
          $('#error-validacion-cedula-grupo').show().fadeOut(10000);
        }
      }
    });
  }
}

function llenarCamposGrupo() {
  $arrGrupo.forEach(function (val, index) {
    $(`#datos_integrante${index} .nom_integrante`).val('aaaaaaa');
    $(`#datos_integrante${index} .dir_integrante`).val('sfgrfgds');
    $(`#datos_integrante${index} .cel_integrante`).val('096854754');
    $(`#datos_integrante${index} .dian_integrante option:eq(4)`).prop(
      'selected',
      true
    );
    $(`#datos_integrante${index} .mesn_integrante option:eq(10)`).prop(
      'selected',
      true
    );
    $(`#datos_integrante${index} .anion_integrante option:eq(4)`).prop(
      'selected',
      true
    );
    $(`#datos_integrante${index} .dep_integrante option:eq(4)`).prop(
      'selected',
      true
    );
    $(`#datos_integrante${index} .dep_integrante`).change();
    $(`#datos_integrante${index} .loc_integrante option:eq(4)`).prop(
      'selected',
      true
    );
  });
}

function obtenerDepartamentosGrupo() {
  $.ajax({
    type: 'POST',
    url: 'Ajax/obtenerDepartamentos.php',
    dataType: 'json',
    success: function (response) {
      if (response.result) {
        var depas = response.departamentos;
        let dep = ``;
        depas.forEach(function (val, i) {
          dep +=
            `<option value="` + val.id + `">` + val.departamento + `</option>`;
        });
        $('.dep_integrante').append(dep);
      }
    }
  });
}

function listarServiciosGrupo() {
  $datosSocios.forEach(function (val, index) {
    $.ajax({
      type: 'POST',
      url: 'Ajax/listarServicios.php',
      dataType: 'json',
      data: {
        code: localStorage.getItem('code'),
        localidad: $filial,
        esGrupo: true
      },
      success: function (response) {
        $('#servicios_int' + (index + 1)).html('');
        var opciones = `<option value="" selected>- Seleccione -</option>`;
        var serv = response.servicios;
        serv.forEach(function (val, i) {
          opciones +=
            `<option value="` + val.id + `">` + val.servicio + `</option>`;
          // if (val.id != 4 || (val.id == 4 && [8, 7, 17, 3, 16, 5, 2, 9, 6, 14].includes(Number($("#depBen").val())))) {
          //   opciones += `<option value="` + val.id + `">` + val.servicio + `</option>`;
          // }
        });
        $('#servicios_int' + (index + 1)).append(opciones);
      }
    });
  });
}

// PRIMER PASO - DATOS PERSONALES
function mostrarPasoUnoGrupo() {
  $('#validacion_cedula_grupo').hide();
  $('#pasodos-grupo').hide();
  $('#pasouno-grupo').show();
  $('#pasotres-grupo').hide();
  obtenerDepartamentosGrupo();
  $datos_integrante = 0;
  $('#datosintegrantes').html('');

  if ($datosSocios.length == 0) {
    cedulasGrupo.forEach(function (value, index) {
      $('#datosintegrantes').append(`<hr> 
      <form class="" id="datos_integrante${index}">
      <div class="row">
        <div class="alert alert-danger alert-dismissable error-datos-grupo" id="error-datos-grupo${index}">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Error: </strong><span class="error-datos"></span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Cédula</label></div>
            <input type="text" class="form-control solo_letras ced_integrante" name="ced_integrante" id="" value="${value}" required disabled>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Nombre completo <span class="requerido">*</span></label></div>
            <input type="text" class="form-control solo_letras nom_integrante" name="nom_integrante" id="nom_integrante" value="" required>
          </div>
        </div>
        <div class="col-md-2">
          <div><label for="" class="texto" >Fecha de nacimiento <span class="requerido">*</span></label></div>
            <div class="form-group">
              <select name="" id="" class="custom-select form-control dian_integrante">
                <option value="" >- Dia -</option>
              </select>
            </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <select name="" id="" class="custom-select form-control mesn_integrante" style="margin-top: 3rem;">
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
            <select name="" id="" class="custom-select form-control anion_integrante" style="margin-top: 3rem;">
              <option value="">- Año -</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-2"> 
          <div class="form-group">
            <label for="" class="texto" >Calle <span class="requerido">*</span></label>
            <input type="text" class="form-control calcularCaracteresDisponibles input-error calle_integrante"  maxlength="20" name="calle_integrante" id="calle_integrante" required>
            <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p> 
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-froup">
          <label class="texto">Elige una opción <span class="requerido">*</span></label>
            <fieldset class="radioPuerta">
              <div class="radio">
                <label>
                  <input type="radio" id="puertaChecked" name="checkPuerta" class="checkPuertaIntegrante puertaChecked" value="0">
                  Puerta
                </label>
                <label>
                  <input type="radio" id="solarChecked" name="checkPuerta" class="checkPuertaIntegrante solarChecked" value="1">
                  Solar/manzana
                </label>
              </div>
            </fieldset>
          </div>
        </div>
        <div class="col-md-1 divPuerta" style="display:none;" id="divPuerta">
          <div class="form-group">
            <label for="" class="texto" >Puerta <span class="requerido">*</span></label>
            <input type="text" class="form-control calcularCaracteresDisponibles input-error puerta_integrante" limitecaracteres="4" maxlength="4" name="puerta_integrante" id="puerta_integrante" required>
            <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
          </div>
        </div>
        <div class="col-md-1 divSolar" style="display:none;" id="divSolar">
          <div class="form-group">
            <label for="" class="texto" >Solar <span class="requerido">*</span></label>
            <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros solar_integrante" limitecaracteres="4" maxlength="4" name="solar_integrante" id="solar_integrante" required>
            <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
          </div>
        </div>
        <div class="col-md-1 divManzana" style="display:none;" id="divManzana">
          <div class="form-group">
            <label for="" class="texto" >Manzana <span class="requerido">*</span></label>
            <input type="text" class="form-control calcularCaracteresDisponibles input-error solo_numeros manzana_integrante" limitecaracteres="4" maxlength="4" name="manzana_integrante" id="manzana_integrante" required>
            <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label for="" class="texto" >Esquina <span class="requerido">*</span></label>
            <input type="text" class="form-control calcularCaracteresDisponibles input-error esquina_integrante" maxlength="20" name="esquina_integrante" id="esquina_integrante" required>
            <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">20</span></p> 
          </div>
        </div>
        <div class="col-md-1">
          <div class="form-group">
            <label for="" class="texto" >Apartamento</label>
            <input type="text" class="form-control calcularCaracteresDisponibles input-error apto_integrante"  maxlength="4" name="apto_integrante" id="apto_integrante" required>
            <p class="small">Caracteres disponibles: <span class="text-danger caracteresDisponibles">4</span></p> 
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label for="" class="texto" >Referencia <span class="requerido">*</span></label>
            <input type="text" class="form-control input-error referencia_integrante"  name="referencia_integrante" id="referencia_integrante" required>
          </div>
        </div>
      </div>
      <div class="row" >
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Departamento <span class="requerido">*</span></label></div>
            <select name="depBen" id="dep_integrante" class="custom-select form-control dep_integrante"><option value="">- Seleccione -</option></select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Localidad <span class="requerido">*</span></label></div>
            <select name="locBen" id="loc_integrante" class="custom-select form-control loc_integrante"><option value="">- Seleccione -</option></select>
          </div>
        </div>
      </div>
      <div class="row" >
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Celular <span class="requerido">*</span> </label></div>
            <input type="text" class="form-control solo_numeros cel_integrante" maxlength="9" name="" id= required value="">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Teléfono fijo</label></div>
            <input type="text" class="form-control solo_numeros tel_integrante" maxlength="9" name="" id="" required value="">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Teléfono alternativo</label></div>
            <input type="text" class="form-control solo_numeros tel_alt_integrante" name="" id="" required value="">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div><label for="" class="texto" >Correo electrónico</label></div>
            <input type="text" class="form-control mail_integrante" name="" id="" required value="">
          </div>
        </div>
      </div>
      <div class="row" id="divDatosExtraIntegrante" style="margin-top: 2rem;">
        <div class="col-md-12">
          <fieldset id="dato_extra_integrante${index}">
          <label class="texto">Elige una opción en caso de aplicar a alguna</label>
            <div class="row">
              <div class="col-md-3">
                <label>
                  <input type="radio" name="dato_adicional" value="1"> Competencia
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
            <label for="" class="texto" >IMPORTANTE:  Los campos con <span class="requerido">*</span> son obligatorios</label>
          </div>
        </div>
      </div>
    </form>`);
      $datos_integrante++;
    });
    agregarEventoRadio();
    $('.error-datos-grupo').hide();
    fillDays('.dian_integrante');
    fillYear('.anion_integrante');
  }
}

function agregarEventoCalcularCaracteres() {
  $('.calcularCaracteresDisponibles').keyup(function () {
    $(this).next().last().children().text(calcularCaracteresDisponibles(this));
  });
}

function mostrarPasoDosGrupo() {
  if ($('#subtotal-price-grupo').text() === '') {
    $datosSocios.forEach(function (value, index) {
      $('#servicios_integrantes').append(`<hr>
        <div id="servicios_integrante${index + 1
        }" class="servicio_integrantes" data-cedula="${value[0]}">
            <div class="form-group">
                <h3 class="texto">Seleccione los servicios beneficiario ${value[1]
        } - C.I: ${value[0]}</h3>
            </div>
            <div class="row" id="row_sanatorio_integrante${index + 1}"></div>
            <div class="row pro" id="producto_integrante${index + 1}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="" class="texto" >Servicios <span class="requerido">*</span></label>
                        <select id="servicios_int${index + 1
        }" name="" class="custom-select form-control servicio_integrante select_principal">
                            <option value="0" selected >Seleccione servicio</option>
                        </select>
                        <input type="hidden" class="importe-servicio-integrante">
                        <input type="hidden" class="importe-base-servicio-integrante"/>
                        <input type="hidden" class="importe-total-servicio-integrante"/>
                        <input type="hidden" id="nro_servicio" name="nro_servicio">
                        <input type="hidden" class="dato_extra_integrante" value="${value[11]
        }">
                        <input type="hidden" class="index_integrante" value="${index}">
                        <input type="hidden" class="importe-servicio-integrante" id="importe-omt-integrante${index + 1}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div id="divHorasServicioIntegrante" class="divHorasServicioIntegrante" style="display:none">
                        <label for="" class="texto" >Horas servicio <span class="requerido">*</span></label>
                        <select id="hrservicio_int${index + 1
        }" name="" class="custom-select form-control hservicio_int">
                            <option value="0" selected disabled>Seleccione cantidad</option>
                            <option value="8" data-base="1">8 hs</option>
                            <option value="16" data-base="1">16 hs</option>
                            <option value="24" data-base="1">24 hs</option>
                        </select>
                        <input type="hidden" class="importe-servicio-integrante"/>
                        <input type="hidden" class="importe-base-servicio-integrante"/>
                        <input type="hidden" class="importe-total-servicio-integrante"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div id="divPromoIntegrante" class="divPromoIntegrante" style="display: none;">
                            <label for="" class="texto" >Promo</label>
                            <select id="promo_int${index + 1
        }" name="promo" class="custom-select form-control promo">
                                <option value="0" selected>Seleccione promo</option>
                                <option value="20">NP17</option>
                            </select>
                            <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
                        </div>
                        <div id="divBotonIntegrante" class="divBotonIntegrante" style="display: none; ">
                            <div class="form-group">
                                <button type="button" id="btnAgregarBeneficiarioGrupo" class="btn btn-primary form-control btnbeneficiario" >Agregar beneficiarios</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SUBTOTAL -->
                <div class="col-md-4" style="margin-top: 1rem;">
                  <div class="form-group">
                    <div class="container-subtotal-afiliado container-subtotal-integrante${index + 1
        }">
                      <div class="subtotal-integrante" style="display:none;">
                        <p class="subtotal-price-integrante font-weight-bold">Total $UY: <span>0</span></p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div id="nuevos_servicios_integrante${index + 1}"></div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="button" id="btnAgregarServicioIntegrante" class="btn btn-primary form-control agregar_servicio_integrante" onclick="agregarServicioIntegrante(${index + 1
        })" >Agregar servicio</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                      <button type="button" id="btnQuitarServicioIntegrante" class="btn btn-danger form-control" onclick="quitarServicioIntegrante(${index + 1
        });">Quitar servicio</button>
                    </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" id="btnOmtInt${index + 1}">
                      <button type="button" onclick="agregarOmtIntegrante(${index + 1});" id="btnAgregarOmtGrupo${index + 1}" class="btn btn-success form-control btnAgregarOmtInt" >Agregar OMT</button>
                  </div>
                </div>
            </div>          
        <div class="row">
          <div class="col-md-4">
            <div class="divComentarioIntegrante" id="divComentarioIntengrante">
              <label for="" class="texto" >Observación <span class="requerido">*</span> </label>
              <textarea class="form-control" name="comentario" id="comentario_integrante" rows="3" placeholder=""></textarea>
            </div>
          </div>       
  
        </div>   
        <div id="divModalInt${index + 1}">

        </div>     
    </div>`);
    });
    listarServiciosGrupo();
  } else {
    $('.hservicio_int').change();
    $('.hsanatorio_int').change();
  }

  $('#pasodos-grupo').show();
  $('#pasouno-grupo').hide();
  $('#pasocuatro-grupo').hide();
  $('#validacion_medio_pago_grupo').hide();
}

function mostrarPasoTresGrupo() {
  $('#validacion_medio_pago_grupo').show();
  $('#pasodos-grupo').hide();
  //si se selcciono promo np17 solo permitir medio de pago con tarjeta
}

function mostrarValidacionMedioPagoGrupo() {
  $('#modal-venta').modal('hide');
  $('#modal-venta-grupo').modal('show');
  $('#validacion_medio_pago_grupo').show();
  $('#validacion_cedula').hide();
  $('#validacion_cedula_grupo').hide();
  $('#validacion_medio_pago').hide();
  $('#btnAtras3').show();
  $('#btnSiguiente3').show();
  $('#btnAtrasInt4').hide();
  $('#btnSiguienteInt4').hide();
  $('#pasotres').hide();
  $('#spanPrecio').html('').append('$');
}

function mostrarModalDatosTarjeta() {
  $('#modal-venta-grupo').modal('hide');
  $('#validacion_medio_pago_grupo').hide();
  $('#modal-venta').modal('show');
  $('#validacion_cedula').hide(), $('#validacion_medio_pago').hide();
  $('#btnAtras3').hide();
  $('#btnSiguiente3').hide();
  $('#btnAtrasInt4').show();
  $('#btnSiguienteInt4').show();
  $('#pasotres').show();
  eventoSiguienteAtrasBotones();

  //si se selcciono promo np17 solo permitir medio de pago con tarjeta
}

function mostrarValidacionGrupo() {
  $('#validacion_cedula_grupo').show();
  $('#pasouno-grupo').hide();
  $('#pasodos-grupo').hide();
  $('#pasocuatro-grupo').hide();
}

function mostrarPasoCuatroGrupo() {
  $('#validacion_cedula_grupo').show();
  $('#pasouno-grupo').hide();
  $('#pasodos-grupo').hide();
  $('#pasocuatro-grupo').hide();
  mostrarResumenVentas();
}

function cambiarPaginaGrupo(pagina) {
  switch (pagina) {
    case 0:
      mostrarValidacionGrupo();
      break;
    case 1:
      mostrarPasoUnoGrupo();
      break;
    case 2:
      mostrarPasoDosGrupo();
      break;
    case 3:
      mostrarPasoTresGrupo();
      break;
    case 4:
      mostrarPasoCuatroGrupo();
      break;
    default:
      break;
  }
}

function siguienteGrupo(seccion, pagina) {
  switch (seccion) {
    case 1:
      let errores = 0;
      $datosSocios = [];
      // recorre todos los formularios para validar los datos
      $('#datosintegrantes form').each(function (index) {
        const $cedula = $(this).find(`.ced_integrante`).val();
        const $nombre = $(this).find(`.nom_integrante`).val().toUpperCase();
        const $dia = $(this).find('.dian_integrante').val();
        const $mes = $(this).find('.mesn_integrante').val();
        const $anio = $(this).find('.anion_integrante').val();
        const $fechaNacimiento = `${$anio}-${$mes}-${$dia}`;
        const $calle = $(this).find('.calle_integrante').val().toUpperCase(); //dir2
        const $puerta = $(this).find('.puerta_integrante').val().toUpperCase();
        const $solar = $(this).find('.solar_integrante').val().toUpperCase();
        const $manzana = $(this).find('.manzana_integrante').val().toUpperCase();
        const $apto = $(this).find('.apto_integrante').val().toUpperCase();
        const $esquina = $(this).find('.esquina_integrante').val().toUpperCase();
        const $referencia = $(this).find('.referencia_integrante').val().toUpperCase();
        const $dep = $(this).find('.dep_integrante').val();
        const $loc = $(this).find('.loc_integrante').val();
        const $mail = $(this).find('.mail_integrante').val();
        const $cel = $(this).find('.cel_integrante').val();
        const $tel = $(this).find('.tel_integrante').val();
        const $telAlternativo = $(this).find('.tel_alt_integrante').val();
        const $datoExtra = $(this)
          .find(
            `#dato_extra_integrante${index} input[name="dato_adicional"]:checked`
          )
          .val();
        const $filial = $(this)
          .find('.loc_integrante option:selected')
          .data('filial-grupo');
        const existenNumeros = validarTelefonoPadron($cel, $tel);
        const $telefonos = `${$cel} ${$tel} ${$telAlternativo}`;
        const $nom_dep = $(this).find('#dep_integrante option:selected').text();
        const $nom_loc = $(this).find('#loc_integrante option:selected').text();
        let error = false;
        let message = '';
        const edad = calcularEdad($fechaNacimiento);

        let rejectCel = new RegExp('^09');
        let rejectTel = new RegExp('^(2|4)');

        if ($nombre == '') {
          error = true;
          message = 'Debe ingresar el nombre del beneficiario';
        } else if ($dia == '') {
          error = true;
          message = 'Debe seleccionar un día';
        } else if ($mes == '') {
          error = true;
          message = 'Debe seleccionar un mes';
        } else if ($anio == '') {
          error = true;
          message = 'Debe seleccionar un año';
        } else if ($mail != '' && !validarEmail($mail)) {
          error = true;
          message = 'El correo es inválido';
        } else if ($cel == '') {
          error = true;
          message = 'Debe ingresar un número celular';
        } else if ($cel != '' && $cel.match(rejectCel) == null) {
          error = true;
          message = 'Debe ingresar un número de celular válido';
        } else if ($tel != '' && $tel.match(rejectTel) == null) {
          error = true;
          message = 'Debe ingresar un telefono fijo válido';
        } else if (existenNumeros[0]) {
          error = true;
          message = existenNumeros[1];
        } else if (!validateDate($fechaNacimiento)) {
          error = true;
          message = 'La fecha ingresada no es valida';
        } else if ($calle == '') {//seba
          error = true;
          message = 'Debe ingresar la calle';
        } else if ($esquina == '') {//seba
          error = true;
          message = 'Debe ingresar la esquina';
        } else if ($referencia == '') {//seba
          error = true;
          message = 'Debe ingresar una refencia';
        } else if (!$(`#datos_integrante${index} .puertaChecked`).is(':checked') && !$(`#datos_integrante${index} .solarChecked`).is(':checked')) {//seba
          error = true;
          message = 'Debe seleccionar para ingresar nro de puerta o solar/manzana';
        } else if ($(`#datos_integrante${index} .puertaChecked`).is(':checked') && ($puerta == '' || $puerta == 0)) { //seba //dir2
          error = true;
          message = 'Debe ingresar el número de puerta(No puede ser 0)';
        } else if ($(`#datos_integrante${index} .solarChecked`).is(':checked') && ($manzana == '' || $solar == '' || $manzana == 0 || $solar == 0)) {//seba
          error = true;
          message = 'Debe ingresar manzana y solar(No puede ser 0)';
        } else if (edad < 18) {
          error = true;
          message = 'No se permite afiliar a menores de edad';
        } else if ($dep == '') {
          error = true;
          message = 'Debe seleccionar un departamento';
        } else if ($loc == '') {
          error = true;
          message = 'Debe seleccionar una localidad';
        }

        if ($calle.substr(4).match(/\d+/) != null) {//seba
          alert('Por favor no ingrese el número de puerta en "Calle", ingréselo en el campo correspondiente');
        }

        let $direccion = '';
        if (!error) {
          if ($(this).find('.puertaChecked').is(':checked')) {
            $direccion = ($apto != '') ? $calle.substr(0, 14) + ' ' + $puerta + '/' + $apto + ' E:' : $calle.substr(0, 17) + ' ' + $puerta + ' E:';
            $direccion += $esquina.substr(0, (36 - $direccion.length)); //di
          } else {
            $direccion = ($apto != '') ? $calle.substr(0, 14) + ' M:' + $manzana + ' S:' + $solar + '/' + $apto : $calle.substr(0, 14) + ' M:' + $manzana + ' S:' + $solar + ' E:';
            $direccion += ($apto == "") ? $esquina.substr(0, (36 - $direccion.length)) : ''; //di
          }

          $datosSocios.push([
            $cedula,
            $nombre,
            $fechaNacimiento,
            $direccion,
            $dep,
            $loc,
            $filial,
            $mail,
            $cel,
            $tel,
            $telAlternativo,
            $datoExtra,
            $nom_dep,
            $calle, //dir2
            $puerta,
            $apto,
            $manzana,
            $solar,
            $esquina,
            $referencia
          ]);
        } else {
          errores++;
          $('#modal-venta-grupo .modal-content').scrollTop(0);
          $(`#error-datos-grupo${index} .error-datos`).text(message);
          $(`#error-datos-grupo${index}`).show().fadeOut(10000);
        }
      });

      if (errores === 0) {
        cambiarPaginaGrupo(pagina);
      }
      break;
    case 2:
      let error = false;
      $('.error-servicios-integrantes').html('');
      $('.servicio_integrantes').each(function () {
        let mensajesDeError = [];

        // Validacion de los servicios
        $(this)
          .find('.servicio_integrante')
          .each(function () {
            if ($(this).val() == '') {
              mensajesDeError.push('Debe seleccionar el servicio');
            }
          });

        // Validacion de las horas servicio
        $(this)
          .find('.hservicio_int')
          .each(function () {
            if ($(this).parent().css('display') !== 'none') {
              if ($(this).val() == null) {
                mensajesDeError.push('Debe indicar las horas del servicio');
              }
            }
          });

        // Validacion horas sanatorio
        $(this)
          .find('.hsanatorio_int')
          .each(function () {
            if ($(this).parent().is(':visible') && $(this).val() == null) {
              mensajesDeError.push('Debe indicar las horas del sanatorio');
            }
          });

        // Validacion de la observacion
        if ($(this).find('.divComentarioIntegrante textarea').val() == '') {
          mensajesDeError.push('Debe indicar una observación');
        }

        if (mensajesDeError.length) {
          error = true;
          $('.error-servicios-integrantes').append(
            `<h3>C.I: ${$(this).data('cedula')}</h3>`
          );
          mensajesDeError.forEach(function (error) {
            $('.error-servicios-integrantes').append(`<p>${error}</p>`);
          });
          $('#error-servicios-integrantes').show().fadeOut(10000);
          $('#modal-venta-grupo .modal-content').scrollTop(0);
        }
      });

      if (!error) {
        let serviciosDeAfiliados = [];
        $('.servicio_integrantes').each(function (index) {
          // [idServicio, horasServicio|horasSanatorio, promo]
          let servicios = [];
          let observacion = $(this)
            .find('.divComentarioIntegrante textarea')
            .val();
          // COMPRUEBO SI TIENE SANATORIO
          let horaSanatorio = $(this)
            .find(`#row_sanatorio_integrante${index + 1} .hsanatorio_int`)
            .val();
          // promoSanatorio_int1
          let promoSanatorio = $(this)
            .find(`#row_sanatorio_integrante${index + 1} .promo_integrante`)
            .val();
          let costoServicio = null;
          let costoBaseServicio = null;
          let costoTotalServicio = null;
          let omtAgregado = false;


          if (
            $(this)
              .find(`#divHorasSanatorioIntegrante${index + 1}`)
              .is(':visible')
          ) {
            costoTotalServicio = Number(
              $(this)
                .find(
                  `#row_sanatorio_integrante${index + 1
                  } .importe-servicio-integrante`
                )
                .val()
            );
            costoBaseServicio = Number(
              $(this)
                .find(
                  `#row_sanatorio_integrante${index + 1
                  } .importe-base-servicio-integrante`
                )
                .val()
            );
            costoServicio = Number(
              $(this)
                .find(
                  `#row_sanatorio_integrante${index + 1
                  } .importe-total-servicio-integrante`
                )
                .val()
            );
            servicios.push([
              1,
              horaSanatorio,
              'Sanatorio',
              costoTotalServicio,
              costoServicio,
              costoBaseServicio,
              promoSanatorio,
              observacion,
              costoTotalServicio,
              costoServicio,
              costoBaseServicio,//dir2
            ]);
          }

          // PRODUCTO POR DEFECTO
          // servicio = $(this).find(`#producto_integrante${index+1} .servicio_integrante`).val();
          servicio = $(this).find(`.servicio_integrante`).val();
          nombreServicio = $(this)
            .find(
              `#producto_integrante${index + 1
              } .servicio_integrante option:selected`
            )
            .text();
          costoTotalServicio = Number(
            $(this)
              .find(
                `#producto_integrante${index + 1
                } .divHorasServicioIntegrante .importe-servicio-integrante`
              )
              .last()
              .val()
          );
          costoBaseServicio = Number(
            $(this)
              .find(
                `#producto_integrante${index + 1
                } .divHorasServicioIntegrante .importe-base-servicio-integrante`
              )
              .last()
              .val()
          );
          costoServicio = Number(
            $(this)
              .find(
                `#producto_integrante${index + 1
                } .divHorasServicioIntegrante .importe-total-servicio-integrante`
              )
              .last()
              .val()
          );
          horasServicio = $(this)
            .find(`#producto_integrante${index + 1} .hservicio_int`)
            .val();

          if (
            horasServicio == '0' ||
            horasServicio == null ||
            horasServicio == undefined
          ) {
            horasServicio = '8';
            costoBaseServicio = Number(
              $(this).find('.servicio_integrante ').next().val()
            );
            costoServicio = Number($(this).next().next().val());
            costoTotalServicio = Number(
              $(this).next().next().next().next().val()
            );
          }

          promoSanatorio =
            servicio == '1'
              ? $(this)
                .find(`#producto_integrante${index + 1} .promo`)
                .val()
              : 0;
          servicios.push([
            servicio,
            horasServicio,
            nombreServicio,
            costoTotalServicio,
            costoServicio,
            costoBaseServicio,
            promoSanatorio,
            observacion,
            costoTotalServicio,
            costoServicio,
            costoBaseServicio, //dir2
          ]);

          // SERVICIOS ADICIONALES
          $(this)
            .find(
              `#nuevos_servicios_integrante${index + 1} .hrservicios_int${index + 1
              }`
            )
            .each(function (indice, element) {
              servicio = $(this).find(`.servicio_integrante`).val();
              let nombreServicio = $(this)
                .find(`.servicio_integrante option:selected`)
                .text();
              let horasServicio = $(this).find(`.hservicio_int`).val();
              costoTotalServicio = Number(
                $(this)
                  .find(
                    `.divHorasServicioIntegrante .importe-servicio-integrante`
                  )
                  .last()
                  .val()
              );
              costoServicio = Number(
                $(this)
                  .find(
                    `.divHorasServicioIntegrante .importe-total-servicio-integrante`
                  )
                  .last()
                  .val()
              );
              costoBaseServicio = Number(
                $(this)
                  .find(
                    `.divHorasServicioIntegrante .importe-base-servicio-integrante`
                  )
                  .last()
                  .val()
              );

              // if (horasServicio == '0' || horasServicio == null) {
              //   horasServicio = 8;
              //   costoTotalServicio = Number($(this).find(`.importe-servicio-int`).val());
              //   costoServicio = Number($(this).find(`.importe-total-servicio-int`).val());
              //   costoBaseServicio = Number($(this).find(`.importe-base-servicio-int`).val());
              // }
              horasServicio = (horasServicio == "0" || horasServicio == null) ? '8' : horasServicio;
              // const observacion = $(this).find(".divComentarioIntegrante textarea").val();
              promoSanatorio =
                servicio == '1'
                  ? $(this)
                    .find(`#producto_integrante${index + 1} .promo`)
                    .val()
                  : 0;

              servicios.push([
                servicio,
                horasServicio,
                nombreServicio,
                costoTotalServicio,
                costoServicio,
                costoBaseServicio,
                promoSanatorio,
                observacion,
                costoTotalServicio,
                costoServicio,
                costoBaseServicio //dir2
              ]);
            });

          serviciosDeAfiliados.push(servicios);

          // total servcios por integrante
          const totalImporte = Number(
            $(this).find('.subtotal-price-integrante span').text()
          );
          // total general
          totalImporteGrupo += totalImporte;
          $datosSocios[index][20] = totalImporte; //dir2

          if (Array.isArray($arrBenOmtGrupo[index]) && !omtAgregado) { //omtgrupo
            let totalImporteGrupoOmt = totalImporte - (Number($arrBenOmtGrupo[index][15]));
            $datosSocios[index].push(totalImporteGrupoOmt);
            servicios.push([106, 8, 'Beneficiario OMT', $arrBenOmtGrupo[index][15], $arrBenOmtGrupo[index][15], $arrBenOmtGrupo[index][15], promoSanatorio, observacion, $arrBenOmtGrupo[index][15], $arrBenOmtGrupo[index][15], 0]); //dir2

          }
        });

        SOCIOS[0] = zip($datosSocios, serviciosDeAfiliados);
        totalImporteGrupo = Number($('#subtotal-price-grupo').text());
        cambiarPaginaGrupo(pagina);
      }
      break;
    case 3:
      let numTarjeta = $('#numTar').val();
      let cedulaTit = $('#cedTit').val();
      let nombreTit = $('#nomTit').val().toUpperCase();
      let cvv = $('#cvv').val();
      let mesVencimiento = $('#mesVen').val();
      let anoVencimiento = $('#anoVen').val();
      let mailTit = $('#mailTit').val();
      let celularTit = $('#celTit').val();
      let telefonoTit = $('#telTit').val();
      let bancoEmisor = $('#bancos').val();
      let tarjeta = $('#payment_method_id').val();
      const currentYear = new Date().getFullYear();
      const currentMonth = new Date().getMonth();

      if (!/^\d{16}$/.test(numTarjeta)) {
        $('.error-pago')
          .text('')
          .append('Debe ingresar un número de tarjeta valido');
        $('#error-pago').show().fadeOut(10000);
      } else if (cedulaTit == '') {
        $('.error-pago').text('').append('Debe ingresar la cedula del titular');
        $('#error-pago').show().fadeOut(10000);
      } else if (!comprobarCI(cedulaTit)) {
        $('.error-pago')
          .text('')
          .append('La cédula del titular ingresada no es válida');
        $('#error-pago').show().fadeOut(10000);
      } else if (nombreTit == '') {
        $('.error-pago')
          .text('')
          .append('Debe ingresar el nombre del titular d ela tarjeta');
        $('#error-pago').show().fadeOut(10000);
      } else if (mesVencimiento == '') {
        $('.error-pago')
          .text('')
          .append('Debe seleccionar el mes de vencimiento');
        $('#error-pago').show().fadeOut(10000);
      } else if (anoVencimiento == '') {
        $('.error-pago')
          .text('')
          .append('Debe seleccionar el año de vencimiento');
        $('#error-pago').show().fadeOut(10000);
      } else if (
        Number(anoVencimiento) < currentYear ||
        (Number(anoVencimiento) == currentYear &&
          Number(mesVencimiento) < currentMonth)
      ) {
        $('.error-pago').text('').append('La tarjeta se encuentra vencida');
        $('#error-pago').show().fadeOut(10000);
      } else if (mailTit != '' && !validarEmail(mailTit)) {
        $('.error-pago').text('').append('El correo es inválido');
        $('#error-pago').show().fadeOut(10000);
      } else if (celularTit == '') {
        $('.error-pago').text('').append('Debe ingresar un número celular');
        $('#error-pago').show().fadeOut(10000);
      } else if (tarjeta == 'debvisa') {
        $('.error-pago')
          .text('')
          .append('Las tarjetas debito VISA no son permitidas');
        $('#error-pago').show().fadeOut(10000);
      } else if (localStorage.mercadopago == '0' && bancoEmisor == '') {
        $('.error-pago').text('').append('Debe seleccionar un banco emisor');
        $('#error-pago').show().fadeOut(10000);
      } else if (localStorage.tipo_tarjeta == 'CABAL' && bancoEmisor == '12') {
        $('.error-pago')
          .text('')
          .append('No se aceptan tarjetas CABAL del BBVA');
        $('#error-pago').show().fadeOut(10000);
      } else if (!localStorage.mercadopago && !validarTarjeta()) {
        $('.error-pago').text('').append('La tarjeta no es válida');
        $('#error-pago').show().fadeOut(10000);
      } else {
        arrDatosTarjeta[0] = numTarjeta;
        arrDatosTarjeta[1] = cedulaTit;
        arrDatosTarjeta[2] = nombreTit;
        arrDatosTarjeta[3] = cvv;
        arrDatosTarjeta[4] = mesVencimiento;
        arrDatosTarjeta[5] = anoVencimiento;
        arrDatosTarjeta[6] = mailTit;
        arrDatosTarjeta[7] = celularTit;
        arrDatosTarjeta[8] = telefonoTit;
        arrDatosTarjeta[9] = bancoEmisor;
        arrDatosTarjeta[10] = tarjeta;
        arrDatosTarjeta[11] = totalImporteGrupo;

        SOCIOS.push(arrDatosTarjeta);
        cambiarPaginaGrupo(pagina);
      }

      break;
    default:
      break;
  }
}

function mostrarResumenVentas() {
  let template = '';
  let cant_socios = $datosSocios.length;
  let porcentajeDto = 0;
  let totalConDescuento = 0;
  let totalMostrar = 0;


  if (cant_socios == 2) {
    porcentajeDto = 0.1;
  } else if (cant_socios == 3) {
    porcentajeDto = 0.15;
  } else if (cant_socios >= 4) {
    porcentajeDto = 0.2;
  }
  SOCIOS[0].forEach(function (socio, indice) {
    if (socio.length == 2) {
      const datos = socio[0];
      const cedula = datos[0];
      const nombre = datos[1];
      const fechaNacimiento = datos[2];
      const direccion = datos[3];
      const departamento = datos[12];
      const localidad = datos[13];
      const mail = datos[7];
      const celular = datos[8];
      const telefono = datos[9];
      const telefonoAlternativo = datos[10];
      const nom_dep = datos[12];
      const nom_loc = datos[13];
      const servicios = socio[1];

      // DATOS PERSONALES
      template += `
            <hr>
              <h4>Datos personales</h4>
              <p><span class='text-muted'>Cédula: </span>${cedula}</p> 
              <p><span class='text-muted'>Nombre: </span>${nombre} </p> 
              <p><span class='text-muted'>Fecha de nacimiento: </span>${fechaNacimiento}</p> 
              <p><span class='text-muted'>Dirección: </span>${direccion}</p> 
              <p><span class='text-muted'>Departamento: </span>${departamento}</p> 
              <p><span class='text-muted'>Localidad: </span>${localidad}</p> 
              <p><span class='text-muted'>Mail: </span>${mail} </p> 
              <p><span class='text-muted'>Celular: </span>${celular} </p> 
              <p><span class='text-muted'>Télefono: </span>${telefono} </p> 
              <p><span class='text-muted'>Télefono alternativo: </span>${telefonoAlternativo} </p> 
            <hr> 
            <h4>Servicios</h4>
        
          `;

      SOCIOS[0][indice][0][20] = 0; //dir2
      SOCIOS[0][indice][0][21] = 0;
      // SERVICIOS
      let dtoOmt = false; //omtgrupo
      servicios.forEach(function (servicio, index) {
        let s = SOCIOS[0][indice][1][index][0];
        const cantidadDeHoras = servicio[1];
        const nombreServicio = servicio[2];
        let costoServicio = servicio[8];
        let costoConDto = costoServicio;

        if (s == '1') {
          costoConDto = Math.ceil(
            costoServicio - costoServicio * porcentajeDto
          );
          SOCIOS[0][indice][1][index][3] = Math.ceil(
            Number(SOCIOS[0][indice][1][index][8]) -
            Number(SOCIOS[0][indice][1][index][8]) * porcentajeDto
          );
          SOCIOS[0][indice][1][index][4] = Math.ceil(
            Number(SOCIOS[0][indice][1][index][9]) -
            Number(SOCIOS[0][indice][1][index][9]) * porcentajeDto
          );
          SOCIOS[0][indice][1][index][5] = Math.ceil(
            Number(SOCIOS[0][indice][1][index][10]) -
            Number(SOCIOS[0][indice][1][index][10]) * porcentajeDto
          );
          template += `<p><span class='text-muted'>${nombreServicio} (${cantidadDeHoras} hrs): </span>  $U ${costoServicio}</p>
            <p><span class='text-primary'>${nombreServicio} (${cantidadDeHoras} hrs) con descuento (${porcentajeDto * 100
            }%) : </span>  $U ${costoConDto}</p>`;
        } else {
          template += `<p><span class='text-primary'>${nombreServicio} (${cantidadDeHoras} hrs): </span>  $U ${costoServicio}</p>`;
        }


        totalMostrar += costoConDto;
        //omitimos el valor dle omt para el total
        costoConDto = (s == 106) ? 0 : costoConDto;
        totalConDescuento += Number(costoConDto);
        console.log(totalConDescuento);
        SOCIOS[0][indice][0][20] += Number(costoConDto); //dir2

        if (Array.isArray($arrBenOmtGrupo[indice]) && !dtoOmt) { //omtgrupo
          SOCIOS[0][indice][0][21] += Number(costoConDto) - Number($arrBenOmtGrupo[indice][15]); //dir2

          dtoOmt = true;
        }
      });
      // template += `<hr>`;
    }
  });

  // DATOS TARJETA
  let banco_emisor = $('#bancos option:selected').text();
  totalImporteGrupo = totalConDescuento;

  template += `
                    <hr>
                    <h4>Datos de la tarjeta</h4>
                    <p><span class='text-muted'>Numero tarjeta: </span> ${SOCIOS[1][0]
    }</p>
                    <p><span class='text-muted'>Tipo de tarjeta: </span> ${SOCIOS[1][10]
    }</p>
                    <p><span class='text-muted'>Vencimiento: </span>  ${SOCIOS[1][4]
    }/${SOCIOS[1][5]}</p>
                    <p><span class='text-muted'>Banco emisor: </span> ${banco_emisor}</p>
                    <p><span class='text-muted'>Cédula del titular: </span> ${SOCIOS[1][1]
    }</p>
                    <p><span class='text-muted'>Nombre del titular: </span> ${SOCIOS[1][2]
    }</p> 
                    <p><span class='text-muted'>Teléfono del titular: </span> ${SOCIOS[1][8]
    }</p> 
                    <p><span class='text-muted'>Mail del titular: </span> ${SOCIOS[1][6] != '' ? SOCIOS[1][6] : '-'
    }</p> 
                    <hr>
                    <p class='text-uppercase font-weight-bold' style='font-size: 2.3rem;'>TOTAL: $UY ${totalMostrar}</p>
                    `;//dir2 total mostarr

  $('#modal-confirmacion-venta-grupo .modal-body').html(template);
  $('#modal-confirmacion-venta-grupo').modal('show');
}

function limpiarElementosHTML() {
  $('.subtotal').hide();
  $('#subtotal-price-grupo').text('');
  $('#servicios_integrantes').html(`
    <div class="alert alert-danger alert-dismissable" id="error-servicios-integrantes" style="display: none; text-align: left; height: auto;">
      <button type="button"  class="close" data-dismiss="alert">&times;</button>
      <h4><strong>Errores: </strong></h4>
      <div class="error-servicios-integrantes"></div>
    </div>
  `);
}

function confirmarVentaGrupo(e) {
  e.preventDefault();
  SOCIOS.push(localStorage.llamadaEntrante);
  let idasesor = $('#buscarAsesor').val();
  const data = {
    socios: JSON.stringify(SOCIOS),
    llamadaEntrante: localStorage.llamadaEntrante,
    idAsesor: idasesor,
    omt: $omtGrupo,
    beneficiariosOmt: $arrBenOmtGrupo
  };

  $.ajax({
    url: 'Ajax/procesoVentaGrupo.php',
    data: data,
    dataType: 'json',
    type: 'POST',
    success: function (response) {
      if (response.result) {
        alert(response.message);
        limpiarInputsDatosTarjeta();
        limpiarDatosIntegrantes();
        reiniciarVariablesGlobales();
        limpiarElementosHTML();
        $('#modal-venta').modal('hide');
        $('#pasotres').hide();
        $('#divAtendio').hide();
        $('#modal-venta-grupo').modal('hide');
        if (localStorage.llamadaEntrante == false) {
          vendido();
        }
      }
    },
    error: function (error) {
      // console.log(error);
    }
  });
}

// Contabiliza el subtotal de cada uno de los afiliados
// CONTABILIZA EL SUBTOTAL
function calcularSubTotalGrupo($parent) {
  console.log($parent);
  let costo = 0;
  $parent.find(`.importe-servicio-integrante`).each(function (i, v) {
    if ($(this).val() !== NaN) {
      costo += Number($(this).val());
    }
  });
  return costo;
}

// CONTABILIZA EL SUBTOTAL
function calcularSubTotalGeneral() {
  let costo = 0;
  $(`.importe-servicio-integrante`).each(function (i, v) {
    if ($(this).val() !== NaN) {
      costo += Number($(this).val());
    }
  });
  $('#subtotal-price-grupo').text(costo);
  $('.subtotal').show();
}

function mostrarSubTotalGrupo(costo, $parent) {
  console.log(costo);
  $parent.find('.subtotal-price-integrante span').html(`${costo}`);
  $parent.find('.subtotal-integrante').show();
}

function limpiarSubTotalGrupo() {
  $('.subtotal-price-integrante span').html('');
  $('.subtotal-integrante').hide();
  $('.importe-servicio-integrante').each(function (i, v) {
    if (this.previousElementSibling.id != 'hrsanatorio') {
      $(this).val('');
    }
  });
}

function contabilizarSubTotalGrupo(e) {
  // subtotal-price-integrante
  let $parent = $(this).parent().parent().parent().parent().parent();

  if (!$parent.hasClass('servicio_integrantes')) {
    $parent = $parent.parent();
  }

  const indexIntegrantes = Number($parent.find('.index_integrante').val());
  const idProducto = e.target.classList.contains('hservicio_int')
    ? $(this).parent().parent().parent().prev().first().find('select').val()
    : 1;
  const $fechaNacimiento = $datosSocios[indexIntegrantes][2];
  const $localidad = $datosSocios[indexIntegrantes][6];
  let horasServicio = $(this).val();
  let horasSanatorio = null;
  let base = false;
  let costo = 0;
  let total = 0;
  let horasActuales = null;
  let importeActual = 0;

  let precioServicio = null;
  let precioBase = null;

  if (idProducto == 1) {
    horasSanatorio = horasServicio;
    horasServicio = null;
    base = true;
  } else if (idProducto == '2') {
    base = true;
  }

  if (horasActuales) {
    if (idProducto == 1) {
      if (horasSanatorio != horasActuales) {
        horasSanatorio = horasSanatorio - horasActuales;
        total = calcularPrecio(
          idProducto,
          horasSanatorio,
          horasServicio,
          $localidad,
          $fechaNacimiento,
          base
        );
        precioBase = total[0];
        precioServicio = total[1];
        total = Number(importeActual) + Number(total[2]);
      } else {
        total = Number(importeActual);
      }
    } else if (horasServicio != horasActuales) {
      horasServicio = horasServicio - horasActuales;
      total = calcularPrecio(
        idProducto,
        horasSanatorio,
        horasServicio,
        $localidad,
        $fechaNacimiento,
        true
      );
      precioBase = total[0];
      precioServicio = total[1];
      total = Number(importeActual) + Number(total[2]);
    } else {
      total = Number(importeActual);
    }
  } else {
    total = calcularPrecio(
      idProducto,
      horasSanatorio,
      horasServicio,
      $localidad,
      $fechaNacimiento,
      base
    );
    precioBase = total[0];
    precioServicio = total[1];
    total = total[2];
  }

  // #################################################################################
  if (idProducto != 0) {
    if (
      e.target.nextElementSibling &&
      e.target.nextElementSibling.tagName == 'INPUT'
    ) {
      e.target.nextElementSibling.value = Number(total);
      e.target.nextElementSibling.nextElementSibling.value = Number(precioBase);
      e.target.nextElementSibling.nextElementSibling.nextElementSibling.value = Number(
        precioServicio
      );
    } else {
      e.target.parentNode.parentNode.parentNode.previousElementSibling.querySelector(
        '.importe-servicio-integrante'
      ).value = Number(total);
      e.target.parentNode.parentNode.parentNode.previousElementSibling.querySelector(
        '.importe-base-servicio-integrante'
      ).value = Number(precioBase);
      e.target.parentNode.parentNode.parentNode.previousElementSibling.querySelector(
        '.importe-total-servicio-integrante'
      ).value = Number(precioServicio);
    }
    mostrarSubTotalGrupo(calcularSubTotalGrupo($parent), $parent);
    calcularSubTotalGeneral();
  }
}

function limpiarDatosIntegrantes() {
  $('.cedula_adi').remove();
  $('#datosintegrantes').html('');
  $('#servicios_integrantes').html('');
  $('.convenios option:eq(0)').prop('selected', true);
  $('.valced_integrante').val('');
  $('#tipo-afiliaciones option:eq(0)').prop('selected', true);
}

function listarConveniosGrupo(id_metodo) {
  $("#conveniosGrupo").html("");
  let opciones = `<option value="" selected>- Seleccione -</option>`;

  if (id_metodo == 2) {
    opciones += `<option value="20" data-idmetodo = "2">TARJETA DE CREDITO</option>`;
  } else if (id_metodo == 3) {
    opciones += `<option value="27" data-idmetodo = "3">CACCEPOL</option>`;
  }
  $("#conveniosGrupo").append(opciones).change();
}