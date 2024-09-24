<?php
require_once "../../logger.php";

function buscarCelular($numeros)
{
  preg_match_all('/(09)[1-9]{1}\d{6}/x', $numeros, $respuesta);

  $respuesta = (count($respuesta[0]) !== 0)
    ? $respuesta[0]
    : false;

  return $respuesta;
}

/**
 * Undocumented function
 *
 * @param [type] $id_cliente
 * @param [type] $mysqli
 * @param mysqli $mysqli250
 * @param [type] $cedulaAfiliado
 * @return void
 */
function guardarPadron($id_cliente, $mysqli, $mysqli250, $cedulaAfiliado)
{
  $error             = false;
  $result            = false;
  $qDatos            = "SELECT * FROM padron_datos_socio WHERE id = '$id_cliente'";
  $rDatos            = mysqli_query($mysqli, $qDatos);
  $accion            = null;
  $datos_socio       = [];
  $retorno_servicios = false;
  $qActualizar       = 'none';
  $grupo_familiar    = false;
  $omt               = false;

  if ($rDatos) {
    while ($row = mysqli_fetch_assoc($rDatos)) {
      $accion       = $row['accion'];
      $idUser       = $row['id_usuario'];
      $alta         = $row['alta'];
      $metodo_pago  = $row['metodo_pago'];
      $origen_venta = $row['origen_venta'];
      $estadoActual = $row['estado'];
      $idGrupoVendedor = obtenerGrupoUsuario($idUser);

      if ($origen_venta == 5) $origenVta = 'UDEMM';
      else if (($idGrupoVendedor) == 10013) $origenVta = 'UCEM';
      else $origenVta = '';

      $datos_socio = array(
        'nombre'                => $row['nombre'],
        'tel'                   => $row['tel'],
        'cedula'                => $row['cedula'],
        'direccion'             => addslashes($row['direccion']),
        'sucursal'              => $row['sucursal'],
        'ruta'                  => $row['ruta'],
        'radio'                 => $row['radio'],
        'activo'                => $row['activo'],
        'fecha_nacimiento'      => $row['fecha_nacimiento'],
        'edad'                  => $row['edad'],
        'tarjeta'               => $row['tarjeta'],
        'tipo_tarjeta'          => $row['tipo_tarjeta'],
        'numero_tarjeta'        => $row['numero_tarjeta'],
        'nombre_titular'        => $row['nombre_titular'],
        'cedula_titular'        => $row['cedula_titular'],
        'telefono_titular'      => $row['telefono_titular'],
        'anio_e'                => $row['anio_e'],
        'mes_e'                 => $row['mes_e'],
        'sucursal_cobranzas'    => $row['sucursal_cobranzas'],
        'sucursal_cobranza_num' => $row['sucursal_cobranza_num'],
        'empresa_marca'         => $row['empresa_marca'],
        'flag'                  => $row['flag'],
        '`count`'               => $row['count'],
        'observaciones'         => mysqli_real_escape_string($mysqli250, $row['observaciones']),
        'grupo'                 => $row['grupo'],
        'idrelacion'            => $row['idrelacion'],
        'empresa_rut'           => $row['empresa_rut'],
        'total_importe'         => $row['total_importe'],
        'nactual'               => $row['nactual'],
        'version'               => $row['version'],
        'flagchange'            => $row['flagchange'],
        'rutcentralizado'       => $row['rutcentralizado'],
        'PRINT'                 => $row['PRINT'],
        'EMITIDO'               => $row['EMITIDO'],
        'movimientoabm'         => $row['movimientoabm'],
        'abm'                   => $row['abm'],
        'abmactual'             => $row['abmactual'],
        '`check`'               => '1',
        'usuario'               => $row['usuario'],
        'usuariod'              => $row['usuariod'],
        'fechafil'              => $row['fechafil'],
        'radioViejo'            => $row['radioViejo'],
        'extra'                 => $row['extra'],
        'nomodifica'            => $row['nomodifica'],
        'origenVta'             => $origenVta,
        'imp_desc'              => '',
      );
    }
    logger("[OK]: RECUPERAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
  } else if ($error = mysqli_error($mysqli)) {
    logger("[ERROR]: $error [CI]: $cedulaAfiliado");
    $error = true;
  }

  if (!$error) {

    $cedula          = $datos_socio['cedula'];
    $qServicios      = "SELECT * FROM padron_producto_socio where cedula = '$cedula' AND accion='1'";
    $rServicios      = mysqli_query($mysqli, $qServicios);
    $servicios_socio = [];

    if ($rServicios) {
      while ($row = mysqli_fetch_assoc($rServicios)) {

        // COMPRUEBA SI TIENE GRUPO FAMILIAR
        if ($row["servicio"] == 63 || $row["servicio"] == 65) {
          $grupo_familiar = true;
        }

        $servicios_socio[] = array(
          'cedula'                 => $row['cedula'],
          'servicio'               => $row['servicio'],
          'hora'                   => $row['hora'],
          'importe'                => $row['importe'],
          'cod_promo'              => $row['cod_promo'],
          'fecha_registro'         => $row['fecha_registro'],
          'numero_contrato'        => $row['numero_contrato'],
          'fecha_afiliacion'       => $row['fecha_afiliacion'],
          'nombre_vendedor'        => $row['nombre_vendedor'],
          'observaciones'          => mysqli_real_escape_string($mysqli250, $row['observaciones']),
          'lugar_venta'            => $row['lugar_venta'],
          'vendedor_independiente' => $row['vendedor_independiente'],
          'activo'                 => $row['activo'],
          'movimiento'             => $row['movimiento'],
          'fecha_inicio_derechos'  => $row['fecha_inicio_derechos'],
          'numero_vendedor'        => $row['numero_vendedor'],
          'keepprice1'             => $row['keepprice1'],
          'promoactivo'            => $row['promoactivo'],
          'tipo_de_cobro'          => $row['tipo_de_cobro'],
          'tipo_iva'               => $row['tipo_iva'],
          'idrelacion'             => $row['idrelacion'],
          'codigo_precio'          => $row['codigo_precio'],
          'aumento'                => $row['aumento'],
          'empresa'                => $row['empresa'],
          'nactual'                => $row['nactual'],
          'servdecod'              => $row['servdecod'],
          'count'                  => $row['count'],
          'version'                => $row['version'],
          'abm'                    => $row['abm'],
          'abmactual'              => $row['abmactual'],
          'usuario'                => $row['usuario'],
          'usuarioid'              => $row['usuariod'],
          'extra'                  => $row['extra'],
          'nomodifica'             => $row['nomodifica'],
          'precioOriginal'         => $row['precioOriginal'],
          'abitab'                 => $row['abitab'],
          'cedula_titular_gf'      => $row['cedula_titular_gf'],
        );
      }
      logger("[OK]: RECUPERAR [TABLE] padron_producto_socio: [CI]: $cedulaAfiliado", false);
    } else if ($error = mysqli_error($mysqli)) {
      logger("[ERROR]: $error [CI]: $cedulaAfiliado");
      $error = true;
    }
  }

  if (!$error) {
    if ($accion == '1') { //insertar nuevo socio en el padron
      $campos        = array_keys($datos_socio);
      $qInsertar     = "INSERT INTO padron_datos_socio VALUES(null,'" . implode("','", $datos_socio) . "')";
      $retorno_datos = mysqli_query($mysqli250, $qInsertar);
      if (!$retorno_datos) {
        $error = mysqli_error($mysqli250);
        logger("[ERROR AL GUARDAR DATOS DATOS]: $error [CI]: $cedulaAfiliado - QUERY:" . $qInsertar);
        $error = true;
      } else {
        $idSocioPadron = mysqli_insert_id($mysqli250); //dir2
        logger("[OK]: GUARDAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
      }
    } else if ($accion == '4' || $accion == '3') { //actualizar datos del socio en el padron
      $qActualizar = "UPDATE padron_datos_socio SET ";
      $data        = array();
      foreach ($datos_socio as $column => $value) {
        $data[] = $column . "=" . "'" . $value . "'";
      }
      $qActualizar .= implode(',', $data);
      $qActualizar .= " where cedula = '" . $datos_socio['cedula'] . "'";
      $retorno_datos = (mysqli_query($mysqli250, $qActualizar)) ? true : false;

      if (!$retorno_datos) {
        $error = mysqli_error($mysqli250);
        logger("[ERROR AL ACTUALIZAR DATOS EN PADRON]: $error" . "CI: $cedulaAfiliado - QUERY: $qActualizar");
        $error = true;
      } else {
        logger("[OK]: ACTUALIZAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
      }
    } else {
      logger("ACCION: $accion - CI: $cedulaAfiliado");
      $error = true;
    }
  }

  if (!$error) {
    $totalIncremento = 0;
    // insertamos los servicios nuevos en el padron
    for ($i = 0; $i < count($servicios_socio); $i++) {
      $campos = array_keys($servicios_socio[$i]);
      $totalIncremento += (int) $servicios_socio[$i]['importe'];
      $qInsertarServicios = "INSERT INTO  padron_producto_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_socio[$i]) . "')";
      $result             = mysqli_query($mysqli250, $qInsertarServicios);
      $retorno_servicios  = ($result) ? true : false;
      if (!$retorno_servicios) {
        $error = mysqli_error($mysqli250);
        logger("[ERROR AL GUARDAR PRODUCTOS]: $error [CI]: $cedulaAfiliado");
        $error = true;
      } else {
        logger("[OK]: GUARDAR [TABLE] padron_producto_socio [CI]: $cedulaAfiliado", false);
      }
    }

    if ($retorno_servicios) {
      // ACTUALIZA LA ACCION A 5 TANTO EN DATOS COMO PRODUCTOS PARA INDICAR QUE PASO A PADRON
      $qActualizarAccionDatos = "UPDATE padron_datos_socio set accion = '5' where cedula = $cedulaAfiliado";
      $result                 = mysqli_query($mysqli, $qActualizarAccionDatos);

      if (!$result) {
        $error = mysqli_error($mysqli);
        logger("[ERROR AL ACTUALIZAR ACCION EN DATOS]: $error C.I: $cedulaAfiliado");
        $error = true;
      } else {
        logger("[OK]: ACTUALIZACION accion [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
      }

      $qActualizarAcccionProductos = "UPDATE padron_producto_socio set accion='5' where cedula = $cedulaAfiliado";
      $result                      = mysqli_query($mysqli, $qActualizarAcccionProductos);

      if (!$result) {
        logger(mysqli_error($mysqli));
        $error = true;
      } else {
        logger("[OK]: ACTUALIZACION accion [TABLE] padron_producto_socio [CI]: $cedulaAfiliado", false);
      }
    }

    // ! #################################################################################
    // ! COMPRUEBA SI EXISTE GRUPO FAMILIAR PARA RECUPERAR LOS DATOS DE LOS BENEFICIARIOS#
    // ! #################################################################################
    if ($grupo_familiar) {
      // GRUPO FAMILIAR
      $cedulasBenficiarios = [];

      // RECUPERA TODOS LOS BENEFICIARIOS
      $query = "SELECT * FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'";
      if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
          $ciBeneficiario = $row['cedula'];
          // SERVICIOS DEL BENEFICIARIO
          $servicios_beneficiario = array(
            'cedula'                 => $row['cedula'],
            'servicio'               => $row['servicio'],
            'hora'                   => $row['hora'],
            'importe'                => $row['importe'],
            'cod_promo'              => $row['cod_promo'],
            'fecha_registro'         => $row['fecha_registro'],
            'numero_contrato'        => $row['numero_contrato'],
            'fecha_afiliacion'       => $row['fecha_afiliacion'],
            'nombre_vendedor'        => $row['nombre_vendedor'],
            'observaciones'          => $row['observaciones'],
            'lugar_venta'            => $row['lugar_venta'],
            'vendedor_independiente' => $row['vendedor_independiente'],
            'activo'                 => $row['activo'],
            'movimiento'             => $row['movimiento'],
            'fecha_inicio_derechos'  => $row['fecha_inicio_derechos'],
            'numero_vendedor'        => $row['numero_vendedor'],
            'keepprice1'             => $row['keepprice1'],
            'promoactivo'            => $row['promoactivo'],
            'tipo_de_cobro'          => $row['tipo_de_cobro'],
            'tipo_iva'               => $row['tipo_iva'],
            'idrelacion'             => $row['idrelacion'],
            'codigo_precio'          => $row['codigo_precio'],
            'aumento'                => $row['aumento'],
            'empresa'                => $row['empresa'],
            'nactual'                => $row['nactual'],
            'servdecod'              => $row['servdecod'],
            'count'                  => $row['count'],
            'version'                => $row['version'],
            'abm'                    => $row['abm'],
            'abmactual'              => $row['abmactual'],
            'usuario'                => $row['usuario'],
            'usuarioid'              => $row['usuariod'],
            'extra'                  => $row['extra'],
            'nomodifica'             => $row['nomodifica'],
            'precioOriginal'         => $row['precioOriginal'],
            'abitab'                 => $row['abitab'],
            'cedula_titular_gf'      => $row['cedula_titular_gf'],
          );

          $campos = array_keys($servicios_beneficiario);
          $query  = "INSERT INTO padron_producto_socio(" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_beneficiario) . "')";

          if ($result2 = mysqli_query($mysqli250, $query)) {
            logger("[OK]: GUARDAR [TABLE] padron_producto_socio - BENEFICIARIO $ciBeneficiario", false);
          } else {
            $error = mysqli_error($mysqli250);
            logger("[ERROR AL GUARDAR DATOS BENEFICIARIO]: $error CI: $ciBeneficiario");
          }

          // GUARDO LAS CEDULAS DE LOS BENEFICIARIOS
          if (!in_array($ciBeneficiario, $cedulasBenficiarios)) {
            array_push($cedulasBenficiarios, $ciBeneficiario);
          }
        }
      }

      // GUARDA LOS DATOS DE LOS BENEFICIARIOS
      if (count($cedulasBenficiarios) > 0) {
        foreach ($cedulasBenficiarios as $key => $ci) {
          // DATOS DEL BENEFICIARIO
          $query  = "SELECT * FROM padron_datos_socio WHERE cedula = '$ci'";
          $result = mysqli_query($mysqli, $query);

          if (mysqli_num_rows($result) > 0) {
            $row                = mysqli_fetch_assoc($result);
            $datos_beneficiario = array(
              'nombre'                => $row['nombre'],
              'tel'                   => $row['tel'],
              'cedula'                => $ci,
              'direccion'             => addslashes($row['direccion']),
              'sucursal'              => $row['sucursal'],
              'ruta'                  => $row['ruta'],
              'radio'                 => $row['radio'],
              'activo'                => $row['activo'],
              'fecha_nacimiento'      => $row['fecha_nacimiento'],
              'edad'                  => $row['edad'],
              'tarjeta'               => $row['tarjeta'],
              'tipo_tarjeta'          => $row['tipo_tarjeta'],
              'numero_tarjeta'        => $row['numero_tarjeta'],
              'nombre_titular'        => $row['nombre_titular'],
              'cedula_titular'        => $row['cedula_titular'],
              'telefono_titular'      => $row['telefono_titular'],
              'anio_e'                => $row['anio_e'],
              'mes_e'                 => $row['mes_e'],
              'sucursal_cobranzas'    => $row['sucursal_cobranzas'],
              'sucursal_cobranza_num' => $row['sucursal_cobranza_num'],
              'empresa_marca'         => $row['empresa_marca'],
              'flag'                  => $row['flag'],
              '`count`'               => $row['count'],
              'observaciones'         => $row['observaciones'],
              'grupo'                 => $row['grupo'],
              'idrelacion'            => $row['idrelacion'],
              'empresa_rut'           => $row['empresa_rut'],
              'total_importe'         => $row['total_importe'],
              'nactual'               => $row['nactual'],
              'version'               => $row['version'],
              'flagchange'            => $row['flagchange'],
              'rutcentralizado'       => $row['rutcentralizado'],
              'PRINT'                 => $row['PRINT'],
              'EMITIDO'               => $row['EMITIDO'],
              'movimientoabm'         => $row['movimientoabm'],
              'abm'                   => $row['abm'],
              'abmactual'             => $row['abmactual'],
              '`check`'               => '1',
              'usuario'               => $row['usuario'],
              'usuariod'              => $row['usuariod'],
              'fechafil'              => $row['fechafil'],
              'radioViejo'            => $row['radioViejo'],
              'extra'                 => $row['extra'],
              'nomodifica'            => $row['nomodifica'],
            );

            $campos = array_keys($datos_beneficiario);
            $query  = "INSERT INTO  padron_datos_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $datos_beneficiario) . "')";

            if ($result4 = mysqli_query($mysqli250, $query)) {
              logger("[OK]: GUARDAR [TABLE] padron_datos_socio - BENEFICIARIO $ci", false);
              //dir2
              $idSocioPadronBen = mysqli_insert_id($mysqli250);
              $qDatosDir = "SELECT calle,puerta,manzana,solar,apartamento,esquina,referencia FROM direcciones_socios WHERE cedula_socio ='$ci'";
              $rDatosDir = mysqli_query($mysqli, $qDatosDir);

              if ($rDatosDir && mysqli_num_rows($rDatosDir) > 0) {
                $row        = mysqli_fetch_assoc($rDatosDir);
                $calle      = mysqli_real_escape_string($mysqli250, $row['calle']);
                $puerta     = $row['puerta'];
                $manzana    = $row['manzana'];
                $solar      = $row['solar'];
                $apto       = $row['apartamento'];
                $esquina    = mysqli_real_escape_string($mysqli250, $row['esquina']);
                $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);

                $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPadronBen,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$ci')";
                $rDir = mysqli_query($mysqli250, $qInsertDireccion);

                if (!$rDir) {

                  $error = mysqli_error($mysqli250);
                  logger("[ERROR AL GUARDAR DATOS DE DIRECCION BENEFICIARIO]: $error [CI]: $ci query: $qInsertDireccion ");
                }
              }
            } else {
              $error = mysqli_query($mysqli250, $query);
              logger("[ERROR AL GUARDAR DATOS BENEFICIARIO]: $error [CI]: $ci");
            }
          }
        }
      }
    }

    if ($grupo_familiar) {
      if ($result = mysqli_query($mysqli, "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'")) {
        while ($row = mysqli_fetch_assoc($result)) {
          $ci = $row["cedula"];
          mysqli_query($mysqli, "UPDATE padron_datos_socio SET accion='5' WHERE cedula='$ci'");
          mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion='5' WHERE cedula='$ci'");
        }
      }
    }

    // ! #################################################################################
    // ! COMPRUEBA SI EXISTE ALGUN BENEFICIARIO OMT#
    // ! #################################################################################
    $qOmt = "SELECT cedula,importe from padron_producto_socio WHERE servicio='70' AND cedula_titular_gf = '$cedulaAfiliado' AND abm='ALTA'";

    if ($resultomt = mysqli_query($mysqli, $qOmt)) {
      if (mysqli_num_rows($resultomt) > 0) {
        $row       = mysqli_fetch_assoc($resultomt);
        $cedulaomt = $row['cedula'];
        $omt       = true;
      }
    }

    if ($omt) {

      $cedulasBenficiarios = [];

      // RECUPERA TODOS LOS BENEFICIARIOS
      $query = "SELECT * FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'";
      if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
          $ciBeneficiario = $row['cedula'];
          // SERVICIOS DEL BENEFICIARIO
          $servicios_beneficiario = array(
            'cedula'                 => $row['cedula'],
            'servicio'               => $row['servicio'],
            'hora'                   => $row['hora'],
            'importe'                => $row['importe'],
            'cod_promo'              => $row['cod_promo'],
            'fecha_registro'         => $row['fecha_registro'],
            'numero_contrato'        => $row['numero_contrato'],
            'fecha_afiliacion'       => $row['fecha_afiliacion'],
            'nombre_vendedor'        => $row['nombre_vendedor'],
            'observaciones'          => $row['observaciones'],
            'lugar_venta'            => $row['lugar_venta'],
            'vendedor_independiente' => $row['vendedor_independiente'],
            'activo'                 => $row['activo'],
            'movimiento'             => $row['movimiento'],
            'fecha_inicio_derechos'  => $row['fecha_inicio_derechos'],
            'numero_vendedor'        => $row['numero_vendedor'],
            'keepprice1'             => $row['keepprice1'],
            'promoactivo'            => $row['promoactivo'],
            'tipo_de_cobro'          => $row['tipo_de_cobro'],
            'tipo_iva'               => $row['tipo_iva'],
            'idrelacion'             => $row['idrelacion'],
            'codigo_precio'          => $row['codigo_precio'],
            'aumento'                => $row['aumento'],
            'empresa'                => $row['empresa'],
            'nactual'                => $row['nactual'],
            'servdecod'              => $row['servdecod'],
            'count'                  => $row['count'],
            'version'                => $row['version'],
            'abm'                    => $row['abm'],
            'abmactual'              => $row['abmactual'],
            'usuario'                => $row['usuario'],
            'usuarioid'              => $row['usuariod'],
            'extra'                  => $row['extra'],
            'nomodifica'             => $row['nomodifica'],
            'precioOriginal'         => $row['precioOriginal'],
            'abitab'                 => $row['abitab'],
            'cedula_titular_gf'      => $row['cedula_titular_gf'],
          );

          $campos = array_keys($servicios_beneficiario);
          $query  = "INSERT INTO padron_producto_socio(" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_beneficiario) . "')";

          if ($result2 = mysqli_query($mysqli250, $query)) {
            logger("[OK]: GUARDAR [TABLE] padron_producto_socio - BENEFICIARIO OMT $ciBeneficiario", false);
          } else {
            $error = mysqli_error($mysqli250);
            logger("[ERROR AL GUARDAR DATOS BENEFICIARIO OMT]: $error CI: $ciBeneficiario");
          }

          // GUARDO LAS CEDULAS DE LOS BENEFICIARIOS
          if (!in_array($ciBeneficiario, $cedulasBenficiarios)) {
            array_push($cedulasBenficiarios, $ciBeneficiario);
          }
        }
      }

      // GUARDA LOS DATOS DE LOS BENEFICIARIOS
      if (count($cedulasBenficiarios) > 0) {
        foreach ($cedulasBenficiarios as $key => $ci) {
          // DATOS DEL BENEFICIARIO
          $query  = "SELECT * FROM padron_datos_socio WHERE cedula = '$ci'";
          $result = mysqli_query($mysqli, $query);

          if (mysqli_num_rows($result) > 0) {
            $row                = mysqli_fetch_assoc($result);
            $datos_beneficiario = array(
              'nombre'                => $row['nombre'],
              'tel'                   => $row['tel'],
              'cedula'                => $ci,
              'direccion'             => addslashes($row['direccion']),
              'sucursal'              => $row['sucursal'],
              'ruta'                  => $row['ruta'],
              'radio'                 => $row['radio'],
              'activo'                => $row['activo'],
              'fecha_nacimiento'      => $row['fecha_nacimiento'],
              'edad'                  => $row['edad'],
              'tarjeta'               => $row['tarjeta'],
              'tipo_tarjeta'          => $row['tipo_tarjeta'],
              'numero_tarjeta'        => $row['numero_tarjeta'],
              'nombre_titular'        => $row['nombre_titular'],
              'cedula_titular'        => $row['cedula_titular'],
              'telefono_titular'      => $row['telefono_titular'],
              'anio_e'                => $row['anio_e'],
              'mes_e'                 => $row['mes_e'],
              'sucursal_cobranzas'    => $row['sucursal_cobranzas'],
              'sucursal_cobranza_num' => $row['sucursal_cobranza_num'],
              'empresa_marca'         => $row['empresa_marca'],
              'flag'                  => $row['flag'],
              '`count`'               => $row['count'],
              'observaciones'         => $row['observaciones'],
              'grupo'                 => $row['grupo'],
              'idrelacion'            => $row['idrelacion'],
              'empresa_rut'           => $row['empresa_rut'],
              'total_importe'         => $row['total_importe'],
              'nactual'               => $row['nactual'],
              'version'               => $row['version'],
              'flagchange'            => $row['flagchange'],
              'rutcentralizado'       => $row['rutcentralizado'],
              'PRINT'                 => $row['PRINT'],
              'EMITIDO'               => $row['EMITIDO'],
              'movimientoabm'         => $row['movimientoabm'],
              'abm'                   => $row['abm'],
              'abmactual'             => $row['abmactual'],
              '`check`'               => '1',
              'usuario'               => $row['usuario'],
              'usuariod'              => $row['usuariod'],
              'fechafil'              => $row['fechafil'],
              'radioViejo'            => $row['radioViejo'],
              'extra'                 => $row['extra'],
              'nomodifica'            => $row['nomodifica'],
            );

            $campos = array_keys($datos_beneficiario);
            $query  = "INSERT INTO  padron_datos_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $datos_beneficiario) . "')";

            if ($result4 = mysqli_query($mysqli250, $query)) {
              logger("[OK]: GUARDAR [TABLE] padron_datos_socio - BENEFICIARIO OMT $ci", false);
              //dir2
              $idSocioPadronBen = mysqli_insert_id($mysqli250);
              $qDatosDir = "SELECT calle,puerta,manzana,solar,apartamento,esquina,referencia FROM direcciones_socios WHERE cedula_socio ='$ci'";
              $rDatosDir = mysqli_query($mysqli, $qDatosDir);

              if ($rDatosDir && mysqli_num_rows($rDatosDir) > 0) {
                $row        = mysqli_fetch_assoc($rDatosDir);
                $calle      = mysqli_real_escape_string($mysqli250, $row['calle']);
                $puerta     = $row['puerta'];
                $manzana    = $row['manzana'];
                $solar      = $row['solar'];
                $apto       = $row['apartamento'];
                $esquina    = mysqli_real_escape_string($mysqli250, $row['esquina']);
                $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);

                $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPadronBen,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$ci')";
                $rDir = mysqli_query($mysqli250, $qInsertDireccion);

                if (!$rDir) {

                  $error = mysqli_error($mysqli250);
                  logger("[ERROR AL GUARDAR DATOS DE DIRECCION BENEFICIARIO]: $error [CI]: $ci query: $qInsertDireccion ");
                }
              }
            } else {
              $error = mysqli_error($mysqli250);
              logger("[ERROR AL GUARDAR DATOS BENEFICIARIO OMT]: $error [CI]: $ci");
            }
          }
        }
      }

      if ($result = mysqli_query($mysqli, "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'")) {
        while ($row = mysqli_fetch_assoc($result)) {
          $ci = $row["cedula"];
          mysqli_query($mysqli, "UPDATE padron_datos_socio SET accion='5' WHERE cedula='$ci'");
          mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion='5' WHERE cedula='$ci'");
        }
      }
    }

    if ($retorno_servicios && $retorno_datos) {

      $retornoHistorico = false;
      $retornoServicios = false;
      $fecha            = date('Y-m-d');
      $log_error        = "../../logs/errors_reportes/error_$fecha.log";
      $log_file         = "../../logs/logs_reportes/log_$fecha.log";
      $sucur            = $datos_socio['sucursal'];
      $qIdFilial        = "SELECT id FROM filiales WHERE nro_filial = $sucur";
      $idFilial         = '';
      $idGrupo          = '';
      $aplicaPromoCr    = false; //conva

      if ($rIdSuc = mysqli_query($mysqli, $qIdFilial)) {
        $idFilial = (mysqli_num_rows($rIdSuc) > 0) ? mysqli_fetch_assoc($rIdSuc)['id'] : $idFilial;
      }

      $qIdGrupo = "SELECT idgrupo FROM usuarios WHERE id = $idUser";
      if ($rIdGrupo = mysqli_query($mysqli, $qIdGrupo)) {
        $idGrupo = (mysqli_num_rows($rIdGrupo) > 0) ? mysqli_fetch_assoc($rIdGrupo)['idgrupo'] : $idGrupo;
      }

      $qHistoricoReporte = "INSERT INTO historico_reportes VALUES(null, $idUser, $id_cliente, '$alta', '" . $servicios_socio[0]['fecha_registro'] . "','" . $datos_socio['total_importe'] . "','$totalIncremento','$idFilial','$idGrupo','$metodo_pago','" . $datos_socio['observaciones'] . "','" . $datos_socio['radio'] . "','$origen_venta')";
      $rHist             = mysqli_query($mysqli, $qHistoricoReporte);

      $idHistoricoReporte = mysqli_insert_id($mysqli);

      if ($rHist) {

        if ($alta == '1') {
          //conva comprobamos si tienen convalecencia de regalo
          $qPromoCr = "SELECT id FROM padron_producto_socio WHERE abm='ALTA' AND cedula = '$cedula' AND cod_promo='24'";
          $rPromoCr = mysqli_query($mysqli, $qPromoCr);
          if ($rPromoCr && mysqli_num_rows($rPromoCr) > 0) {
            $aplicaPromoCr = true;
          }
        }

        $retornoHistorico = true;
        $log_content = "[LOG][$fecha]|datos guardados id_cliente $id_cliente, id historico: $idHistoricoReporte|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);

        $qServicios = "SELECT servicio, sum(importe) AS total_importe, sum(hora) AS horas FROM  padron_producto_socio WHERE cedula = $cedula AND abm<>'0' GROUP BY servicio";
        $rServ      = mysqli_query($mysqli, $qServicios);

        if ($rServ) {

          while ($row = mysqli_fetch_assoc($rServ)) {
            $nroServ      = $row['servicio'];
            $totalImporte = $row['total_importe'];
            $horas        = $row['horas'];
            $qIdServ      = "SELECT id FROM servicios WHERE nro_servicio = " . $nroServ;
            if ($rIdServ = mysqli_query($mysqli, $qIdServ)) {
              $idServ = mysqli_fetch_assoc($rIdServ)['id'];
            }

            $promoCr = ($aplicaPromoCr && $nroServ == '01') ? 1 : 0; //conva

            $qHistoricoServicios = "INSERT INTO historico_reportes_servicios VALUES(null, $idHistoricoReporte,$idServ, '$totalImporte','$horas',$promoCr)";
            if (mysqli_query($mysqli, $qHistoricoServicios)) {
              $retornoServicios = true;
              $idHistoricoServicio = mysqli_insert_id($mysqli);
              $log_content         = "[LOG][$fecha]|servicio $idServ guardado id historico: $idHistoricoReporte|" . mysqli_error($mysqli);
              file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
            } else {
              $log_content = "[ERROR][$fecha]|error servicios: | $qHistoricoServicios|" . mysqli_error($mysqli);
              file_put_contents($log_error, $log_content . "\n", FILE_APPEND);
            }
          }

          if ($retornoHistorico && $retornoServicios) {
            //dir2

            $qDatosDir = "SELECT calle,puerta,manzana,solar,apartamento,esquina,referencia FROM direcciones_socios WHERE cedula_socio ='$cedula'";
            $rDatosDir = mysqli_query($mysqli, $qDatosDir);

            if ($rDatosDir && mysqli_num_rows($rDatosDir) > 0) {
              $row     = mysqli_fetch_assoc($rDatosDir);
              $calle   = mysqli_real_escape_string($mysqli250, $row['calle']);
              $puerta  = $row['puerta'];
              $manzana = $row['manzana'];
              $solar   = $row['solar'];
              $apto    = $row['apartamento'];
              $esquina = mysqli_real_escape_string($mysqli250, $row['esquina']);
              $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);

              $qExistePadron = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
              $rExistePadron = mysqli_query($mysqli250, $qExistePadron);

              if ($qExistePadron && mysqli_num_rows($rExistePadron) > 0) {
                $qInsertDireccion = "UPDATE direcciones_socios SET calle = '$calle', puerta = '$puerta', apartamento = '$apto', manzana = '$manzana', solar = '$solar', esquina = '$esquina', referencia = '$referencia' WHERE cedula_socio = '$cedula'";
                $rDir = mysqli_query($mysqli250, $qInsertDireccion);
              } else {
                $qIdPadron = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
                $rIdPadron = mysqli_query($mysqli250, $qIdPadron);
                $idSocioPadron = mysqli_fetch_assoc($rIdPadron)['id'];
                $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPadron,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$cedula')";
                $rDir = mysqli_query($mysqli250, $qInsertDireccion);
              }

              if (!$rDir) {
                $error = mysqli_error($mysqli250);
                logger("[ERROR AL GUARDAR DATOS DE DIRECCION]: $error [CI]: $cedula query: $qInsertDireccion ");
              } else {
                $log_content         = "[LOG][$fecha]|direcion guardada/actualizada en padron: $cedula| query: $qInsertDireccion";
                file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
              }
            } else {
              $rDir = true;
            }

            $qDatosBenServ = "SELECT * FROM beneficiarios_servicios WHERE cedula_titular ='$cedula'"; //newform
            $rDatosBenServ = mysqli_query($mysqli, $qDatosBenServ);

            if ($rDatosBenServ && mysqli_num_rows($rDatosBenServ) > 0) {

              while ($row = mysqli_fetch_assoc($rDatosBenServ)) {
                $nomBen     = $row['nombre'];
                $cedBen     = $row['cedula'];
                $fnBen      = $row['fecha_nacimiento'];
                $telBen     = $row['telefono'];
                $numServben = $row['num_servicio'];

                $qInsertBenServ = "INSERT INTO beneficiarios_servicios VALUES (null, '$nomBen','$cedBen','$fnBen','$telBen','$cedula','$numServben')";
                $rInsertBenServ = mysqli_query($mysqli250, $qInsertBenServ);

                if (!$rInsertBenServ) {
                  $error = mysqli_error($mysqli250);
                  logger("[ERROR AL GUARDAR DATOS DE BENEFCIARIOS DE SERVICIO]: $error [CI]: $cedula query: $qInsertDireccion ");
                } else {
                  $log_content         = "[LOG][$fecha]|datos de beneficiarios guardados en padron: $cedula| query: $qInsertBenServ";
                  file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
                }
              }

              $qUpdateBS = "UPDATE beneficiarios_servicios SET concretado = 1 WHERE cedula_titular = '$cedula'";
              $rUpdateBS = mysqli_query($mysqli, $qUpdateBS); //newform

            }
          }

          $qBeneficiarios = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedula' GROUP BY cedula";
          $rBeneficiarios = mysqli_query($mysqli, $qBeneficiarios);
          if (mysqli_num_rows($rBeneficiarios) > 0 && $alta = '1') {
            while ($rowBen = mysqli_fetch_assoc($rBeneficiarios)) {
              $cedulaBen = $rowBen['cedula'];
              $qDatosBen = "SELECT  id, alta, total_importe, sucursal, observaciones,radio
                            FROM padron_datos_socio WHERE cedula = $cedulaBen";
              $rDatosBen = mysqli_query($mysqli, $qDatosBen);
              if (mysqli_num_rows($rDatosBen) > 0) {

                while ($datos = mysqli_fetch_assoc($rDatosBen)) {
                  $idBen         = $datos['id'];
                  $alta          = $datos['alta'];
                  $total_importe = $datos['total_importe'];
                  $sucursal      = $datos['sucursal'];
                  $observaciones = $datos['observaciones'];
                }

                $qIdSucursal = "SELECT id FROM filiales WHERE nro_filial =$sucursal";
                $rIdSucursal = mysqli_query($mysqli, $qIdSucursal);
                $idSucursal  = mysqli_fetch_assoc($rIdSucursal)['id'];

                $qHistoricoReporteBen = "INSERT INTO historico_reportes VALUES(null, $idUser, $idBen, '$alta', '" . $servicios_socio[0]['fecha_registro'] . "','$total_importe','$total_importe','$idSucursal','$idGrupo','$metodo_pago','$observaciones','" . $datos_socio['radio'] . "','$origen_venta')";

                $rInsert = mysqli_query($mysqli, $qHistoricoReporteBen);

                if ($rInsert) {

                  $idHistorico = mysqli_insert_id($mysqli);

                  $qServiciosBen = "SELECT servicio, sum(hora) AS horas, sum(importe) AS importe FROM padron_producto_socio WHERE cedula = '$cedulaBen' GROUP BY servicio";
                  $rServiciosBen = mysqli_query($mysqli, $qServiciosBen);

                  if ($rServiciosBen) {

                    while ($row = mysqli_fetch_assoc($rServiciosBen)) {
                      $nroServicio = $row['servicio'];
                      $importe     = $row['importe'];
                      $horas       = $row['horas'];

                      $qIdServicio = "SELECT id FROM servicios WHERE nro_servicio = '$nroServicio'";
                      $rIdServicio = mysqli_query($mysqli, $qIdServicio);

                      $idServicio       = mysqli_fetch_assoc($rIdServicio)['id'];
                      $qGuardarServicio = "INSERT INTO historico_reportes_servicios VALUES (null, $idHistorico, $idServicio, $importe, $horas)";
                      $rGuardarServicio = mysqli_query($mysqli, $qGuardarServicio);
                    }
                  }
                }
              }
            }
          }
        }
      } else {
        $log_content = "[ERROR][$fecha]|error datos| $qHistoricoReporte|" . mysqli_error($mysqli);
        file_put_contents($log_error, $log_content . "\n", FILE_APPEND);
      }
    }

    $rHistorico = ($retornoHistorico && $retornoServicios && $rDir);

    if (!$retorno_servicios || !$retorno_datos || !$rHistorico) {
      if ($alta == '1') {
        mysqli_query($mysqli250, "DELETE FROM padron_datos_socio WHERE cedula ='$cedula'");
        mysqli_query($mysqli250, "DELETE FROM padron_producto_socio WHERE cedula ='$cedula'");
      }

      mysqli_query($mysqli, "UPDATE padron_datos_socio SET accion ='$accion' WHERE cedula ='$cedula'");
      mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion ='1' WHERE cedula ='$cedula' AND fecha_registro='" . $servicios_socio[0]['fecha_registro'] . "'");
      mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion ='3' WHERE cedula ='$cedula' AND fecha_registro<>'" . $servicios_socio[0]['fecha_registro'] . "'");

      $qBen = "SELECT id,cedula from padron_producto_socio WHERE cedula_titular_gf = '$cedula'";
      if ($resultomt = mysqli_query($mysqli250, $qBen)) {
        if (mysqli_num_rows($resultomt) > 0) {
          while ($row = mysqli_fetch_assoc($resultomt)) {
            $cedulaBen = $row['cedula'];
            mysqli_query($mysqli250, "DELETE FROM padron_datos_socio WHERE cedula = '$cedulaBen'");
            mysqli_query($mysqli250, "DELETE FROM padron_producto_socio WHERE cedula = '$cedulaBen'");
          }
        }
      }
    }




    // envio de sms con terminos y condiciones al socio
    if ($retorno_servicios && $retorno_datos && $rHistorico) {

      // envio de sms con terminos y condiciones al socio
      $celulares = buscarCelular($datos_socio['tel']);
      $total     = $datos_socio['total_importe'];
      $sucursal  = $datos_socio['sucursal'];

      if (!$celulares)
        $retorno_sms = false;

      switch ((int) $sucursal) {
        case 1372:
        case 1373:
        case 1374:
          $empresa = 3;
          return;
          break;

        case 1370:
        case 1371:
          $empresa = 2;
          return;
          break;

        default:
          $empresa = 1;
          break;
      }

      $qLink = "SELECT empresa, link FROM terminos_y_condiciones.empresa WHERE id = $empresa";
      $rLink = mysqli_query($mysqli250, $qLink);



      if ($rLink) {
        while ($row = mysqli_fetch_assoc($rLink)) {
          $empresaNombre = $row['empresa'];
          $link          = $row['link'] . '?' . mb_strtolower(substr($row['empresa'], 0, 1));
          $parametros    = '&';
        }

        for ($i = 0; $i < count($servicios_socio); $i++) {
          if (
            substr($parametros, -1) !== '&'
            || $servicios_socio[$i]['servicio'] != '01'
          ) {
            $parametros .= '&';
            $servicio = $servicios_socio[$i]['servicio'];
            $qIdentificador =
              "SELECT
                identificador
              FROM
                v_nexo
              WHERE
                id_empresa    = '$empresa' AND
                id_servicio    = '" . $servicio . "'";

            $mysqli250->select_db('terminos_y_condiciones');

            $rIdentificador = mysqli_query($mysqli250, $qIdentificador);

            if ($rIdentificador) {
              while ($row = mysqli_fetch_assoc($rIdentificador)) {
                $parametros .= $row['identificador'];
              }
            }
          }
        }

        $mysqli250->select_db('abmmod');


        if ($empresa != 3 && $empresa != 2) {
          $mensaje  = "Bienvenido a $empresaNombre, puede ver los terminos y condiciones de su contrato en $link" . $parametros;
          $mensaje .= " . Atencion al socio Tel. 22043739";
          $servicio = "http://192.168.104.6/apiws/1/apiws.php?wsdl";
          $info     = array(
            'authorizedKey' => '9d752cb08ef466fc480fba981cfa44a1',
            'msgId'         => '0',
            'msgData'       => $mensaje,
          );
          $url_tos = "https://www." . $link . $parametros;
          foreach ($celulares as $celular) {
            $info['msgRecip'] = $celular;
            $client           = new SoapClient($servicio, $info);
            //MANDO SMS
            $client->sendSms($info['authorizedKey'], $info['msgId'], $info['msgData'], $info['msgRecip']);
            //MANDO WHATSAPP
            enviarWPP($celular, $datos_socio['nombre'], $url_tos);

            $retorno_sms = true;
          }
        }
      }
    }

    $result = (($retorno_servicios && $retorno_datos && $rHistorico) && $retorno_sms);
  }

  if (!$error)
    copiarPatologiaPiscinaPadron($datos_socio['cedula']);

  return $result;
}

