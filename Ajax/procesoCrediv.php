<?php
session_start();
include('../_conexion.php');
require_once '../Admin/lib/PHPMailerAutoload.php';


$cedula = $_REQUEST['cedula'];
$nombre = $_REQUEST['nombre'];
$telefono = $_REQUEST['telefono'];
$id_vendedor = $_SESSION['idusuario'];
$cedula_vendedor = $_SESSION['cedulaUsuario'];

if ($cedula == "" || $nombre == "" || $telefono == "" || $cedula_vendedor == "") devolver_errores("Corrobore que todos los campos estén completados");

/** Inserto el registro **/
$id_registro_carga = insert_registro($cedula, $nombre, $telefono, $id_vendedor);
if ($id_registro_carga === false) devolver_errores("Ocurrieron errores al registrar los datos");

/** Envío mail con los datos a Elite **/
$enviar_mail = EnviarMail($cedula, $nombre, $telefono, $cedula_vendedor);
if ($enviar_mail != true) devolver_errores("Ocurrieron errores al enviar el email");

/** Edito el campo envio_mail = 1 "Enviado" **/
$modificar_registro_carga = registrar_mail_exitoso($id_registro_carga);
if ($modificar_registro_carga != true) devolver_errores("Ocurrieron errores al modificar el registro");


$response['error'] = false;
$response['mensaje'] = 'Se cargaron los datos con éxito!';


echo json_encode($response);




function insert_registro($cedula, $nombre, $telefono, $id_vendedor)
{
    global $mysqli;

    $sql = "INSERT INTO crediv (cedula, nombre, telefono, id_vendedor, envio_mail, fecha_registro) VALUES ('$cedula', '$nombre', '$telefono', '$id_vendedor', 0, NOW())";
    $consulta = mysqli_query($mysqli, $sql);
    $id_insert = mysqli_insert_id($mysqli);

    return $consulta != false ? $id_insert : false;
    mysqli_close($mysqli);
}

function EnviarMail($cedula, $nombre, $telefono, $cedula_vendedor, $ccs = null)
{
    $configuracion = [
        "host" => "smtp.gmail.com",
        "port" => 587,
        "username" => "no-responder@vida.com.uy",
        "password" => "2k8.vida",
        "from" => "no-responder@vida.com.uy",
        "fromname" => "Call",
    ];

    $datos = [
        "email" => "elite@vida.com.uy",
        "nombre" => "Elite"
    ];

    $ccs = [
        "email" => "vidaoficialparaguay@gmail.com",
        "nombre" => "Jge"
    ];

    $bodyHtml = '<html xmlns="https://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Nueva carga Crediv desde call</title>
            </head>

            <body>
                <h1>Nueva carga Crediv desde call</h1>
                <p>Cédula: <b>' . $cedula . '</b></p>
                <p>Nombre: <b>' . $nombre . '</b></p>
                <p>Teléfono: <b>' . $telefono . '</b></p>
                <p>Cédula Vendedor: <b>' . $cedula_vendedor . '</b></p>
            </body>
            </html>';

    $asunto = "Nueva carga Crediv desde call";

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $configuracion["host"];
    $mail->SMTPAuth = true;
    $mail->Username = $configuracion["username"];
    $mail->Password = $configuracion["password"];
    $mail->SMTPSecure = 'tls';
    $mail->Port = $configuracion["port"];
    $mail->Subject = $asunto;
    $mail->isHTML(true);
    $mail->setFrom($configuracion["from"], $configuracion["fromname"]);
    $mail->addReplyTo($configuracion["from"], $configuracion["fromname"]);
    $mail->addAddress($datos["email"], $datos["nombre"]);
    if ($ccs != null) {
        foreach ($ccs as $cc) {
            $mail->addCC($cc["email"], $cc["nombre"]);
        }
    }
    $mail->Body = $bodyHtml;

    if ($mail->send()) {
        return true;
    } else {
        return $mail->ErrorInfo;
    }
}

function registrar_mail_exitoso($id_registro_carga)
{
    global $mysqli;

    $sql = "UPDATE crediv SET envio_mail = 1 WHERE id = '$id_registro_carga' AND envio_mail = 0";
    $consulta = mysqli_query($mysqli, $sql);

    return $consulta;
    mysqli_close($mysqli);
}

function devolver_errores($mensaje)
{
    $response['erore'] = true;
    $response['mensaje'] = $mensaje;
    die(json_encode($response));
}
