
$.ajaxSetup({
  headers: {
    'app-nombre_url': getUrl('nombre'),
    'app-token': getUrl('token'),
  },
  /*
   data: {
     nombre_url: getUrl('nombre'),
     token: getUrl('token'),
    }
    */
});

async function http(url, method, data, cargando = false) {
  return new Promise(function (resolve, reject) {
    if (cargando !== false) showLoading(cargando || 'Cargando');
    $.ajax({
      type: method,
      url: `${url_api}/${url}`,
      data: data,
      dataType: "JSON",
      success: async (response) => {
        if (cargando !== false) hideLoading();
        return resolve(response);
      }, error: (err) => {
        error("Hubo error al realizar la solicitud");
        console.log(err);
        if (cargando !== false) hideLoading();
        reject(false);
      }
    });
  });
}


