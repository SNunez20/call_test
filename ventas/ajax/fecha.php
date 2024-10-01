<?php

include_once '_init.php';


$_REQUEST["desde"] = empty($_REQUEST["desde"]) ? "" : $_REQUEST["desde"];
$_REQUEST["hasta"] = empty($_REQUEST["hasta"]) ? "" : $_REQUEST["hasta"];

$anio_mes_dia_anterior = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
$dia_anterior = date('d',strtotime($anio_mes_dia_anterior));    
$mes_anterior= date('m',strtotime($anio_mes_dia_anterior));
$anio_actual_anterior=date('Y',strtotime($anio_mes_dia_anterior));
$fecha_anterior = date('d/m/Y', strtotime("{$dia_anterior}-{$mes_anterior}-{$anio_actual_anterior}"));

$fecha_anterior_2 = date('Y-m-d', strtotime("{$anio_actual_anterior}-{$mes_anterior}-{$dia_anterior}"));


$fecha_actual = date("d/m/Y");

$buscar_fecha = "Filtrado entre las fechas :{$fecha_anterior} y {$fecha_actual}";

if ($_REQUEST["desde"] != '' && $_REQUEST["hasta"] != '') {
    $fecha_anterior = date('d/m/Y', strtotime($_REQUEST["desde"]));
    $fecha_actual = date('d/m/Y', strtotime($_REQUEST['hasta']));
    $buscar_fecha = "Filtrado entre las fechas : {$fecha_anterior}  y {$fecha_actual}";
}


die(json_encode(
    [
        'texto' => $buscar_fecha,
        'fecha_desde' => $_REQUEST["desde"] != ''  ? $_REQUEST['desde'] :  $fecha_anterior_2,
        'fecha_hasta' => $_REQUEST["hasta"] != ''  ? $_REQUEST["hasta"] : date('Y-m-d')
    ]
));
