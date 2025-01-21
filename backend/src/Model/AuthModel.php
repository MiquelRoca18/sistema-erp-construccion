<?php
namespace App\Model;
use App\Config\Database;

class AuthModel{
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getUserByUsername($username) {
        $query = 'SELECT * FROM autenticacion WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>