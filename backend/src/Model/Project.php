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
    
    // Obtener proyectos con el nombre del responsable
    public function getProjectsWithResponsable() {
        $query = "SELECT p.*, e.nombre AS responsable_nombre 
                  FROM " . $this->table . " p 
                  LEFT JOIN empleados e ON p.responsable_id = e.empleados_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
