<?php
namespace App\Model;
use App\Config\Database;
use PDO;

class AuthModel {
    private $db;

    public function __construct() {
        // Establece la conexión a la base de datos
        $this->db = (new Database())->getConnection();
    }

    // Obtener usuario por nombre de usuario
    public function getUserByUsername($username) {
        // Se realiza el JOIN para obtener el rol (admin o usuario)
        $stmt = $this->db->prepare("
            SELECT a.*, r.rol AS role 
            FROM autenticacion a 
            JOIN roles r ON a.id_rol = r.id_rol 
            WHERE a.username = :username
        ");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    // Obtener usuario por ID
    public function getUserById($id_usuario) {
        $query = 'SELECT * FROM autenticacion WHERE id_usuario = :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Actualizar contraseña
    public function updatePassword($id_usuario, $newPasswordHash) {
        $query = 'UPDATE autenticacion SET password_hash = :password_hash WHERE id_usuario = :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':password_hash', $newPasswordHash);
        $stmt->bindValue(':id_usuario', $id_usuario);
        $stmt->execute();
    }

}

?>