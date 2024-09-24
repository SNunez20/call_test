<?php
require_once "../../_conexion250.php";
$response = array("data" => []);

if (isset($_POST["typeAdmin"])) {
    $data       = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli250, $data)), $_POST);
    $tipo_admin = $data["typeAdmin"];
    $cedula     = $data['cedula'];

    $query = "SELECT  id, servicio, hora, importe, fecha_afiliacion,fecha_registro, cod_promo, movimiento, observaciones, numero_vendedor,keepprice1
    FROM padron_producto_socio WHERE cedula = $cedula ORDER BY id ASC";

    if ($result = mysqli_query($mysqli250, $query)) {
        $first = true;
        while ($row = mysqli_fetch_assoc($result)) {

            $button1            = "<button class='text-uppercase btn btn-primary text-uppercase' onclick='event.preventDefault(); verEditarServicios(" . $row['id'] . ");'>Editar</button>";
            $button2            = ($first) ? '-' : "<button class='text-uppercase btn btn-danger text-uppercase' onclick='event.preventDefault(); eliminarServicioSocio(" . $row['id'] . ");'>Eliminar</button>";
            $response["data"][] = array(
                $row["servicio"],
                $row["hora"],
                $row["importe"],
                $row["cod_promo"],
                $row["fecha_afiliacion"],
                $row['fecha_registro'],
                $row["movimiento"],
                $row["observaciones"],
                $row["numero_vendedor"],
                $button1,
                $button2,
                $row['id'],
                $row['keepprice1']
            );
            $first = false;
        }
    } else {
        $response['message'] = 'No se encontraron datos';
    }
}

mysqli_close($mysqli250);
echo json_encode($response);
