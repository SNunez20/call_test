<?php
require_once "../../../logger.php";



function guardarPadron($id_cliente, $mysqli, $mysqli250, $cedulaAfiliado)
{
    $error             = false;
    $result            = false;
    $accion            = null;
    $datos_socio       = [];
    $retorno_servicios = false;
    $qActualizar       = 'none';
    $grupo_familiar    = false;
    $omt               = false;
    $retornoHistorico  = false;


    $rDatos = obtener_datos_padron_socio(1, 1, $id_cliente);
    if (!$rDatos) devolver_error("Ocurrieron errores al obtener los datos de #$id_cliente");
    while ($row = mysqli_fetch_assoc($rDatos)) {
        $accion          = $row['accion'];
        $idUser          = $row['id_usuario'];
        $alta            = $row['alta'];
        $metodo_pago     = $row['metodo_pago'];
        $origen_venta    = $row['origen_venta'];
        $idGrupoVendedor = obtenerGrupoUsuario($idUser);

        if ($origen_venta == 5) $origenVta = 'UDEMM';
        else if (($idGrupoVendedor) == 10013) $origenVta = 'UCEM';
        else $origenVta = '';

        $datos_socio = [
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
        ];
    }

    logger("[OK]: RECUPERAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);


    $cedula = $datos_socio['cedula'];
    $servicios_socio = [];
    $rServicios = obtener_padron_productos_socio(6, 1, $cedula);
    if (!$rServicios) devolver_error("Ocurrieron errores al consultar los productos de $cedula");
    while ($row = mysqli_fetch_assoc($rServicios)) {
        // COMPRUEBA SI TIENE GRUPO FAMILIAR
        if (in_array($row["servicio"], [63, 65])) $grupo_familiar = true;

        $servicios_socio[] = [
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
        ];
    }
    logger("[OK]: RECUPERAR [TABLE] padron_producto_socio: [CI]: $cedulaAfiliado", false);


    if ($accion == '1') { //insertar nuevo socio en el padron
        $campos        = array_keys($datos_socio);
        $qInsertar     = "INSERT INTO padron_datos_socio VALUES(null,'" . implode("','", $datos_socio) . "')";
        $retorno_datos = mysqli_query($mysqli250, $qInsertar);
        if (!$retorno_datos) devolver_error("Ocurrieron errores al registrar los datos de $cedulaAfiliado");

        $idSocioPadron = mysqli_insert_id($mysqli250); //dir2
        logger("[OK]: GUARDAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
    } else if (in_array($accion, ["4", "3"])) { //actualizar datos del socio en el padron
        $qActualizar = "UPDATE padron_datos_socio SET ";
        $data        = [];
        foreach ($datos_socio as $column => $value) {
            $data[] = $column . "=" . "'" . $value . "'";
        }
        $qActualizar .= implode(',', $data);
        $qActualizar .= " where cedula = '" . $datos_socio['cedula'] . "'";
        $retorno_datos = mysqli_query($mysqli250, $qActualizar);
        if (!$retorno_datos) devolver_error("Ocurrieron errores al modificar los datos del socio");

        logger("[OK]: ACTUALIZAR [TABLE] padron_datos_socio [CI]: $cedulaAfiliado", false);
    } else {
        logger("ACCION: $accion - CI: $cedulaAfiliado");
        $error = true;
    }


    $totalIncremento = 0;
    // insertamos los servicios nuevos en el padron
    for ($i = 0; $i < count($servicios_socio); $i++) {
        $campos = array_keys($servicios_socio[$i]);
        $totalIncremento += (int) $servicios_socio[$i]['importe'];
        $result             = registrar_padron_productos_socio($campos, $servicios_socio[$i], $cedulaAfiliado);
        $retorno_servicios  = ($result) ? true : false;
        if (!$retorno_servicios) devolver_error("Ocurrieron errores al registrar los productos a $cedulaAfiliado");

        logger("[OK]: GUARDAR [TABLE] padron_producto_socio [CI]: $cedulaAfiliado", false);
    }

    if ($retorno_servicios) {
        // ACTUALIZA LA ACCION A 5 TANTO EN DATOS COMO PRODUCTOS PARA INDICAR QUE PASO A PADRON
        $result = modificar_accion_datos_socio($cedulaAfiliado);
        if (!$result) devolver_error("Ocurrieron errores al modificar la acción en los datos del socio $cedulaAfiliado");

        $result = modificar_accion_productos_socio($cedulaAfiliado);
        if (!$result) devolver_error("Ocurrieron errores al modificar la acción en los productos del socio $cedulaAfiliado");
    }

    // ! #################################################################################
    // ! COMPRUEBA SI EXISTE GRUPO FAMILIAR PARA RECUPERAR LOS DATOS DE LOS BENEFICIARIOS#
    // ! #################################################################################
    if ($grupo_familiar) {
        // GRUPO FAMILIAR
        $cedulasBenficiarios = [];

        // RECUPERA TODOS LOS BENEFICIARIOS
        $result = obtener_padron_productos_socio(1, 1, $cedulaAfiliado);
        if (!$result) devolver_error("Ocurrieron errores al obtener los productos de $cedulaAfiliado");

        while ($row = mysqli_fetch_assoc($result)) {
            $ciBeneficiario = $row['cedula'];
            // SERVICIOS DEL BENEFICIARIO
            $servicios_beneficiario[] = [
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
            ];

            $campos = array_keys($servicios_beneficiario);
            $result2 = registrar_padron_productos_socio($campos, $servicios_beneficiario, $ciBeneficiario);
            if (!$result2) devolver_error("Ocurrieron errores al registrar los productos para $ciBeneficiario");

            logger("[OK]: GUARDAR [TABLE] padron_producto_socio - BENEFICIARIO $ciBeneficiario", false);

            // GUARDO LAS CEDULAS DE LOS BENEFICIARIOS
            if (!in_array($ciBeneficiario, $cedulasBenficiarios))
                array_push($cedulasBenficiarios, $ciBeneficiario);
        }


        // GUARDA LOS DATOS DE LOS BENEFICIARIOS
        if (count($cedulasBenficiarios) > 0) {
            foreach ($cedulasBenficiarios as $key => $ci) {
                // DATOS DEL BENEFICIARIO
                $result = obtener_datos_padron_socio(2, 1, $ci);
                if (!$result) devolver_error("Ocurrieron errores al obtener los datos de $ci");

                if (mysqli_num_rows($result) > 0) {
                    $row              = mysqli_fetch_assoc($result);
                    $row['direccion'] = addslashes($row['direccion']);
                    $row['check']     = '1';
                    $row['`count`']   = $row['count'];

                    $campos = array_keys($row);
                    $result4 = registrar_padron_datos_socio($campos, $row, $ci);
                    if (!$result4) devolver_error("Ocurrieron errores al registrar los datos de los beneficiarios $ci");

                    logger("[OK]: GUARDAR [TABLE] padron_datos_socio - BENEFICIARIO $ci", false);
                    //dir2
                    $idSocioPadronBen = mysqli_insert_id($mysqli250);

                    $rDatosDir = obtener_direccion_socio(1, $ci);
                    if (!$rDatosDir) devolver_error("Ocurrieron errores al consultar la dirección del socio $ci");
                    if (mysqli_num_rows($rDatosDir) > 0) {
                        $row        = mysqli_fetch_assoc($rDatosDir);
                        $calle      = mysqli_real_escape_string($mysqli250, $row['calle']);
                        $puerta     = $row['puerta'];
                        $manzana    = $row['manzana'];
                        $solar      = $row['solar'];
                        $apto       = $row['apartamento'];
                        $esquina    = mysqli_real_escape_string($mysqli250, $row['esquina']);
                        $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);


                        $rDir = registrar_direccion_socio($idSocioPadronBen, $calle, $puerta, $manzana, $solar, $apto, $esquina, $referencia, $ci);
                        if (!$rDir) devolver_error("Ocurrieron errores al registrar la dirección del socio $ci");
                    }
                }
            }
        }
    }

    if ($grupo_familiar) {
        $result = obtener_padron_productos_socio(1, 1, $cedulaAfiliado);
        if (!$result) devolver_error("Ocurrieron errores al obtener los productos de $cedulaAfiliado");

        while ($row = mysqli_fetch_assoc($result)) {
            $ci = $row["cedula"];

            $r_modificar_accion_socio = modificar_accion_datos_socio($ci);
            if (!$r_modificar_accion_socio) devolver_error("Ocurrieron errores al modificar la accion de los datos de $ci");

            $r_modificar_accion_producto = modificar_accion_productos_socio($ci);
            if (!$r_modificar_accion_producto) devolver_error("Ocurrieron errores al modificar la accion de los productos de $ci");
        }
    }


    // ! #################################################################################
    // ! COMPRUEBA SI EXISTE ALGUN BENEFICIARIO OMT#
    // ! #################################################################################
    $resultomt = obtener_padron_productos_socio(5, 1, $cedulaAfiliado);
    if (!$resultomt) devolver_error("Ocurrieron errores al comprobar si existe algún beneficiario OMT de $cedulaAfiliado");
    if (mysqli_num_rows($resultomt) > 0) $omt = true;


    if ($omt) {
        $cedulasBenficiarios = [];

        // RECUPERA TODOS LOS BENEFICIARIOS
        $result = obtener_padron_productos_socio(1, 1, $cedulaAfiliado);
        if (!$result) devolver_error("Ocurrieron errores obtener los beneficiarios de $cedulaAfiliado");

        while ($row = mysqli_fetch_assoc($result)) {
            $ciBeneficiario = $row['cedula'];
            // SERVICIOS DEL BENEFICIARIO
            $servicios_beneficiario[] = [
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
            ];

            $campos = array_keys($servicios_beneficiario);
            $result2 = registrar_padron_productos_socio($campos, $servicios_beneficiario, $cedulaAfiliado);
            if (!$result2) devolver_error("Ocurrieron errores al registrar los productos de los beneficiarios OMT $cedulaAfiliado");

            // GUARDO LAS CEDULAS DE LOS BENEFICIARIOS
            if (!in_array($ciBeneficiario, $cedulasBenficiarios))
                array_push($cedulasBenficiarios, $ciBeneficiario);
        }
    }

    // GUARDA LOS DATOS DE LOS BENEFICIARIOS
    if (count($cedulasBenficiarios) > 0) {
        foreach ($cedulasBenficiarios as $key => $ci) {
            // DATOS DEL BENEFICIARIO
            $result = obtener_datos_padron_socio(2, 1, $ci);
            if (!$result) devolver_error("Ocurrieron errores al obtener los datos de los beneficiarios OMT $ci");

            if (mysqli_num_rows($result) > 0) {
                $row              = mysqli_fetch_assoc($result);
                $row['direccion'] = addslashes($row['direccion']);
                $row['check']     = '1';
                $row['`count`']   = $row['count'];

                $campos = array_keys($row);
                $result4 = registrar_padron_datos_socio($campos, $row, $ci);
                if (!$result4) devolver_error("Ocurrieron errores al registrar los datos del beneficiario OMT $ci");
                //dir2
                $idSocioPadronBen = mysqli_insert_id($mysqli250);

                $rDatosDir = obtener_direccion_socio(1, $ci);
                if (!$rDatosDir) devolver_error("Ocurrieron errores al obtener la dirección del socio");

                if (mysqli_num_rows($rDatosDir) > 0) {
                    $row        = mysqli_fetch_assoc($rDatosDir);
                    $calle      = mysqli_real_escape_string($mysqli250, $row['calle']);
                    $puerta     = $row['puerta'];
                    $manzana    = $row['manzana'];
                    $solar      = $row['solar'];
                    $apto       = $row['apartamento'];
                    $esquina    = mysqli_real_escape_string($mysqli250, $row['esquina']);
                    $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);


                    $rDir = registrar_direccion_socio($idSocioPadronBen, $calle, $puerta, $manzana, $solar, $apto, $esquina, $referencia, $ci);
                    if (!$rDir) devolver_error("Ocurrieron errores al registrar la dirección al socio");
                }
            }
        }
    }


    $result = obtener_padron_productos_socio(1, 1, $cedulaAfiliado);
    if (!$result) devolver_error("Ocurrieron errores obtener los beneficiarios");

    while ($row = mysqli_fetch_assoc($result)) {
        $ci = $row["cedula"];
        $modificar_accion_socio = modificar_accion_datos_socio($ci);
        if (!$modificar_accion_socio) devolver_error("Ocurrieron errores al modificar la acción del socio $ci");
        $modificar_accion_productos_socio = modificar_accion_productos_socio($ci);
        if (!$modificar_accion_productos_socio) devolver_error("Ocurrieron errores al modificar la acción del producto del socio $ci");
    }


    if ($retorno_servicios && $retorno_datos) {
        $retornoHistorico = false;
        $retornoServicios = false;
        $fecha            = date('Y-m-d');
        $log_error        = "../../../logs/errors_reportes/error_$fecha.log";
        $log_file         = "../../../logs/logs_reportes/log_$fecha.log";
        $sucur            = $datos_socio['sucursal'];
        $idFilial         = '';
        $idGrupo          = '';
        $aplicaPromoCr    = false; //conva


        $rIdSuc = obtener_datos_filial($sucur);
        if (!$rIdSuc) devolver_error("Ocurrieron errores al obtener los datos de la filial");
        $idFilial = (mysqli_num_rows($rIdSuc) > 0) ? mysqli_fetch_assoc($rIdSuc)['id'] : $idFilial;


        $rIdGrupo = obtener_datos_usuario($idUser);
        if (!$rIdGrupo) devolver_error("Ocurrieron errores al obtener los datos del usuario");
        $idGrupo = (mysqli_num_rows($rIdGrupo) > 0) ? mysqli_fetch_assoc($rIdGrupo)['idgrupo'] : $idGrupo;


        $fecha_registro = $servicios_socio[0]['fecha_registro'];
        $total_importe = $datos_socio['total_importe'];
        $observaciones = $datos_socio['observaciones'];
        $radio = $datos_socio['radio'];

        $idHistoricoReporte = registrar_historico_reportes(null, $idUser, $id_cliente, $alta, $fecha_registro, $total_importe, $totalIncremento, $idFilial, $idGrupo, $metodo_pago, $observaciones, $radio, $origen_venta);
        if ($idHistoricoReporte == false) devolver_error("Ocurrieron errores al registrar en historico de reportes");


        if ($alta == '1') {
            //conva comprobamos si tienen convalecencia de regalo
            $rPromoCr = obtener_padron_productos_socio(4, 1, $cedula);
            if (!$rPromoCr) {
                $response['error'] = true;
                $response['mensaje'] = "Ocurrieron errores al comprobar si tienen convalecencia de regalo";
                die(json_encode($response));
            }
            if (mysqli_num_rows($rPromoCr) > 0) $aplicaPromoCr = true;
        }

        $retornoHistorico = true;


        $rServ = obtener_padron_productos_socio(7, 1, $cedula);
        if (!$rServ) devolver_error("Ocurrieron errores al obtener los productos de piscina");

        while ($row = mysqli_fetch_assoc($rServ)) {
            $nroServ      = $row['servicio'];
            $totalImporte = $row['total_importe'];
            $horas        = $row['horas'];

            $rIdServ      = obtener_servicios($nroServ);
            if (!$rIdServ) devolver_error("Ocurrieron errores al obtener los datos del servicio");

            $idServ = mysqli_fetch_assoc($rIdServ)['id'];
            $promoCr = ($aplicaPromoCr && $nroServ == '01') ? 1 : 0; //conva
            $rHistoricoServicios = registrar_historico_reporte_servicios(null, $idHistoricoReporte, $idServ, $totalImporte, $horas, $promoCr);
            if (!$rHistoricoServicios) devolver_error("Ocurrieron errores al registrar en el historico de reportes de servicios");
            $retornoServicios = true;
        }

        if ($retornoHistorico && $retornoServicios) {
            //dir2

            $rDatosDir = obtener_direccion_socio(1, $cedula);
            if (!$rDatosDir) devolver_error("Ocurrieron errores al comprobar la dirección del socio en piscina");
            if (mysqli_num_rows($rDatosDir) <= 0) $rDir = true;

            if (mysqli_num_rows($rDatosDir) > 0) {
                $row     = mysqli_fetch_assoc($rDatosDir);
                $calle   = mysqli_real_escape_string($mysqli250, $row['calle']);
                $puerta  = $row['puerta'];
                $manzana = $row['manzana'];
                $solar   = $row['solar'];
                $apto    = $row['apartamento'];
                $esquina = mysqli_real_escape_string($mysqli250, $row['esquina']);
                $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);


                $rExistePadron = obtener_direccion_socio(2, $cedula);
                if (!$rExistePadron) devolver_error("Ocurrieron errores al comprobar la dirección del socio en padrón");

                if (mysqli_num_rows($rExistePadron) > 0) {
                    $rDir = modificar_direccion_socio($calle, $puerta, $apto, $manzana, $solar, $esquina, $referencia, $cedula);
                } else {
                    $rIdPadron = obtener_datos_padron_socio(2, 2, $cedula);
                    $idSocioPadron = mysqli_fetch_assoc($rIdPadron)['id'];
                    $rDir = registrar_direccion_socio($idSocioPadron, $calle, $puerta, $apto, $manzana, $solar, $esquina, $referencia, $cedula);
                }
            }


            $rDatosBenServ = obtener_beneficiarios_servicios($cedula); //newform
            if (!$rDatosBenServ) devolver_error("Ocurrieron errores al obtener los beneficiarios del servicio");

            if (mysqli_num_rows($rDatosBenServ) > 0) {
                while ($row = mysqli_fetch_assoc($rDatosBenServ)) {
                    $nomBen     = $row['nombre'];
                    $cedBen     = $row['cedula'];
                    $fnBen      = $row['fecha_nacimiento'];
                    $telBen     = $row['telefono'];
                    $numServben = $row['num_servicio'];

                    $rInsertBenServ = registrar_beneficiarios_servicios(null, $nomBen, $cedBen, $fnBen, $telBen, $cedula, $numServben);
                    if (!$rInsertBenServ) devolver_error("Ocurrieron errores al registrar los datos de los beneficiarios del servicio");
                }

                $rUpdateBS = modificar_beneficiarios_servicios($cedula); //newform
                if (!$rUpdateBS) devolver_error("Ocurrieron errores al modificar los beneficiarios de los servicios");
            }
        }


        $rBeneficiarios = obtener_padron_productos_socio(3, 1, $cedula);
        if (!$rBeneficiarios) devolver_error("Ocurrieron errores al obtener los productos de los beneficiarios");


        if (mysqli_num_rows($rBeneficiarios) > 0 && $alta = '1') {
            while ($rowBen = mysqli_fetch_assoc($rBeneficiarios)) {
                $cedulaBen = $rowBen['cedula'];
                $rDatosBen = obtener_datos_padron_socio(2, 1, $cedulaBen);
                if (!$rDatosBen) devolver_error("Ocurrieron errores al obtener los datos de los beneficiarios");


                if (mysqli_num_rows($rDatosBen) > 0) {
                    while ($datos = mysqli_fetch_assoc($rDatosBen)) {
                        $idBen         = $datos['id'];
                        $alta          = $datos['alta'];
                        $total_importe = $datos['total_importe'];
                        $sucursal      = $datos['sucursal'];
                        $observaciones = $datos['observaciones'];
                    }

                    $rIdSucursal = obtener_datos_filial($sucursal);
                    if (!$rIdSucursal) devolver_error("Ocurrieron errores al obtener los datos de la filial");


                    $idSucursal  = mysqli_fetch_assoc($rIdSucursal)['id'];
                    $fecha_registro = $servicios_socio[0]['fecha_registro'];
                    $radio = $datos_socio['radio'];

                    $rInsert = registrar_historico_reportes(null, $idUser, $idBen, $alta, $fecha_registro, $total_importe, $idSucursal, $idGrupo, $metodo_pago, $observaciones, $radio, $origen_venta);
                    if (!$rInsert) devolver_error("Ocurrieron errores al registrar el historico de reportes");


                    $idHistorico = mysqli_insert_id($mysqli);
                    $rServiciosBen = obtener_padron_productos_socio(2, 1, $cedulaBen);
                    if (!$rServiciosBen) devolver_error("Ocurrieron errores al obtener los servicios de los beneficiarios");


                    while ($row = mysqli_fetch_assoc($rServiciosBen)) {
                        $nroServicio = $row['servicio'];
                        $importe     = $row['importe'];
                        $horas       = $row['horas'];

                        $rIdServicio = obtener_servicios($nroServicio);
                        if (!$rIdServicio) devolver_error("Ocurrieron errores al obtener los servicios");

                        $idServicio = mysqli_fetch_assoc($rIdServicio)['id'];
                        $rGuardarServicio = registrar_historico_reporte_servicios(null, $idHistorico, $idServicio, $importe, $horas, 0);
                        if (!$rGuardarServicio) devolver_error("Ocurrieron errores al registrar en el historico de reporte de servicios");
                    }
                }
            }
        }
    }

    $rHistorico = ($retornoHistorico && $retornoServicios && $rDir);

    if (!$retorno_servicios || !$retorno_datos || !$rHistorico) {
        if ($alta == '1') {
            //Elimina de padrón datos socio
            eliminar_registros_padron(1, $cedula);
            //Elimina de padron productos socio
            eliminar_registros_padron(2, $cedula);
        }

        mysqli_query($mysqli, "UPDATE padron_datos_socio SET accion = '$accion' WHERE cedula = '$cedula'");
        mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion = '1' WHERE cedula = '$cedula' AND fecha_registro = '" . $servicios_socio[0]['fecha_registro'] . "'");
        mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion = '3' WHERE cedula = '$cedula' AND fecha_registro <> '" . $servicios_socio[0]['fecha_registro'] . "'");


        $resultomt = obtener_padron_productos_socio(1, 2, $cedula);

        if ($resultomt) {
            if (mysqli_num_rows($resultomt) > 0) {
                while ($row = mysqli_fetch_assoc($resultomt)) {
                    $cedulaBen = $row['cedula'];
                    //Elimina de padrón datos socio
                    eliminar_registros_padron(1, $cedulaBen);
                    //Elimina de padron productos socio
                    eliminar_registros_padron(2, $cedulaBen);
                }
            }
        }
    }



    // envio de sms con terminos y condiciones al socio
    if ($retorno_servicios && $retorno_datos && $rHistorico) {

        // envio de sms con terminos y condiciones al socio
        $celulares = buscarCelular($datos_socio['tel']);
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
                if (substr($parametros, -1) !== '&') {
                    $parametros .= '&';
                    $qIdentificador = "SELECT identificador FROM v_nexo WHERE id_empresa = '$empresa' AND id_servicio = '" . $servicios_socio['servicio'] . "'";
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
                $info     = [
                    'authorizedKey' => '9d752cb08ef466fc480fba981cfa44a1',
                    'msgId'         => '0',
                    'msgData'       => $mensaje,
                ];

                foreach ($celulares as $celular) {
                    $info['msgRecip'] = $celular;
                    $client           = new SoapClient($servicio, $info);
                    $client->sendSms($info['authorizedKey'], $info['msgId'], $info['msgData'], $info['msgRecip']);
                    $retorno_sms = true;
                }
            }
        }
    }

    $result = (($retorno_servicios && $retorno_datos && $rHistorico) && $retorno_sms);


    if (!$error) copiarPatologiaPiscinaPadron($datos_socio['cedula']);

    return $result;
}




