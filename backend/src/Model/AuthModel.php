<?php
namespace App\Model;
use App\Config\Database;

class AuthModel {
    private $db;

    public function __construct() {
        // Establece la conexión a la base de datos
        $this->db = (new Database())->getConnection();
    }

    // Obtener usuario por nombre de usuario
    public function getUserByUsername($username) {
        $query = 'SELECT * FROM autenticacion WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear un usuario en la tabla autenticación
    public function create($data) {
        // Convertimos el objeto $data a array para poder manipularlo fácilmente
        $data = (array)$data;

        // Preparamos los nombres de las columnas y sus valores
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        // Consulta SQL para insertar el nuevo usuario
        $query = 'INSERT INTO autenticacion (' . $columns . ') VALUES (' . $placeholders . ')';
        $stmt = $this->db->prepare($query);

        // Asociamos los valores de los parámetros con los valores de $data
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        // Ejecutamos la consulta
        $stmt->execute();
    }
}

?>