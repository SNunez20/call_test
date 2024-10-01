<!-- Modal -->
<div class="modal fade" id="modal_registrarCrediv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Registrar Crediv</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="txt_id_crediv" placeholder="ID" disabled>
                    <label for="txt_id_crediv">ID:</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="select_campo_prestamo_crediv" aria-label="Select Campo Prestamo">
                        <option value="" selected>Seleccione una opción</option>
                        <option value="1">Acepto préstamo</option>
                        <option value="2">No acepto préstamo</option>
                    </select>
                    <label for="select_campo_prestamo_crediv">Campo Préstamo:</label>
                </div>

                <div class="form-floating mb-3" id="div_monto_prestamo_crediv">
                    <input type="number" class="form-control" id="txt_monto_prestamo_crediv" placeholder="Monto Préstamo">
                    <label for="txt_monto_prestamo_crediv">Monto Préstamo:</label>
                </div>

                <div class="form-floating mb-3" id="div_motivo_rechazo_crediv">
                    <textarea class="form-control" placeholder="Motivo de rechazo" id="txt_motivo_rechazo_crediv" style="height: 100px"></textarea>
                    <label for="txt_motivo_rechazo_crediv">Motivo de rechazo:</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="txt_fecha_accion_crediv" placeholder="Fecha de Acción">
                    <label for="txt_fecha_accion_crediv">Fecha de Acción:</label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="registrar_crediv(false)">Registrar</button>
            </div>
        </div>
    </div>
</div>