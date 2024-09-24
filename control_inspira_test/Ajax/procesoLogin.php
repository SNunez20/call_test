<?php
require_once "../../_conexion.php";
$response = array("result" => false);
$data     = array_map(fn($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
$username = $data["username"];
$password = $data["password"];

if ($result = mysqli_query($mysqli, "SELECT * FROM admin WHERE usuario = '$username' AND contrasena='$password'")) {
    if (mysqli_num_rows($result) == 1) {
        $response["result"]   = true;
        $response["userData"] = mysqli_fetch_assoc($result);
    }
}

mysqli_close($mysqli);
echo json_encode($response);
