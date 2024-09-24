$cedulaAfiliado = "";

const sistema_rutas = false;

$("document").ready(function () {
  verifySession();

  const verificarDatosSocioUrl = getUrl("verificar_datos_socio");

  if (verificarDatosSocioUrl !== "") {
    verDatosSocio(Number.parseInt(verificarDatosSocioUrl), false);
  }

  $("#btn_registrar_vidaapp").click(function (e) {
    e.preventDefault();
    registrarVidaApp();
  });

  $("#llenar_contactos_vidaapp").change(function () {
    if (this.checked) {
      $("#div_telefonos_emergencia").css("display", "block");
    } else {
      $("#div_telefonos_emergencia").css("display", "none");
      limpiarContactosDeEmergencia();
    }
  });

  if (localStorage.idUser == "27") {
    $("#op_control_abm").show();
    $("#op_control_abm_piscina").show(); //cpiscina
  }

  //div error productos
  $("#error-productos").hide();
  $("#error-cobro").hide();

  $("#desde").val("");
  $("#hasta").val("");

  //evento boton actualizar productos
  $("#btnActualizarProductos").click(function () {
    ActualizarProductos();
  });

  $("body").on("keypress", "input", function (args) {
    if (args.keyCode == 13) {
      $("#btnEntrar").click();
      return false;
    }
  });

  $("#btnServiciosContratados").hide();
  $("#btnConfirmarCobro").click(confirmarCobro);

  $("#btnActualizarBeneficiarios").click(guardarBeneficiarios);
  $("#error-beneficiarios").hide();

  $("#btnEditarDatos").click(function () {
    if ($("#btnActualizarDatosSocio").is(":visible")) {
      $("#btnActualizarDatosSocio").hide();
      $(".abm_input").prop("readonly", true);
    } else {
      $(".abm_input").removeAttr("readonly");
      $("#btnActualizarDatosSocio").show();
    }
  });

  $("#btnEditarDatosPiscina").click(function () {
    //cpiscina
    if ($("#btnActualizarDatosSocioPiscina").is(":visible")) {
      $("#btnActualizarDatosSocioPiscina").hide();
      $("#btnLimpiarDatosAbmPiscina").hide();
      $(".abmp_input").prop("readonly", true);
      $("#abmp_estado").prop("disabled", true);
      $("#abmp_origenventa").prop("disabled", true);
    } else {
      $("#abmp_estado").prop("disabled", false);
      $("#abmp_origenventa").prop("disabled", false);
      $(".abmp_input").removeAttr("readonly");
      $("#btnActualizarDatosSocioPiscina").show();
      $("#btnLimpiarDatosAbmPiscina").show();
    }
  });

  $("#buscarInfoSocio").click(function () {
    let cedula = $("#cedula_socio").val();
    buscarInfoSocio();
  });

  $("#buscarInfoPiscina").click(buscarInfoSocioPiscina); //cpiscina

  $("#btnActualizarDatosSocio").click(function () {
    swal({
      title: "Estás seguro de realizar esta acción?",
      text: "Se modificarán estos datos en padrón",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        actualizarDatosSocio();
      } else {
        swal("Acción cancelada");
      }
    });
  });

  $("#btnActualizarDatosSocioPiscina").click(function () {
    //cpiscina
    swal({
      title: "Estás seguro de realizar esta acción?",
      text: "Se modificarán estos datos en piscina",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willUpdate) => {
      if (willUpdate) {
        actualizarDatosSocioPiscina();
      } else {
        swal("Acción cancelada");
      }
    });
  });

  $("#btnLimpiarDatosAbm").click(limpiarDatosAbm);

  $("#btnLimpiarDatosAbmPiscina").click(limpiarDatosAbmPiscina); //cpiscina

  $("#btn_editar_servicios").click(function () {
    swal({
      title: "Estás seguro de realizar esta acción?",
      text: "Se modificarán estos datos en padrón",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        actualizarServicioSocio();
      } else {
        swal("Acción cancelada");
      }
    });
  });

  $("#btnVerHistoricoPiscina").click(function () {
    let idSocioPiscina = $("#abmp_idsocio").val();
    verHistorial(idSocioPiscina);
  });

  $("#btn_editar_servicios_piscina").click(function () {
    //cpiscina
    swal({
      title: "Estás seguro de realizar esta acción?",
      text: "Se modificarán estos datos en piscina",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        actualizarServicioSocioPiscina();
      } else {
        swal("Acción cancelada");
      }
    });
  });

  $("#exportarPadronDatos").click(function () {
    exportarDatos(1);
  });

  $("#exportarPadronProducto").click(function () {
    exportarDatos(2);
  });

  $("#verMas").click(function () {
    if ($("#divEliminar").is(":visible")) {
      $("#divEliminar").hide();
      $("#verMas").text("Ver más");
    } else {
      swal("Introduce código de seguridad", {
        content: "input",
      }).then((value) => {
        if (value == 767) {
          $("#verMas").text("Ocultar");
          $("#divEliminar").show();
        } else {
          swal({
            title: "Error",
            text: "Código incorrecto",
            icon: "error",
          });
        }
      });
    }
  });

  $("#btnEliminarSocioPadron").click(eliminarSocioPadron);

  //evento change de los select de productos
  $(document.body).on("change", ".hservicio", function () {
    let padre = $(this).parent().parent().parent().parent().attr("id");
    let servi = $(this).val();
    let horasSanatorio = null;
    let idProducto = $(`#${padre} #id_servicio`).val();
    let loc = localStorage.localidad;
    let fecha_nacimiento = localStorage.fecha_nacimiento;
    let horasServicio = Number($(this).val());
    let base = false;
    let costo = 0;
    let alta = $(`#${padre} .alta`).val();

    if (idProducto == "1") {
      horasSanatorio = horasServicio;
      horasServicio = null;
      base = alta == "0" ? false : true;
    }

    let total = calcularPrecio(
      idProducto,
      horasSanatorio,
      horasServicio,
      loc,
      fecha_nacimiento,
      base
    );

    $(`#${padre} #importe`).val(total[2]);

    $(".importe").each(function (i, val) {
      costo += +$(this).val();
    });

    // $('#total').text(costo);
  });

  if (localStorage.typeAdmin == "comercial") {
    $("#thhead-ruta").show();
    $("#thhead-ruta").text("Seleccionar ruta");
    $("#thead-servicios").text("Servicios contratados");
  } else {
    $("#thhead-ruta").text("-");
    $("#thead-servicios").text("Acción");
  }

  if (localStorage.username == "bienvenida") {
    $("#mis-rechazos").show();
  }

  $("#btnMisRechazos").click(function (e) {
    const $select = $(`#select-filter-4`);

    if ($select.val() == "Rechazo por bienvenida") {
      $select.find(`option:eq(0)`).prop("selected", true);
      $(this)
        .removeClass("btn-success")
        .addClass("btn-primary")
        .text("VER MIS RECHAZOS");
    } else {
      $(this)
        .removeClass("btn-primary")
        .addClass("btn-success")
        .text("VER MIS PENDIENTES");
      $select
        .find(`option[value="Rechazo por bienvenida"]`)
        .prop("selected", "selected");
    }
    $select.change();
  });

  // Datos del usuario
  const username = localStorage.username
    .split(" ")
    .map(function (x) {
      return `${x[0].toUpperCase()}${x.slice(1)}`;
    })
    .join(" ")
    .trim();
  $(".nav-username").html(username);

  $("#btnActulizarRuta").click(cambiarRuta);

  //dashboard
  dashboard();

  $("#btn-clear").click(function (e) {
    e.preventDefault();
    if ($("select option:selected[value!='']").length)
      $("select").val("").change();
    $(".fechas").val("");
    dashboard();
  });

  //evento de boton dashboard
  $("#btnDashBoard").click(function (e) {
    e.preventDefault();
    dashboard();
  });
  $("#btnHistory").click(function (e) {
    e.preventDefault();
    history();
  });

  $("#btn-filtrar").click(function () {
    dashboard();
  });

  //evento boton afiliaciones aprobadas
  $("#btnAfiliaciones").click(function (e) {
    e.preventDefault();
    verAfiliaciones();
  });

  $("#btnControlAbm").click(function (e) {
    e.preventDefault();
    verPanelControl();
  });

  $("#btnControlAbmPiscina").click(function (e) {
    e.preventDefault();
    verPanelControlPiscina();
  });

  $("#btnCloseSession").click(function (e) {
    e.preventDefault();
    closeSession();
  });

  $("#btnConfirmarRechazo").click(rechazo);
});

function getUrl(sParam) {
  let sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split("&"),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split("=");

    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined
        ? true
        : decodeURIComponent(sParameterName[1]);
    }
  }
  return false;
}

function eventMercadoPago() {
  $("#numero_tarjeta").change(guessPaymentMethod);
  $("#numero_tarjeta").keyup(guessPaymentMethod);
}

function comprobarMetodoPagoRC() {
  //newrc
  $tipo = $("#rcTipo").val();

  if ($tipo === null || $tipo == "") {
    swal("Error", "Debe seleccionar una red de cobranza", "error");
  } else {
    localStorage.removeItem("tokenValidador");
    localStorage.removeItem("paymentMethodId");
    $("#tokenValidador").val("no_usa");
    $("#paymentMethodId").val($tipo);
    tokenCorrectoRC();
  }
}

function tokenCorrectoRC() {
  //newrc

  $token = $("#tokenValidador").val();
  $paymentMethodId = $("#rcTipo").val();
  $mail_tit = $("#rcMail").val();
  $ced_tit = $("#rcCedula").val();
  $ced_tit = $("#rcCedula").val();
  $idSocio = $("#rcIdSocio").val();
  $cuotas = null;

  if (
    $token != "" &&
    $token != null &&
    $paymentMethodId != "" &&
    $paymentMethodId != null
  ) {
    localStorage.setItem("tokenValidador", $token);
    localStorage.setItem("paymentMethodId", $paymentMethodId);
    localStorage.setItem("mail_tit", $mail_tit);
    localStorage.setItem("ced_tit", $ced_tit);
    localStorage.setItem("cuotas", $cuotas);
    localStorage.setItem("idSocio", $idSocio);
    enviarCobroRC();
    console.log("Correcto token de mercadopago");
  } else {
    console.log("Error en el token de mercadopago");
  }
}

function enviarCobroRC() {
  //newrc
  $id_usuario = localStorage.getItem("id_usuario");
  $token_mercadopago = localStorage.getItem("tokenValidador");
  $metodo_mercadopago = localStorage.getItem("paymentMethodId");
  $idAfiliado = localStorage.getItem("paymentMethodId");

  if ($token_mercadopago !== null && $metodo_mercadopago !== null) {
    $mail_tit = localStorage.mail_tit;
    $ced_tit = localStorage.ced_tit;
    $cuotas = localStorage.getItem("cuotas");
    if ($mail_tit !== null && $ced_tit !== null && $cuotas !== null) {
      $.ajax({
        url: "https://vida-apps.com/call_pagos/ajax/cobrarCuota.php",
        data: {
          token_mercadopago: $token_mercadopago,
          metodo_mercadopago: $metodo_mercadopago,
          mail_tit: $mail_tit,
          ced_tit: $ced_tit,
          cuotas: $cuotas,
          idSocio: localStorage.getItem("idSocio"),
        },
        method: "POST",
        dataType: "json",
        success: function (content) {
          swal(content.titulo_mensaje, content.mensaje, content.tipo_mensaje);
        },
        complete: function () {
          localStorage.removeItem("tokenValidador");
          localStorage.removeItem("paymentMethodId");
          localStorage.removeItem("mail_tit");
          localStorage.removeItem("ced_tit");
          localStorage.removeItem("cuotas");
          localStorage.removeItem("idSocio");
          $("#tokenValidador").val("");
          $("#paymentMethodId").val("");
          $("#rcForm").trigger("reset");
          $("#modal-red-cobranza").modal("hide");
          $("#modal-solictud-email").modal("hide");
        },
        error: function () {
          swal("Error", content.mensaje, "error");
        },
      });
    }
  } else {
    swal("Cuidado", "Debe ingresar un medio de pago", "warning");
    app.views.main.router.navigate("/payment-method/");
  }
}

function resetearCamposVidaShop() {
  $(".inputVidaShop").val("");
}

function registrarVidaShop() {
  const nombre = document.getElementById("_nombreSocio")
    ? document.getElementById("_nombreSocio").value
    : "";
  const cedula = document.getElementById("_cedulaSocio")
    ? document.getElementById("_cedulaSocio").value
    : "";
  const email =
    document.getElementById("_emailSocio") &&
    document.getElementById("_emailSocio").value.length > 0
      ? document.getElementById("_emailSocio").value
      : `${cedula}@vidashop.com.uy`;
  const vidapesos = document.getElementById("_vidaPesos")
    ? document.getElementById("_vidaPesos").value
    : 0;
  const count = document.getElementById("_countSocio")
    ? document.getElementById("_countSocio").value
    : 0;
  const direccion = "";
  const departamento = "";
  const ciudad = "";
  const codigo_postal = "";
  const telefono = document.getElementById("_telefonoSocio")
    ? document.getElementById("_telefonoSocio").value
    : "";
  const local_fisico = true;
  const idSocio = document.getElementById("_idsocio").value;

  $.ajax({
    type: "POST",
    url: "https://vida-apps.com/vidashop/ajax/registro.php",
    data: {
      nombre,
      cedula,
      email,
      vidapesos,
      direccion,
      departamento,
      ciudad,
      codigo_postal,
      telefono,
      local_fisico,
      count,
      contrasena: cedula,
      plataforma: "web",
    },
    dataType: "JSON",
    success: function (r) {
      if (r.success) {
        swal({
          title: "Exito",
          text: "¡Registrado con exito en VidaShop!",
          icon: "success",
          buttons: "Ok",
        });
        $("#modal-solictud-email").modal("hide");
        resetearCamposVidaShop();
        if (vidapesos == 0) {
          //newrc

          $.confirm({
            title: "¿Desea el socio adelantar la cuota?",
            content:
              "Este socio tiene la posibilidad de adelantar la cuota por alguna de nuestras redes de cobranza",
            buttons: {
              confirm: {
                text: "Sí",
                btnClass: "btn-blue",
                action: function () {
                  $("#rcCedula").val(cedula);
                  $("#rcMail").val(email);
                  $("#rcIdSocio").val(idSocio);
                  $("#modal-red-cobranza").modal("show");
                },
              },
              cancel: {
                text: "No",
                btcClass: "btn-red",
                action: function () {
                  swal({
                    title: "Exito",
                    text: "¡Registrado con exito en VidaShop!",
                    icon: "success",
                    buttons: "Ok",
                  });
                },
              },
            },
          });
        }
      } else if (r.error) {
        swal({
          title: "Error",
          text: "Ocurrio un error",
          icon: "error",
          buttons: "Ok",
        });
      }
    },
  });
}

function cargarEmail(e) {
  e.preventDefault();

  const email = $("#emailSocio").val();

  if (email && !validarEmail(email)) {
    swal({
      title: "Error",
      text: "El email ingresado no es valido",
      icon: "error",
    });
  } else {
    $("#_emailSocio").val(email);
    // llamar a la funcion del Toño
    registrarVidaShop();
  }
}

function closeSession() {
  localStorage.clear();
  location.href = "./login.php";
}

function verifySession() {
  if (localStorage.username === undefined) location.href = "./login.php";
}

