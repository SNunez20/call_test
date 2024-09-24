
/* PARTIAL/VIEWS*/
function cargarPartial(partial) {
  $.get(`${dir_partials}${partial}.html`, function (data) {
    $(`#${partial}`).html(data);
  });
}

function cargarPartialAsync(partial, id_div = false) {
  return new Promise((resolve, reject) => {
    let div = id_div != false ? id_div : partial;
    $.get(`${dir_partials}${partial}.html`, function (data) {
      $(`#${div}`).html(data);
      resolve(true);
    });
  });

}


function cargarPartialViewsAsync(partial, id_div = false) {
  let div = id_div != false ? id_div : partial;
  $.get(`${url_app}/${dir_views}${partial}.html`, (data) => { $(`#${div}`).html(data); });
}

function partialAsync(partial) {
  return new Promise((resolve, reject) => {
    $.get(`${url_app}/${dir_partials}${partial}.html`, (data) => { resolve(data) });
  });
}

function cargarViewAsync(view, id) {
  return new Promise(function (resolve, reject) {
    $.get(`${dir_views}/${partial}.html`, function (data) {
      $(`#${id}`).html(data);
      resolve();
    });
  });
}
  /* PARTIAL/VIEWS*/
