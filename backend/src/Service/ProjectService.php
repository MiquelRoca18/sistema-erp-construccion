<?php
namespace App\Service;

use App\Model\Project;
use App\Model\Employee;
use App\Utils\BaseService;

class ProjectService extends BaseService {
    private $employeeModel;


    public function __construct() {
        parent::__construct(new Project());
        $this->employeeModel = new Employee();
    }

    public function getProjects() {
        $projects = $this->model->getProjectsWithResponsable();
        return $projects 
            ? $this->responseFound($projects, 'Proyectos encontrados') 
            : $this->responseNotFound();
    }

    public function getProject($projectId) {
        // Validar ID
        if ($error = $this->validateId($projectId)) {
            return $error;
        }

        // Validar existencia del proyecto
        if ($error = $this->validateExists($projectId)) {
            return $error;
        }

        $project = $this->model->getById($projectId);
        return $this->responseFound($project, 'Proyecto encontrado');
    }

    public function createProject($data) {
        // Validar campos obligatorios
        $error = $this->validator->validateRequiredFields(['nombre_proyecto', 'descripcion'], $data);
        if ($error) {
            return ['status' => 400, 'message' => $error];
        }
        
        // Asignar valores predeterminados
        $data->estado = $data->estado ?? 'pendiente';
        $data->fecha_inicio = $data->fecha_inicio ?? date('Y-m-d');
        $data->fecha_fin = $data->fecha_fin ?? null;
    
        // Validar que el responsable exista
        if (!empty($data->responsable_id) && !$this->employeeModel->exists($data->responsable_id)) {
            return ['status' => 404, 'message' => 'El responsable no existe'];
        } elseif (empty($data->responsable_id)) {
            return ['status' => 400, 'message' => 'El responsable es obligatorio'];
        }
    
        $projectId = $this->model->create($data);
        
        if ($projectId) {
            // Crear presupuesto por defecto para el proyecto
            $budgetModel = new \App\Model\Budget();
            $defaultBudgetData = (object)[
                'proyectos_id' => $projectId,
                'equipos' => 0,
                'mano_obra' => 0,
                'materiales' => 0
            ];
            $budgetId = $budgetModel->create($defaultBudgetData);
            if ($budgetId) {
                return [
                    'status' => 201,
                    'message' => 'Proyecto y presupuesto creados exitosamente',
                    'data' => ['project_id' => $projectId, 'budget_id' => $budgetId]
                ];
            } else {
                return ['status' => 500, 'message' => 'Proyecto creado, pero fallo al crear el presupuesto'];
            }
        } else {
            return ['status' => 500, 'message' => 'Error al crear el proyecto'];
        }
    }
    

    public function updateProject($projectId, $data) {
        // Validar ID
        if ($error = $this->validateId($projectId)) {
            return $error;
        }

        // Validar existencia del proyecto
        if ($error = $this->validateExists($projectId)) {
            return $error;
        }

        //Validar estado correcto
        if(!empty($data->estado) && !in_array($data->estado, ['pendiente', 'en progreso', 'finalizado'])){
        return ['status' => 400, 'message' => 'El estado no es válido'];
        }

        // Validar que el responsable exista si se proporciona
        if (isset($data->responsables_id) && !$this->employeeModel->exists($data->responsables_id)) {
            return ['status' => 404, 'message' => 'El responsable no existe'];
        }

        // Obtener las fechas actuales del proyecto
        $project = $this->model->get($projectId);
        $fechaInicioActual = $project['fecha_inicio'] ?? null;
        $fechaFinActual = $project['fecha_fin'] ?? null;

        // Validar fechas usando la función general
        if ($error = $this->validator->validateDates($data, $fechaInicioActual, $fechaFinActual)) {
            return ['status' => 400, 'message' => $error];
        }

        // Actualizar el proyecto
        $result = $this->model->update($projectId, $data);
        return $result ? $this->responseUpdated('Tarea actualizada') : $this->responseError();
    }

    public function deleteProject($projectId) {
        // Validar ID
        if ($error = $this->validateId($projectId)) {
            return $error;
        }
    
        // Validar existencia del proyecto
        if ($error = $this->validateExists($projectId)) {
            return $error;
        }
    
        try {
            $db = $this->model->getDb();
            $db->beginTransaction();
    
            // Verificar y eliminar dependencias
            // 1. Eliminar tareas asociadas al proyecto
            $sqlTareas = "DELETE FROM empleados_tareas WHERE tareas_id IN (SELECT tareas_id FROM tareas WHERE proyectos_id = :projectId)";
            $stmtTareas = $db->prepare($sqlTareas);
            $stmtTareas->bindParam(':projectId', $projectId, \PDO::PARAM_INT);
            $stmtTareas->execute();
    
            $sqlEliminarTareas = "DELETE FROM tareas WHERE proyectos_id = :projectId";
            $stmtEliminarTareas = $db->prepare($sqlEliminarTareas);
            $stmtEliminarTareas->bindParam(':projectId', $projectId, \PDO::PARAM_INT);
            $stmtEliminarTareas->execute();
    
            // 2. Eliminar presupuestos asociados al proyecto
            $sqlPresupuestos = "DELETE FROM presupuestos WHERE proyectos_id = :projectId";
            $stmtPresupuestos = $db->prepare($sqlPresupuestos);
            $stmtPresupuestos->bindParam(':projectId', $projectId, \PDO::PARAM_INT);
            $stmtPresupuestos->execute();
    
            // 3. Eliminar el proyecto
            $result = $this->model->delete($projectId);
    
            if ($result) {
                $db->commit();
                return $this->responseDeleted('Proyecto eliminado con todas sus dependencias');
            } else {
                $db->rollBack();
                return $this->responseError('No se pudo eliminar el proyecto');
            }
        } catch (\Exception $e) {
            $db->rollBack();
            return [
                'status' => 500, 
                'message' => 'Error al eliminar el proyecto: ' . $e->getMessage()
            ];
        }
    }
    
    
}
?>