function buscarCelular($numeros)
{
    preg_match_all('/(09)[1-9]{1}\d{6}/x', $numeros, $respuesta);
    $respuesta = (count($respuesta[0]) !== 0) ? $respuesta[0] : false;
    return $respuesta;
}


function registrar_padron_productos_socio($campos, $servicios_beneficiario, $cedula)
{
    require "../../../_conexion250.php";

    try {
        $sql  = "INSERT INTO padron_producto_socio (" . implode(',', $campos) . ") VALUES ('" . implode("', '", $servicios_beneficiario) . "')";
        $consulta = mysqli_query($mysqli250, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR INSERT padron_producto_socio PADRÓN]: $error: BENEFICIARIO OMT $cedula");
        $consulta = false;
    }

    if ($consulta) logger("[OK]: GUARDAR [TABLE] padron_producto_socio $cedula", false);

    mysqli_close($mysqli250);
    return $consulta;
}


function registrar_padron_datos_socio($campos, $datos_beneficiario, $cedula)
{
    require "../../../_conexion250.php";

    try {
        $sql = "INSERT INTO padron_datos_socio (" . implode(',', $campos) . ") VALUES ('" . implode("','", $datos_beneficiario) . "')";
        $consulta = mysqli_query($mysqli250, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR INSERT padron_datos_socio PADRÓN]: $error: BENEFICIARIO OMT $cedula");
        $consulta = false;
    }

    if ($consulta) logger("[OK]: GUARDAR [TABLE] padron_datos_socio $cedula", false);

    mysqli_close($mysqli250);
    return $consulta;
}


