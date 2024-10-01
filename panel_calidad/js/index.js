$cedulaAfiliado = "";

$("document").ready(function () {
  dashboard();
  verifySession();

  $("#btnCloseSession").click(function (e) {
    e.preventDefault();
    closeSession();
  });

  $("#btn-clear").click(function (e) {
    e.preventDefault();
    if ($("select option:selected[value!='']").length) $("select").val("").change();
    historico();
  });

  $("#btn-filtrar").click(function () {
    dashboard();
  });

  $("#btnHistorico").click(function () {
    historico();
  });

  const username = localStorage.username
    .split(" ")
    .map(function (x) {
      return `${x[0].toUpperCase()}${x.slice(1)}`;
    })
    .join(" ")
    .trim();
  $(".nav-username").html(username);
});

function closeSession() {
  localStorage.clear();
  location.href = "./login.php";
}

function verifySession() {
  if (localStorage.username === undefined || localStorage.username != "calidad01") location.href = "./login.php";
}

//evento que arma el datatable del dashboard
function dashboard(e) {
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

  $table = $("#table-panelCalidad").DataTable({
    ajax: {
      url: "Ajax/panelCalidad.php",
      data: {
        username: localStorage.usuario,
        typeAdmin: localStorage.typeAdmin,
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
          if ([0, 1, 2, 3].includes(index)) {
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
    lengthMenu: [7],
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
        targets: [0, 1, 2, 3, 4, 5, 6],
        className: "dt-body-center",
      },
      {
        targets: [0, 1, 2, 3, 4, 5, 6],
        className: "dt-head-center",
      },
      {
        targets: 3,
        createdCell: function (td, cellData, rowData, row, col) {
          $(td).attr("data-toggle", "tooltip");
          $(td).attr("data-placemnt", "top");
          $(td).attr("title", cellData);
          $(td).css("text-transform", "uppercase");
          $(td).css("font-weight", "bold");
          $(td).css("color", COLORS[cellData]);
        },
      },
    ],
    drawCallback: function () {
      $('[data-toggle="popover"]').popover();
    },
  });

  $("#dashboard").is(":hidden") && $("#dashboard").show();
  $("#history").hide();
}

function historico() {
  if (!$.fn.DataTable.isDataTable("#table-history")) {
    $tableH = $("#table-history").DataTable({
      ajax: {
        url: "Ajax/history.php",
        data: {
          username: localStorage.usuario,
          typeAdmin: localStorage.typeAdmin,
        },
        method: "POST",
      },
      initComplete: function (settings, json) {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
        let i = 0;
        this.api()
          .columns()
          .every(function (index) {
            if ([3].includes(index)) {
              let column = this;
              let select = $(`#select-filter-${index}`).on("change", function () {
                let val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? "^" + val + "$" : "", true, false).draw();
              });

              column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                  select.append(`<option value="${d}">${d}</option>`);
                });

              i++;
            }
          });
      },
      columnDefs: [
        {
          targets: 6,
          createdCell: function (td, cellData, rowData, row, col) {
            $(td).attr("data-toggle", "tooltip");
            $(td).attr("data-placemnt", "top");
            $(td).attr("title", cellData);
            $(td).css("text-transform", "uppercase");
          },
        },
      ],
      drawCallback: function () {
        $('[data-toggle="popover"]').popover();
      },
      // lengthMenu: [20],
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
    $tableH.ajax.reload(function () {
      $("#primaria").css("display", "none");
    }, false);
  }
  $("#nav").removeClass("show");
  $("#dashboard").hide();
  $("#history").is(":hidden") && $("#history").show();
}

$arrBeneficiarios = [];

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

function cambiarEstado(idAfiliado, idEstado) {
  swal({
    title: "¿Estás seguro de realizar esta acción?",
    text: "",
    icon: "warning",
    buttons: true,
    confirmButtonText: "Si",
    confirmButtonColor: "#3085d6",
    cancelButtonText: "No",
    cancelButtonColor: "#d33",
  }).then((save) => {
    if (save) {
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
          $.ajax({
            type: "POST",
            url: "ajax/procesoCambiarEstado.php",
            data: {
              typeAdmin: localStorage.typeAdmin,
              idAfiliado: idAfiliado,
              idEstado: idEstado,
              idUser: localStorage.idUser,
              observacion: observacion,
            },
            dataType: "json",
            success: function (response) {
              if (response.result) {
                swal({
                  title: "Éxito",
                  text: response.message,
                  icon: "success",
                });
                dashboard();
              }
            },
          });
        }
      });
    } else {
      swal("Acción cancelada");
    }
  });
}
