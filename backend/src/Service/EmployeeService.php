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
    
        // Validar que el nombre contenga exactamente dos palabras separadas por un espacio
        $nombreLimpio = trim($data->nombre);
        $nombreArray = preg_split('/\s+/', $nombreLimpio);
        if (count($nombreArray) !== 2) {
            return [
                'status' => 400, 
                'message' => 'El nombre debe contener exactamente dos palabras (nombre y apellido) separadas por un solo espacio. Ejemplo: "lola flores".'
            ];
        }
    
        // Asignar valores predeterminados si no se envía fecha de contratación
        $data->fecha_contratacion = $data->fecha_contratacion ?? date('Y-m-d');
    
        // Validar correo electrónico
        $error = $this->validator->validateEmail($data->correo);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }
    
        // Validar teléfono
        $error = $this->validator->validatePhone($data->telefono);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }
    
        // Validar fecha de contratación: permitir fechas desde hoy hasta 10 años en el pasado
        $fechaContratacion = strtotime($data->fecha_contratacion);
        $hoy = strtotime(date('Y-m-d'));
        $limite = strtotime('-10 years');
    
        if ($fechaContratacion > $hoy) {
            return ['status' => 400, 'message' => 'La fecha de contratación no puede ser futura.'];
        }
        if ($fechaContratacion < $limite) {
            return ['status' => 400, 'message' => 'La fecha de contratación es demasiado antigua.'];
        }
    
        // Crear el empleado
        $employee = $this->model->create($data);
    
        // Procesar nombre: ya tenemos dos palabras garantizadas en $nombreArray
        $nombre = $nombreArray[0];
        $apellido = $nombreArray[1];
    
        // Crear nombre de usuario en minúsculas: 'nombre.apellido'
        $username = strtolower($nombre . '.' . $apellido);
    
        // Crear contraseña: primeras dos letras del nombre + primeras dos del apellido, en minúsculas
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

         // Obtener las fechas actuales del proyecto
         $employee = $this->model->get($employeeId);
         $fechaContratacion = $employee['fecha_inicio'] ?? null;
 
         // Validar fechas usando la función general
         if (isset($data->fecha_contratacion)) {
            if ($error = $this->validator->validateDate($data->fecha_contratacion)) {
                return ['status' => 400, 'message' => $error];
            }
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
