<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../config.php";

// Don't touch this
//$secret_key = HCAPTCHA_SECRET;
//$site_key = HCAPTCHA_SITEKEY;

$username = $password = $confirm_password = $licence_key = "";
$username_err = $password_err = $confirm_password_err = $licence_key_err = "";

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



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["password_confirmation"]);
    $licence_key = trim($_POST["license_key"]);

    $sql = "SELECT id FROM licensekeys WHERE `key` = ? AND used = FALSE";
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $licence_key);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) != 1) {
                $licence_key_err = "Invalid or used license key.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }

    if (empty($licence_key_err)) {
        

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

        if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($licence_key_err)) {
            $defaultadmin = false;
            $apikey = generateRandomKey(22);
            $folder_name = generateRandom(10); // Generate a random folder name
        
            $insert_sql = "INSERT INTO users (username, password, is_admin, apikey, folder) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $insert_sql);
        
            if ($stmt) {
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
        
                mysqli_stmt_bind_param($stmt, "ssiss", $param_username, $param_password, $defaultadmin, $apikey, $folder_name);
        
                if (mysqli_stmt_execute($stmt)) {
                    // Mark license key as used
                    $update_sql = "UPDATE licensekeys SET used = TRUE WHERE `key` = ?";
                    $update_stmt = mysqli_prepare($link, $update_sql);
                    if ($update_stmt) {
                        mysqli_stmt_bind_param($update_stmt, "s", $licence_key);
                        mysqli_stmt_execute($update_stmt);
                        mysqli_stmt_close($update_stmt);
                    }
        
                    session_start();
        
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = mysqli_insert_id($link);
                    $_SESSION["username"] = $username;
        
                    // Create the user's folder
                    $user_folder = "../media/" . $folder_name . "/";
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
    }else{
        echo "license key is invalid";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clapped - Register</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
</head>
