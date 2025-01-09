<?php
namespace App\Model;

use App\Config\Database;

class Employee {
    private $db;
    private $table = 'empleados';

    public function __construct() {
        // Crear una instancia de la clase Database
        $this->db = (new Database())->getConnection(); 
    }

    public function get(){
        $query = 'SELECT * FROM ' . $this->table . '';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getEmployee($employeeId){
        $query = 'SELECT * FROM ' . $this->table . ' WHERE empleado_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $employeeId);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function createEmployee(){
        $data = json_decode(file_get_contents('php://input'), true);
        $query = 'INSERT INTO ' . $this->table . ' (nombre, rol, fecha_contratacion, telefono, correo) VALUES (:nombre, :rol, :fecha_contratacion, :telefono, :correo)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':rol', $data['rol']);
        $stmt->bindParam(':fecha_contratacion', $data['fecha_contratacion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':correo', $data['correo']);
        $stmt->execute();
        // Retornar el ID del proyecto creado o true si fue exitoso
        return $this->db->lastInsertId();    
    }

    public function updateEmployee($employeeId, $data) {
        // Preparar la consulta de actualización
        $query = 'UPDATE ' . $this->table . ' SET ';
        $fields = [];
        foreach ($data as $key => $value) {
            // Se actualiza solo lo que se pasa
            $fields[] = "$key = :$key";
        }
    
        if (empty($fields)) {
            return false; // Si no se envía ningún campo, no se actualiza nada
        }
    
        $query .= implode(', ', $fields);
        $query .= " WHERE empleado_id = :id";
    
        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare($query);
    
        // Usar bindValue en lugar de bindParam
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);  // Cambié bindParam a bindValue
        }
        $stmt->bindValue(':id', $employeeId, \PDO::PARAM_INT); // Especificamos el tipo para el id
    
        // Ejecutar la consulta y devolver el resultado
        if ($stmt->execute()) {
            // Verifica si se actualizó algún registro
            return $stmt->rowCount() > 0; // Si se actualizó al menos un registro, retorna true
        } else {
            return false; // Si la ejecución falla
        }
    }  
    

    public function deleteEmployee($employeeId) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE empleado_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $employeeId);
        $stmt->execute();
    
        // Comprobar si se eliminó algún registro
        return $stmt->rowCount() > 0;
    }
      

    public function exists($employeeId) {
        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE empleado_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $employeeId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>