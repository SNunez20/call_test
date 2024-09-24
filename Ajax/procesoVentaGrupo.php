<?php
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
require "../_conexion.php";
require "../_conexion250.php";
require "../_conexion1310.php";

$response = array(
  'result' => false,
  'session' => false,
  'message' => 'Ocurrio un error, intentelo nuevamente más tarde.',
);

$log_date = date("d-m-Y");
$log_file = "../logs/call_$log_date.log";

// FORMATO ESPERADO DE LOS DATOS ENVIADOS POR POST
/**
 * $_POST["socios"] = [ 
 * [ 
 * [
 * CEDULA
 * NOMBRE,
 * FECHA_NACIMIENTO,
 * DIRECCION,
 * DEPARTAMENTO,
 * LOCALIDAD,
 * FILIAL,
 * MAIL,
 * CELULAR,
 * TELEFONO,
 * TELEFONO_ALTERNATIVO,
 * DATO_EXTRA
 * ], 
 * ARRAY_CON_SERVICIOS_SOCIO,
 * ] 
 * ARRAY_DATOS_DE_LA_TARJETA,
 * llamadaEntrante
 * ]
 */

if (isset($_SESSION['idusuario'])) {
  $response["session"] = true;
  $idUser = $_SESSION['idusuario'];
  $nombreVendedor = $_SESSION['nombreUsuario'];
  $numeroVendedor = $_SESSION['cedulaUsuario'];
  $omtGrupo = mysqli_real_escape_string($mysqli, $_POST['omt']);
  $arrBenOmtGrupo = $_POST['beneficiariosOmt'];

  # [ DATOS_SOCIO, SERVICIOS_SOCIO ]
  $socios = json_decode($_POST["socios"], true);

  $datosDeTarjeta = $socios[count($socios) - 2];

  //####### DATOS DE LA TARJETA #######
  $numeroTarjeta = $datosDeTarjeta[0];
  $cedulaTitular = $datosDeTarjeta[1];
  $nombreTitular = $datosDeTarjeta[2];
  $cvv = ($datosDeTarjeta[3] == '') ? 0 : $datosDeTarjeta[3];
  $mesVencimiento = $datosDeTarjeta[4];
  $anioVencimiento = $datosDeTarjeta[5];
  $emailTitular = $datosDeTarjeta[6];
  $celularTitular = $datosDeTarjeta[7];
  $telefonoTitular = $datosDeTarjeta[8];
  $medioPago = 2;
  $isMercadopago = $datosDeTarjeta[2];
  $isMercadopago = ($isMercadopago == '' || $isMercadopago == '0') ? '0' : '1';
  $bancoEmisor = $datosDeTarjeta[9];
  $tipoTarjeta = $datosDeTarjeta[10];
  $llamadaEntrante = $_POST["llamadaEntrante"] == "true" ? "1" : "0";
  $abmActual = $isMercadopago == '1' ? '0' : '1';
  $cobrador = 0;
  $count = 0;
  $estado = 1;
  $existePadron = "0";
  $accion = "1";
  $observacion = "";
  $ruta = "";
  $fecha = date('Y-m-d H:i');
  $tarjetaVida = ($isMercadopago == '1' && $medioPago == '2') ? '0' : '1';

  foreach ($socios[0] as $indice => $socio) {
    $datos = $socio[0];
    $servicios = $socio[1];

    // ######################### GUARDADO DE DATOS #########################
    // DATOS PERSONALES 
    $cedula = $datos[0];
    $qValidacion = "SELECT * FROM padron_datos_socio WHERE cedula = '$cedula' AND estado != 6";
    $rVal = mysqli_query($mysqli, $qValidacion);

    if (mysqli_num_rows($rVal) > 0) {
      $response['message'] = "Ya se ha cargado un contrato del socio cédula $cedula";
      break;
    } else {
      $nombre = $datos[1];
      $fechaNacimiento = $datos[2];
      $direccion = substr($datos[3], 0, 36);
      $departamento = $datos[4];
      $idLocalidad = $datos[5];
      $idSucursal = $datos[6];
      $mail = $datos[7];
      $celular = $datos[8];
      $telefono = $datos[9];
      $datoExtra = $datos[11] != "" ? $datos[11] : 3;
      $telefonos = $celular . " " . $telefono;
      $calle = $datos[13]; //dir2
      $puerta = $datos[14];
      $apto = $datos[15];
      $manzana = $datos[16];
      $solar = $datos[17];
      $esquina = $datos[18];
      $referencia = $datos[19];
      $total = $datos[20];

      // Número de la filial dependiendo la localidad del afiliado
      $query = "SELECT nro_filial FROM motor_de_precios.filiales WHERE id = $idSucursal";
      $result = mysqli_query($mysqli1310, $query);
      $sucursal = mysqli_fetch_assoc($result)['nro_filial'];
      $sucursal = ($sucursal != '0') ? preg_replace('/^0+/', '', $sucursal) : $sucursal;
      $sucursalCobranzasNum = ($isMercadopago == '1' || $tarjetaVida == '1') ? '99' : $sucursal;

      // RUT de la empresa segun la filial
      $query = "SELECT empresa_rut FROM aux2 WHERE num_sucursal = $sucursal";
      $result = mysqli_query($mysqli250, $query);
      $empresaRut = "";
      if (mysqli_num_rows($result)) {
        $empresaRut = mysqli_fetch_assoc($result)['empresa_rut'];
      }

      $rutcentralizado = "99";
      $query = "SELECT empresa_brand FROM aux2 WHERE num_sucursal = $sucursal";
      $result = mysqli_query($mysqli250, $query);
      $empresa_marca = "";
      if (mysqli_num_rows($result)) {
        $empresa_marca = mysqli_fetch_assoc($result)['empresa_brand'];
      }

      $cumpleanos = new DateTime($fechaNacimiento);
      $hoy = new DateTime();
      $anios = $hoy->diff($cumpleanos);
      $edad = $anios->y;

      // ##### MEDIO DE PAGO 2 - TARJETA
      $tipoTarjeta = strtoupper($tipoTarjeta);
      $bin = substr($numeroTarjeta, 0, 6);
      $query = (strtolower($tipoTarjeta) == 'master' && ($bin == '504736' || $bin == '589657')) ? "SELECT * FROM radios_tarjetas WHERE bin like '%$bin%'" : "SELECT * FROM radios_tarjetas WHERE nombre_vida LIKE '%$tipoTarjeta%'";
      $result = mysqli_query($mysqli, $query);
      $row = mysqli_fetch_assoc($result);
      $metodoVida = $row['nombre_mostrar'];
      $radio = $row['radio'];
      $sucursalCobranzaNum = '99';
      $empresaMarca = '99';
      $rutcentralizado = '99';
      $ruta = '0000000000';

      if (mysqli_num_rows($result) > 0) {
        $idRelacion = $empresaRut . '-' . $cedula;
        $fechafil = date("Y-m-d");
        $medioPago = (int) $medioPago;

        // QUERY PADRON_DATOS_SOCIO
        $query = "INSERT INTO padron_datos_socio VALUES(NULL,'$nombre','$telefonos','$cedula','$direccion','$sucursal','$ruta','$radio','1','$fechaNacimiento','$edad','$metodoVida', '$metodoVida',";
        $query .= "'$numeroTarjeta','$nombreTitular','$cedulaTitular','$celularTitular','$anioVencimiento','$mesVencimiento',1
 ,'$sucursal',$sucursalCobranzasNum,'$empresaMarca',1,$count,'$observacion','0','$idRelacion','$empresaRut',$total,";
        $query .= "1, 1, 1, '$rutcentralizado',0,1,'ALTA','ALTA','1','0','0','0','$fechafil','0','0','0',$medioPago,'$cvv','$existePadron', '$mail', '$emailTitular','$tarjetaVida',$bancoEmisor,'$accion',$estado,$idLocalidad,'$datoExtra', '$llamadaEntrante','2','1', '0', $idUser)";

        if (mysqli_query($mysqli, $query)) {
          //dir2
          $idSocioBen = mysqli_insert_id($mysqli);
          $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioBen,'$calle','$puerta','$manzana','$solar','$apto','$esquina','$referencia','$cedula')";
          $rDir = mysqli_query($mysqli, $qInsertDireccion);
          // ######################### GUARDADO DE SERVICIOS #########################
          $sanatorio = false;
          $convalecencia = false;

          // Recorremos el array de servicos y guardamos los numeros de servicios de cada uno en un array
          $nrosServicios = [];
          foreach ($servicios as $key => $servicio) {
            $query = "SELECT nro_servicio FROM motor_de_precios.servicios WHERE id = " . $servicio[0];
            $result = mysqli_query($mysqli1310, $query);
            $nrosServicios[] = mysqli_fetch_assoc($result)['nro_servicio'];
          }

          // RECORRE LOS SERVICIOS
          foreach ($servicios as $key => $servicio) {
            if ($servicio[0] != 106) {
              if ($servicio[0] == '41') {
                // VIDA ESPECIAL
                $vidaEspecial = [
                  ["numero_servicio" => 12, "importe" => 261],
                  ["numero_servicio" => 13, "importe" => 43],
                  ["numero_servicio" => 14, "importe" => 76],
                ];
                foreach ($vidaEspecial as $ve) {
                  $precioBase = $ve["importe"];
                  $nroServicio = $ve["numero_servicio"];
                  $codPromo = $servicio[6];
                  $codPromo = ($medio_pago == 2) ? $codPromo : 0;
                  $observacion = $servicio[5];

                  if ($servicio[0] == '1' && !$sanatorio) {
                    $sanatorio = true;
                    $precioBase = $servicio[5];
                  }

                  // $servdecod = (in_array($servicio[0], ['1', '2', '3'])) ? $nroServicio . '8' : $nroServicio;
                  $tipo_iva = $nro_servicio == '81' ? 1 : 2;
                  $servdecod = in_array($servicio[0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nroServicio . '8' : $nroServicio;

                  $query = "INSERT INTO padron_producto_socio VALUES(null,'$cedula','$nroServicio','8','$precioBase','$codPromo','$fechafil','0','$fechafil','$nombreVendedor','$observacion','0',0,999,'ALTA',";
                  $query .= "'2015-09-15','$numeroVendedor',$precioBase,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresaMarca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precioBase','0',0,'1', NULL)";

                  if (mysqli_query($mysqli, $query)) {
                    $response["result"] = true;
                    $response["message"] = "Datos guardados correctamente.";
                  } else {
                    $response["result"] = false;
                    $response["message"] = "Ocurrio error al guardar productos en el padron.";
                    $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
                    file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
                  }
                }
              } else {
                $hrsServicio = 0;

                // Calcula el número de veces que se ingresa el servicio en fraccion de 8 horas
                if ($servicio[1] != "") {
                  $hrsServicio = $servicio[1] / 8;
                }

                for ($x = 0; $x < $hrsServicio; $x++) {
                  $nroServicio = $nrosServicios[$key];
                  $precioBase = $servicio[4];
                  $codPromo = $servicio[6];
                  $codPromo = ($medioPago == 2) ? $codPromo : 0;
                  $observacion = $servicio[7];

                  if ($servicio[0] == '1' && !$sanatorio) {
                    $sanatorio = true;
                    $precioBase = $servicio[5];
                  } elseif ($servicio[0] == '2' && !$convalecencia) {
                    $convalecencia = true;
                    $precioBase = $servicio[5];
                  }

                  if ($servicio[0] != '1') {
                    $codPromo = 0;
                  }

                  // $servdecod = (in_array($servicio[0], ['1', '2', '3'])) ? $nroServicio . '8' : $nroServicio;
                  $tipo_iva = $nroServicio == '81' ? 1 : 2;
                  $servdecod = in_array($servicio[0], array('1', '2', '3', '12', '16', '37', '46', '51', '56', '58', '61', '63', '65', '82')) ? $nroServicio . '8' : $nroServicio;

                  $query = "INSERT INTO padron_producto_socio VALUES(NULL,'$cedula','$nroServicio','8','$precioBase','$codPromo','$fechafil','0','$fechafil','$nombreVendedor','$observacion','0',0,999,'ALTA',";

                  $query .= "'2015-09-15','$numeroVendedor',$precioBase,'0',0,$tipo_iva,'$idRelacion',0,'0','$empresaMarca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precioBase','0',0,'1', NULL)";

                  if (mysqli_query($mysqli, $query)) {
                    $response["result"] = true;
                    $response["message"] = "Datos guardados correctamente.";
                  } else {
                    $response["result"] = false;
                    $response["message"] = "Ocurrio error al guardar productos en el padron.";
                    $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio error al guardar productos en el padron|$query|" . mysqli_error($mysqli);
                    file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
                  }
                }
              }
            }
          }
          // ######################### FIN GUARDADO DE SERVICIOS ######################### 
        } else {
          $response["result"] = false;
          $response["message"] = "Ocurrio un error al guardar datos en el padron.";
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Ocurrio un error al guardar datos en el padron|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
        }

        if ($omtGrupo == 'true') { //omtgrupo

          if (is_array($arrBenOmtGrupo[$indice])) {
            $nombre_ben = $arrBenOmtGrupo[$indice][0];
            $cedula_ben = $arrBenOmtGrupo[$indice][1];
            $tel_ben = $arrBenOmtGrupo[$indice][2];
            $fechan_ben = $arrBenOmtGrupo[$indice][3];
            $direccion_ben = $arrBenOmtGrupo[$indice][4];
            $filial = $arrBenOmtGrupo[$indice][5];
            $edad_ben = $arrBenOmtGrupo[$indice][6];
            $id_localidad = $arrBenOmtGrupo[$indice][7];
            $calle_ben = addslashes($arrBenOmtGrupo[$indice][8]);
            $puerta_ben = $arrBenOmtGrupo[$indice][9];
            $apto_ben = $arrBenOmtGrupo[$indice][10];
            $manzana_ben = $arrBenOmtGrupo[$indice][11];
            $solar_ben = $arrBenOmtGrupo[$indice][12];
            $esquina_ben = addslashes($arrBenOmtGrupo[$indice][13]);
            $referencia_ben = $arrBenOmtGrupo[$indice][14];
            $precio_base = $arrBenOmtGrupo[$indice][15]; //dir2

            // Traigo el número de la filial segun la localidad del cliente
            $qSucursal = "select nro_filial from motor_de_precios.filiales where id = $filial";
            $rSucursal = mysqli_query($mysqli1310, $qSucursal);
            $sucursal = mysqli_fetch_assoc($rSucursal)['nro_filial'];
            $sucursal = ($sucursal != '0') ? preg_replace('/^0+/', '', $sucursal) : $sucursal;


            // Obtengo el rut de la empresa segun la filial
            $qERut = "select empresa_rut from aux2 where num_sucursal = $sucursal";
            $rERut = mysqli_query($mysqli250, $qERut);
            $empresa_rut = "";
            if (mysqli_num_rows($rERut)) {
              $empresa_rut = mysqli_fetch_assoc($rERut)['empresa_rut'];
            }
            $idRelacion = $empresa_rut . '-' . $cedula_ben;
            $nro_servicio = '70';

            $query = "insert into padron_datos_socio values(null,'$nombre_ben','$tel_ben','$cedula_ben','$direccion_ben','$sucursal','$ruta','$radio','1','$fechan_ben','$edad_ben','$tipoTarjeta','$tipoTarjeta',";
            $query .= "'$numeroTarjeta','$nombreTitular','$cedulaTitular','$celularTitular','$anioVencimiento','$mesVencimiento',1,'$sucursal',$sucursalCobranzasNum,'$empresa_marca',1,$count,'$observacion','0','$idRelacion','$empresa_rut','$precio_base',";
            $query .= "1,1,1,'$rutcentralizado',0,1,'ALTA','ALTA','1','0','0','0','$fechafil','0','0','0',$medioPago,'0','0', '0', '$emailTitular','$tarjetaVida',$bancoEmisor,'1',$estado,$id_localidad,'$datoExtra', '0','0','1', '0', $idUser)";

            $result = mysqli_query($mysqli, $query);
            if ($result) {
              //dir2
              $idSocioBen = mysqli_insert_id($mysqli);
              $qInsertDireccion = "INSERT INTO direcciones_socios (id_socio,calle,puerta,manzana,solar,apartamento,esquina,referencia,cedula_socio) VALUES ($idSocioBen,'$calle_ben','$puerta_ben','$manzana_ben','$solar_ben','$apto_ben','$esquina_ben','$referencia_ben','$cedula_ben')";
              $rDir = mysqli_query($mysqli, $qInsertDireccion);
              $hrs_servicio = 8;
              $observacion = 'socio omt';
              $cod_promo = 0;
              $servdecod = $nro_servicio;
              $query = "insert into padron_producto_socio values(null,'$cedula_ben','$nro_servicio','8','$precio_base','$cod_promo','$fechafil','0','$fechafil','$nombreVendedor','$observacion','0',0,999,'ALTA',";
              $query .= "'2015-09-15','$numeroVendedor','0','0',0,2,'$idRelacion',0,'0','$empresa_marca',1,'$servdecod','$count','1','ALTA','1','0','0','0','0','$precio_base','0',0,'1', '$cedula')";

              if (mysqli_query($mysqli, $query)) {
                $response["result_omt"] = true;
                $response["message_omt"] = "Datos guardados correctamente.";
              } else {
                $response["result_omt"] = false;
                $response["message_omt"] = "Ocurrio error al guardar productos en el padron.";
                $log_content = "[ERROR][$log_date]|$numeroVendedor|ALTA|Ocurrio error al guardar productos en el padron (GRUPO FAMILIAR)|$query|" . mysqli_error($mysqli);
                file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
              }
            }
          }
        }
      } else {
        $response["result"] = false;
        $response["message"] = "Medio de pago inválido.";
        $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Medio de pago inválido|$query|" . mysqli_error($mysqli);
        file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
      }
      // ######################### FIN GUARDADO DE DATOS ######################### 
      // NUEVO REGISTRO EN HISTORICO VENTA
      $query = "SELECT id FROM padron_datos_socio WHERE cedula='$cedula'";
      if ($result = mysqli_query($mysqli, $query)) {
        $idPadron = mysqli_fetch_assoc($result)['id'];
        $query = "INSERT INTO historico_venta VALUES(NULL,30,$idPadron,1,'$fecha','ALTA A TRAVES DE CALL',11)";
        if (mysqli_query($mysqli, $query)) {
          $response['historico'] = 'Historico guardado correctamente';
        } else {
          $response['historico'] = 'Error al guardar el historico';
          $log_content = "[ERROR][$log_date]|$numero_vendedor|ALTA|Error al guardar el historico|$query|" . mysqli_error($mysqli);
          file_put_contents($log_file, $log_content . "\n", FILE_APPEND);
        }
      }
    }
    // FIN CICLO DE SOCIOS
  }
}

mysqli_close($mysqli);
mysqli_close($mysqli1310);
mysqli_close($mysqli250);
echo json_encode($response);
