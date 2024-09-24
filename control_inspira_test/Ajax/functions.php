<?php

if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION)) exit;

require_once  '../../../_conexion.php';
require_once  '../../../_conexion250.php';


function getVendedorById($id)
{
    if (isset($_SESSION['vendedores'], $_SESSION['vendedores'][$id]))
        return $_SESSION['vendedores'][$id];

    $mysqli = $GLOBALS['mysqli'];

    $qSelect = "SELECT
                 `id` AS `u_id`,
                 `nombre` AS `u_nombre`,
                 `usuario` AS `u_usuario`,
                 `idgrupo` AS `u_idgrupo`,
                 `activo` AS `u_activo`,
                 `email` AS `u_email`,
                 `celular` AS `u_celular`
                FROM
                 `usuarios`
                WHERE
                 id = '{$id}'";
    $select = mysqli_query($mysqli, $qSelect);
    $_SESSION['vendedores'][$id] = mysqli_fetch_assoc($select);

    return $_SESSION['vendedores'][$id];
}

function getAdministradorById($id)
{
    if (isset($_SESSION['administradores'], $_SESSION['administradores'][$id]))
        return $_SESSION['administradores'][$id];

    $mysqli = $GLOBALS['mysqli'];

    $qSelect = "SELECT
                 `id` AS `a_id`,
                 `nombre` AS `a_nombre`,
                 `usuario` AS `a_usuario`,
                 `grupo_usuarios` AS `a_grupo_usuarios`,
                 `tipo_admin` AS `a_tipo_admin`,
                 `ultimo_acceso` AS `a_ultimo_acceso`,
                 `activo` AS `a_activo`
                FROM
                 `admin`
                WHERE
                 id = '{$id}'";
    $select = mysqli_query($mysqli, $qSelect);
    $_SESSION['administradores'][$id] = mysqli_fetch_assoc($select);

    return $_SESSION['administradores'][$id];
}

function validarNumeroBaseExterna($numero)
{
    $mysqli = $GLOBALS['mysqli'];
    $numId = getNumeroId($numero);

    $qSelect = "SELECT id_numero FROM contacto_numero WHERE id_numero = '{$numId}'";
    $select = mysqli_query($mysqli, $qSelect);

    return mysqli_num_rows($select) > 0;
}

function getNumeroId($numero)
{
    $mysqli = $GLOBALS['mysqli'];

    $qSelect = "SELECT id AS 'num_id' FROM numeros WHERE numero = '{$numero}'";
    $select = mysqli_query($mysqli, $qSelect);
    $numId = mysqli_fetch_assoc($select);

    return $numId['num_id'];
}

function getContactoId($numero)
{
    $mysqli = $GLOBALS['mysqli'];
    $idNum = getNumeroId($numero);

    $qSelect = "SELECT id_contacto FROM contacto_numero WHERE id_numero = '{$idNum}'";
    $select = mysqli_query($mysqli, $qSelect);
    $contactoId = mysqli_fetch_assoc($select);

    return $contactoId['id_contacto'];
}

function validarExisteEnPadron($cedula)
{
    $mysqli = $GLOBALS['mysqli250'];

    $qSelect = "SELECT id FROM padron_datos_socio WHERE cedula = '{$cedula}'";
    $select = mysqli_query($mysqli, $qSelect);

    return mysqli_num_rows($select) > 0;
}

function validarExisteEnPiscina($cedula)
{
    $mysqli = $GLOBALS['mysqli'];

    $qSelect = "SELECT id FROM padron_datos_socio WHERE cedula = '{$cedula}'";
    $select = mysqli_query($mysqli, $qSelect);

    return mysqli_num_rows($select) > 0;
}
