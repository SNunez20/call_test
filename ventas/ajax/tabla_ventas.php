<?php

include_once './_init.php';

$tabla['data'] = [];

$estado_query = estadoBuscar('padron_datos_socio.');

$buscar_fecha = fechaDesdeHasta("padron_datos_socio.");

$query = "SELECT
            padron_datos_socio.id,
            padron_datos_socio.nombre,
            padron_datos_socio.cedula,
            padron_datos_socio.edad,
            padron_datos_socio.estado,
            padron_datos_socio.id_usuario ,
            padron_datos_socio.activo,
            padron_datos_socio.total_importe,
            padron_datos_socio.metodo_pago,
            padron_datos_socio.id_usuario,
            padron_datos_socio.fechafil,
            padron_datos_socio.radio,
            padron_datos_socio.alta
        FROM
            padron_datos_socio ,
            padron_producto_socio
        WHERE
            padron_datos_socio.activo=1
            AND padron_producto_socio.cedula=padron_datos_socio.cedula
            {$estado_query} 
            {$buscar_fecha} 
            GROUP BY padron_datos_socio.cedula
            ORDER BY padron_datos_socio.id
        ";


$query = query($mysqli, $query);

if (mysqli_num_rows($query) > 0) {
    while ($socio = mysqli_fetch_assoc($query)) {
        $cedula = $socio['cedula'];

        $estado = estados($socio['estado']);

        $id_vendedor = (int)$socio['id_usuario'];

        $query_vendedor = query($mysqli, "SELECT nombre,usuario,idgrupo FROM usuarios WHERE id=$id_vendedor AND activo=1");

        if (mysqli_num_rows($query_vendedor) > 0) {
            $fetch_vendedor = mysqli_fetch_assoc($query_vendedor);
            $ci_vendedor = $fetch_vendedor['usuario'];
            $nombre = $fetch_vendedor['nombre'];
            $vendedor = "{$nombre} ({$ci_vendedor})";
            $id_grupo = (int)$fetch_vendedor['idgrupo'];
        } else {
            $vendedor = 'Inactivo(Eliminado)';
            $ci_vendedor = '';
            $nombre = '';
            $id_grupo = 0;
        }

        $id_forma_de_pago = (int)$socio['metodo_pago'];
        $forma_de_pago = ucfirst(metodoPago($id_forma_de_pago));

        $convenio = (int)$socio['metodo_pago'] == 3 ? convenio((int)$socio['radio']) : "No tiene";

        $reintegro_query = query($mysqli, "SELECT servicio FROM padron_producto_socio WHERE servicio='07' OR servicio='06' AND cedula='$cedula'");
        $reintegro = mysqli_num_rows($reintegro_query) > 0 ? 'Tiene' : 'No tiene';

        $query_servicio = " SELECT
                                sum( hora ) AS horas,
                                sum( importe ) AS importe,
                                servicio ,
                                servicios.nombre_servicio,
                                padron_producto_socio.fecha_afiliacion
                            FROM
                                padron_producto_socio ,
                                servicios  
                            WHERE
                                cedula = '$cedula'
                                AND servicios.nro_servicio=padron_producto_socio.servicio
                            GROUP BY
                                servicio 
                            ORDER BY
                                padron_producto_socio.id ASC
                            ";

        $query_servicio = query($mysqli, $query_servicio);
        $servicio = "";
        while ($producto = mysqli_fetch_assoc($query_servicio)) {
            $horas = (int) $producto['horas'];
            $s = "{$producto['nombre_servicio']}<br>Horas: {$horas}<br>";
            $servicio = "{$servicio} {$s} <br> ";
            
        }
        $id_vendedor = (int)$socio['id_usuario'];
        $supervisor_query = query($mysqli, "SELECT id,nombre FROM admin WHERE grupo_usuarios = $id_grupo  AND activo=1 LIMIT 1");
        $supervisor = mysqli_num_rows($supervisor_query) > 0 ? mysqli_fetch_assoc($supervisor_query)['nombre'] : ' ';


        $id_socio = (int)$socio['id'];

        $estados = [
            1 => "Pendiente de Bienvenida",
            3 => "Pendiente de Calidad",
            4 => "Rechazo por Calidad",
            6 => "Paso a Padrón",
            694 => "Rechazado por Bienvenida",
        ];

        $fecha_carga_query=query($mysqli,"SELECT  fecha_afiliacion FROM padron_producto_socio WHERE   cedula = '$cedula' ORDER BY id DESC LIMIT 1");
        if(mysqli_num_rows($fecha_carga_query)>0){
            $fecha_de_carga = date('d-m-Y', strtotime(mysqli_fetch_assoc($fecha_carga_query)['fecha_afiliacion']));
        }
        else $fecha_de_carga =date('d-m-Y', strtotime($socio['fechafil']));
       


        $fecha_pendiente_bienvenida = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=1  ORDER BY fecha DESC LIMIT 1");
        $fecha_pendiente_bienvenida = mysqli_num_rows($fecha_pendiente_bienvenida) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_pendiente_bienvenida)['fecha'])) : $fecha_de_carga;

        $fecha_rechazo_bienvenida = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=4 ORDER BY fecha DESC   LIMIT 1");
        $fecha_rechazo_bienvenida = mysqli_num_rows($fecha_rechazo_bienvenida) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_rechazo_bienvenida)['fecha'])) : '';

        $fecha_pendiente_calidad = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=3 ORDER BY fecha DESC  LIMIT 1");
        $fecha_pendiente_calidad = mysqli_num_rows($fecha_pendiente_calidad) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_pendiente_calidad)['fecha'])) : '';

        $fecha_paso_a_padron = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=6 ORDER BY fecha DESC   LIMIT 1");
        $fecha_paso_a_padron = mysqli_num_rows($fecha_paso_a_padron) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_paso_a_padron)['fecha'])) : '';

        $fecha_rechazo_calidad = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=678  ORDER BY fecha DESC  LIMIT 1");
        $fecha_rechazo_calidad = mysqli_num_rows($fecha_rechazo_calidad) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_rechazo_calidad)['fecha'])) : '';

        $fecha_paso_morosidad = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=2  ORDER BY fecha DESC  LIMIT 1");
        $fecha_paso_morosidad = mysqli_num_rows($fecha_paso_morosidad) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_paso_morosidad)['fecha'])) : '';

        $fecha_rechazo_morosidad = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=7  ORDER BY fecha DESC  LIMIT 1");
        $fecha_rechazo_morosidad = mysqli_num_rows($fecha_rechazo_morosidad) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_rechazo_morosidad)['fecha'])) : '';

        $fecha_aprobado_morosidad = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=8  ORDER BY fecha DESC  LIMIT 1");
        $fecha_aprobado_morosidad = mysqli_num_rows($fecha_aprobado_morosidad) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_aprobado_morosidad)['fecha'])) : '';

        $fecha_padron = query($mysqli, "SELECT fecha FROM historico_venta WHERE id_cliente = '$id_socio' AND id_estado=6  ORDER BY fecha DESC  LIMIT 1");
        $fecha_padron = mysqli_num_rows($fecha_padron) > 0 ? date("d-m-Y", strtotime(mysqli_fetch_assoc($fecha_padron)['fecha'])) : '';


        $tabla['data'][] = [
            'id' => $id_socio,
            'cedula' => $cedula,
            'nombre' => $socio['nombre'],
            'edad' => (int)$socio['edad'],
            'estado' => $estado,
            'vendedor' => $vendedor,
            'importe_total' => (int)$socio['total_importe'],
            'convenio' => $convenio,
            'forma_de_pago' => $forma_de_pago,
            'reintegro' => $reintegro,
            'horas' => $horas,
            'servicio' => $servicio,
            'supervisor' => $supervisor,
            'fecha_carga' => $fecha_de_carga,
            'fecha_pendiente_bienvenida' => $fecha_pendiente_bienvenida,
            'fecha_pendiente_calidad' => $fecha_pendiente_calidad,
            'fecha_pason_a_padron' => $fecha_paso_a_padron,
            'fecha_rechazo_bienvenida' => $fecha_rechazo_bienvenida,
            'fecha_rechazo_calidad' => $fecha_rechazo_calidad,
            'fecha_paso_morosidad' => $fecha_paso_morosidad,
            'fecha_rechazo_morosidad' => $fecha_rechazo_morosidad,
            'fecha_aprobado_morosidad' => $fecha_aprobado_morosidad,
            'tipo' => (int)$socio['alta'] == 1 ? 'Alta' : 'Incremento'
        ];
    }
}

die(json_encode($tabla));
