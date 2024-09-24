<?php
session_start();
require_once "../_conexion.php";

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION['idusuario'])) {
    /**
     * Code - Estado
     * 0 - Figura en padrón
     * 1 - No es socio ni esta en baja (ALTA)
     * 2 - Clearing
     * 3 - Todos los productos con cualquier medio de pago
     * 4 - Solo productos acotados con culaquier medio de pago
     * 5 - Solo productos actotados SOLO con tarjeta de crédito
     */
    $code = $_POST["code"];
    $localidad = $_POST["localidad"];
    $servicios = [];
    $esGrupo = isset($_POST["esGrupo"]) ? $_POST["esGrupo"] : false; //seba

    $where = ($localidad == '20' || $localidad == '23') ? " AND id <> 3" : "";

    if ($code == 2) {
        $response["message"] = "Persona no autorizada, consultar con Comercial";
    } else {
        if ($code == 4 || $code == 5) {
            $where .= ' AND id IN (22, 25, 27, 30, 31, 32, 35, 36, 37, 38, 39, 40, 41, 107, 109, 110, 111, 114, 133, 134, 140)';
        }

        $where .= ' AND id NOT IN (117, 118, 119, 120, 121, 122, 123, 124, 135, 138, 142)'; //newproducts

        if ($esGrupo) { //seba
            $where .= ' AND id NOT IN (130, 133, 134, 136, 139)';
        }

        $qServicios = "SELECT * FROM servicios WHERE mostrar = 1 $where";

        if ($result = mysqli_query($mysqli, $qServicios)) {

            while ($row = mysqli_fetch_array($result)) {
                $id = $row['id'];
                $nroServicio = $row['nro_servicio'];
                $servicio = $row['nombre_servicio'];

                $servicios[] = array(
                    'id' => $id,
                    'nro_servicio' => $nroServicio,
                    'servicio' => $servicio
                );
            }
        }
    }
    $response = array('result' => true, 'message' => 'Exito', 'servicios' => $servicios);
} else {
    $response = array('result' => false, 'message' => 'Sin sesion');
}

mysqli_close($mysqli);
echo json_encode($response);
