<?php
require_once "../config.php";

// ÃœberprÃ¼fe, ob der Benutzer eingeloggt ist
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Benutzer ist nicht eingeloggt, umleiten oder Fehlermeldung anzeigen
    header("Location: ../login"); // Passe den Pfad zur Login-Seite an
    exit;
}

// ÃœberprÃ¼fe den Bann-Status des Benutzers
$user_id = $_SESSION["id"]; // Passe den Namen der Session-Variable an, die die Benutzer-ID speichert
$sql = "SELECT is_banned FROM users WHERE id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $is_banned);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($is_banned) {
    // Benutzer ist gebannt, zeige Bann-Bildschirm und Option zum Abspielen des Sounds
    echo "<h1>You are banned!</h1>";
    echo "<p>Your account has been banned. Please contact the administrator for further assistance.</p>";
    echo "<button onclick=\"playBanSound()\">Entbannen lassen</button>";
    echo "<audio id=\"banSound\"><source src=\"ban.mp3\" type=\"audio/mpeg\"></audio>";
    echo "<script>";
    echo "function playBanSound() {";
    echo "  var audio = document.getElementById('banSound');";
    echo "  audio.play();";
    echo "}";
    echo "</script>";

    exit;
}
?>
<!DOCTYPE html>