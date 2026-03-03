<?php
require_once __DIR__ . "/../../config/database.php";

class Submission {
    private $conn;
    private $table = "submissions";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create($challenge_id, $user_id, $description, $image, $link) {
        $query = "INSERT INTO " . $this->table . "
                  (challenge_id, user_id, description, image, link, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$challenge_id, $user_id, $description, $image, $link]);
    }

    public function getByChallengeId($challenge_id) {
        $query = "SELECT s.*, u.name AS author_name,
                    (SELECT COUNT(*) FROM votes v WHERE v.submission_id = s.id) AS vote_count
                  FROM " . $this->table . " s
                  JOIN users u ON s.user_id = u.id
                  WHERE s.challenge_id = ?
                  ORDER BY vote_count DESC, s.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$challenge_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT s.*, u.name AS author_name,
                    (SELECT COUNT(*) FROM votes v WHERE v.submission_id = s.id) AS vote_count
                  FROM " . $this->table . " s
                  JOIN users u ON s.user_id = u.id
                  WHERE s.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $user_id, $description, $image, $link) {
        $sub = $this->getById($id);
        if (!$sub || $sub['user_id'] != $user_id) return false;

        if ($image) {
            $query = "UPDATE " . $this->table . " SET description=?, image=?, link=? WHERE id=?";
            $params = [$description, $image, $link, $id];
        } else {
            $query = "UPDATE " . $this->table . " SET description=?, link=? WHERE id=?";
            $params = [$description, $link, $id];
        }
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id, $user_id) {
        $sub = $this->getById($id);
        if (!$sub || $sub['user_id'] != $user_id) return false;
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function hasParticipated($challenge_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT id FROM " . $this->table . " WHERE challenge_id=? AND user_id=?");
        $stmt->execute([$challenge_id, $user_id]);
        return $stmt->fetch() !== false;
    }
}