function obtenerGrupoUsuario($idVendedor)
{ //ucem
  global $mysqli;

  $qIdGrupo =  "SELECT idgrupo FROM usuarios WHERE id = $idVendedor";

  $select = $mysqli->query($qIdGrupo);

  return ($select->num_rows > 0) ? $select->fetch_assoc()['idgrupo'] : false;
}

/**
 * Copia las patologías del documento inidicado del 1.13 al 1.250
 *
 * @param string $_documentoSocio
 * @return void
 */
function copiarPatologiaPiscinaPadron($_documentoSocio)
{
  require __DIR__ . '/../../_conexion.php';
  require __DIR__ . '/../../_conexion250.php';

  $documentoSocio = $mysqli->real_escape_string($_documentoSocio);
  $qSelect = <<<SQL
  SELECT
    *
  FROM
    `patologias_socio`
  WHERE
    `documento_socio` = "$documentoSocio"
SQL;
  $select = $mysqli->query($qSelect);
  $patologiasSocio = $select->fetch_all(MYSQLI_ASSOC);


  foreach ($patologiasSocio as $patologia) {
    $documentoSocio = $mysqli250->real_escape_string($patologia['documento_socio']);
    $idPatologia = $mysqli250->real_escape_string($patologia['id_patologia']);
    $observacion = $mysqli250->real_escape_string($patologia['observacion']);
    $qInsert = <<<SQL
    INSERT INTO
      `patologias_socio`
      (`documento_socio`, `id_patologia`, `observacion`, `fecha`)
    VALUES
      ("{$documentoSocio}", "{$idPatologia}", "{$observacion}", NOW())
SQL;
    $mysqli250->query($qInsert);
  }
}

