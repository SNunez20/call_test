<?php
require_once "../../_conexion.php";
$response = array("data" => []);

if (!isset($_POST["typeAdmin"]))
    die();

$data = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
$tipo_admin = $data["typeAdmin"];

/**
 * TIPO_ADMIN = COMERCIAL
 * Solo muestra aquellos que se encuentran con estado
 * 1 - Pendiente de revisión
 * 4 - Rechazado por bienvenida
 * 7 - Rechazado por morosidad
 * 9 - Pendiente Nicolas
 * 667 - Aprobado morosidad - Alta Calidad
 * 668 - Rechazado morosidad - Alta Calidad
 * 670 - Pendiente guardar en padron
 * 674 - Aprobado calidad (competencia)
 *
 * TIPO_ADMIN = MOROSIDAD
 * 2 - Pendiente morosidad
 * 666 - Alta administrativa
 * 669 - Pendiente morosidad - Alta Administrativa
 *
 * TIPO_ADMIN = BIENVENIDA
 * 3 - En proceso de bienvenida
 */

$estado = "(1, 4, 7, 9, 667, 668, 670, 401, 402, 674, 684, 687, 688, 691)"; //compe

if ($tipo_admin == "bienvenida") {
    $estado = "(3, 4, 8, 10, 685)";
} elseif ($tipo_admin == "morosidad") {
    $estado = "(2, 666, 669)";
}

$desde = !empty($data['desde'])
    ? $data['desde']
    : '2023-11-01 00:00';
$hasta = !empty($data['hasta'])
    ? $data['hasta']
    : date("Y-m-d H:i");

$query = "SELECT
    ps.id,
    ps.nombre,
    ps.cedula,
    ps.direccion,
    ps.tel,
    ps.radio,
    ps.ruta,
    f.nombre_filial,
    mp.metodo,
    ps.usuario,
    e.estado,
    ps.fechafil,
    e.id AS id_estado,
    mp.id AS id_metodo,
    MAX(hv.fecha) as fecha, ps.alta
FROM
    padron_datos_socio ps INNER JOIN estados e ON ps.estado = e.id
INNER JOIN
    metodos_pago mp ON ps.metodo_pago = mp.id
INNER JOIN
    filiales f ON ps.sucursal = f.nro_filial
INNER JOIN
    historico_venta hv ON ps.id = hv.id_cliente
WHERE
    ps.estado IN $estado
    AND hv.fecha >= '$desde' AND hv.fecha <= '$hasta'
    AND ps.cedula NOT IN (SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf <> '' AND `cod_promo` <> 27)
GROUP BY
    ps.id
ORDER BY
    hv.fecha DESC,
    ps.id DESC";
$result = mysqli_query($mysqli, $query);

