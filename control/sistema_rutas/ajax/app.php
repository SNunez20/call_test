<?php

//if (session_status() !== PHP_SESSION_ACTIVE)    session_start();

date_default_timezone_set('America/Montevideo');

define('PATH_APP', __DIR__);

const PRODUCCION = true; // para definir si es Dev o en  Producción la APP

//HEADERS
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type:application/json;charset=utf-8');
header('Content-Type: application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST,OPTIONS');
//header('Access-Control-Allow-Headers: app-nombre_url,app-token');
http_response_code(200);

$nombre_app = "Sistema_de_Rutas_y_Supervision_GPS";

//Frontend URL 
$referer = isset($_SERVER["HTTP_REFERER"]) ?  $_SERVER["HTTP_REFERER"]  : "https://vida-apps.com/{$nombre_app}/";

$URL_FRONTEND = PRODUCCION ? $referer : "http://localhost/{$nombre_app}/[FRONTEND]/";

const FRONTEND_IMG = "https://vida-apps.com/Sistema_de_Rutas_y_Supervision_GPS/img/";

const PATH_MODELOS = PATH_APP . '/models/';
const PATH_LIB = PATH_APP . '/lib/';
const PATH_DATABASE = PATH_APP . '/database/';
const PATH_UTILS = PATH_APP . '/utils/';

//DB Conexiones
include_once PATH_DATABASE . '/conexiones.php';

//Modelos
include_once PATH_MODELOS . 'db.php';
include_once  PATH_MODELOS . 'usuarios_sistema.php';

//Lib
include_once PATH_LIB . 'monolog/monolog.php';
include_once PATH_LIB . 'validate.php';
include_once PATH_LIB . 'mail/mail.php';
include_once PATH_LIB . 'mail/html_mail_pago_aprobado.php';

//LOGS
const LOGS_DIR =  PATH_APP . '\logs';

//Respuestas
include_once PATH_UTILS . '/respuestas.php';

//Utils /Functions
include_once PATH_UTILS . '/utils.php';

//MENESAJES 
const ERROR_PERMISOS =  ['success' => false, 'mensaje' => 'Usted no cuenta con dichos permisos para ejecutar la operación', 'permisos' => false];
const ERROR_LOGIN = 'Error de usuario o contraseña';
const ERROR_SESSION_USUARIO = 'Error al verificar tu sesión , cierra la sesión y vuelve a ingresar';
const GUARDAR_DATOS = 'Se guardaron los datos con exito';
const ERROR_AL_GUARDAR = 'Error al guardar los datos';




//EMAIL
const ASUNTO_PAGO_APROBADO = 'Pago exitoso';
$URL_FAVICON = FRONTEND_IMG . '/logovida.png';
$URL_IMG = FRONTEND_IMG . '/logovida.png';
//EMAIL



//Manejo de errores personalizados para enviar json 
function Error_handler($errno, $errstr = null, $errfile = null, $errline = null)
{
    if (PRODUCCION === false) {
        if ($errno) {
            die(json_encode(
                [
                    'error' => true,
                    'numero_error' => $errno,
                    'mensaje' => $errstr,
                    'html' => $errstr,
                    'archivo' => $errfile,
                    'linea' => $errline
                ]
            ));
        }
        return true;
    } else  return true;
}

set_error_handler('Error_handler'); // Funcion para enviar errores en formato json 

error_reporting(0); // Para solo mostrar errores en formato json