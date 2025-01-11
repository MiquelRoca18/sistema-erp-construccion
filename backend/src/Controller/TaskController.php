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
        echo 'GET tasks';
    }

    public function getTask($id){
        echo 'GET task ' . $id;
    }

    public function createTask(){
        echo 'POST task';
    }

    public function updateTask($id){
        echo 'PUT task ' . $id;
    }

    public function deleteTask($id){
        echo 'DELETE task ' . $id;
    }
}

?>