function enviarWPP($celular, $nombre, $link_tos)
{
  $URL_CONSULTA = "https://vida-apps.com/ws_whatsapp/";
  $USERNAME = "ws_vida_wpp";
  $PASSWORD = ":=!7OK:Q2;Nb#JE8P3i£TcWz<lwBb1x(r0NsW,H2";
  $TEMPLATE = "bienvenido";
  $ID_APLICACION = 1;
  $TOKEN_APLICACION = "275e3db0c4dd1ac0f37588ab258cbc12";

  $DATA = json_encode([
    "celular_to" => (string) $celular,
    "template_name" => (string) $TEMPLATE,
    "id_aplicacion" => (int) $ID_APLICACION,
    "token_aplicacion" => (string) $TOKEN_APLICACION,
    "parametros" => [
      [
        "nro" => 1,
        "texto" => (string) $nombre
      ],
      [
        "nro" => 2,
        "texto" => (string) $link_tos
      ]
    ]
  ]);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_USERPWD, $USERNAME . ":" . $PASSWORD);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
  curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $DATA);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_VERBOSE, true);
  curl_setopt($ch, CURLOPT_URL, $URL_CONSULTA);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  $respuesta_WS = curl_exec($ch);
  $errCurl = $respuesta_WS === false ? curl_error($ch) : false;
  curl_close($ch);

  if ($errCurl)
    return false;

  return true;
}
