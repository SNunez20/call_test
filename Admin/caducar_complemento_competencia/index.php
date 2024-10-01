<?php
require_once __DIR__ . "/../../_conexion.php";
require_once __DIR__ . "/../lib/PHPMailerAutoload.php";

$q = "SELECT
            p.id AS id_socio,
            p.cedula AS cedula_socio,
            p.nombre AS nombre_socio,
            u.usuario AS cedula_vendedor,
            u.nombre AS nombre_vendedor,
            g.nombre AS callcenter
        FROM
            padron_datos_socio AS p
            INNER JOIN ( SELECT id, id_cliente, MAX( fecha ) AS fecha, id_estado FROM historico_venta WHERE id_estado = 693 GROUP BY id_cliente, id_estado ORDER BY id DESC ) AS h ON p.id = h.id_cliente 
            INNER JOIN usuarios AS u on p.id_usuario = u.id
            INNER JOIN gruposusuarios AS g ON u.idgrupo = g.id
        WHERE
            p.estado = 693 
            AND DATE_ADD( h.fecha, INTERVAL 72 HOUR ) < NOW()";

$result = mysqli_query($mysqli, $q);

while($row = mysqli_fetch_array($result)){
    $id_socio = $row['id_socio'];
    $cedula_socio = $row['cedula_socio'];
    $nombre_socio = $row['nombre_socio'];
    $cedula_vendedor = $row['cedula_vendedor'];
    $nombre_vendedor = $row['nombre_vendedor'];
    $callcenter = $row['callcenter'];

    $qAvisado = "SELECT id FROM avisos_competencia WHERE id_socio = $id_socio";
    $rAvisado = mysqli_query($mysqli, $qAvisado);

    if(mysqli_num_rows($rAvisado) == 0){
        $sBody = <<<HTML
            <html xmlns="https://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Complemento competencia sin comprobante</title>
                </head>
                <body>
                    <h1>Complemento competencia sin comprobante</h2>
                    <h2>Pasaron 3 días y aun no se sube el comprobante para el socio:</h2>
                    <p>Cédula socio: <span style="font-weight: bold">$cedula_socio</span></p>
                    <p>Nombre socio: <span style="font-weight: bold">$nombre_socio</span></p>
                    <p>Cédula vendedor: <span style="font-weight: bold">$cedula_vendedor</span></p>
                    <p>Nombre vendedor: <span style="font-weight: bold">$nombre_vendedor</span></p>
                    <p>Call: <span style="font-weight: bold">$callcenter</span></p>
                </body>
            </html>
HTML;
                
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->isHTML(true);
        $mail->Mailer = "smtp";
        $mail->Host = "mail.vida.com.uy";
        $mail->Port = "26";

        $mail->SMTPAuth = true;
        $mail->Username = "info@vida.com.uy";
        $mail->Password = "Q528416d";
        $mail->SMTPOptions = array(

        'ssl' => array(

            'verify_peer' => false,

            'verify_peer_name' => false,

            'allow_self_signed' => true

        )

        );
        $mail->From     = "info@vida.com.uy";
        $mail->FromName = "Complemento competencia";
        $mail->AddAddress("nicolas.g@vida.com.uy");
        $mail->AddCC("comercial@vida.com.uy");
        $mail->Subject  = "Complemento competencia sin comprobante";
        $mail->Body     = $sBody;
        $mail->WordWrap = 50;
        $mail->Send();

        $qInsAviso = "INSERT INTO avisos_competencia VALUES(null, $id_socio, '$cedula_socio', NOW())";
        $rInsAviso = mysqli_query($mysqli, $qInsAviso);
    }
}



$q = "SELECT
            p.id,
            p.cedula
        FROM
            padron_datos_socio AS p
            INNER JOIN (SELECT id, id_cliente, MAX(fecha) AS fecha, id_estado FROM historico_venta WHERE id_estado = 693 GROUP BY id_cliente, id_estado ORDER BY id DESC) AS h ON p.id = h.id_cliente 
        WHERE
            p.estado = 693 AND
            DATE_ADD(h.fecha, INTERVAL 120 HOUR) < NOW()";

$result = mysqli_query($mysqli, $q);

$id_usuario_sistema = 74;

while($row = mysqli_fetch_array($result)){
    $id_afiliado = $row['id'];
    $cedula_afiliado = $row['cedula'];

    $q2 = "INSERT INTO 
            historico_venta 
                (id_usuario, id_cliente, id_estado, fecha, observacion, id_rechazo) 
            VALUES 
                ($id_usuario_sistema, $id_afiliado, 676, NOW(), 'COMPLEMENTO COMPETENCIA VENCIDO POR EL SISTEMA', 11)";
    
    $result2 = mysqli_query($mysqli, $q2);



    $q3 = "UPDATE 
            padron_datos_socio 
                SET estado = 676
            WHERE id = $id_afiliado";
    
    $result3 = mysqli_query($mysqli, $q3);
}

mysqli_close($mysqli);