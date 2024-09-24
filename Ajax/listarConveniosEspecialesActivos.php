<?php

session_start();

if (!isset($_SESSION['idusuario']))
    return false;


require_once '../_conexion.php';

$qSelect = <<<SQL
SELECT
    *
FROM
    `convenios_especiales`
WHERE
    `activo` = 1;
SQL;
$query = $mysqli->query($qSelect);

die(json_encode($query->fetch_all(MYSQLI_ASSOC)));
