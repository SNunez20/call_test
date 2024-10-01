<?php
require_once __DIR__ . "/../../_conexion.php";

const GRUPOS = [
    1, 2, 4, 6, 7, 8, 9, 12, 14,
    15, 16, 17, 18, 19, 20, 23,
    24, 25, 26, 28, 31, 1282,
    1283, 1296, 1298, 1299, 1300,
    1301, 1302, 1303, 1304, 1305,
    1306, 1307, 1314, 6667, 6668, 6669, 6673, 6674,
    6675, 6676, 6677, 6678, 6680, 6681, 6682, 6684,
    6685, 6686, 10000, 10001, 10004, 10005, 10006,
    10009, 10013, 10015, 10017, 10018, 10019, 10020, 10023, 10024, 10025, 10026, 10027, 10029, 10030
];

const EXCLUIDOS = ['11485314', '848517', '12123192', '13673928', '18332148', '62489781', '65581158', '65651850', '31395246', '65971250', 'pablo', '23292923', '70346777', '225372', '729674', '27638155']; //DOCUMENTOS EXTRANJEROS, POR EJ PASAPORTES RECORTADOS PARA EVITAR QUE LOS BORRE
$fecha = date("Y-m-d");
$content = "********************" . $fecha . "***********************\n";
$content .= "Vendedores eliminados:\n";

$q = "SELECT u.id, u.usuario, u.nombre, g.nombre AS grupo FROM gruposusuarios as g INNER JOIN usuarios as u on u.idgrupo = g.id WHERE g.id IN(" . implode(',', GRUPOS) . ") AND (u.activo = 1 OR u.activo = 0) AND u.usuario NOT IN ('" . implode("','", EXCLUIDOS) . "')";
$result = mysqli_query($mysqli, $q);
$serverName        = '192.168.252.12\NODUMBD,1433';
$connectionInfo    = array(
    'Database'        => (string)    'Nodum',
    'UID'            => (string)    'consultas',
    'PWD'            => (string)    'consultas.2k19',
    'Characterset'    => (string)    'UTF-8'
);

$mssql = sqlsrv_connect($serverName, $connectionInfo);

if (!$mssql) {
    echo "No se pudo establecer conexion.<br />";
    die(print_r(sqlsrv_errors(), true));
}

$total = 0;
$total_inactivo = 0;
$total_fallidos = 0;
$fallidos = "";

while ($row = mysqli_fetch_array($result)) {
    $total++;
    $cedula_vendedor = $row['usuario'];
    $id_usuario = $row['id'];
    $nombre_vendedor = $row['nombre'];
    $grupo = $row['grupo'];
    $qNod = "SELECT doc_persona FROM v_RHTrabajador WHERE estado_actual_tr = 'ACTIVO' AND doc_persona = (?)";
    $params = array($cedula_vendedor);
    $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $resultNod = sqlsrv_query($mssql, $qNod, $params, $options);
    $activo = sqlsrv_num_rows($resultNod);

    if ($activo == 0) {
        $resultado_baja = bajarVendedor($id_usuario, $mysqli);


        if ($resultado_baja) {
            $content .= $id_usuario . " " . $cedula_vendedor . " " . $grupo . "\n";
        } else {
            $total_fallidos++;
            $fallidos .= " " . $cedula_vendedor . " ";
        }

        $total_inactivo++;
    }
}
$content .= "\n\n";
$content .= "TOTAL: " . $total . "\n";
$content .= "INACTIVOS: " . $total_inactivo . "\n";
$content .= "FALLIDOS: " . $total_fallidos . " (" . $fallidos . ")\n";
$content .= "********************FIN***********************\n\n\n";
file_put_contents("C:/wamp64/www/call/Admin/eliminar_vendedores_con_nodum/log.txt", $content, FILE_APPEND | LOCK_EX);

function bajarVendedor($usuario, $mysqli)
{
    $q = "UPDATE usuarios SET activo = 2 WHERE id = $usuario";
    $result = mysqli_query($mysqli, $q);

    if (!$result) {
        return false;
    }

    $q2 = "UPDATE numeros AS n INNER JOIN agendados AS a ON n.numero = a.numero SET n.flag = 'libre', n.no_contesta = 0 WHERE a.usuarioid = $usuario";
    $result2 = mysqli_query($mysqli, $q2);

    if (!$result2) {
        return false;
    }

    $q3 = "UPDATE numeros AS n INNER JOIN referidos AS r ON n.numero = r.numero SET n.flag = 'libre', n.no_contesta = 0 WHERE r.usuarioid = $usuario";
    $result3 = mysqli_query($mysqli, $q3);

    if (!$result3) {
        return false;
    }

    $q4 = "DELETE FROM agendados WHERE usuarioid = $usuario";
    $result4 = mysqli_query($mysqli, $q4);

    if (!$result4) {
        return false;
    }

    $q5 = "DELETE FROM referidos WHERE usuarioid = $usuario";
    $result5 = mysqli_query($mysqli, $q5);

    if (!$result5) {
        return false;
    }

    $q6 = "UPDATE numeros INNER JOIN `session` ON numeros.numero = `session`.numero SET numeros.flag = 'libre' WHERE `session`.idusuario = $usuario";
    $result6 = mysqli_query($mysqli, $q6);

    if (!$result6) {
        return false;
    }

    $q7 = "DELETE FROM `session` WHERE idusuario = $usuario";
    $result7 = mysqli_query($mysqli, $q7);

    if (!$result7) {
        return false;
    }

    $q8 = "UPDATE numeros AS n INNER JOIN referidoscuaderno AS r ON n.numero = r.numero SET n.flag = 'libre', n.no_contesta = 0 WHERE r.idusuario = $usuario";
    $result8 = mysqli_query($mysqli, $q8);

    if (!$result8) {
        return false;
    }

    $q9 = "DELETE FROM referidoscuaderno WHERE idusuario = $usuario";
    $result9 = mysqli_query($mysqli, $q9);

    if (!$result9) {
        return false;
    }

    return true;
}

mysqli_close($mysqli);
sqlsrv_close($mssql);
