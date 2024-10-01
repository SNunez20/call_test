<?php
    require_once('mercadopago.php');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");

    $mp = new MP('APP_USR-3794898693286051-052618-fc0fc056114eb9ffde6bd18fb23c3b04-566977467');



    $payment_info = $mp->get('/v1/payments/22104097036');
    var_dump($payment_info);


