$(document).ready(function () {

    tabla_pendientes_crediv();

});

function tabla_pendientes_crediv() {
    $("#tabla_pendientes_crediv").DataTable({
        ajax: `${url_ajax}tabla_pendientes_crediv.php`,
        columns: [
            { data: "id" },
            { data: "cedula" },
            { data: "nombre" },
            { data: "telefono" },
            { data: "nombre_vendedor" },
            { data: "solicitud" },
            { data: "fecha_registro" },
            { data: "acciones" },
        ],
        order: [[0, "asc"]],
        bDestroy: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json",
        },
    });
}


$(document).on('change', '#select_campo_prestamo_crediv', function (event) {
    let monto_prestamo = $("#select_campo_prestamo_crediv option:selected").val();

    if (monto_prestamo == 1) { //Acepto préstamo
        $("#div_monto_prestamo_crediv").css("display", "block");
        $("#div_motivo_rechazo_crediv").css("display", "none");
        $("#txt_motivo_rechazo_crediv").val("");
    } else if (monto_prestamo == 2) { //No acepto préstamo
        $("#div_monto_prestamo_crediv").css("display", "none");
        $("#div_motivo_rechazo_crediv").css("display", "block");
        $("#txt_monto_prestamo_crediv").val("");
    } else { //Seleccionó opción por defecto
        $("#div_monto_prestamo_crediv").css("display", "none");
        $("#div_motivo_rechazo_crediv").css("display", "none");
        $("#txt_monto_prestamo_crediv").val("");
        $("#txt_motivo_rechazo_crediv").val("");
    }
});


function registrar_crediv(openModal = false, id) {
    if (openModal == true) {
        $("#txt_id_crediv").val(id);
        $("#div_monto_prestamo_crediv").css("display", "none");
        $("#div_motivo_rechazo_crediv").css("display", "none");
        $("#modal_registrarCrediv").modal("show");
    } else {

        let id = $("#txt_id_crediv").val();
        let campo_prestamo = $("#select_campo_prestamo_crediv").val();
        let monto_prestamo = $("#txt_monto_prestamo_crediv").val();
        let motivo_rechazo = $("#txt_motivo_rechazo_crediv").val();
        let fecha_accion = $("#txt_fecha_accion_crediv").val();

        if (id == "") {
            error("Debe ingresar un ID");
        } else if (campo_prestamo == "") {
            error("Debe seleccionar una opción en el desplegable Campo Préstamo");
        } else if (campo_prestamo == 1 && monto_prestamo == "") {
            error("Debe ingresar el monto del préstamo");
        } else if (campo_prestamo == 2 && motivo_rechazo == "") {
            error("Debe ingresar el motivo del rechazo");
        } else if (monto_prestamo != "" && motivo_rechazo != "") {
            error("No puede ingresar el monto y el motivo de rechazo del préstamo, Contacte con el administrador");
        } else if (fecha_accion == "") {
            error("Debe ingresar una fecha de acción");
        } else {

            $.ajax({
                type: "POST",
                url: `${url_ajax}registrar_crediv.php`,
                data: {
                    id,
                    campo_prestamo,
                    monto_prestamo,
                    motivo_rechazo,
                    fecha_accion
                },
                dataType: "JSON",
                success: function (response) {
                    if (response.error === false) {
                        correcto(response.mensaje);
                        $("#txt_id_crediv").val("");
                        $("#select_campo_prestamo_crediv").val("");
                        $("#txt_monto_prestamo_crediv").val("");
                        $("#txt_motivo_rechazo_crediv").val("");
                        $("#txt_fecha_accion_crediv").val("");
                        tabla_pendientes_crediv();
                        $("#modal_registrarCrediv").modal("hide");
                    } else {
                        error(response.mensaje);
                    }
                }
            });

        }
    }
}