<?php
require_once "../../../_conexion.php";
require_once "../../../_conexion250.php";

$response['result'] = false;
$response['session'] = false;
$response['datos_pago'] = [];


if (isset($_POST["typeAdmin"])) {
  $response["session"] = true;
  $id                  = $_POST["id"] ?? '';
  $esRechazoComp       = false; //compe
  $esPromoComp         = false; //compe
  $aplicaAdelanto      = false; //compe
  $fechaHoy            = date('Y-m-d H:i');
  $origenVenta         = null;
  $radios_cobro_adelantado = [
    '10901',
    '10902',
    '10903',
    '10904',
    '10905',
    '10906',
    '10907',
    '10908',
    '10909',
    '10910',
    '10911',
    '10912'
  ];
  $arrPromoCompetencia = ['22', '73'];


  $result = obtener_datos_socio($id);
  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $nombre             = $row['nombre'];
      $cedula             = $row['cedula'];
      $sucursal           = $row['sucursal'];
      $tipo_tarjeta       = $row['tipo_tarjeta'];
      $numero_tarjeta     = $row['numero_tarjeta'];
      $nombre_titular     = $row['nombre_titular'];
      $cedula_titular     = $row['cedula_titular'];
      $total_importe      = $row['total_importe'];
      $tarjeta_vida       = $row['tarjeta_vida'];
      $metodo_pago        = $row['metodo_pago'];
      $metodo             = $row['metodo'];
      $cvv                = $row['cvv'];
      $anio_e             = $row['anio_e'];
      $mes_e              = $row['mes_e'];
      $email_titular      = $row['email_titular'];
      $telefono_titular   = $row['telefono_titular'];
      $cuotas_mercadopago = $row['cuotas_mercadopago'];
      $alta               = $row["alta"];
      $estado             = $row["estado"];
      $id_usuario         = $row["id_usuario"];
      $idGrupo            = $row["idgrupo"];
      $fechafil           = date('Y-m-d H:i', strtotime($row["fechafil"]));
      $localidad          = $row["localidad"];
      $origenVenta        = obtenerOrigenVenta($cedula);
      $cobro_obligatorio  = in_array($row['radio'], $radios_cobro_adelantado);


      $corresponde = false;


      if ($alta == '1') { //compe
        $qPromoCompetencia = "SELECT importe, cod_promo FROM padron_producto_socio WHERE abm = 'ALTA' AND cedula = '$cedula'";
        $rPromoComp = mysqli_query($mysqli, $qPromoCompetencia);
        if ($rPromoComp && mysqli_num_rows($rPromoComp) > 0) {
          $total_importe = 0;

          while ($row = mysqli_fetch_assoc($rPromoComp)) {
            $importe   = $row['importe'];
            $total_importe += round($importe);
          }
        }


        $qRechazo = "SELECT id_estado FROM historico_venta WHERE id_cliente = $id AND fecha >= '$fechafil' AND fecha <= '$fechaHoy' AND id_estado = 675";
        $rRechazo = mysqli_query($mysqli, $qRechazo);
        if ($rRechazo && mysqli_num_rows($rRechazo) > 0) $esRechazoComp = true;
      }


      $response['datos_pago'] = [
        'nombre'               => $nombre,
        'cedula'               => $cedula,
        'tipo_tarjeta'         => $tipo_tarjeta,
        'numero_tarjeta'       => $numero_tarjeta,
        'nombre_titular'       => $nombre_titular,
        'cedula_titular'       => $cedula_titular,
        'total_importe'        => $total_importe,
        'tarjeta_vida'         => $tarjeta_vida,
        'cvv'                  => $cvv,
        'metodo_pago'          => $metodo_pago,
        'metodo'               => $metodo,
        'anio_e'               => $anio_e,
        'mes_e'                => $mes_e,
        'cuotas_mercadopago'   => $cuotas_mercadopago,
        'email_titular'        => $email_titular,
        'telefono_titular'     => $telefono_titular,
        'id_socio'             => $id,
        'alta'                 => $alta,
        'estado'               => $estado,
        'correspondeVidaPesos' => $corresponde,
        'promoVuelveAntes'     => false,
        'esRechazoComp'        => $esRechazoComp, //compe
        'aplicaAdelanto'       => $aplicaAdelanto, //compe
        'origenVenta'          => $origenVenta,
        'cobro_obligatorio'    => $cobro_obligatorio,
        'promoMadre'           => false,
        'promoVISA'            => false
      ];
      $response['result'] = true;
    }
  }
}



mysqli_close($mysqli);
echo json_encode($response);




function obtener_datos_socio($id)
{
  require "../../../_conexion.php";

  $sql = "SELECT
           pd.nombre, 
           pd.cedula, 
           pd.sucursal, 
           pd.radio, 
           pd.tipo_tarjeta, 
           pd.numero_tarjeta, 
           pd.email_titular,
           pd.telefono_titular, 
           pd.nombre_titular, 
           pd.cedula_titular, 
           pd.anio_e, 
           pd.mes_e, 
           pd.cuotas_mercadopago,
           pd.total_importe, 
           pd.tarjeta_vida, 
           pd.metodo_pago, 
           pd.cvv, 
           m.metodo, 
           pd.alta, 
           pd.estado, 
           pd.id_usuario,
           u.idgrupo, 
           pd.fechafil, 
           pd.localidad
          FROM 
           padron_datos_socio pd
           INNER JOIN metodos_pago m ON m.id = pd.metodo_pago
           LEFT JOIN usuarios u ON pd.id_usuario = u.id
          WHERE 
           pd.id = $id";
  $consulta = mysqli_query($mysqli, $sql);

  mysqli_close($mysqli);
  return $consulta;
}


function obtenerOrigenVenta($cedulaAfiliado)
{ //web
  $origenVenta = false;
  global $mysqli;

  $query = "SELECT origen_venta FROM padron_datos_socio WHERE cedula = '$cedulaAfiliado'";
  if ($result = mysqli_query($mysqli, $query)) {
    while ($row = mysqli_fetch_assoc($result)) {
      // Valido cada beneficiario en padron
      $origenVenta = $row["origen_venta"];
    }
  }

  return $origenVenta;
}
