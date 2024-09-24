/* INPUTS FUNCIONES */
function limpiarCampos(arrayInputs) {
    arrayInputs.forEach((input) => {
      $(`#${input}`).val('');
    });
  }
  
function deshabilitarBoton(idBtn) {
    $(`#${idBtn}`).prop('disabled', true);
  }
  
  function habilitarBoton(idBtn) {
    $(`#${idBtn}`).removeAttr('disabled');
  }
  
  function mostrarErrorInput(div, mensaje) {
    $(`${div}`).html(`<div style='color:red'> ${mensaje}</div>`);
  }
  function ocultarErrorInput(div) {
    $(`${div}`).html('');
  }
  
  function serializeForm(form) {
    let formserialize = {};
    const form_array = $(`${form}`).serializeArray();
    form_array.map((data) => {
      formserialize[data.name] = data.value;
    });
    return formserialize;
  }