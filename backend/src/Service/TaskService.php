<?php
namespace App\Service;

use App\Utils\BaseService;
use App\Model\Task;

class TaskService extends BaseService{

    public function __construct(){
        parent::__construct(new Task());
    }

    public function getTasks(){
        $tasks = $this->model->get();
        return $tasks ? $this->responseFound($tasks, 'Tareas encontradas') : $this->responseNotFound();
    }

    public function getTask($id){
        //Validar ID
        if($error = $this->validateId($id)){
            return $error;
        }

        //Validar existencia de la tarea
        if($error = $this->validateExists($id)){
            return $error;
        }

        $task = $this->model->getById($id);
        return $task ? $this->responseFound($task, 'Tarea encontrada') : $this->responseNotFound();
    }

    public function createTask($data){
        //Validar campos obligatorios  
        $error = $this->validator->validateRequiredFields(['nombre_tarea', 'descripcion', 'proyectos_id'], $data);
        if($error){
            return ['status' => 400, 'message' => $error];
        }

        //Asignar valores predeterminados
        $data->estado = $data->estado ?? 'pendiente';
        $data->fecha_inicio = $data->fecha_inicio ?? date('Y-m-d');
        $data->fecha_fin = $data->fecha_fin ?? null;

        //Validar que el proyecto exista
        if(!$this->model->exists($data->proyectos_id)){
            return ['status' => 404, 'message' => 'El proyecto no existe'];
        }

        //Llamar al modelo para crear la tarea
        $taskId = $this->model->create($data);
        return $taskId ? $this->responseCreated($taskId, 'Tarea creada exitosamente') : $this->responseError();
    }

    public function updateTask($id, $data){
        //Validar ID
        if($error = $this->validateId($id)){
            return $error;
        }

        //Validar existencia de la tarea
        if($error = $this->validateExists($id)){
            return $error;
        }

        //Validar que el proyecto exista
        if(!$this->model->exists($data->proyectos_id)){
            return ['status' => 404, 'message' => 'El proyecto no existe'];
        }

        //Validar estado correcto
        if(!empty($data->estado) && !in_array($data->estado, ['pendiente', 'en progreso', 'finalizado'])){
            return ['status' => 400, 'message' => 'El estado no es válido'];
        }

        //Obtener las fechas actuales de la tarea
        $task = $this->model->get($id);
        $fechaInicioActual = $task['fecha_inicio'] ?? null;
        $fechaFinActual = $task['fecha_fin'] ?? null;

        // Validar fechas usando la función general
        if ($error = $this->validator->validateDates($data, $fechaInicioActual, $fechaFinActual)) {
            return ['status' => 400, 'message' => $error];
        }

        //Llamar al modelo para actualizar la tarea
        $result = $this->model->update($id, $data);
        return $result ? $this->responseUpdated('Tarea actualizada') : $this->responseError();
    }

    public function deleteTask($id){
        //Validar ID
        if($error = $this->validateId($id)){
            return $error;
        }

        //Validar existencia de la tarea
        if($error = $this->validateExists($id)){
            return $error;
        }

        //Llamar al modelo para eliminar la tarea
        $result = $this->model->delete($id);
        return $result ? $this->responseDeleted('Tarea eliminada') : $this->responseError();   
    }
}

?>