//evento que arma el datatable del dashboard
function dashboard(e) {
  const RADIOS_ADELANTADOS = [
    "10901",
    "10902",
    "10903",
    "10904",
    "10905",
    "10906",
    "10907",
    "10908",
    "10909",
    "10910",
    "10911",
    "10912",
  ];
  const COLORS = {
    "Pendiente revisión": "#03a9f4",
    "Pendiente morosidad": "#03a9f4",
    "En proceso de bienvenida": "#03a9f4",
    "Pendiente Nicolas": "#ccc",
    "Pendiente de llamar": "#ccc",
    "Aprobado por bienvenida": "#28a745",
    "Aprobado morosidad": "#28a745",
    "Rechazo por bienvenida": "#ba000d",
    "Rechazado por morosidad": "#f44336",
  };

  $table = $("#table-dashboard").DataTable({
    ajax: {
      url: "Ajax/dashboard.php",
      data: {
        username: localStorage.usuario,
        typeAdmin: localStorage.typeAdmin,
        desde: $("#desde").val(),
        hasta: $("#hasta").val(),
      },
      method: "POST",
    },
    destroy: true,
    stateSave: true,
    initComplete: function (settings, json) {
      $('[data-toggle="tooltip"]').tooltip();
      $('[data-toggle="popover"]').popover();
      let i = 0;
      this.api()
        .columns()
        .every(function (index) {
          if ([2, 3, 4, 5, 6, 14].includes(index)) {
            let column = this;
            let select = $(`#select-filter-${i}`).on("change", function () {
              let val = $.fn.dataTable.util.escapeRegex($(this).val());
              column.search(val ? "^" + val + "$" : "", true, false).draw();
            });

            if (index != 6) {
              column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                  select.append(`<option value="${d}">${d}</option>`);
                });
            }
            i++;
          }
        });
    },
    lengthMenu: [10],
    searching: true,
    paging: true,
    lengthChange: false,
    ordering: false,
    info: true,
    destroy: true,
    oLanguage: {
      sUrl: "js/spanish.json",
    },
    responsive: true,
    columnDefs: [
      {
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
        className: "dt-body-center",
      },
      {
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
        className: "dt-head-center",
      },
      {
        targets: 6,
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).attr("data-toggle", "tooltip");
          $(td).attr("data-placemnt", "top");
          $(td).attr("title", cellData);
          $(td).css("text-transform", "uppercase");
          $(td).css("font-weight", "bold");
          $(td).css("color", COLORS[cellData]);
        },
      },
      {
        targets: 11,
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).attr("data-toggle", "tooltip");
          $(td).attr("data-placemnt", "top");
          $(td).attr("title", cellData);
          $(td).css("text-transform", "uppercase");
          $(td).css("font-weight", "bold");
          $(td).css("color", COLORS[cellData]);
        },
      },
      {
        targets: 12,
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).attr("data-toggle", "popover");
          $(td).attr("data-placemnt", "top");
          $(td).attr("data-trigger", "focus");
          $(td).attr("data-container", "body");
          $(td).attr("data-content", cellData);
          $(td).attr("title", cellData);
        },
      },
    ],
    createdRow: function (row, data, dataIndex) {
      //seba si es radio adelantado pinto el row de amarillo
      if (RADIOS_ADELANTADOS.includes(data[2])) {
        $(row).addClass("cobroAdelantado");
      }
    },
    drawCallback: function () {
      $('[data-toggle="popover"]').popover();
    },
  });

  $("#nav").removeClass("show");
  $("#history").hide();
  $("#control_abm").hide();
  $("#control_piscina").hide();
  $("#dashboard").is(":hidden") && $("#dashboard").show();
}

function history() {
  if (!$.fn.DataTable.isDataTable("#table-history")) {
    $table = $("#table-history").DataTable({
      ajax: {
        url: "Ajax/history.php",
        data: {
          username: localStorage.usuario,
          typeAdmin: localStorage.typeAdmin,
        },
        method: "POST",
      },
      lengthMenu: [20],
      searching: true,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      oLanguage: {
        sUrl: "js/spanish.json",
      },
      responsive: true,
    });
  } else {
    $("#primaria").css("display", "flex");
    $table.ajax.reload(function () {
      $("#primaria").css("display", "none");
    }, false);
  }
  $("#nav").removeClass("show");
  $("#dashboard").hide();
  $("#control_abm").hide();
  $("#afiliaciones").hide();
  $("#history").is(":hidden") && $("#history").show();
  $("#control_piscina").hide();
}

function verAfiliaciones() {
  if (!$.fn.DataTable.isDataTable("#tabla-afiliaciones")) {
    $table = $("#tabla-afiliaciones").DataTable({
      ajax: {
        url: "Ajax/verAfiliaciones.php",
        data: {
          username: localStorage.usuario,
          typeAdmin: localStorage.typeAdmin,
        },
        method: "POST",
      },
      lengthMenu: [20],
      searching: true,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      oLanguage: {
        sUrl: "js/spanish.json",
      },
      responsive: true,
    });
  } else {
    $("#primaria").css("display", "flex");
    $table.ajax.reload(function () {
      $("#primaria").css("display", "none");
    }, false);
  }
  $("#nav").removeClass("show");
  $("#dashboard").hide();
  $("#history").hide();
  $("#control_abm").hide();
  $("#afiliaciones").is(":hidden") && $("#afiliaciones").show();
  $("#control_piscina").hide();
}

function verPanelControl() {
  $("#dashboard").hide();
  $("#control_abm").is(":hidden") && $("#control_abm").show();
  $("#history").hide();
  $("#afiliaciones").hide();
  $("#control_piscina").hide();
}

function verPanelControlPiscina() {
  //cpiscina
  $("#dashboard").hide();
  $("#control_piscina").is(":hidden") && $("#control_piscina").show();
  $("#history").hide();
  $("#afiliaciones").hide();
  $("#control_abm").hide();
}

function cancelarAfiliacion(idAfiliado) {
  swal({
    title: "Eliminar afiliación",
    text: `¿Estás seguro que deseas eliminar está afiliación?`,
    icon: "warning",
    buttons: ["Cancelar", "Eliminar"],
    dangerMode: true,
  }).then(function (ok) {
    if (ok) {
      $.ajax({
        url: "Ajax/procesoCancelarAfiliacion.php",
        data: {
          idAfiliado: idAfiliado,
          idUser: localStorage.idUser,
          typeAdmin: localStorage.typeAdmin,
        },
        dataType: "json",
        method: "POST",
        success: function (response) {
          swal({
            title: response.title,
            text: response.message,
            icon: response.icon,
          });
          dashboard();
        },
        error: function (error) {
          swal("Lo sentimos, ocurrio un error al realizar la operación.");
        },
      });
    }
  });
}

function seleccionarRuta(id) {
  let ruta_socio = $("#ruta").val();
  localStorage.ruta = ruta_socio;
  $.ajax({
    url: "Ajax/obetenerDatosSocio.php",
    data: { id: id, typeAdmin: localStorage.typeAdmin },
    dataType: "json",
    method: "POST",
    beforeSend: function () {
      $("#primaria").show();
    },
    success: function (response) {
      $("#primaria").hide();
      if (response.result) {
        const socio = response.socio;
        if (socio.metodo_pago == "1") {
          // Rutas
          let rutas =
            "<option value='0000000000' selected>Selecciona una ruta</option>";
          socio.rutas.forEach(function (ruta) {
            const isSelectected = ruta == socio.ruta ? "selected" : "";
            rutas += `<option value="${ruta}" ${isSelectected}>${ruta}</option>`;
          });

          $("#modalRutas .modal-body").html(`
            <div class="form-group">
              <label for="ruta">Ruta</label>
              <select id="ruta" class="form-control">${rutas}</select>
              <input type="hidden" name="_idCliente" id="_idCliente" value="${id}">
            </div>
          `);
          $("#modalRutas").modal("show");
        }
      }
    },
  });
}

function cambiarRuta(e) {
  e.preventDefault();

  if (!$("#ruta").val()) {
    swal({
      title: "Por favor, selecciona una ruta!",
      icon: "error",
    });
  } else {
    $.ajax({
      url: "Ajax/procesoSeleccionarRuta.php",
      data: {
        typeAdmin: localStorage.typeAdmin,
        id: $("#_idCliente").val(),
        ruta: $("#ruta").val(),
      },
      method: "POST",
      dataType: "json",
      success: function (response) {
        if (response.result) {
          swal({
            title: "Ruta agregada con exito!",
            icon: "success",
          });
          $("#modalRutas").modal("hide");
          dashboard();
        } else {
          swal({
            title: "Lo sentimos, ocurrio un error.",
            icon: "error",
          });
        }
      },
    });
  }
}

//CALCULAR PRECIO DE LOS SERVICIOS
function calcularPrecio(
  servicio,
  hrs_sanatorio,
  hrs_servicio,
  localidad,
  fecha_nacimiento,
  base
) {
  $data =
    "servicio=" +
    servicio +
    "&hrs_sanatorio=" +
    hrs_sanatorio +
    "&hrs_servicio=" +
    hrs_servicio +
    "&localidad=" +
    localidad +
    "&fecha_nacimiento=" +
    fecha_nacimiento +
    "&base=" +
    base;
  $arrPrecios = [];
  $.ajax({
    url: "Ajax/calcularPrecio.php",
    data: $data,
    method: "POST",
    dataType: "json",
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
    },
  });
  return $arrPrecios;
}

