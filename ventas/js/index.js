var loading = false;
$(document).ready(function () {
  filtradoPorFecha();
  totalizadorServicio();
  totalizadorPadronPiscina();
  totalizadorVentas();
  tablaSupervisores();
  tablaVendedores(false);
  tablaVentas(false);
  // verVentas(0, 0);
  selectEstados();
  toolTips();
});

const buscarPorFecha = function (data) {
  data.desde = $("#buscar_desde").val();
  data.hasta = $("#buscar_hasta").val();
};

const buscarPorFechaJson = () => {
  return {
    desde: $("#buscar_desde").val(),
    hasta: $("#buscar_hasta").val(),
  };
};

function resetFiltros() {
  $("#buscar_desde").val("");
  $("#buscar_hasta").val("");
  recargar();
}

function filtradoPorFecha() {
  const data = buscarPorFechaJson();
  $.ajax({
    type: "POST",
    url: "ajax/fecha.php",
    data: data,
    dataType: "json",
    success: function (response) {
      $("#filtrado_por_fecha").html(response.texto || "   ");
      $("#buscar_desde").val(response.fecha_desde || "");
      $("#buscar_hasta").val(response.fecha_hasta || "");
    },
  });
}
function recargar() {
  filtradoPorFecha();
  totalizadorServicio();
  totalizadorPadronPiscina();
  totalizadorVentas();
  tablaVentas(true);
  tablaVendedores(true);
  tablaSupervisores(true);
}
function urlEstado(url_param) {
  const estado = $("#estado_ventas").val();
  const url = `ajax/${url_param}.php?estado=${estado}`;
  return url;
}
function verServicios(cedula) {
  $("#modal_servicios_body").html("");
  $("#cin_servicios").html(` (CIN : ${cedula})`);
  const callback = (response) => {
    const data = response;
    if (data.length > 0) {
      for (let i in data) {
        const nro = Number.parseInt(i) + 1;
        $("#modal_servicios_body").append(
          `<p><strong>Servicio contratado N° ${nro}</strong> </p>`
        );
        for (let j in data[i]) {
          let servicio = data[i][j] != null ? data[i][j] : "";
          $("#modal_servicios_body").append(
            `<p><strong>${j} : ${servicio}</strong></p>`
          );
        }
        $("#modal_servicios_body").append(`<hr />`);
      }
      abrirModal("modal_servicios");
    }
  };
  serviciosVendedor(cedula, callback);
}

async function tablaVentas(recargar = false) {
  const url = urlEstado("tabla_ventas");
  const columnsTable = [
    "id",
    "nombre",
    "cedula",
    "edad",
    "estado",
    "vendedor",
    "supervisor",
    "servicio",
    "reintegro",
    "importe_total",
    "forma_de_pago",
    "convenio",
    "tipo",
    "fecha_carga",
    "fecha_pason_a_padron",
    "fecha_pendiente_bienvenida",
    "fecha_rechazo_bienvenida",
    "fecha_pendiente_calidad",
    "fecha_rechazo_calidad",
    "fecha_paso_morosidad",
    "fecha_rechazo_morosidad",
  ];
  const tabla = await tablaAsync(
    "table_ventas",
    url,
    buscarPorFecha,
    columnsTable,
    recargar,
    () => {}
  );
  loading = true;
}

function tablaVendedores(recargar = false) {
  const url = urlEstado("tabla_vendedores");
  const columnsTable = [
    "id",
    "supervisor",
    "nombre",
    "cedula",
    "cantidad",
    "monto",
  ];
  tablaNormal(
    "table_vendedores",
    url,
    buscarPorFecha,
    columnsTable,
    recargar,
    () => {},
    [
      [1, "asc"],
      [4, "desc"],
    ]
  );
}

function tablaSupervisores(recargar = false) {
  const url = urlEstado("tabla_supervisores");
  const columnsTable = [
    "id",
    "nombre",
    "padron",
    "pendiente",
    "morosidad",
    "vendedores",
    "grupo",
  ];
  tablaNormal(
    "table_supervisores",
    url,
    buscarPorFecha,
    columnsTable,
    recargar,
    () => {},
    [
      [2, "asc"],
      [3, "desc"],
      [4, "desc"],
    ]
  );
}

function verVentas(cedula_vendedor, id_vendedor, recargar = false) {
  $("#table_vendedor_venta_body").html("");
  $("#venta_vendedor").html(cedula_vendedor);
  const url = `ajax/ventas_vendedor.php?ci_vendedor=${cedula_vendedor}&id_vendedor=${id_vendedor}`;
  const columnsTable = ["id", "nombre", "cedula", "monto", "estado", "ventas"];
  tablaNormal(
    "table_ventas_vendedores",
    url,
    buscarPorFecha,
    columnsTable,
    recargar,
    () => {},
    3
  );
}

function verServiciosVendedor(cedula) {
  $("#table_vendedor_venta_body").html(
    "<br /> <br /> <br /><h2>Servicios contratados</h2> <hr />"
  );
  const callback = (response) => {
    const data = response;
    if (data.length > 0) {
      for (let i in data) {
        const nro = Number.parseInt(i) + 1;
        $("#table_vendedor_venta_body").append(
          `<p><strong>Servicio contratado N° ${nro}</strong> </p>`
        );
        for (let j in data[i]) {
          let servicio = data[i][j] != null ? data[i][j] : "";
          $("#table_vendedor_venta_body").append(
            `<p><strong>${j} : ${servicio}</strong></p>`
          );
        }
        $("#table_vendedor_venta_body").append(`<hr />`);
      }
    }
  };
  serviciosVendedor(cedula, callback);
}

