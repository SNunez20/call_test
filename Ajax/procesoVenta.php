<?php

session_start();

date_default_timezone_set('America/Argentina/Buenos_Aires');

require_once "../_conexion.php";
require_once "../_conexion1310.php";
require_once "../_conexion250.php";

global $mysqli;
global $mysqli1310;
global $mysqli250;

const CODIGO_PROMO_VISA = 28;

const SERVICIOS_CON_RADIO_ADELANTO = [
  130 => 12,
  146 => 6
];

$response = array(
  'result' => false,
  'session' => false,
  'message' => 'Ocurrio un error, intentelo nuevamente más tarde.',
);

$log_date = date("Y-m-d");
$log_hour = date('H:i');
$log_file = "../logs/call_$log_date.log";
$log_file_step = "../logs/log_step_call/call_$log_date.log";
$log_error_file = "../logs/errors_call/error_$log_date.log";


if (!isset($_SESSION['idusuario']))
  return false;

$arrServiciosConModulos = [138];

$response["session"] = true;
$idUser = $_SESSION['idusuario'];
$nombre_vendedor = $_SESSION['nombreUsuario'];
$numero_vendedor = $_SESSION['cedulaUsuario'];
$socio = $_POST['socio'] == 'true';
$nros_servicios = [];
$servicios = json_decode($_POST["servicios"], true);
$esPromoCompetenciaVeintitres = false;
$precio_segundo_modulo_sanatorio = 0;

$beneficiarios = json_decode($_POST["beneficiarios"], true);
$benOmt = json_decode($_POST["benOmt"], true);
$omt = $_POST["omt"];
$benMama = json_decode($_POST["benMama"], true);
$promoMesMama = $_POST["promoMesMama"];
$incrementoOmt = mysqli_real_escape_string($mysqli250, $_POST["incrementoOmt"]);

// ################################################################################################
if (!str_starts_with($numero_vendedor, 'CO'))
  $numero_vendedor = str_replace(range("a", "z"), range(1, 26), strtolower($numero_vendedor));
if ($numero_vendedor == "149925077")
  $numero_vendedor = "49925077";
// ################################################################################################

// Recorremos el array de servicos y guardamos los numeros de servicios de cada uno en un array

$radioMesesAdelantados = null;
for ($i = 0; $i < count($servicios); $i++) {
  if (!isset($servicios[$i][0])) continue;

  $qServicio = "SELECT nro_servicio from motor_de_precios.servicios where id = " . $servicios[$i][0];
  $rServicio = mysqli_query($mysqli1310, $qServicio);
  $nros_servicios[$i] = mysqli_fetch_assoc($rServicio)['nro_servicio'];

  if (array_key_exists($servicios[$i][0], SERVICIOS_CON_RADIO_ADELANTO))
    $radioMesesAdelantados = radioMesesAdelantados(SERVICIOS_CON_RADIO_ADELANTO[$servicios[$i][0]]);

  if (in_array($servicios[$i][0], $arrServiciosConModulos)) $servicios[$i][1] *= 8;
}

//verificamos si existe servicio grupo familiar
$grupoFamiliar = in_array('63', $nros_servicios) || in_array('65', $nros_servicios);
$emergencial = in_array('24', $nros_servicios);
$arrServiciosIva1 = ['81', '84', '93', '94', '95', '96']; //newproducts

