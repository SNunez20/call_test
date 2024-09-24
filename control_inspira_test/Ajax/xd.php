<?php


require_once __DIR__ . './../../_conexion.php';
require_once __DIR__ . './../../_conexion250.php';

$tarjeta = '4728572016441234';

(require_once __DIR__ . '/__borrarPromoVisa.php')($tarjeta);
