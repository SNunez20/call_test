<?php
include '../_conexion.php';
session_start();
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!', 'fecha_mal' => false);

if (isset($_SESSION['idusuario'])) {
    $idusuario            = $_SESSION['idusuario'];
    $data                 = array_map('stripslashes', $_POST);
    $numero               = $_SESSION["numero"];
    $nombre               = str_replace("'", "", $data['nom']);
    $fechaAgendado        = $data['fec_hor'];
    $fechaAgendadoSinhora = date("Y-m-d", strtotime($fechaAgendado));
    $comentario           = str_replace("'", "", $data['com']);
    $integrantes          = str_replace("'", "", $data['integrantesFamilia']);
    $direccion            = str_replace("'", "", $data['direccion']);

    $chk = '';
    //date_default_timezone_set('America/Montevideo');
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha        = date("Y-m-d H:i:s");
    $fechaSinHora = date("Y-m-d");
    $mesqueviene  = date('Y-m-d', strtotime('next month'));

    if ($fechaAgendadoSinhora <= $mesqueviene && $fechaAgendadoSinhora >= $fechaSinHora) {
        if (isset($data['chknts'])) {
            $chk = 'nts';
        }
        if (isset($data['chksv'])) {
            $chk = 'sv';
        }
        if (isset($data['chktosic'])) {
            $chk = 'tosic';
        }
        if (isset($data['chktosnic'])) {
            $chk = 'tosnic';
        }

        $q2          = "insert into historico values(null,'$numero','agendado',$idusuario,'$fecha','$chk')";
        $result2     = mysqli_query($mysqli, $q2);
        $idhistorico = mysqli_insert_id($mysqli);

        if (isset($data['chknts'])) {
            if (isset($data['ntsa'])) {
                $ntsa = 'si';
            } else {
                $ntsa = 'no';
            }

            if (isset($data['ntsb'])) {
                $ntsb = 'si';
            } else {
                $ntsb = 'no';
            }

            if (isset($data['ntsc'])) {
                $ntsc = 'si';
            } else {
                $ntsc = 'no';
            }

            if (isset($data['ntsd'])) {
                $ntsd = 'si';
            } else {
                $ntsd = 'no';
            }

            if (isset($data['ntse'])) {
                $ntse = 'si';
            } else {
                $ntse = 'no';
            }

            if (isset($data['ntsf'])) {
                $ntsf = 'si';
            } else {
                $ntsf = 'no';
            }

            if (isset($data['ntsg'])) {
                $ntsg = 'si';
            } else {
                $ntsg = 'no';
            }

            if (isset($data['ntsh'])) {
                $ntsh = 'si';
            } else {
                $ntsh = 'no';
            }

            if (isset($data['ntsi'])) {
                $ntsi = 'si';
            } else {
                $ntsi = 'no';
            }

            if (isset($data['ntsj'])) {
                $ntsj = 'si';
            } else {
                $ntsj = 'no';
            }

            if (isset($data['ntsk'])) {
                $ntsk = 'si';
            } else {
                $ntsk = 'no';
            }

            if (isset($data['ntsl'])) {
                $ntsl = 'si';
            } else {
                $ntsl = 'no';
            }

            if (isset($data['ntsm'])) {
                $ntsm = 'si';
            } else {
                $ntsm = 'no';
            }

            if (isset($data['ntsn'])) {
                $ntsn = 'si';
            } else {
                $ntsn = 'no';
            }
            $qnts      = "insert into pnts values(null,$idhistorico,'$ntsa','$ntsb','$ntsc','$ntsd','$ntse','$ntsf','$ntsg','$ntsh','$ntsi','$ntsj','$ntsk','$ntsl','$ntsm','$ntsn')";
            $resultnts = mysqli_query($mysqli, $qnts);
        }

        if (isset($data['chksv'])) {
            if (isset($data['sva'])) {
                $sva = 'si';
            } else {
                $sva = 'no';
            }

            if (isset($data['svb'])) {
                $svb = 'si';
            } else {
                $svb = 'no';
            }

            if (isset($data['svc'])) {
                $svc = 'si';
            } else {
                $svc = 'no';
            }

            if (isset($data['svd'])) {
                $svd = 'si';
            } else {
                $svd = 'no';
            }

            if (isset($data['sve'])) {
                $sve = 'si';
            } else {
                $sve = 'no';
            }

            if (isset($data['svf'])) {
                $svf = 'si';
            } else {
                $svf = 'no';
            }

            if (isset($data['svg'])) {
                $svg = 'si';
            } else {
                $svg = 'no';
            }

            $qsv      = "insert into psv values(null,$idhistorico,'$sva','$svb','$svc','$svd','$sve','$svf','$svg')";
            $resultsv = mysqli_query($mysqli, $qsv);
        }

        if (isset($data['chktosnic'])) {
            if (isset($data['tosnica'])) {
                $tosnica = 'si';
            } else {
                $tosnica = 'no';
            }

            if (isset($data['tosnicb'])) {
                $tosnicb = 'si';
            } else {
                $tosnicb = 'no';
            }

            if (isset($data['tosnicc'])) {
                $tosnicc = 'si';
            } else {
                $tosnicc = 'no';
            }

            if (isset($data['tosnicd'])) {
                $tosnicd = 'si';
            } else {
                $tosnicd = 'no';
            }

            if (isset($data['tosnice'])) {
                $tosnice = 'si';
            } else {
                $tosnice = 'no';
            }

            if (isset($data['tosnicf'])) {
                $tosnicf = 'si';
            } else {
                $tosnicf = 'no';
            }

            if (isset($data['tosnicg'])) {
                $tosnicg = 'si';
            } else {
                $tosnicg = 'no';
            }

            if (isset($data['tosnich'])) {
                $tosnich = 'si';
            } else {
                $tosnich = 'no';
            }

            if (isset($data['tosnici'])) {
                $tosnici = 'si';
            } else {
                $tosnici = 'no';
            }

            if (isset($data['tosnicj'])) {
                $tosnicj = 'si';
            } else {
                $tosnicj = 'no';
            }

            if (isset($data['tosnick'])) {
                $tosnick = 'si';
            } else {
                $tosnick = 'no';
            }

            if (isset($data['tosnicl'])) {
                $tosnicl = 'si';
            } else {
                $tosnicl = 'no';
            }

            if (isset($data['tosnicm'])) {
                $tosnicm = 'si';
            } else {
                $tosnicm = 'no';
            }

            if (isset($data['tosnicn'])) {
                $tosnicn = 'si';
            } else {
                $tosnicn = 'no';
            }
            $qtosnic      = "insert into ptosnic values(null,$idhistorico,'$tosnica','$tosnicb','$tosnicc','$tosnicd','$tosnice','$tosnicf','$tosnicg','$tosnich','$tosnici','$tosnicj','$tosnick','$tosnicl','$tosnicm','$tosnicn')";
            $resulttosnic = mysqli_query($mysqli, $qtosnic);
        }

        if (isset($data['chktosic'])) {
            if (isset($data['tosica'])) {
                $tosica = 'si';
            } else {
                $tosica = 'no';
            }

            if (isset($data['tosicb'])) {
                $tosicb = 'si';
            } else {
                $tosicb = 'no';
            }

            if (isset($data['tosicc'])) {
                $tosicc = 'si';
            } else {
                $tosicc = 'no';
            }

            if (isset($data['tosicd'])) {
                $tosicd = 'si';
            } else {
                $tosicd = 'no';
            }

            if (isset($data['tosice'])) {
                $tosice = 'si';
            } else {
                $tosice = 'no';
            }

            if (isset($data['tosicf'])) {
                $tosicf = 'si';
            } else {
                $tosicf = 'no';
            }

            if (isset($data['tosicg'])) {
                $tosicg = 'si';
            } else {
                $tosicg = 'no';
            }

            if (isset($data['tosich'])) {
                $tosich = 'si';
            } else {
                $tosich = 'no';
            }

            if (isset($data['tosici'])) {
                $tosici = 'si';
            } else {
                $tosici = 'no';
            }

            if (isset($data['tosicj'])) {
                $tosicj = 'si';
            } else {
                $tosicj = 'no';
            }

            if (isset($data['tosick'])) {
                $tosick = 'si';
            } else {
                $tosick = 'no';
            }

            if (isset($data['tosicl'])) {
                $tosicl = 'si';
            } else {
                $tosicl = 'no';
            }

            if (isset($data['tosicm'])) {
                $tosicm = 'si';
            } else {
                $tosicm = 'no';
            }

            if (isset($data['tosicn'])) {
                $tosicn = 'si';
            } else {
                $tosicn = 'no';
            }

            $qtosic      = "insert into ptosic values(null,$idhistorico,'$tosica','$tosicb','$tosicc','$tosicd','$tosice','$tosicf','$tosicg','$tosich','$tosici','$tosicj','$tosick','$tosicl','$tosicm','$tosicn')";
            $resulttosic = mysqli_query($mysqli, $qtosic);
        }

        if (isset($data['servicio'])) {
            $servicio = str_replace("'", "", $data['servicio']);
        } else {
            $servicio = "";
        }

        $observacion2 = str_replace("'", "", $data['observacion2']);

        $q      = "update numeros set flag = 'agendado' where numero = '$numero'";
        $result = mysqli_query($mysqli, $q);
        if ($result) {
            $qBorrarAgendado              = "delete from agendados where numero = '$numero'";
            $resultBorrarAgendado         = mysqli_query($mysqli, $qBorrarAgendado);
            $qBorrarReferido              = "delete from referidos where numero = '$numero'";
            $resultBorrarReferido         = mysqli_query($mysqli, $qBorrarReferido);
            $qBorrarReferidoCuaderno      = "delete from referidoscuaderno where numero = '$numero'";
            $resultBorrarReferidoCuaderno = mysqli_query($mysqli, $qBorrarReferidoCuaderno);
            $q3                           = "insert into detalles values(null,$idhistorico,'$integrantes','$direccion','$servicio','$observacion2')";
            $result3                      = mysqli_query($mysqli, $q3);
            if ($result3) {
                $q4      = "insert into agendados values(null,'$numero','$nombre','$fechaAgendado','$comentario',$idusuario,$idhistorico,'$fecha',0,'No')";
                $result4 = mysqli_query($mysqli, $q4);
                if ($result4) {
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
                                // where(DATEDIFF( now( ), h.fecha ) > 30 or ISNULL(h.id)) and n.grupo = '$grupos[$i]' and n.flag = 'libre'
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
                            $localidad = mb_convert_encoding($row['dep_localidad'], "ISO-8859-1", mb_detect_encoding($row['dep_localidad']));
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
                                    $response = array('result' => 'NumeroDespuesAgendado', 'numero' => $numero, 'message' => 'Correcto.', 'localidad' => $localidad);
                                }
                            }
                        }
                    } else {
                        $q8      = "update session set numero = '' where idusuario = $idusuario";
                        $result8 = mysqli_query($mysqli, $q8);
                    }
                }
            }
        }
    } else {
        $response = array('result' => false, 'message' => 'Error Fecha', 'fecha_mal' => true, 'fechaAgendadoSinhora' => $fechaAgendadoSinhora, 'mesqueviene' => $mesqueviene, 'fechaSinHora' => $fechaSinHora);
    }
} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}
mysqli_close($mysqli);
echo json_encode($response);
