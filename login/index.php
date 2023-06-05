<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../dash");
    exit;
}

require_once "../config.php";

//dont touch this
$secret_key = HCAPTCHA_SECRET;
$site_key = HCAPTCHA_SITEKEY;

$username = $password = "";
$username_err = $password_err = $login_err = "";

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO?>">
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #191919;
            color: white;
            margin:0;
            padding:0;
            font-family: sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin-top: 10%;
        }

        .box {
            background-color: #1E1E1E;
            border-radius: 30px;
            padding: 35px;
            text-align: center;
            margin-bottom: 20%;
        }


        .box h2 {
            margin-bottom: 20px;
            color: white;
        }

        .box p {
            margin-bottom: 20px;
            color: white;
        }

        .box input {
            border-radius: 10px;
        }


        .wrapper h2 {
            margin-bottom: 20px;
            color: white;
        }

        .wrapper p {
            margin-bottom: 20px;
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            background-color: #333;
            border: none;
            border-radius: 0;
            color: white;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .invalid-feedback {
            display: block;
            color: #ff6b81;
        }

        .btn-dark {
            background-color: #8118cc;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 16px 32px;
            text-decoration: none;
            margin: 4px 2px;
            cursor: pointer;
            transition: background-color 250ms;
        }

        .btn-dark:hover {
            background-color: #6f15b0;
        }

        p.login-link {
            margin-top: 20px;
            color: white;
        }

        p.login-link a {
            color: #9b2deb;
        }

        p.login-link a:hover {
            color: #8c18de;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="box">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <div class="h-captcha" data-sitekey="<?php echo $site_key; ?>"></div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-dark" value="Login">
            </div>
            <p class="login-link">Don't have an account? <a href="../register">Sign up now</a>.</p>
        </form>
    </div>
</div>
</body>
</html>