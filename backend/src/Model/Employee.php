<?php
namespace App\Model;
use App\Utils\BaseModel;

class Employee extends BaseModel {
    public function __construct() {
        parent::__construct('empleados');  
    }

    public function getByRole($role) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE rol = :rol';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':rol', $role);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
