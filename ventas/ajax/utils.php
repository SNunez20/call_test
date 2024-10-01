<?php
function cristianBaez($id, $nombre)
{
    if ($id == 26) return "Christian Baez Call";
    elseif ($id == 25) return "Christian Baez Calle";
    return $nombre;
}
function escapeString($fields, $conexion)
{
    if (is_array($fields)) {
        foreach ($fields as $key => $field) {
            if (!is_array($field)) {
                $fields[$key] = is_numeric($fields[$key]) ? $fields[$key] : mysqli_real_escape_string($conexion, trim($fields[$key]));
            }
        }
    } else mysqli_real_escape_string($conexion, trim($fields));
    return $fields;
}

function curlCalcularPrecio($json, $args = [])
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => URL_MOTOR_DE_PRECIOS,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_HTTPHEADER => [
            "Accept: */*",
            "Content-Type: application/json"
        ],
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    return ($err) ? (json_encode([
        'error' => true,
        'mensaje' => 'No pudimos obtener el precio, contactese con el administrador',
        'horas' => $args['horas'],
        'id_servicio' => $args['id_servicio'],
        'reintegro' => $args['reintegro'],
        'forma_de_pago' => $args['forma_de_pago'],
        'convenio' => $args['convenio'],
        'err' => $err
    ])) : $response;
}


function edad($fecha_nacimiento)
{
    $fecha_nacimiento = new DateTime($fecha_nacimiento);
    $fecha_actual = new DateTime(date("Y-m-d"));
    $dif = $fecha_actual->diff($fecha_nacimiento);
    return $dif->format("%y");
}

function generarCodigoSesion()
{
    $length = 10;
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, $charactersLength - 1)];
    }
    return $code;
}

function verificarVendedor($ci_vendedor)
{
    global $mysqli;
    $qvendedor = "SELECT * FROM vendedores WHERE cedula = '$ci_vendedor' AND activo=1 LIMIT 1";
    $rvendedor = mysqli_query($mysqli, $qvendedor);

    if (mysqli_num_rows($rvendedor) > 0) {
        $row = mysqli_fetch_assoc($rvendedor);
        return [
            'tipo_vendedor' => $row['tipo_vendedor'],
            'cedula_vendedor' => $row['cedula'],
            'nombre_vendedor' => $row['nombre'],
            'mail_vendedor' => $row['mail'],
            'idvendedor' => $row['id'],
            'vendedor_local' => 1,
            'call' => '-',
            'civendedorcall' => '-'
        ];
    } else {
        $json = file_get_contents("http://192.168.1.13/callPY/wsusuario/index.php?cedula=" . $ci_vendedor);
        $usuario = json_decode($json);
        if ($usuario->usuario) {
            return [
                'tipo_vendedor' => "Vendedor Call",
                'cedula_vendedor' => $usuario->cedula,
                'nombre_vendedor' => $usuario->nombre,
                'mail_vendedor' => "callpy@vida.com.uy",
                'idvendedor' => 0,
                'vendedor_local' => 0,
                'call' => $usuario->nombrecall,
                'civendedorcall' => $usuario->cedula
            ];
        }
    }

    return [];
}

function generarHash($largo = 20)
{
    $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    return  strtoupper(substr(str_shuffle($caracteres_permitidos), 0, $largo));
}


function estado($estado)
{
    global $mysqli;
    $estado = (int)$estado;
    $query = mysqli_query($mysqli, "SELECT id,estado FROM estados WHERE id= $estado");
    $estado_return = mysqli_fetch_assoc($query)['estado'];
    return  $estado_return;
}

function fechaDesdeHasta($tabla = "")
{
    $_REQUEST["desde"] = empty($_REQUEST["desde"]) ? "" : $_REQUEST["desde"];
    $_REQUEST["hasta"] = empty($_REQUEST["hasta"]) ? "" : $_REQUEST["hasta"];


    $anio_mes_dia_anterior = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
    $dia_anterior = date('d', strtotime($anio_mes_dia_anterior));
    $mes_anterior = date('m', strtotime($anio_mes_dia_anterior));
    $anio_actual_anterior = date('Y', strtotime($anio_mes_dia_anterior));
    $fecha_anterior = date('Y-m-d', strtotime("{$dia_anterior}-{$mes_anterior}-{$anio_actual_anterior}"));

    $fecha_actual = date("Y-m-d");

    $buscar_fecha = " AND (CAST({$tabla}fechafil AS date) >= '{$fecha_anterior}' AND CAST({$tabla}fechafil AS date) <= '{$fecha_actual}')";

    if ($_REQUEST["desde"] != '' && $_REQUEST["hasta"] != '') {
        $buscar_fecha = " AND (CAST({$tabla}fechafil AS date) >= '{$_REQUEST["desde"]}' AND CAST({$tabla}fechafil AS date) <= '{$_REQUEST["hasta"]}')";
    }

    return $buscar_fecha;
}


function estadoBuscar($tabla = "")
{
    $estado_query = "";
    if (isset($_REQUEST['estado'])) {
        $estado = $_REQUEST['estado'];
        $estados_array = ["{$tabla} estado = 6", 3, 1, 678, 692, 7, 2, 4];
        $implode_estados = implode(" OR {$tabla}estado = ", $estados_array);

        if ($estado == 'todos') $estado_query = " AND ($implode_estados) ";
        else {
            $estado = (int)$estado;
            $estado_query =  " AND {$tabla}estado = $estado";
        }
    }

    return $estado_query;
}

function metodoPago($id)
{
    $metodos = [
        '1' => 'Domiciliario',
        '2' => 'tarjeta',
        '3' => 'convenio'
    ];
    return $metodos[$id];
}
function LogDB($nombre, $consulta, $e)
{
    Logger('', " Archivo: {$nombre} | Error : {$e['error']} | Consulta: {$consulta}    ", "error");
}



function query($conexion, $query)
{
    try {
        return mysqli_query($conexion, $query);
    } catch (Exception $e) {
        $error = debug_backtrace();
        $nombre = isset($error[0]['file']) ? $error[0]['file'] : '';
        $linea = isset($error[0]['line'])  ? $error[0]['line'] : '';
        $err = isset($error[0]['args'][0]) ? $error[0]['args'][0]->error : 'Error desconocido';
        $e = [
            'linea' => $linea,
            'error' => $err,
        ];
        LogDB($nombre, $query, $e);
        return false;
    }
}


function estados($id_estado)
{
    $id_estado = (int)$id_estado;

    global $estados_array;

    return isset($estados_array[$id_estado]) ? $estados_array[$id_estado] : estado($id_estado);
}

function convenio($id_radio_convenio)
{
    global $mysqli;
    $query = "SELECT nombre_convenio FROM radios_convenios WHERE radio = '{$id_radio_convenio}' LIMIT 1";
    $convenio_query = query($mysqli, $query);
    return mysqli_num_rows($convenio_query) > 0 ? mysqli_fetch_assoc($convenio_query)['nombre_convenio'] : null;
}
