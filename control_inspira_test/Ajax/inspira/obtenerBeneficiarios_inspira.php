<?php
$response['result'] = false;
$response['session'] = false;
$response['data'] = [];


if (isset($_POST["typeAdmin"])) {
    $response['session'] = true;
    $cedula = $_POST['cedula'];

    $result = obtener_datos_padron($cedula);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response["result"] = true;
            $response["data"][] = [
                $row["cedula"],
                $row["nombre"],
                $row["tel"],
                $row["fecha_nacimiento"],
            ];
        }
    }
}



echo json_encode($response);




function obtener_datos_padron($cedula)
{
    require "../../../_conexion.php";

    $sql = "SELECT 
             ps.cedula, 
             ps.nombre,
             ps.tel, 
             ps.fecha_nacimiento
            FROM 
             padron_datos_socio ps 
             INNER JOIN padron_producto_socio pp ON ps.cedula = pp.cedula
            WHERE 
             pp.cedula_titular_gf = '$cedula'
             GROUP BY pp.cedula";
    $consulta = mysqli_query($mysqli, $sql);

    return $consulta;
}
