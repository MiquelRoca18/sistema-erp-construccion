<?php
namespace App\Service;


class TaskService{
    public function getTasks(){
        return [
            'status' => 200,
            'message' => 'GET tasks',
            'data' => []
        ];
    }

    // public function getTask($id){
    //     return [
    //         'status' => 200,
    //         'message' => 'GET task ' . $id,
    //         'data' => []
    //     ];
    // }

    // public function createTask($data){
    //     return [
    //         'status' => 200,
    //         'message' => 'POST task',
    //         'data' => []
    //     ];
    // }

    // public function updateTask($id, $data){
    //     return [
    //         'status' => 200,
    //         'message' => 'PUT task ' . $id,
    //         'data' => []
    //     ];
    // }

    // public function deleteTask($id){
    //     return [
    //         'status' => 200,
    //         'message' => 'DELETE task ' . $id,
    //         'data' => []
    //     ];
    // }
}

?>