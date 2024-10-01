  
$('document').ready(function (){

    if(!localStorage.typeUser){
        window.location.href = 'login.php';
    }else{
        $('#nombreUser').text(localStorage.nombreUser)
    }
    // $('.dataTables_length').addClass('bs-select');
  
    $('.fecha').datetimepicker({
        format: 'DD-MM-YYYY',
        locale: 'es',
        autoclose: true
    });
    // reportePadron();
    $('#btnReportePadron').click(function(e){
        reportePadron(e);
    });
   
   
    let param = new URLSearchParams(location.search);
    let reporte = param.get('reporte');
  
    if (reporte == '1') {
      
        reportePadron();
        $('.reportePadron').addClass('active');
        $('.reportePiscina').removeClass('active');
        $('.reporteVendedores').removeClass('active');
        $('.historicoVendedores').removeClass('active');
        $('.productosFiliales').removeClass('active');
        $('.historicoAltas').removeClass('active');
    }else if(reporte == '2'){
        reportePiscina();
        $('.reportePadron').removeClass('active');
        $('.reportePiscina').addClass('active');
        $('.reporteVendedores').removeClass('active');
        $('.historicoVendedores').removeClass('active');
        $('.productosFiliales').removeClass('active');
        $('.historicoAltas').removeClass('active');
        
    }else if (reporte=='3'){
        reporteVendedores();
        $('.reportePadron').removeClass('active');
        $('.reportePiscina').removeClass('active');
        $('.reporteVendedores').addClass('active');
        $('.historicoVendedores').removeClass('active');
        $('.productosFiliales').removeClass('active');
        $('.historicoAltas').removeClass('active');
    }else if (reporte=='4'){
      
        $('.reportePadron').removeClass('active');
        $('.reportePiscina').removeClass('active');
        $('.reporteVendedores').removeClass('active');
        $('.historicoVendedores').addClass('active');
        $('.productosFiliales').removeClass('active');
        $('.historicoAltas').removeClass('active');
        $('.graficosComparativos').removeClass('active');
    }else if (reporte=='5'){
      
        llenarSelectFiliales();
        $('.reportePadron').removeClass('active');
        $('.reportePiscina').removeClass('active');
        $('.reporteVendedores').removeClass('active');
        $('.historicoVendedores').removeClass('active');
        $('.productosFiliales').addClass('active');
        $('.historicoAltas').removeClass('active');
        $('.graficosComparativos').removeClass('active');
    }else if (reporte=='6'){
  
        historicoAltas();
        $('.reportePadron').removeClass('active');
        $('.reportePiscina').removeClass('active');
        $('.reporteVendedores').removeClass('active');
        $('.historicoVendedores').removeClass('active');
        $('.productosFiliales').removeClass('active');
        $('.historicoaltas').addClass('active');
        $('.graficosComparativos').removeClass('active');
    }else if (reporte=='7'){
    
        mostrarGraficos();
        $('.reportePadron').removeClass('active');
        $('.reportePiscina').removeClass('active');
        $('.reporteVendedores').removeClass('active');
        $('.historicoVendedores').removeClass('active');
        $('.productosFiliales').removeClass('active');
        $('.historicoaltas').removeClass('active');
        $('.graficosComparativos').addClass('active');
    }

   
  
});


$('#logOutUser').click(function (e) {
    e.preventDefault();
    localStorage.clear();
    window.location.href = 'login.php';
});

