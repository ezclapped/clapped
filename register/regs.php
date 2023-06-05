<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../config.php";

// Don't touch this
$secret_key = HCAPTCHA_SECRET;

$username = $password = $confirm_password = $licence_key = "";
$username_err = $password_err = $confirm_password_err = $licence_key_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $licence_key = trim($_POST["licence_key"]);

    $response = $_POST['h-captcha-response'];

    $verify_url = 'https://hcaptcha.com/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $response
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($verify_url, false, $context);
    $result_data = json_decode($result, true);

    if (!$result_data['success']) {
        $captcha_err = "Please complete the captcha.";
        exit;
    }

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $param_username = trim($_POST["username"]);
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = $param_username;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["password_confirmation"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["password_confirmation"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $apikey = generateRandomKey(22);
        $folder_name = generateRandom(10); // Generate a random folder name
        $sql = "INSERT INTO users (username, password, apikey, folder) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt) {
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $apikey, $folder_name);

            if (mysqli_stmt_execute($stmt)) {
                session_start();

                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = mysqli_insert_id($link);
                $_SESSION["username"] = $username;

                // Create the user's folder
                $user_folder = "../media/" . $folder_name;
                if (!mkdir($user_folder, 0777)) {
                    echo "Oops! Something went wrong. Please try again later.";
                    exit;
                }

                header("location: ../dash");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}

function generateRandomKey($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = 'clapped-';
    $character_count = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[random_int(0, $character_count - 1)];
    }

    return $key;
}

function generateRandom($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = '';
    $character_count = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[random_int(0, $character_count - 1)];
    }

    return $key;
}
function showErrorMessage($message, $timeout = 5) {
    echo '<div id="error-box" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #FF6B6B; color: #FFFFFF; padding: 10px; border-radius: 5px; z-index: 9999;">';
    echo $message;
    echo '</div>';

    echo '<script>';
    echo 'setTimeout(function() {';
    echo 'var errorBox = document.getElementById("error-box");';
    echo 'if (errorBox) { errorBox.remove(); }';
    echo '}, ' . ($timeout * 1000) . ');';
    echo '</script>';
}

?>