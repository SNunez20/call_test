<?php

$response = array("data" => []);

if (!isset($_POST["typeAdmin"]))
    die(json_encode($response));


require_once "../../_conexion.php";


$data = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);
$tipo_admin = $data["typeAdmin"];

/**
 * TIPO_ADMIN = CALIDAD
 * Solo muestra aquellos que se encuentran con estado
 * 673 - Pendiente aprobacion de calidad (competencia)
 * 686 - Pendiente aprobacion calidad (convenio)
 */

if ($tipo_admin == "calidad")
    $estado = "(673, 686, 690)";


$query = "SELECT
    `ps`.`id`, `ps`.`nombre`, `ps`.`cedula`, `ps`.`tel`, `ps`.`fechafil`, `ps`.`nombre_titular`, `ps`.`cedula_titular`,
    `e`.`id` AS `e_id`, `e`.`estado`,
    `cp`.`url_comprobante`,
    `u`.`usuario`, `u`.`nombre` AS `nombre_vendedor`,
    `g`.`nombre_reporte`,
    `d`.`nombre` AS `departamento`,
    `c`.`nombre` AS `ciudad`
FROM
	`padron_datos_socio` AS `ps`
	INNER JOIN `estados` AS `e`
		ON `ps`.`estado` = `e`.`id`
	LEFT JOIN `comprobantes_competencia` AS `cp`
		ON `ps`.`id` = `cp`.`id_afiliado`
	INNER JOIN `usuarios` AS `u`
		ON `ps`.`id_usuario` = `u`.`id`
	INNER JOIN `gruposusuarios` AS `g`
		ON `u`.`idgrupo` = `g`.`id`
    INNER JOIN `ciudades` AS `c`
        ON `ps`.`localidad` = `c`.`id`
    INNER JOIN `departamentos` AS `d`
        ON `c`.`id_departamento` = `d`.`id`
WHERE
	`ps`.estado IN {$estado}
ORDER BY
	`ps`.id DESC";

$result = mysqli_query($mysqli, $query);

$fetch = $result->fetch_all(MYSQLI_ASSOC);
$response['data'] = array_map('mapearPorEstado', $fetch);

die(json_encode($response));

function mapearPorEstado($row)
{
    if ($row['e_id'] === '673')
        return mapearCompetencia($row);
    elseif ($row['e_id'] === '686')
        return mapearConvenioEspecial($row);
    elseif ($row['e_id'] === '690')
        return mapearComplementoCompetencia($row);
}

function mapearCompetencia($row)
{
    $id = $row["id"];
    $fechaFil = $row['fechafil'];
    $urlComprobante = $row['url_comprobante'];
    $fecha_afiliacion = date('d-m-Y', strtotime($fechaFil));
    $cedula = $row["cedula"];
    $nombre = $row["nombre"];
    $estado = $row["estado"];
    $telefonos = $row["tel"];
    $departamento = $row["departamento"];
    $ciudad = $row["ciudad"];
    $button1 = <<<HTML
    <a href="{$urlComprobante}" target="_blank" class="btn btn-primary" role="button" aria-pressed="true">Ver</a>
HTML;
    $button2 = <<<HTML
    <button class='text-uppercase btn btn-success' onclick="cambiarEstado('{$id}', 674)">Aprobar</button>
    <button class='text-uppercase btn btn-danger' onclick="cambiarEstado('{$id}', 675)">Rechazar</button>
HTML;
    $cedulaVend = $row["usuario"];
    $nombreVend = $row["nombre_vendedor"];
    $call = $row["nombre_reporte"];
    $nombreTitular = $row["nombre_titular"];
    $cedulaTitular = $row["cedula_titular"];

    return [
        $nombre,
        $cedula,
        $telefonos,
        $estado,
        $fecha_afiliacion,
        $button1,
        $button2,
        $cedulaVend,
        $nombreVend,
        $call,
        $nombreTitular,
        $cedulaTitular,
        $departamento,
        $ciudad
    ];
}

function mapearComplementoCompetencia($row)
{
    $id = $row["id"];
    $fechaFil = $row['fechafil'];
    $urlComprobante = $row['url_comprobante'];
    $fecha_afiliacion = date('d-m-Y', strtotime($fechaFil));
    $cedula = $row["cedula"];
    $nombre = $row["nombre"];
    $estado = $row["estado"];
    $telefonos = $row["tel"];
    $departamento = $row["departamento"];
    $ciudad = $row["ciudad"];
    $button1 = <<<HTML
    <a href="{$urlComprobante}" target="_blank" class="btn btn-primary" role="button" aria-pressed="true">Ver</a>
HTML;
    $button2 = <<<HTML
    <button class='text-uppercase btn btn-success' onclick="cambiarEstado('{$id}', 691)">Aprobar</button>
    <button class='text-uppercase btn btn-danger' onclick="cambiarEstado('{$id}', 692)">Rechazar</button>
HTML;
    $cedulaVend = $row["usuario"];
    $nombreVend = $row["nombre_vendedor"];
    $call = $row["nombre_reporte"];
    $nombreTitular = $row["nombre_titular"];
    $cedulaTitular = $row["cedula_titular"];

    return [
        $nombre,
        $cedula,
        $telefonos,
        $estado,
        $fecha_afiliacion,
        $button1,
        $button2,
        $cedulaVend,
        $nombreVend,
        $call,
        $nombreTitular,
        $cedulaTitular,
        $departamento,
        $ciudad
    ];
}

function mapearConvenioEspecial($row)
{
    $noCorresponde = 'No corresponde';

    $id = $row["id"];
    $convenio = buscarConvenio($id);
    $fechaFil = $row['fechafil'];
    $fecha_afiliacion = date('d-m-Y', strtotime($fechaFil));
    $cedula = $row["cedula"];
    $nombre = $row["nombre"];
    $estado = $row["estado"];
    $telefonos = $row["tel"];
    $departamento = $row["departamento"];
    $ciudad = $row["ciudad"];
    $button1 = "Convenio: {$convenio['nombre']}";
    $button2 = <<<HTML
    <button class="text-uppercase btn btn-success" onclick="cambiarEstado('{$id}', 687)">Aprobar</button>
    <button class="text-uppercase btn btn-danger" onclick="cambiarEstado('{$id}', 688)">Rechazar</button>
HTML;
    $cedulaVend = $row["usuario"];
    $nombreVend = $row["nombre_vendedor"];
    $call = $row["nombre_reporte"];
    $nombreTitular = $noCorresponde;
    $cedulaTitular = $noCorresponde;

    return [
        $nombre,
        $cedula,
        $telefonos,
        $estado,
        $fecha_afiliacion,
        $button1,
        $button2,
        $cedulaVend,
        $nombreVend,
        $call,
        $nombreTitular,
        $cedulaTitular,
        $departamento,
        $ciudad
    ];
}

function buscarConvenio($idSocio)
{
    global $mysqli;

    $qSelect = <<<SQL
    SELECT
        `convenios_especiales`.*
    FROM
        `convenios_especiales`
    INNER JOIN
        `relacion_socio_convenio_especial`
        ON `convenios_especiales`.`id` = `relacion_socio_convenio_especial`.`id_convenio_especial`
    WHERE
        `relacion_socio_convenio_especial`.`id_socio` = '{$idSocio}';
    SQL;
    $select = $mysqli->query($qSelect);

    return $select->fetch_assoc();
}
