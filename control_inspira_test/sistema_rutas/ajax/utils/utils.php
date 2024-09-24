<?php

function redondeo($total)
{
    $resultado = 0;

    $total = round($total);

    $total = "{$total}";

    $ultimo_digito = substr($total, -1);

    $ultimo_digito = (int)$ultimo_digito;

    if ($ultimo_digito !== 0 && $ultimo_digito < 5) {
        $ultimo_digito = 5;

        $resultado = substr($total, 0, -1) . "{$ultimo_digito}";
    } elseif ($ultimo_digito !== 5 && $ultimo_digito > 5) {
        $resto = 10 - (int)$ultimo_digito;

        $resultado = (int) $total + $resto;
    } else {
        $resultado = $total;
    }

    return (int)$resultado;
}


function edad($fecha_nacimiento)
{
    $fecha_nacimiento = new DateTime($fecha_nacimiento);
    $fecha_actual = new DateTime(date("Y-m-d"));
    $dif = $fecha_actual->diff($fecha_nacimiento);
    return $dif->format("%y");
}
function generarHash($largo = 20)
{
    $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    return  strtoupper(substr(str_shuffle($caracteres_permitidos), 0, $largo));
}

/**
 * controlarExtension
 *
 * @param  mixed $files array imagenes $_FILES
 * @param  mixed $tipo  JPG, JPEG,PNG,ETC
 * @return void
 */
function controlarExtension($files, $tipo)
{
    $validar_extension = $tipo;
    $valido = 0;
    for ($i = 0; $i < count($files["name"]); $i++) {
        $extension_archivo = strtolower(pathinfo(basename($files["name"][$i]), PATHINFO_EXTENSION));

        if (in_array($extension_archivo, $validar_extension))  $valido++;
        else $valido = 0;
    }
    return $valido;
}


/**
 * subirArchivo
 *
 * @param  mixed $archivo  $_FILES["imagen_reclamo"];
 * @param  mixed $destino_distinto  :  "../../documentacion/" por defecto null
 */
function subirArchivo($archivo, $destino_distinto = null)
{
    //  $archivo = $_FILES["imagen_reclamo"];
    if (controlarExtension($archivo, array("png", "jpeg", "jpg", "doc", "docx", "bmp", "msg", "xls", "xlsx", "pdf")) > 0) {

        for ($i = 0; $i < count($archivo["name"]); $i++) {
            $extension_archivo = strtolower(pathinfo(basename($archivo["name"][$i]), PATHINFO_EXTENSION));
            $nombre_archivo =  generarHash(20) . '.' . $extension_archivo;
            $destino = $destino_distinto != null ? "../../documentacion/" . $nombre_archivo : $destino_distinto . "" . $nombre_archivo;

            if (move_uploaded_file($archivo["tmp_name"][$i], $destino)) return true;
            else return false; //Error al subir al servidor

        }
    } else  return false;    //Error de tipo de archivo

}


/**
 * recortarCampo
 *
 * @param  string | Array $campo  EJ: $_REQUEST["descripcion"] o array de campos o strings
 * @param  mixed $largo 60 ( siendo 60 un "int" para que muestre hasta 60 caracteres)
 */
function recortarString($campo, $largo = 60)
{
    if (is_array($campo)) {
        foreach ($campo as $key => $c) {
            if (!is_array($c)) $campo[$key] = remplazarString($c, $largo);
        }
    } else $campo = remplazarString($campo, $largo);

    return $campo;
}

function remplazarString($campo, $largo)
{
    $largo = intval($largo);
    if (strlen($campo) > $largo) {
        $br  = array("<br />", "<br>", "<br/>");
        $campo = str_ireplace($br, "\r\n", $campo);

        $campo_sin_editar = mb_convert_encoding($campo, 'UTF-8', 'UTF-8');
        $campo = substr($campo, 0, $largo) . " ...<a href='#' onclick='verMasTabla(event,`" . $campo_sin_editar . "`);'> Ver Más</a>";
        $campo = mb_convert_encoding($campo, 'UTF-8', 'UTF-8');
    }
    return $campo;
}

function convertUtf8($array, $campo)
{

    foreach ($array as $key => $ar) {
        $array[$key][$campo] = mb_convert_encoding($array[$key][$campo], 'UTF-8', 'UTF-8');
    }
    return $array;
}

function validarForm($request, $reglas, $array = false)
{

    $validador = new Validate($request, $reglas);

    if (!$validador->exec()) {
        $mensajes = $validador->getMessages();
        if ($array == false) {
            $mensaje_con_errores = '';

            foreach ($mensajes as $mens) {
                $mensaje_con_errores .= '<div style="color:red;"><b>' . end($mens) . '</b></div><br>';
            }

            die(jsonErrorMessage($mensaje_con_errores));
        } else {
            return $mensajes;
        }
    } else {
        return true;
    }
}


function fechaMayor($fecha)
{
}
