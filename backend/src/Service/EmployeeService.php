<?php
namespace App\Service;

use App\Model\Employee;
use App\Utils\BaseService;  
use App\Model\AuthModel;

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

        // Asignar valores predeterminados
        $data->fecha_contratacion = $data->fecha_contratacion ?? date('Y-m-d');

        // Validar correo electrónico
        $error = $this->validator->validateEmail($data->correo);
        if ($error) return ['status' => 400, 'message' => $error];

        // Validar teléfono
        $error = $this->validator->validatePhone($data->telefono);
        if ($error) return ['status' => 400, 'message' => $error];

        // Crear el empleado
        $employee = $this->model->create($data);

        // Obtener el nombre completo y separar en nombre y apellido
        $nombreCompleto = strtolower($data->nombre);   
        $nombreArray = explode(' ', $nombreCompleto);  

        $nombre = $nombreArray[0];  
        $apellido = $nombreArray[1];  

        // Crear nombre de usuario: 'nombre.apellido'
        $username = $nombre . '.' . $apellido;

        // Crear contraseña: primeras dos letras del nombre + primeras dos del apellido
        $password = strtolower(substr($nombre, 0, 2)) . strtolower(substr($apellido, 0, 2));
        $password_hash = password_hash($password, PASSWORD_DEFAULT);  

        // Crear el usuario en la tabla autenticación
        $authData = [
            'empleados_id' => $employee,  
            'username' => $username,      
            'password_hash' => $password_hash,  
            'id_rol' => 2      
        ];

        $authModel = new AuthModel();
        $authModel->create($authData);

        return ['status' => 201, 'message' => 'Empleado y autenticación creados correctamente', 'data' => $employee];
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
        $result = $this->model->update($employeeId, $data);
        return $result ? $this->responseUpdated('Tarea actualizada') : $this->responseError();
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
        $result = $this->model->delete($employeeId);
        return $result ? $this->responseDeleted('Tarea eliminada') : $this->responseError();   
    }
}
?>
