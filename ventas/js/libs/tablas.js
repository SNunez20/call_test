
/* VER MAS TABLA */
function verMasTabla(event, descripcion_ver_mas) {
  event.preventDefault();
  $('#descripcion_ver_mas').html(descripcion_ver_mas.replace(/\n/g, '<br />'));
  $('#modalVerMas').modal('show');
}
/* VER MAS TABLA */

/*  FUNCION TABLA   */
function tablaNormal(div, url_ajax, datos, columnsTable, recargar = false, function_Call_Back, order = 0) {
  let columns = [];
  columnsTable.map((column) => {
    columns.push({ data: column },);
  });

  let order_table = [[0, 'desc']];
  if (Number.isInteger(order)) order_table = [[order, 'desc']];
  else order_table = order;

  const config_tabla = {
    processing: false,
    serverMethod: 'POST',
    searching: true,
    ajax: {
      url: url_ajax,
      data: datos
    },
    columns,
    language: { url: `js/libs/config_tabla.json` },
    retrieve: true,
    order: order_table,
    responsive: true,    
    processing:true,
    autoWidth: false,
    pageLength: 10,
    dom: 'Bfrtip',
    buttons: [
      'excelHtml5',
    ]
  };

  let tabla_r = $(`#${div}`).DataTable(config_tabla);
  if (recargar == true) {
    tabla_r.ajax.url(url_ajax);
    tabla_r.ajax.reload();
  }

}


function tablaAsync(div, url_ajax, datos, columnsTable, recargar = false, function_Call_Back, order = 0) {
  return new Promise((resolve)=>{
    let columns = [];
    columnsTable.map((column) => {
      columns.push({ data: column },);
    });
  
    let order_table = [[0, 'desc']];
    if (Number.isInteger(order)) order_table = [[order, 'desc']];
    else order_table = order;
  
    const config_tabla = {
      processing: false,
      serverMethod: 'POST',
      searching: true,
      ajax: {
        url: url_ajax,
        data: datos
      },
      columns,
      language: { url: `js/libs/config_tabla.json` },
      retrieve: true,
      order: order_table,
      responsive: true,
      processing:true,
      autoWidth: false,
      pageLength: 10,
      dom: 'Bfrtip',
      buttons: [
        'excelHtml5',
      ]
    };
  
    let tabla_r = $(`#${div}`).DataTable(config_tabla);

    resolve(true);
    if (recargar == true) {
      tabla_r.ajax.url(url_ajax);
      tabla_r.ajax.reload();
      resolve(true);
    }
  });


}
/*  FUNCION TABLA  */

