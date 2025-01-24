<?php
namespace App\Model;

use App\Utils\BaseModel;

class Task extends BaseModel{

    public function __construct(){
        parent::__construct('tareas');
    }

}

?>