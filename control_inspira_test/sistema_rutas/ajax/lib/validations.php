<?php

/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Kumbia
 * @package    Validate
 *
 * @copyright  Copyright (c) 2005 - 2020 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    https://github.com/KumbiaPHP/KumbiaPHP/blob/master/LICENSE   New BSD License
 */

/**
 * Validate es una Clase que realiza validaciones Lógicas
 *
 * @category   KumbiaPHP
 * @package    validate
 */
class Validations
{
    /**
     * Constantes para definir los patrones
     */

    /*
     * El valor deber ser solo letras y números
     */
    const IS_ALPHANUM = '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]*$/mu';

    /**
     * Solo letras
     */
    const IS_ALPHA    = '/^(?:[^\W\d_]|([ ]))*$/mu';

    /**
     * Almacena la Expresion Regular
     *
     * @var String
     */
    public static $regex = NULL;


    /**
     * Valida que sea numérico
     * @param  mixed $check Valor a ser chequeado
     * @return bool
     */
    public static function numeric($check)
    {
        return is_numeric($check);
    }

    /**
     * Valida que int
     *
     * @param int $check
     * @return bool
     */
    public static function int($check)
    {
        return filter_var($check, FILTER_VALIDATE_INT);
    }

    /**
     * Valida que una cadena este entre un rango.
     * Los espacios son contados
     * Retorna true si el string $value se encuentra entre min and max
     *
     * @param string $value
     * @param array $param
     * @return bool
     */
    public static function maxlength($value, $param)
    {
        $max = isset($param['max']) ? $param['max'] : 0;
        return !isset($value[$max]);
    }

    /**
     * Valida longitud de la cadena
     */
    public static function length($value, $param)
    {
        $param = array_merge(array(
            'min' => 0,
            'max' => 9e100,
        ), $param);
        $length = strlen($value);
        return ($length >= $param['min'] && $length <= $param['max']);
    }

    /**
     * Valida que es un número se encuentre
     * en un rango minímo y máximo
     *
     * @param int $value
     * @param array $param min, max
     */
    public static function range($value, $param)
    {   
        $min = isset($param['min']) ? $param['min'] : 0;
        $max = isset($param['max']) ? $param['max'] : 10;
        $int_options = array('options' => array('min_range' => $min, 'max_range' => $max));
        return filter_var($value, FILTER_VALIDATE_INT, $int_options);
    }

    /**
     * Valida que un valor se encuentre en una lista
     * Retorna true si el string $value se encuentra en la lista $list
     *
     * @param string $value
     * @param array $param
     * @return bool
     */
    public static function select($value, $param)
    {
        $list = isset($param['list']) && is_array($param['list']) ? $param['list'] : array();
        return in_array($value, array_keys($list));
    }

    /**
     * Valida que una cadena sea un mail
     * @param string $mail
     * @return bool
     */
    public static function email($mail)
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Valida URL
     *
     * @param string $url
     * @return bool
     */
    public static function url($url, $param)
    {
        $flag = isset($param['flag']) ? $param['flag'] : 0;
        return filter_var($url, FILTER_VALIDATE_URL | $flag);
    }