function serviciosVendedor(cin, callback) {
  $.ajax({
    type: "POST",
    url: "ajax/servicios.php",
    data: {
      cin: cin,
    },
    dataType: "json",
    success: function (response) {
      callback(response);
    },
  });
}

function selectEstados() {
  $("#estado_ventas").html("");
  $.ajax({
    type: "POST",
    url: "ajax/estados.php",
    data: {},
    dataType: "json",
    success: function (response) {
      for (let i in response) {
        $("#estado_ventas").append(
          `<option value="${response[i].id}">${response[i].estado}</option>`
        );
      }
    },
  });
}

function totalizadorServicio() {
  $("#totalizador_servicios").html("<h3>Total de servicios en Padrón: </h3>");
  $("#totalizador_servicios")
    .append(`<div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargandoo...</span>
    </div>`);
  const url = "ajax/totalizador_servicios.php";
  $.ajax({
    type: "POST",
    url: url,
    data: buscarPorFechaJson(),
    dataType: "json",
    success: function (response) {
      $("#totalizador_servicios").html(
        "<h3>Total de servicios en Padrón: </h3>"
      );
      $("#row_totalizador_servicios").html("");
      $("#totalizador_servicios").append(
        `<div class="row" id="row_totalizador_servicios"></div>`
      );
      for (let nombre in response) {
        const servicio = response[nombre];
        if (typeof servicio === "object") {
          for (let i in servicio) {
            $("#row_totalizador_servicios").append(` 
                        <div class="col-sm-auto">
                            <h5>${i}: <b> ${servicio[i]}</b></h5>
                        </div>                    
                        `);
          }
        } else {
          $("#row_totalizador_servicios").append(` 
                    <div class="col-sm-auto">
                        <h5>${nombre}: <b>  ${servicio}</b></h5>
                    </div>                    
                    `);
        }
      }
    },
  });
}
function totalizadorPadronPiscina() {
  $("#totalizador_padron_piscina").html("<hr /><h3>Total Socios: </h3>");
  $("#totalizador_padron_piscina")
    .append(`<div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargandoo...</span>
        </div>`);
  const url = "ajax/totalizador_pisicina_padron.php";
  $.ajax({
    type: "POST",
    url: url,
    data: buscarPorFechaJson(),
    dataType: "json",
    success: function (response) {
      $("#totalizador_padron_piscina").html("<hr /><h3>Total Socios: </h3>");
      $("#totalizador_padron_piscina").append(`
                <div class="row">
                    <div class="col-sm-auto">
                        <h5>Total : <b>${response.total_socios}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>En Padrón: <b>${response.padron}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>Pendiente de Bienvenida: <b>${response.pendiente}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>Rechazado Bienvenida: <b>${response.rechazado_bienvenida}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>Pendiente de calidad: <b>${response.bienvenida}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>Rechazado por calidad: <b>${response.rechazados}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>Pendiente morosidad : <b>${response.pendiente_morisidad}</b></h5>
                    </div>
                    <div class="col-sm-auto">
                        <h5>Rechazado  por Morosidad:<b> ${response.rechazado_morosidad}</b></h5>
                    </div>
              
                  
                </div>
            `);
    },
  });
}

function totalizadorVentas() {
  $("#totalizador_ventas").html("<hr />");
  const url = urlEstado("tabla_supervisores");

  $.ajax({
    type: "POST",
    url: url,
    data: buscarPorFechaJson(),
    dataType: "json",
    success: function (response) {
      
      let padron = 0;
      let pendientes = 0;
      let morosidad = 0;
      let vendedores = 0;
      const grupo = [];
      for (let i in response.data) {
        if (grupo.includes(response.data[i].grupo) == false) {
          let vendedor = Number.parseInt(response.data[i].vendedores);
          vendedores = vendedor + vendedores;
          let padron_ = Number.parseInt(response.data[i].padron);

          padron = padron + padron_;
          let morosidad_ = Number.parseInt(response.data[i].morosidad);
          morosidad = morosidad + morosidad_;
          let pendiente = Number.parseInt(response.data[i].pendiente);
          pendientes = pendientes + pendiente;
          grupo.push(response.data[i].grupo);
        }
      }
      /*
                <div class="col-sm-auto">
                        <h5>Pendientes: ${pendientes}</h5>
                    </div> 
                    <div class="col-sm-auto">
                        <h5>Padrón: ${padron}</h5>
                    </div>     
                    <div class="col-sm-auto">
                        <h5>Morosidad: ${morosidad}</h5>
                    </div>    

            */
      $("#totalizador_ventas").append(`
                <div class="row">
                    <div class="col-sm-auto">
                        <h5>Supervisores: ${response.data.length}</h5>
                    </div>  
                    <div class="col-sm-auto">
                     <h5>Vendedores Activos: ${vendedores}</h5>
                    </div>                  
                </div>
                `);
    },
  });
}
