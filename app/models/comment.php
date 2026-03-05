<?php
require_once __DIR__ . "/../../config/database.php";

class Comment {
    private $conn;
    private $table = "comments";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create($submission_id, $user_id, $content) {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . "
            (submission_id, user_id, content, created_at)
            VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$submission_id, $user_id, $content]);
    }

    public function getBySubmissionId($submission_id) {
        $stmt = $this->conn->prepare("SELECT c.*, u.name AS author_name
            FROM " . $this->table . " c
            JOIN users u ON c.user_id = u.id
            WHERE c.submission_id = ?
            ORDER BY c.created_at ASC");
        $stmt->execute([$submission_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id, $user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$comment || $comment['user_id'] != $user_id) return false;
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}