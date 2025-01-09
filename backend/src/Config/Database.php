<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $dbname = "sistema_erp_construccion";
    private $pdo;

    public function __construct() {
        try {
            // Crear la conexión con la base de datos
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->user,
                $this->password
            );
            // Configuración para manejar errores
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch (PDOException $e) {
            die("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