// MODAL DATOS SOCIO
function verDatosSocio(id, editar = false) {
  let tipoAdmin = localStorage.typeAdmin;
  $.ajax({
    url: "Ajax/obetenerDatosSocio.php",
    data: { id: id, typeAdmin: localStorage.typeAdmin },
    dataType: "json",
    method: "POST",
    beforeSend: function () {
      $("#primaria").show();
    },
    success: function (response) {
      localStorage.setItem("metodo_pago", response.socio.metodo_pago);
      localStorage.setItem("radio", response.socio.radio);
      localStorage.setItem("ruta", response.socio.ruta);
      let correspondeVidaPesos = response.socio.correspondeVidapesos
        ? '<span class="badge d-block badge-success p-2 mx-auto mb-2" style="font-size: 20px">La persona aplica para VidaPesos</span>'
        : "";
      let promoVuelveAntes = response.socio.promoVuelveAntes
        ? '<br><span class="badge d-block badge-success p-2 mx-auto mb-2" style="font-size: 20px">Promo vuelve antes</span>'
        : "";
      let convenioEspecial = response.socio.convenioEspecial
        ? `<br><span class="badge d-block badge-success p-2 mx-auto mb-2" style="font-size: 20px">Convenio especial - ${response.socio.convenioEspecial}</span>`
        : "";
      let promoMadre = response.socio.promoMadre
        ? `<br><span class="badge d-block badge-success p-2 mx-auto mb-2" style="font-size: 20px">Promo Mamá titular ${response.socio.promoMadre.cedula_titular_gf}</span>`
        : "";
      $("#primaria").hide();
      if (response.result) {
        let disabled = editar ? "" : "disabled";
        // Socio
        const $modal = $("#modalDatosSocioComercial");
        const socio = response.socio;

        let inputHidden = ``;

        const fields = [
          ["nombre", "Nombre"],
          ["cedula", "Cédula"],
          ["tel", "Télefono"],
          ["localidad", "Localidad"],
          ["sucursal", ""],
          ["ruta", "Rutas"],
          ["direccion", "Dirección"],
          ["radio", "Radio"],
          ["observaciones", "Observación"],
          ["fecha_nacimiento", "Fecha de nacimiento"],
          ["tarjeta", "Tarjeta"],
          ["tipo_tarjeta", "Tipo de tarjeta"],
          ["numero_tarjeta", "Número de tarjeta"],
          ["nombre_titular", "Nombre de titular"],
          ["cedula_titular", "Cédula del titular"],
          ["telefono_titular", "Télefono del titular"],
          ["anio_e", "Año de vencimiento"],
          ["mes_e", "Mes de vencimiento"],
          ["cuotas_mercadopago", "Cuotas mercadopago"],
          ["cvv", "Cvv"],
          ["count", "Count"],
          ["email", "Email"],
          ["email_titular", "Email del titular"],
          ["total_importe", "Total importe $UY"],
          ["numero_vendedor", "Cédula del vendedor"],
          ["nombre_vendedor", "Nombre del vendedor"],
        ];

        let infoVidaPesos = socio.esRechazoComp
          ? `<div class="alert alert-danger" role="alert">
        Éste socio aplicó para promo competencia pero fue rechazado por calidad
        </div>`
          : ``; //compe

        let dataSocio = `   
        ${correspondeVidaPesos}
        ${promoVuelveAntes}
        ${promoMadre}
        ${convenioEspecial}
        ${infoVidaPesos}
            <div class="form-group">
              <p class="text-uppercase" style="font-weight: 600; background-color: #c62828; color: #fff; padding: 1rem;">
                ${socio.estado}
              </p> 
            </div> 
            <div class="form-group" id="btnServiciosContratados" class="d-flex flex-row-reverse" >
            <div class="d-flex justify-content-start">
            <a class="btn btn-success" id="btn_modificar_direccion_por_sistema_ruta" style="display:${
              sistema_rutas ? "block" : "none"
            };" href="sistema_rutas/?socio=${
          socio.id_socio
        }"   >Modificar Direccion</a>              
            </div>

              <div class="d-flex justify-content-end">
                <button class="btn btn-primary text-uppercase " onclick="event.preventDefault(); verProductosSocio('${
                  socio.cedula
                }');">Servicios contratados</button>
              </div>
            </div>`;

        fields.forEach(function (field) {
          //si es usuario de morosida/comercial habilita los campos de datos acerca de la tarjeta
          disabled =
            (tipoAdmin == "morosidad" || tipoAdmin == "comercial") &&
            (field[0] == "tarjeta" ||
              field[0] == "tipo_tarjeta" ||
              field[0] == "numero_tarjeta" ||
              field[0] == "nombre_titular" ||
              field[0] == "cedula_titular" ||
              field[0] == "telefono_titular" ||
              field[0] == "email_titular" ||
              field[0] == "anio_e" ||
              field[0] == "mes_e" ||
              field[0] == "cuotas_mercadopago" ||
              field[0] == "cvv")
              ? ""
              : "disabled";

          inputHidden =
            field[0] == "tarjeta"
              ? `<input type="hidden" id="payment_method_id" name="payment_method_id"><input type="hidden" id="is_mercadopago" name="is_mercadopago">`
              : "";

          // disabled = ((field[0] == 'count' || field[0] == 'total_importe') && tipoAdmin != "comercial") ? "disabled" : "";
          if (tipoAdmin == "comercial") disabled = "";

          if (field[0] == "ruta") {
            let rutas =
              '<option value="0000000000">Selecciona una ruta</option>';
            socio["rutas"].forEach(function (ruta) {
              let active = ruta == socio.ruta ? "selected" : "";
              rutas += `<option value="${ruta[0]}" ${active}>${ruta[0]}</option>`;
            });

            dataSocio += `<div class="form-group">
                <label for="${field[0]}">${field[1]}</label>
                <select class="form-control" ${disabled} id="select-ruta">${rutas}</select>
                ${inputHidden}
              </div>`;
          } else {
            if (field[0] == "sucursal") {
              // Sucursales
              let sucursales = "";
              response.filiales.forEach(function (filial) {
                const isSelectected =
                  filial[2] === socio.sucursal ? "selected" : "";
                sucursales += `<option value="${filial[1]}" ${isSelectected}>${filial[2]}</option>`;
              });

              dataSocio += `
                  <div class="form-group">
                    <label for="sucursal">Sucursal</label>
                    <select id="sucursal" class="form-control" ${disabled}>${sucursales}</select>
                  </div>
                `;
            } else {
              if (field[0] == "observaciones") {
                let data = socio[field[0]].replace(/[\n\r]/g, "");

                dataSocio += `<div class="form-group">
                    <label for="${field[0]}">${field[1]}</label>
                    <textarea id="${
                      field[0]
                    }" class="form-control" readonly rows="6" cols="50"/>${data.trim()}</textarea>
                    ${inputHidden}
                  </div>`;
              } else if (field[0] == "direccion" && socio.origen_venta == "6") {
                //web
                dataSocio += `
                <div class="form-group">
                  <label for="direccion">Dirección</label>
                  <div class="d-flex justify-content-start">
                    <button class="btn btn-primary text-uppercase" onclick="event.preventDefault(); verModalDireccion('${socio.id_socio}');">Ingresar dirección</button>
                  </div>
                </div>
              `;
              } else if (field[0] == "cuotas_mercadopago") {
                dataSocio += `<div class="form-group">
                <label for="${field[0]}">${field[1]}</label>
                <input type="text"  value="${socio[field[0]]}" id="${
                  field[0]
                }" class="form-control" disabled/>
                  </div>`;
              } else {
                dataSocio += `<div class="form-group">
                    <label for="${field[0]}">${field[1]}</label>
                    <input type="${
                      field[0] == "cvv" ? "password" : "text"
                    }"  value="${socio[field[0]]}" id="${
                  field[0]
                }" class="form-control" ${disabled}/>
                    ${inputHidden}
                  </div>`;
              }
            }
          }
        });
        // Bancos Emisores
        let bancos = "<option value=''>Selecciona un banco</option>";
        response.bancos.forEach(function (banco) {
          const isSelectected =
            banco[0] === socio.banco_emisor ? "selected" : "";
          bancos += `<option value="${banco[0]}" ${isSelectected}>${banco[1]}</option>`;
        });

        dataSocio += `
            <div class="form-group">
              <label for="bancos">Banco emisor</label>
              <select id="bancos" class="form-control" ${
                tipoAdmin == "morosidad" ? "" : disabled
              }>${bancos}</select>
            </div>
          `;

        // Tarjeta vida
        // Método de pago
        dataSocio += `<div class="form-group">
              <p style="font-weight: 600; text-transform: uppercase;">Tarjeta de vida: ${
                socio.tarjeta_vida == 0 ? "NO" : "SI"
              }</p>
              <input type="hidden" value="${
                socio.tarjeta_vida
              }" id="tarjeta_vida"/>
              <input type="hidden" value="${
                socio.metodo_pago
              }" id="_metodoPago"/>
          </div>`;

        const button =
          editar || tipoAdmin == "morosidad"
            ? `
          <div class="form-group">
            <button class="btn btn-primary text-uppercase" onclick="event.preventDefault(); guardar(${socio.id_socio});">Gurdar datos</button>              
          </div>`
            : "";

        $modal.find(".modal-body").html(`
            <form id="formSocioComercial">
              ${dataSocio}
              ${button}
            </form>
          `);
        $modal.modal("show");
      }
      eventMercadoPago();
      if (tipoAdmin == "bienvenida") {
        $("#btnServiciosContratados").show();
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function verFormularioCobro(id) {
  $.ajax({
    method: "POST",
    url: "Ajax/obtenerDatosPago.php",
    data: { id: id, typeAdmin: localStorage.typeAdmin },
    dataType: "json",
    success: function (response) {
      var datos_modal = ``;
      var datos_tarjeta = ``;
      if (response.result) {
        var datos = response.datos_pago;

        localStorage.setItem("tarjeta_vida", datos.tarjeta_vida);
        localStorage.setItem("metodo_pago", datos.metodo_pago);
        localStorage.setItem("alta", datos.alta);
        localStorage.setItem("estado", datos.estado);
        localStorage.setItem("cobro_obligatorio", datos.cobro_obligatorio);

        let comentario = datos.esRechazoComp
          ? `<div class="alert alert-danger" role="alert">
        Éste socio aplicó para promo competencia pero fue rechazado por calidad
        </div>`
          : datos.aplicaAdelanto
          ? `<div class="alert alert-info" role="alert">
        Éste socio aplica para adelanto de cuota por redes de cobranza Abitab o Redpagos
        </div>`
          : ``; //compe

        let badgeVidapesos = datos.correspondeVidaPesos
          ? '<span class="badge badge-success d-block p-2 mx-auto mb-2" style="font-size: 20px">La persona aplica para VidaPesos</span>'
          : "";

        let badgePromoVuelveAntes = datos.promoVuelveAntes
          ? '<span class="badge badge-success d-block p-2 mx-auto mb-2" style="font-size: 20px">Promo Vuelve Antes</span>'
          : "";

        let badgeConvenioEspecial = datos.convenioEspecial
          ? `<span class="badge badge-success d-block p-2 mx-auto mb-2" style="font-size: 20px">Convenio Especial - ${datos.convenioEspecial}</span>`
          : "";
        let badgePromoMadre = datos.promoMadre
          ? `<span class="badge badge-success d-block p-2 mx-auto mb-2" style="font-size: 20px">Promo Mamá titular ${datos.promoMadre.cedula_titular_gf}</span>`
          : "";

        let badgePagoAdelantado =
          datos.origenVenta == "6"
            ? '<span class="badge badge-success d-block p-2 mx-auto mb-2" style="font-size: 20px">Éste socio ya adelantó el pago por mercadopago a través de la web.</span>'
            : "";

        if (datos.metodo_pago == 2) {
          datos_tarjeta = `
                            <div class="row" id="divDatosTarjeta">
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label  for="cardNumber class="texto">Numero tarjeta: </label>
                                  <input type="text" class="form-control" id="cardNumber" data-checkout="cardNumber" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off  disabled value="${
                                    datos.numero_tarjeta
                                  }">
                                  <input type="hidden" id="paymentMethodId" name="paymentMethodId" value="${
                                    datos.tipo_tarjeta
                                  }">
                                  <input type="hidden" name="token" id="tokenValidador" value="">
                                  
                                  <input type="hidden" name="tarjetavida" id="tarjetavida" value="${
                                    datos.tarjeta_vida
                                  }">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label for="" class="texto">Tipo tarjeta: </label>
                                  <input type="text" class="form-control" id="tipo_tarjeta" disabled value="${
                                    datos.tipo_tarjeta
                                  }">
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label for="" class="texto">Cvv: </label>
                                  <input type="password" maxlength="4" name="securityCode" id="securityCode" class="form-control" data-checkout="securityCode" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off value="${
                                    datos.cvv.length == 3 ? datos.cvv : ""
                                  }">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label for="" class="texto">Cuotas mercadopago: </label>
                                  <input type="text" class="form-control" id="cuotas_mercadopago" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off disabled value="${
                                    datos.cuotas_mercadopago
                                  }">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label for="" class="texto">Email del titular: </label>
                                  <input type="text" class="form-control" id="email_titular" data-checkout="email_titular" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off disabled value="${
                                    datos.email_titular
                                  }">
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label for="" class="texto">Año vencimiento: </label>
                                  <input type="text" class="form-control" id="cardExpirationYear" data-checkout="cardExpirationYear" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off disabled value="${
                                    datos.anio_e
                                  }">
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label for="" class="texto">Mes vencimiento: </label>
                                  <input type="text" class="form-control" id="cardExpirationMonth" data-checkout="cardExpirationMonth" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off disabled value="${
                                    datos.mes_e
                                  }">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-5">
                                <div class="form-group">
                                  <label for="" class="texto">Telefono del titular: </label>
                                  <input type="text" class="form-control" id="telefono_titular" data-checkout="telefono_titular" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off disabled value="${
                                    datos.telefono_titular
                                  }">
                                </div>
                              </div>
                            </div>
                         `;
        }

        datos_modal = `
        <h3>Datos del Beneficiario</h3>
        ${badgePromoVuelveAntes}
        ${badgeConvenioEspecial}
        ${badgeVidapesos}
        ${badgePromoMadre}
        ${badgePagoAdelantado}
        ${comentario}
        <div class="row" compe>
          <div class="col-md-5">
            <div class="form-group">
              <label for="" class="texto">Nombre del beneficiario</label>
              <input type="text" class="form-control" id="nombreBeneficiario" disabled value="${datos.nombre}">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="" class="texto">Cédula</label>
              <input type="text" class="form-control" id="cedula_pago" disabled value="${datos.cedula}">
            </div>
          </div>
        </div>
        <hr>
        <h3>Datos del Pago</h3>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="" class="texto">Metodo de pago: </label>
              <input type="text" class="form-control" id="metodo_pago" disabled value="${datos.metodo}">
            </div>
          </div>
        </div>
        <form method="post" id="pay" name="pay">
        <div class="row">
          <div class="col-md-5">
            <div class="form-group">
              <label for="" class="texto">Nombre titular: </label>
              <input type="text" class="form-control" id="cardholderName" data-checkout="cardholderName" disabled value="${datos.nombre_titular}">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="" class="texto">Cedula titular: </label>
              <input type="text" class="form-control" id="docNumber" data-checkout="docNumber" disabled value="${datos.cedula_titular}">
            </div>
          </div>
        </div>
        
        ${datos_tarjeta}
        <input type="hidden" name="id_socio" id="id_socio" value="${datos.id_socio}">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="transaction_amount" class="texto">TOTAL: $UY</label>
              <input type="text" class="form-control"  name="transaction_amount" id="transaction_amount" disabled value="${datos.total_importe}">
            </div>
          </div>
        </div>
        </form>`;

        $("#divDatosPago").html(datos_modal);
        $("#modal-formulario-cobro").modal("show");
      }
    },
  });
}

// Funcion que realiza el cobro
/*function confirmarCobro(e) {
  e.preventDefault();
  let id = $('#id_socio').val();
  var ob = $('#observacionCobro').val();
  var cvv = $('#securityCode').val();
  $cedulaAfiliado =  $('#cedula').val();
  if (ob == '') {
    $('.error-cobro').text('').append('Debe ingresar una observación');
    $('#error-cobro').show().fadeOut(10000);
  } else {  
    if (localStorage.metodo_pago == '2' && localStorage.alta == '1') { // Alta con tarjeta
      
      if (localStorage.tarjeta_vida == '0') { // Tarjeta de MERCADO PAGO

        // COMPROBAR ALTA CON MERCADO PAGO SIN CVV
        if (cvv == '0' || cvv == '') {
          let buttons = {
            omitir: {
              text: "Ingresar sin mercado pago",
              value: "omitir"
            },
            catch: {
              text: "Ingresar",
              value: "catch"
            },
            cancel: {
              text: "Cancelar",
              value: "default"
            }
          };

          if (localStorage.estado != 8) { // NO FUE COMPROBADO POR MOROSIDAD
            buttons.catch = {
              text: "Comprobar morosidad",
              value: "morosidad"
            };
          }

          swal("¿Quieres ingresar el CVV?", {
            buttons: buttons
          })
            .then((value) => {
              switch (value) {
                case "catch":
                  // Ingresa el CVV
                  $('#securityCode').focus();
                  break;
                case "omitir":
                  // Ingresa por vida ya que previamente fue comprobada por morosidad
                  cobroAprobado(id, ob, false);
                  $('#modal-formulario-cobro').modal('hide');
                  break;
                case "morosidad":
                  // Cambia el estado a "PENDIENTE MOROSIDAD"
                  continuarProceso(id, 2);
                  $('#modal-formulario-cobro').modal('hide');
                  break;
                default:
                  $('#modal-formulario-cobro').modal('hide');
                  return;
              }
            });
        } else {
          tokenPagar({
            nombreTitular: $('#cardholderName').val(),
            anoVencimiento: $('#cardExpirationYear').val(),
            mesVencimiento: $('#cardExpirationMonth').val(),
            numeroTarjeta: $('#cardNumber').val(),
            peticion_pago: true,
            numeroCedula: $('#docNumber').val(),
            csv: cvv,
            totalImporte: $('#transaction_amount').val(),
            paymentMethodId: $('#paymentMethodId').val(),
            cuotas: 1,
            email: $('#email_titular').val(),
            telefono: $('#telefono_titular').val()
          });
          $('#modal-formulario-cobro').modal('hide');
          dashboard();
        }
      } else {
        cobroAprobado(id,ob, false); 
        $('#modal-formulario-cobro').modal('hide');
        dashboard();
      }     
    } else {
      cobroAprobado(id,ob, false); 
      $('#modal-formulario-cobro').modal('hide');
      dashboard();
    }
  }
}*/

function controlDuplicado(idAfiliado, cedulaAfiliado) {
  let retorno = false;
  $.ajax({
    type: "POST",
    url: "Ajax/existeEnPadron.php",
    data: { cedula: cedulaAfiliado, id: idAfiliado },
    dataType: "json",
    async: false,
    success: function (response) {
      retorno = response.existe;
    },
    error: function (error) {
      console.log(error);
    },
  });
  return retorno;
}

// Funcion que realiza el cobro
function confirmarCobro(e) {
  e.preventDefault();
  let id = $("#id_socio").val();
  var ob = $("#observacionCobro").val();
  var cvv = $("#securityCode").val();
  $cedulaAfiliado = $("#cedula_pago").val();
  let cobro_obligatorio = localStorage.getItem("cobro_obligatorio");

  if (ob == "") {
    $(".error-cobro").text("").append("Debe ingresar una observación");
    $("#error-cobro").show().fadeOut(10000);
  } else if ((cvv == "" || cvv == 0) && cobro_obligatorio === "true") {
    $(".error-cobro")
      .text("")
      .append("Debe ingresar el CVV, es obligatorio el cobro por MercadoPago");
    $("#error-cobro").show().fadeOut(15000);
  } else {
    if (
      localStorage.metodo_pago == "2" &&
      localStorage.alta == "1" &&
      localStorage.estado != 685
    ) {
      // Alta con tarjeta
      if (localStorage.tarjeta_vida == "0") {
        // Tarjeta de MERCADO PAGO
        // COMPROBAR ALTA CON MERCADO PAGO SIN CVV
        if (cvv == "0" || cvv == "") {
          let title = "¿Quieres ingresar el CVV?";
          let content = "";
          // let buttons = {
          //   omitir: {
          //     text: "Ingresar SIN Mercado Pago",
          //     value: "omitir"
          //   },
          //   catch: {
          //     text: "Ingresar CVV",
          //     value: "catch"
          //   },
          //   cancel: {
          //     text: "Cancelar",
          //     value: "default"
          //   }
          // };

          let buttons = {
            omitir: {
              text: "Ingresar sin MercadoPago",
              action: function () {
                // Ingresa por vida ya que previamente fue comprobada por morosidad
                cobroAprobado(id, ob, false, 1);
                $("#modal-formulario-cobro").modal("hide");
              },
            },
            cvv: {
              text: "Ingresar CVV",
              action: function () {
                // Ingresa el CVV
                $("#securityCode").focus();
              },
            },
          };

          if (localStorage.estado != 8) {
            // NO FUE COMPROBADO POR MOROSIDAD
            delete buttons.omitir;
            title = "ATENCIÓN, el cliente no brindo el CVV de la tarjeta";
            content = "¿Que desea realizar?";
            buttons.morosidad = {
              text: "Comprobar morosidad",
              action: function () {
                //Cambia el estado a "PENDIENTE MOROSIDAD"
                continuarProceso(id, 2);
                $("#modal-formulario-cobro").modal("hide");
                dashboard();
              },
            };

            // title = "ATENCIÓN, el cliente no brindo el CVV de la tarjeta, ¿Qué deseas realizar?";
            // buttons.morosidad = {
            //   text: "Comprobar morosidad",
            //   value: "morosidad"
            // };
          }

          buttons.cancelar = {
            text: "Cancelar",
            btnClass: "btn-red",
          };

          $.confirm({
            title: title,
            content: content,
            boxWidth: "30%",
            buttons: buttons,
          });

          // swal(title, {
          //   buttons: buttons
          // })
          //   .then((value) => {
          //     switch (value) {
          //       case "catch":
          //         // Ingresa el CVV
          //         $('#securityCode').focus();
          //         break;
          //       case "omitir":
          //         // Ingresa por vida ya que previamente fue comprobada por morosidad
          //         cobroAprobado(id, ob, false,1);
          //         $('#modal-formulario-cobro').modal('hide');
          //         break;
          //       case "morosidad":
          //         // Cambia el estado a "PENDIENTE MOROSIDAD"
          //         continuarProceso(id, 2);
          //         $('#modal-formulario-cobro').modal('hide');
          //         dashboard();
          //         break;
          //       default:
          //         $('#modal-formulario-cobro').modal('hide');
          //         return;
          //     }
          //   });
        } else {
          if (!controlDuplicado(id, $cedulaAfiliado)) {
            tokenPagar({
              nombreTitular: $("#cardholderName").val(),
              anoVencimiento: $("#cardExpirationYear").val(),
              mesVencimiento: $("#cardExpirationMonth").val(),
              numeroTarjeta: $("#cardNumber").val(),
              peticion_pago: true,
              numeroCedula: $("#docNumber").val(),
              csv: cvv,
              totalImporte: $("#transaction_amount").val(),
              paymentMethodId: $("#paymentMethodId").val(),
              email: $("#email_titular").val(),
              telefono: $("#telefono_titular").val(),
            });
            $("#modal-formulario-cobro").modal("hide");
            dashboard();
          } else {
            swal({
              title: "Error",
              icon: "error",
              text: `La cédula ${$cedulaAfiliado} ya se encuentra en padrón.`,
              button: "Cerrar",
            });
          }
        }
      } else {
        cobroAprobado(id, ob, false, 2);
        $("#modal-formulario-cobro").modal("hide");
        dashboard();
      }
    } else {
      cobroAprobado(id, ob, false, 3);
      $("#modal-formulario-cobro").modal("hide");
      dashboard();
    }
  }
}

function enviarPago() {
  let id_socio = $("#id_socio").val();
  let observacion = $("#observacionCobro").val();
  $cedulaAfiliado = $("#cedula_pago").val();
  $.ajax({
    url: "Ajax/procesarPago.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      id_afiliado: $("#id_socio").val(),
      token: $("#tokenValidador").val(),
      email: $("#email_titular").val(),
      cedulaBeneficiario: $("#cedula_pago").val(),
      cedulaTitular: $("#docNumber").val(),
      cardNumber: $("#cardNumber").val(),
      tipoTarjeta: $("#paymentMethodId").val(),
      total_importe: $("#transaction_amount").val(),
      observacion: $("#observacionCobro").val(),
      cvv: $("#securityCode").val(),
      cuotas_mercadopago: $("#cuotas_mercadopago").val(),
      id_usuario: localStorage.idUser,
    },
    dataType: "json",
    method: "POST",
    beforeSend: function () {
      $("#loader").css("display", "flex");
    },
    success: function (response) {
      $("#loader").css("display", "none");
      if (response.result) {
        if (response.approved == true) {
          cobroAprobado(id_socio, observacion, true, 4);
        } else {
          swal({
            title: response.titulo_mensaje,
            icon: response.tipo_mensaje,
            text: response.mensaje,
            buttons: {
              text: "Aceptar",
            },
          });
        }
      } else {
        swal({
          title: "Error",
          icon: "error",
          text: response.mensaje,
          button: "Cerrar",
        });
      }
    },
  });
}

// RECHAZO DE BIENVENIDA
function rechazo(event) {
  event.preventDefault();
  const $motivo = $("#motivoRechazo").val();
  const $observacion = $("#observacionRechazo").val();
  let error = false;
  let mensaje = "";
  $(".error-rechazo").text("");

  if (!$motivo) {
    error = true;
    mensaje = "Debe indicar un motivo de rechazo";
  } else if (!$observacion) {
    error = true;
    mensaje = "Debe indicar una observación";
  }

  if (error) {
    $(".error-rechazo").text(mensaje);
    $("#error-rechazo").show().fadeOut(5000);
  } else {
    $.ajax({
      url: "Ajax/procesoCambiarEstado.php",
      method: "POST",
      data: {
        id: $("#btnConfirmarRechazo").data("id"),
        motivo: $motivo,
        estado: $("#btnConfirmarRechazo").data("estado"),
        idUser: localStorage.idUser,
        typeAdmin: localStorage.typeAdmin,
        observacion: $observacion,
      },
      dataType: "json",
      success: function (response) {
        const options = {
          text: "Lo sentimos, ocurrio un error intenta más tarde.",
          title: "Error",
          icon: "error",
        };

        if (response.result) {
          options.title = "Exito!";
          options.text = "Operación realizada con exito!";
          options.icon = "success";
          $("#modalRechazos").modal("hide");
          dashboard();
        }

        swal(options);
      },
      error: function (error) {},
    });
  }
}

function mostrarModalRechazos(id, estado, title) {
  $.ajax({
    url: "Ajax/obtenerRechazos.php",
    method: "POST",
    data: {
      idUser: localStorage.idUser,
      typeAdmin: localStorage.typeAdmin,
    },
    dataType: "json",
    success: function (response) {
      const $modalRechazos = $("#modalRechazos");
      let options = `<option value=''>Selecciona un motivo</option>`;
      for (let rechazo of response.rechazos)
        options += `<option value=${rechazo[0]}>${rechazo[1]}</option>`;
      $modalRechazos.find(".modal-body").html(
        `<div class="alert alert-danger alert-dismissable" id="error-rechazo">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Error: </strong><span class="error-rechazo"></span>
        </div>
        <form>
          <div class="form-group">
            <select class="form-control" id="motivoRechazo">
              ${options}
            </select>
          </div>
          <div class="form-group">
            <textarea class="form-control" id="observacionRechazo" placeholder="Ingresa una observación..."></textarea>
          </div>
        </form>
        `
      );
      $("#btnConfirmarRechazo").data("id", id);
      $("#btnConfirmarRechazo").data("estado", estado);
      $modalRechazos.modal("show");
      $("#error-rechazo").hide();
    },
    error: function (error) {},
  });
}
// VER HISTORICO DE UN AFILIADO
function verHistorial(id) {
  $("#table-historial-afiliado").DataTable({
    ajax: {
      url: "Ajax/historico-afiliado.php",
      data: {
        username: localStorage.usuario,
        typeAdmin: localStorage.typeAdmin,
        id: id,
      },
      method: "POST",
    },
    initComplete: function (settings, json) {
      $('[data-toggle="tooltip"]').tooltip();
    },
    columnDefs: [
      {
        targets: [0, 1, 2, 3, 4, 5, 6],
        className: "dt-body-center",
      },
      {
        targets: [0, 1, 2, 3, 4, 5, 6],
        className: "dt-head-center",
      },
      {
        targets: 5,
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).attr("data-toggle", "tooltip");
          $(td).attr("data-placemnt", "top");
          $(td).attr("title", cellData);
        },
      },
    ],
    lengthMenu: [20],
    searching: true,
    paging: true,
    lengthChange: false,
    ordering: true,
    order: [[6, "desc"]],
    info: true,
    oLanguage: {
      sUrl: "js/spanish.json",
    },
    responsive: true,
    destroy: true,
  });
  $("#modalHistorico").modal("show");
}

//FUNCION QUE CAMBIA LOS ESTADOS DEL PROCESO
function continuarProceso(id, estado = false, title = "") {
  if (estado == 4) {
    mostrarModalRechazos(id, estado, title);
  } else if (
    id == 2 &&
    localStorage.metodo_pago == "1" &&
    (localStorage.radio == "" || localStorage.ruta == "")
  ) {
    swal({
      title: "Error",
      text: "El radio y la ruta deben estar cargados",
      icon: "error",
    });
  } else {
    swal({
      text: "Ingresa una observación",
      content: "input",
      button: {
        text: "Aceptar",
        closeModal: true,
      },
    }).then((observacion) => {
      if (!observacion) {
        swal({
          title: "Error",
          text: "Observación requerida",
          icon: "error",
        });
      } else {
        swal({
          title: title,
          text: "¿Estás seguro que deseas continuar con el proceso?",
          icon: "warning",
          buttons: ["Cancelar", "Ok"],
          dangerMode: false,
        }).then(function (ok) {
          if (ok) {
            $.ajax({
              url: "Ajax/procesoCambiarEstado.php",
              data: {
                id: id,
                estado: estado,
                observacion: observacion,
                idUser: localStorage.idUser,
                typeAdmin: localStorage.typeAdmin,
              },
              dataType: "json",
              method: "POST",
              success: function (response) {
                if (response.result) {
                  swal({
                    title: "Operación realizada con exito!",
                    icon: "success",
                  });
                } else {
                  swal("Lo sentimos, ocurrio un error intenta más tarde.");
                }
                dashboard();
              },
              error: function (error) {},
            });
          }
        });
      }
    });
  }
}

function cobroAprobado(id, ob, isMercadoPago, origen) {
  $.ajax({
    type: "POST",
    url: "Ajax/procesoCobroAprobado.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      idUser: localStorage.idUser,
      id: id,
      observacionCobro: ob,
      cedulaAfiliado: $cedulaAfiliado,
      isMercadoPago: isMercadoPago,
      origen: origen,
    },
    beforeSend: function () {
      $("#loader").css("display", "flex");
      $("#btnConfirmarCobro").attr("disabled", true);
    },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        $("#loader").css("display", "none");
        $("#btnConfirmarCobro").attr("disabled", false);

        if (response.guardado_padron) {
          swal({
            title: response.title,
            text: response.message,
            icon: response.icon,
            buttons: "Ok",
          }).then(function () {
            /*if (response.registroVidaShop) {
              $('#_nombreSocio').val(response.datosVidaShop.nombreSocio);
              $('#_cedulaSocio').val(response.datosVidaShop.cedulaSocio);
              $('#_telefonoSocio').val(response.datosVidaShop.telefonoSocio);
              $('#_vidaPesos').val(response.datosVidaShop.vidaPesos);
              $('#_countSocio').val(response.datosVidaShop.countSocio);
              $('#_idsocio').val(response.datosVidaShop.id_socio);

              if (response.preguntarEmail) {
                $('#modal-solictud-email').modal('show');
              } else {
                $('#_emailSocio').val(response.datosVidaShop.emailSocio);
                // llamar a la funcion del Toño
                registrarVidaShop();
              }
            }*/

            if (response.esPromoComp && !isMercadoPago) {
              //compe

              $("#_cedulaSocio").val(response.datosAdelantoCobro.cedulaSocio);
              $("#_idsocio").val(response.datosAdelantoCobro.id_socio);
              $("#_countSocio").val(response.datosAdelantoCobro.countSocio);

              $.confirm({
                //compe
                title: "¿Desea el socio adelantar la cuota?",
                content:
                  "Este socio tiene la posibilidad de adelantar la cuota por alguna de nuestras redes de cobranza",
                buttons: {
                  confirm: {
                    text: "Sí",
                    btnClass: "btn-blue",
                    action: function () {
                      $("#rcCedula").val(
                        response.datosAdelantoCobro.cedulaSocio
                      );
                      $("#rcMail").val(response.datosAdelantoCobro.emailSocio);
                      $("#rcIdSocio").val(response.datosAdelantoCobro.id_socio);
                      $("#modal-red-cobranza").modal("show");
                    },
                  },
                  cancel: {
                    text: "No",
                    btcClass: "btn-red",
                    action: function () {},
                  },
                },
              });
            }
          });

          dashboard();
          $(".modal-formulario-cobro").modal("hide");
        } else {
          swal({
            title: response.title,
            text: response.message,
            icon: response.icon,
            buttons: "Ok",
          });
        }
      }
    },
  });
}

