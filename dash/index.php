<?php
require_once "check_ban.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login");
    exit;
}

$userName = $_SESSION["username"];


$sql = "SELECT apikey FROM users WHERE username = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $apiKey);
    mysqli_stmt_fetch($stmt);

    mysqli_stmt_close($stmt);
}


$username = $_SESSION["username"];
$filename = $username . ".sxcu";
$jsonContent = '{
  "Version": "14.1.0",
  "DestinationType": "clapped",
  "RequestMethod": "POST",
  "RequestURL": "https://clapped.rip/x/upload.php",
  "Body": "MultipartFormData",
  "Arguments": {
    "key": "' . $apiKey . '"
  },
  "FileFormName": "sharex"
}';

$file = fopen($filename, "w");
fwrite($file, $jsonContent);
fclose($file);

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

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 30px;
            border: none;
            border-radius: 25px;
            transition: background-color 250ms;
        }
        .btn.btn-warning {
            background-color: #fb6efd;
            color: black;
        }
        .btn.btn-dark {
            background-color: #ea6efd;
            color: black;
        }
        .btn:hover {
            background-color: #8c319c;
        }

        a {
            color: #fd6ee5;
        }
        a:hover {
            color: #ff40f2;
        }

        .btn-danger:hover {
            background-color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="box">
        <h1 class="my-5">Hi, <b id="name"></b></h1>
        <h3>Welcome to <?php echo WEBSITE_NAME?></h3>
        <p>
            <a href="settings" class="btn btn-dark">Account Settings</a>
            <br><br>
            <a href="<?php echo $filename?>" class="btn btn-warning">Download ShareX Config</a>
            <?php

            $sql = "SELECT is_admin FROM users WHERE username = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $is_admin);
                mysqli_stmt_fetch($stmt);

                if ($is_admin == 1) {
                    echo '<br><br><a href="../admin" class="btn btn-danger">Admin Panel</a>';
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($link);
            ?>
        </p>
    </div>
</div>
</body>
<script>
    var i = 0;
    var txt = '<?php echo htmlspecialchars($_SESSION["username"]); ?>';
    var speed = 100;

    function typeWriter() {
        if (i < txt.length) {
            document.getElementById("name").innerHTML += txt.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }
    typeWriter()
</script>
</html>