<div class="modal modal-lg" tabindex="-1" role="dialog" id="modal_ventas_vendedor">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-black">
                <h4 class="modal-title" style="color:white;">Ventas de Vendedor <span id="venta_vendedor"></span></h4>

            </div>
            <div class="modal-body" id="modal_servicios_body">
               
                <div class="table-responsive">
                    <table class="table" id="table_ventas_vendedores">
                        <thead class="bg-black" style="color:white;">
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Cedula</th>
                                <th scope="col">Monto</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Ventas</th>
                            </tr>
                        </thead>
                        <tbody >
                        </tbody>
                        
                    </table>

                    <div id="table_vendedor_venta_body"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="cerrarModal('modal_ventas_vendedor');">Cerrar</button>
            </div>
        </div>
    </div>
</div>