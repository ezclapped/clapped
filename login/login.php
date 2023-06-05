<?php 
require_once "../config.php";
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
         if(!DEV_MODE) {
             echo 'Invalid hCaptcha verification. (You might be a bot)';
             exit;
         }
     }



    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
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
?>