//#region Types
/**
 * @typedef {{idServicio: number, descuento: number, porcentaje: bool, mensaje: string }} Descuento
 */
//#endregion

/**
 * Recibe y filtra los convenios especiales disponibles dependiendo de los datos ingresados

 * @returns {void}
 */
function listarConveniosEspeciales() {
  $.ajax({
    url: './Ajax/listarConveniosEspecialesActivos.php',
    dataType: 'JSON',
    /** @param {[]} conveniosActivos */
    success: function (conveniosActivos) {
      // #region Filtrar Convenios
      const productosSeleccionados = $arrServicios.map(
        (_servicio) => _servicio[0]
      );
      const deparatamentoSeleccionado = $('#depBen :selected').text();
      const competenciaSeleccionado =
        $('[name=dato_adicional]:checked').val() === '1';

      conveniosActivos = conveniosActivos.filter(
        ({ id }) =>
          !conveniosEspecialesNoCompatiblesConDepartamentos(
            deparatamentoSeleccionado
          ).includes(Number(id))
      );

      conveniosActivos = conveniosActivos.filter(({ id }) => {
        let convenioValido = true;

        productosSeleccionados.forEach((producto) => {
          if (convenioValido) {
            const filtro =
              conveniosEspecialesNoCompatiblesConProductos(producto);

            if (
              filtro.includes('*') ||
              conveniosEspecialesNoCompatiblesConProductos(producto).includes(
                Number(id)
              )
            )
              convenioValido = false;
          }
        });

        return convenioValido;
      });

      if (competenciaSeleccionado)
        conveniosActivos = conveniosActivos.filter(
          ({ id }) =>
            !conveniosEspecialesNoCompatiblesConCompetencia().includes(
              Number(id)
            )
        );
      // #endregion

      const addOption = (value, text) =>
        `<option value="${value}">${text}</option>`;

      const select = $('#convenio_especial');
      let options = '';

      if (conveniosActivos.length === 0)
        options += addOption('', 'Sin convenios disponibles');
      else {
        options += addOption('', 'No aplica');

        conveniosActivos.forEach(
          ({ id, nombre }) => (options += addOption(id, nombre))
        );
      }

      select.empty();
      select.append(options);
    },
  });
}

/**
 * Modifica el array global $arrServicios para agregarle los descuentos dependiendo de los convenios especiales seleccionados

 * @returns {void}
 */
function aplicarDescuentosConveniosEspeciales() {
  const convenioEspecial = $('#convenio_especial').val();

  if (convenioEspecial === '') return;

  const descuentos = descuentosConveniosEspeciales(convenioEspecial);
  const serviciosInlcuidos = descuentos.map(
    (descuento) => descuento.idServicio
  );
  let total = 0;

  const arrServicios = $arrServicios.map((servicio) => {
    if (!serviciosInlcuidos.includes(Number(servicio[0]))) {
      total += servicio[2];
      return servicio;
    }

    const { descuento, porcentaje, mensaje } = descuentos.filter(
      ({ idServicio }) => idServicio === Number(servicio[0])
    )[0];

    //TODO - Corregir el redondeo si es más de una hora

    const precioTotalServicioSinDescuento = servicio[8];
    const precioPrimerasOchoHorasSinDescuento = servicio[9];
    const precioHorasExtrasSinDescuento = servicio[10];

    const cantidadTotalDescontar = porcentaje
      ? (precioTotalServicioSinDescuento * descuento) / 100
      : descuento;
    const cantidadPrimerasOchoHorasDescontar = porcentaje
      ? (precioPrimerasOchoHorasSinDescuento * descuento) / 100
      : descuento;
    const cantidadHorasExtrasDescontar = porcentaje
      ? (precioHorasExtrasSinDescuento * descuento) / 100
      : descuento;

    servicio[2] = Math.round(
      precioTotalServicioSinDescuento - cantidadTotalDescontar
    );
    servicio[3] = Math.round(
      precioPrimerasOchoHorasSinDescuento - cantidadPrimerasOchoHorasDescontar
    );
    servicio[4] = Math.round(
      precioHorasExtrasSinDescuento - cantidadHorasExtrasDescontar
    );

    window.$productos += `<p><span class='text-primary'>${servicio[7]} (${mensaje}):</span> $U ${servicio[2]}</p>`;

    total += servicio[2];
    return servicio;
  });

  window.$total = total;
  window.$arrServicios = arrServicios;
}

