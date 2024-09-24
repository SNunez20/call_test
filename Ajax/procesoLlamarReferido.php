<?php
include('../_conexion.php');
session_start();
$response     = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {
    $numeroReferido = $_GET['numero'];
    if (isset($_SESSION['numero'])) {
        $numero = $_SESSION["numero"];
    } else {
        $numero = "";
    }
    $idusuario = $_SESSION['idusuario'];
    $_SESSION["numero"] = $numeroReferido;
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $chk = '';
    $q = "update numeros set flag = 'libre' where numero = '$numero' and flag != 'agendado' and flag != 'referido' and flag != 'no contesta'";
    $result = mysqli_query($mysqli, $q);
    if ($result) {
        $q3 = "update session set numero = '$numeroReferido' where idusuario = $idusuario";
        $result3 = mysqli_query($mysqli, $q3);
        if ($result3) {
            $q4 = "insert into historico values(null,'$numeroReferido','tomado de referidos',$idusuario,'$fecha','$chk')";
            $result4 = mysqli_query($mysqli, $q4);
            if ($result4) {
                $qLocalidad = "select dep_localidad from numeros where numero = '$numeroReferido'";
                $resultLocalidad = mysqli_query($mysqli, $qLocalidad);
                while ($row = mysqli_fetch_assoc($resultLocalidad)) {
                    $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));;
                }
                $devuelve = mysqli_num_rows($resultLocalidad);
                if ($devuelve > 0) {
                    $response = array('result' => 'NumeroLlamarReferido', 'numero' => $numeroReferido, 'message' => 'Correcto.', 'localidad' => $localidad);
                } else {
                    $response = array('result' => 'NumeroLlamarReferido', 'numero' => $numeroReferido, 'message' => 'Correcto.', 'localidad' => 'Localidad Desconocida');
                }
            }
        }
    }
} else {
    $response     = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);
