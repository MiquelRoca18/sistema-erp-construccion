<?php
namespace App\Service;

use App\Model\Employee;
use App\Utils\Validator;

class EmployeeService {
    private $employeeModel;
    private $validator;

    public function __construct() {
        $this->employeeModel = new Employee();
        $this->validator = new Validator();
    }
    
    public function getEmployees(){
        $employees = $this->employeeModel->get();

        if ($employees) {
            return ['status' => 200, 'message' => 'Empleados encontrados', 'data' => $employees];
        } else {
            return ['status' => 404, 'message' => 'No se encontraron empleados'];
        }
    }

    public function getEmployee($employeeId){
        //Validar datos enviados
        if(empty($employeeId)){
            return ['status' => 400, 'message' => 'El ID del empleado es obligatorio'];
        }
        //Validar si existe el empleado
        if(!$this->employeeModel->exists($employeeId)){
            return ['status' => 404, 'message' => 'El empleado no existe'];
        }

        // Obtener empleado
        $employee = $this->employeeModel->getEmployee($employeeId);
        return ['status' => 200, 'message' => 'Empleado encontrado', 'data' => $employee];
    }

    public function createEmployee($data) {
        // Validar campos obligatorios
        $error = $this->validator->validateRequiredFields(['nombre', 'rol', 'telefono', 'correo'], $data);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }
    
        // Validar correo electrónico
        if ($error = $this->validator->validateEmail($data->correo)) {
            return ['status' => 400, 'message' => $error];
        }
    
        // Validar fecha de contratación
        if (isset($data->fecha_contratacion)) {
            if ($error = $this->validator->validateDate($data->fecha_contratacion)) {
                return ['status' => 400, 'message' => $error];
            }
        }
    
        // Validar teléfono
        if ($error = $this->validator->validatePhone($data->telefono)) {
            return ['status' => 400, 'message' => $error];
        }
    
        // Crear empleado
        $employee = $this->employeeModel->createEmployee($data);
        return ['status' => 201, 'message' => 'Empleado creado correctamente', 'data' => $employee];
    }
    
    public function updateEmployee($employeeId, $data){
        // Validar si el ID del empleado es válido
        if (empty($employeeId)) {
            return ['status' => 400, 'message' => 'El ID del empleado es obligatorio'];
        }
    
        // Validar si el empleado existe
        if (!$this->employeeModel->exists($employeeId)) {
            return ['status' => 404, 'message' => 'El empleado no existe'];
        }
    
        // Validar los datos recibidos (puedes agregar validaciones específicas si es necesario)
        // Si no hay datos enviados, devolver un error
        // if (empty((array) $data)) {
        //     return ['status' => 400, 'message' => 'No se enviaron datos para actualizar'];
        // }

        // Validar fecha de contratación
        if (isset($data->fecha_contratacion)) {
            if ($error = $this->validator->validateDate($data->fecha_contratacion)) {
                return ['status' => 400, 'message' => $error];
            }
        }
    
        // Aquí puedes agregar validaciones específicas para cada campo si lo deseas
        // Por ejemplo, validar correo, teléfono, etc.
        if (isset($data->correo) && $error = $this->validator->validateEmail($data->correo)) {
            return ['status' => 400, 'message' => $error];
        }
    
        if (isset($data->telefono) && $error = $this->validator->validatePhone($data->telefono)) {
            return ['status' => 400, 'message' => $error];
        }
    
        // Llamar al modelo para actualizar el empleado
        $this->employeeModel->updateEmployee($employeeId, $data);

        return ['status' => 200, 'message' => 'Empleado actualizado correctamente', 'data' => $data];
    }


    public function deleteEmployee($employeeId) {
        // Validar que el ID sea proporcionado
        if (empty($employeeId)) {
            return ['status' => 400, 'message' => 'El ID del empleado es obligatorio'];
        }
    
        // Validar si el empleado existe antes de intentar eliminar
        if (!$this->employeeModel->exists($employeeId)) {
            return ['status' => 404, 'message' => 'El empleado no existe'];
        }
    
        // Intentar eliminar el empleado
        $deleted = $this->employeeModel->deleteEmployee($employeeId);
    
        if ($deleted) {
            return ['status' => 200, 'message' => 'Empleado eliminado correctamente'];
        } else {
            return ['status' => 500, 'message' => 'Hubo un problema al eliminar el empleado'];
        }
    }
    
}
?>