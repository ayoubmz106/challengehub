<?php
session_start();
require_once __DIR__ . "/../../config/csrf.php";
require_once __DIR__ . "/../models/comment.php";

$base    = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder  = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;

if (empty($_SESSION['user_id'])) {
    header("Location: $baseUrl/app/views/login.php");
    exit();
}

verifyCsrfToken();

$action       = $_POST['action'] ?? '';
$commentModel = new Comment();

switch ($action) {

    case 'create':
        $submission_id = (int)($_POST['submission_id'] ?? 0);
        $challenge_id  = (int)($_POST['challenge_id'] ?? 0);
        $content       = htmlspecialchars(trim($_POST['content'] ?? ''));

        if ($submission_id && $content) {
            $commentModel->create($submission_id, $_SESSION['user_id'], $content);
        }
        header("Location: $baseUrl/app/views/submission_detaile.php?id=$submission_id&challenge_id=$challenge_id");
        exit();

    case 'delete':
        $id            = (int)($_POST['id'] ?? 0);
        $submission_id = (int)($_POST['submission_id'] ?? 0);
        $challenge_id  = (int)($_POST['challenge_id'] ?? 0);
        if ($id) $commentModel->delete($id, $_SESSION['user_id']);
        header("Location: $baseUrl/app/views/submission_detaile.php?id=$submission_id&challenge_id=$challenge_id");
        exit();

    default:
        header("Location: $baseUrl/app/views/challenge.php");
        exit();
}