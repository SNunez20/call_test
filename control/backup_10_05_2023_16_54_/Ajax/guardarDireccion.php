<?php
require_once "../../_conexion.php";

$response = array(
    "result"  => false,
    "session" => false,
    "message" => "Ocurrio un error, vuelva a intentar.",
    "error"   => "" 
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $datosDir       = json_decode($_POST["objdir"], true);
    $data = array();
    $idSocio = obtenerIdSocio($datosDir['idDir']);
   

    if ($idSocio) {

        foreach ($datosDir as $col => $value) {
            $data[] = $col."='".$value."'";
        }
        array_splice($data,7,2);
        $qUpdateTableDir = "UPDATE direcciones_socios SET ".implode(',',$data)." WHERE id = ". $datosDir['idDir'];
        $rUpdateTableDir = mysqli_query($mysqli, $qUpdateTableDir);
    
        $qUpdateDir = "UPDATE padron_datos_socio SET direccion = '".substr($datosDir['direccion'], 0, 36)."' WHERE id =".$idSocio;
        $rUpdateDir = mysqli_query($mysqli, $qUpdateDir);

        if ( $rUpdateDir && $rUpdateTableDir ) {
           $response['result'] = true;
           $response['session'] = true;
           $response['message'] = 'Dirección guardada correctamente';
        }else{
            var_dump(mysqli_error($mysqli)); exit;
        }
    }   
}

function obtenerIdSocio($idDir){
    global $mysqli;
    $qIdSocio = "SELECT id_socio FROM direcciones_socios WHERE id = $idDir";
    $rIdSocio = $mysqli->query($qIdSocio);
    $idSocio  = ($rIdSocio->num_rows > 0) ? $rIdSocio->fetch_assoc()['id_socio'] : false;
    
    return $idSocio;
}

mysqli_close($mysqli);
echo json_encode($response);
