<?php
session_start();
include '../_conexion.php';
include '../_conexionBilletera.php';

$response = ["error" => true, "mensaje" => "Ocurrió un error, intente mas tarde"];

if (!isset($_SESSION['idusuario']))
    die(json_encode($response));

$id_usuario = $_SESSION['idusuario'];
const VIDAPESOS_INICIALES = 0;
$mensaje = "Bienvenido a la billetera de Vida, para empezar a utilizarla debe acceder a https://vida.com.uy/billeteravida/ su usuario y password es su cedula";

$q = "SELECT usuario FROM usuarios WHERE id = $id_usuario";
$result = mysqli_query($mysqli, $q);

if (mysqli_num_rows($result) === 0)
    die(json_encode($response));

$cedula_vendedor = mysqli_fetch_assoc($result)['usuario'];
$cedula =  mysqli_real_escape_string($mysqliBilletera, $_POST['cedula']);
$nombre =  formatoNombre(mysqli_real_escape_string($mysqliBilletera, $_POST['nombre']));
$celular = mysqli_real_escape_string($mysqliBilletera, $_POST['celular']);

$verificar = mysqli_query($mysqliBilletera, "SELECT id FROM usuarios WHERE celular ='{$celular}' OR documento = '{$cedula}'");

if (mysqli_num_rows($verificar) != 0) {
    $response['mensaje'] = "El celular o cedula ya se encuentra registrado en la billetera.";
    die(json_encode($response));
}

$activo = 1;
$password = hash('sha512', $cedula);
$email =  "";
$hash = hash('sha256', $password .  $cedula . date('Y-m-d H:i:s'));
$hash_confirmacion = $hash;
$vidapesos_iniciales = encrypt(VIDAPESOS_INICIALES);
$fecha = date("Y-m-d H:i:s");
$vencimiento_vidapesos = "0000-00-00 00:00:00";

$query = "INSERT INTO usuarios (documento,nombre,celular,email,password,hash,vidapesos,vencimiento_vidapesos,fecha_registrado,activo,cedula_agente_call)
VALUES('{$cedula}','{$nombre}','{$celular}','{$email}','{$password}','{$hash}','{$vidapesos_iniciales}','{$vencimiento_vidapesos}','{$fecha}',$activo, '$cedula_vendedor')";
$insert = mysqli_query($mysqliBilletera, $query);

if (!$insert)
    die(json_encode($response));

$sms = sendSMS($celular, $mensaje, false);

$response['error'] = false;
$response['mensaje'] = "Usuario registrado correctamente, a la brevedad le va a llegar un sms de confirmación al celular registrado.";

die(json_encode($response));

function formatoNombre($nombre)
{
    return  ucwords(mb_strtolower($nombre, 'UTF-8'));
}

function encrypt($val)
{
    list($pass, $iv, $method) = cryptParam();
    return openssl_encrypt($val, $method, $pass, false, $iv);
}

function cryptParam()
{
    /**
     * Password -> bin2hex(Esto es un passphrase para que se pasa a hexadecimal para encriptar)
     * Iv -> bin2hex(Esto debe random)  - Por el tipo de cifrado debe ser de 16 digitos, esto deberia ser dinamico por cada cifrado y guardarse en base para cada dato
     */

    /**
     * EJEMPLO encrypt decrypt
     * ------------------------
     * $vidapesos = 1500;
     * $vidapesos_cifrado = encrypt($vidapesos);
     * echo "Vidapesos : " . decrypt($vidapesos_cifrado);
     * echo "<br>";
     * echo "Vidapesos cifrado: " . $vidapesos_cifrado;
     */
    $algoritm = "AES-256-CBC";
    $password_hex = "4573746f20657320756e2070617373706872617365207061726120717565207365207061736120612068657861646563696d616c207061726120656e63726970746172";
    $iv_hex = "4573746f20646562652072616e646f6d";
    return [
        hex2bin($password_hex),
        hex2bin($iv_hex),
        $algoritm
    ];
}

function sendSMS($celular, $mensaje, $historico = true)
{
    global $conexion;

    $servicio = "http://192.168.104.6/apiws/1/apiws.php?wsdl";
    $parametros = array();

    $a = $parametros['authorizedKey'] = "9d752cb08ef466fc480fba981cfa44a1";
    $b = $parametros['msgId'] = "0";
    $c = $parametros['msgData'] = (string) $mensaje;
    $d = $parametros['msgRecip'] = (string)$celular;

    $client = new SoapClient($servicio, $parametros);
    $send  = $client->sendSms($a, $b, $c, $d);
    return $send;
}
