<?php
session_start();
//if(isset($_SESSION['idadmin'])){
    //include('../../_conexion.php');
    $mysqli = mysqli_connect('localhost', 'root', 'sist.2k8','call');

    if (mysqli_connect_errno())
    {
        echo "Error al conectar a MySql: " . mysqli_connect_error();
    }
    require("lib/PHPMailerAutoload.php");
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $fecha_log = date("Y-m");
    $tiempo_total = "";
    $tiempos[] = "";
    
    
    $file = 'C:\wamp64\www\call\Admin\velocidad_mysql\logs/'.$fecha_log.'-log.txt';

    if(!file_exists($file)){
        $fp = fopen($file,"w"); 
        fwrite($fp,""); 
        fclose($fp);
    }
    $current = file_get_contents($file);
    $current .= "Informe fecha $fecha\r\n";
    $current .= "----------------------------------\r\n";
    
    for ($i = 1; $i <= 20; $i++) {
        //$a1 = array("A", "B");
        //$grupo1 = $a1[array_rand($a1)];
        //$a2 = array("C", "D");
        //$grupo2 = $a2[array_rand($a2)];
        //$a3 = array("E", "F");
        //$grupo3 = $a3[array_rand($a3)];
        //$a4 = array("G", "H");
        //$grupo4 = $a4[array_rand($a4)];
        //$a5 = array("I", "J");
        //$grupo5 = $a5[array_rand($a5)];
        $qGrupo = "select * from relacion where idgrupousuarios=2 ORDER BY rand() limit 1";
        $resultGrupo = mysqli_query($mysqli,$qGrupo);
        $devuelveGrupo = mysqli_num_rows($resultGrupo);
        if($resultGrupo){
            if($devuelveGrupo > 0){
                 while ($row = mysqli_fetch_assoc($resultGrupo)) {
                    $grupo = $row['idgrupotel'];
                 }
            }else{
                $grupo = 'sin grupo';
            }
        }
        $q = "select * from numeros where grupo = '$grupo' and flag='libre' order by rand() LIMIT 1";
        $msc = microtime(true);
        $result = mysqli_query($mysqli,$q);
        $msc = microtime(true)-$msc;
        $current .= "Tiempo $i --- $msc s\r\n";
        if($msc >3){
            $tiempos[$i] =  "<span style = 'color:red'>" . $msc . " s</span>";
            echo "<p style = 'color:red'>" . $i . ")  " . $tiempos[$i] . "</p>";
        }else if($msc <=3 && $msc >= 1){
            $tiempos[$i] =  "<span style = 'color:orange'>" . $msc . " s</span>";
            echo "<p style = 'color:orange'>" . $i . ")  " . $tiempos[$i] . "</p>";
        }else{
            $tiempos[$i] =  "<span style = 'color:green'>" . $msc . " s</span>";
            echo "<p style = 'color:green'>" . $i . ")  " . $tiempos[$i] . "</p>";
        }
        $tiempo_total = (float)$tiempo_total + (float)$msc;
    }
    $promedio = $tiempo_total / 20;
    $current .= "Promedio: $promedio s\r\n";
    $current .= "\r\n";
    file_put_contents($file, $current);
    if($promedio >3){
        $promedio = "<span style = 'color:red'>". $promedio . ' s</span>';
        echo "<p style = 'color:red'>El promedio fue: ". $promedio . '</p>'; // in seconds
    }else if($promedio <=3 && $promedio >= 1){
        $promedio = "<span style = 'color:orange'>". $promedio . ' s</span>';
        echo "<p style = 'color:orange'>El promedio fue: ". $promedio . '</p>'; // in seconds
    }else{
        $promedio = "<span style = 'color:green'>". $promedio . ' s</span>';
        echo "<p style = 'color:green'>El promedio fue: ". $promedio . '</p>'; // in seconds
    }
    
    
    $sBody = '<html xmlns="https://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Informe tiempo respuesta mysql(call)</title>
            </head>

            <body>
                <h1>Informe tiempo respuesta mysql(call)</h2>
                <h1>Fecha del informe <span style="font-weight:bold">'.$fecha.'</span></h1>
                <h2>Todos los tiempos</h2>
                <p style="font-size:16px">Tiempo 1: <span style="font-weight:bold">'.$tiempos[1].'</span>     Tiempo 2: <span style="font-weight:bold">'.$tiempos[2].'</span></p>
                <p style="font-size:16px">Tiempo 3: <span style="font-weight:bold">'.$tiempos[3].'</span>     Tiempo 4: <span style="font-weight:bold">'.$tiempos[4].'</span></p>
                <p style="font-size:16px">Tiempo 5: <span style="font-weight:bold">'.$tiempos[5].'</span>     Tiempo 6: <span style="font-weight:bold">'.$tiempos[6].'</span></p>
                <p style="font-size:16px">Tiempo 7: <span style="font-weight:bold">'.$tiempos[7].'</span>     Tiempo 8: <span style="font-weight:bold">'.$tiempos[8].'</span></p>
                <p style="font-size:16px">Tiempo 9: <span style="font-weight:bold">'.$tiempos[9].'</span>     Tiempo 10: <span style="font-weight:bold">'.$tiempos[10].'</span></p>
                <p style="font-size:16px">Tiempo 11: <span style="font-weight:bold">'.$tiempos[11].'</span>   Tiempo 12: <span style="font-weight:bold">'.$tiempos[12].'</span></p>
                <p style="font-size:16px">Tiempo 13: <span style="font-weight:bold">'.$tiempos[13].'</span>   Tiempo 14: <span style="font-weight:bold">'.$tiempos[14].'</span></p>
                <p style="font-size:16px">Tiempo 15: <span style="font-weight:bold">'.$tiempos[15].'</span>   Tiempo 16: <span style="font-weight:bold">'.$tiempos[16].'</span></p>
                <p style="font-size:16px">Tiempo 17: <span style="font-weight:bold">'.$tiempos[17].'</span>   Tiempo 18: <span style="font-weight:bold">'.$tiempos[18].'</span></p>
                <p style="font-size:16px">Tiempo 19: <span style="font-weight:bold">'.$tiempos[19].'</span>   Tiempo 20: <span style="font-weight:bold">'.$tiempos[20].'</span></p>
                
                <h2>Tiempo promedio</h2>
                <p style="font-size:16px">El tiempo promedio: <span style="font-weight:bold">'.$promedio.'</span></p>
                
                <h2>Grupos de telefonos usados</h2>
                <p style="font-size:16px">Grupo usado: <span style="font-weight:bold;color:blue">'.$grupo.' </span></p>
                <br />
                <a style="font-size:16px" href="http://192.168.1.13/call/admin/velocidad_mysql/logs/'.$fecha_log.'-log.txt">Ver Log</a>
            </body>
            </html>';
            
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->isHTML(true);
            $mail->Mailer = "smtp";
            $mail->Host = "mail.vida.com.uy";
            $mail->Port = "25";

            $mail->SMTPAuth = true;
            $mail->Username = "programacion@vida.com.uy";
            $mail->Password = "sist.2k8";
            $mail->SMTPOptions = array(

            'ssl' => array(

		      'verify_peer' => false,

		      'verify_peer_name' => false,

		      'allow_self_signed' => true

	       )

          );
          $mail->From     = "programacion@vida.com.uy";
          $mail->FromName = "Informe Respuesta Mysql";
          $mail->AddAddress("programacion@vida.com.uy");
          $mail->Subject  = "Nuevo Informe";
          $mail->Body     = $sBody;
          $mail->WordWrap = 50;
          if($mail->Send()){
            echo "Se envio correctamente el mail de informe";
          }else{
            echo "No fue posible enviar el mail de informe";
          }
    mysqli_close($mysqli);
//}else{
    //header('Location: ../login.php');
///}
?>