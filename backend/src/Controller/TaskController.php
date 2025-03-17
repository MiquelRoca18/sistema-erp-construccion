<?php
namespace App\Controller;

use App\Utils\BaseController;
use App\Service\TaskService;

class TaskController extends BaseController{
    private $taskService;

    public function __construct(){
        $this->taskService = new TaskService();
    }

    public function get(){
        $cacheKey = "tasks_all";
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Tareas encontradas (caché)', $cachedData);
            return;
        }
        
        $result = $this->taskService->getTasks();
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); 
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getTask($id){
        $cacheKey = "task_" . $id;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Tarea encontrada (caché)', $cachedData);
            return;
        }
        
        $result = $this->taskService->getTask($id);
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300);         }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createTask(){
        $data = $this->getRequestData();
        $result = $this->taskService->createTask($data);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("tasks_all");
            
            // Si la tarea está asociada a un proyecto, limpiamos también sus cachés
            if (isset($data->proyectos_id)) {
                apcu_delete("project_tasks_" . $data->proyectos_id);
            }
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function updateTask($id){
        $data = $this->getRequestData();
        $result = $this->taskService->updateTask($id, $data);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("tasks_all");
            apcu_delete("task_" . $id);
            
            // Limpiar también cachés de tareas por empleado
            if (function_exists('apcu_cache_info')) {
                try {
                    $cache_info = apcu_cache_info();
                    foreach ($cache_info['cache_list'] as $entry) {
                        if (strpos($entry['info'], 'employee_tasks_') === 0 ||
                            strpos($entry['info'], 'employee_pending_tasks_') === 0) {
                            apcu_delete($entry['info']);
                        }
                    }
                } catch (\Exception $e) {
                }
            }
            
            // Si la tarea está asociada a un proyecto, limpiamos también sus cachés
            if (isset($data->proyectos_id)) {
                apcu_delete("project_tasks_" . $data->proyectos_id);
            }
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function deleteTask($id){
        $result = $this->taskService->deleteTask($id);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("tasks_all");
            apcu_delete("task_" . $id);
            
            // Limpiar también cachés de tareas por empleado
            if (function_exists('apcu_cache_info')) {
                try {
                    $cache_info = apcu_cache_info();
                    foreach ($cache_info['cache_list'] as $entry) {
                        if (strpos($entry['info'], 'employee_tasks_') === 0 ||
                            strpos($entry['info'], 'employee_pending_tasks_') === 0) {
                            apcu_delete($entry['info']);
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}
?>