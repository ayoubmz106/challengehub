<?php
session_start();
require_once __DIR__ . "/../../config/csrf.php";
require_once __DIR__ . "/../models/vote.php";

$base    = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$folder  = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $base . $folder;

if (empty($_SESSION['user_id'])) {
    header("Location: $baseUrl/app/views/login.php");
    exit();
}

verifyCsrfToken();

$submission_id = (int)($_POST['submission_id'] ?? 0);
$challenge_id  = (int)($_POST['challenge_id'] ?? 0);

if ($submission_id) {
    $voteModel = new Vote();
    $voteModel->vote($submission_id, $_SESSION['user_id']);
}

header("Location: $baseUrl/app/views/submission_detaile.php?id=$submission_id&challenge_id=$challenge_id");
exit();