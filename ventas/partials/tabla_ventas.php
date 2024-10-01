<h1 class="text-center">Ventas</h1>
 
<div class="row text-center container">
    <div class="col-sm-1">
        <strong>Estado:</strong>
    </div>
    <div class="col-sm-auto">

        <select class="form-control" name="estado_ventas" id="estado_ventas"
         onchange="recargar();">
            <option value="todos" selected>Todos</option>
            <option value="1">Pendientes revisión</option>
            <option value="6">En Padrón</option>
        </select>
    </div>
    <div class="col-sm-auto">
        <button class="btn btn-primary" onclick="recargar();" 
        data-bs-toggle="tooltip" data-bs-placement="top" title="Recargar la tabla">Recargar</button>
    </div>
    <div class="col-auto">
        <label for="buscar_desde" class="col-form-label" >📅 <strong>Desde:</strong></label>
    </div>
    <div class="col-auto">
        <input type="date" id="buscar_desde" name="buscar_desde" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" title="Fecha de inicio">
    </div>
    <div class="col-auto">
        <label for="buscar_hasta" class="col-form-label">📅 <strong>Hasta:</strong></label>
    </div>
    <div class="col-auto">
        <input type="date" id="buscar_hasta" name="buscar_hasta" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" title="Fecha de fin">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary" id="btn_buscar_ventas"
        onclick="recargar();" 
        data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar por fechas de afiliación">🔍</button>
    </div>
    <div class="col-auto">
        <button class="btn btn-danger" onclick="resetFiltros();" data-bs-toggle="tooltip" data-bs-placement="top" title="Restablecer Filtros">↻</button>
    </div>
</div>

<hr />

<div class="table-responsive-xl">
    <table class="table table-hover" id="table_ventas">
        <thead class="bg-primary" style="color:white;">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Nombre</th>
                <th scope="col">Cedula</th>
                <th scope="col">Edad</th>
                <th scope="col">Estado</th>
                <th scope="col">Vendedor</th>
                <th scope="col">Supervisor</th>
                <th scope="col">Servicio</th> 
                <th scope="col">Reintegro</th>
                <th scope="col">Total Importe</th>
                <th scope="col">Forma de pago</th>
                <th scope="col">Convenio</th>
                <th scope="col">Tipo</th>
                <th scope="col">Fecha Carga</th>
                <th scope="col">Fecha en padrón</th>
                <th scope="col">Pendiente de Bienvenida</th>
                <th scope="col">Rechazado por bienvenida</th>
                <th scope="col">Pendiente Calidad</th>
                <th scope="col">Rechazo Calidad</th>
                <th scope="col">Pendiente Morosidad</th>
                <th scope="col">Rechazado por Morosidad</th>              
            </tr>
        </thead>
        <tbody id="table_ventas_body">
        </tbody>
    </table>
</div>
