<?php
require_once __DIR__ . "/../../config/database.php";

class Classement {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Top participations par votes
   public function getTopSubmissions($limit = 10) {
    $query = "SELECT s.id, s.challenge_id, s.user_id, s.description, 
                s.image, s.link, s.created_at,
                u.name AS author_name, 
                c.title AS challenge_title,
                COUNT(v.id) AS vote_count
              FROM submissions s
              JOIN users u ON s.user_id = u.id
              JOIN challenges c ON s.challenge_id = c.id
              LEFT JOIN votes v ON v.submission_id = s.id
              GROUP BY s.id, s.challenge_id, s.user_id, s.description, 
                       s.image, s.link, s.created_at, u.name, c.title
              ORDER BY vote_count DESC
              LIMIT ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Top utilisateurs par votes reçus
    public function getTopUsers($limit = 10) {
    $query = "SELECT u.id, u.name,
                COUNT(v.id) AS total_votes,
                COUNT(DISTINCT s.id) AS total_submissions
              FROM users u
              LEFT JOIN submissions s ON s.user_id = u.id
              LEFT JOIN votes v ON v.submission_id = s.id
              GROUP BY u.id, u.name
              ORDER BY total_votes DESC
              LIMIT ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}