    /**
     * Valida que sea una IP, por defecto v4
     * TODO: Revisar este método
     * @param String $ip
     * @return bool
     */
    public static function ip($ip, $flags = FILTER_FLAG_IPV4)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, $flags);
    }

    /**
     * Valida que un string no sea null
     *
     * @param string $check
     * @return bool
     */
    public static function required($check)
    {
        return (bool) strlen(trim($check));
    }

    /**
     * Valida que un String sea alpha-num (incluye caracteres acentuados)
     * TODO: Revisar este método
     *
     * @param string $string
     * @return bool
     */
    public static function alphanum($string)
    {
        return self::pattern($string, array('regexp' => self::IS_ALPHANUM));
    }

    /**
     * Valida que un String sea alpha (incluye caracteres acentuados y espacio)
     *
     * @param string $string
     * @return bool
     */
    public static function alpha($string)
    {
        return self::pattern($string, array('regexp' => self::IS_ALPHA));
    }


    /**
     * Valida una fecha
     * @param string $value fecha a validar acorde al formato indicado
     * @param array $param como en DateTime
     * @return boolean
     */
    public static function date($value, $param)
    {
        $format = isset($param['format']) ? $param['format'] : 'Y-m-d';
        $date = DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) == $value;
    }

    /**
     * Valida un string dada una Expresion Regular
     *
     * @param string $check
     * @param array $param  regex
     * @return bool
     */
    public static function pattern($check, $param)
    {
        $regex = isset($param['regexp']) ? $param['regexp'] : '/.*/';
        return empty($check) || FALSE !== filter_var($check, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regex)));
    }

    /**
     * Valida si es un número decimal
     *
     * @param string $value
     * @param array $param
     * @return boolean
     */
    public static function decimal($value, $param)
    {
        $decimal = isset($param['decimal']) ? $param['decimal'] : ',';
        return filter_var($value, FILTER_VALIDATE_FLOAT, array('options' => array('decimal' => $decimal)));
    }

    /**
     * Valida si los valores son iguales
     *
     * @param string $value
     * @param array $param
     * @param object $obj
     * @return boolean
     */
    public static function equal($value, $param, $obj)
    {
        $equal = isset($param['to']) ? $param['to'] : '';
        return ($obj->$equal == $value);
    }

    /**
     * Devuelve el mensaje por defecto de una validación
     * @param string $key
     * @return string
     */
    public static function getMessage($key)
    {
        $arr  = array(
            'required' => 'Este campo es requerido',
            'alphanum' => 'Debe ser un valor alfanumérico',
            'alpha'    => 'Solo caracteres alfabeticos',
            'length'   => 'Longitud incorrecta',
            'email'    => 'Email no válido',
            'pattern'  => 'El valor no posee el formato correcto',
            'date'     => 'Fecha no valida',
            'range'    => 'Error en el campo ingresado',
        );
        return $arr[$key];
    }


    // Validaciones personalizadas
    /**
     * dni
     * 
     * @param string $dni
     * 
     * @return boolean
     */
    public static function dni($dni)
    {
        return preg_match('/^\d{8}$/', $dni);
        // if (preg_match('/^\d{7,8}$/', $dni)) return true;

        // if (preg_match('/^\d{8}[a-zA-Z]$/', $dni)) {
        //     $numero =  substr($dni, 0, 8);
        //     $letra = substr($dni, 8, 1);
        //     return strtoupper($letra) === substr('TRWAGMYFPDXBNJZSQVHLCKET', $numero % 23, 1);
        // }
        // return false;
    }

    /**
     * edad
     *
     * @param  mixed $fecha_nacimiento  ("Y-m-d")
     * @return Int
     */
    public static function edad($fecha_nacimiento)
    {
        $fecha_nacimiento = new DateTime($fecha_nacimiento);
        $fecha_actual = new DateTime(date("Y-m-d"));
        $dif = $fecha_actual->diff($fecha_nacimiento);
        return $dif->format("%y");
    }

    /**
     * esMayorDeEdad
     *
     * @param  mixed $fecha_nacimiento ("Y-m-d")
     * @return Boolean
     */
    public static function esMayorDeEdad($fecha_nacimiento)
    {
        return self::edad($fecha_nacimiento) >= 18 ?  true : false;
    }
    public static function ci($ci)
    {
        $validationDigit = $ci[-1];
        $ci = str_pad($ci, 7, '0', STR_PAD_LEFT);
        $a = 0;
        $baseNumber = "2987634";
        for ($i = 0; $i < 7; $i++) {
            $baseDigit = $baseNumber[$i];
            $ciDigit = $ci[$i];

            $a += (intval($baseDigit) * intval($ciDigit)) % 10;
        }
        $result = $a % 10 == 0 ? 0 : 10 - $a % 10;
        return (int)$validationDigit == (int)$result;
    }


    public static function password($password)
    {
        $minLevel = 3;
        $strength = 0;

        $strength += preg_match('/[A-Z]+/', $password) ? 1 : 0;
        $strength += preg_match('/[a-z]+/', $password) ? 1 : 0;
        $strength += preg_match('/[0-9]+/', $password) ? 1 : 0;
        $strength += preg_match('/[\W]+/', $password) ? 1 : 0;

        return $strength >= $minLevel;
    }
    public static function number($numero)
    {
        return is_numeric($numero);
    }
    public static function entre($value,$params){
        if($value!==''){
            if(strlen($value)<$params['min'] && strlen($value)>$params['max']) return true;
            else return false;
        }
        return true;
    }
}
