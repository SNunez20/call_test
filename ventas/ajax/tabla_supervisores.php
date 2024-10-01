<?php

include_once '_init.php';

$tabla['data'] = [];

$supervisores = [];
$buscar_fecha = fechaDesdeHasta();
$estado_query = estadoBuscar();

$excepciones_supervisores_array = [ "AND admin.id <>67", 92,19,25,51,78,41];
$excepciones_supervisores = implode(' AND admin.id<> ', $excepciones_supervisores_array);

$supervisor_arrays_implode=implode(" OR admin.id = ",$supervisores_arrays_id);

$supervisores_query = "SELECT
                admin.id as id_supervisor,
                admin.usuario,
                admin.nombre as supervisor,
                admin.grupo_usuarios,
                gruposusuarios.nombre AS grupo ,
                count( usuarios.id ) AS vendedores 
                FROM
                    admin,
                    gruposusuarios,
                    usuarios 
                WHERE
                    usuarios.activo = 1 
                    AND admin.activo = 1 
                    AND usuarios.idgrupo = admin.grupo_usuarios 
                    AND gruposusuarios.id = admin.grupo_usuarios 
                    AND usuarios.idgrupo = gruposusuarios.id 
                    AND usuarios.idgrupo <> 0
                    AND admin.tipo_admin = 'normal'
                    AND ({$supervisor_arrays_implode})
                    GROUP BY admin.id
                ";

$supervisores = query($mysqli, $supervisores_query);

if (mysqli_num_rows($supervisores) > 0) {
    while ($supervisor = mysqli_fetch_assoc($supervisores)) {
        $vendedores = (int) $supervisor['vendedores'];
        $nombre_supervisor = $supervisor['supervisor'];
        $grupo = $supervisor['grupo'];
        $id_supervisor = (int)$supervisor['id_supervisor'];

        $padron = queryVendedoresEstado($id_supervisor, 6, $buscar_fecha);
        $padron = mysqli_num_rows($padron);

        $pendiente = queryVendedoresEstado($id_supervisor, 1, $buscar_fecha);
        $pendiente = mysqli_num_rows($pendiente);

        $morosidad = queryVendedoresEstado($id_supervisor, 2, $buscar_fecha);
        $morosidad = mysqli_num_rows($morosidad);


        $tabla['data'][] = [
            'id' => $id_supervisor,
            'nombre' => $nombre_supervisor,
            'vendedores' => $vendedores,
            'grupo' => $grupo,
            'pendiente' => $pendiente,
            'padron' => $padron,
            'morosidad' => $morosidad
        ];
    }
}


die(json_encode($tabla));


function queryVendedoresEstado($id, $estado, $buscar_fecha)
{
    global $mysqli;
    $q = "SELECT
                padron_datos_socio.cedula
            FROM
                usuarios,
                padron_datos_socio,
                admin,
                gruposusuarios 
            WHERE
                usuarios.activo = 1 
                AND admin.activo = 1 
                AND usuarios.idgrupo = admin.grupo_usuarios 
                AND gruposusuarios.id = admin.grupo_usuarios 
                AND usuarios.idgrupo = gruposusuarios.id 
                AND usuarios.idgrupo <> 0 
                AND usuarios.id = padron_datos_socio.id_usuario 
                AND padron_datos_socio.activo = 1 
                AND padron_datos_socio.estado = $estado
                AND admin.id=$id
                {$buscar_fecha} 
            GROUP BY
                padron_datos_socio.cedula 
            ORDER BY
                padron_datos_socio.estado
    ";

    return query($mysqli, $q);
}