function verModalDireccion(idSocio) {
  //web
  let row = ``;
  let arrDireccion = buscarDireccionSocio(idSocio);
  // console.log(arrDireccion);return false;
  limpiarDatosDireccion();

  $("#calle").val(arrDireccion.calle);
  if (arrDireccion.puerta != "") $("#puertaChecked").click();
  if (arrDireccion.solar != "") $("#solarChecked").click();
  $("#puerta").val(arrDireccion.puerta);
  $("#manzana").val(arrDireccion.manzana);
  $("#solar").val(arrDireccion.solar);
  $("#apto").val(arrDireccion.apartamento);
  $("#esquina").val(arrDireccion.esquina);
  $("#referencia").val(arrDireccion.referencia);
  $("#idDir").val(arrDireccion.id);

  // $("#btnGuardarBeneficiarios").click(function(){guardarBeneficiariosServicios(cantBen)}); //newform
  $("#modalEditarDireccion").modal("show");
}

function buscarDireccionSocio(idSocio) {
  //web
  let dir = "";
  $.ajax({
    type: "POST",
    url: "Ajax/buscarDireccionSocio.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      idSocio,
    },
    dataType: "json",
    async: false,
    success: function (response) {
      if (response.result) {
        dir = response.direccion;
      }
    },
  });

  return dir;
}

function calcularCaracteresDisponibles(campo) {
  //web
  let caracteresActuales = campo.value.length;
  let limiteCaracteres = Number(campo.attributes.maxLength.value);
  return limiteCaracteres - caracteresActuales;
}

function limpiarDatosDireccion() {
  //web
  $("#calle").val("");
  $("#puerta").val("");
  $("#mazana").val("");
  $("#solar").val("");
  $("#apto").val("");
  $("#esquina").val("");
  $("#referencia").val("");
}

$(".calcularCaracteresDisponibles").keyup(function () {
  //web
  $(this).next().last().children().text(calcularCaracteresDisponibles(this));
});

$(".checkPuerta").click(function () {
  //web
  if ($(this).val() == "0") {
    $("#divPuerta").show();
    $("#divSolar").hide();
    $("#divManzana").hide();
    $("#solar").val("");
    $("#manzana").val("");
  } else if ($(this).val() == "1") {
    $("#divPuerta").hide();
    $("#divSolar").show();
    $("#divManzana").show();
    $("#puerta").val("");
  } else {
    $("#divPuerta").hide();
    $("#divSolar").hide();
    $("#divManzana").hide();
    $("#solar").val("");
    $("#manzana").val("");
    $("#puerta").val("");
  }
});

$("#btnGuardarDireccion").click(function () {
  //web
  // debugger;
  let calle = $("#calle").val().trim().toUpperCase();
  let puerta = $("#puerta").val().trim().toUpperCase();
  let solar = $("#solar").val().trim().toUpperCase();
  let manzana = $("#manzana").val().trim().toUpperCase();
  let esquina = $("#esquina").val().trim().toUpperCase();
  let apartamento = $("#apto").val().trim().toUpperCase();
  let referencia = $("#referencia").val().trim().toUpperCase();
  let idDir = $("#idDir").val();
  let direccion;

  if (
    !$("#puertaChecked").is(":checked") &&
    !$("#solarChecked").is(":checked")
  ) {
    swal({
      title: "Debe seleccionar puerta o solar/manzana.",
      icon: "error",
    });
  } else if (
    calle == "" ||
    ($("#puertaChecked").is(":checked") && puerta == "") ||
    ($("#solarChecked").is(":checked") && solar == "") ||
    esquina == "" ||
    referencia == ""
  ) {
    swal({
      title: "Debe llenar todos los campos con requeridos(*).",
      icon: "error",
    });
  } else {
    if ($("#puertaChecked").is(":checked")) {
      direccion = armarDireccion(
        { calle, puerta, esquina, apartamento, referencia, solar, manzana },
        true
      );
    } else {
      direccion = armarDireccion(
        { calle, puerta, esquina, apartamento, referencia, solar, manzana },
        false
      );
    }

    guardarDireccionSocio({
      calle,
      puerta,
      solar,
      manzana,
      apartamento,
      esquina,
      referencia,
      direccion,
      idDir,
    });
  }
});

function guardarDireccionSocio(objdir = {}) {
  //web

  $.ajax({
    type: "POST",
    url: "ajax/guardarDireccion.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      objdir: JSON.stringify(objdir),
    },
    dataType: "json",
    success: function (response) {
      let icon = response.result ? "success" : "error";
      swal({
        title: response.message,
        icon: icon,
      });
    },
  });
}

function armarDireccion(datosDir = {}, esPuerta = true) {
  //web

  if (esPuerta) {
    let direccion =
      datosDir.apto != ""
        ? datosDir.calle.substr(0, 14) +
          " " +
          datosDir.puerta +
          "/" +
          datosDir.apartamento +
          " E:"
        : datosDir.calle.substr(0, 17) + " " + datosDir.puerta + " E:";
    direccion += datosDir.esquina.substr(0, 36 - direccion.length);

    return direccion;
  } else {
    let direccion =
      datosDir.apto != ""
        ? datosDir.calle.substr(0, 14) +
          " M:" +
          datosDir.manzana +
          " S:" +
          datosDir.solar +
          "/" +
          datosDir.apartamento
        : datosDir.calle.substr(0, 14) +
          " M:" +
          datosDir.manzana +
          " S:" +
          datosDir.solar +
          " E:";
    direccion +=
      datosDir.apartamento == ""
        ? datosDir.esquina.substr(0, 36 - direccion.length)
        : "";

    return direccion;
  }
}

