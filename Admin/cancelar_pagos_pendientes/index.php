<?php
require_once __DIR__."/../../_conexion.php";
require_once __DIR__."/../../control/lib/mercadopago.php";

$mp = new MP('APP_USR-4283536006492703-082014-6052ef0b0b62b8412cc3192938ca3bca-323867780');

$hoy = date("Y-m-d");
$menos_14_dias = date("Y-m-d" , strtotime ('-14 days' , strtotime ($hoy)));

$q = "SELECT id_pago FROM pagos WHERE estado = 'pending' AND CAST(fecha AS date) <= '$menos_14_dias'";
$result = mysqli_query($mysqli, $q);

while($row = mysqli_fetch_array($result)){
    $id_pago = (string)$row['id_pago'];
    
    $errorTry = false;

    try{
        $cancelacion = $mp->cancel_payment($id_pago);
    }catch(Exception $e){
        $errorTry = true;
    }

    if(!$errorTry && ( $cancelacion['status'] == 200 || $cancelacion['status'] == 201) && $cancelacion['response']['status'] == "cancelled"){
        echo $id_pago;
        echo "<br>";
    }
}
