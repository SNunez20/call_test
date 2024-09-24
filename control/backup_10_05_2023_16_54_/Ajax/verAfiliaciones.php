<?php
require_once "../../_conexion.php";
$response = array("data" => []);

if (isset($_POST["typeAdmin"])) {
    $data = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);

    $query = "SELECT ps.nombre, ps.cedula, e.estado, r.rechazo AS motivo, h.observacion ,h.fecha
              FROM historico_venta h
              INNER JOIN padron_datos_socio ps ON h.id_cliente = ps.id
              INNER JOIN rechazos r ON r.id = h.id_rechazo
              INNER JOIN estados e ON e.id = h.id_estado
              WHERE h.id_estado=6 ORDER BY h.fecha DESC";

    if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response["data"][] = array(
                $row["nombre"],
                $row["cedula"],
                $row['estado'],
                $row["observacion"],
                date('d-m-Y H:i', strtotime($row["fecha"])),
            );
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
