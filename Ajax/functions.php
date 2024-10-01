<?php

if (session_status() == PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION)) exit;

require_once __DIR__ . '/../_conexion.php';
require_once __DIR__ . '/../_conexion250.php';


function getVendedorById($id)
{
    if (isset($_SESSION['vendedores'], $_SESSION['vendedores'][$id]))
        return $_SESSION['vendedores'][$id];

    $mysqli = $GLOBALS['mysqli'];

    $qSelect = <<<SQL
    SELECT
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
        `id` = "{$id}";
SQL;
    $select = $mysqli->query($qSelect);

    $_SESSION['vendedores'][$id] = $select->fetch_assoc();

    return $_SESSION['vendedores'][$id];
}

function getAdministradorById($id)
{

    if (isset($_SESSION['administradores'], $_SESSION['administradores'][$id]))
        return $_SESSION['administradores'][$id];

    $mysqli = $GLOBALS['mysqli'];

    $qSelect = <<<SQL
    SELECT
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
        `id` = "{$id}";
SQL;
    $select = $mysqli->query($qSelect);

    $_SESSION['administradores'][$id] = $select->fetch_assoc();

    return $_SESSION['administradores'][$id];
}

function validarNumeroBaseExterna($numero)
{

    $mysqli = $GLOBALS['mysqli'];
    $numId = getNumeroId($numero);


    $qSelect = <<<SQL
    SELECT
        `id_numero`
    FROM
        `contacto_numero`
    WHERE
        `id_numero` = "{$numId}";
SQL;

    $select = $mysqli->query($qSelect);

    return ($select->num_rows > 0);
}

function getNumeroId($numero)
{

    $mysqli = $GLOBALS['mysqli'];

    $qSelect = <<<SQL
    SELECT
        `id` AS `num_id`
    FROM
        `numeros`
    WHERE
        `numero` = "{$numero}";
SQL;
    $select = $mysqli->query($qSelect);

    $numId = $select->fetch_assoc();

    return $numId['num_id'];
}

function getContactoId($numero)
{

    $mysqli = $GLOBALS['mysqli'];
    $idNum = getNumeroId($numero);

    $qSelect = <<<SQL
    SELECT
        `id_contacto` 
    FROM
        `contacto_numero`
    WHERE
        `id_numero` = "{$idNum}";
SQL;
    $select = $mysqli->query($qSelect);

    $contactoId = $select->fetch_assoc();

    return $contactoId['id_contacto'];
}

function validarExisteEnPadron($cedula)
{

    $mysqli = $GLOBALS['mysqli250'];

    $qSelect = <<<SQL
    SELECT
        `id` 
    FROM
        `padron_datos_socio`
    WHERE
        `cedula` = "{$cedula}";
    SQL;

    $select = $mysqli->query($qSelect);

    return ($select->num_rows > 0);
}

function validarExisteEnPiscina($cedula)
{

    $mysqli = $GLOBALS['mysqli'];

    $qSelect = <<<SQL
    SELECT
        `id` 
    FROM
        `padron_datos_socio`
    WHERE
        `cedula` = "{$cedula}";
    SQL;

    $select = $mysqli->query($qSelect);

    return ($select->num_rows > 0);
}


