<?php
require_once "../../_conexion.php";
$response = array("data" => []);

if (isset($_POST["typeAdmin"])) {
    $data  = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $query = "SELECT a.nombre AS nombre_usuario, ps.nombre, ps.cedula, e.estado, r.rechazo AS motivo, h.observacion ,h.fecha
            FROM padron_datos_socio ps
              INNER JOIN  historico_venta h ON h.id_cliente = ps.id
              INNER JOIN admin a ON a.id = h.id_usuario
              INNER JOIN rechazos r ON r.id = h.id_rechazo
              INNER JOIN estados e ON e.id = h.id_estado
            WHERE ps.id =" . $data["id"];

    if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response["data"][] = array(
                $row["nombre_usuario"],
                $row["nombre"],
                $row["cedula"],
                $row['estado'],
                $row["motivo"],
                $row["observacion"],
                date('d-m-Y H:i', strtotime($row["fecha"])),
            );
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