function modificar_accion_datos_socio($cedula)
{
    require "../../../_conexion.php";

    try {
        $sql = "UPDATE padron_datos_socio SET accion = '5' WHERE cedula = '$cedula'";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR UPDATE padron_datos_socio PISCINA]: $error: $cedula");
        $consulta = false;
    }

    if ($consulta) logger("[OK]: GUARDAR [TABLE] padron_datos_socio $cedula", false);

    mysqli_close($mysqli);
    return $consulta;
}


function modificar_accion_productos_socio($cedula)
{
    require "../../../_conexion.php";

    try {
        $sql = "UPDATE padron_producto_socio SET accion = '5' WHERE cedula = '$cedula'";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR UPDATE padron_producto_socio PISCINA]: $error: $cedula");
        $consulta = false;
    }

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_datos_usuario($id_usuario)
{
    require "../../../_conexion.php";

    try {
        $sql = "SELECT * FROM usuarios WHERE id = $id_usuario";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA usuarios PISCINA]: $error: $id_usuario");
        $consulta = false;
    }

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_direccion_socio($opcion, $cedula)
{
    require "../../../_conexion.php";
    require "../../../_conexion250.php";

    $nombre_referencia = $opcion == 1 ? "PISCINA" : "PADRÓN";
    $conexion = $opcion == 1 ? $mysqli : $mysqli250;

    try {
        $sql = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
        $consulta = mysqli_query($conexion, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA direcciones_socios $nombre_referencia]: $error: $cedula");
        $consulta = false;
    }

    mysqli_close($mysqli);
    mysqli_close($mysqli250);
    return $consulta;
}


