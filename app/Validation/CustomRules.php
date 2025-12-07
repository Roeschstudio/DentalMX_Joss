<?php

namespace App\Validation;

use CodeIgniter\HTTP\IncomingRequest;

class CustomRules
{
    /**
     * Valida que un texto solo contenga letras y espacios
     */
    public function alpha_space(string $str): bool
    {
        return (bool) preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $str);
    }

    /**
     * Valida que un texto solo contenga letras, números y espacios
     */
    public function alpha_numeric_space(string $str): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/', $str);
    }

    /**
     * Valida caracteres seguros (sin scripts o código malicioso)
     */
    public function safe_chars(string $str): bool
    {
        // Permitir caracteres comunes pero bloquear scripts y código
        $pattern = '/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_.,()#ºª\/\\]+$/';
        return (bool) preg_match($pattern, $str);
    }

    /**
     * Valida formato de teléfono mexicano
     */
    public function phone_valid(string $str): bool
    {
        // Acepta 10 dígitos consecutivos
        return (bool) preg_match('/^\d{10}$/', $str);
    }

    /**
     * Valida contraseña fuerte
     */
    public function strong_password(string $str): bool
    {
        // Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número
        return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $str);
    }

    /**
     * Valida email único considerando edición
     */
    public function unique_email(string $str, string $field, array $data): bool
    {
        // Parsear el campo: tabla.columna,id_column
        $parts = explode(',', $field);
        $tableColumn = $parts[0];
        $idColumn = $parts[1] ?? 'id';
        
        [$table, $column] = explode('.', $tableColumn);
        
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        
        // Si estamos editando, excluir el registro actual
        if (isset($data[$idColumn]) && !empty($data[$idColumn])) {
            $builder->where($idColumn . ' !=', $data[$idColumn]);
        }
        
        $count = $builder->where($column, $str)->countAllResults();
        
        return $count === 0;
    }

    /**
     * Valida CURP mexicana
     */
    public function curp_valid(string $str): bool
    {
        return (bool) preg_match('/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/', strtoupper($str));
    }

    /**
     * Valida RFC mexicano
     */
    public function rfc_valid(string $str): bool
    {
        return (bool) preg_match('/^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/', strtoupper($str));
    }

    /**
     * Valida que un número sea decimal con máximo 2 decimales
     */
    public function decimal_two_places(string $str): bool
    {
        return (bool) preg_match('/^\d+(\.\d{1,2})?$/', $str);
    }
}
