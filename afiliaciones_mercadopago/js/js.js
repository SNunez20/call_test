$(()=>{
    ListarAfiliaciones();
});

function ListarAfiliaciones(){
    if (!$.fn.DataTable.isDataTable('#TableAfiliados')) {
        desde = $('#desde').val();
        hasta = $('#hasta').val();

        table = $('#TableAfiliados').DataTable({
            ajax: { 
                "url": 'ajax/listarAfiliaciones.php',
                "data": {
                    "desde": desde,
                    "hasta": hasta
                },
                "type": 'POST'
            },
            lengthChange: true,
            reposive: true,
            ordering: true,
            order: [[2, "asc"],[3, "asc"]],
            oLanguage: {
                "sUrl": "json/traducciontabla.json"
            },
            dom: 'Bfrtip',
            buttons: [
                'excel'
            ]
        });
    } else {
        table.ajax.reload();
    }
}