function registrar_direccion_socio($idSocioPadron, $calle, $puerta, $manzana, $solar, $apto, $esquina, $referencia, $cedula)
{
    require "../../../_conexion250.php";

    try {
        $sql = "INSERT INTO direcciones_socios (id_socio, calle, puerta, manzana, solar, apartamento, esquina, referencia, cedula_socio) VALUES ($idSocioPadron, '$calle', '$puerta', '$manzana', '$solar', '$apto', '$esquina', '$referencia', '$cedula')";
        $consulta = mysqli_query($mysqli250, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR INSERT direcciones_socios PADRÓN]: $error: $cedula");
        $consulta = false;
    }

    if ($consulta) {
        global $fecha;
        global $log_file;
        $log_content = "[LOG][$fecha]|direcion guardada/actualizada en padron: $cedula| query: $sql";
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }

    mysqli_close($mysqli250);
    return $consulta;
}


function modificar_direccion_socio($calle, $puerta, $apto, $manzana, $solar, $esquina, $referencia, $cedula)
{
    require "../../../_conexion250.php";

    try {
        $sql = "UPDATE direcciones_socios SET calle = '$calle', puerta = '$puerta', apartamento = '$apto', manzana = '$manzana', solar = '$solar', esquina = '$esquina', referencia = '$referencia' WHERE cedula_socio = '$cedula'";
        $consulta = mysqli_query($mysqli250, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR UPDATE direcciones_socios PADRÓN]: $error: $cedula");
        $consulta = false;
    }

    if ($consulta) {
        global $fecha;
        global $log_file;
        $log_content = "[LOG][$fecha]|direcion guardada/actualizada en padron: $cedula| query: $sql";
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }

    mysqli_close($mysqli250);
    return $consulta;
}


