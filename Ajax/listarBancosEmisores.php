<?php
include('../_conexion.php');
session_start();

if(isset($_SESSION['idusuario'])){

    $q = "SELECT id, banco FROM bancos_emisores WHERE mostrar='1'";
   
    if ( $result = mysqli_query($mysqli,$q)) {

        $bancos = array();

        while($row = mysqli_fetch_array($result)) {

            $id=$row['id'];
            $banco=$row['banco'];
        
            $bancos[] =  array('id'=> $id, 'banco'=> $banco);
    
            $response = array(
                'result'  => true,
                'session' => true,
                'message' => 'Bancos listados correctamente.',
                'bancos' => $bancos
            );
        }
    }else{
        $response = array(
            'result' => false,
            'session' => true,
            'message' => 'Ocurrio un error en la consulta.'
        );
    }
   
}else{
    $response = array(
        'result' => false,
        'session' => false,
        'message' => 'sin sesion.'
    );
}
 mysqli_close($mysqli);
 echo json_encode($response);
?>