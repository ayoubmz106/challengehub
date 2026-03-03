<?php
require_once __DIR__ . "/../../config/database.php";

class Challenge {
    private $conn;
    private $table = "challenges";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create($title, $description, $category, $deadline, $image, $user_id) {
        $query = "INSERT INTO " . $this->table . "
                  (title, description, category, deadline, image, user_id, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $description, $category, $deadline, $image, $user_id]);
    }

    public function getAll($keyword = '', $category = '', $sort = 'date') {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $where .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        if (!empty($category)) {
            $where .= " AND c.category = ?";
            $params[] = $category;
        }

        $order = $sort === 'popularity' ? "ORDER BY submission_count DESC" : "ORDER BY c.created_at DESC";

        $query = "SELECT c.*, u.name AS author_name,
                    (SELECT COUNT(*) FROM submissions s WHERE s.challenge_id = c.id) AS submission_count
                  FROM " . $this->table . " c
                  JOIN users u ON c.user_id = u.id
                  $where $order";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT c.*, u.name AS author_name
                  FROM " . $this->table . " c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $description, $category, $deadline, $image, $user_id) {
        $challenge = $this->getById($id);
        if (!$challenge || $challenge['user_id'] != $user_id) return false;

        if ($image) {
            $query = "UPDATE " . $this->table . " SET title=?, description=?, category=?, deadline=?, image=? WHERE id=?";
            $params = [$title, $description, $category, $deadline, $image, $id];
        } else {
            $query = "UPDATE " . $this->table . " SET title=?, description=?, category=?, deadline=? WHERE id=?";
            $params = [$title, $description, $category, $deadline, $id];
        }

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id, $user_id) {
        $challenge = $this->getById($id);
        if (!$challenge || $challenge['user_id'] != $user_id) return false;

        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getCategories() {
        $stmt = $this->conn->prepare("SELECT DISTINCT category FROM " . $this->table . " ORDER BY category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}