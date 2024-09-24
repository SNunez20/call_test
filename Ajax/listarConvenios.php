<?php
include '../_conexion.php';
session_start();

if (isset($_SESSION['idusuario'])) {

    $id_metodo = mysqli_real_escape_string($mysqli,$_POST['idMetodo']);
    $servicios = $_POST['servicios'];
    $id_localidad = mysqli_real_escape_string($mysqli,$_POST['localidad']);
    $esPromoComp = mysqli_real_escape_string($mysqli,$_POST['promoComp']); //compe

    $where = (in_array(114,$servicios) || $esPromoComp=='true') ? "AND mc.id_convenio <> 9" : ""; //compe
    if ($_SESSION['grupoUsuario']!=666) {
        $where .= " AND mc.id_convenio <> 26";
    }

    $q = "SELECT  rc.id, rc.cod_metodo, rc.nombre_convenio 
    FROM medios_servicios mc
    INNER JOIN radios_convenios rc ON mc.id_convenio = rc.id
    WHERE rc.mostrar='1' AND rc.id_metodo = $id_metodo AND mc.id_servicio IN (".implode(',',($servicios)).") $where GROUP BY mc.id_convenio";
//die($q);
    if ($result = mysqli_query($mysqli, $q)) {

        $convenios = array();

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {

                $id        = $row['id'];
                $id_metodo = $row['cod_metodo'];
                $convenio  = $row['nombre_convenio'];

                $convenios[] = array('id' => $id, 'convenio' => $convenio, 'id_metodo' => $id_metodo);

                $response = array(
                    'result'    => true,
                    'session'   => true,
                    'message'   => 'Convenios listados correctamente.',
                    'convenios' => $convenios,
                );
            }
        } else {
            $response = array(
                'result'    => true,
                'session'   => true,
                'message'   => 'No hay convenios para este producto.',
                'convenios' => $convenios,
            );
        }

    } else {
        $response = array(
            'result'     => false,
            'session'    => true,
            'message'    => 'Ocurrio un error en la consulta.',
            'convenios ' => $convenios,

        );
    }

} else {
    $response = array(
        'result'  => false,
        'session' => false,
        'message' => 'sin sesion.',
    );
}
mysqli_close($mysqli);
echo json_encode($response);
