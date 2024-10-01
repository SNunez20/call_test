<br />
<hr />
<br />
<div class="row text-center container">
    <div class="col-sm-8">
    <h2>Vendedores</h2>
    </div>
    <div class="col-sm-4">
        <button class="btn btn-success" onclick="tablaVendedores(true);" data-bs-toggle="tooltip" data-bs-placement="top" title="Recargar la tabla">Recargar</button>
    </div>
</div>

<hr />

<div class="table-responsive">
    <table class="table" id="table_vendedores">
        <thead class="bg-success" style="color:white;">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Supervisor</th>
                <th scope="col">Nombre</th>
                <th scope="col">Usuario</th>
                <th scope="col">Cantidad</th>
                <th scope="col">Monto Total</th>
            </tr>
        </thead>
        <tbody id="table_vendedores_body">
        </tbody>
    </table>
</div>

<br/> <br />