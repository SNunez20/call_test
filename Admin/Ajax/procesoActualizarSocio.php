<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";
session_start();
$response = array(
    "result"  => false,
    "session" => false,
);

if (isset($_SESSION["idadmin"])) {
    $response["session"] = true;
    $data                = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $id                  = $data['id'];
    $nombre              = $data['nombre'];
    $cedula              = $data['cedula'];
    $tel                 = $data['tel'];
    $direccion           = $data['direccion'];
    $radio               = $data['radio'];
    $fechaNacimiento     = $data['fechaNacimiento'];
    $tarjeta             = $data['tarjeta'];
    $tipoTarjeta         = $data['tipoTarjeta'];
    $numeroTarjeta       = $data['numeroTarjeta'];
    $nombreTitular       = $data['nombreTitular'];
    $cedulaTitular       = $data['cedulaTitular'];
    $telefonoTitular     = $data['telefonoTitular'];
    $count               = $data['count'];
    $email               = $data['email'];
    $emailTitular        = $data['emailTitular'];
    $observaciones       = $data['observaciones'];
    $totalImporte        = $data['totalImporte'];
    $sucursal            = $data['sucursal'];
    $bancoEmisor         = $data['bancoEmisor'] ? $data['bancoEmisor'] : 0;
    $ruta                = $data['ruta'] ?? '';
    $tarjetaVida         = $data['tarjetaVida'];
    $continuarProceso    = $data['continuarProceso'];

    $query = "UPDATE padron_datos_socio
    SET nombre = '$nombre',
        cedula = '$cedula',
        tel = '$tel',
        direccion = '$direccion',
        radio = '$radio',
        ruta = '$ruta',
        fecha_nacimiento = '$fechaNacimiento',
        tarjeta = '$tarjeta',
        tipo_tarjeta = '$tipoTarjeta',
        numero_tarjeta = '$numeroTarjeta',
        nombre_titular = '$nombreTitular',
        cedula_titular = '$cedulaTitular',
        telefono_titular = '$telefonoTitular',
        `count` = $count,
        email = '$email',
        email_titular = '$emailTitular',
        observaciones = '$observaciones',
        total_importe = '$totalImporte',
        sucursal = '$sucursal',
        banco_emisor = $bancoEmisor,
        ruta = '$ruta',
        tarjeta_vida = '$tarjetaVida'
    WHERE id = $id";
    $response["result"] = mysqli_query($mysqli, $query);
}

mysqli_close($mysqli);
echo json_encode($response);
