<?php

include '../_conexion.php';
session_start();
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!', 'fecha_mal' => false);

if (isset($_SESSION['idusuario'])) {
    $idusuario    = $_SESSION['idusuario'];
    $numero       = $_SESSION['numero'];
    $fecha        = $_POST['fecha'];
    $obs          = $_POST['observacion'];
    $num          = $_POST['num'];
    $fecha_actual = date('Y-m-d H:i:s');
    $id           = $_SESSION['idusuario'];

    $query = "INSERT INTO historico (numero,estado,idusuario,fecha) VALUES ($num,'agendado',$id,'$fecha_actual')";

    if (mysqli_query($mysqli, $query)) {
        $result = mysqli_query($mysqli, "SELECT comentario FROM agendados WHERE numero=$num");

        $comentario = mysqli_fetch_array($result)[0];

        $comentario .= '\n' . $obs;

        $query2 = "UPDATE agendados SET fecha_agendado='$fecha',comentario='$comentario',fecha='$fecha_actual' WHERE numero='$num'";

        if (mysqli_query($mysqli, $query2)) {
            #-----------------------------------------------------------------------------
            $grupoUsuario = $_SESSION["grupoUsuario"];
            $qGrupo       = "select * from relacion where idgrupousuarios=$grupoUsuario ORDER BY RAND()";
            $resultGrupo  = mysqli_query($mysqli, $qGrupo);
            $cantGrupos   = mysqli_num_rows($resultGrupo);
            while ($reg = mysqli_fetch_array($resultGrupo)) {
                $grupos[] = $reg['idgrupotel'];
            }
            $resetNoContesta = "update numeros set no_contesta = 0 where numero = '$numero'";
            $resultReset     = mysqli_query($mysqli, $resetNoContesta);
            if ($resultGrupo) {
                if ($cantGrupos > 0) {
                    for ($i = 0; $i < count($grupos); $i++) {
                        $q6 = "select * from numeros where grupo = '$grupos[$i]' and flag = 'libre' order by rand() LIMIT 1";

                        // $q6 = "select n.numero, n.flag, n.dep_localidad, h.fecha from numeros as n
                        // left join historico as h
                        // on n.numero =h.numero
                        // where (DATEDIFF( now( ), h.fecha ) > 30 or ISNULL(h.id)) and n.grupo = '$grupos[$i]' and n.flag = 'libre'
                        // order by rand(),fecha desc limit 1";
                        $result6  = mysqli_query($mysqli, $q6);
                        $devuelve = mysqli_num_rows($result6);
                        if ($devuelve > 0) {
                            $i = count($grupos);
                        }
                    }
                } else {
                    $grupos = 'sin grupo';
                }
            }
            if ($devuelve > 0) {
                while ($row = mysqli_fetch_assoc($result6)) {
                    $numero    = $row['numero'];
                    $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));;
                }
                $_SESSION["numero"]    = $numero;
                $_SESSION["localidad"] = $localidad;
                if ($result6) {
                    $q7      = "update session set numero = '$numero' where idusuario = $idusuario";
                    $result7 = mysqli_query($mysqli, $q7);
                    if ($result7) {
                        $q8      = "update numeros set flag = 'en uso' where numero = '$numero'";
                        $result8 = mysqli_query($mysqli, $q8);
                        if ($result8) {
                            $response = array('result' => true, 'numero' => $numero, 'message' => 'Se ha reagendado correctamemte', 'localidad' => $localidad);
                        }
                    }
                }
            } else {
                $q8      = "update session set numero = '' where idusuario = $idusuario";
                $result8 = mysqli_query($mysqli, $q8);
            }

            #----------------------------------------------------------------------------------------------------------
        } else {
            $response = array('result' => false, 'message' => 'Se ha producido un error');
        }
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);
