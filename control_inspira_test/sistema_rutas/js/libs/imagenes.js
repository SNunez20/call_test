/* IMAGENES */
function quitarImagenes(imagen, event) {
    event.preventDefault();
    $(`#${imagen}`).val('');
  
    function agregarImagen(imagenes) {
      for (let imagen of imagenes) {
        $(`#${imagen.div}`).attr("src", `${url_imagenes}${imagen.nombre}`);
  
      }
  
    }
  
  }
  /* IMAGENES */