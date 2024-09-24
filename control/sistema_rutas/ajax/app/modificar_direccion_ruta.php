<?php

include_once '../app.php';

validarForm($_POST, reglasDireccion());

$conexion_call = conexion(DB_CALL);

$request = escapeString($_POST, $conexion_call);

$conexion = conexion(DB_SISTEMA_RUTAS);


if (empty($request['puerta']) && empty($request['manzana']) && empty($request['solar'])) dieJson(true, 'Revisa los datos enviados');

if (empty($request['latitud']) || empty($request['longitud']) && empty($request['solar'])) dieJson(true, 'Revisa los datos enviados, debes de seleccionar una dirección marcando en el mapa');

$id_socio = (int)$request['id_socio'];

$padron_datos = query($conexion_call, "SELECT cedula FROM padron_datos_socio WHERE id = $id_socio OR  cedula= '{$id_socio}'");

if (mysqli_num_rows($padron_datos) == 0) {
    dieJson(true, "No se pudo encontrar al socio en el padrón por el ID $id_socio o la cedula $id_socio");
}
$socio = mysqli_fetch_assoc($padron_datos);

$cedula_socio = $socio["cedula"];

$latLng = query($conexion, "SELECT * FROM lat_lng_socios WHERE id_socio=$id_socio");

$id_socio = (int)$request['id_socio'];

if (mysqli_num_rows($latLng) > 0) {

    $query_latLng = query($conexion, "UPDATE lat_lng_socios SET latitud= '{$request['latitud']}',  longitud = '{$request['longitud']}', fecha_modificacion=now() WHERE id_socio=$id_socio ");
} else {
    $query_latLng = query($conexion, "INSERT INTO lat_lng_socios(id_socio,latitud,longitud,fecha_modificacion) 
    VALUES($id_socio,'{$request['latitud']}','{$request['longitud']}',now())");
}

$buscar_direccion = query($conexion_call, "SELECT id FROM direcciones_socios WHERE cedula_socio =$cedula_socio");

if (mysqli_num_rows($buscar_direccion) > 0) {
    $query_update_direcciones = query($conexion_call, "UPDATE direcciones_socios SET calle='{$request['calle']}' , 
    puerta='{$request['puerta']}', manzana='{$request['manzana']}' ,
    solar='{$request['solar']}' ,apartamento='{$request['apartamento']}', 
    esquina='{$request['esquina']}' ,referencia='{$request['referencia']}'  
    WHERE cedula_socio='{$cedula_socio}'        ");
} else {
    $query_update_direcciones = query($conexion_call, "INSERT INTO 
    direcciones_socios(calle,puerta,manzana,solar,apartamento,esquina,cedula_socio,id_socio)
    VALUES('{$request['calle']}','{$request['puerta']}','{$request['solar']}',
    '{$request['apartamento']}','{$request['esquina']}','{$request['esquina']}',
    '{$cedula_socio}',$id_socio)");
}

$query_update_padron = query($conexion_call, "UPDATE padron_datos_socio SET direccion='{$request['direccion']}'    WHERE cedula='{$cedula_socio}'        ");


if ($query_update_direcciones && $query_update_padron && $query_latLng) dieJson(false, 'Se confirmo la dirección del socio');

else dieJson(true, 'Hubo un error al intentar modificar la dirección del socio, recarga la página e intente nuevamente o comuniquese con desarrollo ');


function reglasDireccion()
{
    return [
        "direccion" => [
            "required" => ["error" => "El campo dirección completa es requerido"],
            "length" => ["min" => 2, "max" => 100, "error" => "El campo dirección completa debe tener entre 2 y 100 caracteres"],
        ],
        "calle" => [
            "required" => ["error" => "El campo calle es requerido"],
            "length" => ["min" => 3, "max" => 60, "error" => "El campo calle debe tener entre 3 y 60 caracteres"],
        ],
        "esquina" => [
            "required" => ["error" => "El campo esquina es requerido"],
            "length" => ["min" => 2, "max" => 60, "error" => "El campo esquina debe tener entre 3 y 60 caracteres"],
        ],
        "referencia" => [
            "required" => ["error" => "El campo referencia es requerido"],
            "length" => ["min" => 1, "max" => 100, "error" => "El campo referencia debe tener entre 1 y 100 caracteres"],
        ],
        "id_socio" => [
            "required" => ["error" => "Debes de buscar un Socio primero por ID o Cédula"],
        ]
    ];
}