function obtener_beneficiarios_servicios($cedula)
{
    require "../../../_conexion.php";

    try {
        $sql = "SELECT * FROM beneficiarios_servicios WHERE cedula_titular = '$cedula'"; //newform
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA beneficiarios_servicios PISCINA]: $error: $cedula");
        $consulta = false;
    }

    mysqli_close($mysqli);
    return $consulta;
}


function registrar_beneficiarios_servicios($nomBen, $cedBen, $fnBen, $telBen, $cedula, $numServben)
{
    require "../../../_conexion250.php";

    try {
        $sql = "INSERT INTO beneficiarios_servicios VALUES (null, '$nomBen', '$cedBen', '$fnBen', '$telBen', '$cedula', '$numServben')";
        $consulta = mysqli_query($mysqli250, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR INSERT beneficiarios_servicios PADRÓN]: $error: $cedula");
        $consulta = false;
    }

    if ($consulta) {
        global $fecha;
        global $log_file;

        $log_content = "[LOG][$fecha]|datos de beneficiarios guardados en padron: $cedula| query: $sql";
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }

    mysqli_close($mysqli250);
    return $consulta;
}


function modificar_beneficiarios_servicios($cedula)
{
    require "../../../_conexion.php";

    try {
        $sql = "UPDATE beneficiarios_servicios SET concretado = 1 WHERE cedula_titular = '$cedula'";
        $consulta = mysqli_query($mysqli, $sql); //newform
    } catch (\Throwable $error) {
        logger("[ERROR UPDATE beneficiarios_servicios PISCINA]: $error: $cedula");
        $consulta = false;
    }

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_datos_filial($sucursal)
{
    require "../../../_conexion.php";

    try {
        $sql = "SELECT * FROM filiales WHERE nro_filial = $sucursal";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA filiales PISCINA]: $error: $sucursal");
        $consulta = false;
    }

    mysqli_close($mysqli);
    return $consulta;
}


