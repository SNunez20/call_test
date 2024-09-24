<?php
include '../_conexion.php';
include '../../cdns/php/Agente.php';
session_start();
$response = ['error' => true, 'mensaje' => 'ERROR: Ocurrió un error, CONTACTE AL ADMINISTRADOR', 'desloguear' => false];

if (!isset($_SESSION['idusuario'])) {
    $response['desloguear'] = true;
    die(json_encode($response));
}

$id_usuario = $_SESSION['idusuario'];

//ESTO ES POR SI EL AGENTE NO ES LA CEDULA
/**$q = "SELECT nro_agente FROM agentes_expand WHERE id_usuario = $id_usuario AND activo = 1";
$result = mysqli_query($mysqli, $q);*/

$q = "SELECT usuario FROM usuarios WHERE id = $id_usuario AND activo = 1";
$result = mysqli_query($mysqli, $q);


if(!mysqli_num_rows($result)){
    $response['mensaje'] = 'Su usuario esta inactivo, contacte a su supervisor.';
    die(json_encode($response));
}

$rowAgente = mysqli_fetch_assoc($result);

$nro_agente = $rowAgente['usuario'];

$Agente = new Agente($nro_agente);

$Agente_username = $Agente->getUsername();

if(!$Agente_username){
    $response['mensaje'] = 'Usted no cuenta con un agente de expand activo, contacte a su supervisor para que le gestione uno.';
    die(json_encode($response));
}

if(!$Agente->getLogueado()){
    $response['mensaje'] = 'Antes de poder realizar llamadas, debe loguearse en el interno que va a utilizar.';
    die(json_encode($response));
}

if ($Agente->getExtension() == null) {
    $response['mensaje'] = 'Ocurrió un error determinando el interno al que esta logueado, deslogueese del interno y vuelva a loguearse con su agente.';
    die(json_encode($response));
}

$Agente->llamada->setReceptor($_POST['numero']);
$Agente->llamada->dstCallerID = $_POST['numero'];
$Agente->llamada->realizarLlamada();
sleep(2);

$callId = $Agente->llamada->getCallId();

$q2 = "INSERT INTO llamadas_expand VALUES(null, '".$_POST['numero']."', $id_usuario, '$callId', NOW())";
$result2 = mysqli_query($mysqli, $q2);

if(is_numeric($callId)){
    $response['mensaje'] = 'No se pudo realizar la llamada, intente mas tarde, o con otro numero.';
    $response['CallId'] = $Agente->llamada->getCallId();
    die(json_encode($response));
}

if(!$result2){
    $response['mensaje'] = 'Hubo un error registrando la llamada, contacte al administrador del sistema.';
    die(json_encode($response));
}

$response['error'] = false;
$response['mensaje'] = 'Correcto: A la brevedad debería sonar su interno con el numero que intenta llamar';

die(json_encode($response));