<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
$hoy = date("Y-m-d");
$hoy2 = date('d/m/Y');

require("lib/PHPMailerAutoload.php");

$mysqli = mysqli_connect('162.144.40.168', 'eldesafi_root', 'sist.2k8','eldesafi_o');

if (mysqli_connect_errno())
{
    echo "Error al conectar a MySql: " . mysqli_connect_error();
}
$q = "select * from jugadores_referidos where cast(fecha as date) = '$hoy' and telefono_jugador like '09%'";
$result = mysqli_query($mysqli,$q);
while($row = mysqli_fetch_array($result)){
    $referidos[] = array('id' => $row['id'], 'idcall' => $row['idusuario_call'], 'fecha' => $row['fecha']); 
}
mysqli_close($mysqli);

$mysqli = mysqli_connect('localhost', 'root', 'sist.2k8','call');

if (mysqli_connect_errno())
{
    echo "Error al conectar a MySql: " . mysqli_connect_error();
}

$q2 = "create temporary table referidos_desafio (`id` int NOT NULL, `idusuario_call` int NOT NULL, `fecha` datetime, PRIMARY KEY(id))";
$result2 = mysqli_query($mysqli,$q2);
foreach($referidos as $r){
    $q3 = "insert into referidos_desafio values(".$r['id'].",".$r['idcall'].",'".$r['fecha']."')";
    $result3 = mysqli_query($mysqli,$q3);
}
$qGar = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 2";
$rGar = mysqli_query($mysqli,$qGar);
$total_gar = mysqli_num_rows($rGar);

$qGarNoc = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 9";
$rGarNoc = mysqli_query($mysqli,$qGarNoc);
$total_garnoc = mysqli_num_rows($rGarNoc);

$qGar2 = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 13";
$rGar2 = mysqli_query($mysqli,$qGar2);
$total_gar2 = mysqli_num_rows($rGar2);

$qDur = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 1";
$rDur = mysqli_query($mysqli,$qDur);
$total_dur = mysqli_num_rows($rDur);

$qDurNoc = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 7";
$rDurNoc = mysqli_query($mysqli,$qDurNoc);
$total_durnoc = mysqli_num_rows($rDurNoc);

$qMel = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 6";
$rMel = mysqli_query($mysqli,$qMel);
$total_mel = mysqli_num_rows($rMel);

$qMelNoc = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 12";
$rMelNoc = mysqli_query($mysqli,$qMelNoc);
$total_melnoc = mysqli_num_rows($rMelNoc);

$qMin = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 5";
$rMin = mysqli_query($mysqli,$qMin);
$total_min = mysqli_num_rows($rMin);

$qMinNoc = "select * from referidos_desafio as r inner join usuarios as  u on r.idusuario_call = u.id where u.idgrupo = 8";
$rMinNoc = mysqli_query($mysqli,$qMinNoc);
$total_minnoc = mysqli_num_rows($rMinNoc);

$suma = $total_gar + $total_garnoc + $total_gar2 + $total_dur + $total_durnoc + $total_mel + $total_melnoc + $total_min + $total_minnoc;

mysqli_close($mysqli);

    $sBody = '<html xmlns="https://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Informe tiempo respuesta mysql(call)</title>
            </head>

            <body>
                <h1>Informe Referidos El Desafio (de hoy)</h2>
                <h2>Listado de celulares ingresados:</h2>
                <p>Garibaldi: <b>'.$total_gar.'</b></p>
                <p>Garibaldi Nocturno: <b>'.$total_garnoc.'</b></p>
                <p>Garibaldi Ana Laura: <b>'.$total_gar2.'</b></p>
                <p>Minas: <b>'.$total_min.'</b></p>
                <p>Minas Nocturno: <b>'.$total_minnoc.'</b></p>
                <p>Durazno: <b>'.$total_dur.'</b></p>
                <p>Durazno Nocturno: <b>'.$total_durnoc.'</b></p>
                <p>Melo: <b>'.$total_mel.'</b></p>
                <p>Melo Nocturno: <b>'.$total_melnoc.'</b></p>
                <br />
                <span><b>TOTAL: '.$suma.'</b></span>
            </body>
            </html>';
            
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->isHTML(true);
            $mail->Mailer = "smtp";
            $mail->Host = "mail.vida.com.uy";
            $mail->Port = "26";

            $mail->SMTPAuth = true;
            $mail->Username = "informe_referidos@vida.com.uy";
            $mail->Password = "2k8.vida";
            $mail->SMTPOptions = array(

            'ssl' => array(

		      'verify_peer' => false,

		      'verify_peer_name' => false,

		      'allow_self_signed' => true

	       )

          );
          $mail->From     = "informe_referidos@vida.com.uy";
          $mail->FromName = "Informe Referidos Desafio";
          $mail->AddAddress("carlos.goni@vida.com.uy");
          $mail->Subject  = "informe de hoy";
          $mail->Body     = $sBody;
          $mail->WordWrap = 50;
          $mail->Send();

?>