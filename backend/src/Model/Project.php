<?php
namespace App\Model;
use App\Utils\BaseModel;

class Project extends BaseModel {
    public function __construct() {
        parent::__construct('proyectos');  // Pasamos el nombre de la tabla al constructor de BaseModel
    }

    // Puedes agregar métodos específicos para 'Project' aquí si es necesario
    // Ejemplo: si necesitas un método especial para obtener los proyectos por estado
    public function getByState($state) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE estado = :estado';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $state);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
