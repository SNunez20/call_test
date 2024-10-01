<?php
    include('../_conexion.php');
    require_once('control/lib/mercadopago.php');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");

    $mp = new MP('APP_USR-3794898693286051-052618-fc0fc056114eb9ffde6bd18fb23c3b04-566977467');

    $json_event = file_get_contents('php://input', true);
    $event = json_decode($json_event);

    if (!isset($event->type, $event->data) || !ctype_digit($event->data->id)){
        http_response_code(400);
        return;
    }

    if ($event->type == 'payment'){
        $payment_info = $mp->get('/v1/payments/'.$event->data->id);
        $idpago = $payment_info["response"]["id"];
        $action = $event->action;
        $estado = $payment_info["response"]["status"];


        $q = "UPDATE pagos SET estado = '$estado' WHERE id_pago = $idpago";
        $result = mysqli_query($mysqli,$q);

        $q2 = "INSERT INTO webhooks VALUES(null,$idpago,'$action','$estado','$fecha')";
        $result2 = mysqli_query($mysqli,$q2);

       
    }
    http_response_code(200);
    return;
?>