function registrar_historico_reportes($id, $idUser, $idBen, $alta, $fecha_registro, $total_importe, $idSucursal, $idGrupo, $metodo_pago, $observaciones, $radio, $origen_venta)
{
    require "../../../_conexion.php";

    try {
        $sql = "INSERT INTO historico_reportes VALUES ($id, $idUser, $idBen, '$alta', '$fecha_registro', '$total_importe', '$total_importe', '$idSucursal', '$idGrupo', '$metodo_pago', '$observaciones', '$radio', '$origen_venta')";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR INSERT historico_reportes PISCINA]: $error: $idUser");
        $consulta = false;
    }

    $respuesta = $consulta != false ? mysqli_insert_id($mysqli) : false;

    if ($respuesta != false) {
        global $log_file;
        global $log_content;
        $log_content = "[LOG][$fecha]|datos guardados id_cliente $idBen, id historico: $respuesta|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }

    mysqli_close($mysqli);
    return $respuesta;
}


function obtener_servicios($nroServicio)
{
    require "../../../_conexion.php";

    try {
        $sql = "SELECT id FROM servicios WHERE nro_servicio = '$nroServicio'";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA servicios PISCINA]: $error: $nroServicio");
        $consulta = false;
    }

    mysqli_close($mysqli);
    return $consulta;
}


