<?php
require "../../_conexion.php";
session_start();

$response = array('result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');

if (isset($_SESSION["idadmin"])) {
    $vendedor = $_GET["vendedor"];
    $desde = $_GET["desde"];
    $hasta = isset($_GET["hasta"]) && $_GET["hasta"] != "" ? $_GET["hasta"] : date("Y-m-d H:i:s");

    if ($result = mysqli_query($mysqli, "SELECT nombre FROM usuarios WHERE id=$vendedor AND activo=1")) {
        $response["result"]["vendedor"] = mysqli_fetch_assoc($result)["nombre"];
    }

    $query = "SELECT COUNT(DISTINCT numero) as cantidad_numeros FROM `historico` where idusuario=$vendedor and fecha>='$desde' <= '$hasta'";
    if ($result = mysqli_query($mysqli, $query)) {
        $response["result"]["total_numeros"] = mysqli_fetch_assoc($result)["cantidad_numeros"];
        $estados = array(
            "no contesta",
            "no interesado",
            "agendado",
            "borrado permanentemente",
            "referido",
            "tomado de agenda",
            "vendido",
            "tomado de referidos",
        );

        $total_llamadas=0;

        foreach ($estados as $estado) {
            // Recupero la cantidad de números por estado
            $query = "SELECT COUNT(numero) FROM historico where estado='$estado' and idusuario=$vendedor and fecha>='$desde' and fecha <='$hasta'";
           
            if ($result = mysqli_query($mysqli, $query)) {

                $cantidad_llamadas= mysqli_fetch_array($result)[0];
                $key = "cantidad_" . join("_", explode(" ", $estado));

                $response["result"][$key] = intval($cantidad_llamadas);

                $total_llamadas+=$cantidad_llamadas;
            }
        
        }

        $response['result']['total_llamadas']=$total_llamadas;
    }

} else {
    $response = array('result' => false, 'message' => 'Sin Sesion');
}

mysqli_close($mysqli);
echo json_encode($response);
