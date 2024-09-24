<?php
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once '../_conexion250.php';
require_once '../_conexion.php';
require_once '../_conexion1310.php';

$response = array(
    'result' => false,
    'sesion' => false,
    'message' => 'Ocurrio un error. Intentelo nuevamente más tarde.',
);

// if (isset($_SESSION['idusuario'])) {
$response['sesion'] = true;
$data = array_map(fn ($param) => strip_tags(mysqli_real_escape_string($mysqli, $param)), $_POST);
$cedula = $data['cedula'];
$query = "SELECT * FROM padron_datos_socio WHERE cedula = '$cedula'";
$result = mysqli_query($mysqli250, $query);
$socio = false;
$result2 = mysqli_query($mysqli, $query);
$estado = 6; // busca en padron
$arrDir = [];
$servAcumuladores = ['12', '56', '83']; //combo servicios que se fragmentan en dos o mas
$servOcultos = ['13', '14', '57', '84']; //combo servicios fragmentos
$abmActual = false;
$abm = false;
$result3 = mysqli_query($mysqli250, $query);

if ($result3 && mysqli_num_rows($result3) > 0) {
    $row = mysqli_fetch_assoc($result3);
    $abmActual = $row['abmactual'];
    $abm = $row['abm'];
}

if ($result2 && mysqli_num_rows($result2) > 0) {
    // BUSCA DATOS EN PISCINA
    $data = mysqli_fetch_assoc($result2);
    $estado = $data["estado"];
    $id = $data["id"];
}

//CONTROL PROMO NP (NO SE PUEDE EN INCREMENTOS)
$qNP = "SELECT id FROM padron_producto_socio WHERE cedula = '$cedula' AND cod_promo like '20%'";
$rNP = mysqli_query($mysqli250, $qNP);
$tiene_NP = mysqli_num_rows($rNP);
///////////////////////////////////////////////