function registrar_historico_reporte_servicios($id, $idHistorico, $idServicio, $importe, $horas, $promoCr)
{
    require "../../../_conexion.php";

    try {
        $sql = "INSERT INTO historico_reportes_servicios VALUES ($id, '$idHistorico', '$idServicio', '$importe', '$horas', $promoCr)";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR INSERT historico_reportes_servicios PISCINA]: $error: $idHistorico");
        $consulta = false;
    }

    $respuesta = $consulta != false ? mysqli_insert_id($mysqli) : false;

    if ($respuesta != false) {
        global $fecha;
        global $log_file;
        global $log_content;
        $log_content = "[LOG][$fecha]|servicio $idServicio guardado id historico: $respuesta|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }

    mysqli_close($mysqli);
    return $respuesta;
}


function obtener_datos_padron_socio($opcion, $opcion_bd, $param)
{
    require "../../../_conexion.php";
    require "../../../_conexion250.php";

    $nombre_referencia = $opcion_bd == 1 ? "PISCINA" : "PADRÓN";
    $conexion = $opcion_bd == 1 ? $mysqli : $mysqli250;

    $nombre_param = $opcion == 1 ? "ID" : "CI";
    $where = $opcion == 1 ? "id = '$param'" : "cedula = '$param'";

    try {
        $sql = "SELECT * FROM padron_datos_socio WHERE $where";
        $consulta = mysqli_query($conexion, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA padron_datos_socio $nombre_referencia]: $error [$nombre_param]: $param");
        $consulta = false;
    }

    mysqli_close($mysqli);
    mysqli_close($mysqli250);
    return $consulta;
}


