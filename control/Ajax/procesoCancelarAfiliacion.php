<?php
include "../../_conexion.php";
$response = array(
    "title"   => "Error",
    "message" => "Ocurrio un error, inteta nuevamente más tarde",
    "icon"    => "error",
);

if (isset($_POST["typeAdmin"])) {
    $idAfiliado = mysqli_real_escape_string($mysqli, $_POST["idAfiliado"]);
    $idUser = mysqli_real_escape_string($mysqli, $_POST["idUser"]);
    $fecha = date('Y-m-d');

    if ($result = mysqli_query($mysqli, "SELECT
        cedula, alta, numero_tarjeta
    FROM
        padron_datos_socio
    WHERE
        id=$idAfiliado")) {
        $row    = mysqli_fetch_assoc($result); //dir2
        $cedula = $row["cedula"];
        $alta   = $row['alta'];
        $tarjeta = $row['numero_tarjeta'];

        if ($alta == '1') {

            // Elimino de padron_producto_socio
            mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula = '$cedula'");

            // Elimino de padron_datos_socio
            mysqli_query($mysqli, "DELETE FROM padron_datos_socio WHERE cedula = '$cedula'");

            //Elimino la direccion de la tabla de direcciones_socios
            mysqli_query($mysqli, "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedula'");

            //elimino el registro de la tabla beneficiarios
            mysqli_query($mysqli, "DELETE FROM beneficiarios_servicios WHERE cedula_titular='$cedula' AND concretado = 0");

            mysqli_query($mysqli, "DELETE FROM comprobantes_competencia WHERE cedula = '$cedula'");

            //Elimino el afiliado omt o beneficiarios que tenga
            $qBen = "SELECT id,cedula from padron_producto_socio WHERE cedula_titular_gf = '$cedula'";
            if ($resultomt = mysqli_query($mysqli, $qBen)) {
                if (mysqli_num_rows($resultomt) > 0) {
                    while ($row = mysqli_fetch_assoc($resultomt)) {
                        $cedulaBen = $row['cedula'];
                        mysqli_query($mysqli, "DELETE FROM padron_datos_socio WHERE cedula = '$cedulaBen'");
                        mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula = '$cedulaBen'");
                        mysqli_query($mysqli, "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedula'");
                    }
                }
            }

            //PROMO VISA
            (require_once __DIR__ . '/__borrarPromoVisa.php')($tarjeta);
        } else {

            $qIdPiscina = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
            $rIdPiscina = mysqli_query($mysqli, $qIdPiscina);

            $idPiscina = ($rIdPiscina && mysqli_num_rows($rIdPiscina) > 0) ? mysqli_fetch_assoc($rIdPiscina)['id'] : false;
            $existeHistorico = false;

            if ($idPiscina != false) {
                $qExisteHistorico = "SELECT id FROM historico_venta WHERE id_cliente=$idPiscina AND id_estado=6";
                $rExisteHistorico = mysqli_query($mysqli,  $qExisteHistorico);
                $existeHistorico = ($rExisteHistorico && mysqli_num_rows($rExisteHistorico) > 0) ? true : false;
            }

            if ($existeHistorico) {

                // Actualizo padron productos
                mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion = '5' WHERE cedula = '$cedula'");

                //elimino los productos nuevos registrados

                $qServiciosNew = "SELECT id FROM padron_producto_socio WHERE cedula = '$cedula' AND abm='ALTA-PRODUCTO'";
                $rServNew = mysqli_query($mysqli, $qServiciosNew);
                if ($rServNew && mysqli_num_rows($rServNew) > 0) {

                    while ($row = mysqli_fetch_assoc($rServNew)) {
                        mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE id = " . $row['id']);
                    }
                }

                $qServiciosNew = "SELECT sum(importe) as total FROM padron_producto_socio WHERE cedula = '$cedula'";
                $rServNew = mysqli_query($mysqli, $qServiciosNew);
                if ($rServNew && mysqli_num_rows($rServNew) > 0) {
                    $totalImporte = mysqli_fetch_assoc($rServNew)['total'];
                }

                // Actualizo el estado y la accion
                mysqli_query($mysqli, "UPDATE padron_datos_socio SET estado = 6, accion = '5', total_importe='$totalImporte' WHERE cedula = '$cedula'");

                $qHistrocio = "INSERT INTO historico_venta VALUES(null,$idUser,$idPiscina,671,'$fecha','AFILIACION CANCELADA',11)";
                mysqli_query($mysqli, $qHistrocio);
            } else {
                // Elimino de padron_producto_socio
                mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula = '$cedula'");

                // Elimino de padron_datos_socio
                mysqli_query($mysqli, "DELETE FROM padron_datos_socio WHERE cedula = '$cedula'");

                //Elimino la direccion de la tabla de direcciones_socios
                mysqli_query($mysqli, "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedula'");

                //elimino el registro del historico
                mysqli_query($mysqli, "DELETE FROM historico_venta WHERE id_cliente=$idPiscina");
            }
        }

        $response = array(
            "title"   => "Exito",
            "message" => "Operación realizada con exito. El afiliado con la cédula $cedula fue eliminado del sistema.",
            "icon"    => "success",
        );
    }
}

mysqli_close($mysqli);
echo json_encode($response);
