<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";

$response = array(
    "result"  => false,
    "session" => false,
);

$fecha = date('Y-m-d H:i');

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $cambioProducto      = false;
    $fecha               = date('Y-m-d H:i');
    $data                = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id                  = $data['id'];
    $nombre              = strtoupper($data['nombre']);
    $cedula              = $data['cedula'];
    $tel                 = $data['tel'];
    $direccion           = strtoupper($data['direccion']);
    $radio               = $data['radio'];
    $fechaNacimiento     = date('Y-m-d', strtotime($data['fechaNacimiento']));
    $tarjeta             = $data['tarjeta'];
    $tipoTarjeta         = $data['tipoTarjeta'];
    $numeroTarjeta       = $data['numeroTarjeta'];
    $nombreTitular       = $data['nombreTitular'];
    $cedulaTitular       = $data['cedulaTitular'];
    $telefonoTitular     = $data['telefonoTitular'];
    $anio_e              = $data['anio_e'];
    $mes_e               = $data['mes_e'];
    $count               = $data['count'];
    $email               = $data['email'];
    $cvv                 = $data['cvv'];
    $emailTitular        = $data['emailTitular'];
    $observaciones       = strtoupper($data['observaciones']);
    $totalImporte        = $data['totalImporte'];
    $sucursal            = $data['sucursal'];
    $bancoEmisor         = $data['bancoEmisor'] ? $data['bancoEmisor'] : 0;
    $ruta                = $data['ruta'] ?? '';
    $tarjetaVida         = $data['tarjetaVida'];
    $tipoAdmin           = $data['typeAdmin'];
    $is_mercadopago      = isset($data['is_mercadopago']) ? $data['is_mercadopago'] : '';
    $idUser              = $data['idUser'];
    $observacionCobro    = isset($data['observacionCobro']) ? $data['observacionCobro'] : '';
    $numero_vendedor     = $data['numero_vendedor'];
    $nombre_vendedor     = $data['nombre_vendedor'];

    $qERut       = "select empresa_rut from aux2 where num_sucursal = $sucursal";
    $rERut       = mysqli_query($mysqli250, $qERut);
    $empresa_rut = "";
    if (mysqli_num_rows($rERut)) {
        $empresa_rut = mysqli_fetch_assoc($rERut)['empresa_rut'];
    }

    $qEmarca         = "select empresa_brand from aux2 where num_sucursal = $sucursal";
    $rEmarca         = mysqli_query($mysqli250, $qEmarca);
    $empresa_marca   = "";
    if (mysqli_num_rows($rEmarca)) {
        $empresa_marca = mysqli_fetch_assoc($rEmarca)['empresa_brand'];
    }

    $qCedulaAnterior         = "select cedula from padron_datos_socio where id = $id";
    $rCedulaAnterior         = mysqli_query($mysqli, $qCedulaAnterior);
    $ced_old   = "";
    if (mysqli_num_rows($rCedulaAnterior)) {
        $ced_old = mysqli_fetch_assoc($rCedulaAnterior)['cedula'];
    }

    #recupero los beneficiarios
    $qBeneficiarios   = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$ced_old' GROUP BY cedula";
    $rBeneficiarios   = mysqli_query($mysqli, $qBeneficiarios );
    $cedBeneficiarios  = [];
    $hayBeneficiarios = false;
    if (mysqli_num_rows($rBeneficiarios)>0) {
        $hayBeneficiarios = true;
        while($row = mysqli_fetch_assoc($rBeneficiarios)){
            array_push($cedBeneficiarios,$row['cedula']);
        }   
    }

    if ($tipoAdmin == 'comercial') {
        $cambioProducto = true;
        $query          = "UPDATE padron_datos_socio
    SET
        nombre= '$nombre',
        tel = '$tel',
        cedula = '$cedula',
        direccion ='$direccion',
        sucursal = $sucursal,
        ruta = '$ruta',
        radio = '$radio',
        fecha_nacimiento = '$fechaNacimiento',
        tarjeta = '$tarjeta',
        tipo_tarjeta = '$tipoTarjeta',
        numero_tarjeta = '$numeroTarjeta',
        nombre_titular = '$nombreTitular',
        cedula_titular = '$cedulaTitular',
        telefono_titular = '$telefonoTitular',
        email_titular = '$emailTitular',
        tarjeta_vida = '$tarjetaVida',
        anio_e = $anio_e,
        mes_e = $mes_e,
        `count` = $count,
        email = '$email',
        email_titular = '$emailTitular',
        observaciones = '$observaciones',
        total_importe = '$totalImporte',
        banco_emisor  = '$bancoEmisor'
    WHERE id = $id";

        $queryProducto = "UPDATE padron_producto_socio
    SET
        numero_vendedor= '$numero_vendedor',
        nombre_vendedor = '$nombre_vendedor'
    WHERE cedula = '$ced_old' AND accion='1'";

    #actualizamos productos
    $queryProducto = "UPDATE padron_producto_socio
    SET
        cedula ='$cedula',
        numero_vendedor= '$numero_vendedor',
        nombre_vendedor = '$nombre_vendedor',
        idrelacion = '$empresa_rut-$cedula'
    WHERE cedula = '$ced_old' AND accion='1'";

    #actualizamos datos beneficiarios
    $qUpdateBen  = "UPDATE padron_datos_socio
    SET
        direccion ='$direccion',
        sucursal = $sucursal,
        ruta = '$ruta',
        radio = '$radio',
        tarjeta = '$tarjeta',
        tipo_tarjeta = '$tipoTarjeta',
        numero_tarjeta = '$numeroTarjeta',
        nombre_titular = '$nombreTitular',
        cedula_titular = '$cedulaTitular',
        telefono_titular = '$telefonoTitular',
        email_titular = '$emailTitular',
        idrelacion    = '$empresa_rut-$cedula',
        empresa_rut   = '$empresa_rut',
        empresa_marca   = '$empresa_marca',
        tarjeta_vida = '$tarjetaVida',
        anio_e = $anio_e,
        mes_e = $mes_e,
        `count` = $count,
        email = '$email',
        email_titular = '$emailTitular',
        observaciones = '$observaciones',
        banco_emisor  = '$bancoEmisor'
        WHERE cedula in (".implode(',',$cedBeneficiarios).")";

    #actualizamos productos de los beneficiarios
    $queryProductoBen = "UPDATE padron_producto_socio
    SET
        numero_vendedor= '$numero_vendedor',
        nombre_vendedor = '$nombre_vendedor',
        idrelacion = '$empresa_rut-$cedula',
        cedula_titular_gf = '$cedula'
    WHERE cedula in (".implode(',',$cedBeneficiarios).") AND accion='1'";

    } else if ($tipoAdmin == 'morosidad') {
        $tarjetaVida = (($is_mercadopago == '1' && $cvv == '0') || $is_mercadopago == '0') ? '1' : '0';
        $estado      = ($tarjetaVida == '0') ? '1' : '2';
        $query       = "UPDATE padron_datos_socio
        SET tarjeta = '$tarjeta',
            tipo_tarjeta = '$tipoTarjeta',
            numero_tarjeta = '$numeroTarjeta',
            nombre_titular = '$nombreTitular',
            cedula_titular = '$cedulaTitular',
            telefono_titular = '$telefonoTitular',
            email_titular = '$emailTitular',
            anio_e = $anio_e,
            mes_e = $mes_e,
            cvv   = $cvv,
            tarjeta_vida = '$tarjetaVida',
            estado ='$estado'
        WHERE id = $id";

        #actualizamos datos de la tarjeta a los beneficiarios
        $qUpdateBen       = "UPDATE padron_datos_socio
        SET tarjeta = '$tarjeta',
            tipo_tarjeta = '$tipoTarjeta',
            numero_tarjeta = '$numeroTarjeta',
            nombre_titular = '$nombreTitular',
            cedula_titular = '$cedulaTitular',
            telefono_titular = '$telefonoTitular',
            email_titular = '$emailTitular',
            anio_e = $anio_e,
            mes_e = $mes_e,
            cvv   = $cvv,
            tarjeta_vida = '$tarjetaVida',
            estado ='$estado'
        WHERE cedula in (".implode(',',$cedBeneficiarios).")";

    } else if ($tipoAdmin == 'bienvenida') {
     
        $query = "UPDATE padron_datos_socio
        SET
        estado = 6
        WHERE id = $id";

    }

    if (mysqli_query($mysqli, $query)) {
        $continuar = true;

        if ($cambioProducto) {
            $continuar = mysqli_query($mysqli, $queryProducto) ? true : false;
        }

        if ($hayBeneficiarios && $tipoAdmin != 'bienvenida') {
            mysqli_query($mysqli, $qUpdateBen);
            
            if ($tipoAdmin == 'comercial') {
                mysqli_query($mysqli,$queryProductoBen);
            }
            
        }

        if ($continuar) {
            $qEstado = "SELECT id, estado from padron_datos_socio WHERE cedula = '$cedula'";
            if ($rEstado = mysqli_query($mysqli, $qEstado)) {
                $row = mysqli_fetch_assoc($rEstado);
                $id_socio = $row['id'];
                $idEstado = $row['estado'];
            }
            $qHistorico = "INSERT INTO historico_venta VALUES(null,$idUser,$id_socio,$idEstado,'$fecha','Se actualizaron datos del socio',11)";
 
            if (mysqli_query($mysqli, $qHistorico)) {
                $response["result_historico"]  = true;
                $response['message'] = 'Historico guardado correctamente';
            } else {
                $response["result_historico"]  = false;
                $response['message_historico'] = 'Error al guardar el historico';
            }
        }else{
            $response["result"]  = $continuar;
            $response['message'] = 'Ocurrio un error al actualizar por favor intente de nuevo, si el error persiste comuniquese con sistemas.';
        }

        $response["result"]  = true;
        $response['message'] = 'Datos guardados correctamente';

    } else {
        $response["result"]  = false;
        $response['message'] = 'Error al actualizar padron';
    }
}

mysqli_close($mysqli);
echo json_encode($response);