//ACTUALIZA LOS DATOS DEL SOCIO
function guardar(id) {
  let retorno = null;
  if (localStorage.metodo_pago == "2") {
    retorno = validarTarjeta();
  } else {
    retorno = true;
  }

  if (retorno) {
    localStorage.radio = $("#radio").val();
    localStorage.ruta = $("#ruta").val();
    const data = {
      id: id,
      nombre: $("#nombre").val(),
      cedula: $("#cedula").val(),
      tel: $("#tel").val(),
      ruta: $("#select-ruta").val(),
      direccion: $("#direccion").val(),
      radio: $("#radio").val(),
      fechaNacimiento: $("#fecha_nacimiento").val(),
      tarjeta: $("#tarjeta").val(),
      tipoTarjeta: $("#tipo_tarjeta").val(),
      numeroTarjeta: $("#numero_tarjeta").val(),
      nombreTitular: $("#nombre_titular").val(),
      cedulaTitular: $("#cedula_titular").val(),
      telefonoTitular: $("#telefono_titular").val(),
      anio_e: $("#anio_e").val(),
      mes_e: $("#mes_e").val(),
      cvv: $("#cvv").val(),
      count: $("#count").val(),
      email: $("#email").val(),
      emailTitular: $("#email_titular").val(),
      observaciones: $("#observaciones").val(),
      totalImporte: $("#total_importe").val(),
      sucursal: $("#sucursal").val(),
      bancoEmisor: $("#bancos").val(),
      numero_vendedor: $("#numero_vendedor").val(),
      nombre_vendedor: $("#nombre_vendedor").val(),
      tarjetaVida: $("#tarjeta_vida").val(),
      is_mercadopago: localStorage.mercadopago,
      typeAdmin: localStorage.typeAdmin,
      idUser: localStorage.idUser,
    };

    $.ajax({
      url: "Ajax/procesoActualizarSocio.php",
      data: data,
      dataType: "json",
      method: "POST",
      async: false,
      success: function (response) {
        dashboard();
        if (response.result) {
          swal({
            title: "Éxito!",
            text: "Datos actualizados correctamente",
            icon: "success",
            buttons: "Ok",
          });
          $("#modalDatosSocioComercial").modal("hide");
        } else {
          swal({
            title: "Error!",
            text: response.message,
            icon: "error",
            buttons: "Ok",
          });
        }
      },
      error: function (error) {
        // console.log(error);
      },
    });
  } else {
    swal("Error!", "La tarjeta ingresada es inválida", "error");
  }
}

//PETICION PARA OBTENENER LOS PRODUCTOS DEL SOCIO
function verProductosSocio(id) {
  $.ajax({
    type: "POST",
    url: "Ajax/obtenerProductosSocio.php",
    data: { id: id, typeAdmin: localStorage.typeAdmin },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        $("#datos_productos").html("");
        localStorage.setItem("localidad", response.localidad);
        localStorage.setItem("fecha_nacimiento", response.fecha_nacimiento);
        localStorage.setItem("cedula", response.cedula);
        localStorage.setItem("nombre_cliente", response.nombre_cliente);
        localStorage.setItem("omt", response.omt);
        localStorage.setItem("datosOmtBen", JSON.stringify(response.datosOmt));
        localStorage.setItem("total_general", response.total);
        localStorage.setItem(
          "totalDtoCompetencia",
          response.totalDtoCompetencia
        ); //compe
        localStorage.setItem("promoCompetencia", response.dtoCompetencia); //compe
        localStorage.setItem("aplicaPromoCr", response.aplicaPromoCr); //conva
        localStorage.setItem(
          "productos_actuales",
          JSON.stringify(response.productos)
        );
        localStorage.setItem("id_socio", response.productosResumen[0].id);
        listarServiciosSocio(response.productos, response.productosResumen);
      }
      $("#modal-datos-productos").modal("show");
    },
  });
}

function listarBeneficiarios(nroServicio) {
  localStorage.nroServicio = nroServicio;
  $.ajax({
    type: "POST",
    url: "Ajax/obtenerBeneficiarios.php",
    data: { typeAdmin: localStorage.typeAdmin, cedula: localStorage.cedula },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        let formElements = ``;
        response.data.forEach(function (data) {
          const disabled =
            localStorage.typeAdmin == "comercial" ? "" : "disabled";
          formElements += `
            <div class="row mb-2 beneficiario">
              <div class="col-md-3">
                <label>Cédula</label>
              </div>
              <div class="col-md-3">
                <label>Nombre</label>
              </div>
              <div class="col-md-3">
                <label>Télefono</label>
              </div>
              <div class="col-md-3">
                <label>Fecha de nacimiento</label>
              </div>
              <div class="col-md-3">                
                <input disabled type="text" name="" class="cedula_ben form-control" placeholder="" value="${data[0]}"/>
              </div>
              <div class="col-md-3">
                <input ${disabled} type="text" name="" class="nombre_ben form-control" placeholder="" value="${data[1]}"/>
              </div>
              <div class="col-md-3">
                <input ${disabled} type="text" name="" class="telefono_ben form-control" placeholder="Télefono" value="${data[2]}"/>
              </div>
              <div class="col-md-3">
                <input ${disabled} type="datetime" name="" class="form-control fn_beneficiario fechan_ben" placeholder="Fecha de nacimiento" value="${data[3]}"/>
              </div>
            </div>
          `;
        });

        $("#modalBeneficiarios .modal-body").html(`
            <form>
              <div class="alert alert-danger alert-dismissable" id="error-beneficiarios">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Error: </strong><span class="error-beneficiarios"></span>
              </div>
              <div class="container">${formElements}</div>
            </form>
          `);

        $("#modalBeneficiarios").modal("show");

        $("#error-beneficiarios").hide();
        $(".fn_beneficiario").datetimepicker({
          format: "Y-m-d",
          timepicker: false,
        });
      }
    },
  });
}

$arrBeneficiarios = [];
function guardarBeneficiarios() {
  let error = false;
  let socio = null;
  let mensaje = "";
  $(".beneficiario").each(function (index) {
    let nombre_ben = $(".nombre_ben", this).val();
    let cedula_ben = $(".cedula_ben", this).val();
    let telefono_ben = $(".telefono_ben", this).val();
    let fechan_ben = $(".fechan_ben", this).val();
    let edad = calcularEdad(fechan_ben);

    socio = existeEnPadron(cedula_ben);

    if (
      nombre_ben == "" ||
      cedula_ben == "" ||
      telefono_ben == "" ||
      fechan_ben == ""
    ) {
      error = true;
      mensaje =
        "Debe completar todos los datos del beneficiario con la cedula " +
        cedula_ben;
    } else if (!comprobarCI(cedula_ben)) {
      error = true;
      mensaje = "La cédula " + cedula_ben + " es incorrecta";
    } else if (edad < 18) {
      error = true;
      mensaje =
        "El beneficiario con la cédula " + cedula_ben + " es menor de edad";
    } else if (socio[0]) {
      error = true;
      mensaje = socio[1];
    } else {
      $arrBeneficiarios[index] = [
        nombre_ben,
        cedula_ben,
        telefono_ben,
        fechan_ben,
        edad,
      ];
    }
  });

  const valEdades = validarEdades(localStorage.nroServicio);

  if (valEdades[0]) {
    $(".error-beneficiarios").text(valEdades[1]);
    $("#error-beneficiarios").show().fadeOut(10000);
    $arrBeneficiarios.length = 0;
  } else if (localStorage.nroServicio == "65" && $arrBeneficiarios.length < 4) {
    $(".error-beneficiarios").text(
      "Debe ingresar 5 beneficiarios para éste servicio"
    );
    $("#error-beneficiarios").show().fadeOut(10000);
    $arrBeneficiarios.length = 0;
  } else if (error) {
    $(".error-beneficiarios").text(mensaje);
    $("#error-beneficiarios").show().fadeOut(10000);
    $arrBeneficiarios.length = 0;
  } else {
    const beneficiarios = JSON.stringify($arrBeneficiarios);
    $.ajax({
      type: "POST",
      url: "Ajax/actualizarBeneficiarios.php",
      data: {
        typeAdmin: localStorage.typeAdmin,
        beneficiarios: beneficiarios,
      },
      dataType: "json",
      success: function (response) {
        console.log(response);
        if (response.result) {
          $("#modalBeneficiarios").modal("hide");
        } else {
          $(".error-beneficiarios").text(response.message);
          $("#error-beneficiarios").show().fadeOut(10000);
        }
      },
    });
  }
}

function buscarInfoSocio() {
  let cedulaSocio = $("#cedula_socio").val();
  if (cedulaSocio == "") {
    swal({
      title: "Error",
      text: "Debe ingresar una cédula",
      icon: "error",
    });
  } else if (!comprobarCI(cedulaSocio)) {
    swal({
      title: "Error",
      text: "La cédula no es válida",
      icon: "error",
    });
  } else {
    $.ajax({
      type: "POST",
      url: "Ajax/buscarInfoSocio.php",
      data: { typeAdmin: localStorage.typeAdmin, cedula: cedulaSocio },
      dataType: "json",
      success: function (response) {
        let datos = response.datos;
        if (response.result) {
          $("#abm_cedula").val(datos.cedula);
          $("#abm_idsocio").val(datos.id_socio);
          $("#abm_nombre").val(datos.nombre);
          $("#abm_direccion").val(datos.direccion);
          $("#abm_tel").val(datos.tel);
          $("#abm_suc").val(datos.sucursal);
          $("#abm_fechan").val(datos.fecha_nacimiento);
          $("#abm_radio").val(datos.radio);
          $("#abm_ruta").val(datos.ruta);
          $("#abm_numtar").val(datos.numero_tarjeta);
          $("#abm_nomtit").val(datos.nombre_titular);
          $("#abm_cedtit").val(datos.cedula_titular);
          $("#abm_movimiento").val(datos.movimientoabm);
          $("#abm_fechafil").val(datos.fechafil);
          $("#abm_observaciones").val(datos.observaciones);
          $("#abm_totalimporte").val(datos.total_importe);
          $("#btnEditarDatos").removeAttr("disabled");
          var table = $("#table-servicios").DataTable();
          table.destroy();
          buscarServiciosSocio(cedulaSocio);
        } else {
          swal({
            title: "Error",
            text: response.message,
            icon: "error",
          });
        }
      },
    });
  }
}

function buscarInfoSocioPiscina() {
  //cpiscina
  let cedulaSocio = $("#cedula_sociop").val();

  if (cedulaSocio == "") {
    swal({
      title: "Error",
      text: "Debe ingresar una cédula",
      icon: "error",
    });
  } else if (!comprobarCI(cedulaSocio)) {
    swal({
      title: "Error",
      text: "La cédula no es válida",
      icon: "error",
    });
  } else {
    $.ajax({
      type: "POST",
      url: "Ajax/buscarInfoSocioPiscina.php",
      data: { typeAdmin: localStorage.typeAdmin, cedula: cedulaSocio },
      dataType: "json",
      success: function (response) {
        let datos = response.datos;
        if (response.result) {
          $("#abmp_cedula").val(datos.cedula);
          $("#abmp_idsocio").val(datos.id);
          $("#abmp_metodo_pago").val(datos.metodo_pago); //cambio
          $("#abmp_localidad").val(datos.localidad); //cambio
          $("#abmp_nombre").val(datos.nombre);
          $("#abmp_direccion").val(datos.direccion);
          $("#abmp_tel").val(datos.tel);
          $("#abmp_suc").val(datos.sucursal);
          $("#abmp_fechan").val(datos.fecha_nacimiento);
          $("#abmp_radio").val(datos.radio);
          $("#abmp_ruta").val(datos.ruta);
          $("#abmp_numtar").val(datos.numero_tarjeta);
          $("#abmp_nomtit").val(datos.nombre_titular);
          $("#abmp_cedtit").val(datos.cedula_titular);
          $("#abmp_movimiento").val(datos.movimientoabm);
          $("#abmp_fechafil").val(datos.fechafil);
          $("#abmp_observaciones").val(datos.observaciones);
          $("#abmp_totalimporte").val(datos.total_importe);
          $("#btnEditarDatosPiscina").removeAttr("disabled");
          obtenerEstados(datos.esCompetencia);
          obtenerOrigenesVenta();
          $(`#abmp_origenventa option[value=${datos.origen_venta}]`).prop(
            "selected",
            true
          );
          $(`#abmp_estado option[value='${datos.estado}']`).prop(
            "selected",
            true
          );

          buscarServiciosSocioPiscina(cedulaSocio);
        } else {
          swal({
            title: "Error",
            text: response.message,
            icon: "error",
          });
        }
      },
    });
  }
}

function buscarServiciosSocio(cedula) {
  if (!$.fn.DataTable.isDataTable("#table-servicios")) {
    $table = $("#table-servicios").DataTable({
      ajax: {
        url: "Ajax/buscarServiciosSocio.php",
        data: {
          username: localStorage.usuario,
          typeAdmin: localStorage.typeAdmin,
          cedula: cedula,
        },
        method: "POST",
      },
      columnDefs: [
        {
          targets: [11, 12],
          visible: false,
          searchable: false,
        },
      ],
      lengthMenu: [20],
      searching: true,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      oLanguage: {
        sUrl: "js/spanish.json",
      },
      responsive: true,
    });
  } else {
    $("#primaria").css("display", "flex");
    $table.ajax.reload(function () {
      $("#primaria").css("display", "none");
    }, false);
  }
  $("#nav").removeClass("show");
  $("#dashboard").hide();
  $("#history").hide();
  $("#afiliaciones").hide();
}

function buscarServiciosSocioPiscina(cedula) {
  //cpiscina
  if (!$.fn.DataTable.isDataTable("#table-servicios-piscina")) {
    $table = $("#table-servicios-piscina").DataTable({
      ajax: {
        url: "Ajax/buscarServiciosSocioPiscina.php",
        data: {
          username: localStorage.usuario,
          typeAdmin: localStorage.typeAdmin,
          cedula: cedula,
        },
        method: "POST",
      },
      columnDefs: [
        {
          targets: [11, 12],
          visible: false,
          searchable: false,
        },
      ],
      lengthMenu: [20],
      searching: true,
      paging: true,
      lengthChange: false,
      ordering: true,
      info: true,
      oLanguage: {
        sUrl: "js/spanish.json",
      },
      responsive: true,
    });
  } else {
    $("#primaria").css("display", "flex");
    $table.ajax.reload(function () {
      $("#primaria").css("display", "none");
    }, false);
  }
  $("#nav").removeClass("show");
  $("#dashboard").hide();
  $("#history").hide();
  $("#afiliaciones").hide();
  $("#control_abm").hide();
}

$("#table-servicios tbody ").on("click", "tr", function () {
  $array = $table.row(this).data();
  $("#e_servicio").val($array[0]);
  $("#e_horas").val($array[1]);
  $("#e_importe").val($array[2]);
  $("#e_codpromo").val($array[3]);
  $("#e_fechafil").val($array[4]);
  $("#e_fechareg").val($array[5]);
  $("#e_abm").val($array[6]);
  $("#e_observacion").val($array[7]);
  $("#e_numven").val($array[8]);
  $("#abm_idservicio").val($array[11]);
  $("#e_keepprice").val($array[12]);
});

$("#table-servicios-piscina tbody ").on("click", "tr", function () {
  //cpiscina
  $array = $table.row(this).data();
  $("#ep_servicio").val($array[0]);
  $("#ep_horas").val($array[1]);
  $("#ep_importe").val($array[2]);
  $("#ep_codpromo").val($array[3]);
  $("#ep_fechafil").val($array[4]);
  $("#ep_fechareg").val($array[5]);
  $("#ep_abm").val($array[6]);
  $("#ep_observacion").val($array[7]);
  $("#ep_numven").val($array[8]);
  $("#abmp_idservicio").val($array[11]);
  $("#ep_keepprice").val($array[12]);
});

function verEditarServicios(id) {
  $("#modal_editar_datos_socio").modal("show");
}

function verEditarServiciosPiscina(id) {
  //cpiscina
  $("#modal_editar_datos_socio_piscina").modal("show");
}

function eliminarSocioPadron() {
  let borrarci = $("#cedula_eliminar").val();
  if (borrarci == "") {
    swal({
      title: "Error",
      text: "Debe ingresar una cédula",
      icon: "error",
    });
  } else if (!comprobarCI(borrarci)) {
    swal({
      title: "Error",
      text: "La cédula es inválida",
      icon: "error",
    });
  } else {
    swal({
      title: "¿Estás seguro que deseas eliminar éste socio?",
      text: "Estos datos se eliminarán permanentemente del padrón",
      icon: "warning",
      buttons: ["Cancelar", "Sí"],
      dangerMode: false,
    }).then(function (ok) {
      if (ok) {
        $.ajax({
          type: "POST",
          url: "Ajax/eliminarCedulaPadron.php",
          data: { typeAdmin: localStorage.typeAdmin, cedula: borrarci },
          dataType: "json",
          success: function (response) {
            if (response.result) {
              swal({
                title: "Éxito",
                text: response.message,
                icon: "success",
              });
              $("#divEliminar").hide();
              $("#cedula_eliminar").val("");
            } else {
              swal({
                title: "Éxito",
                text: response.message,
                icon: "success",
              });
            }
          },
        });
      }
    });
  }
}

