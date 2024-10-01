window.onload = () => {
    cargarTabla();

    document.getElementById('filtrarTabla').addEventListener("click", filtrarTabla);
    document.getElementById('limpiarFiltros').addEventListener("click", limpiarFiltros);
};


const cargarTabla = () => {
    const desde = document.getElementById('desde').value;
    const hasta = document.getElementById('hasta').value;

    if ($.fn.DataTable.isDataTable('#datatable'))
        $('#datatable').DataTable().destroy();

    $('#datatable').DataTable({
        ajax: function (d, cb) {
            fetch(`./ajax/get_ventas_comepa.php?desde=${desde}&hasta=${hasta}`)
                .then(response => response.json())
                .then(data => cb(data));
        },
        columns: [
            { data: 'ac_id', title: 'ID' },
            { data: 'ac_cedula', title: 'Cédula Afiliado' },
            { data: 'ac_metodo_pago', title: 'Método pago' },
            { data: 'ac_fecha_afiliacion', title: 'Fecha' },
            { data: 'u_nombre', title: 'Nombre vendedor' },
            { data: 'u_usuario', title: 'Cédula vendedor' },
        ],
        dom: 'Bfrtip',
        buttons: ['excel']
    });
}

const filtrarTabla = event => {
    event.preventDefault();

    const desde = document.getElementById('desde').value;
    const hasta = document.getElementById('hasta').value;

    if (desde === '' || hasta === '')
        return alert('Es necesario especificar ambas fechas.');
    else if (desde > hasta) {
        document.getElementById('hasta').value = desde;
        document.getElementById('desde').value = hasta;
    }

    cargarTabla();
}

const limpiarFiltros = event => {
    event.preventDefault();

    document.getElementById('desde').value = '';
    document.getElementById('hasta').value = '';

    cargarTabla();
}