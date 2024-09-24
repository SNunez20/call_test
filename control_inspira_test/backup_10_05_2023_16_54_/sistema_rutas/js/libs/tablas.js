
/* VER MAS TABLA */
function verMasTabla(event, descripcion_ver_mas) {
    event.preventDefault();
    $('#descripcion_ver_mas').html(descripcion_ver_mas.replace(/\n/g, '<br />'));
    $('#modalVerMas').modal('show');
  }
  /* VER MAS TABLA */
  
  /*  FUNCION TABLA   */
  
  function tabla(div, url, datos, columnsTable, recargar = false, function_Call_Back) {
    let columns = [];
    columnsTable.map((column) => {
      columns.push({ data: column },);
    });
  
    let tabla = $(`#${div}`).DataTable({
      "drawCallback": function (settings) {
        function_Call_Back();
      },
      processing: true,
      serverMethod: 'post',
      searching: true,
      ajax: {
        url: `${url_api}${url}`,
        data: datos
      },
      columns,
      language: { url: `${url_app}js/libs/config_tabla.json` },
  
      retrieve: true,
      order: [[0, 'desc']],
      responsive: true,
      autoWidth: false,
      pageLength: 10
    });
    if (recargar == true) {
      tabla.ajax.reload();
    }
  }
  
  
  function tablaAsync(div, url, datos, columnsTable, recargar = false, funcion_Call_Back) {
    return new Promise((resolve, reject) => {
      let columns = [];
      columnsTable.map((column) => {
        columns.push({ data: column },);
      });
      let tabla = $(`#${div}`).DataTable({
        "drawCallback": function (settings) {
          funcion_Call_Back();
        },
        processing: true,
        serverMethod: 'post',
        searching: true,
        ajax: {
          url: `${url_api}${url}`,
          data: datos
        },
        columns,
        language: { url: `${url_app}js/libs/config_tabla.json` },
  
        retrieve: true,
        order: [[0, 'desc']],
        responsive: true,
        autoWidth: false,
        pageLength: 10
      });
      if (recargar == true) {
        tabla.ajax.reload();
      }
      resolve(true);
    });
  }
  
  /*  FUNCION TABLA  */
  
  