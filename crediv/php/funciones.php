<?php

/** Registrar errores en la base de datos **/
/*
function registrar_errores($consulta, $nombre_archivo, $error)
{
    $conexion = connection(DB);
    $tabla = TABLA_LOG_ERRORES;

    $consulta = str_replace("'", '"', $consulta);
    $error = str_replace("'", '"', $error);

    $sql = "INSERT INTO {$tabla} (consulta, nombre_archivo, error, fecha_registro) VALUES ('$consulta', '$nombre_archivo', '$error', NOW())";
    $consulta = mysqli_query($conexion, $sql);

    return $consulta;
}
*/

/** Generar color random **/
function randomColor()
{
    $str = "#";
    for ($i = 0; $i < 6; $i++) {
        $randNum = rand(0, 15);
        switch ($randNum) {
            case 10:
                $randNum = "A";
                break;
            case 11:
                $randNum = "B";
                break;
            case 12:
                $randNum = "C";
                break;
            case 13:
                $randNum = "D";
                break;
            case 14:
                $randNum = "E";
                break;
            case 15:
                $randNum = "F";
                break;
        }
        $str .= $randNum;
    }
    return $str;
}

/** Reemplazar acentos de string **/
function remplazarAcentos($texto)
{

    //  $texto_parseado = eliminarAcentos($texto);
    $texto_parseado = $texto;

    $remplazar_array = array(
        "'" => '', '"' => ' ', '`' => ' ', '`' => '',
        'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
        'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',  'Ò' => 'O', 'Ó' => 'O',
        'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
        'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o',  'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ñ' => 'N', 'ñ' => 'n', '°' => ' ', 'Â' => ' ',
        'â' => 'a', '™' => ' ', '€' => '', 'Âº' => '', '/' => '/'
    );

    $texto_parseado = strtr($texto_parseado, $remplazar_array);
    $texto_parseado = preg_replace('([^A-Za-z0-9 ])', '', $texto_parseado);
    return $texto_parseado;
}

/** Eliminar acentos de string **/
function eliminarAcentos($cadena)
{
    $especial = @utf8_decode('ÁÀÂÄáàäâªÉÈÊËéèëêÍÌÏÎíìïîÓÒÖÔóòöôÚÙÛÜúùüûÑñÇç³€™º');
    $reemplazar = @utf8_decode('AAAAaaaaaEEEEeeeeIIIIiiiiOOOOooooUUUUuuuuNnCcA    ');
    for ($i = 0; $i <= strlen($cadena); $i++) {
        for ($f = 0; $f < strlen($especial); $f++) {
            $caracteri = substr($cadena, $i, 1);
            $caracterf = substr($especial, $f, 1);
            if ($caracteri === $caracterf) {
                $cadena = substr($cadena, 0, $i) . substr($reemplazar, $f, 1) . substr($cadena, $i + 1);
            }
        }
    }
    return  $cadena;
}

/** Generar hash de largo especificado **/
function generarHash($largo)
{
    $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($caracteres_permitidos), 0, $largo);
}

/** Controlar extención del archivo **/
function controlarExtension($files, $tipo)
{
    $validar_extension = $tipo;
    $valido = 0;
    for ($i = 0; $i < count($files["name"]); $i++) {
        $extension_archivo = strtolower(pathinfo(basename($files["name"][$i]), PATHINFO_EXTENSION));

        if (in_array($extension_archivo, $validar_extension)) {
            $valido++;
        } else {
            $valido = 0;
        }
    }
    return $valido;
}

/** Subir archivo al servidor **/
function subir_documento($archivo, $nombre_app)
{
    $extension_archivo = strtolower(pathinfo(basename($archivo["name"]), PATHINFO_EXTENSION));
    $nombre_archivo =  generarHash(20) . '.' . $extension_archivo;
    $ruta_origen = $archivo["tmp_name"];
    $destino = "../../assets/documentos/" . $nombre_archivo;
    $destinoBD = $nombre_app . "assets/documentos/" . $nombre_archivo;
    $respuesta = "";

    if (move_uploaded_file($ruta_origen, $destino)) {
        $respuesta['nombre_archivo'] = $nombre_archivo;
        $respuesta['destino_servidor'] = $destino;
        $respuesta['destinoBD'] = $destinoBD;
    }

    return $respuesta == "" ? false : $respuesta;
}

/** Crear el html para el mail */
function htmlBodyEmail($texto)
{
    $html = '
        <!DOCTYPE html>
        <html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="x-apple-disable-message-reformatting">
        <style>
            table, td, div, h1, p {font-family: Arial, sans-serif;}
        </style>
        </head>
        <body style="margin:0;padding:0;">
        <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
            <tr>
            <td align="center" style="padding:0;">
                <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                <tr>
                    <td align="center" style="padding:40px 0 30px 0;background:#304689;">
                    <img src="https://i.ibb.co/WkqgSFv/111-fotor-bg-remover-2023051092030.png" alt="" width="300" style="height:auto;display:block;" />
                    </td>
                </tr>
                <tr>
                    <td style="padding:36px 30px 42px 30px;">
                    <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                        <tr>
                        <td style="padding:0 0 36px 0;color:#153643;">
                            <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">' . $texto["titulo"] . '</h1>
                            <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">' . $texto["cabecera"] . '</p>
                            <p style="margin:0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">' . $texto["detalle1"] . '<a href="#" style="color: #942f4a;text-decoration:underline;">' . $texto["detalle2"] . '</a></p>
                        </td>
                        </tr>
                    </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px;background: #942f4a !important;">
                    
                    </td>
                </tr>
                </table>
            </td>
            </tr>
        </table>
        </body>
        </html>';

    return $html;
}

/** Enviar mail **/
function EnviarMail($area_carga, $bodyHtml, $ccs = null)
{
    $configuracion = [
        "host" => "smtp.gmail.com",
        "port" => 587,
        "username" => "no-responder@vida.com.uy",
        "password" => "2k8.vida",
        "from" => "no-responder@vida.com.uy",
        "fromname" => $area_carga,
    ];

    $datos = [
        "email" => "s.nunez@vida.com.uy",
        "nombre" => "Desarrollo"
    ];

    $asunto = "Aviso Para Personal";

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $configuracion["host"];
    $mail->SMTPAuth = true;
    $mail->Username = $configuracion["username"];
    $mail->Password = $configuracion["password"];
    $mail->SMTPSecure = 'tls';
    $mail->Port = $configuracion["port"];
    $mail->Subject = $asunto;
    $mail->isHTML(true);
    $mail->setFrom($configuracion["from"], $configuracion["fromname"]);
    $mail->addReplyTo($configuracion["from"], $configuracion["fromname"]);
    $mail->addAddress($datos["email"], $datos["nombre"]);
    if ($ccs != null) {
        foreach ($ccs as $cc) {
            $mail->addCC($cc["email"], $cc["nombre"]);
        }
    }
    $mail->Body = $bodyHtml;

    if ($mail->send()) {
        return true;
    } else {
        return $mail->ErrorInfo;
    }
}

/** Devolver JSON con error **/
function devolver_error($mensaje)
{
    $response['error'] = true;
    $response['mensaje'] = $mensaje;
    die(json_encode($response));
}
