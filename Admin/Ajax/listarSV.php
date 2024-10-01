<?php
include('../../_conexion.php');
session_start();
if(isset($_SESSION['idadmin'])){
    $idhistorico = $_GET['id'];
    $q = "select * from psv where idhistorico = $idhistorico";
    $result = mysqli_query($mysqli,$q);
    $sv = array();

    while($row = mysqli_fetch_array($result)) {
        $san=$row['sanatorio'];
        $con=$row['convalecencia'];
        $dom=$row['domicilioEspecial'];
        $rei=$row['reintegro'];
        $ampp=$row['amparoPlus'];
        $assp=$row['assistPlus'];
        $hot=$row['hotel'];       
        
        $sv[] =  array('san'=> $san, 'con'=> $con, 'dom'=> $dom, 'rei'=> $rei, 'ampp'=> $ampp, 'assp'=> $assp, 'hot'=> $hot);
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
 mysqli_close($mysqli);
 echo json_encode( $sv );
?>