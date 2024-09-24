<?php
require_once "../../_conexion.php";
require_once './functions.php';
$response = array("beneficiarios" => array(), 'result' => false);

if (isset($_POST["typeAdmin"])) {
    $data           = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $tipo_admin     = $data["typeAdmin"];
    $cedulaSocio    = $data['cedulaSocio'];
    

    if ($cedulaSocio) {

        $qBeneficiarios = "SELECT * FROM beneficiarios_servicios WHERE cedula_titular = '$cedulaSocio' AND concretado = 0";
        $result = mysqli_query($mysqli, $qBeneficiarios);

        while ($row = mysqli_fetch_assoc($result)) {

            $response['beneficiarios'][] = array(
                'id'              => $row['id'],
                'nombre'          => $row['nombre'],
                'cedula'          => $row['cedula'],
                'fechaNacimiento' => $row['fecha_nacimiento'],
                'telefono'        => $row['telefono'],
                'num_servicio'    => $row['num_servicio']
            );
        }
        
        $response['result'] = ($response['beneficiarios'] != null) ? true : false;
    }
}

mysqli_close($mysqli);
echo json_encode($response);
