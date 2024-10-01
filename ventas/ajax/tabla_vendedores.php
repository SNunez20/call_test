<?php

include_once './_init.php';

$tabla['data'] = [];

$supervisor_arrays_implode = implode(" OR admin.id = ", $supervisores_arrays_id);

$vendedores_arrays_implode = implode(" AND usuarios.id<> ", $vendedores_arrays_id);

$query =    "SELECT
                usuarios.*,
                admin.nombre as supervisor
            FROM usuarios,admin 
            WHERE 
                usuarios.activo=1
                AND admin.activo=1  
                AND admin.grupo_usuarios=usuarios.idgrupo
                AND ({$vendedores_arrays_implode})
                AND ({$supervisor_arrays_implode})
                
            GROUP BY
                usuarios.id
            ORDER BY id
        ";

 
$vendedores_query = query($mysqli, $query);
$estado_query = estadoBuscar();
$buscar_fecha = fechaDesdeHasta();
if (mysqli_num_rows($vendedores_query) > 0) {



    while ($vendedor = mysqli_fetch_assoc($vendedores_query)) {
        $id = (int) $vendedor['id'];
        $supervisor = $vendedor['supervisor'];
        //   $btn_ventas = "<button class='btn btn-success' onclick='verVentas(`{$cedula}`,`{$id}`,true);abrirModal(`modal_ventas_vendedor`);' >Ver</button>";

        $query_cantidad =   "SELECT
                                count(id) as cantidad,
                                sum( total_importe ) AS monto 
                            FROM
                                padron_datos_socio 
                            WHERE
                                id_usuario=$id 
                                and padron_datos_socio.activo=1
                                {$estado_query}
                                {$buscar_fecha} 
                            ";

        $query_cantidad = query($mysqli, $query_cantidad);
        if (mysqli_num_rows($query_cantidad) > 0) {
            $socio = mysqli_fetch_assoc($query_cantidad);
            $cantidad = (int)$socio['cantidad'];
            $monto = (int) $socio['monto'];
        } else {
            $cantidad = 0;
            $monto = 0;
        }

        $tabla['data'][] = [
            'id' => $id,
            'nombre' => mb_convert_encoding($vendedor['nombre'], 'UTF-8', 'UTF-8'),
            'cedula' => $vendedor['usuario'],
            //         'ventas' => $btn_ventas,
            'cantidad' => $cantidad,
            'supervisor' => $supervisor,
            'monto' =>  $monto
        ];
    }
}
die(json_encode($tabla));
