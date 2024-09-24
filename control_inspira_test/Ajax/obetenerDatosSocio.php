<?php
require_once "../../_conexion.php";
require_once "../../_conexion250.php";

$response['result'] = false;
$response['session'] = false;
$response['socio'] = [];
$response['filiales'] = [];
$response['metodos_pago'] = [];


if (isset($_POST["typeAdmin"])) {
    $response["session"] = true;
    $id                  = $_POST["id"] ?? '';
    $esRechazoComp = false; //compe

    // $query = "SELECT ps.*, ps.id as id_socio, pp.numero_vendedor, pp.nombre_vendedor, ps.count, ps.id_usuario, u.idgrupo, ps.fechafil, ps.nombre, ps.localidad, ps.alta
    //   FROM bancos_emisores b, padron_datos_socio ps INNER JOIN estados e ON ps.estado = e.id
    //   INNER JOIN metodos_pago mp ON ps.metodo_pago = mp.id
    //   INNER JOIN filiales f ON ps.sucursal = f.nro_filial
    //   INNER JOIN padron_producto_socio pp ON ps.cedula = pp.cedula
    //   INNER JOIN usuarios u ON ps.id_usuario = u.id
    //   WHERE ps.id = $id AND pp.accion = '1' GROUP BY ps.id";

    $query = "SELECT 
               ps.nombre, 
               ps.cedula, 
               ps.tel, 
               ps.radio, 
               ps.localidad, 
               ps.sucursal, 
               ps.direccion, 
               ps.observaciones, 
               ps.fecha_nacimiento, 
               ps.numero_tarjeta, 
               ps.tarjeta, 
               ps.tipo_tarjeta, 
               ps.nombre_titular, 
               ps.cedula_titular, 
               ps.telefono_titular, 
               ps.anio_e, 
               ps.mes_e, 
               ps.cvv, 
               ps.cuotas_mercadopago, 
               ps.count, 
               ps.email, 
               ps.email_titular, 
               ps.total_importe, 
               ps.metodo_pago, 
               ps.banco_emisor, 
               b.banco, 
               u.idgrupo, 
               ps.fechafil, 
               ps.id as id_socio, 
               pp.numero_vendedor, 
               pp.nombre_vendedor, 
               ps.count, 
               ps.ruta, 
               ps.tarjeta_vida, 
               ps.id_usuario, 
               ps.alta, 
               ps.estado as id_estado, 
               e.estado, 
               ps.origen_venta
              FROM 
               bancos_emisores b, 
               padron_datos_socio ps 
               INNER JOIN estados e ON ps.estado = e.id
               INNER JOIN metodos_pago mp ON ps.metodo_pago = mp.id
               INNER JOIN filiales f ON ps.sucursal = f.nro_filial
               INNER JOIN padron_producto_socio pp ON ps.cedula = pp.cedula
               INNER JOIN usuarios u ON ps.id_usuario = u.id
              WHERE 
               ps.id = $id AND 
               pp.accion = '1' 
              GROUP BY ps.id";
    // die($query);

    if ($result = mysqli_query($mysqli, $query)) {

        $response["socio"] = mysqli_fetch_assoc($result);

        // Rutas
        $cedula   = $response["socio"]["cedula"];
        $radio    = $response["socio"]["radio"];
        $sucursal = $response["socio"]["sucursal"];
        $idGrupo  = $response["socio"]["idgrupo"];
        $fechafil = $response["socio"]["fechafil"];
        $localidad = $response["socio"]["localidad"];
        $fechaHoy = date('Y-m-d');
        $es_alta = $response["socio"]["alta"];



        if ($es_alta == '1') { //compe
            $qRechazo = "SELECT id_estado FROM historico_venta WHERE id_cliente = $id AND fecha >= '$fechafil' AND fecha <='$fechaHoy' AND id_estado = 675";

            $rRechazo = mysqli_query($mysqli, $qRechazo);
            if ($rRechazo && mysqli_num_rows($rRechazo) > 0) {
                $esRechazoComp = true;
            }
        }

        $response['socio']['esRechazoComp'] = $esRechazoComp; //compe

        $fechaAfiliacion = new DateTime($fechafil);
        $fechaVigenciaCarmelo = new DateTime('2021-12-13');
        $fechaVigenciaPando = new DateTime('2022-03-23');
        $fechaVigenciaSantaLucia = new DateTime('2022-03-31');


        // $response["socio"]['correspondeVidapesos'] = (($sucursal == 7 || ($localidad == 172 && $fechaAfiliacion >= $fechaVigenciaCarmelo) || ($sucursal == 115 && $fechaAfiliacion >= $fechaVigenciaPando) || ($localidad == 288 && $fechaAfiliacion >= $fechaVigenciaSantaLucia) ) && $es_alta == '1') ? true : false; 


        $response["socio"]['correspondeVidapesos'] = false;

        $qPromoVuelveAntes = "SELECT id FROM historico_venta WHERE id_cliente = $id AND id_estado = 689";
        $rPromoVuelveAntes = mysqli_query($mysqli, $qPromoVuelveAntes);
        $response["socio"]['promoVuelveAntes'] = mysqli_num_rows($rPromoVuelveAntes) ? true : false;

        $qConvenioEspecial = "SELECT
                `nombre`
            FROM
                `convenios_especiales`
            INNER JOIN
                `relacion_socio_convenio_especial`
                ON `convenios_especiales`.`id` = `relacion_socio_convenio_especial`.`id_convenio_especial`
            WHERE
                `relacion_socio_convenio_especial`.`id_socio` = $id;";
        $rConvenioEspecial = $mysqli->query($qConvenioEspecial);
        $response['socio']['convenioEspecial'] = $rConvenioEspecial->num_rows === 1 ? $rConvenioEspecial->fetch_assoc()['nombre'] : false;

        $qPromoMadre = "SELECT cedula_titular_gf FROM padron_producto_socio WHERE cod_promo = 27 AND cedula = '$cedula'";
        $rPromoMadre = $mysqli->query($qPromoMadre);
        $promoMadre = mysqli_num_rows($rPromoMadre) > 0 ? mysqli_fetch_assoc($rPromoMadre) : false;
        $qPromoVISA = "SELECT cedula_titular_gf FROM padron_producto_socio WHERE cod_promo = 28 AND cedula = '$cedula'";
        $rPromoVISA = $mysqli->query($qPromoVISA);
        $promoVISA = mysqli_num_rows($rPromoVISA) > 0;
        $qPromoFloreada = "SELECT cedula_titular_gf FROM padron_producto_socio WHERE cod_promo = 30 AND cedula = '$cedula'";
        $rPromoFloreada = $mysqli->query($qPromoFloreada);
        $promoFloreada = mysqli_num_rows($rPromoFloreada) > 0;

        $response['socio']['promoMadre'] = $promoMadre;
        $response['socio']['promoVISA'] = $promoVISA;
        $response['socio']['promoFloreada'] = $promoFloreada;

        $qGF = "SELECT cedula_titular_gf FROM padron_producto_socio WHERE cedula = '$cedula' AND !ISNULL(cedula_titular_gf) AND cedula_titular_gf != '' AND cod_promo != 27";
        $rGF = $mysqli->query($qGF);
        $cedula_titular_gf = mysqli_num_rows($rGF) > 0 ? mysqli_fetch_assoc($rGF)['cedula_titular_gf'] : '';
        $response['socio']['cedula_titular_gf'] = $cedula_titular_gf;

        $query = "SELECT ruta FROM abmmod.aux2 WHERE num_sucursal=$sucursal";

        $result                     = mysqli_query($mysqli250, $query);
        $response["socio"]["rutas"] = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_NUM) : '';

        // Sucursal
        $result = mysqli_query($mysqli, "SELECT id, nombre_filial FROM filiales WHERE nro_filial=" . $response["socio"]["sucursal"]);

        $data                          = mysqli_fetch_assoc($result);
        $id_filial                     = $data["id"];
        $response["socio"]["sucursal"] = $data["nombre_filial"];

        $rLoc                           = mysqli_query($mysqli, "SELECT nombre from ciudades where id = " . $response["socio"]["localidad"]);
        $loc                            = mysqli_fetch_assoc($rLoc)['nombre'];
        $response["socio"]["localidad"] = $loc;

        // Filiales
        $result               = mysqli_query($mysqli, "SELECT * FROM filiales");
        $response["filiales"] = mysqli_fetch_all($result, MYSQLI_NUM);

        // Bancos
        $result             = mysqli_query($mysqli, "SELECT * FROM bancos_emisores");
        $response["bancos"] = mysqli_fetch_all($result, MYSQLI_NUM);

        // Métodos de pago
        $result                   = mysqli_query($mysqli, "SELECT * FROM metodos_pago");
        $response["metodos_pago"] = mysqli_fetch_all($result, MYSQLI_NUM);

        $response["result"] = true;
    }
}

mysqli_close($mysqli);
mysqli_close($mysqli250);
die(json_encode($response, JSON_INVALID_UTF8_IGNORE));
