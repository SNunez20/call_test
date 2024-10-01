<?php

session_start();

$idVendedor = $_SESSION['idusuario'];
$grupoUsuario = $_SESSION['grupoUsuario'];

if ($grupoUsuario !== '10022')
    die(json_encode([
        'error' => true,
        'group' => true
    ]));


if (
    !isset(
        $_POST['nombre'],
        $_POST['cedula'],
        $_POST['fechaNacimiento'],
        $_POST['telefono'],
        $_POST['celular'],
        $_POST['direccion'],
        $_POST['observacion'],
        $_POST['metodo_pago'],
        $_POST['caja'],
        $_POST['tarjeta_emisor'],
        $_POST['tarjeta_nombre'],
        $_POST['tarjeta_documento'],
        $_POST['tarjeta_numero'],
        $_POST['tarjeta_vencimiento_mes'],
        $_POST['tarjeta_vencimiento_ano']
    ) ||
    empty($_POST['nombre']) ||
    empty($_POST['cedula']) ||
    empty($_POST['direccion']) ||
    empty($_POST['fechaNacimiento']) ||
    (empty($_POST['telefono']) && empty($_POST['celular'])) ||
    empty($_POST['metodo_pago'])
)
    die(json_encode([
        'error' => true,
        'params' => true
    ]));


require_once '../_conexion.php';

$nombre = $mysqli->real_escape_string($_POST['nombre']);
$cedula = $mysqli->real_escape_string($_POST['cedula']);
$fechaNacimiento = $mysqli->real_escape_string($_POST['fechaNacimiento']);
$telefono = $mysqli->real_escape_string($_POST['telefono']);
$celular = $mysqli->real_escape_string($_POST['celular']);
$direccion = $mysqli->real_escape_string($_POST['direccion']);
$observacion = $mysqli->real_escape_string($_POST['observacion']);
$metodo_pago = $mysqli->real_escape_string($_POST['metodo_pago']);
$caja = $mysqli->real_escape_string($_POST['caja']);
$tarjeta_emisor = $mysqli->real_escape_string($_POST['tarjeta_emisor']);
$tarjeta_nombre = $mysqli->real_escape_string($_POST['tarjeta_nombre']);
$tarjeta_documento = $mysqli->real_escape_string($_POST['tarjeta_documento']);
$tarjeta_numero = $mysqli->real_escape_string($_POST['tarjeta_numero']);
$tarjeta_vencimiento = $mysqli->real_escape_string(
    $_POST['tarjeta_vencimiento_mes'] . '/' . $_POST['tarjeta_vencimiento_ano']
);
$idSocio = isset($_POST['id_socio']) && !empty($_POST['id_socio'])
    ? $mysqli->real_escape_string($_POST['id_socio'])
    : '';

if (corroborarExistencia())
    die(json_encode([
        'error' => true,
        'exist' => true
    ]));

$insertarDatos = insertarDatos();

if (!$insertarDatos['result'])
    die(json_encode([
        'error' => true,
        'mysqli' => true,
        'query' => $insertarDatos['query']
    ]));


if ($idVendedor != "3113")
    mandarMail();

die(json_encode([
    'success' => true,
    'last_id' => $mysqli->insert_id
]));

function insertarDatos()
{
    global $mysqli;
    global $idSocio;
    global $idVendedor;
    global $nombre;
    global $cedula;
    global $fechaNacimiento;
    global $telefono;
    global $celular;
    global $direccion;
    global $observacion;
    global $metodo_pago;
    global $caja;
    global $tarjeta_emisor;
    global $tarjeta_nombre;
    global $tarjeta_documento;
    global $tarjeta_numero;
    global $tarjeta_vencimiento;


    $qInsert = <<<SQL
    INSERT INTO
        `afiliados_comepa`
        (
            `id_socio`,
            `id_vendedor`,
            `nombre`,
            `cedula`,
            `telefono`,
            `celular`,
            `fecha_nacimiento`,
            `direccion`,
            `observacion`,
            `metodo_pago`,
            `caja`,
            `tarjeta_emisor`,
            `tarjeta_nombre`,
            `tarjeta_documento`,
            `tarjeta_numero`,
            `tarjeta_vencimiento`,
            `fecha_afiliacion`
        )
    VALUES
        (
            NULLIF('{$idSocio}', ''),
            '{$idVendedor}',
            '{$nombre}',
            '{$cedula}',
            NULLIF('{$telefono}', ''),
            NULLIF('{$celular}', ''),
            '{$fechaNacimiento}',
            '{$direccion}',
            NULLIF('{$observacion}', ''),
            '{$metodo_pago}',
            NULLIF('{$caja}', 'Seleccione una opción'),
            NULLIF('{$tarjeta_emisor}', 'Seleccione una opción'),
            NULLIF('{$tarjeta_nombre}', ''),
            NULLIF('{$tarjeta_documento}', ''),
            NULLIF('{$tarjeta_numero}', ''),
            NULLIF('{$tarjeta_vencimiento}', '/'),
            NOW()
        );
SQL;

    return ['result' => $mysqli->query($qInsert), 'query' => $qInsert];
}

