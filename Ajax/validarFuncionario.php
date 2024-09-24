<?php

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once '../_conexion250.php';

$response = array(
    'result'  => false,
    'session' => false,
    'message' => 'Ocurrio un error, intentelo nuevamente más tarde.',
);

$cedula = mysqli_real_escape_string($mysqli250, $_POST["cedula"]);

$fields = array(
                "token"=> "wRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
                "cedula"=> (string)$cedula
               );

$url = "http://192.168.1.250:82/ws_funcionarios/index.php";
$ch = curl_init();

curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result);

// var_dump($result);

if($result->error){
    exit($result->mensaje);
    $response['message']=$result->$mensaje;
}

if($result->funcionario_valido){
    $response['result']  = $result->funcionario_valido;
    $response['message'] = $result->mensaje;
   
}else{
    $response['result']  = $result->funcionario_valido;
    $response['message'] = $result->mensaje;
}

echo json_encode($response);

?>