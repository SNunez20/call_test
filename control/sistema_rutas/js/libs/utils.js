
/* PRIMERA LETRA A MAYUSCULAS */
function primeraLetraAMayusculas(cadena) {
  return cadena.charAt(0).toUpperCase().concat(cadena.substring(1, cadena.length));
}
/* PRIMERA LETRA A MAYUSCULAS */

/* SCROLL */
function scroll(div) {
  $('html, body').animate({
    scrollTop: $(`#${div}`).offset().top
  });
}
/* SCROLL */

function remplazar(div, datos) {
  return new Promise(function (resolve, reject) {
    for (let key in datos) {
      if ($(`${div}:contains('{{${key}}}')`)) {
        let regex = new RegExp(`{{${key}}}`, 'gi');
        $(`${div}`).html($(`${div}`).html().replace(regex, datos[key]));
      }
    }
  });
}



async function verMas(event, descripcion_ver_mas) {
  event.preventDefault();
  await partialAsync('ver_mas_modal');
  $('#descripcion_ver_mas').html(descripcion_ver_mas.replace(/\n/g, '<br />'));
  $('#modalVerMas').modal('show');
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}