function llenarSelectFiliales(){
    calcularIndicadores();
    $.ajax({
        type: 'POST',
        url: 'ajax/obtenerFiliales.php',
        data: {},
        dataType: 'JSON',
        beforeSend: () => {
            Swal.fire({
                title: 'Cargando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: (res) => {
            Swal.close();
            if (res.result) {
                let select = `<option value="0">- Seleccione -</option>`;
                res.filiales.forEach(function(v, i){
                    console.log(v);
                    select += `<option value="${v.id}">${v.nombreFilial}</option>`;
                });

                $('#filial').empty().append(select);
               
                
               
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.log(error);
        }
    });
}

function calcularIndicadores(){

    let fecha_desde = $('#fecha_desde').val();
    let fecha_hasta = $('#fecha_hasta').val();

    $.ajax({
        type: 'POST',
        url: 'ajax/calcularIndicadores.php',
        data: {
            fecha_desde: fecha_desde,
            fecha_hasta: fecha_hasta
        },
        dataType: 'JSON',
        beforeSend: () => {
            // Swal.fire({
            //     title: 'Cargando datos...',
            //     allowEscapeKey: false,
            //     allowOutsideClick: false,
            //     onOpen: () => {
            //         Swal.showLoading();
            //     }
            // });
        },
        success: (res) => {
            // Swal.close();
            if (res.result) {
                $('#cantAltas').text(res.totalAltas);
                $('#montoAltas').text(res.montoTotalAltas+' $');
                $('#cantIncrementos').text(res.totalIncrementos);
                $('#montoIncrementos').text(res.montoTotalIncrementos+' $');
                $('.vendedoresActivos').text(res.totalVendedoresActivos);
                
               
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.log(error);
        }
    });
}

function reportePadron(){
    let fecha_desde = $('#fecha_desde').val();
    let fecha_hasta = $('#fecha_hasta').val();

    $.ajax({
        type: 'POST',
        url: 'ajax/reportePadron.php',
        data: {
            fecha_desde: fecha_desde,
            fecha_hasta: fecha_hasta
        },
        dataType: 'JSON',
        beforeSend: () => {
            Swal.fire({
                title: 'Cargando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: (res) => {
            calcularIndicadores();
            Swal.close();
            if (res.result) {
                console.log(res);
                // $('#cantAltas').text(res.totalAltas);
                // $('#montoAltas').text(res.montoTotalAltas+' $');
                // $('#cantIncrementos').text(res.totalIncrementos);
                // $('#montoIncrementos').text(res.montoTotalIncrementos+' $');
                // $('.vendedoresActivos').text(res.totalVendedoresActivos);

                
                     $('#tableAltasPorFilial').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy:true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.altasFilial,
                        "columns": [
                            { "data": "filial" },
                            { "data": "cantidad_altas" },
                            { "data": "monto_ventas" },
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
              
        
                    $('#tableIncrementosPorFilial').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy:true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.incrementosFilial,
                        "columns": [
                            { "data": "filial" },
                            { "data": "cantidad_incrementos" },
                            { "data": "monto_ventas" },
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
                   
           
                    $('#tableAltasPorCall').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy:true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.altasCall,
                        "columns": [
                            { "data": "call" },
                            { "data": "cantidad_altas" },
                            { "data": "monto_ventas" },
                            { "data": "vendedores_activos"}
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
                
                  
                    $('#tableIncrementosPorCall').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy:true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.incrementosCall,
                        "columns": [
                            { "data": "call" },
                            { "data": "cantidad_incrementos" },
                            { "data": "monto_ventas" },
                            { "data": "vendedores_activos"}
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
               

                
                    // let tdIncrementosCall=``;
                    // res.incrementosCall.forEach(function(val,index ){
                    //     tdIncrementosCall +=`<tr>
                    //                         <td>${val.call}</td>
                    //                         <td>${val.cantidad_incrementos}</td>
                    //                         <td>${val.monto_ventas}</td>
                    //                     </tr>`;
                    // });
                    // $('#tbodyIncrementosPorCall').empty().append( tdIncrementosCall);
                
               
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.log(error);
        }
    });

}

function reportePiscina(){
    let fecha_desde_piscina = $('#fecha_desde_piscina').val();
    let fecha_hasta_piscina = $('#fecha_hasta_piscina').val();

    $.ajax({
        type: 'POST',
        url: 'ajax/reportePiscina.php',
        data: {
            fecha_desde: fecha_desde_piscina,
            fecha_hasta: fecha_hasta_piscina
        },
        dataType: 'JSON',
        beforeSend: () => {
            Swal.fire({
                title: 'Cargando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: (res) => {
            Swal.close();
            if (res.result) {
        
                $('#cantAltasPiscina').text(res.totalAltas);
                $('#montoAltasPiscina').text(res.montoTotalAltas+' $');
                $('#cantIncrementosPiscina').text(res.totalIncrementos);
                $('#montoIncrementosPiscina').text(res.montoTotalIncrementos+' $');
                $('#cantVendedores').text(res.totalVendedoresActivos);

                    $('#tableAltasPorFilialPiscina').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy: true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.altasFilial,
                        "columns": [
                            { "data": "filial" },
                            { "data": "cantidad_altas" },
                            { "data": "monto_ventas" },
                           
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
                
               
                    $('#tableIncrementosPorFilialPiscina').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy: true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.incrementosFilial,
                        "columns": [
                            { "data": "filial" },
                            { "data": "cantidad_incrementos" },
                            { "data": "monto_ventas" },
                           
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
               

                    $('#tableAltasPorCallPiscina').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy:true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.altasCall,
                        "columns": [
                            { "data": "call" },
                            { "data": "cantidad_altas" },
                            { "data": "monto_ventas" },
                            { "data": "cantidad_vendedores"}
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
                

                    $('#tableIncrementosPorCallPiscina').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy:true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.incrementosCall,
                        "columns": [
                            { "data": "call" },
                            { "data": "cantidad_incrementos" },
                            { "data": "monto_ventas" },
                            { "data": "cantidad_vendedores" }
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
                
               
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.log(error);
        }
    });

}

function reporteVendedores(){
    calcularIndicadores();
    let fecha_desde_vendedores = $('#fecha_desde').val();
    let fecha_hasta_vendedores = $('#fecha_hasta').val();
    let sueldo = ($('#sueldo').val()=='') ? 0 : $('#sueldo').val();

    

    $.ajax({
        type: 'POST',
        url: 'ajax/reporteVendedores.php',
        data: {
            fecha_desde: fecha_desde_vendedores,
            fecha_hasta: fecha_hasta_vendedores,
            sueldo: sueldo
        },
        dataType: 'JSON',
        beforeSend: () => {
            Swal.fire({
                title: 'Cargando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: (res) => {
            Swal.close();
            if (res.result) {
            

                    $('#tableAltasPorVendedor').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        destroy:true,
                        "data": res.altasVendedores,
                        "columns": [
                            { "data": "vendedor" },
                            { "data": "cedula" },
                            { "data": "call" },
                            { "data": "cantidad_altas" },
                            { "data": "monto_ventas" },
                            { "data": "target" },
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                        
                    });

                    $('#tableAltasPorVendedorInactivo').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        destroy:true,
                        "data": res.altasVendedoresInactivos,
                        "columns": [
                            { "data": "vendedor" },
                            { "data": "cedula" },
                            { "data": "call" },
                            { "data": "cantidad_altas" },
                            { "data": "monto_ventas" },
                            { "data": "target" },
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                        
                    });
                 
         
                    $('#tableIncrementosPorVendedor').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy: true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.incrementosVendedores,
                        "columns": [
                            { "data": "vendedor" },
                            { "data": "cedula" },
                            { "data": "call" },
                            { "data": "cantidad_incrementos" },
                            { "data": "monto_ventas" },
                            { "data": "target" },
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });

                    $('#tableIncrementosPorVendedorInactivo').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy: true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.incrementosVendedoresInactivos,
                        "columns": [
                            { "data": "vendedor" },
                            { "data": "cedula" },
                            { "data": "call" },
                            { "data": "cantidad_incrementos" },
                            { "data": "monto_ventas" },
                            { "data": "target" },
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                    });
                  
                
               
            } else {
                
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.error(error);
        }
    });

}

function historicoVendedores(){
    calcularIndicadores();
    let fecha_desde_hv = $('#fecha_desde').val();
    let fecha_hasta_hv = $('#fecha_hasta').val();
    let cedula_vendedor = $('#cedula_vendedor').val();

    if (cedula_vendedor=='') {
  
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debe ingresar una cédula',
        });
    }else if (!comprobarCI(cedula_vendedor)) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La cédula ingresada no es válida',
        });
    }

    $.ajax({
      
        type: 'POST',
        url: 'ajax/historicoVendedor.php',
        data: {
            fecha_desde: fecha_desde_hv,
            fecha_hasta: fecha_hasta_hv,
            cedula_vendedor : cedula_vendedor
        },
        dataType: 'JSON',
        beforeSend: () => {
            Swal.fire({
                title: 'Cargando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: (res) => {
            Swal.close();
            if (res.result) {
               
                // $('#cantAltasVendedores').text(res.totalAltas);
                // $('#montoAltasVendedores').text(res.montoTotalAltas+' $');
                // $('#cantIncrementosVendedores').text(res.totalIncrementos);
                // $('#montoIncrementosVendedores').text(res.montoTotalIncrementos+' $');
            

                if (res.result_vendedor) {
            
                        $('#tableHistoricoVendedor').DataTable({
                            "dom": 'Bfrtip',
                            buttons: [
                                'excel', 'pdf'
                            ],
                            destroy: true,
                            "scrollY": "50vh",
                            "scrollCollapse": true,
                            "paging": false,
                            "data": res.historicoVendedor,
                            "columns": [
                                { "data": "vendedor" },
                                { "data": "cedula" },
                                { "data": "call" },
                                { "data": "socio" },
                                { "data": "cedula_socio" },
                                { "data": "tipo_afiliacion" },
                                { "data": "monto_venta" },
                                { "data": "fecha" },
                            ],
                            "language": {
                                url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                                
                            }
                            
                        });
                  
                }else{
                    Swal.fire({
                        icon: 'info',
                        title: 'No hay registros',
                        text: 'Debe ingresar una cédula',
                   
                    });
                }
               
               

               
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.error(error);
        }
    });

}

function productosPorFilial(){
    calcularIndicadores();
    let fecha_desde_hv = $('#fecha_desde').val();
    let fecha_hasta_hv = $('#fecha_hasta').val();
    let filial = $('#filial').val();
    let nombre_filial = $('#filial option:selected').text();

    if (filial=='') {
  
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debe seleccionar una filial',
        });
    }

    $.ajax({
      
        type: 'POST',
        url: 'ajax/productosPorFilial.php',
        data: {
            fecha_desde: fecha_desde_hv,
            fecha_hasta: fecha_hasta_hv,
            filial: filial,
            nombreFilial : nombre_filial
        },
        dataType: 'JSON',
        beforeSend: () => {
            Swal.fire({
                title: 'Cargando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: (res) => {
            Swal.close();
            if (res.result) {
            
                $('#productosPorFilial').DataTable({
                    "dom": 'Bfrtip',
                    buttons: [
                        'excel', 'pdf'
                    ],
                    destroy: true,
                    "scrollY": "50vh",
                    "scrollCollapse": true,
                    "paging": false,
                    "data": res.productosFilial,
                    "columns": [
                        { "data": "producto" },
                        { "data": "filial" },
                        { "data": "total" },
                        { "data": "monto_ventas" },
                    ],
                    "language": {
                        url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                        
                    }
                    
                });
                  
                

            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: (error) =>{
            console.error(error);
        }
    });

}

function monthDiff(dateFrom, dateTo) {
    return dateTo.getMonth() - dateFrom.getMonth() + 
      (12 * (dateTo.getFullYear() - dateFrom.getFullYear()))
}
   

function historicoAltas(){
    calcularIndicadores();
    let fecha_desde = $('#fecha_desde').val();
    let fecha_hasta = $('#fecha_hasta').val();

    let fd = (fecha_desde!='') ? fecha_desde.split('-'): '';
    let fh = (fecha_desde!='') ? fecha_hasta.split('-'): '';
    fd = new Date(fd[2],fd[1]-1);
    fh = new Date(fh[2],fh[1]-1);
    
    if ((fecha_desde!='' && fecha_hasta!='') && monthDiff(new Date(fd),new Date(fh)) != 6) {
  
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debe ingresar un rango de fechas de 6 meses',
        });
    }
    else{
        $.ajax({
      
            type: 'POST',
            url: 'ajax/historicoAltas.php',
            data: {
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta,
              
            },
            dataType: 'JSON',
            beforeSend: () => {
                Swal.fire({
                    title: 'Cargando datos...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: (res) => {
                Swal.close();
                console.log(res);
                if (res.result) {

    
                    $('#tableHistoricoAltas').DataTable({
                        "dom": 'Bfrtip',
                        buttons: [
                            'excel', 'pdf'
                        ],
                        destroy: true,
                        "scrollY": "50vh",
                        "scrollCollapse": true,
                        "paging": false,
                        "data": res.estadisticas,
                        "columns": [
                            { "data": "filial" },
                            { "data": "mes1" },
                            { "data": "mes2" },
                            { "data": "mes3" },
                            { "data": "mes4" },
                            { "data": "mes5" },
                            { "data": "mes6" }
                        ],
                        'columnDefs': [
                            {
                                'title': res.headers.filial,
                                'targets': 0
                            },
                            {
                                'title': res.headers.mes1,
                                'targets': 1
                            }, {
                                'title': res.headers.mes2,
                                'targets': 2
                            }, {
                                'title': res.headers.mes3,
                                'targets': 3
                            }, {
                                'title': res.headers.mes4,
                                'targets': 4
                            }, {
                                'title': res.headers.mes5,
                                'targets': 5
                            }, {
                                'title': res.headers.mes6,
                                'targets': 6
                            }
                        ],
                        "language": {
                            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
                            
                        }
                        
                    });
                      
                   
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            },
            error: (error) =>{
                console.error(error);
            }
        });
    
    }


}

function mostrarGraficos(){
    calcularIndicadores();
    let fecha_desde = $('#fecha_desde').val();
    let fecha_hasta = $('#fecha_hasta').val();

    let fd = (fecha_desde!='') ? fecha_desde.split('-'): '';
    let fh = (fecha_desde!='') ? fecha_hasta.split('-'): '';
    fd = new Date(fd[2],fd[1]-1);
    fh = new Date(fh[2],fh[1]-1);
    console.log(monthDiff(new Date(fd),new Date(fh)));
    if ((fecha_desde!='' && fecha_hasta!='') && monthDiff(new Date(fd),new Date(fh)) != 6) {
  
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debe ingresar un rango de fechas de 6 meses',
        });
    }
    else{
        $.ajax({
      
            type: 'POST',
            url: 'ajax/calcularEstadisticas.php',
            data: {
                fecha_desde: fecha_desde,
                fecha_hasta: fecha_hasta,
              
            },
            dataType: 'JSON',
            beforeSend: () => {
                Swal.fire({
                    title: 'Cargando datos...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: (res) => {
                Swal.close();
                console.log(res);
                if (res.result) {
                    let altas = res.estadisticas.altas;
                    let incrementos = res.estadisticas.incrementos;
                    let bajas = res.estadisticas.bajas;
                    var areaChartOptions = {
                        maintainAspectRatio : false,
                        responsive : true,
                        legend: {
                          display: false
                        },
                        scales: {
                          xAxes: [{
                            gridLines : {
                              display : false,
                            }
                          }],
                          yAxes: [{
                            gridLines : {
                              display : false,
                            }
                          }]
                        }
                    
                    }
                    var areaChartData = {
                        labels  : [res.headers.mes1, res.headers.mes2, res.headers.mes3, res.headers.mes4, res.headers.mes5, res.headers.mes6],
                        datasets: [
                          {
                            label               : 'ALTAS',
                            backgroundColor     : 'rgba(30,196,12,1)',
                            borderColor         : 'rgba(30,196,12,1)',
                            pointRadius          : false,
                            pointColor          : '#3b8bba',
                            pointStrokeColor    : 'rgba(30,196,12,1)',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(30,196,12,1)',
                            data                : [altas[0],altas[1],altas[2], altas[3], altas[4], altas[5]]
                          },
                          {
                            label               : 'INCREMENTOS',
                            backgroundColor     : 'rgba(60,141,188,0.9)',
                            borderColor         : 'rgba(60,141,188,0.8)',
                            pointRadius          : false,
                            pointColor          : '#3b8bba',
                            pointStrokeColor    : 'rgba(60,141,188,1)',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(60,141,188,1)',
                            data                : [incrementos[0],incrementos[1],incrementos[2], incrementos[3], incrementos[4], incrementos[5]]
                          },
                          {
                            label               : 'BAJAS',
                            backgroundColor     : 'rgba(249, 112, 112, 1)',
                            borderColor         : 'rgba(249, 112, 112, 1)',
                            pointRadius         : false,
                            pointColor          : 'rgba(249, 112, 112, 1)',
                            pointStrokeColor    : '#F97070',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(181,22,22,1)',
                            data                : [bajas[0],bajas[1],bajas[2], bajas[3], bajas[4], bajas[5]]
                          },
                        ]
                    }
                        
                    var barChartCanvas = $('#barChart').get(0).getContext('2d')
                    var barChartData = $.extend(true, {}, areaChartData)
                    var temp0 = areaChartData.datasets[0]
                    var temp1 = areaChartData.datasets[1]
                
                    barChartData.datasets[0] = temp0
                    barChartData.datasets[1] = temp1
                
                
                    var barChartOptions = {
                      responsive              : true,
                      maintainAspectRatio     : false,
                      datasetFill             : false
                    }
                
                    new Chart(barChartCanvas, {
                      type: 'bar',
                      data: barChartData,
                      options: barChartOptions
                    })
    
                  
                   
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            },
            error: (error) =>{
                console.error(error);
            }
        });
    
    }


 

}

$('.solo_numeros').keydown(soloNumeros);


/**
 * soloNumeros
 * Permite el ingreso unicamente de números
 * @param e {Object}
 * @return {boolean|undefined}
 */
function soloNumeros(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if (
      $.inArray(e.keyCode, [46, 8, 9, 27, 13, 40]) !== -1 ||
      // Allow: home, end, left, right
      (e.keyCode >= 35 && e.keyCode <= 39)
    ) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
    if (e.altKey) {
      return false;
    }
  }

/**
 * comprobarCI
 * Realiza la validación de una cédula de identidad, aplicando el algoritmo que calcula el digito verificador
 * @param cedi {number}
 * @return {boolean}
 */
function comprobarCI(cedi){
    if (cedi.length >= 7) {
      //Inicializo los coefcientes en el orden correcto
      let arrCoefs = [2, 9, 8, 7, 6, 3, 4, 1];
      let suma = 0;
      //Para el caso en el que la CI tiene menos de 8 digitos
      //calculo cuantos coeficientes no voy a usar
      let difCoef = parseInt(arrCoefs.length - cedi.length);
      //let difCoef = parseInt(arrCoefs.length – ci.length);
      //recorro cada digito empezando por el de más a la derecha
      //o sea, el digito verificador, el que tiene indice mayor en el array
      for (let i = cedi.length - 1; i > -1; i--) {
        //for (let i = ci.length – 1; i > -1; i–) {
        //ooObtengo el digito correspondiente de la ci recibida
        let dig = cedi.substring(i, i + 1);
        //Lo tenía como caracter, lo transformo a int para poder operar
        let digInt = parseInt(dig);
        //Obtengo el coeficiente correspondiente al ésta posición del digito
        let coef = arrCoefs[i + difCoef];
        //Multiplico dígito por coeficiente y lo acumulo a la suma total
        suma = suma + digInt * coef;
      }
      // si la suma es múltiplo de 10 es una ci válida
      if (suma % 10 == 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }
  