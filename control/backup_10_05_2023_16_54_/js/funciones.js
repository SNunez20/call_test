function tokenPagar(data) {
    $('#loader').css('display','flex');
    window.addEventListener("message", receiver, false);
    document.getElementById('iframeDePago').contentWindow.postMessage(data, 'https://vida-apps.com/call_pagos/iframe/index.php');
}

function tokenCorrecto() {
    $token           = $('#tokenValidador').val();
    $paymentMethodId = $('#paymentMethodId').val();
    
    if ($token == 'no_usa') {
        $mail_tit = $('#rcMail').val();
        $ced_tit = $('#rcCedula').val();
        $cuotas = null;
    } else {
        $mail_tit = $('#mailTit').val();
        $ced_tit = $('#cedTit').val();
        $cuotas = $('#cuotas').val()
    }
    
    if ($token != '' && $token != null && $paymentMethodId != '' && $paymentMethodId != null) {
        enviarPago();
    } else {
        console.log('Error en el token de mercadopago');
    }
}

function receiver(event) {
    $('#loader').css('display','none');
    if (event.origin == "https://vida-apps.com" && event.data.es_pago) {
        $("#tokenValidador").val('');
        $('#paymentMethodId').val('');
        if (!event.data.error) {
            $("#tokenValidador").val(event.data.token);
            $('#paymentMethodId').val(event.data.metodo);
            tokenCorrecto();
        } else {
            console.log(event.data);
            swal({
              title: "Error",
              text: event.data.errorCual,
              icon: "error",
              buttons: "Cerrar",
            });
        }
    }
}
                            