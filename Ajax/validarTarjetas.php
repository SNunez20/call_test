<?php
include('../_conexion.php');
session_start();

$response = array(
    'result' => false,
    'session' => false,
    'message' => 'Ocurrio un error, intentelo nuevamente más tarde.'
  );

  if ( isset($_SESSION['idusuario']) ) {    
    $numeroTarjeta= mysqli_real_escape_string($mysqli, $_POST["numeroTarjeta"]);
    $numeroTarjeta= substr($numeroTarjeta,0,4);
    $query="SELECT nombre_vida FROM radios_tarjetas WHERE bin like '%$numeroTarjeta%'";
   
    if ($result=mysqli_query($mysqli,$query)) {
        $tipotarjeta= mysqli_fetch_assoc($result)['nombre_vida'];
        $response = array(
            'result' => true,
            'session' => true,
            'message' => 'Tarjeta válida',
            'tipo_tarjeta'=>$tipotarjeta
        );      
    }else{
        $response = array(
            'result' => false,
            'session' => true,
            'message' => 'Tarjeta inválida'
        );
    }   
  }else{
    $response = array(
        'result' => false,
        'session' => false,
        'message' => 'Sin sesion'
      );    
  }

mysqli_close($mysqli);
echo json_encode( $response );
