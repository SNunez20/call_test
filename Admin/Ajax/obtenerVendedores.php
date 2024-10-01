<?php
require "../../_conexion.php";
session_start();

$response = array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_POST["usuario"])) {
    $id_usuario = $_POST["usuario"];

    # todos los registros del usuario/vendor que se quiere eliminar
    $query = "SELECT 
    (SELECT count(*) FROM agendados a WHERE a.usuarioid=$id_usuario) +
    (SELECT count(*) FROM referidos r WHERE r.usuarioid=$id_usuario) +
    (SELECT count(*) FROM referidoscuaderno rc WHERE rc.idusuario=$id_usuario) AS total";
    // exit($query);
    
    if ($result =  mysqli_query($mysqli, $query)) {
        $response["result"]["total_registros"] = mysqli_fetch_assoc($result)["total"];

        # todos los vendedores
        if ($_SESSION["tipo_admin"] == "full") {
            $query = "SELECT id, nombre FROM usuarios WHERE id <> $id_usuario";
        } else {
            $query = "SELECT id, nombre FROM usuarios WHERE id <> $id_usuario and idgrupo=".$_SESSION["grupo_usuarios"];
        }
        if ($result = mysqli_query($mysqli, $query)) {
            while($row = mysqli_fetch_assoc($result)) {
                $response["result"]["vendedores"][] = array($row["id"], $row["nombre"]);
            }
        }
    }
    
}

mysqli_close($mysqli);
echo json_encode($response);
