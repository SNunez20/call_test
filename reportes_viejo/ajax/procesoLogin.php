<?php
require "../../_conexion.php";

$response = array(
    "result"        => false,
    "message"       => "Ocurrio un error. Por favor vuelva a intentar en instantes.",
    "inputs_errors" => [],
    "data"          => []
);

if (isset($_POST["usuario"]) && isset($_POST["password"])) {
 
    $_POST["usuario"]  = filter_var($_POST["usuario"], FILTER_SANITIZE_STRING);
    $_POST["password"] = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    if ($_POST["usuario"] == "") {
        $response["inputs_errors"] = "Usuario requerido";
    } else if ($_POST["password"] == "") {
        $response["inputs_errors"] = "Contraseña requerida";
    }

    if (!count($response["inputs_errors"])) {
      
        $usuario  = mysqli_real_escape_string($mysqli, $_POST["usuario"]);
        $password = mysqli_real_escape_string($mysqli, $_POST["password"]);
        $query    = "SELECT *
                FROM admin 
                WHERE usuario = '$usuario'
                    AND contrasena = '$password'
                    AND activo = '1'";
    
        if ($result = mysqli_query($mysqli, $query)) {
           
            if (mysqli_affected_rows($mysqli) > 0) {
                $row                = mysqli_fetch_assoc($result);
                $tipoUser = $row["tipo_admin"];
                if ($tipoUser=='full') {
                    $response["result"] = true;
                    $response["data"]   = array(
                        "id_usuario"   => $row["id"],
                        "usuario"      => $row["usuario"],
                        "nombre"       => strtoupper($row["nombre"]),
                        "tipo_usuario" => $tipoUser
                    );
                }else{
                    $response["result"] = false;
                    $response["message"]   = 'Usted no posee permisos para ingresar';
                }
            
            } else {
                $response["result"]  = false;
                $response["message"] = "Usuario y/o contraseña incorrectos";
            }
        }
    }else{
        $response["result"]  = false;
        $response["message"] = $response["inputs_errors"];
    }
}

mysqli_close($mysqli);
echo json_encode($response);
