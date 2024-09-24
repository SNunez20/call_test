<?php
include '../_conexion.php';
session_start();
/**if ($_SESSION['idusuario'] == 2598) {
mysqli_set_charset($mysqli, 'utf8mb4');
}*/
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {
    $idusuario    = $_SESSION['idusuario'];
    $data         = array_map('stripslashes', $_POST);
    $grupoUsuario = $_SESSION["grupoUsuario"];
    if ($grupoUsuario == 27) {
        mysqli_set_charset($mysqli, 'utf8mb4');
    }
    $nombreUsuario = $_SESSION["nombreUsuario"];
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha    = date("Y-m-d H:i:s");
    $fecha2   = date("Y-m-d");
    $sql      = "select nombre from gruposusuarios where id = $grupoUsuario";
    $consulta = mysqli_query($mysqli, $sql);
    while ($row = mysqli_fetch_assoc($consulta)) {
        $nombreGrupo = $row['nombre'];
    }

    $q        = "select * from session where idusuario=$idusuario";
    $result   = mysqli_query($mysqli, $q);
    $devuelve = mysqli_num_rows($result);
    while ($row = mysqli_fetch_assoc($result)) {
        $numero = $row['numero'];
    }

    if ($devuelve > 0 && $numero != "") {
        $_SESSION["numero"] = $numero;
        $qLocalidad         = "select dep_localidad,flag from numeros where numero = '$numero'";
        $resultLocalidad    = mysqli_query($mysqli, $qLocalidad);
        while ($row = mysqli_fetch_assoc($resultLocalidad)) {
            $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));
            $flag      = $row['flag'];
        }

        $devuelveLocalidad = mysqli_num_rows($resultLocalidad);
        if ($devuelveLocalidad == 0) {
            $localidad = "Localidad Desconocida";
        }
        $_SESSION["localidad"] = $localidad;
        $query1                = "update session set ultimo_inicio = '$fecha' where idusuario = $idusuario";
        $r                     = mysqli_query($mysqli, $query1);
        $q8                    = "select * from agendados where date(fecha_agendado) = '$fecha2' && usuarioid = $idusuario";
        $result8               = mysqli_query($mysqli, $q8);
        $devuelve6             = mysqli_num_rows($result8);

        if ($devuelve6 > 0) {
            $response = array('result' => 'NumeroSession', 'numero' => $numero, 'flag' => $flag, 'message' => 'Correcto.', 'idgrupo' => $grupoUsuario, 'grupo' => $nombreGrupo, 'nombre' => $nombreUsuario, 'agendado' => true, 'localidad' => $localidad);
        } else {
            if ($_SESSION['idusuario'] == 2598) {
                //     die($_SESSION["numero"]);
            }
            $response = array('result' => 'NumeroSession', 'numero' => $numero, 'flag' => $flag, 'message' => 'Correcto.', 'idgrupo' => $grupoUsuario, 'grupo' => $nombreGrupo, 'nombre' => $nombreUsuario, 'agendado' => false, 'localidad' => $localidad);
        }
    } else {
        $devuelve3 = 0;


        #busca un numero que pertenezca a un grupo del usuario
        $qGrupo = "select * from relacion where idgrupousuarios=$grupoUsuario ORDER BY RAND()";

        $resultGrupo = mysqli_query($mysqli, $qGrupo);
        $cantGrupos  = mysqli_num_rows($resultGrupo);
        while ($reg = mysqli_fetch_array($resultGrupo)) {
            $grupos[] = $reg['idgrupotel'];
        }

        if ($resultGrupo) {
            if ($cantGrupos > 0) {
                for ($i = 0; $i < count($grupos); $i++) {

                    #MODIFICAR ESTA CONSULTA HACIENDO JOIN CON HISTORIAL
                    $q3 = "select * from numeros where grupo = '$grupos[$i]' and flag = 'libre' order by rand() LIMIT 1";
                    #consulta mejorada
                    // $q3 = "select n.numero, n.flag,n.dep_localidad, h.fecha from numeros as n
                    //     left join historico as h
                    //     on n.numero =h.numero
                    //     where (DATEDIFF( now( ), h.fecha ) > 30 or ISNULL(h.id)) and n.grupo = '$grupos[$i]' and n.flag = 'libre'
                    //     order by rand(),fecha desc limit 1";

                    $result3   = mysqli_query($mysqli, $q3);
                    $devuelve3 = mysqli_num_rows($result3);
                    if ($devuelve3 > 0) {
                        $i = count($grupos);
                    }
                }
            } else {
                $grupos = 'sin grupo de tel asociado';
            }
        }

        if ($devuelve3 > 0) {
            while ($row = mysqli_fetch_assoc($result3)) {
                $numero    = $row['numero'];
                $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));
                $flag      = $row['flag'];
            }
            if ($result3) {
                $q4        = "select * from session where idusuario = $idusuario";
                $result4   = mysqli_query($mysqli, $q4);
                $devuelve4 = mysqli_num_rows($result4);
                if ($devuelve4 > 0) {
                    $q5      = "update session set numero = '$numero' where idusuario = $idusuario";
                    $result5 = mysqli_query($mysqli, $q5);
                } else {
                    $q5      = "insert into session values(null,$idusuario,'$numero','$fecha')";
                    $result5 = mysqli_query($mysqli, $q5);
                }
                if ($result5) {
                    $q6                    = "update numeros set flag='en uso' where numero = '$numero'";
                    $result6               = mysqli_query($mysqli, $q6);
                    $_SESSION["numero"]    = $numero;
                    $_SESSION["localidad"] = $localidad;
                    if ($result6) {
                        $q7        = "select * from agendados where date(fecha_agendado) = '$fecha2' && usuarioid = $idusuario";
                        $result7   = mysqli_query($mysqli, $q7);
                        $devuelve5 = mysqli_num_rows($result7);
                        if ($devuelve5 > 0) {
                            $response = array('result' => 'NumeroNuevo', 'numero' => $numero, 'message' => 'Correcto.', 'grupo' => $nombreGrupo, 'nombre' => $nombreUsuario, 'flag' => $flag, 'agendado' => true, 'localidad' => $localidad);
                        } else {
                            $response = array('result' => 'NumeroNuevo', 'numero' => $numero, 'message' => 'Correcto.', 'grupo' => $nombreGrupo, 'nombre' => $nombreUsuario, 'flag' => $flag, 'agendado' => false, 'localidad' => $localidad);
                        }
                    }
                }
            }
        } else {
            $q7        = "select * from agendados where date(fecha_agendado) = '$fecha2' && usuarioid = $idusuario";
            $result7   = mysqli_query($mysqli, $q7);
            $devuelve5 = mysqli_num_rows($result7);
            if ($devuelve5 > 0) {
                $agendado = true;
            } else {
                $agendado = false;
            }
            $response = array('result' => false, 'message' => 'Sin Numero', 'grupo' => $nombreGrupo, 'nombre' => $nombreUsuario, 'agendado' => $agendado);
        }
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);
