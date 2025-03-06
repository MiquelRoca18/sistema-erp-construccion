<?php
namespace App\Utils;

use App\Config\Database;

class BaseModel {
    protected $db;
    protected $table;

    public function __construct($table) {
        $this->db = (new Database())->getConnection();
        $this->table = $table;
    }
    
    public function getDb() {
        return $this->db;
    }
    
    // Obtener todos los registros de la tabla
    public function get() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Obtener registros paginados (nuevo método)
    public function getPaginated($page = 1, $itemsPerPage = 10, $orderBy = null) {
        $offset = ($page - 1) * $itemsPerPage;
        
        $orderClause = $orderBy ? " ORDER BY $orderBy" : "";
        $query = 'SELECT * FROM ' . $this->table . $orderClause . ' LIMIT :limit OFFSET :offset';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Contar registros para paginación (nuevo método)
    public function count($whereClause = '1') {
        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE ' . $whereClause;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Obtener un registro por su ID
    public function getById($id) {
        
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->table . '_id = :id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result;
        } catch (\PDOException $e) {
            return null;
        }
    }

    // Verificar si un registro existe
    public function exists($id) {
        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE ' . $this->table . '_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Crear un nuevo registro
    public function create($data) {
        $data = (array)$data;
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = 'INSERT INTO ' . $this->table . ' (' . $columns . ') VALUES (' . $placeholders . ')';
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    
    // Actualizar un registro
    public function update($id, $data) {
        $data = (array)$data;
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        if (empty($fields)) {
            return false;
        }

        $query = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $fields) . ' WHERE ' . $this->table . '_id = :id';
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar un registro
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->table . '_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    
    // Buscar registros genéricos (nuevo método)
    public function search($column, $term, $limit = 20) {
        $query = "SELECT * FROM {$this->table} WHERE {$column} LIKE :term LIMIT :limit";
        $term = "%{$term}%";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>