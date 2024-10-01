<?php

include_once './_init.php';

$tabla['data'] = [];

if (isset($_REQUEST['estado'])) {
    $estado = $_REQUEST['estado'];
    if ($estado == 'todos') $estado_query = "WHERE estado <>999 AND estado <>998";
    else {
        $estado = (int)$estado;
        $estado_query =  " WHERE estado = $estado";
    }
} else $estado_query = "";

$buscar_fecha=fechaDesdeHasta();

$query = "SELECT id,nombre,cedula,edad,estado,id_usuario FROM padron_datos_socio_piscina  {$estado_query} {$buscar_fecha} ORDER BY id";

$query = mysqli_query($mysqli, $query);

if (mysqli_num_rows($query) > 0) {
    while ($socio = mysqli_fetch_assoc($query)) {
        $cedula = $socio['cedula'];

        $btn_servicios = "<button class='btn btn-primary' onclick='verServicios(`{$cedula}`);'>Ver</button>";

        $estado = estado((int)$socio['estado']);

        $id_vendedor = (int)$socio['id_usuario'];

        $query_vendedor = mysqli_query($mysqli, "SELECT nombre,cedula FROM vendedores WHERE id=$id_vendedor AND activo=1");

        if (mysqli_num_rows($query_vendedor) > 0) {
            $fetch_vendedor = mysqli_fetch_assoc($query_vendedor);
            $ci_vendedor = $fetch_vendedor['cedula'];
            $nombre = $fetch_vendedor['nombre'];
            $vendedor = "{$nombre} ({$ci_vendedor})";
        } else $vendedor = '';

        $tabla['data'][] = [
            'id' => $socio['id'],
            'cedula' => $cedula,
            'nombre' => $socio['nombre'],
            'edad' => (int)$socio['edad'],
            'estado' => $estado,
            'servicios' => $btn_servicios,
            'vendedor' => $vendedor
        ];
    }
}

die(json_encode($tabla));
