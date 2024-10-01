<?php

include_once './_init.php';

$tabla['data'] = [];

$id = (int) $_REQUEST['id_vendedor'];
$cedula_vendedor = $_REQUEST['ci_vendedor'];

$query = "SELECT id,nombre,cedula,edad,estado,id_usuario,total_importe FROM padron_datos_socio_piscina WHERE id_usuario=$id ORDER BY id";

$query = mysqli_query($mysqli, $query);

if (mysqli_num_rows($query) > 0) {
    while ($socio = mysqli_fetch_assoc($query)) {
        $estado = estado($socio['estado']);
        $cedula= $socio['cedula'];
        $btn_servicios = "<button class='btn btn-primary'  onclick='verServiciosVendedor(`{$cedula}`);' >Ver</button>";
        $monto = (int) $socio['total_importe'];
        $tabla['data'][] = [
            'id' => $socio['id'],
            'nombre' => $socio['nombre'],
            'cedula' => $cedula,
            'estado' => $estado,
            'ventas'=>$btn_servicios,
            'monto'=>$monto
        ];
    }
}

die(json_encode($tabla));
