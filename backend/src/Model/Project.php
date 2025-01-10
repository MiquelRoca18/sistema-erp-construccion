<?php
namespace App\Model;
use App\Utils\BaseModel;

class Project extends BaseModel {
    public function __construct() {
        parent::__construct('proyectos');  
    }

    public function getByState($state) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE estado = :estado';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $state);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
