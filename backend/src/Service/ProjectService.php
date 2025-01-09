<?php
    namespace App\Service;

    use App\Model\Project;
    use App\Model\Employee;
    use App\Utils\Validator;
    use \DateTime;
    use \Exception;

    class ProjectService {
        private $projectModel;
        private $employeeModel;
        private $validator;

        public function __construct() {
            $this->projectModel = new Project();
            $this->employeeModel = new Employee();
            $this->validator = new Validator();
        }

        public function getProjects() {
            $projects = $this->projectModel->get();
        
            if ($projects) {
                return ['status' => 200, 'message' => 'Proyectos encontrados', 'data' => $projects];
            } else {
                return ['status' => 404, 'message' => 'No se encontraron proyectos'];
            }
        }
        

        public function getProject($projectId){
            //Validar datos enviados
            if(empty($projectId)){
                return ['status' => 400, 'message' => 'El ID del proyecto es obligatorio'];
            }
            //Validar si existe el proyecto
            if(!$this->projectModel->exists($projectId)){
                return ['status' => 404, 'message' => 'El proyecto no existe'];
            }

            // Obtener proyecto
            $project = $this->projectModel->getProject($projectId);
            return ['status' => 200, 'message' => 'Proyecto encontrado', 'data' => $project];
        }

        public function createProject($data) {
            // Validar campos obligatorios
            $error = $this->validator->validateRequiredFields(['nombre_proyecto', 'descripcion'], $data);
            if ($error) {
                return ['status' => 400, 'message' => $error];
            }
        
            // Asignar valores predeterminados
            $data->estado = $data->estado ?? 'pendiente'; // Estado por defecto
            $data->fecha_inicio = $data->fecha_inicio ?? date('Y-m-d'); // Fecha de hoy por defecto
            $data->fecha_fin = $data->fecha_fin ?? null; // Fecha de fin como NULL por defecto
        
            // Validar que el responsable exista
            if (!empty($data->responsable_id)) {
                $employeeExists = $this->employeeModel->exists($data->responsable_id);
                if (!$employeeExists) {
                    return ['status' => 404, 'message' => 'El responsable no existe'];
                }
            } else {
                return ['status' => 400, 'message' => 'El responsable es obligatorio'];
            }
        
            // Llamar al modelo para crear el proyecto
            $projectId = $this->projectModel->createProject($data);
            if ($projectId) {
                return ['status' => 201, 'message' => 'Proyecto creado exitosamente', 'data' => $projectId];
            }
        
            // Si ocurre algún error inesperado
            return ['status' => 500, 'message' => 'Error al crear el proyecto'];
        }

        public function updateProject($projectId, $data) {
            // Validar si el ID del proyecto es válido
            if (empty($projectId)) {
                return ['status' => 400, 'message' => 'El ID del proyecto es obligatorio'];
            }
        
            // Validar si el proyecto existe
            if (!$this->projectModel->exists($projectId)) {
                return ['status' => 404, 'message' => 'El proyecto no existe'];
            }
        
            // Validar que el responsable exista si se proporciona
            if (isset($data->responsable_id)) {
                $employeeExists = $this->employeeModel->exists($data->responsable_id);
                if (!$employeeExists) {
                    return ['status' => 404, 'message' => 'El responsable no existe'];
                }
            }
        
            // Obtener el proyecto actual para validaciones
            $project = $this->projectModel->getProject($projectId); 
        
            // Manejar si $project es un arreglo o un objeto
            $fechaInicioActual = isset($project['fecha_inicio']) ? $project['fecha_inicio'] : null;
            $fechaFinActual = isset($project['fecha_fin']) ? $project['fecha_fin'] : null;
        
            // Validar y construir objetos DateTime de las fechas actuales
            try {
                $fechaInicioActual = $fechaInicioActual ? new DateTime($fechaInicioActual) : null;
                $fechaFinActual = $fechaFinActual ? new DateTime($fechaFinActual) : null;
                $fechaActual = new DateTime();
        
                // Validar fecha_inicio si se proporciona
                if (isset($data->fecha_inicio)) {
                    $nuevaFechaInicio = new DateTime($data->fecha_inicio);
        
                    if ($fechaFinActual && $nuevaFechaInicio > $fechaFinActual) {
                        return ['status' => 400, 'message' => 'La fecha de inicio no puede ser posterior a la fecha de finalización'];
                    }
                }
        
                // Validar fecha_fin si se proporciona
                if (isset($data->fecha_fin)) {
                    $nuevaFechaFin = new DateTime($data->fecha_fin);
        
                    if ($fechaInicioActual && $nuevaFechaFin < $fechaInicioActual) {
                        return ['status' => 400, 'message' => 'La fecha de finalización no puede ser anterior a la fecha de inicio'];
                    }
        
                    if ($nuevaFechaFin > $fechaActual) {
                        return ['status' => 400, 'message' => 'La fecha de finalización no puede ser posterior a la fecha actual'];
                    }
                }
            } catch (Exception $e) {
                return ['status' => 400, 'message' => 'Formato de fecha inválido'];
            }
        
            // Actualizar el proyecto
            $this->projectModel->updateProject($projectId, $data);
        
            return ['status' => 200, 'message' => 'Proyecto actualizado correctamente', 'data' => $data];
        }
        
        public function deleteProject($projectId){
            // Validar que el ID sea proporcionado
            if (empty($projectId)) {
                return ['status' => 400, 'message' => 'El ID del empleado es obligatorio'];
            }
        
            // Validar si el empleado existe antes de intentar eliminar
            if (!$this->projectModel->exists($projectId)) {
                return ['status' => 404, 'message' => 'El empleado no existe'];
            }
        
            // Intentar eliminar el empleado
            $deleted = $this->projectModel->deleteProject($projectId);
        
            if ($deleted) {
                return ['status' => 200, 'message' => 'Empleado eliminado correctamente'];
            } else {
                return ['status' => 500, 'message' => 'Hubo un problema al eliminar el empleado'];
            }
        }   
    }

?>
