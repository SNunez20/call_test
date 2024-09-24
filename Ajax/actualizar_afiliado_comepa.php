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
        $_POST['id'],
        $_POST['id_socio']
    ) ||
    empty($_POST['id']) ||
    empty($_POST['id_socio'])
)
    die(json_encode([
        'error' => true,
        'params' => true
    ]));

require_once '../_conexion.php';

$id = $mysqli->real_escape_string($_POST['id']);
$id_socio = $mysqli->real_escape_string($_POST['id_socio']);

if (!actualizarRegistro())
    die(json_encode([
        'error' => true,
        'mysqli' => true
    ]));

die(json_encode([
    'success' => true
]));

function actualizarRegistro()
{
    global $mysqli;
    global $idVendedor;
    global $id;
    global $id_socio;

    $qUpdate = <<<SQL
    UPDATE
        `afiliados_comepa`
    SET
        `id_socio` = '{$id_socio}'
    WHERE
        `id` = '{$id}' AND
        `id_vendedor` = '{$idVendedor}';
SQL;
    return $mysqli->query($qUpdate);
}
