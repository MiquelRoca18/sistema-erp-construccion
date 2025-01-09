<?php
    namespace App\Model;

    use App\Config\Database;

    class Project {
        private $db;
        private $table = 'proyectos';

        public function __construct() {
            $this->db = (new Database())->getConnection();
        }

        public function get() {
            $query = 'SELECT * FROM ' . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        
            // Devolver los datos de los proyectos como un array asociativo
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        

        public function getProject($projectId) {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE proyecto_id = :id';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $projectId);
            $stmt->execute();
        
            // Devolver los datos del proyecto como un array asociativo
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        

        public function exists($projectId){
            $query = 'SELECT * FROM ' . $this->table . ' WHERE proyecto_id = :id';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $projectId);
            $stmt->execute();
            $project = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $project ? true : false;
        }

        public function createProject($data) {
            $query = 'INSERT INTO proyectos (responsable_id, estado, nombre_proyecto, fecha_inicio, fecha_fin, descripcion) 
                      VALUES (:responsable_id, :estado, :nombre_proyecto, :fecha_inicio, :fecha_fin, :descripcion)';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':responsable_id', $data->responsable_id);
            $stmt->bindParam(':estado', $data->estado);
            $stmt->bindParam(':nombre_proyecto', $data->nombre_proyecto);
            $stmt->bindParam(':fecha_inicio', $data->fecha_inicio);
            $stmt->bindParam(':fecha_fin', $data->fecha_fin);
            $stmt->bindParam(':descripcion', $data->descripcion);
            $stmt->execute();
        
            // Retornar el ID del proyecto creado o true si fue exitoso
            return $this->db->lastInsertId();
        }
        
        public function updateProject($projectId, $data) {
            // Construir dinámicamente la consulta de actualización
            $query = 'UPDATE ' . $this->table . ' SET ';
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[] = "$key = :$key";
            }
        
            if (empty($fields)) {
                return false; // No se envió nada para actualizar
            }
        
            $query .= implode(', ', $fields);
            $query .= ' WHERE proyecto_id = :id';
        
            // Preparar y ejecutar la consulta
            $stmt = $this->db->prepare($query);
        
            // Usar bindValue en lugar de bindParam
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);  // Cambié bindParam a bindValue
            }
            $stmt->bindValue(':id', $projectId, \PDO::PARAM_INT); // Especificamos el tipo para el id
        
            // Ejecutar la consulta y devolver el resultado
            if ($stmt->execute()) {
                // Verifica si se actualizó algún registro
                return $stmt->rowCount() > 0; // Si se actualizó al menos un registro, retorna true
            } else {
                return false; // Si la ejecución falla
            }
        }
        
        public function deleteProject($projectId) {
            $query = 'DELETE FROM ' . $this->table . ' WHERE proyecto_id = :id';
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $projectId, \PDO::PARAM_INT);
            $stmt->execute();
        
            // Verificar si se eliminó algún registro
            return $stmt->rowCount() > 0;
        }
}
?>