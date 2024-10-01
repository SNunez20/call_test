<?php
require_once "../../_conexion.php";
$response = array("data" => []);

if (isset($_POST["typeAdmin"])) {
    $data       = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
    $tipo_admin = $data["typeAdmin"];

    /**
     * TIPO_ADMIN = CALIDAD
     * Solo muestra aquellos que se encuentran con estado
     *  674 - Aprobado por calidad
     *  675 - Rechazado por calidad
     *  687 - Aprobado calidad (convenio)
     *  688 - Rechazado calidad (convenio)
     *  691 - Aprobado calidad (Complemento Competencia)
     *  692 - Rechazado calidad (Complemento Competencia)
     */

    if ($tipo_admin == "calidad") {
        $estado = "(674,675,687,688,691,692)";
    }

    $query = "SELECT ps.id,
    ps.nombre,
    ps.cedula,
    ps.tel,
    e.estado,
    ps.fechafil,
    ps.nombre_titular,
    ps.cedula_titular,
    cp.url_comprobante,
    h.observacion,
    h.fecha as fecha_historico,
    u.nombre as nombre_vendedor,
    u.usuario,
    g.nombre_reporte
    FROM historico_venta h  
    INNER JOIN padron_datos_socio ps ON ps.id = h.id_cliente
    LEFT JOIN comprobantes_competencia cp ON ps.id = cp.id_afiliado
    INNER JOIN estados e ON h.id_estado = e.id
    INNER JOIN usuarios u ON ps.id_usuario = u.id
    INNER JOIN gruposusuarios g ON u.idgrupo = g.id
    WHERE h.id_estado IN $estado
    ORDER BY ps.id DESC";

    if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id               = $row["id"];
            $fechaFil         = $row['fechafil'];
            $urlComprobante   = $row['url_comprobante'];
            $fecha_afiliacion = date('d-m-Y', strtotime($fechaFil));
            $cedula           = $row["cedula"];
            $nombre           = $row["nombre"];
            $estado           = $row["estado"];
            $telefonos        = $row["tel"];
            $button1          = !empty($urlComprobante) ?
                '<a href="' . $urlComprobante . '" target="_blank" class="btn btn-primary" role="button" aria-pressed="true">Ver</a>'
                :
                'No corresponde';
            $observacion      = $row['observacion'];
            $fecha_historico  = date('d-m-Y', strtotime($row['fecha_historico']));
            $cedulaVend       = $row['usuario'];
            $nombreVend       = $row['nombre_vendedor'];
            $call             = $row['nombre_reporte'];
            $nombreTitular     = $row["nombre_titular"];
            $cedulaTitular     = $row["cedula_titular"];


            $response["data"][] = array(
                $nombre,
                $cedula,
                $telefonos,
                $estado,
                $fecha_afiliacion,
                $button1,
                $observacion,
                $fecha_historico,
                $cedulaVend,
                $nombreVend,
                $call,
                $nombreTitular,
                $cedulaTitular
            );
        }
    }
}

mysqli_close($mysqli);
echo json_encode($response);
