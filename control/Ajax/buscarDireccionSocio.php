<?php
require_once "../../_conexion.php";
require_once './functions.php';
$response = array("direccion" => array(), 'result' => false);

if (isset($_POST["typeAdmin"])) {
    $data           = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $tipo_admin     = $data["typeAdmin"];
    $idSocio        = $data['idSocio'];
    

    if ($idSocio) {

        $qDireccion = "SELECT * FROM direcciones_socios WHERE id_socio = '$idSocio'";
        $result = mysqli_query($mysqli, $qDireccion);

        while ($row = mysqli_fetch_assoc($result)) {

            $response['direccion'] = array(
                'id'            => $row['id'],
                'calle'         => $row['calle'],
                'puerta'        => $row['puerta'],
                'manzana'       => $row['manzana'],
                'solar'         => $row['solar'],
                'esquina'       => $row['esquina'],
                'referencia'    => $row['referencia'],
                'apartamento'   => $row['apartamento'],
            );
        }
        
        $response['result'] = ($response['direccion'] != null) ? true : false;
    }
}

mysqli_close($mysqli);
echo json_encode($response);
