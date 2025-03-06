<?php
namespace App\Model;
use App\Utils\BaseModel;

class Employee extends BaseModel {
    public function __construct() {
        parent::__construct('empleados');  
    }

    public function getByRole($role) {
        // Optimización: Selección específica de columnas y orden
        $query = 'SELECT 
                    empleados_id, 
                    nombre, 
                    rol, 
                    fecha_contratacion, 
                    telefono, 
                    correo 
                  FROM ' . $this->table . ' 
                  WHERE rol = :rol
                  ORDER BY nombre';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':rol', $role);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Método nuevo para buscar empleados por nombre (útil para búsquedas)
    public function searchByName($searchTerm) {
        $query = 'SELECT 
                    empleados_id, 
                    nombre, 
                    rol 
                  FROM ' . $this->table . ' 
                  WHERE nombre LIKE :searchTerm
                  ORDER BY nombre
                  LIMIT 20';
        $searchTerm = '%' . $searchTerm . '%';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>