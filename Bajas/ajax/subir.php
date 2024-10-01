<?php
$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha          = date("Y-m-d H:i:s");
$error          = false;
$borrar_archivo = false;
$cant_bajas     = 0;
$carpeta        = "../csv/";
include '../../_conexion.php';
$ultimo_id = 1;

if (!file_exists($carpeta)) {
    mkdir($carpeta);
}
if (!isset($_FILES['file-0']['tmp_name'])) {
    $error      = true;
    $error_cual = "No se encontro ningun archivo";
} else {
    $archivo = "../csv/" . $_FILES['file-0']['name'];

    if (file_exists($archivo)) {
        $error      = true;
        $error_cual = "Ese archivo ya fue subido";
    } else {
        if (move_uploaded_file($_FILES["file-0"]["tmp_name"], $archivo)) {
            $q = "select id from bajas ORDER BY id desc limit 1";
            $result = mysqli_query($mysqli, $q);
            while ($row = mysqli_fetch_assoc($result)) {
                $ultimo_id = $row['id'];
            }
            $fp = fopen($archivo, "a+");
            while ($row = fgetcsv($fp, "1024", ";")) {
                // fix
                $q2 = "INSERT INTO bajas (cedula,tipo_producto,fecha_baja,clearing,`count`) VALUES('" . implode("','", $row) . "')";

                $result2 = mysqli_query($mysqli, $q2);

                if (!$result2) {
                    $error          = true;
                    $borrar_archivo = true;
                    $error_cual     = "No se pudieron agregar los datos a la base de datos";
                    break;
                } else {
                    $cant_bajas     = $cant_bajas + 1;
                    $borrar_archivo = true;
                }
            }
            fclose($fp);
        } else {
            $error      = true;
            $error_cual = "No se pudo subir el csv al servidor";
        }
    }
}

if (!$error) {
    $nombre_archivo = $_FILES['file-0']['name'];
    $q5             = "insert into datos_extra_bajas values(null,$cant_bajas,'$nombre_archivo','$fecha')";
    $result5        = mysqli_query($mysqli, $q5);
    if ($result5) {
        $fecha_subido = date("d/m/Y", strtotime($fecha));
        $response     = array('result' => true, 'message' => 'Datos agregados correctamente!', 'fecha_subido' => $fecha_subido, 'registros_subidos' => $cant_bajas);
    }

    if ($borrar_archivo) {
        // unlink("C:/wamp64/www/call/Bajas/csv/".$_FILES['file-0']['name']);
        $q3             = "delete from bajas where id <= $ultimo_id";
        $result3        = mysqli_query($mysqli, $q3);
        $auto_increment = $ultimo_id + 1;
        $q4             = "alter table bajas AUTO_INCREMENT = $auto_increment";
        $result4        = mysqli_query($mysqli, $q4);
        if (!$result3) {
            $error_cual .= " - No se pudieron eliminar los registros recien agregados.";
        }
        if (!$result4) {
            $error_cual .= " - No se pudo setear el auto_increment.";
        }
    }
} else {
    if ($borrar_archivo) {
        unlink("C:/wamp64/www/call/Bajas/csv/" . $_FILES['file-0']['name']);
        $q3             = "delete from bajas where id <= $ultimo_id";
        $result3        = mysqli_query($mysqli, $q3);
        $auto_increment = $ultimo_id + 1;
        $q4             = "alter table bajas AUTO_INCREMENT = $auto_increment";
        $result4        = mysqli_query($mysqli, $q4);
        if (!$result3) {
            $error_cual .= " - No se pudieron eliminar los registros recien agregados.";
        }
        if (!$result4) {
            $error_cual .= " - No se pudo setear el auto_increment.";
        }
    }
    $response = array('result' => false, 'message' => 'Hubo un error: ' . $error_cual);
}
mysqli_close($mysqli);
echo json_encode($response);
