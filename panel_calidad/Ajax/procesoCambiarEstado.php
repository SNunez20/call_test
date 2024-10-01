<?php

$response = array(
    "result" => false,
    "session" => false,
);

if (!isset($_POST["typeAdmin"]))
    die(json_encode($response));

require_once "../../_conexion.php";

$data = array_map(fn ($data) => strip_tags(mysqli_real_escape_string($mysqli, $data)), $_POST);

$idAfiliado = $data['idAfiliado'];
$idEstado = $data['idEstado'];
$idUser = $data['idUser'];
$observacion = $data['observacion'];
$accion = false;

const RECHAZADO_COMPETENCIA = 675;
const RECHAZADO_CONVENIO_ESPECIAL = 688;
const RECHAZADO_COMPLEMENTO_COMPETENCIA = 692;

$result = cambiarEstadoSocio($idEstado);

if (!$result)
    die(json_encode($response));

if ($idEstado == RECHAZADO_COMPETENCIA)
    procesarRechazoCompetencia();
elseif ($idEstado == RECHAZADO_CONVENIO_ESPECIAL)
    procesarRechazoConvenioEspecial();

$response["result"] = true;
$response["message"] = 'Acción efectuada correctamente';

die(json_encode($response));

function procesarRechazoCompetencia()
{
    global $mysqli;
    global $idAfiliado;

    $codigosPromo = "('35', '2035', '3335')";

    $qPromos = "SELECT
        `id`, `cod_promo`
    FROM
        `padron_producto_socio`
    WHERE
        `cod_promo` IN {$codigosPromo}
        AND `cedula` = (
            SELECT
                `cedula`
            FROM
                `padron_datos_socio`
            WHERE
                `id` = {$idAfiliado}
        )";
    $rPromos =  $mysqli->query($qPromos);

    if ($rPromos && mysqli_num_rows($rPromos) > 0) {

        while ($row = mysqli_fetch_assoc($rPromos)) {
            $idServ = $row['id'];
            $codPromo = $row['cod_promo'];
            $codPromo = str_replace(array('35', '2035', '3335'), array('', '20', '33'), $codPromo);
            $codPromo = ($codPromo == '') ? '0' : $codPromo;

            $qActualizar = "UPDATE `padron_producto_socio` SET `cod_promo` = {$codPromo} WHERE `id` = {$idServ}";
            $mysqli->query($qActualizar);
        }

        $qActualizarDatos = "UPDATE `padron_datos_socio` SET `dato_extra` = '3' WHERE `id` = {$idAfiliado}";
        $mysqli->query($qActualizarDatos);
    }

    cambiarEstadoSocio(1);
}

function procesarRechazoConvenioEspecial()
{
    global $idAfiliado;

    $cedulaAfiliado = buscarCedulaPorID($idAfiliado);
    $servicios = buscarServiciosSocio($cedulaAfiliado);
    $total = 0;

    foreach ($servicios as $servicio) {
        borrarDescuentoPadronProductoSocios($servicio['id']);
        $total += $servicio['keepprice1'];
    }

    reemplazarTotalImporteSocio($total);
    borrarConvenioEspecial();

    cambiarEstadoSocio(1);
}

function borrarConvenioEspecial()
{
    global $mysqli;
    global $idAfiliado;

    $qDelete = <<<SQL
    DELETE FROM
        `relacion_socio_convenio`
    WHERE
        `id_socio` = '{$idAfiliado}';
SQL;
    $mysqli->query($qDelete);
}

function reemplazarTotalImporteSocio($importeSinDescuentos)
{
    global $mysqli;
    global $idAfiliado;

    $qUpdate = <<<SQL
    UPDATE
        `padron_datos_socio`
    SET
        `total_importe` = '{$importeSinDescuentos}'
    WHERE
        `id` = '{$idAfiliado}';
SQL;
    $mysqli->query($qUpdate);
}

function buscarServiciosSocio($cedulaAfiliado)
{

    global $mysqli;

    $qSelect = <<<SQL
    SELECT
        *
    FROM
        `padron_producto_socio`
    WHERE
        `cedula` = '{$cedulaAfiliado}'
SQL;
    $select = $mysqli->query($qSelect);

    return $select->fetch_all(MYSQLI_ASSOC);
}

function borrarDescuentoPadronProductoSocios($_idPadronProductoSocio)
{
    global $mysqli;

    $idPadronProductoSocio = $mysqli->real_escape_string($_idPadronProductoSocio);

    $qUpdate = <<<SQL
    UPDATE
        `padron_producto_socio`
    SET
        `importe` = `keepprice1`
    WHERE
        `id` = '{$idPadronProductoSocio}';
SQL;
    $mysqli->query($qUpdate);

    return $mysqli->affected_rows;
}

function buscarCedulaPorID($_idAfiliado)
{
    global $mysqli;
    $idAfiliado = $mysqli->real_escape_string($_idAfiliado);

    $qSelect = <<<SQL
    SELECT
        `cedula`
    FROM
        `padron_datos_socio`
    WHERE
        `id` = '{$idAfiliado}'
SQL;
    $select = $mysqli->query($qSelect);

    return $select->fetch_assoc()['cedula'];
}

function cambiarEstadoSocio($estado)
{
    global $mysqli;
    global $idAfiliado;
    global $observacion;

    insertarHistorico($estado, $observacion);

    $qUpdate = "UPDATE `padron_datos_socio` SET `estado` = {$estado} WHERE `id` = {$idAfiliado}";
    return $mysqli->query($qUpdate);
}

function insertarHistorico($idEstado, $_observacion)
{
    global $mysqli;
    global $idUser;
    global $idAfiliado;
    $observacion = strtoupper($_observacion);

    $qInsert = "INSERT INTO `historico_venta` VALUES (null, {$idUser}, {$idAfiliado}, {$idEstado}, NOW(), '{$observacion}', 11)";
    return $mysqli->query($qInsert);
}