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
    public static function validateDate($date, $minYear = 1900, $maxDate = null) {
        if (strtotime($date) === false || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return "La fecha no es válida. Debe tener el formato YYYY-MM-DD";
        }
    
        // Obtener el año de la fecha pasada
        $year = (int)date('Y', strtotime($date));
        
        // Si no se proporciona una fecha máxima, establecerla como mañana
        $maxDate = $maxDate ?? date('Y-m-d', strtotime('+1 day')); // Fecha de mañana
    
        // Comprobar si la fecha está dentro de los límites permitidos
        if ($year < $minYear) {
            return "La fecha debe ser mayor o igual a $minYear";
        }
        
        // Verificar que la fecha no exceda el día de mañana
        if (strtotime($date) > strtotime($maxDate)) {
            return "La fecha debe ser anterior o igual a $maxDate";
        }
    
        return null; // Si todo está bien
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
            $currentDate = new DateTime();
    
            // Si se envía la fecha en la solicitud y no está vacía, usarla; de lo contrario, usar el valor actual
            $start = (isset($data->fecha_inicio) && trim($data->fecha_inicio) !== '')
                ? trim($data->fecha_inicio)
                : $currentStartDate;
            $end = (isset($data->fecha_fin) && trim($data->fecha_fin) !== '')
                ? trim($data->fecha_fin)
                : $currentEndDate;
            
            // Si ambos valores están definidos, compararlos
            if ($start && $end) {
                $newStartDate = new DateTime($start);
                $newEndDate = new DateTime($end);
                if ($newStartDate > $newEndDate) {
                    return 'La fecha de inicio no puede ser posterior a la fecha de finalización';
                }
            }
            
            // Si la fecha de finalización (nueva o la existente) está definida, comprobar que no sea posterior a la fecha actual
            if ($end) {
                $newEndDate = new DateTime($end);
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