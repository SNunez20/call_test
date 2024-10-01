<?php
include '../../../_conexion.php';


$tabla["data"] = [];


$obtener_pendientes = obtener_todos_pendientes();

while ($row = mysqli_fetch_assoc($obtener_pendientes)) {

    $id              = $row['id'];
    $cedula          = $row['cedula'];
    $nombre          = $row['nombre'];
    $telefono        = $row['telefono'];
    $id_vendedor     = $row['id_vendedor'];
    $nombre_vendedor = obtener_nombre_usuario($id_vendedor);
    //$envio_mail      = $row['envio_mail'];
    $solicitud       = $row['tipo'] == 1 ? "Call" : "Web";
    $fecha_registro  = $row['fecha_registro'];
    $acciones        = "<button class='btn btn-sm btn-success' onclick='registrar_crediv(true, `" . $id . "`)'>✅ Registrar</button>";



    $tabla["data"][] = [
        "id"              => $id,
        "cedula"          => $cedula,
        "nombre"          => $nombre,
        "telefono"        => $telefono,
        "nombre_vendedor" => $nombre_vendedor,
        //"envio_mail"     => $envio_mail == 1 ? "<span class='text-success'> Si </span>" : "<span class='text-danger'> No </span>",
        "solicitud"       => $solicitud,
        "fecha_registro"  => date("d/m/Y H:i:s", strtotime($fecha_registro)),
        "acciones"        => $acciones,
    ];
}



echo json_encode($tabla);







function obtener_todos_pendientes()
{
    global $mysqli;

    $sql = "SELECT 
    id,
    cedula,
    nombre,
    telefono,
    id_vendedor,
    envio_mail,
    fecha_registro,
    tipo
    FROM 
    crediv 
    WHERE 
    pendiente = 1 AND 
    activo = 1";

    $consulta = mysqli_query($mysqli, $sql);

    return $consulta;
}

function obtener_nombre_usuario($id_vendedor)
{
    global $mysqli;

    $sql = "SELECT 
    nombre
    FROM 
    usuarios 
    WHERE 
    id = '$id_vendedor'";

    $consulta = mysqli_query($mysqli, $sql);
    $resultado = $consulta != false ? mysqli_fetch_assoc($consulta)['nombre'] : "";

    return $resultado;
}
