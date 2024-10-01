<?php

include_once '../_init.php'; //CONST PRODUCCION //NOMBRE_APP // CONST BASE DE DATOS
set_time_limit(1000);
ini_set('memory_limit', '-1');

const LOGS_DIR = './logs/';

/* INCLUDES */
include_once './utils.php';
include_once './lib/monolog/monolog.php';
/* INCLUDES */


//set_error_handler('Error_handler'); // Funcion para enviar errores en formato json 
error_reporting(E_ALL); // Para solo mostrar errores en formato json

//HEADERS
header("HTTP/1.1 200 OK");
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
http_response_code(200);
//HEADERS

$mysqli = mysqli_connect(DB_CALL['host'], DB_CALL['user'], DB_CALL['password'], DB_CALL['db']);

$mysqli_motor = mysqli_connect(DB_MOTOR['host'], DB_MOTOR['user'], DB_MOTOR['password'], DB_MOTOR['db']);


if (mysqli_connect_errno()) {
  die(json_encode(
    [
      'error' => true,
      'numero_error' => mysqli_connect_errno(),
      'mensaje' => mysqli_connect_error(),
      'html' =>  mysqli_connect_error(),
      'archivo' => "ajax/_init.php",
      'linea' => 25
    ]
  ));
}
/*CONEXIONES DB */



//Manejo de errores personalizados para enviar json 
function Error_handler($errno, $errstr = null, $errfile = null, $errline = null)
{

  if ($errno) {
    if (PRODUCCION === false) {
      die(json_encode(
        [
          'error' => true,
          'result' => false,
          'message' => $errstr,
          'numero_error' => $errno,
          'mensaje' => $errstr,
          'html' => $errstr,
          'archivo' => $errfile,
          'linea' => $errline
        ]
      ));
    } else {
      die(json_encode(
        [
          'error' => true,
          'result' => false,
          'numero_error' => $errno,
          'mensaje' => $errstr,
          'html' => $errstr,
          'archivo' => $errfile,
          'linea' => $errline
        ]
      ));
    }
  }
  return true;
}

$supervisores_arrays_id = ['admin.id=9', 77, 13, 24, 5, 8, 14, 10, 23, 44, 100, 102, 104, 109, 54,  55, 88, 108, 95];
$vendedores_arrays_id=['usuarios.id<>1995',4739,1763,3067,48,514,1113,1817,34413872,34413947,572,4740,2146,757,3910	];

$estados_array_select = [
  6 => 'En Padrón',
  1 => 'Pendiente de bienvenida',
  4 => 'Rechazado de bienvenida',
  3 => 'Pendiente de calidad',
  678 => 'Rechazado por calidad',
  2 => 'Pendiente de morosidad',
  7 => 'Rechazado por morosidad'

];


$estados_array = [
  1 => 'Pendiente de bienvenida',
  2 => 'Pendiente de morosidad',
  3 => 'Pendiente de calidad',
  4 => 'Rechazado de bienvenida',
  5 => 'Aprobado por bienvenida',
  6 => 'En Padrón',
  7 => 'Rechazado por morosidad',
  8 => 'Aprobado por morosidad',
  678 => 'Rechazado por calidad',
  692 => 'Eliminado'
];
