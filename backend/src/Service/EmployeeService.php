<?php
namespace App\Service;

use App\Model\Employee;
use App\Utils\BaseService;  


class EmployeeService extends BaseService {

    public function __construct() {
        parent::__construct(new Employee());
    }

    public function getEmployees() {
        $employees = $this->model->get();
        return $employees ? $this->responseFound($employees, 'Empleados encontrados') : $this->responseNotFound();
    }

    public function getEmployee($employeeId) {
        // Validar ID
        if ($error = $this->validateId($employeeId)) {
            return $error;
        }

        // Validar existencia del empleado
        if ($error = $this->validateExists($employeeId)) {
            return $error;
        }

        $employee = $this->model->getByid($employeeId);
        return $this->responseFound($employee, 'Empleado encontrado');
    }

    public function createEmployee($data) {
        // Validar campos obligatorios
        $error = $this->validator->validateRequiredFields(['nombre', 'rol', 'telefono', 'correo'], $data);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }

        // Validar correo electrónico
        $error = $this->validator->validateEmail($data->correo);
        if ($error) return ['status' => 400, 'message' => $error];

        // Validar teléfono
        $error = $this->validator->validatePhone($data->telefono);
        if ($error) return ['status' => 400, 'message' => $error];

        // Crear el empleado
        $employee = $this->model->create($data);
        return ['status' => 201, 'message' => 'Empleado creado correctamente', 'data' => $employee];
    }

    public function updateEmployee($employeeId, $data) {
        // Validar ID
        if ($error = $this->validateId($employeeId)) {
            return $error;
        }

        // Validar existencia del empleado
        if ($error = $this->validateExists($employeeId)) {
            return $error;
        }

        // Validar datos específicos
        if (isset($data->correo) && $error = $this->validator->validateEmail($data->correo)) {
            return ['status' => 400, 'message' => $error];
        }

        if (isset($data->telefono) && $error = $this->validator->validatePhone($data->telefono)) {
            return ['status' => 400, 'message' => $error];
        }

        // Actualizar el empleado
        $this->model->update($employeeId, $data);
        return ['status' => 200, 'message' => 'Empleado actualizado correctamente', 'data' => $data];
    }

    public function deleteEmployee($employeeId) {
        // Validar ID
        if ($error = $this->validateId($employeeId)) {
            return $error;
        }

        // Validar existencia del empleado
        if ($error = $this->validateExists($employeeId)) {
            return $error;
        }

        // Eliminar el empleado
        $deleted = $this->model->delete($employeeId);
        return $deleted ? ['status' => 200, 'message' => 'Empleado eliminado correctamente'] : ['status' => 500, 'message' => 'Hubo un problema al eliminar el empleado'];
    }
}
?>
