<?php
require_once "../../_conexion1310.php";
include "../../admin/lib/PHPMailerAutoload.php";

$response = ['error' => true, 'mensaje' => 'Ha ocurrido un error, intente mas tarde'];

mysqli_select_db($mysqli1310, 'vidaapp');

$cedula               = mysqli_real_escape_string($mysqli1310, $_POST['cedula']);
$mail                 = ($_POST['mail'] == '') ? $cedula . '@sinmail.com.uy' : mysqli_real_escape_string($mysqli1310, $_POST['mail']);
$contactos_emergencia = $_POST['contactos_emergencia'];

$nombre     = mysqli_real_escape_string($mysqli1310, $_POST['nombre']);
$contrasena = hash('sha256', $_POST['cedula']);

$q      = "SELECT id FROM usuarios WHERE cedula = '$cedula' OR mail = '$mail'";
$result = mysqli_query($mysqli1310, $q);

if (mysqli_num_rows($result) > 0) {
    $response['mensaje'] = "Cédula o email ya utilizado";
    die(json_encode($response));
}

$q2      = "INSERT INTO usuarios (nombre,cedula,contrasena,mail,fecha_creacion,activo) VALUES('$nombre','$cedula','$contrasena','$mail',NOW(), 1)";
$result2 = mysqli_query($mysqli1310, $q2);

if (!$result2) {
    die(json_encode($response));
}

if (!empty($contactos_emergencia)) {
    $id_usuario = mysqli_insert_id($mysqli1310);
    foreach ($contactos_emergencia as $contacto) {
        $q3      = "INSERT INTO telefonos_contacto_emergencias VALUES(null, $id_usuario, '" . $contacto['nombre'] . "', '" . $contacto['telefono'] . "')";
        $result3 = mysqli_query($mysqli1310, $q3);

        if (!$result3) {
            $response['mensaje'] = 'Ocurrió un error ingresando los contactos de emergencia';
            die(json_encode($response));
        }
    }
}

$_host     = "smtp.gmail.com";
$_port     = 587;
$_username = "no-responder@vida.com.uy";
$_password = "2k8.vida";
$_from     = "no-responder@vida.com.uy";
$_fromname = "VidaApp";
$_subject  = "Manual Vida Alert";

$_body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual</title>
    <h1>Bienvenid@ a VidaApp</h1>
    <p>Estimad@ usuari@, le damos la bienvenida a su servicio Vida Alert</p>
    <p>Para acceder al manual instructivo de como usar su servicio acceda <a href="https://vida.com.uy/alerta.pdf">AQUI</a></p>
    <br>
    <p>Atentamente el equipo de Vida</p>
</head>
<body>

</body>
</html>
HTML;

$mail1 = new PHPMailer();
$mail1->IsSMTP();
$mail1->isHTML(true);
$mail1->Host       = $_host;
$mail1->Port       = $_port;
$mail1->SMTPAuth   = true;
$mail1->SMTPSecure = 'tls';
$mail1->Username   = $_username;
$mail1->Password   = $_password;
$mail1->From       = $_from;
$mail1->FromName   = $_fromname;
$mail1->AddAddress($mail);
$mail1->Subject  = mb_convert_encoding($_subject, "ISO-8859-1", mb_detect_encoding($_subject));
$mail1->Body     = mb_convert_encoding($_body, "ISO-8859-1", mb_detect_encoding($_body));
$mail1->WordWrap = 50;

if (!$mail1->Send()) {
    $response['mensaje'] = 'La persona fue registrada correctamente, pero no se pudo enviar el mail con el manual instructivo';
    die(json_encode($response));
}

$response['error']   = false;
$response['mensaje'] = 'Usuario registrado correctamente';

mysqli_close($mysqli1310);
echo json_encode($response);