function validarEdades(num_servicio) {
  //G5                          //G6
  //5 pers. entre 18 - 49       //5 pers. entre 18 - 49
  //2 pers. entre 50 - 65       //2 pers. entre 50 - 65
  //1 pers>65                   //2 pers>65

  let edades = $(".fechan_ben")
    .map(function () {
      return calcularEdad($(this).val());
    })
    .get();

  let franja1 = 0;
  let franja2 = 0;
  let franja3 = 0;
  let error = false;
  let message = "";

  edades.forEach(function (val) {
    if (val >= 18 && val < 49) {
      franja1++;
    } else if (val > 49 && val < 66) {
      franja2++;
    } else if (val > 65) {
      franja3++;
    }
  });

  if (num_servicio == "25" && franja3 > 1) {
    message = "El grupo familiar G5 solo admite 1 persona mayor a 65 años";
    error = true;
  } else if (num_servicio == "25" && franja2 > 2) {
    message =
      "El grupo familiar G5 solo admite 2 personas con edades entre 50 y 65 años";
    error = true;
  } else if (num_servicio == "27" && franja2 > 2) {
    message =
      "El grupo familiar G6 solo admite 2 personas con edades entre 50 y 65 años";
    error = true;
  } else if (num_servicio == "27" && franja3 > 2) {
    message = "El grupo familiar G6 solo admite 2 personas mayor a 65 años";
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
    },
  });

  return retorno;
}

function generarFormBeneficiarios(cantBen = 1, numServicio = null) {
  //newform
  let row = ``;
  let arrBeneficiarios = buscarBeneficiariosServicio(localStorage.cedula);
  // console.log(arrBeneficiarios);return false;

  for (let i = 0; i < cantBen; i++) {
    let id = "";
    let nombre = "";
    let cedula = "";
    let telefono = "";
    let fechaNacimiento = "";
    if (arrBeneficiarios.length > 0 && arrBeneficiarios[i]) {
      id = arrBeneficiarios[i].id;
      nombre = arrBeneficiarios[i].nombre;
      cedula = arrBeneficiarios[i].cedula;
      telefono = arrBeneficiarios[i].telefono;
      fechaNacimiento = arrBeneficiarios[i].fechaNacimiento;
    }
    row += `<div class="row beneficiario" id="beneficiario${i + 1}">
            <div class="col-md-4">
              <div class="form-group">
                <label for="">Nombre</label>
                <input type="text" class="form-control nombre_ben solo_letras" value="${
                  arrBeneficiarios[i] ? nombre : ""
                }" name="" id="">
                <input type="hidden" class="form-control id_ben solo_letras" value="${
                  arrBeneficiarios[i] ? id : ""
                }" name="" id="">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="">Cédula</label>
                <input type="text" class="form-control cedula_ben solo_numeros" value="${
                  arrBeneficiarios[i] ? cedula : ""
                }" name="" id="">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="">Teléfono</label>
                <input type="text" class="form-control telefono_ben solo_numeros" value="${
                  arrBeneficiarios[i] ? telefono : ""
                }" name="" id="">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label for="">Fecha de nacimiento</label>
                <input type="text" class="form-control fn_beneficiario fechan_ben" value="${
                  arrBeneficiarios[i] ? fechaNacimiento : ""
                }" name="" id="">
              </div>
            </div>
          </div>`;
  }
  const modal = `
    <div class="modal" tabindex="-1" data-backdrop="static" role="dialog" id="modal-agregar-beneficiarios">
      <div class="modal-dialog modal-lg" style="width:80%;">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Agregar beneficiarios</h2>
          </div>
          <div class="modal-body" id="datos_beneficiarios">
            <div class="alert alert-danger alert-dismissable" id="error-beneficiarios"  style="display:none;">
              <strong>Error: </strong><span class="error-beneficiarios"></span>
            </div>
            <form action="" id="beneficiarios_form">
            ${row}
            <input type="hidden" class="form-control" value="${localStorage.cedula}" name="" id="cedulaSocioTitular">
            <input type="hidden" class="form-control" value="${numServicio}" name="" id="idServicioBen">
            </form>
          </div>
          <div class="modal-footer">
            <button id="btnCancelarBeneficiarios" type="button" class="btn btn-secondary mainbutton" data-dismiss="modal">Cancelar</button>
            <button id="btnGuardarBeneficiarios" type="button" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
  </div>`;

  $("#modal-agregar-beneficiarios").remove();

  $("body").append(modal);
  $(".fn_beneficiario").datetimepicker({
    format: "Y-m-d",
    timepicker: false,
  });
  $("#btnGuardarBeneficiarios").click(function () {
    guardarBeneficiariosServicios(cantBen);
  }); //newform
  $("#modal-agregar-beneficiarios").modal("show");
}

function guardarBeneficiariosServicios(cantBen) {
  //newform

  let arrBeneficiarios = [];
  let edades = $(".fechan_ben")
    .map(function () {
      let age = $(this).val();
      if (age != "") {
        age = calcularEdad(age);
        return age;
      }
    })
    .get();

  let hayMenores = edades.some(function (val) {
    return val < 18;
  });

  if (edades.length != cantBen) {
    mostrarMensaje(
      "error-beneficiarios",
      "Debe llenar todos los campos con los datos de los beneficiarios"
    );
    return false;
  } else if (hayMenores) {
    mostrarMensaje(
      "error-beneficiarios",
      "No pueden haber menores de edad entre los beneficiaros"
    );
    return false;
  } else {
    let mensaje = "";
    let cedulas = $(".cedula_ben")
      .map(function () {
        let ced = $(this).val();
        return ced;
      })
      .get();
    let cedulaTitular = $("#cedulaSocioTitular").val();
    let numServi = $("#idServicioBen").val();
    $(".beneficiario").each(function (index) {
      let nombre_ben = $(".nombre_ben", this).val().toUpperCase();
      let cedula_ben = $(".cedula_ben", this).val();
      let telefono_ben = $(".telefono_ben", this).val();
      let fechan_ben = $(".fechan_ben", this).val();
      let id_ben = $(".id_ben", this).val();
      let edad = calcularEdad(fechan_ben);
      let indice = cedulas.indexOf(cedula_ben);
      let socio = existeEnPadron(cedula_ben);
      cedulas.splice(indice, 1);

      if (
        nombre_ben == "" ||
        cedula_ben == "" ||
        telefono_ben == "" ||
        fechan_ben == ""
      ) {
        mostrarMensaje(
          "error-beneficiarios",
          "Debe completar todos los datos del beneficiario con la cedula " +
            cedula_ben
        );
        return false;
      } else if (!comprobarCI(cedula_ben)) {
        mostrarMensaje(
          "error-beneficiarios",
          "La cédula " + cedula_ben + " es incorrecta"
        );
        return false;
      } else if (edad < 18) {
        mostrarMensaje(
          "error-beneficiarios",
          "El beneficiario con la cédula " + cedula_ben + " es menor de edad"
        );
        return false;
      } else if (socio[0]) {
        mostrarMensaje("error-beneficiarios", socio[1]);
        return false;
      } else if (cedula_ben == localStorage.cedula) {
        mostrarMensaje(
          "error-beneficiarios",
          "La cédula " + cedula_ben + " es igual a la del titular del grupo"
        );
        return false;
      } else if (cedulas.includes(cedula_ben)) {
        mostrarMensaje(
          "error-beneficiarios",
          "La cédula " + cedula_ben + " está repetida"
        );
        return false;
      } else {
        arrBeneficiarios[index] = [
          nombre_ben,
          cedula_ben,
          telefono_ben,
          fechan_ben,
          edad,
          id_ben,
        ];
      }
    });

    // console.log(arrBeneficiarios); return false;
    if (arrBeneficiarios.length > 0) {
      $.ajax({
        type: "POST",
        url: "Ajax/guardarBeneficiariosServicio.php",
        data: {
          typeAdmin: localStorage.typeAdmin,
          beneficiarios: JSON.stringify(arrBeneficiarios),
          cedulaTitular,
          numServi,
        },
        dataType: "json",
        success: function (response) {
          $("#modal-agregar-beneficiarios").modal("hide");
        },
      });
    }
  }
}

function mostrarMensaje(div = "", mensaje = "") {
  //newform
  $(`#${div}`).text(`${mensaje}`).show().fadeOut(7000);
}

function buscarBeneficiariosServicio(cedulaSocio) {
  //newform
  let arrBeneficiarios = [];
  $.ajax({
    type: "POST",
    url: "Ajax/buscarBeneficiariosServicio.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      cedulaSocio,
    },
    dataType: "json",
    async: false,
    success: function (response) {
      if (response.result) {
        arrBeneficiarios = response.beneficiarios;
      }
    },
  });

  return arrBeneficiarios;
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

function abrirRegistrarVidaApp(ci) {
  $.ajax({
    type: "POST",
    url: "ajax/registroVidaApp.php",
    data: { cedula: ci },
    dataType: "dataType",
    success: function (response) {},
  });
}

/**
 * obtenerEstados
 * Llena los select de estado
 * @param void
 * @return {void}
 */
function obtenerEstados(esCompetencia) {
  //cpiscina

  $.ajax({
    type: "POST",
    url: "Ajax/obtenerEstados.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      esCompetencia,
    },
    dataType: "json",
    async: false,
    success: function (response) {
      if (response.result) {
        var status = response.estados;
        $("#abmp_estado").empty();
        status.forEach(function (val, i) {
          $("#abmp_estado").append(
            `<option value="` + val.id + `">` + val.estado + `</option>`
          );
        });
      }
    },
  });
}

/**
 * obtenerOrigenes
 * Llena el select de origen venta
 * @param void
 * @return {void}
 */
function obtenerOrigenesVenta() {
  //cpiscina

  $.ajax({
    type: "POST",
    url: "Ajax/obtenerOrigenesVenta.php",
    data: { typeAdmin: localStorage.typeAdmin },
    dataType: "json",
    async: false,
    success: function (response) {
      if (response.result) {
        var origenes = response.origenes;
        $("#abmp_origenventa").empty();
        origenes.forEach(function (val, i) {
          $("#abmp_origenventa").append(
            `<option value="` + val.cod + `">` + val.origen + `</option>`
          );
        });
      }
    },
  });
}

//LISTA LOS SERVICIOS DEL SOCIO, ARMA LOS SELECT
function listarServiciosSocio(productosSocio, productosResumen) {
  $pro = 1;
  let template = "";
  let total = 0;

  let productosActuales = "";
  let productosNuevos = "";
  let existepromo = false;
  let datosOmt = JSON.parse(localStorage.datosOmtBen);

  productosResumen.forEach(function (producto) {
    if (producto.accion == "1") {
      // GRUPO FAMILIAR
      let btnBeneficiarios = "";
      if (producto.nro_servicio == 63 || producto.nro_servicio == 65) {
        btnBeneficiarios = `<button class="btn btn-success" onclick="listarBeneficiarios(${producto.nro_servicio})">Ver benificiarios</button>`;
      }
      let hayUdemmGrupo =
        producto.nro_servicio == 87 || producto.nro_servicio == 88
          ? true
          : false;
      const btnAgregarBeneficiarios = hayUdemmGrupo
        ? `   <button class="btn btn-success" onclick="generarFormBeneficiarios(${
            producto.nro_servicio == 87 ? 1 : 3
          },${producto.nro_servicio})">Agregar benificiarios</button>`
        : ``; //newform

      productosNuevos += `
        <div>
        <p class="font-weight-bolder">${
          !existepromo
            ? producto.dato_extra == "3"
              ? ""
              : producto.dato_extra == "1"
              ? "(Competencia)"
              : producto.dato_extra == "4"
              ? "(PROMO % OFF)"
              : "(Herencia)"
            : ""
        }</p>
          <p>${producto.nombre_servicio}
          ${
            producto.nro_servicio == "01" &&
            (producto.promo == "20" || producto.promo == "2035")
              ? producto.promo == "2035"
                ? "promo: NP17+Competencia"
                : "promo: NP17"
              : producto.promo == "35" || producto.promo == "3335"
              ? "promo: competencia"
              : producto.nro_servicio != "01"
              ? ""
              : "promo: ninguna"
          }  
          (${producto.horas}hs) - $UY ${
        producto.promo == "35" ||
        producto.promo == "2035" ||
        producto.promo == "3335"
          ? producto.importe +
            ' - con  dcto al 10% del total -> <strong class="text-bold">$UY ' +
            Math.ceil(producto.importe * 0.1) +
            "</strong>"
          : producto.importe
      } 
        ${btnBeneficiarios}
        ${btnAgregarBeneficiarios} 
        ${
          (producto.nro_servicio == "81" || producto.nro_servicio == "83") &&
          localStorage.typeAdmin == "bienvenida"
            ? `     <button id="btnRegistrar" title="Registrar" type="button" class="btn btn-success btn-sm" onclick="event.preventDefault(); abrirRegistrarVidaApp('${localStorage.cedula}','${localStorage.nombre_cliente}');">Registrar en vidaApp</button>`
            : ""
        }</p>
        </div>
      `;
      existepromo = true;
    } else {
      productosActuales += `
        <div>        
          <p>${producto.nombre_servicio} (${producto.horas}hs) - $UY ${producto.importe}</p>
        </div>
      `;
    }
  });

  let tipoAdmin = localStorage.typeAdmin;
  for (let i = 0; i < productosSocio.length; i++) {
    let divhorasServicio = "";
    let divPromo = "";
    let divImporte = "";
    let productos_acotados = [
      07, 10, 55, 58, 56, 59, 18, 19, 28, 53, 54, 67, 68, 69, 89, 12, 13, 14,
    ];
    let opcpromo = ``;
    let divEliminar = ``;
    total +=
      productosSocio[i].cod_promo == "35" ||
      productosSocio[i].cod_promo == "2035"
        ? +productosSocio[i].total_importe * 0.1
        : +productosSocio[i].total_importe; //compe

    if (!productos_acotados.includes(+productosSocio[i].nro_servicio)) {
      let horasServicio = "";
      if (
        productosSocio[i].alta == "1" ||
        productosSocio[i].total_horas == null
      ) {
        for (var x = 8; x <= 24; x += 8) {
          horasServicio += `
              <option value="${x}" ${
            x === +productosSocio[i].horas ? "selected" : ""
          }>${x}hs</option>
            `;
        }
      } else {
        let horas_actuales = Number(productosSocio[i].total_horas);
        let horas_contratadas = Number(productosSocio[i].horas);

        // let h = (horas_actuales>=horas_contratadas) ? 24-horas_actuales : 24-horas_contratadas;
        let h = 24 - horas_actuales;
        let y = h / 8;

        for (var x = 1; x <= y; x++) {
          horasServicio += `
              <option value="${h == 8 ? 8 : h - 8}" ${
            h === horas_contratadas ? "selected" : ""
          }>${h == 8 ? "24" : h}hs</option>
            `;
          h += 8;
        }

        // for (
        //   var x = bool ? total_horas : (total_horas == 8) ? 8 : total_horas - 8;
        //   x <= 24;
        //   x += 8
        // ) {
        //   console.log(x);
        //   horasServicio += `
        //       <option value="${bool ? x - 8 : (!bool) ? x - 8: x}" ${
        //     x === total_horas ? 'selected' : ''
        //   }>${x}hs</option>
        //     `;
        // }
      }

      if (productosSocio[i].cod_promo == "20") {
        //conva
        opcpromo += `<option value="0">Seleccione</option>
                    <option value="20" selected>NP17</option>
                    <option value="2035">NP17+Competencia</option>
                    <option value="35">Competencia</option>`;
      } else if (productosSocio[i].cod_promo == "2035") {
        opcpromo += `<option value="0">Seleccione</option>
                    <option value="20" >NP17</option>
                    <option value="2035" selected>NP17+Competencia</option>
                    <option value="35">Competencia</option>`;
      } else if (productosSocio[i].cod_promo == "35") {
        opcpromo += `<option value="0">Seleccione</option>
                    <option value="20" >NP17</option>
                    <option value="2035">NP17+Competencia</option>
                    <option value="35" selected>Competencia</option>`;
      } else {
        opcpromo += `
          <option value="0" selected>Seleccione</option>
          <option value="20">NP17</option>
          <option value="2035">NP17+Competencia</option>
          <option value="35">Competencia</option>
        `;
      }

      if (productosSocio[i].nro_servicio == "01") {
        divPromo += `<div class="col-md-2">
          <div class="form-group">
            <div id="divPromoSocio" class="divPromoSocio">
              <label for="" class="texto" >Promo</label>
              <select id="promoSocio" name="promoSocio" class="custom-select form-control promo " ${
                tipoAdmin == "bienvenida" ? "disabled " : ""
              }>
                  ${opcpromo}
              </select>
              <span class="text-muted">Esta promoción sólo es válida para pago con tarjeta</span>
            </div>
          </div>   
        </div>`;
      }

      divhorasServicio = `<div class="col-md-2">
                            <div class="form-group">
                              <div id="divHorasServicio" class="divHorasServicioSocio">
                                <label for="" class="texto" >Horas</label>
                                  <select id="hrservicio" name="hrservicio" class="custom-select form-control hservicio " ${
                                    tipoAdmin == "bienvenida" ? "disabled" : ""
                                  }>
                                      ${horasServicio}
                                  </select>
                              </div>
                            </div>
                          </div>
                          ${divPromo}
                          `;
    }

    divImporte += ` <div class="col-md-3">
                         <div class="form-group">
                            <div id="divImporte" class="divImporte">
                             <label for="" class="texto" >${
                               productosSocio[i].alta == "0"
                                 ? "Importe incremento"
                                 : "Importe"
                             }</label>
                             <input type="text" class="form-control importe" id="importe" name="importe" disabled value="${
                               productosSocio[i].total_importe
                             }">
  
                            </div>
                          </div>   
                      </div> `;

    if (i != 0 && tipoAdmin == "comercial") {
      divEliminar = ` <div class="col-md-1">
                      <div class="form-group">
                        <label>Acción</label>
                        <button id="btnEliminar" title="Eliminar" type="button" class="btn btn-danger" onclick="event.preventDefault(); EliminarServicio(${productosSocio[i].id})">ELIMINAR</button>
                      </div>   
                    </div> `;
    }

    template +=
      `<div class='row productos_socio' id="productos_socio` +
      $pro +
      `">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="" class="texto" >Producto </label>
                          <input type="text" class="form-control" id="nombre_servicio" name="nombre_servicio" disabled value="${productosSocio[i].nombre_servicio}">
                          <input type="hidden" class="id_padron_producto" name="id_padron_producto" id="id_padron_producto" value="${productosSocio[i].id}">
                          <input type="hidden" class="nro_servicio" name="nro_servicio" id="nro_servicio" value="${productosSocio[i].nro_servicio}">
                          <input type="hidden" class="id_servicio" name="id_servicio" id="id_servicio" value="${productosSocio[i].id_servicio}">
                          <input type="hidden" class="alta" name="alta" id="alta" value="${productosSocio[i].alta}">
                      </div>    
                    </div>
                    ${divhorasServicio}
                    ${divImporte}
                    ${divEliminar}
                  </div>`;
    $pro++;
  }

  if (tipoAdmin == "bienvenida" || tipoAdmin == "morosidad") {
    $("#divComentario").hide();
    $("#btnActualizarProductos").hide();
    $("#datos_productos").hide();
  }

  let divOmt = ``;
  if (localStorage.omt == "true") {
    $omt = true;
    divOmt = `
    <div>
      <h5 style="color: #2e7d32;">Persona afiliada OMT</h5>
      <p><span class='text-muted'>Nombre: </span>
        ${datosOmt.nombre_omtben}
      </p>
      <p><span class='text-muted'>Cédula: </span>
      ${datosOmt.cedula_omtben}
      </p>
      <p><span class='text-muted'>Teléfono: </span>
      ${datosOmt.telefono_omtben}
      </p>
      <p><span class='text-muted'>servicio OMT (8 hrs): </span>  $U ${datosOmt.importeomt}</p>
    <div> `;
  } else {
    $omt = false;
  }

  if (productosActuales) {
    productosActuales = `
      <div>
        <h5 style="color: #2e7d32;">Servicios actuales</h5>
        <div>
          ${productosActuales}  
        </div>
      </div>
    `;
  }

  $("#resumen-productos").html(`
      <hr>
      ${productosActuales}
        <div>
          <h5  style="color: #0062cc;">Nuevos servicios</h5>
          ${
            localStorage.aplicaPromoCr == "true"
              ? `<div class="alert alert-info" role="alert">
          A éste socio se le otorgó 8 hrs de convalecencia de regalo
          </div>`
              : ``
          } 
        <div>
          ${productosNuevos}  
        </div>
      </div>
      ${divOmt} 
      <hr>
    `);
  $("#datos_productos").html(`${template}`);
  $("#total").text(
    `${
      localStorage.promoCompetencia == "true"
        ? localStorage.total_general +
          " - Dcto Competencia -> $UY " +
          localStorage.totalDtoCompetencia
        : localStorage.total_general
    }`
  ); //compe
}

