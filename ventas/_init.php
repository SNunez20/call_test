<?php
if (session_status() !== PHP_SESSION_ACTIVE)    session_start();

set_time_limit(999999999);

const PRODUCCION = true; // para definir si es Dev o en  Producción la APP

const NOMBRE_APP = PRODUCCION ? 'ventas' : 'ventas';
const SERVER_APP = PRODUCCION ? 'http://192.168.1.13/' : 'http://192.168.1.13/';
const URL_APP = SERVER_APP . "" . NOMBRE_APP;

$NOMBRE_APP = NOMBRE_APP;

// DB PRODUCCION
const DB_AFILIFACION_PY_PROD = array("host" => "192.168.13.10", "user" => "root", "password" => "sist.2k8", "db" => "afiliacionparaguay");
const DB_MOTOR_PROD = array("host" => "192.168.13.10", "user" => "root", "password" => "sist.2k8", "db" => "motor_de_precios");
const DB_CALL_PROD = array("host" => "localhost", "user" => "root", "password" => "sist.2k8", "db" => "call");

//DEV O DB TEST
const DB_AFILIFACION_PY_DEV = array("host" => "localhost", "user" => "root", "password" => "sist.2k8", "db" => "afiliacionparaguay_dev");
const DB_MOTOR_DEV = array("host" => "localhost", "user" => "root", "password" => "", "db" => "motor_de_precios");
const DB_CALL_DEV = array("host" => "localhost", "user" => "root", "password" => "sist.2k8", "db" => "call");
// DB PRODUCCION

const DB_PY = PRODUCCION ? DB_AFILIFACION_PY_PROD : DB_AFILIFACION_PY_DEV;
const DB_MOTOR = PRODUCCION ? DB_MOTOR_PROD : DB_MOTOR_DEV;
const DB_CALL = PRODUCCION ? DB_CALL_PROD : DB_CALL_DEV;

/*URL MOTOR DE PRECIOS*/
const URL_MOTOR_DE_PRECIOS = PRODUCCION ? "https://vida-apps.com/".NOMBRE_APP."/motor_de_precios_py/" : "http://localhost/".NOMBRE_APP."/motor_de_precios_py/";
/*URL MOTOR DE PRECIOS*/

define('PATH_APP_RAIZ', __DIR__);
const PATH_PARTIALS = PATH_APP_RAIZ . '/partials/';
const PATH_PARTIALS_VENTAS =PATH_APP_RAIZ. "/partials";

const VERSION_JS = 1.03;