/**
 * Agregar en la @constant conveniosEspeciales manualmente todos los descuentos de convenios especiales
 * Utilizar el formato id_servicio: Descuentos[] es importante para la coherencia del programa
 *
 * @param {number} idConvenio
 * @returns {Descuento[]}
 */
function descuentosConveniosEspeciales(idConvenio) {
  /**
   * Utilizar esta función para mantener coherencia en los descuentos

   * @param {number} idServicio ID Servicio al que se le aplica descuento
   * @param {number} descuento Valor que se descontará
   * @param {bool} porcentaje Si el descuento es porcentaje o fijo
   * @param {string} mensaje

  * @returns {Descuento}
   */
  const descuento = (idServicio, descuento, porcentaje, mensaje) => {
    return { idServicio, descuento, porcentaje, mensaje };
  };

  const conveniosEspeciales = {
    1: [
      descuento(1, 10, true, '10% descuento Odontos Red'),
      descuento(2, 10, true, '10% descuento Odontos Red'),
    ],
    2: [
      descuento(1, 10, true, '10% descuento SOEM'),
      descuento(2, 10, true, '10% descuento SOEM'),
    ],
  };

  return conveniosEspeciales[idConvenio] || [];
}

//#region Filtros
/**
 * Devuelve los ids de los convenios especiales no compatibles con cada producto
 * En caso de no tener incompatibilidad con algún convenio no es necesario agregar el producto al array
 * Agregar a la @constant noCompatibles en el siguiente formato: id_servicio: id_convenio_especial[]
 *
 * @param {number} id_producto
 * @returns {number[] | string[]}
 */
function conveniosEspecialesNoCompatiblesConProductos(id_producto) {
  const noCompatibles = {
    139: ['*'],
  };

  return noCompatibles[id_producto] || [];
}

/**
 * Devuelve los ids de los convenios especiales no compatibles con promo competencias
 * Los convenios especiales están en la tabla 192.168.1.13\call.convenios_especiales
 * Agregar el id del convenio especial incompatible al array del return
 *
 * @returns {number[]}
 * */
function conveniosEspecialesNoCompatiblesConCompetencia() {
  return [1, 2];
}

/**
 * Devuelve los ids de los convenios especiales no compatibles con los departamentos
 * Los convenios especiales están en la tabla 192.168.1.13\call.convenios_especiales
 * Agregar el id del convenio especial incompatible al array correspondiente al departamento
 * Únicamente modifcar la @constant noCompatibles, no modificar la asignación por id departamentos
 *
 * @param {string} departamento
 *
 * @returns {number[]}
 * */
function conveniosEspecialesNoCompatiblesConDepartamentos(departamento) {
  const noCompatibles = {
    Artigas: [],
    Canelones: [],
    'Cerro Largo': [],
    Colonia: [],
    Durazno: [],
    Flores: [],
    Florida: [],
    Lavalleja: [],
    Maldonado: [],
    Montevideo: [1],
    Paysandú: [],
    'Río Negro': [],
    Rivera: [],
    Rocha: [],
    Salto: [],
    'San José': [],
    Soriano: [],
    Tacuarembó: [],
    'Treinta y tres': [],
  };

  // #region Asignación por Id de departamento
  noCompatibles[1] = noCompatibles['Montevideo'];
  noCompatibles[2] = noCompatibles['Salto'];
  noCompatibles[3] = noCompatibles['Paysandú'];
  noCompatibles[4] = noCompatibles['Maldonado'];
  noCompatibles[5] = noCompatibles['Rivera'];
  noCompatibles[6] = noCompatibles['Tacuarembó'];
  noCompatibles[7] = noCompatibles['Cerro Largo'];
  noCompatibles[8] = noCompatibles['Artigas'];
  noCompatibles[9] = noCompatibles['Soriano'];
  noCompatibles[10] = noCompatibles['Lavalleja'];
  noCompatibles[11] = noCompatibles['San José'];
  noCompatibles[12] = noCompatibles['Durazno'];
  noCompatibles[13] = noCompatibles['Florida'];
  noCompatibles[14] = noCompatibles['Treinta y tres'];
  noCompatibles[15] = noCompatibles['Colonia'];
  noCompatibles[16] = noCompatibles['Rocha'];
  noCompatibles[17] = noCompatibles['Río Negro'];
  noCompatibles[18] = noCompatibles['Flores'];
  noCompatibles[19] = noCompatibles['Canelones'];
  //#endregion

  return noCompatibles[departamento] || [];
}
//#endregion
