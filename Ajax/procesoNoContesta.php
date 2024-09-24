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
    $fecha   = date("Y-m-d H:i:s");
    $chk     = '';
    $q2      = "insert into historico values(null,'$numero','no contesta',$idusuario,'$fecha','$chk')";
    $result2 = mysqli_query($mysqli, $q2);
    if ($result2) {
        $grupoUsuario = $_SESSION["grupoUsuario"];
        $qGrupo       = "select * from relacion where idgrupousuarios=$grupoUsuario ORDER BY RAND()";
        $resultGrupo  = mysqli_query($mysqli, $qGrupo);
        $cantGrupos   = mysqli_num_rows($resultGrupo);
        while ($reg = mysqli_fetch_array($resultGrupo)) {
            $grupos[] = $reg['idgrupotel'];
        }
        $qSumarNoContesta             = "update numeros set no_contesta = no_contesta + 1 where numero = '$numero'";
        $resultNoContesta             = mysqli_query($mysqli, $qSumarNoContesta);
        $qBorrarReferidoCuaderno      = "delete from referidoscuaderno where numero = '$numero'";
        $resultBorrarReferidoCuaderno = mysqli_query($mysqli, $qBorrarReferidoCuaderno);
        $qContador                    = "select no_contesta from numeros where numero = '$numero'";
        $resultContador               = mysqli_query($mysqli, $qContador);
        if ($resultContador) {
            while ($row = mysqli_fetch_assoc($resultContador)) {
                $no_contesta = $row['no_contesta'];
            }
        }
        if (isset($no_contesta)) { //NO CONTESTA SI ESTA EN AGENDADO O REFERIDO PERO TAMBIEN ESTA EN BASE DE TELEFONOS
            if ($no_contesta >= 1) {
                $qNoContesta          = "update numeros set flag = 'no contesta' where numero = '$numero'";
                $resultNoContesta     = mysqli_query($mysqli, $qNoContesta);
                $qBorrarAgendado      = "delete from agendados where numero = '$numero'";
                $resultBorrarAgendado = mysqli_query($mysqli, $qBorrarAgendado);
                $qBorrarReferido      = "delete from referidos where numero = '$numero'";
                $resultBorrarReferido = mysqli_query($mysqli, $qBorrarReferido);
            } else {
                $qLiberar      = "update numeros set flag = 'libre' where numero = '$numero' and flag != 'agendado' and flag != 'referido'";
                $resultLiberar = mysqli_query($mysqli, $qLiberar);
            }
        } else {
            $qContadorRef      = "select no_contesta from referidos where numero = '$numero'";
            $resultContadorRef = mysqli_query($mysqli, $qContadorRef);
            if ($resultContadorRef) {
                while ($row = mysqli_fetch_assoc($resultContadorRef)) {
                    $no_contestaRef = $row['no_contesta'];
                }
            }
            if (isset($no_contestaRef)) { //NO CONTESTA SI ESTA EN REFERIDO PERO NO EN BASE DE TELEFONO
                $qSumarNoContestaRef = "update referidos set no_contesta = no_contesta + 1 where numero = '$numero'";
                $resultNoContestaRef = mysqli_query($mysqli, $qSumarNoContestaRef);
                $qContadorRef        = "select no_contesta from referidos where numero = '$numero'";
                $resultContadorRef   = mysqli_query($mysqli, $qContadorRef);
                if ($resultContadorRef) {
                    while ($row = mysqli_fetch_assoc($resultContadorRef)) {
                        $no_contestaRef = $row['no_contesta'];
                    }
                }
                if ($no_contestaRef >= 2) {
                    $qBorrarAgendado      = "delete from agendados where numero = '$numero'";
                    $resultBorrarAgendado = mysqli_query($mysqli, $qBorrarAgendado);
                    $qBorrarReferido      = "delete from referidos where numero = '$numero'";
                    $resultBorrarReferido = mysqli_query($mysqli, $qBorrarReferido);
                }
            } else { //NO CONTESTA SI ESTA EN AGENDADO PERO NO EN BASE DE TELEFONO
                $qSumarNoContestaAge = "update agendados set no_contesta = no_contesta + 1 where numero = '$numero'";
                $resultNoContestaAge = mysqli_query($mysqli, $qSumarNoContestaAge);
                $qContadorAge        = "select no_contesta from agendados where numero = '$numero'";
                $resultContadorAge   = mysqli_query($mysqli, $qContadorAge);
                if ($resultContadorAge) {
                    while ($row = mysqli_fetch_assoc($resultContadorAge)) {
                        $no_contestaAge = $row['no_contesta'];
                    }
                }
                if ($no_contestaAge >= 2) {
                    $qBorrarAgendado      = "delete from agendados where numero = '$numero'";
                    $resultBorrarAgendado = mysqli_query($mysqli, $qBorrarAgendado);
                    $qBorrarReferido      = "delete from referidos where numero = '$numero'";
                    $resultBorrarReferido = mysqli_query($mysqli, $qBorrarReferido);
                }
            }
        }

        if ($resultGrupo) {
            if ($cantGrupos > 0) {
                for ($i = 0; $i < count($grupos); $i++) {
                    $q3 = "select * from numeros where grupo = '$grupos[$i]' and flag = 'libre' order by rand() LIMIT 1";

                    // $q3 = "select n.numero, n.flag,n.dep_localidad, h.fecha from numeros as n
                    // left join historico as h
                    // on n.numero =h.numero
                    // where (DATEDIFF( now( ), h.fecha ) > 30 or ISNULL(h.id)) and n.grupo = '$grupos[$i]' and n.flag = 'libre'
                    // order by rand(),fecha desc limit 1";
                    $result3  = mysqli_query($mysqli, $q3);
                    $devuelve = mysqli_num_rows($result3);
                    if ($devuelve > 0) {
                        $i = count($grupos);
                    }
                }
            } else {
                $grupos = 'sin grupo';
            }
        }
        if ($devuelve > 0) {
            while ($row = mysqli_fetch_assoc($result3)) {
                $numero    = $row['numero'];
                $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));;
            }
            $_SESSION["numero"]    = $numero;
            $_SESSION["localidad"] = $localidad;
            if ($result3) {
                $q4      = "update numeros set flag = 'en uso' where numero = '$numero'";
                $result4 = mysqli_query($mysqli, $q4);
                if ($result4) {
                    $q5      = "update session set numero = '$numero' where idusuario = $idusuario";
                    $result5 = mysqli_query($mysqli, $q5);
                    if ($result5) {
                        $response = array('result' => 'NumeroDespuesNoContesta', 'numero' => $numero, 'message' => 'Correcto.', 'localidad' => $localidad);
                    }
                }
            }
        } else {
            $q6      = "update session set numero = '' where idusuario = $idusuario";
            $result6 = mysqli_query($mysqli, $q6);
        }
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);