if ($estado != 6) {
    $socio = true;
    $response = array('result' => false, 'socio' => false, 'message' => 'Este cliente se encuentra en proceso de afiliación');
} else if ($abmActual == '1' && ($abm == 'ALTA' || $abm == 'ALTA-PRODUCTO')) {
    $socio = true;
    $response['message'] = 'No es posible incrementar ya que se afilió o incrementó recientemente.';
} else if ($tiene_NP) {
    $socio = true;
    $response['message'] = 'La persona no puede incrementar ya que cuenta con promo NP activa.';
} else {
    // BUSCA EN PADRON
    if (mysqli_num_rows($result) > 0) {

        $socio = true;
        $row = mysqli_fetch_array($result);
        $suc = $row['sucursal'];
        $qlocalidad = "SELECT id FROM filiales WHERE nro_filial = $suc";
        $rloc = mysqli_query($mysqli1310, $qlocalidad);
        $localidad = mysqli_fetch_assoc($rloc)['id'];

        /**
         * COBRADOR 1
         * TARJETA 2
         * CONVENIO 3
         */
        $medio_pago = 1;

        // Recupero el medio de pago
        if ($row["ruta"] == "0000000000") {
            $radio = $row["radio"];
            $result = mysqli_query($mysqli, "SELECT * FROM radios_tarjetas WHERE radio = '$radio'");
            if (mysqli_num_rows($result)) {
                $medio_pago = 2;
            }
            $result = mysqli_query($mysqli, "SELECT * FROM radios_convenios WHERE radio = '$radio'");
            if (mysqli_num_rows($result)) {
                $medio_pago = 3;
            }
        }

        //recupero id de la filial
        $sucursal = $row['sucursal'];
        $qIdSucursal = "select id from motor_de_precios.filiales where nro_filial = $sucursal";
        $rIdSucursal = mysqli_query($mysqli1310, $qIdSucursal);
        $id_sucursal = mysqli_fetch_assoc($rIdSucursal)['id'];

        $qDirSocio = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
        $rDirSocio = mysqli_query($mysqli250, $qDirSocio);

        if ($rDirSocio && mysqli_num_rows($rDirSocio) > 0) {
            $datosDir = mysqli_fetch_assoc($rDirSocio);

            $arrDir = array(
                'calle' => @utf8_encode($datosDir['calle']),
                'puerta' => @utf8_encode($datosDir['puerta']),
                'manzana' => @utf8_encode($datosDir['manzana']),
                'solar' => @utf8_encode($datosDir['solar']),
                'apartamento' => @utf8_encode($datosDir['apartamento']),
                'esquina' => @utf8_encode($datosDir['esquina']),
                'referencia' => @utf8_encode($datosDir['referencia'])
            );
        }

        // Datos socio
        $datos_socio = array(
            'medio_pago' => $medio_pago,
            'id_padron' => $row['id'],
            'nombre' => $row['nombre'],
            'cedula' => $row['cedula'],
            'fechaNacimiento' => $row['fecha_nacimiento'],
            'telefono' => $row['tel'],
            'edad' => $row['edad'],
            'direccion' => $row['direccion'],
            'filial' => $row['sucursal'],
            'fechaFil' => $row['fechafil'],
            'empresa_rut' => $row['empresa_rut'],
            'ruta' => $row['ruta'],
            'radio' => $row['radio'],
            'activo' => $row['activo'],
            'tarjeta' => $row['tarjeta'],
            'banco' => array_key_exists('banco_emisor', $row) ? $row['banco_emisor'] : '',
            'tipo_tarjeta' => $row['tipo_tarjeta'],
            'numero_tarjeta' => $row['numero_tarjeta'],
            'nombre_titular' => $row['nombre_titular'],
            'cedula_titular' => $row['cedula_titular'],
            'tipo_tarjeta' => $row['tipo_tarjeta'],
            'telefono_titular' => $row['telefono_titular'],
            'email_titular' => array_key_exists('email_titular', $row) ? $row['email_titular'] : '',
            'anio_e' => $row['anio_e'],
            'mes_e' => $row['mes_e'],
            'sucursal_cobranzas' => $row['sucursal_cobranzas'],
            'sucursal_cobranza_num' => $row['sucursal_cobranza_num'],
            'empresa_marca' => $row['empresa_marca'],
            'flag' => $row['flag'],
            'count' => $row['count'],
            'observaciones' => $row['observaciones'],
            'grupo' => $row['grupo'],
            'idrelacion' => $row['idrelacion'],
            'total_importe' => $row['total_importe'],
            'nactual' => $row['nactual'],
            'version' => $row['version'],
            'flagchange' => $row['flagchange'],
            'rutcentralizado' => $row['rutcentralizado'],
            'print' => $row['PRINT'],
            'emitido' => $row['EMITIDO'],
            'movimientoabm' => $row['movimientoabm'],
            'abm' => $row['abm'],
            'abmactual' => $row['abmactual'],
            'check' => $row['check'],
            'usuario' => $row['usuario'],
            'usuariod' => $row['usuariod'],
            'radioViejo' => $row['radioViejo'],
            'extra' => $row['extra'],
            'nomodifica' => $row['nomodifica'],
            'localidad' => $localidad,
            'email' => array_key_exists('email', $row) ? $row['email'] : '',
            'id_filial' => $id_sucursal
        );

        $radio = $datos_socio['radio'];
        $qConvenio = "SELECT id FROM radios_convenios WHERE radio like '%$radio%'";
        $qTarjeta = "SELECT id FROM radios_tarjetas WHERE radio like '%$radio%'";

        if ($rConvenio = mysqli_query($mysqli, $qConvenio)) {
            if (mysqli_num_rows($rConvenio) > 0) {
                $idconvenio = mysqli_fetch_assoc($rConvenio)['id'];
                $datos_socio['idconvenio'] = $idconvenio;
            } else if ($rTarjeta = mysqli_query($mysqli, $qTarjeta)) {
                if (mysqli_num_rows($rTarjeta) > 0) {
                    $datos_socio['idconvenio'] = 20;
                } else {
                    $datos_socio['idconvenio'] = 21;
                }
            }
        }

        # Recupero el departamento
        $qDepa = "SELECT id_departamento FROM filiales WHERE id =" . $localidad;

        $rDepa = mysqli_query($mysqli, $qDepa);

        $datos_socio["departamento"] = mysqli_fetch_assoc($rDepa)["id_departamento"];
        $productos_socio = [];
        $productos_socio_detallado = [];

        //PRODUCTOS DETALLADOS
        $queryDetallada = "SELECT abm.id as id_padron,abm.cedula, abm.fecha_registro, abm.fecha_afiliacion, abm.nombre_vendedor, abm.numero_vendedor, abm.servicio as num_servicio,abm.importe, abm.observaciones,abm.hora AS total_horas, abm.count,abm.cedula_titular_gf
 FROM abmmod.padron_producto_socio abm
 WHERE abm.cedula = '$cedula' AND abm.cod_promo <> '24'";
        $productos_detallados = mysqli_query($mysqli250, $queryDetallada);

        if (mysqli_num_rows($productos_detallados) == 0) {
            $queryDetallada = "SELECT abm.cod_promo, s.id,s.nombre_servicio, abm.id as id_padron,abm.cedula, abm.servicio AS num_servicio, sum(importe) as importe, abm.fecha_registro, abm.fecha_afiliacion, abm.nombre_vendedor, abm.numero_vendedor, abm.observaciones, abm.hora AS total_horas, abm.count
 FROM servicios s INNER JOIN padron_producto_socio abm ON s.nro_servicio= abm.servicio
 WHERE abm.cedula = '$cedula' AND abm.cod_promo <> '24'";

            $productos_detallados = mysqli_query($mysqli, $queryDetallada);
        }

        while ($row = mysqli_fetch_array($productos_detallados)) {
            $nro_servicio = $row['num_servicio'];
            $id = $row['id_padron'];
            $queryId = "SELECT id FROM motor_de_precios.servicios WHERE nro_servicio='$nro_servicio'";
            $result = mysqli_query($mysqli1310, $queryId);
            $fetch = mysqli_fetch_assoc($result);
            $id_motor = @$fetch["id"];
            $num_servicio = $row['num_servicio'];
            $qThoras = "SELECT sum(hora) AS horas_totales from padron_producto_socio WHERE cedula = '$cedula' AND servicio = '$num_servicio'";
            $rThoras = mysqli_query($mysqli250, $qThoras);
            $tHoras = mysqli_fetch_assoc($rThoras)["horas_totales"];
            $cedula_titular = $row['id_padron'];
            $productos_socio_detallados[] = array(
                'id_padron' => $id,
                'id_servicio' => $id_motor,
                'total_horas' => $row['total_horas'],
                'num_servicio' => $num_servicio,
                'observaciones' => htmlspecialchars(trim($row['observaciones'])),
                'importe' => $row['importe'],
                'fecha_registro' => $row['fecha_registro'],
                'fecha_afiliacion' => $row['fecha_afiliacion'],
                'count' => $row['count'],
                'nombre_vendedor' => $row['nombre_vendedor'],
                'numero_vendedor' => $row['numero_vendedor'],
                'horas_totales' => $tHoras,
                'cedula_titular' => $cedula_titular,
            );
        }

        //PRODUCTOS RESUMIDOS
        $queryProductos = "SELECT abm.cod_promo, ab.id,ab.servicio as nombre_servicio, abm.id as id_padron,abm.cedula, abm.fecha_registro, abm.fecha_afiliacion, abm.servicio as num_servicio,sum(abm.importe) AS importe, abm.observaciones, sum(abm.hora) AS total_horas, COUNT(ab.id) AS cant_registros
 FROM abmmod.servicios_codigos ab INNER JOIN abmmod.padron_producto_socio abm ON ab.nro_servicio= abm.servicio
 WHERE abm.cedula = '$cedula' AND abm.cod_promo <> '24'
 GROUP BY (ab.id)";

        $productos = mysqli_query($mysqli250, $queryProductos);

        if (mysqli_num_rows($productos) == 0) {
            $queryProductos = "SELECT abm.cod_promo, s.id,s.nombre_servicio, abm.id as id_padron,abm.cedula, abm.servicio AS num_servicio, sum(importe) as importe, abm.fecha_registro, abm.fecha_afiliacion, abm.observaciones, sum(abm.hora) AS total_horas , count(s.id) as cant_registros
 FROM servicios s INNER JOIN padron_producto_socio abm ON s.nro_servicio = abm.servicio
 WHERE abm.cedula = '$cedula' AND abm.cod_promo <> '24'
 GROUP BY (s.id)";

            $productos = mysqli_query($mysqli, $queryProductos);
        }

        $es_oculto = false;
        $importe_oculto = 0;

        while ($row = mysqli_fetch_array($productos)) {

            if (in_array($row['num_servicio'], $servOcultos)) {
                $es_oculto = true;
                $importe_oculto += $row['importe'];
            } else {
                $nro_servicio = $row['num_servicio'];
                $id = $row['id_padron'];
                $queryId = "SELECT id FROM motor_de_precios.servicios WHERE nro_servicio='$nro_servicio'";
                $result = mysqli_query($mysqli1310, $queryId);
                $fetch = mysqli_fetch_assoc($result);
                $id_motor = $fetch["id"];
                $productos_socio[] = array(
                    'id_padron' => $id,
                    'id_servicio' => $id_motor,
                    'total_horas' => $row['total_horas'],
                    'servicio' => $row['nombre_servicio'],
                    'num_servicio' => $row['num_servicio'],
                    'observaciones' => htmlspecialchars(trim($row['observaciones'])),
                    'importe' => $row['importe'],
                    'fecha_registro' => $row['fecha_registro'],
                    'fecha_afiliacion' => $row['fecha_afiliacion'],
                    'cant_registros' => $row['cant_registros'],
                    'cod_promo' => $row['cod_promo'],
                );
            }
        }

        foreach ($productos_socio as $clave => $valor) {
            if (in_array($productos_socio[$clave]['num_servicio'], $servAcumuladores) && $es_oculto) {
                $sum = (int) $productos_socio[$clave]['importe'] + (int) $importe_oculto;
                $productos_socio[$clave]['importe'] = $sum;
            }
        }

        $response = array('result' => true, 'socio' => true, 'datos_socio' => $datos_socio, 'productos_socio' => $productos_socio, 'productos_socio_detallados' => $productos_socio_detallados, 'code' => 0, 'datosDireccion' => $arrDir);
    }
}

####################################################################################################
// Compruebo si es SOCIO, para de esta manera tener en cuenta que productos ofrecer
// Abrimos la sesión cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/call/Ajax/buscarBaja.php');
// indicamos el tipo de petición
curl_setopt($ch, CURLOPT_POST, true);
// definimos cada uno de los párametros
curl_setopt($ch, CURLOPT_POSTFIELDS, "cedula=$cedula&ptbbddvp=true");
// recibimos la respuesta
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = json_decode(curl_exec($ch));
curl_close($ch);
####################################################################################################

// print_r($res);
$response["code"] = $res->code;
$response["vuelve_antes"] = $res->vuelve_antes;
$response['estado'] = $estado;

if (!$socio) {
    $response = array('result' => true, 'socio' => false);
    $response["code"] = $res->code;
    $response["vuelve_antes"] = $res->vuelve_antes;
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
echo json_encode($response);
