<?php
require_once "../../_conexion250.php";
$response = array("datos" => [], "result" => false);

if (isset($_POST["typeAdmin"])) {
    $data = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli250, $data)), $_POST);
    $cedula = $data['cedula'];

    $query = "SELECT id, cedula, nombre, direccion, tel, sucursal, fecha_nacimiento, radio, ruta, numero_tarjeta, nombre_titular,cedula_titular,movimientoabm,total_importe, fechafil,observaciones from padron_datos_socio WHERE cedula= $cedula";

    
    if ($result = mysqli_query($mysqli250, $query)) {
        if (mysqli_num_rows($result)>0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $response["datos"]= array(
                    'id_socio'          => $row['id'],
                    'cedula'            => $row["cedula"],
                    'nombre'            => $row["nombre"],
                    'direccion'         => $row["direccion"],
                    'tel'               => $row['tel'],
                    'sucursal'          => $row["sucursal"],
                    'fecha_nacimiento'  => $row["fecha_nacimiento"],
                    'radio'             => $row["radio"],
                    'ruta'              => $row["ruta"],
                    'numero_tarjeta'    => $row["numero_tarjeta"],
                    'nombre_titular'    => $row["nombre_titular"],
                    'cedula_titular'    => $row["cedula_titular"],
                    'movimientoabm'     => $row["movimientoabm"],
                    'fechafil'          => ($row['fechafil']=='') ? '0000-00-00' : $row['fechafil'],
                    'observaciones'     => $row["observaciones"],
                    'total_importe'     => $row["total_importe"]
                );
            }
            $response["result"]=true;
        }else{
            $response["result"]=false;
            $response["message"]='No hay socio afiliado con éste número de cédula';
        }
       
 

    }else{
        $response["result"]=false;
        $response["message"]='Ha ocurrido un error';
    }
}

mysqli_close($mysqli250);
echo json_encode($response);