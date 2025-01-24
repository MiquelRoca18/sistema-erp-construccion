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
        $result = $this->taskService->getTasks();
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getTask($id){
        $result = $this->taskService->getTask($id);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createTask(){
        $data = $this->getRequestData();
        $result = $this->taskService->createTask($data);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function updateTask($id){
        $data = $this->getRequestData();
        $result = $this->taskService->updateTask($id, $data);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function deleteTask($id){
        $result = $this->taskService->deleteTask($id);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}

?>