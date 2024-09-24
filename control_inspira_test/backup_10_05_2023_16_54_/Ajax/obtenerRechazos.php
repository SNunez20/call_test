<?php
require_once "../../_conexion.php";

$response = array(
    "result"  => false,
    "session" => false,
);

if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    if ($result = mysqli_query($mysqli, "SELECT * FROM rechazos")) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response["rechazos"][] = array($row["id"], $row["rechazo"]);
        }

        $response["result"] = true;
    }
}

mysqli_close($mysqli);
echo json_encode($response);
