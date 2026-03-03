<?php
require_once __DIR__ . "/../../config/database.php";

class User {
    private $conn;
    private $table = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function register($nom, $email, $password) {
        // Vérifier si email existe déjà
        $check = $this->conn->prepare("SELECT id FROM " . $this->table . " WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) return false;

        $query = "INSERT INTO " . $this->table . " (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$nom, $email, $password]);
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}