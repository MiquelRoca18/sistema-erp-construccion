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
        $projects = $this->model->get();
        return $projects ? $this->responseFound($projects, 'Proyectos encontrados') : $this->responseNotFound();
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

        // Llamar al modelo para crear el proyecto
        $projectId = $this->model->create($data);
        return $projectId ? ['status' => 201, 'message' => 'Proyecto creado exitosamente', 'data' => $projectId] : ['status' => 500, 'message' => 'Error al crear el proyecto'];
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
        $this->model->update($projectId, $data);
        return ['status' => 200, 'message' => 'Proyecto actualizado correctamente', 'data' => $data];
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

        // Eliminar el proyecto
        $deleted = $this->model->delete($projectId);
        return $deleted ? ['status' => 200, 'message' => 'Proyecto eliminado correctamente'] : ['status' => 500, 'message' => 'Hubo un problema al eliminar el proyecto'];
    }
}
?>
