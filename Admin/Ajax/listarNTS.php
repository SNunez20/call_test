<?php
include('../../_conexion.php');
session_start();
if(isset($_SESSION['idadmin'])){
    $idhistorico = $_GET['id'];
    $q = "select * from pnts where idhistorico = $idhistorico";
    $result = mysqli_query($mysqli,$q);
    $nts = array();

    while($row = mysqli_fetch_array($result)) {
        $san=$row['sanatorio'];
        $con=$row['convalecencia'];
        $dom=$row['domicilioEspecial'];
        $rei=$row['reintegro'];
        $amp=$row['amparo'];
        $ampp=$row['amparoPlus'];
        $asse=$row['assistExpress'];
        $assp=$row['assistPlus'];
        $hot=$row['hotel'];
        $gru=$row['grupoFamiliar'];
        $tar=$row['tarjetaVida'];
        $fb=$row['fb2012'];  
        $sup=$row['superPromo'];
        $pro=$row['promoCompetencia'];       
        
        $nts[] =  array('san'=> $san, 'con'=> $con, 'dom'=> $dom, 'rei'=> $rei, 'amp'=> $amp, 'ampp'=> $ampp, 'asse'=> $asse, 'assp'=> $assp, 'hot'=> $hot, 'gru'=> $gru, 'tar'=> $tar, 'fb'=> $fb, 'sup'=> $sup, 'pro'=> $pro);
    }
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
 mysqli_close($mysqli);
 echo json_encode( $nts );
?>