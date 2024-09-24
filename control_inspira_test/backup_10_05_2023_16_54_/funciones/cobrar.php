<?php

require_once('../lib/mercadopago.php');

function cobrar($cedula,$email,$total,$metodo,$token,$mysqli){

    
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");

    $mp = new MP('APP_USR-3794898693286051-052618-fc0fc056114eb9ffde6bd18fb23c3b04-566977467');
        $payment_data = array(
            'transaction_amount' => (int)$total,
            'token' => $token,
            'description' => 'ABM CALL',
            'installments' => (int)1,
            'payment_method_id' => $metodo,
            'payer' => array (
                'email' => $email,
                'identification' => array(
                    'type' => 'CI',
                    'number' => $cedula
                )
            ),
        );
        $errorTry = false;

        try{
        
            $payment = $mp->post("/v1/payments", $payment_data);

        }catch(Exception $e){
            $errorTry = true;
        }

        if(!$errorTry){

            $status         = $payment['response']['status'];
            $status_detail  = $payment['response']['status_detail'];

            $respuesta = array(
                'result' => true,
                'correcto' => false,
                'error' => true,
                'status' => $status,
                'status_detail' => $status_detail,
                'mensaje' => '',
                'metodo' => $metodo,
                'approved' => false,
                'pending' => false,
                'in_process' => false,
                'rejected' => false,
                'idpago' => '',
                'tipo_mensaje' => 'error',
                'titulo_mensaje' => 'Error',
                'comprobante' => ''
            );

            

            switch ($payment['status'])
            {
                case '106':
                    $respuesta['mensaje'] = 'Sólo se pueden efectuar pagos dentro de Uruguay.';
                    break;
                case '200':
                case '201':
                    $respuesta['correcto'] = true;
                    $respuesta['error'] = false;
                    switch ($status) 
                    {
                        case 'approved':
                            $respuesta['approved'] = true;
                            $respuesta['mensaje'] = 'El pago se ha efectuado de forma correcta.';
                            break;
                        case 'pending':
                            $respuesta['pending'] = true;
                            $respuesta['mensaje'] = ($payment['status']['payment_method_id'] == 'redpagos' || $payment['status']['payment_method_id'] == 'abitab')
                                ? 'Se le acreditará el pago una vez abone el monto acordado en la red de cobranzas más cercana.'
                                : "Su pago está siendo procesado.";
                            $respuesta['cedula'] = $cedula;
                            $respuesta['monto'] = $total;
                            $respuesta['red'] = ucwords($metodo);
                            $respuesta['vencimiento'] = Date('d/m/Y', strtotime("+5 days"));
                            break;
                        case 'in_process':
                            $respuesta['mensaje'] = ($status_detail == 'pending_contingency')
                                ? "Su pago está siendo procesado.
                                    En aproximadamente una hora le llegará un correo a: <b>$email</b> con los detalles."
                                : "Su pago está siendo procesado.
                                    En aproximadamente dos días (hábiles) le llegará un correo a: <b>$email</b> con más detalles.";
                            break;
                        case 'rejected':
                            $respuesta['rejected'] = true;
                            switch ($status_detail)
                            {
                                case 'cc_rejected_bad_filled_card_number':
                                    $respuesta['mensaje'] = 'Por favor verifique el número de la tarjeta.';
                                    break;
                                case 'cc_rejected_bad_filled_date':
                                    $respuesta['mensaje'] = 'Por favor verifique la fecha de vencimiento.';
                                    break;
                                case 'cc_rejected_bad_filled_other':
                                    $respuesta['mensaje'] = 'Por favor verifique la información de la tarjeta.';
                                    break;
                                case 'cc_rejected_bad_filled_security_code':
                                    $respuesta['mensaje'] = 'Por favor verifique el código de seguridad de la tarjeta.';
                                    break;
                                case 'cc_rejected_call_for_authorize':
                                    $respuesta['mensaje'] = 'Debe de darle permiso a su tarjeta para abonar por MercadoPago.';
                                    break;
                                case 'cc_rejected_card_disabled':
                                    $respuesta['mensaje'] = 'Su tarjeta está deshabilitada.';
                                    break;
                                case 'cc_rejected_duplicated_payment':
                                    $respuesta['mensaje'] = 'Ya ha efectuado un pago por la misma cantidad, no se efectuará el pago.';
                                    break;
                                case 'cc_rejected_high_risk':
                                    $respuesta['mensaje'] = 'El pago ha sido rechazado, trate con otra tarjeta u otro método de pago.';
                                    break;
                                case 'cc_rejected_insufficient_amount':
                                    $respuesta['mensaje'] = 'Saldo insuficiente.';
                                    break;
                                case 'cc_rejected_max_attempts':
                                    $respuesta['mensaje'] = 'Ha llegado a el máximo de intentos para pagos, espere un momento o trate con otro método de pago.';
                                    break;
                                case 'cc_rejected_blacklist':
                                case 'cc_rejected_card_error':
                                case 'cc_rejected_other_reason':
                                case 'cc_rejected_invalid_installments':
                                    $respuesta['mensaje'] = 'No hemos podido procesar su pago.';
                                    break;
                                default:
                                    $respuesta['mensaje'] = 'Ha ocurrido un error al procesar su pago.';
                            }
                            break;
                    }
                    break;
                case '801':
                    $respuesta['mensaje'] = 'Ha hecho un pago similar hace poco, trate nuevamente en unos minutos.';
                    break;
                default:
                    $respuesta['mensaje'] = 'No se ha podido procesar el pago.
                                            Puede intentar con otra tarjeta u otro medio de pago.';
            }

            if($respuesta['correcto']){
                $idPago = $payment['response']['id'];
                $qPago = "INSERT INTO pagos VALUES(null,$idPago,'$token',$total,'$metodo','$status','$fecha')";
                $rPago = mysqli_query($mysqli,$qPago);

                if($rPago){
                    if($respuesta['approved'] || $respuesta['pending'] || $respuesta['in_process']){
                        if($respuesta['approved']){
                            $mensajeFinal = "Compra realizada correctamente, el número de referencia de su compra es ";
                        }else if($respuesta['pending']){
                            if($metodo == 'abitab' || $metodo == 'redpagos'){
                                $mensajeFinal = "Su pedido quedó pendiente a la espera de que abone en su ".$metodo." mas cercano. De no realizar el pago en las proximas 48 horas su pedido será cancelado y se le reintegraran sus VidaPesos. El número de referencia de su compra es ";
                            }else{
                                $mensajeFinal = "Su pedido quedó pendiente a que su tarjeta ".$metodo." apruebe el pago. De no concretarse en las proximas 48 horas su pedido será cancelado y se le reintegraran sus VidaPesos. El número de referencia de su compra es ";
                            }
                        }else if($respuesta['in_process']){
                            $mensajeFinal = "El pago está siendo procesado por su emisora de tarjeta, una vez aprobado el pago su compra será completada";
                        }


                          
                        $respuesta['mensaje'] = $mensajeFinal;
                        $respuesta['idpago'] = $idPago;
                        $respuesta['comprobante'] = $payment['response']['transaction_details']['external_resource_url'];
                        $respuesta['fecha_pago'] = $fecha;
                     
                    }

                    if($respuesta['approved']){
                        $respuesta['tipo_mensaje'] = "success";
                        $respuesta['titulo_mensaje'] = "Correcto";
                    }else if($respuesta['pending']){
                        $respuesta['tipo_mensaje'] = "warning";
                        $respuesta['titulo_mensaje'] = "Pago Pendiente";
                    }else if($respuesta['in_process']){
                        $respuesta['tipo_mensaje'] = "warning";
                        $respuesta['titulo_mensaje'] = "Pago En Proceso";
                    }else if($respuesta['rejected']){
                        $respuesta['tipo_mensaje'] = "error";
                        $respuesta['titulo_mensaje'] = "Pago Rechazado";
                    }
                }
            }

        }else{
            $respuesta = array(
                'result' => true,
                'correcto' => false,
                'error' => true,
                'mensaje' => 'Ha ocurrido un error, intente más tarde!',
                'approved' => false,
                'pending' => false,
                'in_process' => false,
                'rejected' => false,
                'tipo_mensaje' => 'error',
                'titulo_mensaje' => 'Error',
                'comprobante' => ''
            );
        }
        return json_encode($respuesta);
}


?>