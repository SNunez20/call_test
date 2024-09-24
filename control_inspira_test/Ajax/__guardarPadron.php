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

    if ($rDatos) {
        $row         = mysqli_fetch_assoc($rDatos);
        $accion      = $row['accion'];
        $datos_socio = array(
            'nombre'                => $row['nombre'],
            'tel'                   => $row['tel'],
            'cedula'                => $row['cedula'],
            'direccion'             => $row['direccion'],
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
        logger("[OK]: RECUPERAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
    } else if ($error = mysqli_error($mysqli)) {
        logger("[ERROR]: $error" . "CI: $cedulaAfiliado");
        $error = true;
    }

    if (!$error) {
        $cedula          = $datos_socio['cedula'];
        $qServicios      = "SELECT * FROM padron_producto_socio where cedula = '$cedula' AND accion='1'";
        $rServicios      = mysqli_query($mysqli, $qServicios);
        $servicios_socio = [];

        if ($rServicios) {
            while ($row = mysqli_fetch_assoc($rServicios)) {
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
            }
            logger("[OK]: RECUPERAR [TABLE] padron_producto_socio [CI]: $cedulaAfiliado", false);
        } else if ($error = mysqli_error($mysqli)) {
            logger("[ERROR]: $error" . "CI: $cedulaAfiliado");
            $error = true;
        }
    }

    if (!$error) {
        if ($accion == '1') { //insertar nuevo socio en el padron
            $campos        = array_keys($datos_socio);
            $qInsertar     = "INSERT INTO padron_datos_socio VALUES(null,'" . implode("','", $datos_socio) . "')";
            $retorno_datos = mysqli_query($mysqli250, $qInsertar);
            if (!$retorno_datos) {
                $error = mysqli_error($mysqli);
                logger("[ERROR]: $error" . "CI: $cedulaAfiliado" . " QUERY: " . $query);
                $error = true;
            } else {
                logger("[OK]: GUARDAR [TABLE] padron_datos_socio", false);
            }
        } else if ($accion == '4') { //actualizar datos del socio en el padron
            $qActualizar = "UPDATE padron_datos_socio SET ";
            $data        = array();
            foreach ($datos_socio as $column => $value) {
                $data[] = $column . "=" . "'" . $value . "'";
            }
            $qActualizar .= implode(',', $data);
            $qActualizar .= " where cedula = '" . $datos_socio['cedula'] . "'";
            $retorno_datos = (mysqli_query($mysqli250, $qActualizar)) ? true : false;

            if (!$retorno_datos) {
                $error = mysqli_error($mysqli);
                logger("[ERROR]: $error" . "CI: $cedulaAfiliado");
                $error = true;
            } else {
                logger("[OK]: ACTUALIZAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado" . " QUERY: " . $query, false);
            }
        } else {
            logger("ACCION: $accion - CI: $cedulaAfiliado");
            $error = true;
        }
    }

    if (!$error) {
        // insertamos los servicios nuevos en el padron
        for ($i = 0; $i < count($servicios_socio); $i++) {
            $campos             = array_keys($servicios_socio[$i]);
            $qInsertarServicios = "INSERT INTO  padron_producto_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_socio[$i]) . "')";
            $retorno_servicios  = mysqli_query($mysqli250, $qInsertarServicios);
            if (!$retorno_servicios) {
                $error = mysqli_error($mysqli);
                logger("[ERROR]: $error" . "CI: $cedulaAfiliado " . " QUERY: " . $query);
                $error = true;
            } else {
                logger("[OK]: GUARDAR [TABLE] padron_producto_socio [CI]: $cedulaAfiliado", false);
            }
        }

        $query  = "SELECT * FROM padron_datos_socio where cedula = '$cedulaAfiliado'";
        $result = mysqli_query($mysqli250, $query);
        if (mysqli_num_rows($result) == 1) {
            $qActualizarAccionDatos = "UPDATE padron_datos_socio set accion = '5' where cedula = '$cedulaAfiliado'";
            $result                 = mysqli_query($mysqli, $qActualizarAccionDatos);

            if (!$result) {
                $error = mysqli_error($mysqli);
                logger("[ERROR]: $error" . "CI: $cedulaAfiliado " . " QUERY: " . $query);
                $error = true;
            } else {
                logger("[OK]: ACTUALIZACION accion [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
            }

            $qActualizarAcccionProductos = "UPDATE padron_producto_socio set accion='5' where cedula = '$cedulaAfiliado'";
            $result                      = mysqli_query($mysqli, $qActualizarAcccionProductos);

            if (!$result) {
                $error = mysqli_error($mysqli);
                logger("[ERROR]: $error" . "CI: $cedulaAfiliado " . " QUERY: " . $query);
                $error = true;
            } else {
                logger("[OK]: ACTUALIZACION accion [TABLE] padron_producto_socio [CI]: $cedulaAfiliado", false);
            }
        }

        // envio de sms con terminos y condiciones al socio
        $celulares = buscarCelular($datos_socio['tel']);
        $total     = $datos_socio['total_importe'];
        $sucursal  = $datos_socio['sucursal'];

        if (!$celulares) {
            $retorno_sms = false;
        }

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
                if (substr($parametros, -1) !== '&') {
                    $parametros .= '&';
                    $qIdentificador =
                        "SELECT
                        identificador
                    FROM
                        v_nexo
                    WHERE
                        id_empresa	= '$empresa' AND
                        id_servicio	= '" . $servicios_socio['servicio'] . "'";

                    $rIdentificador = mysqli_query($mysqli250, $qIdentificador);

                    if ($rIdentificador) {
                        while ($row = mysqli_fetch_assoc($rIdentificador)) {
                            $parametros .= $row['identificador'];
                        }
                    }
                }
            }

            if ($empresa != 3 && $empresa != 2) {
                $mensaje  = "Bienvenido a $empresaNombre, puede ver los terminos y condiciones de su contrato en $link" . $parametros;
                $servicio = "http://192.168.104.6/apiws/1/apiws.php?wsdl";
                $info     = array(
                    'authorizedKey' => '9d752cb08ef466fc480fba981cfa44a1',
                    'msgId'         => '0',
                    'msgData'       => $mensaje,
                );

                foreach ($celulares as $celular) {
                    $info['msgRecip'] = $celular;
                    $client           = new SoapClient($servicio, $info);
                    $client->sendSms($info['authorizedKey'], $info['msgId'], $info['msgData'], $info['msgRecip']);
                    $retorno_sms = true;
                }
            }
        }

        $result = (($retorno_servicios && $retorno_datos) && $retorno_sms);
        logger("RETORNO $result", false);
    }

    return $result;
}