if (!$socio) {
  // Llamada entrante
  $llamada_entrante = mysqli_real_escape_string($mysqli, $_POST["llamadaEntrante"]) == "true" ? "1" : "0";
  $existe_padron = '0';
  $cedula = mysqli_real_escape_string($mysqli250, $_POST["cedula"]);
  $nombre = mysqli_real_escape_string($mysqli250, $_POST["nombre"]);
  $celular = mysqli_real_escape_string($mysqli250, $_POST["celular"]);
  $telefono = mysqli_real_escape_string($mysqli250, $_POST["telefono"]);
  $fecha_nacimiento = mysqli_real_escape_string($mysqli250, $_POST["fechaNacimiento"]);
  $mail = mysqli_real_escape_string($mysqli250, $_POST["mail"]);
  $direccion = substr(mysqli_real_escape_string($mysqli250, $_POST["direccion"]), 0, 36);
  $calle = mysqli_real_escape_string($mysqli250, $_POST["calle"]); //dir2
  $puerta = mysqli_real_escape_string($mysqli250, $_POST["puerta"]);
  $apto = mysqli_real_escape_string($mysqli250, $_POST["apto"]);
  $esquina = mysqli_real_escape_string($mysqli250, $_POST["esquina"]);
  $manzana = mysqli_real_escape_string($mysqli250, $_POST["manzana"]);
  $solar = mysqli_real_escape_string($mysqli250, $_POST["solar"]);
  $referencia = mysqli_real_escape_string($mysqli250, $_POST["referencia"]);
  $departamento = mysqli_real_escape_string($mysqli250, $_POST["departamento"]);
  $observacion = mysqli_real_escape_string($mysqli250, $_POST["observacion"]);
  $id_localidad = mysqli_real_escape_string($mysqli250, $_POST["localidad"]);
  $id_sucursal = mysqli_real_escape_string($mysqli250, $_POST["filial"]);
  $numeroTarjeta = mysqli_real_escape_string($mysqli250, $_POST["numeroTarjeta"]);
  $cuotas_mercadopago = isset($_POST["cuotas"]) ? mysqli_real_escape_string($mysqli250, $_POST["cuotas"]) : 1; //seba
  //$cvv = mysqli_real_escape_string($mysqli250, $_POST["cvv"]);
  $cvv = '';
  $mesVencimiento = mysqli_real_escape_string($mysqli250, $_POST["mesVencimiento"]);
  $anioVencimiento = mysqli_real_escape_string($mysqli250, $_POST["anioVencimiento"]);
  $nombreTitular = mysqli_real_escape_string(
    $mysqli250,
    !empty($_POST["nombreTitular"])
      ? $_POST["nombreTitular"]
      : $_POST["nombreTitularConvenio"]
  );
  $cedulaTitular = mysqli_real_escape_string(
    $mysqli250,
    !empty($_POST["cedulaTitular"])
      ? $_POST["cedulaTitular"]
      : $_POST["cedulaTitularConvenio"]
  );
  $celularTitular = mysqli_real_escape_string($mysqli250, $_POST["celularTitular"]);
  $telefonoTitular = mysqli_real_escape_string($mysqli250, $_POST["telefonoTitular"]);
  $mailTitular = mysqli_real_escape_string($mysqli250, $_POST["mailTitular"]);
  $medio_pago = mysqli_real_escape_string($mysqli250, $_POST["medio_pago"]);
  $total = mysqli_real_escape_string($mysqli250, $_POST["total"]);
  $is_mercadopago = mysqli_real_escape_string($mysqli250, $_POST["is_mercadopago"]);
  $tipo_tarjeta = mysqli_real_escape_string($mysqli250, $_POST["tipo_tarjeta"]);
  $email_titular = mysqli_real_escape_string($mysqli250, $_POST["mailTitular"]);
  $id_convenio = mysqli_real_escape_string($mysqli250, $_POST["idConvenio"]);
  $bancoEmisor = !empty($_POST["bancoEmisor"]) ? mysqli_real_escape_string($mysqli250, $_POST["bancoEmisor"]) : 0;
  $datoExtra = mysqli_real_escape_string($mysqli250, $_POST["datoExtra"]);
  $esPromoCompetencia = mysqli_real_escape_string($mysqli250, $_POST["esPromoCompetencia"]); //compe
  $esPromoCompetenciaVeintitres = mysqli_real_escape_string($mysqli250, isset($_POST["esPromoCompetenciaVeintitres"])
    ? $_POST["esPromoCompetenciaVeintitres"] : false); //compe
  $es_vuelve_antes = isset($_POST["es_vuelve_antes"])
    ? (bool)json_decode($_POST["es_vuelve_antes"]) : false; //vuelve antes
  $idConvenioEspecial = isset($_POST['tieneConvenioEspecial']) && $_POST['tieneConvenioEspecial'] != 'false'
    ? $mysqli250->real_escape_string($_POST['tieneConvenioEspecial']) : false;


  $accion = '1';
  $fecha = date('Y-m-d H:i');
  $observacion = trim($observacion);
  $metodo_vida = '';
  $tel = $celular . ' ' . $telefono;
  $mesVencimiento = $numeroTarjeta == '' ? '' : $mesVencimiento;
  $anioVencimiento = $numeroTarjeta == '' ? '' : $anioVencimiento;
  $is_mercadopago = ($is_mercadopago == '' || $is_mercadopago == '0') ? '0' : '1';
  $count = 0;
  $abm_actual = $is_mercadopago == '1' ? '0' : '1';
  $cobrador = ($medio_pago == '1') ? '1' : '0';
  $ruta = '';
  $cvv = ($cvv == '') ? 0 : $cvv;
  $anioVencimiento = ($anioVencimiento == '') ? 0 : $anioVencimiento;
  $mesVencimiento = ($mesVencimiento == '') ? 0 : $mesVencimiento;
  $tarjeta_vida = ($is_mercadopago == '1' && $medio_pago == '2') ? '0' : '1';

  if ($esPromoCompetencia != "false" || $esPromoCompetenciaVeintitres != "false")
    $estado = 672;
  elseif ($idConvenioEspecial !== false)
    $estado = buscarEstadoConvenioEspecial($idConvenioEspecial);
  elseif (requiereCorroboracionCalidad())
    $estado = estadoCalidad();
  else
    $estado = 1;

  // Traigo el número de la filial segun la localidad del cliente
  $qSucursal = "SELECT nro_filial from motor_de_precios.filiales where id = $id_sucursal";
  $rSucursal = mysqli_query($mysqli1310, $qSucursal);
  $sucursal = mysqli_fetch_assoc($rSucursal)['nro_filial'];
  $sucursal = ($sucursal != '0') ? preg_replace('/^0+/', '', $sucursal) : $sucursal;
  $sucursal_cobranzas_num = ($is_mercadopago == '1' || $tarjeta_vida == '1') ? '99' : $sucursal;

  // Obtengo el rut de la empresa segun la filial
  $qERut = "SELECT empresa_rut from aux2 where num_sucursal = $sucursal";
  $rERut = mysqli_query($mysqli250, $qERut);
  $empresa_rut = "";

  if (mysqli_num_rows($rERut))
    $empresa_rut = mysqli_fetch_assoc($rERut)['empresa_rut'];


  $rutcentralizado = ($cobrador == '1') ? $empresa_rut : '99';
  $qEmarca = "SELECT empresa_brand from aux2 where num_sucursal = $sucursal";
  $rEmarca = mysqli_query($mysqli250, $qEmarca);
  $empresa_marca = "";

  if (mysqli_num_rows($rEmarca))
    $empresa_marca = mysqli_fetch_assoc($rEmarca)['empresa_brand'];


  $ruta = '0000000000';
  $cumpleanos = new DateTime($fecha_nacimiento);
  $hoy = new DateTime();
  $annos = $hoy->diff($cumpleanos);
  $edad = $annos->y;
  $idRelacion = $empresa_rut . '-' . $cedula;

  if ($medio_pago == '1') {
    $sucursal = ($sucursal == '18' && $_POST['localidad'] == 140) ? '3' : $sucursal;
    $radio = $sucursal;
    $sucursal_cobranza_num = $sucursal;
    $medio_valido = 1;
    $rutcentralizado = $empresa_rut;
    $tarjeta_vida = '0';
    $ruta = '';
  } elseif ($medio_pago == '2') {
    $tipo_tarjeta = strtoupper($tipo_tarjeta);
    // Obtener el radio del metodo de pago.
    //NOTA las tarjetas MASTER con bin 504736 se les cambiara el radio al que corresponda segun la tabla de radios
    $bin = substr($numeroTarjeta, 0, 6);
    $qMedio = (strtolower($tipo_tarjeta) == 'master' && ($bin == '504736' || $bin == '589657'))
      ? "SELECT radio FROM radios_tarjetas WHERE bin like '%$bin%'"
      : "SELECT radio FROM radios_tarjetas WHERE nombre_vida LIKE '%$tipo_tarjeta%'";
    $rMedio = mysqli_query($mysqli, $qMedio);
    $row = mysqli_fetch_assoc($rMedio);
    $radio = $radioMesesAdelantados ?? $row['radio'];

    $medio_valido = mysqli_num_rows($rMedio);
    $sucursal_cobranza_num = '99';
    $empresa_marca = '99';
    $rutcentralizado = '99';
  } elseif ($medio_pago == '3') {
    $qMedio = "SELECT * FROM radios_convenios WHERE id = " . $id_convenio;
    $rMedio = mysqli_query($mysqli, $qMedio);
    $row = mysqli_fetch_assoc($rMedio);
    $radio = $row['radio'];
    $tarjeta_vida = '0';
    $medio_valido = mysqli_num_rows($rMedio);

    if ($id_convenio == '26') { //cieloazul
      $empresa_marca = '99';
      $empresa_rut = '07';
    } elseif ($id_convenio == '27') { //Caccepol
      $empresa_marca = '99';
      $empresa_rut = '04';
    }
  } elseif ($medio_pago == '5') {
    $estado = 403;
    $radio = 294;
    $tarjeta_vida = '1';
    $sucursal_cobranza_num = '99';
    $empresa_marca = '99';
    $rutcentralizado = '99';
    $medio_valido = 1;
  }

  $radiosDomiciliarios = array('210', '237', '10914', '209', '150', '292'); //newproducts

  if ($emergencial && in_array($radio, $radiosDomiciliarios)) {
    $radio = '154';
  } elseif ($emergencial && $medio_pago == '2') {
    $radio = '153';
  }

  if ($medio_valido <= 0) {
    $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Medio de pago inválido|$qMedio|" . mysqli_error($mysqli);
    file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    $response['result'] = false;
    $response['message'] = 'Medio de pago inválido.';

    die(json_encode($response));
  }

  $fechafil = date("Y-m-d");
  $medio_pago = (int)$medio_pago;
  $sucursal = (int)$sucursal;

  $empresa_marca = modificarEmpresaMarcaPorRadio($radio, $empresa_marca);

  //datos socio
  $query = "INSERT INTO
                `padron_datos_socio`
            VALUES (
                NULL, '$nombre', '$tel', '$cedula', '$direccion', '$sucursal', '$ruta', '$radio', '1',
                '$fecha_nacimiento', '$edad', '$tipo_tarjeta', '$tipo_tarjeta', '$numeroTarjeta', '$nombreTitular',
                '$cedulaTitular', '$celularTitular', '$anioVencimiento', '$mesVencimiento', $cuotas_mercadopago,
                '$sucursal', $sucursal_cobranzas_num, '$empresa_marca', 1, $count, '$observacion',
                '0', '$idRelacion', '$empresa_rut', $total, 1, 1, 1, '$rutcentralizado', 0, 1,
                'ALTA', 'ALTA', '1', '0', '0', '0', '$fechafil', '0', '0', '0', $medio_pago,
                '$cvv', '$existe_padron', '$mail', '$email_titular', '$tarjeta_vida', $bancoEmisor,
                '$accion', $estado, $id_localidad, '$datoExtra', '$llamada_entrante', '0', '1', '0', $idUser
            )";
  $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR DATOS | query: $query|";
  file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

  $result = mysqli_query($mysqli, $query);
  $idSocioPiscina = mysqli_insert_id($mysqli);
  $error_padron = !$result ? true : false;

  // VIDA ESPECIAL
  $vidaEspecial = [
    ["numero_servicio" => 12, "importe" => 330],
    ["numero_servicio" => 13, "importe" => 50],
    ["numero_servicio" => 14, "importe" => 90]
  ];

  $combo21 = ($edad >= 75) ?
    [
      ['numero_servicio' => '83', 'importe' => 690],
      ['numero_servicio' => '84', 'importe' => 730]
    ]
    :
    [
      ['numero_servicio' => '83', 'importe' => 690],
      ['numero_servicio' => '84', 'importe' => 495]
    ];

  $planPlatino = [
    ["numero_servicio" => '100', "importe" => 550],
    ["numero_servicio" => '101', "importe" => 615],
  ];

  $planOro = [
    ["numero_servicio" => '102', "importe" => 340],
    ["numero_servicio" => '101', "importe" => 615],
  ];

  $promoInternados = [ //seba
    ["numero_servicio" => '56', "importe" => (390 * 12), 'keepprice1' => 390],
    ["numero_servicio" => '89', "importe" => (1220 * 12), 'keepprice1' => 1220],
  ];

  $promoComboSura1 = [
    ["numero_servicio" => '95', "importe" => 110, 'tipo_iva' => 1, 'extra' => '7872'],
    ["numero_servicio" => '105', "importe" => 665, 'tipo_iva' => 2, 'extra' => '7872'],
    ["numero_servicio" => '106', "importe" => 115, 'tipo_iva' => 1, 'extra' => '7872'],
  ];

  $promoComboSura2 = [
    ["numero_servicio" => '56', "importe" => 390, 'tipo_iva' => 2, 'extra' => '0'],
    ["numero_servicio" => '95', "importe" => 110, 'tipo_iva' => 1, 'extra' => '0'],
  ];

  $promoComplementoCompetencia = [
    ["numero_servicio" => '08', "importe" => 35, 'tipo_iva' => 2],
    ["numero_servicio" => '97', "importe" => 510, 'tipo_iva' => 2],
  ];

  $comboAldeasInfantiles = [
    ["numero_servicio" => '112', "importe" => 200, 'tipo_iva' => 2],
    ["numero_servicio" => '113', "importe" => 100, 'tipo_iva' => 99]
  ];

  if ($error_padron) {
    $response = array(
      'result' => false,
      'session' => true,
      'message' => 'Ocurrio un error al guardar datos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.',
    );
    $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio un error al guardar datos en el padron|$query|" . mysqli_error($mysqli);
    file_put_contents($log_file, $log_content . "\n", FILE_APPEND);

    die(json_encode($response));
  }

  $qInsertDireccion = "INSERT INTO
                            `direcciones_socios`
                            (
                                id_socio, calle, puerta ,manzana, solar,
                                apartamento, esquina, referencia, cedula_socio
                            )
                        VALUES
                            (
                                $idSocioPiscina, '$calle', '$puerta', '$manzana', '$solar',
                                '$apto', '$esquina', '$referencia', '$cedula'
                            )";
  $rDir = mysqli_query($mysqli, $qInsertDireccion); //dir2
  if ($rDir) {
    $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR DIRECCION | query: $qInsertDireccion|";
    file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);
  } else {
    $log_content = "[ERROR][$log_date]|$cedula|ALTA|Ocurrio error al guardar direccion |$query|" . mysqli_error($mysqli);
    file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
  }

  $sanatorio = false;
  $convalecencia = false;

  for ($i = 0; $i < count($servicios); $i++) {

    if ($servicios[$i][0] == '41') {
      foreach ($vidaEspecial as $ve) {
        $precio_base = $ve["importe"];
        $nro_servicio = $ve["numero_servicio"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = $servicios[$i][6];
        if ($servicios[$i][0] == '1' && !$sanatorio) {
          $sanatorio = true;
          $precio_base = $servicios[$i][3];
        } elseif ($servicios[$i][0] == '2' && !$convalecencia) {
          $convalecencia = true;
          $precio_base = $servicios[$i][3];
        }

        $cod_promo = ($medio_pago == 2) ? $cod_promo : 0;
        $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2; //newproducts
        $servdecod = in_array($servicios[$i][0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82'))
          ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO
                    `padron_producto_socio`
                    VALUES (
                        NULL, '$cedula', '$nro_servicio', '8', '$precio_base', '$cod_promo', '$fechafil',
                        '0', '$fechafil', '$nombre_vendedor', '$observacion', '0', 0, 999, 'ALTA',
                        '2015-09-15', '$numero_vendedor', $precio_base, '0', 0, $tipo_iva, '$idRelacion',
                        0, '0', '$empresa_marca', 1, '$servdecod', '$count', '1', 'ALTA',
                        '1', '0', '0', '0', '0', '$precio_base', '0', 0, '1', NULL
                    )";
        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '114') {

      foreach ($combo21 as $cb) {
        $precio_base = $cb["importe"];
        $nro_servicio = $cb["numero_servicio"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '23';
        //$hrsServi = ($nro_servicio == '84') ? '0' : '8';
        $hrsServi = '8';
        $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2; //newproducts
        $servdecod = in_array($servicios[$i][0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','$hrsServi','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
        $query .= "'2015-09-15','$numero_vendedor',$precio_base,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precio_base','0',0,'1', NULL)";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '125') {

      foreach ($planPlatino as $pp) {
        $precio_base = $pp["importe"];
        $nro_servicio = $pp["numero_servicio"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '0';
        $hrsServi = '8';
        $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2;
        $servdecod = in_array($servicios[$i][0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','$hrsServi','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
        $query .= "'2015-09-15','$numero_vendedor',$precio_base,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precio_base','0',0,'1', NULL)";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '127') {

      foreach ($planOro as $po) {
        $precio_base = $po["importe"];
        $nro_servicio = $po["numero_servicio"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '0';
        $hrsServi = '8';
        $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2;
        $servdecod = in_array($servicios[$i][0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','$hrsServi','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
        $query .= "'2015-09-15','$numero_vendedor',$precio_base,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precio_base','0',0,'1', NULL)";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '130') {
      //promo internados
      foreach ($promoInternados as $pi) {
        $precio_base = $pi["importe"];
        $nro_servicio = $pi["numero_servicio"];
        $keepprice1 = $pi["keepprice1"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '0';
        $hrsServi = '8';
        $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2;
        $servdecod = in_array($servicios[$i][0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','$hrsServi','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
        $query .= "'2015-09-15','$numero_vendedor',$keepprice1,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precio_base','0',0,'1', NULL)";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '133') {
      //promo Combo Sura 1
      foreach ($promoComboSura1 as $pcs) {
        $precio_base = $pcs["importe"];
        $nro_servicio = $pcs["numero_servicio"];
        $keepprice1 = $pcs["importe"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $hrsServi = '8';
        $tipo_iva = $pcs['tipo_iva'];
        $cod_promo = $servicios[$i][6];
        $servdecod = in_array($servicios[$i][0], array(
          '1',
          '2',
          '3',
          '12',
          '16',
          '37',
          '46',
          '51',
          '56',
          '58',
          '61',
          '63',
          '65',
          '82'
        )) ? $nro_servicio . '8' : $nro_servicio;
        $extra = $pcs['extra'];

        $query = "INSERT INTO
                `padron_producto_socio`
                    VALUES (
                        NULL, '$cedula', '$nro_servicio', '$hrsServi', '$precio_base', '$cod_promo', '$fechafil',
                        '0', '$fechafil', '$nombre_vendedor', '$observacion', '0', 0, 999, 'ALTA', '2015-09-15',
                        '$numero_vendedor', $keepprice1, '0', 0, $tipo_iva, '$idRelacion', 0, '0', '$empresa_marca',
                        1, '$servdecod', '$count', '1', 'ALTA', '1', '0', '0', '$extra', '0', '$precio_base',
                        '0', 0, '1', NULL
                    )";
        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '134') {
      //promo Combo Sura 2
      foreach ($promoComboSura2 as $pcs) {
        $precio_base = $pcs["importe"];
        $nro_servicio = $pcs["numero_servicio"];
        $keepprice1 = $pcs["importe"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '0';
        $hrsServi = '8';
        $tipo_iva = $pcs['tipo_iva'];
        $servdecod = in_array($servicios[$i][0], array(
          '1',
          '2',
          '3',
          '12',
          '16',
          '37',
          '46',
          '51',
          '56',
          '58',
          '61',
          '63',
          '65',
          '82'
        )) ? $nro_servicio . '8' : $nro_servicio;
        $extra = $pcs['extra'];


        $query = "INSERT INTO
                    `padron_producto_socio`
                    VALUES (
                        NULL, '$cedula', '$nro_servicio', '$hrsServi', '$precio_base', '$cod_promo', '$fechafil',
                        '0', '$fechafil', '$nombre_vendedor', '$observacion', '0', 0, 999, 'ALTA', '2015-09-15',
                        '$numero_vendedor', $keepprice1, '0',0,$tipo_iva, '$idRelacion', 0, '0', '$empresa_marca',
                        1, '$servdecod', '$count', '1', 'ALTA', '1', '0', '0', '$extra', '0', '$precio_base',
                        '0', 0, '1', NULL
                    )";
        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '136') {
      //promo COMPLEMENTO COMPETENCIA

      foreach ($promoComplementoCompetencia as $pcc) {
        $precio_base = $pcc["importe"];
        $nro_servicio = $pcc["numero_servicio"];
        $keepprice1 = $pcc["importe"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '0';
        $hrsServi = '8';
        $tipo_iva = $pcc['tipo_iva'];
        $servdecod = in_array($servicios[$i][0], array(
          '1',
          '2',
          '3',
          '12',
          '16',
          '37',
          '46',
          '51',
          '56',
          '58',
          '61',
          '63',
          '65',
          '82'
        )) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO
                    `padron_producto_socio`
                    VALUES (
                        NULL, '$cedula', '$nro_servicio', '$hrsServi', '$precio_base', '$cod_promo', '$fechafil',
                        '0', '$fechafil', '$nombre_vendedor', '$observacion', '0', 0, 999, 'ALTA', '2015-09-15',
                        '$numero_vendedor', $keepprice1, '0', 0, $tipo_iva, '$idRelacion', 0, '0', '$empresa_marca',
                        1, '$servdecod', '$count', '1', 'ALTA', '1', '0', '0', '0', '0', '$precio_base',
                        '0', 0, '1', NULL
                    )";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } elseif ($servicios[$i][0] == '144') {
      //combo Aldeas Infantiles

      foreach ($comboAldeasInfantiles as $cai) {
        $precio_base = $cai["importe"];
        $nro_servicio = $cai["numero_servicio"];
        $keepprice1 = $cai["importe"];
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = '0';
        $hrsServi = '8';
        $tipo_iva = $cai['tipo_iva'];
        $servdecod = in_array($servicios[$i][0], array(
          '1',
          '2',
          '3',
          '12',
          '16',
          '37',
          '46',
          '51',
          '56',
          '58',
          '61',
          '63',
          '65',
          '82'
        )) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO
                    `padron_producto_socio`
                    VALUES (
                        NULL, '$cedula', '$nro_servicio', '$hrsServi', '$precio_base', '$cod_promo', '$fechafil',
                        '0', '$fechafil', '$nombre_vendedor', '$observacion', '0', 0, 999, 'ALTA', '2015-09-15',
                        '$numero_vendedor', $keepprice1, '0', 0, $tipo_iva, '$idRelacion', 0, '0', '$empresa_marca',
                        1, '$servdecod', '$count', '1', 'ALTA', '1', '0', '0', '0', '0', '$precio_base',
                        '0', 0, '1', NULL
                    )";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Datos guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    } else {
      if ($servicios[$i][1] == "") {
        $hrs_servicio = 0;
      } else {
        // Calcula el número de veces que se ingresa el servicio en fraccion de 8 horas
        $hrs_servicio = $servicios[$i][1] / 8;
      }

      if ($servicios[$i][0] == '1') {
        $precio_segundo_modulo_sanatorio = $servicios[$i][4];
      }

      for ($x = 0; $x < $hrs_servicio; $x++) {
        $precio_base = $servicios[$i][4];
        $keepprice1 = $servicios[$i][10];
        $precioOriginal = $keepprice1;
        $observacion = mysqli_real_escape_string($mysqli250, $servicios[$i][5]);
        $cod_promo = $servicios[$i][6];

        if ($servicios[$i][0] == '1' && !$sanatorio) {
          $sanatorio = true;
          $precio_base = $servicios[$i][3];
          $keepprice1 = $servicios[$i][9];
          $precioOriginal = $keepprice1;
        } elseif ($servicios[$i][0] == '2' && !$convalecencia) {
          $convalecencia = true;
          $precio_base = $servicios[$i][3];
          $keepprice1 = $servicios[$i][9];
          $precioOriginal = $keepprice1;
        }

        $nro_servicio = $nros_servicios[$i];

        if ($servicios[$i][0] == '1' && $cod_promo == '20' && ($medio_pago != '1' && $medio_pago != '2' && $radio != '271' && $radio != '1018' && $radio != '292')) { //compe
          $cod_promo = '0';
        }

        $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2; //newproducts
        $servdecod = in_array($servicios[$i][0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nro_servicio . '8' : $nro_servicio;

        $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','8','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
        $query .= "'2015-09-15','$numero_vendedor',$keepprice1,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precioOriginal','0',0,'1', NULL)";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |ALTA| INSERTAR PRODUCTOS | query: $query|";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (mysqli_query($mysqli, $query)) {
          $response["result"] = true;
          $response["message"] = "Servicios guardados correctamente.";
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio error al guardar productos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, true); //dir2
        }
      }
    }
  }

  // ############################ GRUPO FAMILIAR ######################################
  // si existe grupo familiar guardar los datos de los beneficiarios
  if ($grupoFamiliar) {
    for ($i = 0; $i < count($beneficiarios); $i++) {
      $nombre_ben = $beneficiarios[$i][0];
      $cedula_ben = $beneficiarios[$i][1];
      $tel_ben = $beneficiarios[$i][2];
      $fechan_ben = $beneficiarios[$i][3];
      $edad_ben = $beneficiarios[$i][4];
      $idRelacion = $empresa_rut . '-' . $cedula_ben;

      $query = "INSERT INTO padron_datos_socio VALUES (null,'$nombre_ben','$tel_ben','$cedula_ben','$direccion','$sucursal','$ruta','$radio','1','$fechan_ben','$edad_ben','$metodo_vida','$metodo_vida',";
      $query .= "'$numeroTarjeta','$nombreTitular','$cedulaTitular','$celularTitular','$anioVencimiento','$mesVencimiento',$cuotas_mercadopago,'$sucursal',$sucursal_cobranzas_num,'$empresa_marca',1,$count,'$observacion','0','$idRelacion','$empresa_rut',0,";
      $query .= "1,1,1,'$rutcentralizado',0,1,'ALTA','ALTA','1','0','0','0','$fechafil','0','0','0',$medio_pago,'$cvv','$existe_padron', '$mail', '$email_titular','$tarjeta_vida',$bancoEmisor,'$accion',$estado,$id_localidad,'$datoExtra', '$llamada_entrante','0','1', '0', $idUser)";
      $result = mysqli_query($mysqli, $query);

      if ($result) { //dir2
        $idSocioBen = mysqli_insert_id($mysqli);
        $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioBen,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$cedula_ben')";
        $rDir = mysqli_query($mysqli, $qInsertDireccion);
      }

      $qHoras = "SELECT hora FROM padron_producto_socio WHERE servicio in ('63','65') AND cedula = '$cedula'";
      $rHoras = mysqli_query($mysqli, $qHoras);
      $hrs = mysqli_fetch_assoc($rHoras)['hora'];
      $hrs = (int)$hrs / 8;

      for ($y = 0; $y < $hrs; $y++) {
        if ($servicios[$y][1] == "") {
          $hrs_servicio = 0;
        } else {
          // Calcula el número de veces que se ingresa el servicio en fraccion de 8 horas
          $hrs_servicio = $servicios[$y][1] / 8;
        }

        for ($x = 0; $x < $hrs_servicio; $x++) {
          $precio_base = $servicios[$y][4];
          $observacion = mysqli_real_escape_string($mysqli250, $servicios[$y][5]);
          $cod_promo = $servicios[$y][6];

          $nro_servicio = ($nros_servicios[$y] == '63') ? '64' : '66';

          $cod_promo = ($medio_pago == 2) ? $cod_promo : 0;
          if ($servicios[$y][0] != '1') {
            $cod_promo = 0;
          }

          $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2; //newproducts
          $servdecod = in_array(
            $servicios[$y][0],
            array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')
          )
            ? $nro_servicio . '8'
            : $nro_servicio;

          $query = "INSERT INTO padron_producto_socio VALUES (null,'" . $beneficiarios[$i][1] . "','$nro_servicio','8','0','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
          $query .= "'2015-09-15','$numero_vendedor','0','0',0,$tipo_iva,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','0','0',0,'1', '$cedula')";

          if (mysqli_query($mysqli, $query)) {
            $response["result"] = true;
            $response["message"] = "Datos guardados correctamente.";
          } else {
            $response["result"] = false;
            $response["message"] = "Ocurrio error al guardar productos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
            $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron (GRUPO FAMILIAR)|$query|" . mysqli_error($mysqli);
            file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
            eliminarDatos($mysqli, $cedula, false, true); //dir2
          }
        }
      }
    }
  }
  // ############################ GRUPO FAMILIAR ######################################

  #region BENEFICIARIO OMT
  if ($omt === 'true') {
    $nombre_ben = $benOmt[0];
    $cedula_ben = $benOmt[1];
    $tel_ben = $benOmt[2];
    $fechan_ben = $benOmt[3];
    $direccion_ben = substr($benOmt[4], 0, 36);
    $filial = $benOmt[5];
    $edad_ben = $benOmt[6];
    $id_localidad = $benOmt[7];
    $calle_ben = addslashes($benOmt[8]); //dir2
    $puerta_ben = $benOmt[9];
    $apto_ben = $benOmt[10];
    $manzana_ben = $benOmt[11];
    $solar_ben = $benOmt[12];
    $esquina_ben = $benOmt[13];
    $referencia_ben = $benOmt[14];
    $precio_base = $benOmt[15];

    $qSucursal = "SELECT nro_filial from motor_de_precios.filiales where id = $filial";
    $rSucursal = mysqli_query($mysqli1310, $qSucursal);

    // Guardo el número en la variable
    $sucursal = mysqli_fetch_assoc($rSucursal)['nro_filial'];
    $sucursal_cobranzas_num = ($is_mercadopago == '1' || $tarjeta_vida == '1') ? '99' : $sucursal;

    // Obtengo el rut de la empresa segun la filial
    $qERut = "SELECT empresa_rut from aux2 where num_sucursal = $sucursal";
    $rERut = mysqli_query($mysqli250, $qERut);
    $empresa_rut = "";
    if (mysqli_num_rows($rERut)) {
      $empresa_rut = mysqli_fetch_assoc($rERut)['empresa_rut'];
    }
    $idRelacion = $empresa_rut . '-' . $cedula_ben;
    $nro_servicio = '70';

    $empresa_marca = modificarEmpresaMarcaPorRadio($radio, $empresa_marca);

    $query = "INSERT INTO padron_datos_socio VALUES (null,'$nombre_ben','$tel_ben','$cedula_ben','$direccion_ben','$sucursal','$ruta','$radio','1','$fechan_ben','$edad_ben','$tipo_tarjeta','$tipo_tarjeta',";
    $query .= "'$numeroTarjeta','$nombreTitular','$cedulaTitular','$celularTitular','$anioVencimiento','$mesVencimiento',$cuotas_mercadopago,'$sucursal',$sucursal_cobranzas_num,'$empresa_marca',1,$count,'$observacion','0','$idRelacion','$empresa_rut','$precio_base',";
    $query .= "1,1,1,'$rutcentralizado',0,1,'ALTA','ALTA','1','0','0','0','$fechafil','0','0','0',$medio_pago,'0','0', '0', '$email_titular','$tarjeta_vida',$bancoEmisor,'1',$estado,$id_localidad,'$datoExtra', '0','0','1', '0', $idUser)";

    $result = mysqli_query($mysqli, $query);
    if ($result) {

      $idSocioBen = mysqli_insert_id($mysqli); //dir2
      $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioBen,'$calle_ben','$puerta_ben','$manzana_ben','$solar_ben','$apto_ben','$esquina_ben','$referencia_ben','$cedula_ben')";
      $rDir = mysqli_query($mysqli, $qInsertDireccion);

      $hrs_servicio = 8;
      $observacion = 'socio omt';
      $cod_promo = 0;
      $servdecod = $nro_servicio;
      $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula_ben','$nro_servicio','8','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
      $query .= "'2015-09-15','$numero_vendedor','0','0',0,2,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precio_base','0',0,'1', '$cedula')";

      if (mysqli_query($mysqli, $query)) {
        $response["result_omt"] = true;
        $response["message_omt"] = "Datos guardados correctamente.";
      } else {
        $response["result_omt"] = false;
        $response["message_omt"] = "Ocurrio error al guardar productos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
        $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron (OMT)|$query|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
      }
    }
  }
  #endregion


  $qId = "SELECT id FROM padron_datos_socio WHERE cedula='$cedula'";
  if ($r = mysqli_query($mysqli, $qId)) {
    $id_padron = mysqli_fetch_assoc($r)['id'];

    if ($es_vuelve_antes) { //SI ES PROMO VUELVE ANTES
      $qHistoricoVuelveAntes = "INSERT INTO historico_venta VALUES (null,30,$id_padron,689,'$fecha','PROMO VUELVE ANTES',11)";
      $rHistoricoVuelveAntes = mysqli_query($mysqli, $qHistoricoVuelveAntes);
    }
    $qHistrocio = "INSERT INTO
                        historico_venta
                    VALUES (
                        NULL, 30, $id_padron, $estado, '$fecha', 'ALTA A TRAVES DE CALL', 11
                    )";
    if (mysqli_query($mysqli, $qHistrocio)) {
      $id_historico = mysqli_insert_id($mysqli);
      $response['historico'] = 'Historico guardado correctamente';
    } else {
      $response['historico'] = 'Error al guardar el historico';
      $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Error al guardar el historico|$query|" . mysqli_error($mysqli);
      file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }
  }

  $validarDatos = validarRegistros($mysqli, $cedula, $socio, $servicios, $id_historico, $total, true); //dir2

  if (!$validarDatos) {
    $response["result"] = false;
    $response["message"] = "Ocurrio error al guardar datos por favor intente cargar el contrato de nuevo. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";

    die(json_encode($response));
  }

  if ($esPromoCompetencia == 'true' || $esPromoCompetenciaVeintitres == 'true')
    enviarSMS($nombre, $tel);

  #region PROMO VISA
  if (mb_strtolower(trim($tipo_tarjeta)) === 'visa' && limiteDePromoVisa(100)) {
    $altasConLaMismaTarjeta = buscarAltaConMismaTarjeta($numeroTarjeta, $cedula);

    if (!empty($altasConLaMismaTarjeta)) {
      foreach ($altasConLaMismaTarjeta as $alta)

        if (!in_array($alta['cod_promo'], ['35', '2035', '31', '2031']))
          aplicarCodigoPromoPorId($alta['pps_id'], CODIGO_PROMO_VISA);
      if ($esPromoCompetencia == 'false' && $esPromoCompetenciaVeintitres == 'false')
        aplicarCodigoPromoPorCedula($cedula, CODIGO_PROMO_VISA);
    }
  }
  #endregion
} else {
  // ############################## INCREMENTO ##############################
  /*
 Acciones para aplicar a padron_datos y padron_productos
 1 => insertar
 2 => eliminar
 3 => mantener
 4 => modifica
 */

  # extrae los ids del array de servicios actuales
  function extraerIds($arr)
  {
    return (isset($arr['id_servicio'])) ? $arr['id_servicio'] : $arr[0]; //conva
  }


  $promoIncrementoSura = [
    ["numero_servicio" => '06', "importe" => 55, 'tipo_iva' => 2, 'extra' => '0'],
    ["numero_servicio" => '95', "importe" => 110, 'tipo_iva' => 1, 'extra' => '0'],
  ];

  $nuevos_servicios = json_decode($_POST["servicios"], true); // nuevos servicios
  $servicios_actuales = $_POST["serviciosActuales"]; // actuales

  $medio_pago = $_POST["medio_pago"];
  $id_padron = $_POST["id_padron"];
  $cedula = mysqli_real_escape_string($mysqli250, $_POST["cedula"]);
  $nombre = $_POST["nombre"];
  $fecha_nacimiento = $_POST["fechaNacimiento"];
  $celular = $_POST["celular"];
  $telefono = $_POST["telefono"];
  $telefonoAlternativo = $_POST["telefonoAlternativo"];
  $direccion = $_POST["direccion"];
  $calle = addslashes($_POST["calle"]); //dir2
  $puerta = $_POST["puerta"];
  $apto = $_POST["apto"];
  $manzana = $_POST["manzana"];
  $solar = $_POST["solar"];
  $esquina = $_POST["esquina"];
  $referencia = $_POST["referencia"];
  $fechaFil = date('Y-m-d');
  $filial = $_POST["filial"];
  $observacion = trim($_POST['observacion']);
  $empresa_rut = $_POST['empresa_rut'];
  $total_anterior = $_POST['total_importe'];
  $cedula_titular = mysqli_real_escape_string(
    $mysqli250,
    !empty($_POST["cedulaTitular"])
      ? $_POST["cedulaTitular"]
      : $_POST["cedulaTitularConvenio"]
  );
  $nombre_titular = mysqli_real_escape_string(
    $mysqli250,
    !empty($_POST["nombreTitular"])
      ? $_POST["nombreTitular"]
      : $_POST["nombreTitularConvenio"]
  );
  $email_titular = $_POST['mailTitular'];
  $total_actual = $_POST['total'];
  $total_neto = $total_anterior + $total_actual;
  $existe_padron = '1';
  $cvv = $_POST['cvv'] ? $_POST['cvv'] : 0;
  $medio_pago_anterior = $_POST['medioPagoActual'];
  $tipo_tarjeta = $_POST['tipo_tarjeta'];
  $numero_tarjeta = $_POST['numero_tarjeta'];
  $cuotas_mercadopago = isset($_POST["cuotas"]) ? mysqli_real_escape_string($mysqli250, $_POST["cuotas"]) : 1; //seba
  $anioVencimiento = $_POST['anioVencimiento'];
  $mesVencimiento = $_POST['mesVencimiento'];
  $is_mercadopago = $_POST['is_mercadopago'];
  $id_convenio = $_POST['idConvenio'];
  $localidadAnterior = $_POST['localidadAnterior'];
  $localidad = $_POST['localidad'];
  $estado = 1;
  $id_sucursal = $localidad ? $localidad : $localidadAnterior;
  $sucursal = $filial;
  $fecha = date('Y-m-d H:i');
  $datoExtra = '3';
  $telefonos = $celular . ' ' . $telefono . ' ' . $telefonoAlternativo;
  $bancoEmisor = !empty($_POST['bancoEmisor']) ? $_POST['bancoEmisor'] : 0;
  $email = $_POST["mail"];
  $ruta = $_POST["ruta"];
  $radio = $_POST["radio"];
  $activo = $_POST["activo"];
  $edad = $_POST["edad"];
  $tarjeta = $_POST["tarjeta"];
  $tipo_tarjeta = $_POST["tipo_tarjeta"];
  $numero_tarjeta = $_POST["numero_tarjeta"];
  $nombreTitularAnterior = $_POST["nombreTitularAnterior"];
  $telefono_titular = $_POST["celularTitular"] . ' ' . $_POST["telefonoTitular"];
  $anio_e = $_POST["anio_e"];
  $mes_e = $_POST["mes_e"];
  $sucursal_cobranzas = $_POST["sucursal_cobranzas"];
  $sucursal_cobranza_num = $_POST["sucursal_cobranzas_num"];
  $empresa_marca = $_POST["empresa_marca"];
  $flag = $_POST["flag"];
  $count = $_POST["count"];
  $observaciones = mysqli_real_escape_string($mysqli250, $_POST['observaciones']);
  $grupo = $_POST["grupo"];
  $idrelacion = $_POST["idrelacion"];
  $total_importe = $_POST["total_importe"];
  $nactual = $_POST["nactual"];
  $version = $_POST["version"];
  $flagchange = $_POST["flagchange"];
  $rutcentralizado = $_POST["rutcentralizado"];
  $print = $_POST["print"];
  $emitido = $_POST["emitido"];
  $movimientoabm = $_POST["movimientoabm"];
  $abm = $_POST["abm"];
  $abmactual = $_POST["abmactual"];
  $check = $_POST["check"];
  $usuario = $_POST["usuario"];
  $usuariod = $_POST["usuariod"];
  $radioViejo = $_POST["radioViejo"];
  $extra = $_POST["extra"];
  $nomodifica = $_POST["nomodifica"];
  $accion_socio = '3';
  $anio_e = ($anio_e) == '' ? 0 : $anio_e;
  $mes_e = ($mes_e) == '' ? 0 : $mes_e;
  $idServiciosActuales = array_map('extraerIds', $servicios_actuales);
  $tieneConvalecencia = in_array('2', $idServiciosActuales);
  $idServiciosNuevos = array_map('extraerIds', $nuevos_servicios); //conva
  $contrataConvalecencia = in_array('2', $idServiciosNuevos); //conva verificamos que haya contratado convalecencia

  // Traigo el número de la filial segun la localidad del cliente
  $qSucursal = "SELECT nro_filial from motor_de_precios.filiales where id = $filial";
  $rSucursal = mysqli_query($mysqli1310, $qSucursal);
  if (mysqli_num_rows($rSucursal) == 1) {
    $sucursal = mysqli_fetch_assoc($rSucursal)['nro_filial'];
  }
  $sucursal = ($sucursal != '0') ? preg_replace('/^0+/', '', $sucursal) : $sucursal;

  // Traigo la sucursal anterior y el radio anterior
  $qSucursalAnterior = "SELECT sucursal,radio FROM padron_datos_socio where cedula = '$cedula'";
  if ($rSucursalAnt = mysqli_query($mysqli250, $qSucursalAnterior)) {
    while ($row = mysqli_fetch_assoc($rSucursalAnt)) {
      $sucursalAnterior = $row['sucursal'];
      $radioAnterior = $row['radio'];
    }
  }

  // Obtengo el rut de la empresa segun la filial
  $qERut = "SELECT empresa_rut from aux2 where num_sucursal = $sucursal limit 1";
  $rERut = mysqli_query($mysqli250, $qERut);
  $empresa_rut = mysqli_fetch_assoc($rERut)['empresa_rut'];

  $qEmarca = "SELECT empresa_brand from aux2 where num_sucursal = $sucursal";
  $rEmarca = mysqli_query($mysqli250, $qEmarca);
  $empresa_marca = mysqli_fetch_assoc($rEmarca)['empresa_brand'];
  $idrelacion = $empresa_rut . '-' . $cedula;

  if ($medio_pago == '1') {
    $sucursal = ($sucursal == '18' && $_POST['localidad'] == 140) ? '3' : $sucursal;
    $radio = $sucursal;
    $sucursal_cobranza_num = $sucursal;
    $medio_valido = 1;
    $rutcentralizado = $empresa_rut;
    $tarjeta_vida = '0';
  } elseif ($medio_pago == '2') {
    $tipo_tarjeta = strtoupper($tipo_tarjeta);
    // Obtener el radio del metodo de pago.
    // NOTA las tarjetas MASTER con bin 504736 se les cambiara el radio al que corresponda segun la tabla de radios
    $bin = substr($numero_tarjeta, 0, 6);
    $qMedio = (strtolower($tipo_tarjeta) == 'master' && ($bin == '504736' || $bin == '589657'))
      ? "SELECT radio FROM radios_tarjetas WHERE bin like '%$bin%'"
      : "SELECT radio FROM radios_tarjetas WHERE nombre_vida LIKE '%$tipo_tarjeta%'";
    $rMedio = mysqli_query($mysqli, $qMedio);
    $row = mysqli_fetch_assoc($rMedio);
    // $metodo_vida = $row['nombre_vida'];
    $radio = $row['radio'];
    $medio_valido = mysqli_num_rows($rMedio);
    $sucursal_cobranza_num = '99';
    $empresa_marca = '99';
    $rutcentralizado = '99';
    $tarjeta_vida = ($is_mercadopago == '1') ? '0' : '1';
    $ruta = '0000000000';
  } elseif ($medio_pago == '3') {
    $qMedio = "SELECT * FROM radios_convenios WHERE id = " . $id_convenio;
    $rMedio = mysqli_query($mysqli, $qMedio);
    $row = mysqli_fetch_assoc($rMedio);
    $radio = $row['radio'];
    $tarjeta_vida = '0';
    $medio_valido = mysqli_num_rows($rMedio);
    $sucursal_cobranza_num = $filial;
    $ruta = '0000000000';
  } elseif ($medio_pago == '5') {
    $estado = 403;
    $radio = 294;
    $tarjeta_vida = '1';
    $sucursal_cobranza_num = '99';
    $empresa_marca = '99';
    $rutcentralizado = '99';
    $medio_valido = 1;
    $ruta = '0000000000';
  }

  if ($medio_pago_anterior && $medio_pago_anterior != $medio_pago) {
    $accion_socio = '4';
    if ($medio_pago == '1') {
      $print = 1;
    } elseif ($medio_pago == '2') {
      $print = 0;
    }
  }

  // Comprueba si hay cambios en los datos del socio
  if (($anioVencimiento && $anioVencimiento != $anio_e) ||
    ($mesVencimiento && $mesVencimiento != $mes_e) ||
    ($nombre_titular && $nombre_titular != $nombreTitularAnterior) ||
    (!empty($localidad) && $localidad != $localidadAnterior)
  ) {
    $accion_socio = '4';
  }

  $anio_e = ($anioVencimiento != '') ? $anioVencimiento : $anio_e;
  $mes_e = ($mesVencimiento != '') ? $mesVencimiento : $mes_e;
  $nombre_titular = $nombre_titular ? $nombre_titular : $nombreTitularAnterior;

  # comprueba si existe en piscina
  $query = "SELECT * FROM padron_datos_socio WHERE cedula='$cedula'";
  $result = mysqli_query($mysqli, $query);
  if (mysqli_num_rows($result) > 0) {
    // ACTUALIZA LOS DATOS
    $idSocioPiscina = mysqli_fetch_assoc($result)['id']; //dir2
    $query = "UPDATE
            `padron_datos_socio`
        SET
            `nombre` = '$nombre',
            `tel` = '$telefonos',
            `direccion` = '$direccion',
            `sucursal` = '$sucursal',
            `ruta` = '$ruta',
            `radio` = '$radio',
            `activo` = '$activo',
            `fecha_nacimiento` = '$fecha_nacimiento',
            `edad` = $edad,
            `tipo_tarjeta` = '$tipo_tarjeta',
            `numero_tarjeta` = '$numero_tarjeta',
            `nombre_titular` = '$nombre_titular',
            `cedula_titular` = '$cedula_titular',
            `telefono_titular` = '$telefono_titular',
            `anio_e` = '$anio_e',
            `mes_e` = '$mes_e',
            `sucursal_cobranzas` = '$sucursal_cobranzas',
            `sucursal_cobranza_num` = '$sucursal_cobranza_num',
            `empresa_marca` = '$empresa_marca',
            `flag` = '$flag',
            `count` = $count,
            `observaciones` = '$observaciones',
            `grupo` = '$grupo',
            `idrelacion` = '$idrelacion',
            `empresa_rut` = '$empresa_rut',
            `total_importe` = '$total_actual',
            `nactual` = '$nactual',
            `version` = $version,
            `flagchange` = $flagchange,
            `rutcentralizado` = '$rutcentralizado',
            `PRINT` = $print,
            `EMITIDO` = $emitido,
            `movimientoabm` = '$movimientoabm',
            `abm` = '$abm',
            `abmactual` = '$abmactual',
            `check` = '$check',
            `usuario` = '$usuario',
            `usuariod` = '$usuariod',
            `fechaFil` = '$fechaFil',
            `radioViejo` = '$radioViejo',
            `extra` = '$extra',
            `nomodifica` = '$nomodifica',
            `metodo_pago` = '$medio_pago',
            `cvv` = '$cvv',
            `existe_padron` = '$existe_padron',
            `email` = '$email',
            `email_titular` = '$email_titular',
            `banco_emisor` = '$bancoEmisor',
            `accion` = '4',
            `estado` = 1,
            `localidad` = $id_sucursal,
            `dato_extra` = '$datoExtra',
            `origen_venta` = '0',
            `alta` = '0',
            `id_usuario` = $idUser
        WHERE
            `cedula` = '$cedula'";

    $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |INCREMENTO| ACTUALIZAR DATOS EN PISCINA | query: $query ";
    file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

    if (!mysqli_query($mysqli, $query)) {
      $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|ERROR AL ACTUALIZAR SOCIO EN PISCINA|$query|" . mysqli_error($mysqli);
      file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    } else { //dir2
      $qBuscarDir = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
      $rBuscarDir = mysqli_query($mysqli, $qBuscarDir);

      if ($rBuscarDir && mysqli_num_rows($rBuscarDir) > 0) {

        $qUpdateDir = "UPDATE direcciones_socios SET calle = '$calle', puerta = '$puerta', apartamento = '$apto', manzana = '$manzana', solar = '$solar', esquina = '$esquina', referencia = '$referencia' WHERE cedula_socio = '$cedula'";
        $rUpdatedir = mysqli_query($mysqli, $qUpdateDir);
      } else {

        $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPiscina,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$cedula')";
        $rDir = mysqli_query($mysqli, $qInsertDireccion);
      }
    }

    // ELIMINO LOS SERVICIOS DE LA PSCINA
    mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula='$cedula'");
  } else {

    $empresa_marca = modificarEmpresaMarcaPorRadio($radio, $empresa_marca);

    $query = "INSERT INTO
            `padron_datos_socio`
            VALUES (
                NULL, '$nombre', '$telefonos', '$cedula', '$direccion', '$sucursal', '$ruta', '$radio',
                '$activo', '$fecha_nacimiento', $edad, '$tipo_tarjeta', '$tipo_tarjeta', '$numero_tarjeta',
                '$nombre_titular', '$cedula_titular', '$telefono_titular', '$anio_e', '$mes_e',
                $cuotas_mercadopago, '$sucursal_cobranzas', '$sucursal_cobranza_num', '$empresa_marca',
                '$flag', $count, '$observaciones', '$grupo', '$idrelacion', '$empresa_rut', $total_actual,
                $nactual, $version, $flagchange, '$rutcentralizado', $print, $emitido, '$movimientoabm', '$abm',
                '$abmactual', '$check', '$usuario', '$usuariod', '$fechaFil', '$radioViejo', '$extra',
                '$nomodifica', '$medio_pago', $cvv, '$existe_padron', '$email', '$email_titular', '$tarjeta_vida',
                '$bancoEmisor', '$accion_socio', $estado, $localidad, '$datoExtra', '0', '0', '0', '0', $idUser
            )";

    $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |INCREMENTO| INSERTAR DATOS EN PISCINA | query: $query ";
    file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

    $rInsert = mysqli_query($mysqli, $query); //dir2
    if (!$rInsert) {
      $response['result'] = false;
      $response['message'] = 'Error al guardar datos del socio';
      $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|ERROR AL GUARDARO SOCIO EN PISCINA|$query|" . mysqli_error($mysqli);
      file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
      eliminarDatos($mysqli, $cedula, false, false); //dir2
    } else {

      $idSocioPiscina = mysqli_insert_id($mysqli); //dir2
      $qBuscarDir = "SELECT * FROM direcciones_socios WHERE cedula_socio = '$cedula'";
      $rBuscarDir = mysqli_query($mysqli, $qBuscarDir);

      if ($rBuscarDir && mysqli_num_rows($rBuscarDir) > 0) {

        $qUpdateDir = "UPDATE direcciones_socios SET calle = '$calle', puerta = '$puerta', apartamento = '$apto', manzana = '$manzana', solar = '$solar', esquina = '$esquina', referencia = '$referencia' WHERE cedula_socio = '$cedula'";
        $rUpdatedir = mysqli_query($mysqli, $qUpdateDir);
      } else {

        $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioPiscina,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$cedula')";
        $rDir = mysqli_query($mysqli, $qInsertDireccion);
      }
    }
  }

  //ciclo recorre los servicios que ya tenia (servicios actuales)
  for ($i = 0; $i < count($servicios_actuales); $i++) {
    if ($servicios_actuales[$i]['num_servicio'] != '70') {
      // $idRelacion = $empresa_rut . "-" . $cedula;
      $nro_servicio = $servicios_actuales[$i]['num_servicio'];
      $precio_base = $servicios_actuales[$i]['importe'];
      $observaciones = mysqli_real_escape_string($mysqli250, $servicios_actuales[$i]['observaciones']);
      $cod_promo = '';
      $id_padron = $servicios_actuales[$i]['id_padron'];
      $fecha_registro = $servicios_actuales[$i]['fecha_registro'];
      $fecha_afiliacion = $servicios_actuales[$i]['fecha_afiliacion'];
      $count = $servicios_actuales[$i]['count'];
      $nombreVendedor = $servicios_actuales[$i]['nombre_vendedor'];
      $cedulaVendedor = $servicios_actuales[$i]['numero_vendedor'];
      $accion = '3';
      $tipo_iva = in_array($nro_servicio, $arrServiciosIva1) ? 1 : 2; //newproducts
      $servdecod = in_array(
        $servicios_actuales[$i]['id_servicio'],
        array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')
      ) ? $nro_servicio . '8' : $nro_servicio;

      $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','8','$precio_base','0','$fecha_registro','0','$fecha_registro','$nombreVendedor','$observaciones','0',0,999,'ALTA',";
      $query .= "'2015-09-15','$cedulaVendedor',$precio_base,'0',0,$tipo_iva,'$idrelacion',0,'0','$empresa_rut',1,'$servdecod',$count,'1','0','0','0','0','0','0','$precio_base','0','$id_padron','$accion', NULL)";

      //ERROR DE PRECIOS INCREMENTOS

      $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |INCREMENTO| INSERTAR PRODUCTOS ACTUALES| query: $query ";
      file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

      $result = mysqli_query($mysqli, $query);

      if ($result) {
        $query = "UPDATE padron_datos_socio SET total_importe=$total_actual WHERE cedula='$cedula'";
        mysqli_query($mysqli, $query);
        $response["result"] = true;
        $response["message"] = "Datos guardados correctamente.";
      } else {
        $response["result"] = false;
        $response["message"] = "Ocurrio un error al guardar productos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
        $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|Ocurrio un error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
        eliminarDatos($mysqli, $cedula, false, false); //dir2
      }
    }
  }

  $sanatorio = false;
  $convalecencia = false;

  // ciclo recorre los nuevos servicios (incrementos)
  for ($i = 0; $i < count($nuevos_servicios); $i++) {
    $accion = '1';

    // Calcula el número de veces que se ingresa el servicio en fracción de 8 horas
    $hrs_servicio = isset($nuevos_servicios[$i][1]) ? $nuevos_servicios[$i][1] / 8 : 0;
    // INCREMENTO DEL SERVICIO
    // COMPRUEBO SI EL PRODUCTO ES EL MISMO Y TIENE DIFERENTES HORAS DE SERVICIOS
    // ENTONCES SE REALIZA UN INCREMENTO DEL SERVICIO

    $z = 0;
    $existe = false;
    while ($z < count($servicios_actuales) && !$existe) {
      // Servicio acotado
      if ($hrs_servicio == 0 && $nuevos_servicios[$i][0] == $servicios_actuales[$z]['id_servicio']) {
        $existe = true;
        // Si el producto existe y el total de horas son distintas, quiere decir que es un incremento
        // por lo que se calcula el número de horas que tiene el servicio.
      } elseif (
        ($nuevos_servicios[$i][0] == $servicios_actuales[$z]['id_servicio'])
        && ($nuevos_servicios[$i][1] > $servicios_actuales[$z]['horas_totales'])
      ) {
        $hrs_servicio = ($nuevos_servicios[$i][1] - $servicios_actuales[$z]['horas_totales']) / 8;
        break;
      } elseif (
        ($nuevos_servicios[$i][0] == $servicios_actuales[$z]['id_servicio'])
        && ($nuevos_servicios[$i][1] == $servicios_actuales[$z]['horas_totales'])
      ) {
        $accion = '3';
      }
      $z++;
    }

    if ($contrataConvalecencia && $nuevos_servicios[$i][0] == '2') { //conva
      $horasConvalecencia = 0;
      $qHorasConvaPromo = "SELECT sum(hora) as hora FROM padron_producto_socio WHERE cedula ='$cedula' AND servicio='02'";
      $rHorasConvaPromo = mysqli_query($mysqli250, $qHorasConvaPromo);
      $horasConvalecencia = ($rHorasConvaPromo && mysqli_num_rows($rHorasConvaPromo) > 0)
        ? (int)mysqli_fetch_assoc($rHorasConvaPromo)['hora'] / 8 : $horasConvalecencia;

      if (($hrs_servicio + $horasConvalecencia) > 3) {
        mysqli_query($mysqli250, "DELETE FROM padron_producto_socio WHERE cedula='$cedula' AND servicio='02' AND cod_promo='24'");
      }
    }

    $numero_vendedor = $_SESSION["cedulaUsuario"];
    $nombre_vendedor = $_SESSION["nombreUsuario"];
    $nro_servicio = $nros_servicios[$i];
    $precio_base = $nuevos_servicios[$i][4];
    $observacion = mysqli_real_escape_string($mysqli250, $nuevos_servicios[$i][5]);
    $cod_promo = $nuevos_servicios[$i][6];
    $fechareg = date('Y-m-d');
    $servdecod2 = ($nuevos_servicios[$i][0] == '1' || $nuevos_servicios[$i][0] == '2' || $nuevos_servicios[$i][0] == '3') ? $nros_servicios[$i] . '8' : $nros_servicios[$i];

    if ($hrs_servicio == 0 && !$existe) {
      //seba aca1
      if ($nro_servicio == '135') { //si es incremento combo sura 3
        foreach ($promoIncrementoSura as $pis) { //seba sura
          $precio_base = $pis["importe"];
          $nro_servicio = $pis["numero_servicio"];
          $tipo_iva = $pis['tipo_iva'];
          $extra = $pis['extra'];

          $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','8','$precio_base','$cod_promo','$fechareg','0','$fechareg','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
          $query .= "'2015-09-15','$numero_vendedor',$precio_base,'0',0,$tipo_iva,'$idrelacion',0,'0','$empresa_rut',1,'$servdecod2','0','1','ALTA-PRODUCTO','1','0','0','$extra','0','$precio_base',0,$id_padron,'$accion', NULL)";

          $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |INCREMENTO| INSERTAR PRODUCTOS NUEVOS | query: $query ";
          file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

          if (!mysqli_query($mysqli, $query)) {
            $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|Ocurrio un error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
            file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
            eliminarDatos($mysqli, $cedula, false, false); //dir2
          }
        }
      } else {
        $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','8','$precio_base','$cod_promo','$fechareg','0','$fechareg','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
        $query .= "'2015-09-15','$numero_vendedor',$precio_base,'0',0,2,'$idrelacion',0,'0','$empresa_rut',1,'$servdecod2','0','1','ALTA-PRODUCTO','1','0','0','0','0','$precio_base',0,$id_padron,'$accion', NULL)";

        $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |INCREMENTO| INSERTAR PRODUCTOS NUEVOS | query: $query ";
        file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

        if (!mysqli_query($mysqli, $query)) {
          $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|Ocurrio un error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
          eliminarDatos($mysqli, $cedula, false, false); //dir2
        }
      }
    }

    // ciclo inserta los servicios tradicionales
    for ($x = 0; $x < $hrs_servicio; $x++) {
      $c = 0;
      $precio_base = $servicios[$i][4];

      if ($nuevos_servicios[$i][0] == '1' && !$sanatorio) {
        $sanatorio = true;
        if ($incrementoOmt === 'true') {
          $c = $count;
          $incrementoOmt = false;
        }
        if ($hrs_servicio > 2) {
          $precio_base = $nuevos_servicios[$i][3];
        }
      } elseif ($nuevos_servicios[$i][0] == '2' && !$convalecencia) {
        $convalecencia = true;
        $precio_base = (!$tieneConvalecencia) ? $nuevos_servicios[$i][3] : $nuevos_servicios[$i][4];
      }

      $tipo_iva = in_array($nuevos_servicios[$i][0], $arrServiciosIva1) ? 1 : 2; //newproducts
      $servdecod = in_array(
        $nuevos_servicios[$i][0],
        array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')
      )
        ? $nuevos_servicios[$i][0] . '8' : $nuevos_servicios[$i][0];

      $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula','$nro_servicio','8','$precio_base','0','$fechareg','0','$fechareg','$nombre_vendedor','$observacion','0',0,999,'ALTA',";
      $query .= "'2015-09-15','$numero_vendedor','$precio_base','0',0,$tipo_iva,'$idrelacion',0,'0','$empresa_rut',1,'$servdecod','$c','1','ALTA-PRODUCTO','1','0','0','0','0','$precio_base',0,'$id_padron','$accion', NULL)";
      $result = mysqli_query($mysqli, $query);

      $log_content = "[LOG][$log_date][$log_hour]|CI: $cedula |INCREMENTO| INSERTAR PRODUCTOS NUEVOS| query: $query ";
      file_put_contents($log_file_step, $log_content . "\n", FILE_APPEND);

      if (!$result) {
        $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|Ocurrio un error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
        eliminarDatos($mysqli, $cedula, false, false); //dir2
      }
    }

    if ($result) {
      $query = "UPDATE padron_datos_socio SET total_importe=$total_actual WHERE cedula='$cedula'";
      mysqli_query($mysqli, $query);
      $response["result"] = true;
      $response["message"] = "Datos guardados correctamente.";
    } else {
      $response["result"] = false;
      $response["message"] = "Ocurrio un error al guardar los datos. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
      $log_content = "[ERROR][$log_date]|$numero_vendedor|INCREMENTO|Ocurrio un error al guardar los datos|$query";
      file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
      eliminarDatos($mysqli, $cedula, false, false); //todo
    }
  }
  $qId = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
  if ($r = mysqli_query($mysqli, $qId)) {
    $id_padron = mysqli_fetch_assoc($r)['id'];

    $qHistorico = "INSERT INTO historico_venta VALUES (null,30,$id_padron,1,'$fecha','INCREMENTO A TRAVES DE CALL',11)";
    if (mysqli_query($mysqli, $qHistorico)) {
      $id_historico = mysqli_insert_id($mysqli);
      $response['historico'] = 'Historico guardado correctamente';
    } else {
      $response['historico'] = 'Error al guardar el historico';
      $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Error al guardar el historico|$query|" . mysqli_error($mysqli);
      file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
      eliminarDatos($mysqli, $cedula, false, false); //dir2
    }
  }

  $validarDatos = validarRegistros($mysqli, $cedula, $socio, $servicios, $id_historico, $total_actual, false); //dir2

  if (!$validarDatos) {
    $response["result"] = false;
    $response["message"] = "Ocurrio error al guardar datos por favor intente cargar el contrato de nuevo. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
  }
}


#region BENEFICIARIO PROMO MAMÁ
if ($promoMesMama === 'true') {

  $nombre_ben = $benMama[0];
  $cedula_ben = $benMama[1];
  $tel_ben = $benMama[2];
  $direccion_ben = substr($benMama[4], 0, 36);
  $filial = $benMama[5];
  $edad_ben = $benMama[6];
  $id_localidad = $benMama[7];
  $calle_ben = addslashes($benMama[8]); //dir2
  $puerta_ben = $benMama[9];
  $apto_ben = $benMama[10];
  $manzana_ben = $benMama[11];
  $solar_ben = $benMama[12];
  $esquina_ben = $benMama[13];
  $referencia_ben = $benMama[14];
  $precio_base = 0;
  $keep_price = $benMama[15];
  $numeroTarjeta = isset($numeroTarjeta) ? $numeroTarjeta : $numero_tarjeta;
  $nombreTitular = isset($nombreTitular) ? $nombreTitular : $nombre_titular;
  $cedulaTitular = isset($cedulaTitular) ? $cedulaTitular : $cedula_titular;
  $celularTitular = isset($celularTitular) ? $celularTitular : $telefono_titular;
  $estadoMadre = '1';
  $observacionMadre = 'PROMO MES DE LA MADRE';

  $qSucursal = "SELECT nro_filial from motor_de_precios.filiales where id = $filial";
  $rSucursal = mysqli_query($mysqli1310, $qSucursal);

  // Guardo el número en la variable
  $sucursal = mysqli_fetch_assoc($rSucursal)['nro_filial'];
  $sucursal_cobranzas_num = ($is_mercadopago == '1' || $tarjeta_vida == '1') ? '99' : $sucursal;

  // Obtengo el rut de la empresa segun la filial
  $qERut = "SELECT empresa_rut from aux2 where num_sucursal = $sucursal";
  $rERut = mysqli_query($mysqli250, $qERut);
  $empresa_rut = "";
  if (mysqli_num_rows($rERut)) {
    $empresa_rut = mysqli_fetch_assoc($rERut)['empresa_rut'];
  }
  $idRelacion = $empresa_rut . '-' . $cedula_ben;
  $nro_servicio = '01';

  $empresa_marca = modificarEmpresaMarcaPorRadio($radio, $empresa_marca);

  $query = "INSERT INTO padron_datos_socio VALUES (null,'$nombre_ben','$tel_ben','$cedula_ben','$direccion_ben','$sucursal','$ruta','$radio','1',NOW(),'$edad_ben','$tipo_tarjeta','$tipo_tarjeta',";
  $query .= "'$numeroTarjeta','$nombreTitular','$cedulaTitular','$celularTitular','$anioVencimiento','$mesVencimiento',$cuotas_mercadopago,'$sucursal',$sucursal_cobranzas_num,'$empresa_marca',1,0,'$observacionMadre','0','$idRelacion','$empresa_rut','$precio_base',";
  $query .= "1,1,1,'$rutcentralizado',0,1,'ALTA','ALTA','1','0','0','0',NOW(),'0','0','0',$medio_pago,'0','0', '0', '$email_titular','$tarjeta_vida',$bancoEmisor,'1', '$estadoMadre',$id_localidad,'$datoExtra', '0','0','1', '0', $idUser)";

  $result = mysqli_query($mysqli, $query);
  if ($result) {

    $idSocioBen = mysqli_insert_id($mysqli); //dir2
    $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioBen,'$calle_ben','$puerta_ben','$manzana_ben','$solar_ben','$apto_ben','$esquina_ben','$referencia_ben','$cedula_ben')";
    $rDir = mysqli_query($mysqli, $qInsertDireccion);

    $hrs_servicio = 8;
    $observacion = 'socio mama';
    $cod_promo = 27;
    $servdecod = $nro_servicio;
    $query = "INSERT INTO padron_producto_socio VALUES (null,'$cedula_ben','$nro_servicio','8','$precio_base','$cod_promo',NOW(),'0',NOW(),'$nombre_vendedor','$observacion','0',0,999,'ALTA',";
    $query .= "'2015-09-15','$numero_vendedor','$keep_price','0',0,2,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod',0,'1','ALTA','1','0','0','0','0','$keep_price','0',0,'1', '$cedula')";

    if (mysqli_query($mysqli, $query)) {
      $response["result_mama"] = true;
      $response["message_mama"] = "Datos guardados correctamente.";
    } else {
      $response["result_mama"] = false;
      $response["message_mama"] = "Ocurrio error al guardar productos en el padron. Por favor intente cargar el contrato nuevamente. Si el error persiste comuniquese con comercial.";
      $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron (OMT)|$query|" . mysqli_error($mysqli);
      file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
    }


    $qHistorico = "INSERT INTO historico_venta VALUES (NULL, 30, $idSocioBen, 1, '$fecha', 'PROMO MES DE LA MADRE', 11)";
    mysqli_query($mysqli, $qHistorico);
  }
}
#endregion

$idSocioPiscina = (isset($idSocioPiscina)) ? $idSocioPiscina : buscarIdPiscina($cedula);

if ($idSocioPiscina && !empty($_POST['tieneConvenioEspecial']))
  insertarConvenioEspecial($idSocioPiscina, $_POST['tieneConvenioEspecial']);

$response['tieneConvenioEspecial'] = !empty($_POST['tieneConvenioEspecial']);
$response['idSocioPiscina'] = $idSocioPiscina;

aplicarPromoFloreada($cedula, $precio_segundo_modulo_sanatorio);
if ($esPromoCompetenciaVeintitres == 'true') {
  aplicarPromoCompetenciaVeintitres($cedula, $id_sucursal, $fecha_nacimiento, $servicios);
}

mysqli_close($mysqli);
die(json_encode($response));

#region Functions
function buscarIdPiscina($cedula)
{
  global $mysqli;

  $qSelect = <<<SQL
    SELECT
        *
    FROM
        `padron_datos_socio`
    WHERE
        `cedula` = $cedula;
SQL;
  $select = $mysqli->query($qSelect);

  if (!$select || $select->num_rows < 1)
    return false;

  return $select->fetch_assoc()['id'];
}

function validarRegistros($mysqli, $cedula, $socio, $servicios, $id_historico, $total, $alta)
{ //dir2
  $rDatos = true;
  $rServicios = true;
  $servicio = '';
  global $log_error_file;
  global $log_content;
  global $log_date;

  //validamos que tenga un registro en padron datos
  $valDatos = "SELECT * FROM padron_datos_socio where cedula = '$cedula'";
  $rValDatos = mysqli_query($mysqli, $valDatos);

  if (!$rValDatos || mysqli_num_rows($rValDatos) > 1) {
    $rDatos = false;
    eliminarDatos($mysqli, $cedula, $id_historico, $alta); //dir2
    $log_content = "[ERROR][$log_date]| $cedula ||Error: registro duplicado el padron datos|" . mysqli_error($mysqli);
    file_put_contents($log_error_file, $log_content . "\n", FILE_APPEND);
  }

  //validamos que los servicios esten guardados en la cantidad de módulos exactos
  for ($i = 0; $i < count($servicios); $i++) {
    $num_servicio = $servicios[$i][0];

    $mod_servicio = isset($servicios[$i][1]) ? $servicios[$i][1] / 8 : 8;
    $qCantServicios = "SELECT COUNT(id) AS cuenta FROM padron_producto_socio WHERE cedula = '$cedula' AND servicio ='$num_servicio' AND accion='1'";
    $rCantServicios = mysqli_query($mysqli, $qCantServicios);
    if ($rCantServicios && !mysqli_num_rows($rCantServicios) > 0) {
      $cantServicios = mysqli_fetch_assoc($rCantServicios)['cuenta'];
      if ($cantServicios != $mod_servicio) {
        $rServicios = false;
        eliminarDatos($mysqli, $cedula, $id_historico, $alta); //dir2
        $log_content = "[ERROR][$log_date]| $cedula ||Error: registros duplicados el padron productos|" . mysqli_error($mysqli);
        file_put_contents($log_error_file, $log_content . "\n", FILE_APPEND);
        break;
      }
    }
  }

  //validamos que el total importe concuerde con la sumatoria de todos los servicios
  if (!in_array($num_servicio, [136, 146])) {
    $qTotalImporte = "SELECT SUM(importe) AS sumatoria, servicio FROM padron_producto_socio WHERE cedula = '$cedula'";
    $rTotalImporte = mysqli_query($mysqli, $qTotalImporte);
    if ($rTotalImporte !== false && mysqli_num_rows($rTotalImporte) > 0) {
      $row = mysqli_fetch_assoc($rTotalImporte);
      $servicio = $row['servicio'];
      $sumatoria = $row['sumatoria'];

      //ERROR
      if ($sumatoria != $total && !in_array($servicio, ['63', '65', '146']) && empty($_POST['tieneConvenioEspecial'])) {
        $rServicios = false;
        eliminarDatos($mysqli, $cedula, $id_historico, $alta); //dir2
        $log_content = "[ERROR][$log_date]| $cedula ||Error: total importe no concuerda con la suma de los servicios| TOTAL: {$total} SUMATORIA: {$sumatoria}" . mysqli_error($mysqli);
        file_put_contents($log_error_file, $log_content . "\n", FILE_APPEND);
      }
    }
  }

  //validamos que si hay un grupo familiarse hayan guardado los integrantes
  $grupoFamiliar = in_array('63', $servicios) || in_array('65', $servicios);
  if ($grupoFamiliar) {
    $qBeneficiarios = "SELECT cedula FROM padron_producto_socio WHERE cedula_titular_gf = '$cedula'";
    $rBen = mysqli_query($mysqli, $qBeneficiarios);
    if ($rBen !== false && mysqli_num_rows($rBen) == 0) {
      $rServicios = false;
      eliminarDatos($mysqli, $cedula, $id_historico, $alta); //dir2
      $log_content = "[ERROR][$log_date]| $cedula ||Error: No se a guardado los beneficiarios del grupo correctamente|" . mysqli_error($mysqli);
      file_put_contents($log_error_file, $log_content . "\n", FILE_APPEND);
    }
  }

  return $rDatos && $rServicios;
}

function eliminarDatos($mysqli, $cedula, $id_historico, $alta)
{ //dir2
  global $log_date;
  if ($alta) {

    // Elimino de padron_producto_socio
    mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula = '$cedula'");

    // Elimino de padron_datos_socio
    mysqli_query($mysqli, "DELETE FROM padron_datos_socio WHERE cedula = '$cedula'");

    //Elimino la direccion de la tabla de direcciones_socios
    mysqli_query($mysqli, "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedula'");

    //Elimino el afiliado omt o beneficiarios que tenga
    $qBen = "SELECT id,cedula from padron_producto_socio WHERE cedula_titular_gf = '$cedula'";
    if ($resultomt = mysqli_query($mysqli, $qBen)) {
      if (mysqli_num_rows($resultomt) > 0) {
        while ($row = mysqli_fetch_assoc($resultomt)) {
          $cedulaBen = $row['cedula'];
          mysqli_query($mysqli, "DELETE FROM padron_datos_socio WHERE cedula = '$cedulaBen'");
          mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula = '$cedulaBen'");
          mysqli_query($mysqli, "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedulaBen'");
        }
      }
    }
  } else {

    $qIdPiscina = "SELECT id FROM padron_datos_socio WHERE cedula = '$cedula'";
    $rIdPiscina = mysqli_query($mysqli, $qIdPiscina);

    $idPiscina = ($rIdPiscina && mysqli_num_rows($rIdPiscina) > 0) ? mysqli_fetch_assoc($rIdPiscina)['id'] : false;
    $existeHistorico = false;

    if ($idPiscina != false) {
      $qExisteHistorico = "SELECT id FROM historico_venta WHERE id_cliente=$idPiscina AND id_estado=6";
      $rExisteHistorico = mysqli_query($mysqli, $qExisteHistorico);
      $existeHistorico = ($rExisteHistorico && mysqli_num_rows($rExisteHistorico) > 0) ? true : false;
    }

    if ($existeHistorico) {

      // Actualizo padron productos
      mysqli_query($mysqli, "UPDATE padron_producto_socio SET accion = '5' WHERE cedula = '$cedula'");

      //elimino los productos nuevos registrados
      $fechaR = date('Y-m-d');
      $qServiciosNew = "SELECT id FROM padron_producto_socio WHERE fecha_registro = '$fechaR' AND cedula = '$cedula'";
      $rServNew = mysqli_query($mysqli, $qServiciosNew);
      if ($rServNew && mysqli_num_rows($rServNew) > 0) {

        while ($row = mysqli_fetch_assoc($rServNew)) {
          mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE id = " . $row['id']);
        }
      }

      $qServiciosNew = "SELECT sum(importe) as total FROM padron_producto_socio WHERE cedula = '$cedula'";
      $rServNew = mysqli_query($mysqli, $qServiciosNew);
      if ($rServNew && mysqli_num_rows($rServNew) > 0) {
        $totalImporte = mysqli_fetch_assoc($rServNew)['total'];
      }

      // Actualizo el estado y la accion
      mysqli_query($mysqli, "UPDATE padron_datos_socio SET estado = 6, accion = '5', total_importe='$totalImporte' WHERE cedula = '$cedula'");
    } else {
      // Elimino de padron_producto_socio
      mysqli_query($mysqli, "DELETE FROM padron_producto_socio WHERE cedula = '$cedula'");

      // Elimino de padron_datos_socio
      mysqli_query($mysqli, "DELETE FROM padron_datos_socio WHERE cedula = '$cedula'");

      //Elimino la direccion de la tabla de direcciones_socios
      mysqli_query($mysqli, "DELETE FROM direcciones_socios WHERE cedula_socio = '$cedula'");
    }
  }

  if ($id_historico != false) {
    // Elimino de historico_venta
    mysqli_query($mysqli, "DELETE FROM historico_venta WHERE id = $id_historico");
  }
}

function enviarSMS($nombre, $telefonos)
{ //sms
  $celulares = buscarCelular($telefonos);
  $mensaje = "Estimado/a socio/a, para completar su afiliacion ingrese su comprobante de la competencia en https://vida-apps.com/comprobante_competencia/";
  $servicio = "http://192.168.104.6/apiws/1/apiws.php?wsdl";
  $info = array(
    'authorizedKey' => '9d752cb08ef466fc480fba981cfa44a1',
    'msgId' => '0',
    'msgData' => $mensaje,
  );

  foreach ($celulares as $celular) { //sms
    $info['msgRecip'] = $celular;
    $client = new SoapClient($servicio, $info);
    $client->sendSms($info['authorizedKey'], $info['msgId'], $info['msgData'], $info['msgRecip']);
  }
}

function buscarEstadoConvenioEspecial($idConvenioEspecial)
{
  define('ESTADO_PENDIENTE_APROBACION', 686);
  define('ESTADO_PREAPROBADO', 687);


  $estadoConvenioEspecial = [
    '1' => ESTADO_PENDIENTE_APROBACION,
    '2' => ESTADO_PREAPROBADO
  ];

  return isset($estadoConvenioEspecial[$idConvenioEspecial]) ? $estadoConvenioEspecial[$idConvenioEspecial] : 1;
}

function insertarConvenioEspecial($_idSocioPiscina, $_idConvenioEspecial)
{
  global $mysqli;

  $idSocioPiscina = $mysqli->real_escape_string($_idSocioPiscina);
  $idConvenioEspecial = $mysqli->real_escape_string($_idConvenioEspecial);
  $existeConvenio = corroborarConvenioEspecial($_idConvenioEspecial);

  if (!$existeConvenio)
    return false;

  $qInsert = <<<SQL
 INSERT INTO
 `relacion_socio_convenio_especial`
 (`id_socio`, `id_convenio_especial`, `created_at`)
 VALUE
 ("{$idSocioPiscina}", "{$idConvenioEspecial}", NOW());
 SQL;
  $mysqli->query($qInsert);

  corregirPrecioTotal($_idSocioPiscina);
}

function corregirPrecioTotal($_idSocioPiscina)
{
  global $mysqli;
  $idSocioPiscina = $mysqli->real_escape_string($_idSocioPiscina);
  global $cedula;

  $qSelect = <<<SQL
 SELECT
 `importe`
 FROM
 `padron_producto_socio`
 WHERE
 `cedula` = '{$cedula}';
SQL;
  $select = $mysqli->query($qSelect);
  $fetch = $select->fetch_all(MYSQLI_ASSOC);

  $total = array_reduce($fetch, function ($suma, $row) {
    return $suma + $row['importe'];
  }, 0);

  $qUpdate = <<<SQL
    UPDATE
        `padron_datos_socio`
    SET
        `total_importe` = '{$total}'
    WHERE
        `id` = '{$idSocioPiscina}';
SQL;
  $mysqli->query($qUpdate);
}

function corroborarConvenioEspecial($idConvenioEspecial)
{
  global $mysqli;

  $idConvenioEspecial = $mysqli->real_escape_string($idConvenioEspecial);

  $qSelect = <<<SQL
 SELECT
 *
 FROM
 `convenios_especiales`
 WHERE
 `id` = "{$idConvenioEspecial}";
 SQL;
  $query = $mysqli->query($qSelect);

  return $query->num_rows > 0;
}

function buscarCelular($numeros) //sms
{
  preg_match_all('/(09)[1-9]\d{6}/x', $numeros, $respuesta);

  return (count($respuesta[0]) !== 0)
    ? $respuesta[0]
    : false;
}

function modificarEmpresaMarcaPorRadio($radio, $empresa)
{
  switch ((int) $radio) {
    case 10914:
      $empresa_marca = 99;
      break;
    case 202:
      $empresa_marca = 99;
      break;
    case 7032:
      $empresa_marca = 16;
      break;

    default:
      $empresa_marca = $empresa;
      break;
  }

  return $empresa_marca;
}

/**
 * Devuelve el radio correspondiente a la cantidad de meses adelantados
 *
 * @param $cantidadDeMesesAdelantados
 *
 * @return string
 */
function radioMesesAdelantados($cantidadDeMesesAdelantados): string
{
  $date = new DateTime();
  $meses = new DateInterval("P{$cantidadDeMesesAdelantados}M");
  $mes = $date->add($meses)->format('m');

  return "109{$mes}";
}

function requiereCorroboracionCalidad()
{
  global $servicios;
  $serviciosCalidad = [136];
  $corresponde = false;

  foreach ($servicios as $_servicio) {
    if (!isset($_servicio[0])) continue;
    $servicio = (int)$_servicio[0];
    $corresponde = $corresponde || in_array($servicio, $serviciosCalidad);
  }

  return $corresponde;
}

function estadoCalidad()
{
  global $servicios;

  $estado = 1;
  $estados = ['136' => '693'];

  foreach ($servicios as $_servicio) {
    if (!isset($_servicio[0])) continue;
    $servicio = trim($_servicio[0]);
    $estado = isset($estados[$servicio]) ? $estados[$servicio] : $estado;
  }

  return $estado;
}


/**
 * Busca a todos los clientes en proceso de afiliación que tengan la misma tarjeta
 *
 * @param string $tarjeta Número de tarjeta a buscar
 * @param string $cedula Cédula a excluir
 *
 * @return array
 */
function buscarAltaConMismaTarjeta($tarjeta, $cedula)
{
  global $mysqli;
  $codigosPromoExcluidos = ['28', '30'];
  $codigosPromoExcluidosString = implode(', ', $codigosPromoExcluidos);

  $qSelect = <<<SQL
  SELECT
    `pds`.`id` AS `pds_id`, `pps`.`id` AS `pps_id`, `pps`.`cod_promo`
  FROM
    `padron_datos_socio` AS `pds`
  INNER JOIN
    `padron_producto_socio` AS `pps`
    ON `pds`.`cedula` = `pps`.`cedula`
  WHERE
		`pds`.`fechafil` > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND
    `pds`.`movimientoabm` = 'ALTA' AND
    `pds`.`abm` = 'ALTA' AND
		`pds`.`tarjeta` != '' AND
		`pds`.`tipo_tarjeta` != '' AND
		`pds`.`numero_tarjeta` != '' AND
    `pps`.`cod_promo` NOT IN ($codigosPromoExcluidosString) AND
		(
      LENGTH(`pps`.`cedula_titular_gf`) = 0 OR
      `pps`.`cedula_titular_gf` IS NULL
    ) AND
		`pps`.`servicio` != '70' AND
    `pds`.`numero_tarjeta` = '{$tarjeta}' AND
    `pds`.`cedula` != '{$cedula}';
SQL;
  $select = $mysqli->query($qSelect);


  return $select->fetch_all(MYSQLI_ASSOC);
}

/**
 * Aplica código promo en padron_producto_socio (piscina) al ID indicado
 *
 * @param int $pps_id ID de la tabla padron_producto_socio EN PISCINA a modificar
 * @param string $pps_cod_promo codigo promo a aplicar en padron_producto_socio EN PISCINA
 *
 * @return bool
 */
function aplicarCodigoPromoPorId($pps_id, $pps_cod_promo)
{
  global $mysqli;

  $pps_cod_promo = $mysqli->real_escape_string($pps_cod_promo);

  $qUpdate = <<<SQL
  UPDATE `padron_producto_socio`
  SET `cod_promo` = "{$pps_cod_promo}"
  WHERE `id` = {$pps_id};
SQL;
  $mysqli->query($qUpdate);

  return $mysqli->affected_rows === 1;
}

/**
 * Aplica código promo en padron_producto_socio (piscina) a todos los productos de la cédula indicada
 *
 * @param string $cedula cedula a modificarle los productos
 * @param string $pps_cod_promo codigo promo a aplicar en padron_producto_socio EN PISCINA
 *
 * @return bool
 */
function aplicarCodigoPromoPorCedula($cedula, $pps_cod_promo)
{
  global $mysqli;

  $pps_cod_promo = $mysqli->real_escape_string($pps_cod_promo);

  $qUpdate = <<<SQL
  UPDATE `padron_producto_socio`
  SET `cod_promo` = "{$pps_cod_promo}"
  WHERE `cedula` = "{$cedula}";
SQL;
  $mysqli->query($qUpdate);

  return $mysqli->affected_rows > 0;
}

function limiteDePromoVisa($limiteMensual)
{
  global $mysqli;

  $qSelect = <<<SQL
  SELECT
    `cedula`
  FROM
    `padron_producto_socio`
  WHERE
    `cod_promo` = '28' AND
    `fecha_afiliacion` >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL DAYOFMONTH(NOW())-1 DAY), '%y-%m-%d')
  GROUP BY
  `cedula`;
SQL;
  $select = $mysqli->query($qSelect);

  return $select->num_rows < $limiteMensual;
}

/**
 * Busca si la cédula ingresada es parte de la promo Floreada y la aplica
 *
 * @param int $cedula
 * @param int $keepprice1
 *
 * @return void
 */
function aplicarPromoFloreada($cedula, $keepprice1)
{
  global $mysqli;
  $cod_promo = 30;

  $qSelect = <<<SQL
  SELECT
    *
  FROM
    `padron_producto_socio`
  WHERE
    `cedula` = "{$cedula}" AND
    `cod_promo` = "{$cod_promo}";
SQL;
  $select = $mysqli->query($qSelect);
  $numRows = $select->num_rows;

  if ($numRows === 0)
    return;

  $qUpdate = <<<SQL
  UPDATE
    `padron_producto_socio`
  SET
    `cod_promo` = 0
  WHERE
    `cedula` = "{$cedula}" AND
    `cod_promo` = "{$cod_promo}";
SQL;
  $mysqli->query($qUpdate);

  if ($numRows === 3)
    return;

  $_data = $select->fetch_all(MYSQLI_ASSOC);
  $data = (count($_data) > 1) ? $_data[1] : $_data[0];

  unset($data['id']);
  $data['importe'] = 0;
  $data['cod_promo'] = 30;
  $data['keepprice1'] = $keepprice1;
  $values = "NULL, " . '"' . implode('", "', $data) . '"';

  $qInsert = <<<SQL
  INSERT INTO
    `padron_producto_socio`
  VALUES
    ($values);
SQL;
  $mysqli->query($qInsert);
}

function aplicarPromoCompetenciaVeintitres($cedula, $nroFilial, $fechaNacimiento, $servicios)
{
  global $mysqli;
  $precioCompetencia = calcularPrecio($nroFilial, $fechaNacimiento, '02', 8, 0, false);
  $tieneConvalecencia = !empty(array_filter($servicios, function ($servicio) {
    return $servicio[0] == '2' || $servicio[0] == '02';
  }));
  $precio = $tieneConvalecencia
    ? $precioCompetencia['precio_servicio']
    : $precioCompetencia['precio_base'];

  $qSelect = <<<SQL
  SELECT
    *
  FROM
    `padron_producto_socio`
  WHERE
    `cedula` = '{$cedula}' AND
    `servicio` = '01'
SQL;
  $select = $mysqli->query($qSelect);

  if ($select->num_rows === 0)
    return;

  $data = $select->fetch_assoc();

  if (!in_array($data['cod_promo'], ['31', '2031']))
    return;
  elseif ($data['cod_promo'] == '2031') {
    actualizarCodigoPromoCompetenciaVeintitres($data['id']);
    $data['cod_promo'] = '31';
  }

  unset($data['id']);
  $data['servicio'] = '02';
  $data['importe'] = 0;
  $data['keepprice1'] = $precio;
  $data['precioOriginal'] = $precio;
  $values = "NULL, " . '"' . implode('", "', $data) . '"';

  $qInsert = <<<SQL
  INSERT INTO
    `padron_producto_socio`
  VALUES
    ($values);
SQL;
  $mysqli->query($qInsert);
}

function actualizarCodigoPromoCompetenciaVeintitres($idServicio)
{
  global $mysqli;

  $qUpdate = <<<SQL
  UPDATE
    `padron_producto_socio`
  SET
    `cod_promo` = '31'
  WHERE
    `id` = {$idServicio};
SQL;
  $mysqli->query($qUpdate);
}


/**
 * Calcula el precio de un servicio en particular
 *
 * @param int $nroFilial Número de filial
 * @param string $fechaNacimiento Fecha de nacimiento
 * @param int $idServicio Número de servicio
 * @param int $cantidadHoras Cantidad de horas del servicio
 * @param int $cantidadHorasSanatorio Cantidad de horas de sanatorio
 * @param bool $socio ¿Es socio o no?
 *
 * @return array
 */
function calcularPrecio(
  $nroFilial,
  $fechaNacimiento,
  $idServicio,
  $cantidadHoras,
  $cantidadHorasSanatorio,
  $socio
) {

  $calcularConBase = in_array($idServicio, ['01', '02']);

  if ($cantidadHorasSanatorio == "null" || $cantidadHorasSanatorio == "" || $cantidadHorasSanatorio == 0) {
    $cantidadHorasSanatorio = false;
  }
  if ($cantidadHoras == "null" || $cantidadHoras == "" || $cantidadHoras == 0) {
    $cantidadHoras = 8;
  }

  $getdata = http_build_query(
    array(
      'consulta' => 'calcularTotal',
      'idFilial' => $nroFilial,
      'fechaNacimiento' => $fechaNacimiento,
      'idServicio' => $idServicio,
      'cantidadHoras' => $cantidadHoras,
      'cantidadHorasSanatorio' => $cantidadHorasSanatorio,
      'socio' => $socio,
      'calcular_con_base' => $calcularConBase,
    )
  );

  $opts = array(
    'http' => array(
      'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
        "Content-Length: " . strlen($getdata) . "\r\n" .
        "User-Agent:MyAgent/1.0\r\n",
      'method' => 'GET',
      'content' => $getdata,
    ),
  );

  $context = stream_context_create($opts);
  $resultado = file_get_contents(
    'https://vida-apps.com/motorDePrecios_new/PHP/clases/Precios.php?' . $getdata,
    false,
    $context
  );
  return json_decode($resultado, true);
}

#endregion