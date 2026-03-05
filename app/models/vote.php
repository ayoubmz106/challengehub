<?php
require_once __DIR__ . "/../../config/database.php";

class Vote {
    private $conn;
    private $table = "votes";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function vote($submission_id, $user_id) {
        // Si déjà voté → supprimer le vote (toggle)
        if ($this->hasVoted($submission_id, $user_id)) {
            $stmt = $this->conn->prepare("DELETE FROM " . $this->table . "
                WHERE submission_id = ? AND user_id = ?");
            $stmt->execute([$submission_id, $user_id]);
            return 'removed';
        }
        // Sinon → ajouter le vote
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . "
            (submission_id, user_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$submission_id, $user_id]);
        return 'added';
    }

    public function hasVoted($submission_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT id FROM " . $this->table . "
            WHERE submission_id = ? AND user_id = ?");
        $stmt->execute([$submission_id, $user_id]);
        return $stmt->fetch() !== false;
    }

    public function countVotes($submission_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM " . $this->table . "
            WHERE submission_id = ?");
        $stmt->execute([$submission_id]);
        return $stmt->fetchColumn();
    }
}