if (!$result)
    die();

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row["id"];
    $query = "SELECT
        `fecha`
    FROM
        `historico_venta`
    WHERE
        `id_estado` IN (1, 672, 684, 687, 691)
        AND `id_cliente` = $id
    ORDER BY
        `fecha` DESC
    LIMIT
        1"; //compe
    $fecha_afiliacion = "";
    if ($result_2 = mysqli_query($mysqli, $query)) {
        $fetch = mysqli_fetch_assoc($result_2);
        if ($fetch == null) continue;
        $fecha_afiliacion = explode(" ", $fetch["fecha"]);
        $fecha = explode("-", $fecha_afiliacion[0]);
        $fecha_afiliacion = $fecha[2] . "/" . $fecha[1] . "/" . $fecha[0] . " " . $fecha_afiliacion[1];
    }

    $params = "";
    $cedula = $row["cedula"];
    $query = "SELECT
        `observaciones`
    FROM
        `padron_producto_socio`
    WHERE
        `cedula` = '$cedula'
    GROUP BY
        `cedula`
    ORDER BY
        `accion` ASC";
    $result2 = mysqli_query($mysqli, $query);
    $observacion = "";
    if (mysqli_num_rows($result2)) {
        $observacion = mysqli_fetch_assoc($result2)["observaciones"];
    }

    switch ($row["id_estado"]) {
        case 7: // Rechazado morosidad
            // Pendiente morosidad
            $params = $row['id'] . ",2";
            break;
        case 8: // Aprobado morosidad
        case 4: // Rechazado Bienvenida
            $params = $row['id'] . ",3"; // Proceso bienvenida
            break;
        case 670: // Aprobado morosidad
            $params = $row['id'] . ",6"; // Proceso bienvenida
            break;
        case 684: // Afiliacion web pendiente por bienvenida
            $params = $row['id'] . ",685"; //web
            break;
        case 686: //Convenio pendiente de aprobación por Bienvenida (o calidad, no me acuerdo :c)
        case 687: //Convenio aprobado por Bienvenida (o calidad, no me acuerdo :c)
            $params = $row['id'] . ',false';
            $idConvenio = getIdConvenio($row['id']);
            $convenio = getConvenioEspecial($idConvenio);

            if (isset($convenio['nombre'])) {
                $badge = "<span class='badge badge-dark'>{$convenio['nombre']}</span>";
                $row["nombre"] = "{$badge} {$row['nombre']}";
                $row['estado'] = substr($row['estado'], 0, (strlen($row['estado']) - 1)) .  " - {$convenio['nombre']})";
            }
            break;
        default:
            $params = $row['id'] . ',false';
            break;
    }

    $title = '"Continuar el proceso"';
    $title_aprobar = '"Aprobar"';
    $title_rechazar = '"Rechazar"';
    $title_cobrar = '"Cobrar"';
    $message_rechazar = '"Rechazar"';
    $button1 = "<button class='text-uppercase btn btn-info' onclick='verDatosSocio(" . $row["id"] . ", true)'>Editar</button>";
    $button2 = "<button class='text-uppercase btn btn-success' onclick='verProductosSocio(" . $row["cedula"] . ")'>Ver y editar</button>";
    $button3 = "<button class='text-uppercase btn btn-primary text-uppercase' onclick='event.preventDefault(); continuarProceso($params, $title);'>Aprobar</button>";
    $button4 = ($row["id_metodo"] == 1 && $tipo_admin == "comercial") ? "<button class='text-uppercase btn btn-primary text-uppercase' onclick='event.preventDefault(); seleccionarRuta(" . $row["id"] . ");'>Seleccionar ruta</button>" : "-";
    $button5 = "<button class='text-uppercase btn btn-danger text-uppercase' onclick='event.preventDefault(); cancelarAfiliacion(" . $row["id"] . ");'>Eliminar afiliación</button>";
    $button6 = "<button class='text-uppercase btn btn-success text-uppercase' onclick='event.preventDefault(); continuarProceso(" . $row['id'] . ", 9, " . $title_rechazar . ");'>Marcar como pendiente</button>";

    if ($row["id_estado"] == 667 || $row["id_estado"] == 670) {
        $params = $row['id'] . ',6';
        $button3 = "<button style='background-color: #e64a19; color: #fff;' class='text-uppercase btn btn-default text-uppercase' onclick='event.preventDefault(); continuarProceso($params, $title);'>Guardar en padrón</button>";
    }

    if ($row["id_estado"] == 670 || $row["id_estado"] == 9) {
        $button6 = "";
    }

    // RECHAZADO POR CREDITOS DIRECTOS
    if ($row["id_estado"] == 401) {
        $button3 = "-";
    }

    if ($tipo_admin == "bienvenida") {
        $button1 = "<button class='text-uppercase btn btn-info' onclick='verDatosSocio(" . $row["id"] . ", false)'>Ver Datos</button>";
        $button2 = "<button class='text-uppercase btn btn-success text-uppercase' onclick='event.preventDefault(); verFormularioCobro(" . $row['id'] . ");'>Cobrar</button>";
        $button3 = "<button class='text-uppercase btn btn-danger text-uppercase' onclick='event.preventDefault(); continuarProceso(" . $row['id'] . ", 4, " . $title_rechazar . ");'>Rechazar</button>";
        $button5 = "";
        $button6 = "";
        // $button6 = "<button class='text-uppercase btn btn-success text-uppercase' onclick='event.preventDefault(); continuarProceso(" . $row['id'] . ", 10, " . $title_rechazar . ");'>Marcar como pendiente</button>";
    } elseif ($tipo_admin == "morosidad") {
        $rechazo = 7;
        $aprobado = 8;

        if ($row["id_estado"] == 666 || $row["id_estado"] == 669) {
            $aprobado = 667;
            $rechazo = 668;
        }

        $button1 = "<button class='text-uppercase btn btn-info' onclick='verDatosSocio(" . $row["id"] . ", false)'>Ver Datos</button>";
        $button2 = "<button class='text-uppercase btn btn-success text-uppercase' onclick='event.preventDefault(); continuarProceso(" . $row['id'] . ", $aprobado, " . $title_aprobar . ");'>Aprobar</button>";
        $button3 = "<button class='text-uppercase btn btn-danger text-uppercase' onclick='event.preventDefault(); continuarProceso(" . $row['id'] . ", $rechazo, " . $title_rechazar . ");'>Rechazar</button>";
        $button5 = "";
        $button6 = "";
    }

    $es_alta = $row["alta"] == "1" ? "ALTA" : "INCREMENTO";

    $response["data"][] = array(
        $row["nombre"],
        $row["cedula"],
        $row["radio"],
        $row["ruta"],
        $row["nombre_filial"],
        $row["metodo"],
        $row['estado'],
        $fecha_afiliacion,
        $button1,
        $button2,
        $button3,
        $button4 ? $button4 : '',
        $observacion,
        "<button class='text-uppercase btn btn-primary text-uppercase' onclick='event.preventDefault(); verHistorial(" . $row['id'] . ");'>Historial</button>",
        $es_alta,
        $button5,
        $button6,
    );
}

function getIdConvenio($idSocio)
{
    global $mysqli;

    $qSelect = <<<SQL
    SELECT
        `id_convenio_especial`
    FROM
        `relacion_socio_convenio_especial`
    WHERE
        `id_socio` = $idSocio;
SQL;
    $query = $mysqli->query($qSelect);

    $fetch = $query->fetch_assoc();

    return count($fetch) ? $fetch['id_convenio_especial'] : 0;
}

function getConvenioEspecial($id)
{
    $convenios = getConveniosEspeciales();

    $convenio = array_filter($convenios, function ($convenio) use ($id) {
        return $id == $convenio['id'];
    });

    return count($convenio) ? array_shift($convenio) : [];
}

function getConveniosEspeciales()
{

    static $convenios = [];

    if (count($convenios))
        return $convenios;

    global $mysqli;

    $qSelect = <<<SQL
 SELECT
 *
 FROM
 `convenios_especiales`
SQL;
    $query = $mysqli->query($qSelect);

    return $query->fetch_all(MYSQLI_ASSOC);
}

mysqli_close($mysqli);
echo json_encode($response);
