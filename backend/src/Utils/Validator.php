<?php
namespace App\Utils;

use DateTime;
use Exception;

class Validator {
    // Validar datos obligatorios
    public static function validateRequiredFields($fields, $data) {
        foreach ($fields as $field) {
            if (empty($data->$field)) {
                return "El campo $field es obligatorio";
            }
        }
        return null; // Sin errores
    }
    public function validateNumbers(array $fields, $data) {
        foreach ($fields as $field) {
            // Verificar si el campo existe en $data antes de validarlo
            if (property_exists($data, $field) && !is_numeric($data->$field)) {
                return "El campo $field debe ser un número";
            }
        }
        return null;
    }
    

    // Validar correo electrónico
    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El correo electrónico no es válido";
        }
        return null;
    }

    // Validar fecha con coherencia
    public static function validateDate($date, $minYear = 1900, $maxYear = null) {
        if (strtotime($date) === false || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return "La fecha no es válida. Debe tener el formato YYYY-MM-DD";
        }

        $year = (int)date('Y', strtotime($date));
        $maxYear = $maxYear ?? (int)date('Y');
        if ($year < $minYear || $year > $maxYear) {
            return "La fecha debe estar entre $minYear y $maxYear";
        }
        return null;
    }

    // Validar teléfono
    public static function validatePhone($phone) {
        if (!preg_match('/^\d{9}$/', $phone)) {
            return "El número de teléfono debe tener 9 dígitos";
        }
        return null;
    }

    public static function validateDates($data, $currentStartDate = null, $currentEndDate = null) {
        try {
            $currentStartDate = $currentStartDate ? new DateTime($currentStartDate) : null;
            $currentEndDate = $currentEndDate ? new DateTime($currentEndDate) : null;
            $currentDate = new DateTime();
    
            // Validar fecha de inicio
            if (!empty($data->fecha_inicio)) {
                $newStartDate = new DateTime($data->fecha_inicio);
                if ($currentEndDate && $newStartDate > $currentEndDate) {
                    return 'La fecha de inicio no puede ser posterior a la fecha de finalización';
                }
            }
    
            // Validar fecha de finalización
            if (!empty($data->fecha_fin)) {
                $newEndDate = new DateTime($data->fecha_fin);
                if ($currentStartDate && $newEndDate < $currentStartDate) {
                    return 'La fecha de finalización no puede ser anterior a la fecha de inicio';
                }
                if ($newEndDate > $currentDate) {
                    return 'La fecha de finalización no puede ser posterior a la fecha actual';
                }
            }
        } catch (Exception $e) {
            return 'Formato de fecha inválido';
        }
    
        return null;
    }
    
    
}

?>