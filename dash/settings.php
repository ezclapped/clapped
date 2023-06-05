<?php
require_once "check_ban.php";
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login");
    exit;
}

$userName = $_SESSION["username"];

require_once "../config.php";
 
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    if(empty($new_password_err) && empty($confirm_password_err)){
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            if(mysqli_stmt_execute($stmt)){
                session_destroy();
                header("location: login");
                exit();
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
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO?>">
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

        .wrapper h1 {
            margin-bottom: 20px;
        }

        .wrapper h3 {
            margin-bottom: 10px;
            color: #fd6ef6;
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 30px;
            border: none;
            border-radius: 0;
            transition: background-color 250ms;
        }

        .btn.btn-warning {
            background-color: #ef6efd;
            color: black;
        }

        .btn-dark {
            background-color: #e36efd;
            color: black;
        }

        #logoutbutton:hover {
            background-color: red;
        }

        .btn:hover {
            background-color: #8e319c;
        }

        a {
            color: #d96efd;
        }

        a:hover {
            color: #e640ff;
        }

        .form-control {
            width: 350px;
            margin: auto;
        }

        .invalid-feedback {
            color: #fdcb6e;
        }

        .changetext {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="box">
        <a href="../logout" id="logoutbutton" class="btn btn-danger">Sign Out of Your Account</a>
        <h1 class="my-5"><b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h1>
        <h3 class="my-5">Account Settings</h3>
        <div class="form-group">
            <h5 class="changetext">Change your Password:</h5>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                    <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-dark" value="Submit">
                </div>
            </form>
        </div>

        <button onclick="goToDashboard()" class="btn btn-dark mt-3">Go to Dashboard</button>
    </div>
</div>
</body>
<script>
    function goToDashboard() {
        window.location.href = "./";
    }
</script>
</html>