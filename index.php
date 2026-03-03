<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header("Location: app/views/challenge.php");
} else {
    header("Location: app/views/login.php");
}
exit();