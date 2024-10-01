/*   MODAL */
function cerrarModal(div) {
    $(`#${div}`).modal('hide');
  }
  
  function abrirModal(div) {
    $(`#${div}`).modal('show');
  }
  
  function abrirModalStatic(div) {
    $(`#${div}`).modal({ backdrop: 'static', keyboard: false }).modal('show');
  }
  
 
  