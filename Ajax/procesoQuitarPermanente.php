<?php
include '../_conexion.php';
session_start();
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {
    $idusuario = $_SESSION['idusuario'];
    $data      = array_map('stripslashes', $_POST);
    $numero    = $_SESSION["numero"];
    //date_default_timezone_set('America/Montevideo');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $obs   = str_replace("'", "", $data['observacion']);
    $chk   = '';

    $q      = "update numeros set flag = 'borrado permanente' where numero = '$numero'";
    $result = mysqli_query($mysqli, $q);
    if ($result) {
        $q2      = "insert into historico values(null,'$numero','borrado permanente',$idusuario,'$fecha','$chk')";
        $result2 = mysqli_query($mysqli, $q2);
        if ($result2) {
            $q3      = "insert into listanegra values(null,'$numero','$obs','$fecha',$idusuario)";
            $result3 = mysqli_query($mysqli, $q3);
            if ($result3) {
                $grupoUsuario = $_SESSION["grupoUsuario"];
                $qGrupo       = "select * from relacion where idgrupousuarios=$grupoUsuario ORDER BY RAND()";
                $resultGrupo  = mysqli_query($mysqli, $qGrupo);
                $cantGrupos   = mysqli_num_rows($resultGrupo);
                while ($reg = mysqli_fetch_array($resultGrupo)) {
                    $grupos[] = $reg['idgrupotel'];
                }
                $qBorrarAgendado              = "delete from agendados where numero = '$numero'";
                $resultBorrarAgendado         = mysqli_query($mysqli, $qBorrarAgendado);
                $qBorrarReferido              = "delete from referidos where numero = '$numero'";
                $resultBorrarReferido         = mysqli_query($mysqli, $qBorrarReferido);
                $qBorrarReferidoCuaderno      = "delete from referidoscuaderno where numero = '$numero'";
                $resultBorrarReferidoCuaderno = mysqli_query($mysqli, $qBorrarReferidoCuaderno);
                if ($resultGrupo) {
                    if ($cantGrupos > 0) {
                        for ($i = 0; $i < count($grupos); $i++) {
                            $q4 = "select * from numeros where grupo = '$grupos[$i]' and flag = 'libre' order by rand() LIMIT 1";
                            // $q4 = "select n.numero, n.flag,n.dep_localidad, h.fecha from numeros as n
                            // left join historico as h
                            // on n.numero =h.numero
                            // where (DATEDIFF( now( ), h.fecha ) > 30 or ISNULL(h.id)) and n.grupo = '$grupos[$i]' and n.flag = 'libre'
                            // order by rand(),fecha desc limit 1";
                            $result4  = mysqli_query($mysqli, $q4);
                            $devuelve = mysqli_num_rows($result4);
                            if ($devuelve > 0) {
                                $i = count($grupos);
                            }
                        }
                    } else {
                        $grupos = 'sin grupo';
                    }
                }
                if ($devuelve > 0) {
                    while ($row = mysqli_fetch_assoc($result4)) {
                        $numero    = $row['numero'];
                        $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));;
                    }
                    $_SESSION["numero"]    = $numero;
                    $_SESSION["localidad"] = $localidad;
                    if ($result4) {
                        $q5      = "update session set numero = '$numero' where idusuario = $idusuario";
                        $result5 = mysqli_query($mysqli, $q5);
                        if ($result5) {
                            $q6      = "update numeros set flag = 'en uso' where numero = '$numero'";
                            $result6 = mysqli_query($mysqli, $q6);
                            if ($result6) {
                                $response = array('result' => 'NumeroDespuesListaNegra', 'numero' => $numero, 'message' => 'Correcto.', 'localidad' => $localidad);
                            }
                        }
                    }
                } else {
                    $q7      = "update session set numero = '' where idusuario = $idusuario";
                    $result7 = mysqli_query($mysqli, $q7);
                }
            }
        }
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);