function obtener_padron_productos_socio($opcion, $opcion_bd, $cedula)
{
    require "../../../_conexion.php";
    require "../../../_conexion250.php";

    $nombre_referencia = $opcion_bd == 1 ? "PISCINA" : "PADRÓN";
    $conexion = $opcion_bd == 1 ? $mysqli : $mysqli250;

    $select = "*";
    if ($opcion == 2) $select = "servicio, sum(hora) AS horas, sum(importe) AS importe";
    if ($opcion == 7) $select = "servicio, sum(importe) AS total_importe, sum(hora) AS horas";

    $where = "";
    if (in_array($opcion, [1, 3])) $where = "cedula_titular_gf = '$cedula'";
    if ($opcion == 2) $where = "cedula = '$cedula'";
    if ($opcion == 4) $where = "abm = 'ALTA' AND cedula = '$cedula' AND cod_promo = '24'";
    if ($opcion == 5) $where = "servicio = '70' AND cedula_titular_gf = '$cedula' AND abm = 'ALTA'";
    if ($opcion == 6) $where = "cedula = '$cedula' AND accion = '1'";
    if ($opcion == 7) $where = "cedula = '$cedula' AND abm <> '0'";

    $group_by = "";
    if (in_array($opcion, [2, 7])) $group_by = "GROUP BY servicio";
    if ($opcion == 3) $group_by = "GROUP BY cedula";

    try {
        $sql = "SELECT $select FROM padron_producto_socio WHERE $where $group_by";
        $consulta = mysqli_query($conexion, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR CONSULTA padron_producto_socio $nombre_referencia]: $error [CI]: $cedula");
        $consulta = false;
    }

    mysqli_close($mysqli);
    mysqli_close($mysqli250);
    return $consulta;
}




function obtenerGrupoUsuario($idVendedor)
{ //ucem
    global $mysqli;

    $qIdGrupo = "SELECT idgrupo FROM usuarios WHERE id = $idVendedor";
    $select = mysqli_query($mysqli, $qIdGrupo);

    return mysqli_num_rows($select) > 0 ? mysqli_fetch_assoc($select)['idgrupo'] : false;
}


/* Copia las patologías del documento inidicado del 1.13 al 1.250 */
function copiarPatologiaPiscinaPadron($_documentoSocio)
{
    require __DIR__ . '/../../_conexion.php';
    require __DIR__ . '/../../_conexion250.php';

    $documentoSocio = $mysqli->real_escape_string($_documentoSocio);
    $qSelect = "SELECT * FROM patologias_socio WHERE documento_socio = '$documentoSocio'";
    $select = mysqli_query($mysqli, $qSelect);
    $patologiasSocio = mysqli_fetch_assoc($select);

    foreach ($patologiasSocio as $patologia) {
        $documentoSocio = mysqli_real_escape_string($mysqli250, $patologia['documento_socio']);
        $idPatologia = mysqli_real_escape_string($mysqli250, $patologia['id_patologia']);
        $observacion = mysqli_real_escape_string($mysqli250, $patologia['observacion']);

        $qInsert = "INSERT INTO patologias_socio (documento_socio, id_patologia, observacion, fecha) VALUES ('$documentoSocio', '$idPatologia', '$observacion', NOW())";
        mysqli_query($mysqli250, $qInsert);
    }
}


function eliminar_registros_padron($opcion, $cedula)
{
    global $mysqli250;

    $tabla = $opcion == 1 ? "padron_datos_socio" : "padron_producto_socio";

    try {
        $sql = "DELETE FROM {$tabla} WHERE cedula = '$cedula'";
        $consulta = mysqli_query($mysqli250, $sql);
    } catch (\Throwable $error) {
        logger("[ERROR AL ELIMINAR REGISTROS DE $tabla PADRÓN]: $error [CI]: $cedula");
        $consulta = false;
    }

    mysqli_close($mysqli250);
    return $consulta;
}



function devolver_error($mensaje, $error = true)
{
    $response['error'] = $error;
    $response['mensaje'] = $mensaje;
    die(json_encode($response));
}
