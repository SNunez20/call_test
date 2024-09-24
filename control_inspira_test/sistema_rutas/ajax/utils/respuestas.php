<?php

function dieJson($error = true, $mensaje = '', $args = [])
{
    die(json_encode(
        count($args) > 0 ?
            array_merge(['error' => $error, 'mensaje' => $mensaje], $args)
            : ['error' => $error, 'mensaje' => $mensaje]
    ));
}

/**
 * respuesta
 *
 * @param  mixed $respuesta true or false en json quedaria respuesta :true  ó respuesta: false
 * @param  mixed $mensaje String
 */
function respuesta($status, $text = "")
{
    return json_encode(
        array(
            "success" => $status,
            "text" => $text,
        )
    );
}

function jsonMessage($status = 'success', $icon = '', $text = '', $title = '')
{
    die(json_encode(array(
        'success' => $status,
        'icon' => $icon,
        'title' => $title,
        'text' => $text
    )));
}

function jsonErrorMessage($text, $errores = null)
{
    die(json_encode(array(
        'success' => false,
        'errores' => $errores,
        'html' => $text,
        'mensaje' => $text,
        'error' => true,
    )));
}
function jsonSuccessMessage($text, $title = 'Éxito')
{
    die(json_encode(array(
        'success' => true,
        'icon' => 'success',
        'title' => $title,
        'text' => $text
    )));
}

/**
 * joinMessages
 * 
 * @param array $messages
 * 
 * @return string
 */
function joinMessages($messages)
{
    return join(' ', array_map(function ($error) {
        return "<p>$error</p>";
    }, $messages));
}
