<br />
<hr />
<br />
<div class="row text-center container">
    <div class="col-sm-8">
        <h2>Supervisores</h2>
    </div>
    <div class="col-sm-4">
        <button class="btn btn-danger" onclick="tablaSupervisores(true);" data-bs-toggle="tooltip" data-bs-placement="top" title="Recargar la tabla">Recargar</button>
    </div>
</div>

<hr />

<div class="table-responsive">
    <table class="table" id="table_supervisores">
        <thead class="bg-danger" style="color:white;">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Nombre</th>
                <th scope="col">Padrón</th>
                <th scope="col">Pendiente</th>
                <th scope="col">Morosidad</th>
                <th scope="col">Vendedores activos</th>
                <th scope="col">Grupo</th>
            </tr>
        </thead>
        <tbody id="table_supervisores_body">
        </tbody>
    </table>
</div>
<br />
