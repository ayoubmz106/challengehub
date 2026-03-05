<?php
session_start();
require_once __DIR__ . "/../../config/csrf.php";
require_once __DIR__ . "/../models/challenge.php";

$base    = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder  = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;

function requireAuth($baseUrl) {
    if (empty($_SESSION['user_id'])) {
        header("Location: $baseUrl/app/views/login.php");
        exit();
    }
}

function handleImageUpload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return null;
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $ext;
    $dest     = __DIR__ . "/../../public/images/challenges/" . $filename;
    if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
    return move_uploaded_file($file['tmp_name'], $dest) ? $filename : null;
}

$action         = $_POST['action'] ?? '';
$challengeModel = new Challenge();

switch ($action) {

    case 'create':
        verifyCsrfToken();
        requireAuth($baseUrl);
        $title       = htmlspecialchars(trim($_POST['title'] ?? ''));
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));
        $category    = htmlspecialchars(trim($_POST['category'] ?? ''));
        $deadline    = $_POST['deadline'] ?? '';
        $image       = handleImageUpload($_FILES['image'] ?? null);

        if ($title && $description && $category && $deadline) {
            $challengeModel->create($title, $description, $category, $deadline, $image, $_SESSION['user_id']);
            header("Location: $baseUrl/app/views/challenge.php?success=created");
        } else {
            header("Location: $baseUrl/app/views/create_challenge.php?error=missing");
        }
        exit();

    case 'edit':
        verifyCsrfToken();
        requireAuth($baseUrl);
        $id          = (int)($_POST['id'] ?? 0);
        $title       = htmlspecialchars(trim($_POST['title'] ?? ''));
        $description = htmlspecialchars(trim($_POST['description'] ?? ''));
        $category    = htmlspecialchars(trim($_POST['category'] ?? ''));
        $deadline    = $_POST['deadline'] ?? '';
        $image       = handleImageUpload($_FILES['image'] ?? null);

        if ($id && $title && $description && $category && $deadline) {
            $challengeModel->update($id, $title, $description, $category, $deadline, $image, $_SESSION['user_id']);
            header("Location: $baseUrl/app/views/challenge.php?success=updated");
        } else {
            header("Location: $baseUrl/app/views/edit_challenge.php?id=" . $id . "&error=missing");
        }
        exit();

    case 'delete':
        verifyCsrfToken();
        requireAuth($baseUrl);
        $id = (int)($_POST['id'] ?? 0);
        if ($id) $challengeModel->delete($id, $_SESSION['user_id']);
        header("Location: $baseUrl/app/views/challenge.php?success=deleted");
        exit();

    default:
        header("Location: $baseUrl/app/views/challenge.php");
        exit();
}