//ELIMINA UN SERVICIO, RECIBE EL ID DEL SERVICIO A ELIMINAR
function EliminarServicio(idServicio) {
  i = 0;
  arrIdProductos = [];
  $(".productos_socio").each(function (index, element) {
    idProducto = $(this).find(".id_servicio").val();
    arrIdProductos[i++] = idProducto;
  });

  if (arrIdProductos.length > 1) {
    swal({
      title: "¿Estás seguro que deseas eliminar éste servicio?",
      icon: "warning",
      buttons: ["Cancelar", "Sí"],
      dangerMode: false,
    }).then(function (ok) {
      if (ok) {
        $.ajax({
          url: "Ajax/EliminarServicio.php",
          data: {
            id: idServicio,
          },
          dataType: "json",
          method: "POST",
          success: function (response) {
            if (response.result) {
              verProductosSocio(localStorage.cedula);
              localStorage.removeItem("cedula");
              dashboard();
              swal({
                title: "Operación realizada con exito!",
                icon: "success",
              });
            } else {
              swal("Lo sentimos, ocurrio un error intenta más tarde.");
            }
          },
          error: function (error) {
            // console.log(error);
          },
        });
      }
    });
  } else {
    $(".error-productos").text(
      "No se pueden eliminar todos los servicios del cliente"
    );
    $("#error-productos").show().fadeOut(5000);
  }
}

function ActualizarProductos() {
  $("#loader").css("display", "flex");
  arrProductos = [];
  arrProductosActuales = JSON.parse(localStorage.productos_actuales);
  var c = 0;
  var precio = 0;
  var loc = localStorage.localidad;
  var fecha_nacimiento = localStorage.fecha_nacimiento;
  let cambios = [];
  promo = "0";
  var observacion = $("#comentario").val();
  var codpromo = "0";
  var cambiopromo = false;

  $(".productos_socio").each(function (index, element) {
    base = false;
    let hrSanatorio = null;

    //capturamos el id del producto
    var idProducto = $("#id_servicio", this).val();

    //capturamos las horas servicio contratado
    var hrServicio = $("#hrservicio", this).val();
    var horass = hrServicio;

    //capturamos el nombre del producto
    var nombreProducto = $("#nombre_servicio", this).text();

    //importe
    var importe = $("#importe", this).val();

    //nro de servicio
    var nro_servicio = $("#nro_servicio", this).val();

    var alta = $(".alta", this).val();

    //la accion (1 incremento, 2 decremento, 3 se matiene)
    let accion = null;
    let limit = 0;

    arrProductosActuales.forEach(function (element, index) {
      if (element.id_servicio == idProducto) {
        codpromo = element.cod_promo;
        if (+element.horas < +hrServicio) {
          accion = 1;
          limit = (+hrServicio - +element.horas) / 8;
        } else if (+element.horas > +hrServicio) {
          accion = 2;
          limit = (+element.horas - +hrServicio) / 8;
        } else {
          accion = 3;
        }
      }
    });

    cambios[c] = arrProductosActuales.some(function (prod) {
      hrServicioActuales = prod.total_horas;
      return prod.id_servicio == idProducto && prod.horas != hrServicio;
    });

    if (idProducto == "1") {
      hrSanatorio = hrServicio;
      hrServicio = null;
      base = alta == "1" ? true : false;
      promo = $("#promoSocio").val();
    } else if (idProducto == "2") {
      base = alta == "1" ? true : false;
    }

    //devuelve precio base [0], precio servicio[1], total con base[2]
    precios = calcularPrecio(
      idProducto,
      hrSanatorio,
      hrServicio,
      loc,
      fecha_nacimiento,
      base
    );

    arrProductos[c] = [
      idProducto,
      hrServicio,
      nro_servicio,
      precios[0],
      precios[1],
      precios[2],
      promo,
      observacion,
      accion,
      limit,
    ];
    c++;

    if (idProducto == "1" && promo != codpromo) {
      cambiopromo = true;
    }
  });

  let hayCambio = cambios.some(function (element) {
    return element == true;
  });

  if (!hayCambio && !cambiopromo) {
    $("#loader").css("display", "none");
    $(".error-productos").text("No se han hecho cambios");
    $("#error-productos").show().fadeOut(5000);
  } else if (observacion == "") {
    $("#loader").css("display", "none");
    $(".error-productos").text("Debe dejar una observación");
    $("#error-productos").show().fadeOut(5000);
  } else {
    $.ajax({
      method: "POST",
      url: "Ajax/actualizarServicios.php",
      data: {
        servicios: arrProductos,
        cedula: localStorage.getItem("cedula"),
        typeAdmin: localStorage.typeAdmin,
        observacion: observacion,
        idUser: localStorage.idUser,
        id_socio: localStorage.id_socio,
      },
      dataType: "json",
      success: function (response) {
        if (response.result) {
          $("#loader").css("display", "none");
          dashboard();
          verProductosSocio(localStorage.cedula);
          swal({
            title: "Éxito!",
            text: "Datos actualizados correctamente",
            icon: "success",
            buttons: "Ok",
          });
        }
      },
    });
  }
}

function exportarDatos(identificador) {
  let url = "";
  if (identificador === 1) {
    url = "Ajax/exportarPadronDatos.php";
  } else if (identificador === 2) {
    url = "Ajax/exportarPadronProductos.php";
  }

  window.location = url;
}

function limpiarDatosAbm() {
  $("#cedula_socio").val("");
  $("#abm_cedula").val("");
  $("#abm_nombre").val("");
  $("#abm_direccion").val("");
  $("#abm_tel").val("");
  $("#abm_suc").val("");
  $("#abm_radio").val("");
  $("#abm_ruta").val("");
  $("#abm_numtar").val("");
  $("#abm_nomtit").val("");
  $("#abm_cedtit").val("");
  $("#abm_observaciones").val("");
  $("#btnEditarDatos").prop("disabled", true);
  $("#btnLimpiarDatosAbm").hide();
  $("#btnActualizarDatosSocio").hide();

  var table = $("#table-servicios").DataTable();
  table.clear().draw();
}

function limpiarDatosAbmPiscina() {
  //cpiscina
  $("#cedula_sociop").val("");
  $("#abmp_cedula").val("");
  $("#abmp_nombre").val("");
  $("#abmp_direccion").val("");
  $("#abmp_tel").val("");
  $("#abmp_suc").val("");
  $("#abmp_radio").val("");
  $("#abmp_ruta").val("");
  $("#abmp_numtar").val("");
  $("#abmp_nomtit").val("");
  $("#abmp_cedtit").val("");
  $("#abmp_observaciones").val("");
  $("#btnEditarDatosPiscina").prop("disabled", true);
  $("#btnLimpiarDatosAbmPiscina").hide();
  $("#btnActualizarDatosSocioPiscina").hide();

  var table = $("#table-servicios-piscina").DataTable();
  table.clear().draw();
}

function actualizarDatosSocio() {
  let cedula = $("#abm_cedula").val();
  let id_socio = $("#abm_idsocio").val();
  let nombre = $("#abm_nombre").val();
  let direccion = $("#abm_direccion").val();
  let telefono = $("#abm_tel").val();
  let sucursal = $("#abm_suc").val();
  let fecha_nacimiento = $("#abm_fechan").val();
  let radio = $("#abm_radio").val();
  let ruta = $("#abm_ruta").val();
  let numtar = $("#abm_numtar").val();
  let nomtit = $("#abm_nomtit").val();
  let cedtit = $("#abm_cedtit").val();
  let fechafil = $("#abm_fechafil").val();
  let observaciones = $("#abm_observaciones").val();

  $.ajax({
    type: "POST",
    url: "Ajax/actualizarDatosSocio.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      id_socio: id_socio,
      cedula: cedula,
      nombre: nombre,
      direccion: direccion,
      telefono: telefono,
      sucursal: sucursal,
      radio: radio,
      ruta: ruta,
      fecha_nacimiento: fecha_nacimiento,
      numtar: numtar,
      nomtit: nomtit,
      cedtit: cedtit,
      fechafil: fechafil,
      observaciones: observaciones,
    },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        swal({
          title: "Éxito",
          text: response.message,
          icon: "success",
        });
        buscarInfoSocio();
        $("#btnEditarDatos").click();
      } else {
        swal({
          title: "Error",
          text: response.message,
          icon: "error",
        });
      }
    },
  });
}

function actualizarDatosSocioPiscina() {
  //cpiscina
  let cedula = $("#abmp_cedula").val();
  let idSocio = $("#abmp_idsocio").val();
  let nombre = $("#abmp_nombre").val();
  let direccion = $("#abmp_direccion").val();
  let telefono = $("#abmp_tel").val();
  let sucursal = $("#abmp_suc").val();
  let fecha_nacimiento = $("#abmp_fechan").val();
  let radio = $("#abmp_radio").val();
  let ruta = $("#abmp_ruta").val();
  let numtar = $("#abmp_numtar").val();
  let nomtit = $("#abmp_nomtit").val();
  let cedtit = $("#abmp_cedtit").val();
  let fechafil = $("#abmp_fechafil").val();
  let observaciones = $("#abmp_observaciones").val();
  let estado = $("#abmp_estado").val();
  let origenVenta = $("#abmp_origenventa").val();
  let metodoPago = $("#abmp_metodo_pago").val();
  let localidad = $("#abmp_localidad").val();

  $.ajax({
    type: "POST",
    url: "Ajax/actualizarDatosSocioPiscina.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      cedula: cedula,
      id_socio: idSocio,
      nombre: nombre,
      direccion: direccion,
      telefono: telefono,
      sucursal: sucursal,
      radio: radio,
      ruta: ruta,
      fecha_nacimiento: fecha_nacimiento,
      numtar: numtar,
      nomtit: nomtit,
      cedtit: cedtit,
      fechafil: fechafil,
      observaciones: observaciones,
      estado: estado,
      origenVenta: origenVenta,
      idUser: localStorage.idUser,
      metodoPago,
      localidad,
    },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        swal({
          title: "Éxito",
          text: response.message,
          icon: "success",
        });
        $("#cedula_sociop").val(cedula);
        buscarInfoSocioPiscina();
        $("#btnEditarDatosPiscina").click();
      } else {
        swal({
          title: "Error",
          text: response.message,
          icon: "error",
        });
      }
    },
  });
}

function actualizarServicioSocio() {
  let id = $("#abm_idservicio").val();
  let servicio = $("#e_servicio").val();
  let horas = $("#e_horas").val();
  let importe = $("#e_importe").val();
  let codpromo = $("#e_codpromo").val();
  let abm = $("#e_abm").val();
  let fechafil = $("#e_fechafil").val();
  let fechareg = $("#e_fechareg").val();
  let observacion = $("#e_observacion").val();
  let numero_vendedor = $("#e_numven").val();
  let cedula_socio = $("#cedula_socio").val(); //newhist
  let keepprice = $("#e_keepprice").val();

  $.ajax({
    type: "POST",
    url: "Ajax/actualizarServicioSocio.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      id: id,
      cedula: cedula_socio,
      servicio: servicio,
      horas: horas,
      importe: importe,
      codpromo: codpromo,
      abm: abm,
      fechafil: fechafil,
      fechareg: fechareg,
      observacion: observacion,
      numero_vendedor: numero_vendedor,
      keepprice: keepprice,
    },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        swal({
          title: "Éxito",
          text: response.message,
          icon: "success",
        });
        buscarInfoSocio();
      } else {
        swal({
          title: "Error",
          text: response.message,
          icon: "error",
        });
      }
    },
  });
}

