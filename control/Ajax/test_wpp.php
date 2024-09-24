<?php

$respuesta = enviarWPP('092100700', 'Sebastián Mainero', 'https://www.vida.com.uy/TOS/?v&7');
var_dump($respuesta);
function enviarWPP($celular, $nombre, $link_tos)
{
    $URL_CONSULTA = "https://vida-apps.com/ws_whatsapp/";
    $USERNAME = "ws_vida_wpp";
    $PASSWORD = ":=!7OK:Q2;Nb#JE8P3i£TcWz<lwBb1x(r0NsW,H2";
    $TEMPLATE = "bienvenido";
    $ID_APLICACION = 1;
    $TOKEN_APLICACION = "275e3db0c4dd1ac0f37588ab258cbc12";

    $DATA = json_encode([
        "celular_to" => (string) $celular,
        "template_name" => (string) $TEMPLATE,
        "id_aplicacion" => (int) $ID_APLICACION,
        "token_aplicacion" => (string) $TOKEN_APLICACION,
        "parametros" => [
            [
                "nro" => 1,
                "texto" => (string) $nombre
            ],
            [
                "nro" => 2,
                "texto" => (string) $link_tos
            ]
        ]
    ]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_USERPWD, $USERNAME . ":" . $PASSWORD);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $DATA);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_URL, $URL_CONSULTA);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $respuesta_WS = curl_exec($ch);
    $errCurl = $respuesta_WS === false ? curl_error($ch) : false;
    curl_close($ch);

    if ($errCurl)
        return false;

    return true;
}
