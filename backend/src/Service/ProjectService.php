<?php
namespace App\Service;

use App\Model\Project;
use App\Model\Employee;
use App\Utils\BaseService;  

use \DateTime;
use \Exception;

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

        // Obtener el proyecto actual para validaciones
        $project = $this->model->get($projectId);
        $fechaInicioActual = isset($project['fecha_inicio']) ? $project['fecha_inicio'] : null;
        $fechaFinActual = isset($project['fecha_fin']) ? $project['fecha_fin'] : null;

        // Validar las fechas
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
