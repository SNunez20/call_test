<?php
require_once "../../../logger.php";


function guardarPadron($id_cliente, $mysqli, $mysqli250, $mysqli1310, $cedulaAfiliado)
{
    global $mysqli250_TOCS;

    $error             = false;
    $result            = false;
    $accion            = null;
    $datos_socio       = [];
    $retorno_servicios = false;
    $qActualizar       = 'none';
    $grupo_familiar    = false;
    $omt               = false;
    $retornoHistorico  = false;

    try {
        $qDatos = "SELECT * FROM padron_datos_socio WHERE id = '$id_cliente'";
        $rDatos = mysqli_query($mysqli, $qDatos);
    } catch (\Throwable $errores) {
        registrar_errores($qDatos, "guardarPadron_inspira.php", $errores);
        $error = true;
    }


    if ($rDatos) {
        while ($row = mysqli_fetch_assoc($rDatos)) {
            $accion          = $row['accion'];
            $idUser          = $row['id_usuario'];
            $alta            = $row['alta'];
            $metodo_pago     = $row['metodo_pago'];
            $origen_venta    = $row['origen_venta'];
            $estadoActual    = $row['estado'];
            $idGrupoVendedor = obtenerGrupoUsuario($idUser);

            if ($origen_venta == 5) $origenVta = 'UDEMM';
            else if ($idGrupoVendedor == 10013) $origenVta = 'UCEM';
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
    }

    if (!$error) {
        $cedula          = $datos_socio['cedula'];
        $servicios_socio = [];

        try {
            $qServicios = "SELECT * FROM padron_producto_socio where cedula = '$cedula' AND accion='1'";
            $rServicios = mysqli_query($mysqli, $qServicios);
        } catch (\Throwable $errores) {
            registrar_errores($qServicios, "guardarPadron_inspira.php", $errores);
            $error = true;
        }

        if ($rServicios) {
            while ($row = mysqli_fetch_assoc($rServicios)) {

                // COMPRUEBA SI TIENE GRUPO FAMILIAR
                if ($row["servicio"] == 63 || $row["servicio"] == 65) $grupo_familiar = true;

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
        }
    }

    if (!$error) {
        if ($accion == '1') { //insertar nuevo socio en el padron
            $campos = array_keys($datos_socio);
            try {
                $qInsertar = "INSERT INTO padron_datos_socio VALUES(null,'" . implode("','", $datos_socio) . "')";
                $retorno_datos = mysqli_query($mysqli250, $qInsertar);
            } catch (\Throwable $errores) {
                registrar_errores($qInsertar, "guardarPadron_inspira.php", $errores);
                $error = true;
            }
            if ($retorno_datos) $idSocioPadron = mysqli_insert_id($mysqli250); //dir2
        } else if (in_array($accion, ["4", "3"])) { //actualizar datos del socio en el padron
            $data = [];
            foreach ($datos_socio as $column => $value) {
                $data[] = "$column='$value'";
            }
            try {
                $qActualizar = "UPDATE padron_datos_socio SET ";
                $qActualizar .= implode(',', $data);
                $qActualizar .= " WHERE cedula = '" . $datos_socio['cedula'] . "'";
                $retorno_datos = mysqli_query($mysqli250, $qActualizar);
            } catch (\Throwable $errores) {
                registrar_errores($qActualizar, "guardarPadron_inspira.php", $errores);
                $error = true;
            }
        } else {
            $error = true;
        }
    }

    if (!$error) {
        $totalIncremento = 0;
        // insertamos los servicios nuevos en el padron
        for ($i = 0; $i < count($servicios_socio); $i++) {
            $campos = array_keys($servicios_socio[$i]);
            if (!in_array($servicios_socio[$i]['servicio'], ["08", "110"])) $totalIncremento += (int) $servicios_socio[$i]['importe'];
            try {
                $qInsertarServicios = "INSERT INTO padron_producto_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_socio[$i]) . "')";
                $retorno_servicios = mysqli_query($mysqli250, $qInsertarServicios);
            } catch (\Throwable $errores) {
                registrar_errores($qInsertarServicios, "guardarPadron_inspira.php", $errores);
                $error = true;
            }
        }

        if ($retorno_servicios) {
            // ACTUALIZA LA ACCION A 5 TANTO EN DATOS COMO PRODUCTOS PARA INDICAR QUE PASO A PADRON
            try {
                $qActualizarAccionDatos = "UPDATE padron_datos_socio set accion = '5' where cedula = $cedulaAfiliado";
                $result = mysqli_query($mysqli, $qActualizarAccionDatos);
            } catch (\Throwable $errores) {
                registrar_errores($qActualizarAccionDatos, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            try {
                $qActualizarAcccionProductos = "UPDATE padron_producto_socio set accion = '5' where cedula = $cedulaAfiliado";
                $result = mysqli_query($mysqli, $qActualizarAcccionProductos);
            } catch (\Throwable $errores) {
                registrar_errores($qActualizarAcccionProductos, "guardarPadron_inspira.php", $errores);
                $error = true;
            }
        }

        // ! #################################################################################
        // ! COMPRUEBA SI EXISTE GRUPO FAMILIAR PARA RECUPERAR LOS DATOS DE LOS BENEFICIARIOS#
        // ! #################################################################################
        if ($grupo_familiar) {
            // GRUPO FAMILIAR
            $cedulasBenficiarios = [];

            // RECUPERA TODOS LOS BENEFICIARIOS
            try {
                $query = "SELECT * FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'";
                $result = mysqli_query($mysqli, $query);
            } catch (\Throwable $errores) {
                registrar_errores($query, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $ciBeneficiario = $row['cedula'];
                    // SERVICIOS DEL BENEFICIARIO
                    $servicios_beneficiario = [
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
                    ];

                    $campos = array_keys($servicios_beneficiario);
                    try {
                        $query  = "INSERT INTO padron_producto_socio(" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_beneficiario) . "')";
                        $result2 = mysqli_query($mysqli250, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "guardarPadron_inspira.php", $errores);
                        $error = true;
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
                    try {
                        $query  = "SELECT * FROM padron_datos_socio WHERE cedula = '$ci'";
                        $result = mysqli_query($mysqli, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }

                    if (mysqli_num_rows($result) > 0) {
                        $row                = mysqli_fetch_assoc($result);
                        $datos_beneficiario = [
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
                        ];

                        $campos = array_keys($datos_beneficiario);
                        try {
                            $query  = "INSERT INTO padron_datos_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $datos_beneficiario) . "')";
                            $result4 = mysqli_query($mysqli250, $query);
                        } catch (\Throwable $errores) {
                            registrar_errores($query, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }


                        if ($result4) {
                            //dir2
                            $idSocioPadronBen = mysqli_insert_id($mysqli250);
                            try {
                                $qDatosDir = "SELECT calle,puerta,manzana,solar,apartamento,esquina,referencia FROM direcciones_socios WHERE cedula_socio ='$ci'";
                                $rDatosDir = mysqli_query($mysqli, $qDatosDir);
                            } catch (\Throwable $errores) {
                                registrar_errores($qDatosDir, "guardarPadron_inspira.php", $errores);
                                $error = true;
                            }


                            if ($rDatosDir && mysqli_num_rows($rDatosDir) > 0) {
                                $row        = mysqli_fetch_assoc($rDatosDir);
                                $calle      = mysqli_real_escape_string($mysqli250, $row['calle']);
                                $puerta     = $row['puerta'];
                                $manzana    = $row['manzana'];
                                $solar      = $row['solar'];
                                $apto       = $row['apartamento'];
                                $esquina    = mysqli_real_escape_string($mysqli250, $row['esquina']);
                                $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);

                                try {
                                    $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPadronBen,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$ci')";
                                    $rDir = mysqli_query($mysqli250, $qInsertDireccion);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qInsertDireccion, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($grupo_familiar) {
            try {
                $query = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'";
                $result = mysqli_query($mysqli, $query);
            } catch (\Throwable $errores) {
                registrar_errores($query, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $ci = $row["cedula"];
                    try {
                        $q1 = "UPDATE padron_datos_socio SET accion='5' WHERE cedula='$ci'";
                        $qr1 = mysqli_query($mysqli, $q1);
                    } catch (\Throwable $errores) {
                        registrar_errores($q1, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }
                    try {
                        $q2 = "UPDATE padron_producto_socio SET accion='5' WHERE cedula='$ci'";
                        $qr2 = mysqli_query($mysqli, $q2);
                    } catch (\Throwable $errores) {
                        registrar_errores($q2, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }
                }
            }
        }

        // ! #################################################################################
        // ! COMPRUEBA SI EXISTE ALGUN BENEFICIARIO OMT#
        // ! #################################################################################
        try {
            $qOmt = "SELECT cedula,importe from padron_producto_socio WHERE servicio='70' AND cedula_titular_gf = '$cedulaAfiliado' AND abm='ALTA'";
            $resultomt = mysqli_query($mysqli, $qOmt);
        } catch (\Throwable $errores) {
            registrar_errores($qOmt, "guardarPadron_inspira.php", $errores);
            $error = true;
        }

        if ($resultomt) {
            if (mysqli_num_rows($resultomt) > 0) {
                $row       = mysqli_fetch_assoc($resultomt);
                $cedulaomt = $row['cedula'];
                $omt       = true;
            }
        }

        if ($omt) {
            $cedulasBenficiarios = [];

            // RECUPERA TODOS LOS BENEFICIARIOS
            try {
                $query = "SELECT * FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'";
                $result = mysqli_query($mysqli, $query);
            } catch (\Throwable $errores) {
                registrar_errores($query, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $ciBeneficiario = $row['cedula'];
                    // SERVICIOS DEL BENEFICIARIO
                    $servicios_beneficiario = [
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
                    ];

                    $campos = array_keys($servicios_beneficiario);
                    try {
                        $query  = "INSERT INTO padron_producto_socio(" . implode(',', $campos) . ") VALUES('" . implode("','", $servicios_beneficiario) . "')";
                        $result2 = mysqli_query($mysqli250, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }

                    // GUARDO LAS CEDULAS DE LOS BENEFICIARIOS
                    if (!in_array($ciBeneficiario, $cedulasBenficiarios))
                        array_push($cedulasBenficiarios, $ciBeneficiario);
                }
            }

            // GUARDA LOS DATOS DE LOS BENEFICIARIOS
            if (count($cedulasBenficiarios) > 0) {
                foreach ($cedulasBenficiarios as $key => $ci) {
                    // DATOS DEL BENEFICIARIO
                    try {
                        $query  = "SELECT * FROM padron_datos_socio WHERE cedula = '$ci'";
                        $result = mysqli_query($mysqli, $query);
                    } catch (\Throwable $errores) {
                        registrar_errores($query, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }

                    if (mysqli_num_rows($result) > 0) {
                        $row                = mysqli_fetch_assoc($result);
                        $datos_beneficiario = [
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
                        ];

                        $campos = array_keys($datos_beneficiario);
                        try {
                            $query  = "INSERT INTO  padron_datos_socio (" . implode(',', $campos) . ") VALUES('" . implode("','", $datos_beneficiario) . "')";
                            $result4 = mysqli_query($mysqli250, $query);
                        } catch (\Throwable $errores) {
                            registrar_errores($query, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        if ($result4) {
                            //dir2
                            $idSocioPadronBen = mysqli_insert_id($mysqli250);
                            try {
                                $qDatosDir = "SELECT calle,puerta,manzana,solar,apartamento,esquina,referencia FROM direcciones_socios WHERE cedula_socio ='$ci'";
                                $rDatosDir = mysqli_query($mysqli, $qDatosDir);
                            } catch (\Throwable $errores) {
                                registrar_errores($qDatosDir, "guardarPadron_inspira.php", $errores);
                                $error = true;
                            }

                            if ($rDatosDir && mysqli_num_rows($rDatosDir) > 0) {
                                $row        = mysqli_fetch_assoc($rDatosDir);
                                $calle      = mysqli_real_escape_string($mysqli250, $row['calle']);
                                $puerta     = $row['puerta'];
                                $manzana    = $row['manzana'];
                                $solar      = $row['solar'];
                                $apto       = $row['apartamento'];
                                $esquina    = mysqli_real_escape_string($mysqli250, $row['esquina']);
                                $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);

                                try {
                                    $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPadronBen,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$ci')";
                                    $rDir = mysqli_query($mysqli250, $qInsertDireccion);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qInsertDireccion, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }
                            }
                        }
                    }
                }
            }

            try {
                $q1 = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedulaAfiliado'";
                $rq1 = mysqli_query($mysqli, $q1);
            } catch (\Throwable $errores) {
                registrar_errores($q1, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            if ($rq1) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $ci = $row["cedula"];
                    try {
                        $q1 = "UPDATE padron_datos_socio SET accion='5' WHERE cedula='$ci'";
                        $rq1 = mysqli_query($mysqli, $q1);
                    } catch (\Throwable $errores) {
                        registrar_errores($q1, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }
                    try {
                        $q2 = "UPDATE padron_producto_socio SET accion='5' WHERE cedula='$ci'";
                        $rq2 = mysqli_query($mysqli, $q2);
                    } catch (\Throwable $errores) {
                        registrar_errores($q2, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }
                }
            }
        }

        if ($retorno_servicios && $retorno_datos) {

            $retornoHistorico = false;
            $retornoServicios = false;
            $fecha            = date('Y-m-d');
            $log_error        = "../../../logs/errors_reportes/error_$fecha.log";
            $log_file         = "../../../logs/logs_reportes/log_$fecha.log";
            $idFilial         = '';
            $idGrupo          = '';
            $aplicaPromoCr    = false; //conva
            $sucur            = $datos_socio['sucursal'];


            try {
                $qIdFilial = "SELECT id FROM filiales WHERE nro_filial = $sucur";
                $rIdSuc = mysqli_query($mysqli, $qIdFilial);
            } catch (\Throwable $errores) {
                registrar_errores($qIdFilial, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            $idFilial = ($rIdSuc && mysqli_num_rows($rIdSuc) > 0) ? mysqli_fetch_assoc($rIdSuc)['id'] : $idFilial;

            try {
                $qIdGrupo = "SELECT idgrupo FROM usuarios WHERE id = $idUser";
                $rIdGrupo = mysqli_query($mysqli, $qIdGrupo);
            } catch (\Throwable $errores) {
                registrar_errores($qIdGrupo, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            $idGrupo = ($rIdGrupo && mysqli_num_rows($rIdGrupo) > 0) ? mysqli_fetch_assoc($rIdGrupo)['idgrupo'] : $idGrupo;



            try {
                $qHistoricoReporte = "INSERT INTO historico_reportes VALUES(null, $idUser, $id_cliente, '$alta', '" . $servicios_socio[0]['fecha_registro'] . "','" . $datos_socio['total_importe'] . "','$totalIncremento','$idFilial','$idGrupo','$metodo_pago','" . $datos_socio['observaciones'] . "','" . $datos_socio['radio'] . "','$origen_venta')";
                $rHist = mysqli_query($mysqli, $qHistoricoReporte);
            } catch (\Throwable $errores) {
                registrar_errores($qHistoricoReporte, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            $idHistoricoReporte = mysqli_insert_id($mysqli);

            if ($rHist) {

                if ($alta == '1') {
                    //conva comprobamos si tienen convalecencia de regalo
                    try {
                        $qPromoCr = "SELECT id FROM padron_producto_socio WHERE abm='ALTA' AND cedula = '$cedula' AND cod_promo='24'";
                        $rPromoCr = mysqli_query($mysqli, $qPromoCr);
                    } catch (\Throwable $errores) {
                        registrar_errores($qPromoCr, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }

                    if ($rPromoCr && mysqli_num_rows($rPromoCr) > 0) $aplicaPromoCr = true;
                }

                $retornoHistorico = true;

                try {
                    $qServicios = "SELECT servicio, sum(importe) AS total_importe, sum(hora) AS horas FROM padron_producto_socio WHERE cedula = $cedula AND abm <> '0' GROUP BY servicio";
                    $rServ = mysqli_query($mysqli, $qServicios);
                } catch (\Throwable $errores) {
                    registrar_errores($qServicios, "guardarPadron_inspira.php", $errores);
                    $error = true;
                }

                if ($rServ) {
                    while ($row = mysqli_fetch_assoc($rServ)) {
                        $nroServ      = $row['servicio'];
                        $totalImporte = $row['total_importe'];
                        $horas        = $row['horas'];

                        try {
                            $qIdServ = "SELECT id_servicio FROM numeros_servicios WHERE numero_servicio = '$nroServ' GROUP BY id_servicio";
                            $rIdServ = mysqli_query($mysqli1310, $qIdServ);
                        } catch (\Throwable $errores) {
                            registrar_errores($qIdServ, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        $idServ  = mysqli_fetch_assoc($rIdServ)['id_servicio'];
                        $promoCr = ($aplicaPromoCr && $nroServ == '01') ? 1 : 0; //conva

                        try {
                            $qHistoricoServicios = "INSERT INTO historico_reportes_servicios VALUES(null, $idHistoricoReporte,$idServ, '$totalImporte','$horas',$promoCr)";
                            $rHistoricoServicios = mysqli_query($mysqli, $qHistoricoServicios);
                        } catch (\Throwable $errores) {
                            registrar_errores($qHistoricoServicios, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        if ($rHistoricoServicios) {
                            $retornoServicios = true;
                            $idHistoricoServicio = mysqli_insert_id($mysqli);
                        }
                    }

                    if ($retornoHistorico && $retornoServicios) {
                        //dir2
                        try {
                            $qDatosDir = "SELECT calle, puerta, manzana, solar, apartamento, esquina, referencia FROM direcciones_socios WHERE cedula_socio ='$cedula'";
                            $rDatosDir = mysqli_query($mysqli, $qDatosDir);
                        } catch (\Throwable $errores) {
                            registrar_errores($qDatosDir, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        if (mysqli_num_rows($rDatosDir) > 0) {
                            $row     = mysqli_fetch_assoc($rDatosDir);
                            $calle   = mysqli_real_escape_string($mysqli250, $row['calle']);
                            $puerta  = $row['puerta'];
                            $manzana = $row['manzana'];
                            $solar   = $row['solar'];
                            $apto    = $row['apartamento'];
                            $esquina = mysqli_real_escape_string($mysqli250, $row['esquina']);
                            $referencia = mysqli_real_escape_string($mysqli250, $row['referencia']);

                            try {
                                $qExistePadron = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
                                $rExistePadron = mysqli_query($mysqli250, $qExistePadron);
                            } catch (\Throwable $errores) {
                                registrar_errores($qExistePadron, "guardarPadron_inspira.php", $errores);
                                $error = true;
                            }


                            if ($qExistePadron && mysqli_num_rows($rExistePadron) > 0) {
                                try {
                                    $qInsertDireccion = "UPDATE direcciones_socios SET calle = '$calle', puerta = '$puerta', apartamento = '$apto', manzana = '$manzana', solar = '$solar', esquina = '$esquina', referencia = '$referencia' WHERE cedula_socio = '$cedula'";
                                    $rDir = mysqli_query($mysqli250, $qInsertDireccion);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qInsertDireccion, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }
                            } else {
                                try {
                                    $qIdPadron = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
                                    $rIdPadron = mysqli_query($mysqli250, $qIdPadron);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qIdPadron, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }

                                $idSocioPadron = mysqli_fetch_assoc($rIdPadron)['id'];

                                try {
                                    $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio, calle, puerta, manzana, solar, apartamento, esquina, referencia, cedula_socio) VALUES ($idSocioPadron,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$cedula')";
                                    $rDir = mysqli_query($mysqli250, $qInsertDireccion);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qInsertDireccion, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }
                            }
                        } else {
                            $rDir = true;
                        }

                        try {
                            $qDatosBenServ = "SELECT * FROM beneficiarios_servicios WHERE cedula_titular ='$cedula'"; //newform
                            $rDatosBenServ = mysqli_query($mysqli, $qDatosBenServ);
                        } catch (\Throwable $errores) {
                            registrar_errores($qDatosBenServ, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        if ($rDatosBenServ && mysqli_num_rows($rDatosBenServ) > 0) {
                            while ($row = mysqli_fetch_assoc($rDatosBenServ)) {
                                $nomBen     = $row['nombre'];
                                $cedBen     = $row['cedula'];
                                $fnBen      = $row['fecha_nacimiento'];
                                $telBen     = $row['telefono'];
                                $numServben = $row['num_servicio'];

                                try {
                                    $qInsertBenServ = "INSERT INTO beneficiarios_servicios VALUES (null, '$nomBen','$cedBen','$fnBen','$telBen','$cedula','$numServben')";
                                    $rInsertBenServ = mysqli_query($mysqli250, $qInsertBenServ);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qInsertBenServ, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }
                            }

                            try {
                                $qUpdateBS = "UPDATE beneficiarios_servicios SET concretado = 1 WHERE cedula_titular = '$cedula'";
                                $rUpdateBS = mysqli_query($mysqli, $qUpdateBS); //newform
                            } catch (\Throwable $errores) {
                                registrar_errores($qUpdateBS, "guardarPadron_inspira.php", $errores);
                                $error = true;
                            }
                        }
                    }

                    try {
                        $qBeneficiarios = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedula' GROUP BY cedula";
                        $rBeneficiarios = mysqli_query($mysqli, $qBeneficiarios);
                    } catch (\Throwable $errores) {
                        registrar_errores($qBeneficiarios, "guardarPadron_inspira.php", $errores);
                        $error = true;
                    }

                    if (mysqli_num_rows($rBeneficiarios) > 0 && $alta = '1') {
                        while ($rowBen = mysqli_fetch_assoc($rBeneficiarios)) {
                            $cedulaBen = $rowBen['cedula'];
                            try {
                                $qDatosBen = "SELECT id, alta, total_importe, sucursal, observaciones, radio FROM padron_datos_socio WHERE cedula = $cedulaBen";
                                $rDatosBen = mysqli_query($mysqli, $qDatosBen);
                            } catch (\Throwable $errores) {
                                registrar_errores($qDatosBen, "guardarPadron_inspira.php", $errores);
                                $error = true;
                            }

                            if (mysqli_num_rows($rDatosBen) > 0) {
                                while ($datos = mysqli_fetch_assoc($rDatosBen)) {
                                    $idBen         = $datos['id'];
                                    $alta          = $datos['alta'];
                                    $total_importe = $datos['total_importe'];
                                    $sucursal      = $datos['sucursal'];
                                    $observaciones = $datos['observaciones'];
                                }

                                try {
                                    $qIdSucursal = "SELECT id FROM filiales WHERE nro_filial =$sucursal";
                                    $rIdSucursal = mysqli_query($mysqli, $qIdSucursal);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qIdSucursal, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }

                                $idSucursal = mysqli_fetch_assoc($rIdSucursal)['id'];

                                try {
                                    $qHistoricoReporteBen = "INSERT INTO historico_reportes VALUES(null, $idUser, $idBen, '$alta', '" . $servicios_socio[0]['fecha_registro'] . "','$total_importe','$total_importe','$idSucursal','$idGrupo','$metodo_pago','$observaciones','" . $datos_socio['radio'] . "','$origen_venta')";
                                    $rInsert = mysqli_query($mysqli, $qHistoricoReporteBen);
                                } catch (\Throwable $errores) {
                                    registrar_errores($qHistoricoReporteBen, "guardarPadron_inspira.php", $errores);
                                    $error = true;
                                }


                                if ($rInsert) {
                                    $idHistorico = mysqli_insert_id($mysqli);
                                    try {
                                        $qServiciosBen = "SELECT servicio, sum(hora) AS horas, sum(importe) AS importe FROM padron_producto_socio WHERE cedula = '$cedulaBen' GROUP BY servicio";
                                        $rServiciosBen = mysqli_query($mysqli, $qServiciosBen);
                                    } catch (\Throwable $errores) {
                                        registrar_errores($qServiciosBen, "guardarPadron_inspira.php", $errores);
                                        $error = true;
                                    }

                                    if ($rServiciosBen) {
                                        while ($row = mysqli_fetch_assoc($rServiciosBen)) {
                                            $nroServicio = $row['servicio'];
                                            $importe     = $row['importe'];
                                            $horas       = $row['horas'];

                                            try {
                                                $qIdServicio = "SELECT id_servicio FROM numeros_servicios WHERE numero_servicio = '$nroServicio' GROUP BY id_servicio";
                                                $rIdServicio = mysqli_query($mysqli1310, $qIdServicio);
                                            } catch (\Throwable $errores) {
                                                registrar_errores($qIdServicio, "guardarPadron_inspira.php", $errores);
                                                $error = true;
                                            }

                                            $idServicio = mysqli_fetch_assoc($rIdServicio)['id_servicio'];

                                            try {
                                                $qGuardarServicio = "INSERT INTO historico_reportes_servicios VALUES (null, $idHistorico, $idServicio, $importe, $horas)";
                                                $rGuardarServicio = mysqli_query($mysqli, $qGuardarServicio);
                                            } catch (\Throwable $errores) {
                                                registrar_errores($qGuardarServicio, "guardarPadron_inspira.php", $errores);
                                                $error = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $rHistorico = $retornoHistorico && $retornoServicios && $rDir ? true : false;
        logger("RESULTADO LINEA 940 = rHistorico=$rHistorico => retornoHistorico=$retornoHistorico retornoServicios=$retornoServicios rDir=$rDir", false);

        
        $resultado = !$retorno_servicios || !$retorno_datos || !$rHistorico;
        logger("RESULTADO LINEA 944 = resultado=$resultado => retorno_servicios=$retorno_servicios retorno_datos=$retorno_datos rHistorico=$rHistorico", false);

        if ($resultado) {
            if ($alta == '1') {
                try {
                    $q1 = "DELETE FROM padron_datos_socio WHERE cedula ='$cedula'";
                    $rq1 = mysqli_query($mysqli250, $q1);
                } catch (\Throwable $errores) {
                    registrar_errores($q1, "guardarPadron_inspira.php", $errores);
                    $error = true;
                }

                try {
                    $q2 = "DELETE FROM padron_producto_socio WHERE cedula ='$cedula'";
                    $rq2 = mysqli_query($mysqli250, $q2);
                } catch (\Throwable $errores) {
                    registrar_errores($q2, "guardarPadron_inspira.php", $errores);
                    $error = true;
                }
            }

            try {
                $q1 = "UPDATE padron_datos_socio SET accion = '$accion' WHERE cedula = '$cedula'";
                $rq1 = mysqli_query($mysqli, $q1);
            } catch (\Throwable $errores) {
                registrar_errores($q1, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            try {
                $q2 = "UPDATE padron_producto_socio SET accion = '1' WHERE cedula = '$cedula' AND fecha_registro = '" . $servicios_socio[0]['fecha_registro'] . "'";
                $rq2 = mysqli_query($mysqli, $q2);
            } catch (\Throwable $errores) {
                registrar_errores($q2, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            try {
                $q3 = "UPDATE padron_producto_socio SET accion = '3' WHERE cedula = '$cedula' AND fecha_registro <> '" . $servicios_socio[0]['fecha_registro'] . "'";
                $rq3 = mysqli_query($mysqli, $q3);
            } catch (\Throwable $errores) {
                registrar_errores($q3, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            try {
                $qBen = "SELECT id,cedula from padron_producto_socio WHERE cedula_titular_gf = '$cedula'";
                $resultomt = mysqli_query($mysqli250, $qBen);
            } catch (\Throwable $errores) {
                registrar_errores($qBen, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            if ($resultomt) {
                if (mysqli_num_rows($resultomt) > 0) {
                    while ($row = mysqli_fetch_assoc($resultomt)) {
                        $cedulaBen = $row['cedula'];
                        try {
                            $q1 = "DELETE FROM padron_datos_socio WHERE cedula = '$cedulaBen'";
                            $rq1 = mysqli_query($mysqli250, $q1);
                        } catch (\Throwable $errores) {
                            registrar_errores($q1, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        try {
                            $q2 = "DELETE FROM padron_producto_socio WHERE cedula = '$cedulaBen'";
                            $rq2 = mysqli_query($mysqli250, $q2);
                        } catch (\Throwable $errores) {
                            registrar_errores($q2, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }
                    }
                }
            }
        }

        $resultados = $retorno_servicios && $retorno_datos && $rHistorico ? true : false;
        logger("RESULTADO LINEA 1019 = resultados=$resultados => retorno_servicios=$retorno_servicios retorno_datos=$retorno_datos rHistorico=$rHistorico", false);

        // envio de sms con terminos y condiciones al socio
        if ($resultados) {

            // envio de sms con terminos y condiciones al socio
            $celulares   = buscarCelular($datos_socio['tel']);
            $total       = $datos_socio['total_importe'];
            $sucursal    = $datos_socio['sucursal'];
            $retorno_sms = false;


            if (in_array($sucursal, [1372, 1373, 1374])) $empresa = 3;
            else if (in_array($sucursal, [1370, 1371])) $empresa = 2;
            else $empresa = 1;


            try {
                //$qLink = "SELECT empresa, link FROM terminos_y_condiciones.empresa WHERE id = $empresa";
                $qLink = "SELECT empresa, link FROM empresa WHERE id = $empresa";
                $rLink = mysqli_query($mysqli250_TOCS, $qLink);
            } catch (\Throwable $errores) {
                registrar_errores($qLink, "guardarPadron_inspira.php", $errores);
                $error = true;
            }

            if ($rLink) {
                while ($row = mysqli_fetch_assoc($rLink)) {
                    $empresaNombre = $row['empresa'];
                    $link = $row['link'] . '?' . mb_strtolower(substr($empresaNombre, 0, 1));
                    $parametros = '&';
                }

                for ($i = 0; $i < count($servicios_socio); $i++) {
                    if (substr($parametros, -1) !== '&') {
                        $parametros .= '&';

                        try {
                            $qIdentificador = "SELECT identificador FROM v_nexo WHERE id_empresa = '$empresa' AND id_servicio = '" . $servicios_socio['servicio'] . "'";
                            $rIdentificador = mysqli_query($mysqli250_TOCS, $qIdentificador);
                        } catch (\Throwable $errores) {
                            registrar_errores($qIdentificador, "guardarPadron_inspira.php", $errores);
                            $error = true;
                        }

                        if ($rIdentificador) {
                            while ($row = mysqli_fetch_assoc($rIdentificador)) {
                                $parametros .= $row['identificador'];
                            }
                        }
                    }
                }

                if (!in_array($empresa, [2, 3])) {
                    $mensaje = "Bienvenido a $empresaNombre, puede ver los terminos y condiciones de su contrato en $link" . $parametros;
                    $servicio = "http://192.168.104.6/apiws/1/apiws.php?wsdl";
                    $info = [
                        'authorizedKey' => '9d752cb08ef466fc480fba981cfa44a1',
                        'msgId'         => '0',
                        'msgData'       => $mensaje,
                    ];

                    foreach ($celulares as $celular) {
                        $info['msgRecip'] = $celular;
                        $client = new SoapClient($servicio, $info);
                        $retorno_sms = $client->sendSms($info['authorizedKey'], $info['msgId'], $info['msgData'], $info['msgRecip']);
                    }
                }else{
                    $retorno_sms = true;
                }
            }
        }

        $result = $retorno_servicios && $retorno_datos && $rHistorico && $retorno_sms ? true : false;
        logger("RESULTADO LINEA 1092 = result=$result => retorno_servicios=$retorno_servicios retorno_datos=$retorno_datos rHistorico=$rHistorico retorno_sms=$retorno_sms", false);
    }

    if (!$error) copiarPatologiaPiscinaPadron($datos_socio['cedula']);

    return $result;
}






function buscarCelular($numeros)
{
    preg_match_all('/(09)[1-9]{1}\d{6}/x', $numeros, $respuesta);
    $respuesta = (count($respuesta[0]) !== 0) ? $respuesta[0] : false;
    return $respuesta;
}


function obtenerGrupoUsuario($idVendedor)
{ //ucem
    require "../../../_conexion.php";

    try {
        $qIdGrupo =  "SELECT idgrupo FROM usuarios WHERE id = $idVendedor";
        $select = $mysqli->query($qIdGrupo);
    } catch (\Throwable $errores) {
        registrar_errores($qIdGrupo, "guardarPadron_inspira.php", $errores);
    }

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
    require "../../../_conexion.php";
    require "../../../_conexion250.php";

    $documentoSocio = mysqli_real_escape_string($mysqli, $_documentoSocio);

    try {
        $qSelect = "SELECT * FROM `patologias_socio` WHERE `documento_socio` = '$documentoSocio'";
        $select = mysqli_query($mysqli, $qSelect);
    } catch (\Throwable $errores) {
        registrar_errores($qSelect, "guardarPadron_inspira.php", $errores);
    }

    while ($row = mysqli_fetch_assoc($select)) {
        $documentoSocio = mysqli_real_escape_string($mysqli250, $row['documento_socio']);
        $idPatologia = mysqli_real_escape_string($mysqli250, $row['id_patologia']);
        $observacion = mysqli_real_escape_string($mysqli250, $row['observacion']);

        try {
            $qInsert = "INSERT INTO patologias_socio SET 
                        documento_socio = '$documentoSocio', 
                        id_patologia = '$idPatologia', 
                        observacion = '$observacion', 
                        fecha = NOW()";
            $r = mysqli_query($mysqli250, $qInsert);
        } catch (\Throwable $errores) {
            registrar_errores($qInsert, "guardarPadron_inspira.php", $errores);
        }
    }
}


function registrar_errores($consulta, $nombre_archivo, $error)
{
    require "../../../_conexion.php";

    $consulta = str_replace("'", '"', $consulta);
    $error = str_replace("'", '"', $error);

    try {
        $sql = "INSERT INTO log_errores (consulta, nombre_archivo, error, fecha_registro) VALUES ('$consulta', '$nombre_archivo', '$error', NOW())";
        $consulta = mysqli_query($mysqli, $sql);
    } catch (\Throwable $errores) {
        Logger_inspira(false, "", "", "", "", $errores, $sql, "", "");
    }

    if ($consulta)
        Logger_inspira(true, "", "", "", "", false, $sql, "", "");


    mysqli_close($mysqli);
    return $consulta;
}
