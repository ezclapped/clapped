<?php
require_once "../config.php";

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../login"); // Passe den Pfad zur Login-Seite an
    exit;
}

$user_id = $_SESSION["id"];
$sql = "SELECT is_banned FROM users WHERE id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $is_banned);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($is_banned) {
    header("Location: ../media");
    exit;
}
?>
<!DOCTYPE html>