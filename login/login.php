<?php
session_start();
require_once "../config.php";
$secret_key = HCAPTCHA_SECRET;
$site_key = HCAPTCHA_SITEKEY;
$login_err = "";
$pass_err = "";
$user_err = "";
$cap_error = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //added hcapcha
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
        if (!DEV_MODE) {
            $_SESSION['captcha_err'] = 'Invalid hCaptcha verification. (You might be a bot)';
            $cap_error = "Invalid hCaptcha verification.";
            header("location: indexold.php");
            exit;
        }
    }

    if (empty(trim($_POST["username"]))) {
        $_SESSION['username_err'] = "Please enter a username.";
        $user_err = "Please enter username.";
        header("location: indexold.php");
        exit;
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $_SESSION['password_err'] = "Please enter your password.";
        $pass_err = "Please enter your password.";
        header("location: indexold.php");
        exit;
    } else {
        $password = trim($_POST["password"]);
    }

    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = $username;

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            header("location: ../dash");   
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
$_SESSION['login_err'] = $login_err;
$_SESSION['username_err'] = $user_err;
$_SESSION['password_err'] = $pass_err;
$_SESSION['captcha_err'] = $cap_error;
header("location: indexold.php");
exit;

?>