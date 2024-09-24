<?php
require_once "../../../_conexion.php";

$response['result'] = false;
$response['session'] = false;


if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $tipoAdmin           = $_POST["typeAdmin"];
    $cambioProducto      = false;
    $fecha               = date('Y-m-d H:i');
    $data                = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id                  = $data['id'];
    $cedula              = $data['cedula'];
    $sucursal            = $data['sucursal'];
    $tarjetaVida         = $data['tarjetaVida'];
    $is_mercadopago      = isset($data['is_mercadopago']) ? $data['is_mercadopago'] : '';
    $idUser              = $data['idUser'];
    $radio               = $data['radio'];
    $empresa_marca       = in_array($radio, ["1372", "13728"]) ? '18' : '99';
    $empresa_rut         = "08";


    $rCedulaAnterior = obtener_padron_datos_socio(2, $id);
    $ced_old = "";
    if (mysqli_num_rows($rCedulaAnterior))
        $ced_old = mysqli_fetch_assoc($rCedulaAnterior)['cedula'];


    #recupero los beneficiarios
    $rBeneficiarios   = obtener_beneficiarios_grupo_familiar($ced_old);
    $cedBeneficiarios = [];
    $hayBeneficiarios = false;
    if (mysqli_num_rows($rBeneficiarios) > 0) {
        $hayBeneficiarios = true;
        while ($row = mysqli_fetch_assoc($rBeneficiarios)) {
            array_push($cedBeneficiarios, $row['cedula']);
        }
    }


    if ($tipoAdmin == 'comercial') {
        $cambioProducto = true;
        $modificar_padron_datos_socio = modificar_padron_datos_socio(1);
    }


    if ($tipoAdmin == 'morosidad') {
        $tarjetaVida = (($is_mercadopago == '1' && $cvv == '0') || $is_mercadopago == '0') ? '1' : '0';
        $estado = ($tarjetaVida == '0') ? '1' : '2';
        $modificar_padron_datos_socio = modificar_padron_datos_socio(2, $estado);
    }


    if ($tipoAdmin == 'bienvenida') $modificar_padron_datos_socio = modificar_padron_datos_socio(3);


    if ($modificar_padron_datos_socio) {
        $continuar = true;

        #actualizamos productos
        if ($cambioProducto) $continuar = modificar_productos($empresa_rut, $ced_old);

        if ($hayBeneficiarios && $tipoAdmin != 'bienvenida') {
            #actualizamos datos de la tarjeta a los beneficiarios
            if ($tipoAdmin == 'comercial') modificar_datos_tarjeta_beneficiarios(1, $cedBeneficiarios, $empresa_rut, $empresa_marca);
            if ($tipoAdmin == 'morosidad') modificar_datos_tarjeta_beneficiarios(2, $cedBeneficiarios, $empresa_rut, $empresa_marca);

            #actualizamos productos de los beneficiarios
            if ($tipoAdmin == 'comercial') modificar_productos_beneficiarios($cedBeneficiarios, $empresa_rut);
        }

        if ($continuar) {
            $rEstado = obtener_padron_datos_socio(1, $cedula);
            if ($rEstado) {
                $row = mysqli_fetch_assoc($rEstado);
                $id_socio = $row['id'];
                $idEstado = $row['estado'];
            }

            $qHistorico = registrar_historico_venta($idUser, $id_socio, $idEstado);
            if ($qHistorico) {
                $response["result_historico"]  = true;
                $response['message']           = 'Histórico guardado correctamente';
            } else {
                $response["result_historico"]  = false;
                $response['message_historico'] = 'Error al guardar el histórico';
            }
        } else {
            $response["result"]  = $continuar;
            $response['message'] = 'Ocurrió un error al actualizar por favor intente de nuevo, si el error persiste comuníquese con sistemas.';
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




function obtener_beneficiarios_grupo_familiar($ced_old)
{
    require "../../../_conexion.php";

    $sql = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$ced_old' GROUP BY cedula";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function modificar_padron_datos_socio($opcion, $estado = false)
{
    require '../../../_conexion.php';


    $data            = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id              = $data['id'];
    $nombre          = strtoupper($data['nombre']);
    $cedula          = $data['cedula'];
    $tel             = $data['tel'];
    $direccion       = strtoupper($data['direccion']);
    $radio           = $data['radio'];
    $fechaNacimiento = date('Y-m-d', strtotime($data['fechaNacimiento']));
    $tarjeta         = $data['tarjeta'];
    $tipoTarjeta     = $data['tipoTarjeta'];
    $numeroTarjeta   = $data['numeroTarjeta'];
    $nombreTitular   = $data['nombreTitular'];
    $cedulaTitular   = $data['cedulaTitular'];
    $telefonoTitular = $data['telefonoTitular'];
    $anio_e          = $data['anio_e'];
    $mes_e           = $data['mes_e'];
    $count           = $data['count'];
    $email           = $data['email'];
    $cvv             = $data['cvv'];
    $emailTitular    = $data['emailTitular'];
    $observaciones   = strtoupper($data['observaciones']);
    $totalImporte    = $data['totalImporte'];
    $sucursal        = $data['sucursal'];
    $bancoEmisor     = $data['bancoEmisor'] ? $data['bancoEmisor'] : 0;
    $ruta            = $data['ruta'] ?? '';
    $tarjetaVida     = $data['tarjetaVida'];


    $sql1 = "UPDATE padron_datos_socio SET
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
             WHERE 
              id = $id";


    $sql2 = "UPDATE padron_datos_socio SET 
              tarjeta = '$tarjeta',
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
             WHERE 
              id = $id";


    $sql3 = "UPDATE padron_datos_socio SET estado = 6 WHERE id = $id";


    $sql = ($opcion == 1) ? $sql1 : (($opcion == 2) ? $sql2 : (($opcion == 3) ? $sql3 : ""));
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function modificar_productos($empresa_rut, $ced_old)
{
    require '../../../_conexion.php';

    $data            = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $cedula          = $data['cedula'];
    $numero_vendedor = $data['numero_vendedor'];
    $nombre_vendedor = $data['nombre_vendedor'];


    $sql = "UPDATE padron_producto_socio SET
             cedula ='$cedula',
             numero_vendedor= '$numero_vendedor',
             nombre_vendedor = '$nombre_vendedor',
             idrelacion = '$empresa_rut-$cedula'
            WHERE 
             cedula = '$ced_old' AND 
             accion='1'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function modificar_datos_tarjeta_beneficiarios($opcion, $array_cedula_beneficiarios, $empresa_rut, $empresa_marca)
{
    require '../../../_conexion.php';


    $data            = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $cedula          = $data['cedula'];
    $direccion       = strtoupper($data['direccion']);
    $radio           = $data['radio'];
    $tarjeta         = $data['tarjeta'];
    $tipoTarjeta     = $data['tipoTarjeta'];
    $numeroTarjeta   = $data['numeroTarjeta'];
    $nombreTitular   = $data['nombreTitular'];
    $cedulaTitular   = $data['cedulaTitular'];
    $telefonoTitular = $data['telefonoTitular'];
    $anio_e          = $data['anio_e'];
    $mes_e           = $data['mes_e'];
    $count           = $data['count'];
    $email           = $data['email'];
    $cvv             = $data['cvv'];
    $emailTitular    = $data['emailTitular'];
    $observaciones   = strtoupper($data['observaciones']);
    $sucursal        = $data['sucursal'];
    $bancoEmisor     = $data['bancoEmisor'] ? $data['bancoEmisor'] : 0;
    $ruta            = $data['ruta'] ?? '';
    $tarjetaVida     = $data['tarjetaVida'];
    $is_mercadopago  = isset($data['is_mercadopago']) ? $data['is_mercadopago'] : '';
    $tarjetaVida     = (($is_mercadopago == '1' && $cvv == '0') || $is_mercadopago == '0') ? '1' : '0';
    $estado          = ($tarjetaVida == '0') ? '1' : '2';

    $cedula_beneficiarios = implode(',', $array_cedula_beneficiarios);


    $sql1 = "UPDATE padron_datos_socio SET
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
             WHERE 
              cedula IN ($cedula_beneficiarios)";


    $sql2 = "UPDATE padron_datos_socio SET 
              tarjeta = '$tarjeta',
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
             WHERE 
              cedula IN ($cedula_beneficiarios)";


    $sql = ($opcion == 1) ? $sql1 : (($opcion == 2) ? $sql2 : "");
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function modificar_productos_beneficiarios($array_cedula_beneficiarios, $empresa_rut)
{
    require '../../../_conexion.php';

    $data            = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $cedula          = $data['cedula'];
    $numero_vendedor = $data['numero_vendedor'];
    $nombre_vendedor = $data['nombre_vendedor'];

    $cedula_beneficiarios = implode(',', $array_cedula_beneficiarios);

    $sql = "UPDATE padron_producto_socio SET
             numero_vendedor= '$numero_vendedor',
             nombre_vendedor = '$nombre_vendedor',
             idrelacion = '$empresa_rut-$cedula',
             cedula_titular_gf = '$cedula'
            WHERE 
             cedula IN ($cedula_beneficiarios) AND 
             accion='1'";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function obtener_padron_datos_socio($opcion, $param)
{
    require "../../../_conexion.php";

    $where = $opcion == 1 ? "cedula = '$param'" : "id = '$param'";

    $sql = "SELECT * from padron_datos_socio WHERE $where";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}


function registrar_historico_venta($id_usuario, $id_socio, $id_estado)
{
    require "../../../_conexion.php";

    global $fecha;

    $sql = "INSERT INTO historico_venta VALUES(null, $id_usuario, $id_socio, $id_estado, '$fecha', 'Se actualizaron datos del socio', 11)";
    $consulta = mysqli_query($mysqli, $sql);

    mysqli_close($mysqli);
    return $consulta;
}
