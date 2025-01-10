<?php
namespace App\Utils;

use App\Utils\Validator;

abstract class BaseService {
    protected $model;
    protected $validator;

    public function __construct($model) {
        $this->model = $model;
        $this->validator = new Validator();
    }

    // Validar si el ID está vacío
    protected function validateId($id) {
        if (empty($id)) {
            return ['status' => 400, 'message' => 'El ID es obligatorio'];
        }
        return null;
    }

    // Validar si el recurso existe (proyecto o empleado)
    protected function validateExists($id) {
        if (!$this->model->exists($id)) {
            return ['status' => 404, 'message' => 'El recurso no existe'];
        }
        return null;
    }

    // Respuesta estándar para datos no encontrados
    protected function responseNotFound() {
        return ['status' => 404, 'message' => 'No se encontraron recursos'];
    }

    // Respuesta estándar para datos encontrados
    protected function responseFound($data, $message = 'Recursos encontrados') {
        return ['status' => 200, 'message' => $message, 'data' => $data];
    }
}
?>
