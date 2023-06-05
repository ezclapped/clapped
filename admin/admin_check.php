<?php
require_once "../config.php";

// Überprüfe, ob der Benutzer eingeloggt ist
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Benutzer ist nicht eingeloggt, umleiten oder Fehlermeldung anzeigen
    header("Location: ../login");
    exit;
}

// Überprüfe den Administratorenstatus des Benutzers
$user_id = $_SESSION["id"]; // Passe den Namen der Session-Variable an, die die Benutzer-ID speichert
$sql = "SELECT is_admin FROM users WHERE id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $is_admin);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$is_admin) {
    // Benutzer ist kein Administrator, umleiten oder Fehlermeldung anzeigen
    header("Location: error.html");
    exit;
}
?>