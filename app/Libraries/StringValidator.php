<?php

namespace App\Libraries;

class StringValidator
{
    public static function isValidString($str)
    {
        // Elimina los espacios en blanco a los lados del string y verifica si no es vacío ni nulo
        return isset($str) && strlen(trim($str))>0;
    }
}
