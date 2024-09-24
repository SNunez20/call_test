<?php


function reglasUsuarioSistema()
{
    return array(
        "usuario" => [
            "required" => ["error" => "El campo usuario es requerido"],
            "length" => ["min" => 3, "max" => 50, "error" => "El campo usuario debe tener entre 3 y 50 caracteres"],
            'alpha'    => ['error' => 'Usuario incompleto o incorrecto.'],
        ],
        "nombre" => [
            "required" => ["error" => "El campo nombre es requerido"],
            "length" => ["min" => 3, "max" => 50, "error" => "El campo nombre debe tener entre 3 y 50 caracteres"],
            'alpha'    => ['error' => 'Nombre incompleto o incorrecto.'],
        ],
        "email" => [
            "required" => ["error" => "El campo Email es requerido"],
            'email'    => ['error' => 'Email incorrecto.'],
            "length" => ["min" => 3, "max" => 60, "error" => "El campo Email debe tener entre 3 y 60 caracteres"],
        ],

    );
}

function reglasPassword()
{
    return array(
        "password" => [
            "required" => ["error" => "El campo Contraseña es requerido"],
            'password'    => ['error' => 'La Contraseña debe tener una Máyusculas y 5 caracteres como mínimo .'],
            "length" => ["min" => 5, "max" => 60, "error" => "El campo Contraseña debe tener entre 3 y 60 caracteres"],
        ],
    );
}
