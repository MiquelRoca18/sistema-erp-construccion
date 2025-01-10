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

    // Obtener todos los registros de la tabla
    public function get() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Obtener un registro por su ID
    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->table . '_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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
}
?>