function corroborarExistencia()
{
    global $mysqli;
    global $cedula;

    $qSelect = <<<SQL
    SELECT
        *
    FROM
        `afiliados_comepa`
    WHERE
        `cedula` = '{$cedula}';
SQL;
    $query = $mysqli->query($qSelect);

    return $query->num_rows > 0;
}

function mandarMail()
{
    global $nombre;
    global $cedula;
    global $fechaNacimiento;
    global $telefono;
    global $celular;
    global $direccion;
    global $observacion;
    global $metodo_pago;
    global $caja;
    global $tarjeta_emisor;
    global $tarjeta_nombre;
    global $tarjeta_documento;
    global $tarjeta_numero;
    global $tarjeta_vencimiento;
    define('SIN_INFORMACION', 'Sin información');

    require_once './../lib/PHPMailer/src/PHPMailer.php';

    $_fechaNacimiento = (new DateTime($fechaNacimiento))->format('d/m/Y');
    $_telefono = empty($telefono) ? SIN_INFORMACION : $telefono;
    $_celular = empty($celular) ? SIN_INFORMACION : $celular;
    $_observacion = empty($observacion) ? SIN_INFORMACION : $observacion;

    if ($metodo_pago === 'Cajas COMEPA')
        $seccionPago =  "Caja: {$caja} <br />";
    elseif ($metodo_pago === 'Tarjeta de débito/crédito')
        $seccionPago =  <<<HTML
    Emisor de la tarjeta: {$tarjeta_emisor} <br />
    Número de tarjeta: {$tarjeta_numero} <br />
    Fecha de vencimiento: {$tarjeta_vencimiento} <br />
    Nombre del propietario: {$tarjeta_nombre} <br />
    Documento del propietario: {$tarjeta_documento} <br />
HTML;
    else
        $seccionPago = '';

    $body = <<<HTML
    Nombre: {$nombre} <br />
    Documento: {$cedula} <br />
    Fecha de nacimiento: {$_fechaNacimiento} <br />
    Teléfono: {$_telefono} <br />
    Celular: {$_celular} <br />
    Dirección: {$direccion} <br />
    Observacion: {$_observacion} <br />
    Método de pago: {$metodo_pago} <br />
    {$seccionPago}
HTML;
    $subject = "Datos de afiliado {$cedula} - {$nombre}. Vida, servicio de compañía";

    $mail1 = new PHPMailer();
    $mail1->IsSMTP();
    $mail1->isHTML(true);
    $mail1->Host = "smtp.gmail.com";
    $mail1->Port = 587;
    $mail1->SMTPAuth = true;
    $mail1->SMTPSecure = 'tls';
    $mail1->Username = "no-responder@vida.com.uy";
    $mail1->Password = "2k8.vida";
    $mail1->From     = "no-responder@vida.com.uy";
    $mail1->FromName = "Vida";
    $mail1->AddAddress('aseguradora.oficina@comepa.com.uy');
    $mail1->addBCC('josemage@gmail.com');
    $mail1->addBCC('gerenciacomercial@vida.com.uy');
    $mail1->Subject  = mb_convert_encoding($subject, "ISO-8859-1", mb_detect_encoding($subject));
    $mail1->Body     = mb_convert_encoding($body, "ISO-8859-1", mb_detect_encoding($body));
    $mail1->WordWrap = 50;
    if (!$mail1->Send()) {
        die(json_encode($mail1->ErrorInfo));
    }
}



die(json_encode($_SESSION));
