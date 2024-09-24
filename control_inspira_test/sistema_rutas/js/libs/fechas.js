
/* FECHA ACTUAL */
function fechaActual() {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const dia = fecha.getDate();
    const mes = fecha.getMonth() + 1;
  
    return { dia: dia, mes: mes, anio: anio };
  }
  
  function setFechaActualMayor(input) {
    let fecha_actual = fechaActual();
    $(`${input}`).attr({
      max: `${fecha_actual.anio - 18}-${fecha_actual.mes}-${fecha_actual.dia}`,
      min: `${fecha_actual.anio - 120}-${fecha_actual.mes}-${fecha_actual.dia}`
    });
  }
  /* FECHA ACTUAL */

  
  function selectFechasForm() {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    for (let i = anio; i <= anio + 15; i++) {
      $('#anio_vencimiento').append(` <option value="${i}">${i}</option>`);
    }
  
    for (let j = 1; j <= 12; j++) {
      $('#mes_vencimiento').append(` <option value="0${j}">${j}</option>`);
    }
  }
  