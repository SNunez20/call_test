<?php
require_once "../../_conexion.php";

$response = array(
    "result"  => false,
    "session" => false,
    "data"    => [],
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $cedula              = $_POST['cedula'];
    $query               = "SELECT ps.cedula, ps.nombre, ps.tel, ps.fecha_nacimiento
                            FROM padron_datos_socio ps, padron_producto_socio pp
                            WHERE pp.cedula_titular_gf='$cedula'AND ps.cedula = pp.cedula
                            GROUP BY pp.cedula";
    if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response["result"] = true;
            $response["data"][] = array(
                $row["cedula"],
                $row["nombre"],
                $row["tel"],
                $row["fecha_nacimiento"],
            );
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
