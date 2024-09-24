<?php
session_start();
require_once "../../_conexion250.php";
require_once "../../_conexion.php";
$response = array(
    "result"  => false,
    "session" => false,
);

if (isset($_POST['typeAdmin'])) {
    $response["session"] = true;
    $data = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli250, $data)), $_POST);
    $cedula = $data['cedula'];

    $qDeleteDatos = "DELETE FROM padron_datos_socio WHERE cedula = $cedula";
    $qDeleteProductos = "DELETE FROM padron_producto_socio WHERE cedula = $cedula";
    $qDeleteDir = "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedula'";
    $rDatos = mysqli_query($mysqli250, $qDeleteDatos);
    $rProductos =  mysqli_query($mysqli250, $qDeleteProductos);
    $rDir =  mysqli_query($mysqli250, $qDeleteDir);
    // si tiene beneficiarios de servicios externos
    $qDelSB = "DELETE FROM beneficiarios_servicios WHERE cedula_titular='$cedula'";
    mysqli_query($mysqli250,$qDelSB); //newform
    if ($rDatos && $rProductos) {

        $response["result"]  = true;
        $response["sesion"]  = true;
        $response["message"] = 'Datos eliminados correctamente';

        #buscamos el id de ese socio en piscina
        $qIdPiscina = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
        $rId= mysqli_query($mysqli, $qIdPiscina);
        if ($rId && mysqli_num_rows($rId)>0) {
            $idPiscina = mysqli_fetch_assoc($rId)['id'];

            #buscamos el id del ultimo historico
            $qIdHistorico = "SELECT id FROM historico_reportes WHERE id_cliente = $idPiscina ORDER BY id DESC LIMIT 1";
            $rIdHistorico = mysqli_query($mysqli,$qIdHistorico);
                if ($rIdHistorico && mysqli_num_rows($rIdHistorico)>0) {
                    $idHistorico = mysqli_fetch_assoc($rIdHistorico)['id'];

                    $qEliminar = "DELETE FROM historico_reportes WHERE id = $idHistorico";
                    mysqli_query($mysqli, $qEliminar);
                    $qEliminar = "DELETE FROM historico_reportes_servicios WHERE id_historico_reporte = $idHistorico";
                    mysqli_query($mysqli, $qEliminar);

                }
        }

    }else {
        $response["result"]  = false;
        $response["sesion"]  = true;
        $response["message"] = 'Ha ocurrido un error';
    }
}

mysqli_close($mysqli250);
mysqli_close($mysqli);
echo json_encode($response);