function actualizarServicioSocioPiscina() {
  //cpiscina
  let id = $("#abmp_idservicio").val();
  let servicio = $("#ep_servicio").val();
  let horas = $("#ep_horas").val();
  let importe = $("#ep_importe").val();
  let codpromo = $("#ep_codpromo").val();
  let abm = $("#ep_abm").val();
  let fechafil = $("#ep_fechafil").val();
  let fechareg = $("#ep_fechareg").val();
  let observacion = $("#ep_observacion").val();
  let numero_vendedor = $("#ep_numven").val();
  let keepprice = $("#ep_keepprice").val();
  let cedula_socio = $("#cedula_sociop").val();

  $.ajax({
    type: "POST",
    url: "Ajax/actualizarServicioSocioPiscina.php",
    data: {
      typeAdmin: localStorage.typeAdmin,
      id: id,
      cedula: cedula_socio,
      servicio: servicio,
      horas: horas,
      importe: importe,
      codpromo: codpromo,
      abm: abm,
      fechafil: fechafil,
      fechareg: fechareg,
      observacion: observacion,
      numero_vendedor: numero_vendedor,
      keepprice: keepprice,
    },
    dataType: "json",
    success: function (response) {
      if (response.result) {
        swal({
          title: "Éxito",
          text: response.message,
          icon: "success",
        });
        buscarInfoSocioPiscina(cedula_socio);
      } else {
        swal({
          title: "Error",
          text: response.message,
          icon: "error",
        });
      }
    },
  });
}

function eliminarServicioSocio(id) {
  let cedula_socio = $("#cedula_socio").val();

  swal({
    title: "¿Estás seguro de realizar esta acción?",
    text: "Si elimina este registro no podrá recuperarlo",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      $.ajax({
        type: "POST",
        url: "Ajax/eliminarServicioSocio.php",
        data: { id: id },
        dataType: "json",
        success: function (response) {
          if (response.result) {
            swal({
              title: "Éxito",
              text: response.message,
              icon: "success",
            });
            buscarInfoSocio(cedula_socio);
          } else {
            swal({
              title: "Error",
              text: response.message,
              icon: "error",
            });
          }
        },
      });
    } else {
      swal("Acción cancelada");
    }
  });
}

function eliminarServicioSocioPiscina(id) {
  let cedula_socio = $("#cedula_sociop").val();
  swal({
    title: "¿Estás seguro de realizar esta acción?",
    text: "Si elimina este registro no podrá recuperarlo",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      $.ajax({
        type: "POST",
        url: "Ajax/eliminarServicioSocioPiscina.php",
        data: { id: id },
        dataType: "json",
        success: function (response) {
          if (response.result) {
            swal({
              title: "Éxito",
              text: response.message,
              icon: "success",
            });
            buscarInfoSocioPiscina(cedula_socio);
          } else {
            swal({
              title: "Error",
              text: response.message,
              icon: "error",
            });
          }
        },
      });
    } else {
      swal("Acción cancelada");
    }
  });
}

/**
 * soloNumeros
 * Permite el ingreso unicamente de números
 * @param e {Object}
 * @return {boolean|undefined}
 */
function soloNumeros(e) {
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
  if (
    (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
    (e.keyCode < 96 || e.keyCode > 105)
  ) {
    e.preventDefault();
  }
  if (e.altKey) {
    return false;
  }
}

/**
 * comprobarCI
 * Realiza la validación de una cédula de identidad, aplicando el algoritmo que calcula el digito verificador
 * @param cedi {number}
 * @return {boolean}
 */
function comprobarCI(cedi) {
  if (cedi.length >= 6) {
    //Inicializo los coefcientes en el orden correcto
    let arrCoefs = [2, 9, 8, 7, 6, 3, 4, 1];
    let suma = 0;
    //Para el caso en el que la CI tiene menos de 8 digitos
    //calculo cuantos coeficientes no voy a usar
    let difCoef = parseInt(arrCoefs.length - cedi.length);
    //let difCoef = parseInt(arrCoefs.length – ci.length);
    //recorro cada digito empezando por el de más a la derecha
    //o sea, el digito verificador, el que tiene indice mayor en el array
    for (let i = cedi.length - 1; i > -1; i--) {
      //for (let i = ci.length – 1; i > -1; i–) {
      //ooObtengo el digito correspondiente de la ci recibida
      let dig = cedi.substring(i, i + 1);
      //Lo tenía como caracter, lo transformo a int para poder operar
      let digInt = parseInt(dig);
      //Obtengo el coeficiente correspondiente al ésta posición del digito
      let coef = arrCoefs[i + difCoef];
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

function addEvent(el, eventName, handler) {
  if (el.addEventListener) {
    el.addEventListener(eventName, handler);
  } else {
    el.attachEvent("on" + eventName, function () {
      handler.call(el);
    });
  }
}

function validarTarjeta() {
  guessPaymentMethod();

  const cardnumber = $("#numero_tarjeta").val();
  const tipo_tarjeta = localStorage.getItem("tipo_tarjeta");
  const is_mercadopago = localStorage.getItem("mercadopago");
  let retorno = true;
  console.log(tipo_tarjeta);
  console.log(is_mercadopago);

  if (
    (tipo_tarjeta == "" || tipo_tarjeta == null) &&
    (is_mercadopago == "0" || is_mercadopago == null)
  ) {
    $.ajax({
      type: "POST",
      url: "Ajax/validarTarjetas.php",
      data: {
        numeroTarjeta: cardnumber,
        typeAdmin: localStorage.typeAdmin,
      },
      dataType: "json",
      async: false,
      success: function (response) {
        if (response.result) {
          console.log("exito");
          localStorage.setItem("mercadopago", "0");
          localStorage.setItem("tipo_tarjeta", response.tipo_tarjeta);
          $("#payment_method_id").val(response.tipo_tarjeta);
          $("#is_mercadopago").val("0");
          $("#tarjeta_vida").val("1");
          $("#tarjeta").val(response.tipo_tarjeta);
          $("#tipo_tarjeta").val(response.tipo_tarjeta);

          retorno = true;
        } else {
          console.log("fallo");
          retorno = false;
        }
      },
    });
  } else {
    localStorage.setItem("mercadopago", "1");
    localStorage.setItem("tipo_tarjeta", tipo_tarjeta);
    retorno = true;
  }

  return retorno;
}

//#################################### MERCADO PAGO
function guessPaymentMethod() {
  const cardnumber = document.getElementById("numero_tarjeta").value;

  if (cardnumber.length >= 6) {
    const bin = cardnumber.substring(0, 6);
    window.Mercadopago.getPaymentMethod(
      {
        bin: bin,
      },
      setPaymentMethod
    );
  }
  return true;
}

function setPaymentMethod(status, response) {
  if (status == 200) {
    const paymentMethodId = response[0].id;
    const element = document.getElementById("payment_method_id");
    element.value = paymentMethodId;
    $("#is_mercadopago").val("1");
    $("#tarjeta").val("").val(paymentMethodId);
    $("#tarjeta_vida").val("0");
    $("#tipo_tarjeta").val("").val(paymentMethodId);
    localStorage.setItem("mercadopago", "1");
    localStorage.setItem("tipo_tarjeta", paymentMethodId);
  } else {
    localStorage.setItem("mercadopago", "0");
    localStorage.setItem("tipo_tarjeta", "");
    console.log(`payment method info error: ${response}`);
  }
}

// doSubmit = false;

// function doPay(){
//     //event.preventDefault();
//     if(!doSubmit){
//         var $form = document.querySelector('#pay');

//         window.Mercadopago.createToken($form, sdkResponseHandler);

//         return false;
//     }
// };

// function sdkResponseHandler(status, response) {
//     if (status != 200 && status != 201) {
//         alert("verify filled data");
//     }else{
//         var form = document.querySelector('#pay');
//         var card = document.createElement('input');
//         card.setAttribute('name', 'token');
//         card.setAttribute('id', 'token');
//         card.setAttribute('type', 'hidden');
//         card.setAttribute('value', response.id);
//         form.appendChild(card);
//         //doSubmit=true;
//         //form.submit();
//     }
// };

// Ressultados de creación de un cobro
/*function getStatusDescription(status) {
  const statusPayment = {
    "approved": [{
      "accredited": "¡Listo! Se acreditó tu pago"
    }],
    "in_process": [
      {"pending_contingency": "Estamos procesando tu pago."},
      {"pending_review_manual": "No te preocupes, menos de 2 días hábiles te avisaremos por e-mail si se acreditó o si necesitamos más información."},
    ],
    "rejected": [
      {"cc_rejected_bad_filled_card_number": "Revisa el número de tarjeta."},
      {"cc_rejected_bad_filled_date": "Revisa la fecha de vencimiento." },
      {"cc_rejected_bad_filled_other": "Revisa los datos."},
      {"cc_rejected_bad_filled_security_code": "Revisa el código de seguridad de la tarjeta." },
      {"cc_rejected_blacklist": "No pudimos procesar tu pago."},
      {"cc_rejected_call_for_authorize": "Debes autorizar ante payment_method_id el pago de amount."},
      {"cc_rejected_card_disabled": "El teléfono está al dorso de tu tarjeta."},
      {"cc_rejected_card_error": "No pudimos procesar tu pago." },
      {"cc_rejected_duplicated_payment": " Ya hiciste un pago por ese valor. Si necesitas volver a pagar usa otra tarjeta u otro medio de pago."},
      {"cc_rejected_high_risk": "Tu pago fue rechazado. Elige otro de los medios de pago, te recomendamos con medios en efectivo."},
      {"cc_rejected_insufficient_amount": "Tu payment_method_id no tiene fondos suficientes"},
      {"cc_rejected_max_attempts": "Llegaste al límite de intentos permitidos. Elige otra tarjeta u otro medio de pago"},
      {"cc_rejected_other_reason": "payment_method_id no procesó el pago"}
    ]
  };

  // let description = "";
  // statusPayment[status.status].forEach(function (e) {
  //   if (e[status.status_detail]) description = e[status.status_detail];
  // });
  // return description;
  return statusPayment[status.status].find((e) => e[status.status_detail])[status.status_detail];
}*/

function abrirRegistrarVidaApp(cedula, nombre) {
  $("#cedula_vidaapp").val(cedula);
  $("#nombre_vidaapp").val(nombre);
  $("#modal_registrar_vidaapp").modal("show");
}

function validarEmail(mail) {
  let regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(mail) ? true : false;
}

function registrarVidaApp() {
  let cedula = $("#cedula_vidaapp").val();
  let nombre_completo = $("#nombre_vidaapp").val();
  let mail = $("#mail_vidaapp").val();

  if (cedula == "" || nombre_completo == "") {
    swal({
      title: "Error",
      text: "Complete todos los campos",
      icon: "error",
    });
  } else if (!comprobarCI(cedula)) {
    swal({
      title: "Error",
      text: "Cédula incorrecta",
      icon: "error",
    });
  } else if (mail != "" && !validarEmail(mail)) {
    swal({
      title: "Error",
      text: "Formato de email incorrecto",
      icon: "error",
    });
  } else if (
    $("#llenar_contactos_vidaapp").is(":checked") &&
    !validarContactosEmergencia()
  ) {
    return;
  } else {
    let contactos = $("#llenar_contactos_vidaapp").is(":checked")
      ? validarContactosEmergencia()
      : null;
    console.log(contactos);
    $.ajax({
      type: "POST",
      url: "ajax/registrarVidaApp.php",
      data: {
        cedula: cedula,
        nombre: nombre_completo,
        mail: mail,
        contactos_emergencia: contactos,
      },
      dataType: "JSON",
      beforeSend: function () {
        $("#loader").css("display", "flex");
      },
      complete: function () {
        $("#loader").css("display", "none");
      },
      success: function (content) {
        content.error
          ? swal({
              title: "Error",
              text: content.mensaje,
              icon: "error",
            })
          : (swal({
              title: "Correcto",
              text: content.mensaje,
              icon: "success",
            }),
            limpiarRegistrarVidaApp());
      },
      error: function () {
        swal({
          title: "Error",
          text: "Ha ocurrido un error",
          icon: "error",
        });
      },
    });
  }
}

function limpiarRegistrarVidaApp() {
  $("#modal_registrar_vidaapp").modal("hide");
  $("#cedula_vidaapp").val("");
  $("#nombre_vidaapp").val("");
  $("#mail_vidaapp").val("");
  limpiarContactosDeEmergencia();
}

function limpiarContactosDeEmergencia() {
  $("#mceNC1").val("");
  $("#mceTC1").val("");
  $("#mceNC2").val("");
  $("#mceTC2").val("");
  $("#mceNC3").val("");
  $("#mceTC3").val("");
}

function validarContactosEmergencia() {
  let contactos = new Object();
  contactos["nombre1"] = $("#mceNC1").val();
  contactos["telefono1"] = $("#mceTC1").val();
  contactos["nombre2"] = $("#mceNC2").val();
  contactos["telefono2"] = $("#mceTC2").val();
  contactos["nombre3"] = $("#mceNC3").val();
  contactos["telefono3"] = $("#mceTC3").val();
  let telefonos_contacto = [];
  let return_contactos = [];

  let contactos_correctos = true;

  if (
    contactos["telefono1"] == "" &&
    contactos["telefono2"] == "" &&
    contactos["telefono3"] == ""
  ) {
    contactos_correctos = false;
    swal({
      title: "¡Error!",
      text: "Debe completar al menos un contacto",
      icon: "error",
    });
  } else {
    if (contactos["telefono1"] != "") {
      if (!comprobarTelefono(contactos["telefono1"])) {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: "El teléfono/celular 1 es incorrecto",
          icon: "error",
        });
      } else if (contactos["nombre1"] == "") {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: "Debe completar nombre de contacto 1",
          icon: "error",
        });
      } else {
        telefonos_contacto.push(contactos["telefono1"]);
        return_contactos.push({
          nombre: contactos["nombre1"],
          telefono: contactos["telefono1"],
        });
      }
    }

    if (contactos["telefono2"] != "" && contactos_correctos) {
      if (!comprobarTelefono(contactos["telefono2"])) {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: "El teléfono/celular 2 es incorrecto",
          icon: "error",
        });
      } else if (contactos["nombre2"] == "") {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: "Debe completar nombre de contacto 2",
          icon: "error",
        });
      } else {
        telefonos_contacto.push(contactos["telefono2"]);
        return_contactos.push({
          nombre: contactos["nombre2"],
          telefono: contactos["telefono2"],
        });
      }
    }

    if (contactos["telefono3"] != "" && contactos_correctos) {
      if (!comprobarTelefono(contactos["telefono3"])) {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: '"El teléfono/celular 3 es incorrecto',
          icon: "error",
        });
      } else if (contactos["nombre3"] == "") {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: "Debe completar nombre de contacto 3",
          icon: "error",
        });
      } else {
        telefonos_contacto.push(contactos["telefono3"]);
        return_contactos.push({
          nombre: contactos["nombre3"],
          telefono: contactos["telefono3"],
        });
      }
    }

    if (contactos_correctos) {
      if (comprobarRepetidosArray(telefonos_contacto)) {
        contactos_correctos = false;
        swal({
          title: "¡Error!",
          text: "Todos los teléfonos de contacto deben ser distintos",
          icon: "error",
        });
      }
    }
  }
  return contactos_correctos ? return_contactos : false;
}

function comprobarTelefono(telefono) {
  return (/^(09)/.test(telefono) && telefono.length == 9) ||
    (/^4/.test(telefono) && telefono.length == 8) ||
    (/^2/.test(telefono) && telefono.length == 8)
    ? true
    : false;
}

function comprobarRepetidosArray(a) {
  for (let i = 0; i < a.length; i++) {
    if (a.indexOf(a[i]) !== a.lastIndexOf(a[i])) {
      return true;
    }
  }
  return false;
}
