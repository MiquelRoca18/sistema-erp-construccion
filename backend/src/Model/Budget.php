<?php
namespace App\Model;
use App\Utils\BaseModel;

class Budget extends BaseModel{

    public function __construct() {
        parent::__construct('presupuestos');
    }
}

?>