<?php

include_once './_init.php';

$tabla['data'] = [];

$query = "SELECT * FROM vendedores WHERE activo=1  GROUP BY id ORDER BY id";

$vendedores_query = mysqli_query($mysqli, $query);

if (mysqli_num_rows($vendedores_query) > 0) {

    if (isset($_REQUEST['estado'])) {
        $estado = $_REQUEST['estado'];
        if ($estado == 'todos') $estado_query = "AND estado <>999 AND estado <>998";
        else {
            $estado = (int)$estado;
            $estado_query =  " AND estado = $estado";
        }
    } else $estado_query = "";

    while ($vendedor = mysqli_fetch_assoc($vendedores_query)) {
        $id = (int) $vendedor['id'];
        $cedula = $vendedor['cedula'];
        $btn_ventas = "<button class='btn btn-success' onclick='verVentas(`{$cedula}`,`{$id}`,true);abrirModal(`modal_ventas_vendedor`);' >Ver</button>";

        $buscar_fecha = fechaDesdeHasta();

        $query_cantidad = "SELECT id FROM padron_datos_socio_piscina WHERE id_usuario=$id {$estado_query} {$buscar_fecha}  ORDER BY id";

        $query_cantidad = mysqli_query($mysqli, $query_cantidad);

        $cantidad = (int)mysqli_num_rows($query_cantidad);

        $id_supervisor=$vendedor['id_supervisor'];

        $query_supervisor = mysqli_query($mysqli, "SELECT nombre FROM supervisores WHERE id=$id_supervisor  LIMIT 1");

        if (mysqli_num_rows($query_supervisor) > 0) $supervisor = mysqli_fetch_assoc($query_supervisor)['nombre'];

        else $supervisor = '';

        $tabla['data'][] = [
            'id' => $id,
            'nombre' => $vendedor['nombre'],
            'cedula' => $cedula,
            'ventas' => $btn_ventas,
            'cantidad' => $cantidad,
            'supervisor' => $supervisor
        ];
    }
}
die(json_encode($tabla));


/*
SELECT
	supervisores.*,
	count( padron_datos_socio_piscina.cedula ) AS socios 
FROM
	vendedores,
	supervisores,
	padron_datos_socio_piscina 
WHERE
	vendedores.activo = 1 
	AND supervisores.id = vendedores.id_supervisor 
	AND padron_datos_socio_piscina.id_usuario = vendedores.id 
GROUP BY
	vendedores.id 
ORDER BY
	socios DESC

*/