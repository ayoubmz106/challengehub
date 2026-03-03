<?php
session_start();
require_once __DIR__ . "/../models/User.php";

$base = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;

$action = '';
if (isset($_POST['login']))    $action = 'login';
if (isset($_POST['register'])) $action = 'register';
if (isset($_GET['action']))    $action = $_GET['action'];

$userModel = new User();

switch ($action) {

    case 'register':
        $nom      = htmlspecialchars(trim($_POST['nom'] ?? ''));
        $email    = htmlspecialchars(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($nom && $email && $password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $result = $userModel->register($nom, $email, $hashed);
            if ($result) {
                header("Location: $baseUrl/app/views/login.php?success=1");
            } else {
                header("Location: $baseUrl/app/views/registre.php?error=exists");
            }
        } else {
            header("Location: $baseUrl/app/views/registre.php?error=missing");
        }
        exit();

    case 'login':
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $userModel->login($email, $password);
        if ($user) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: $baseUrl/app/views/challenge.php");
        } else {
            header("Location: $baseUrl/app/views/login.php?error=invalid");
        }
        exit();

    case 'logout':
        session_destroy();
        header("Location: $baseUrl/app/views/login.php");
        exit();

    default:
        header("Location: $baseUrl/app/views/login.php");
        exit();
}