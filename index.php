<?php
require_once "config.php";
$sql = "SELECT id FROM users";
$result = mysqli_query($link, $sql);
$totalUsers = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#">
<head>
    <title><?php  echo WEBSITE_NAME?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:image" content="<?php echo LOGO?>"/>
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO?>">
    <meta property="og:description" content="Upload your images to the web!"/>
    <meta property="og:url" content="<?php echo DISCORD_INV?>"/>
    <meta property="og:title" content="CDN Upload"/>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/PureSnow.css">
    <link rel="stylesheet" href="./assets/css/Index/index.css">
</head>

<body>
    <div class="mainContent">
        <h1 class="clapped-text">CLAPPED.<span class="rip-text">RIP</span></h1>

        <div class="box-container">
            <div class="infobox">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="infobox">
                <h3 id="fileCount">Loading..</h3>
                <p>Total Uploads</p>
            </div>
            <div class="infobox">
                <h3 id="folder-size">Loading..</h3>

                <p>Total Storage</p>
            </div>
        </div>

        <div class="descbox">
            <p class="description-text">Another image/file uploader like any other, but this one is special because of the domain and because of the best support team, your experience will be incredible.</p>
        </div>

        <div class="login-buttons">
            <a href="login" class="loginButton">Login</a>
            <p class="or-divider">or</p>
            <a href="register" class="registerButton">Register</a>
        </div>

    </div>
    <div id="snowcontainer">
        <div id="snow"></div>
    </div>
</body>
</html>

<script src="./assets/js/PureSnow.js"></script>
<script src="./utils/client.js"></script>
<script src="./utils/getpngs.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://kit.fontawesome.com/983377ea12.js" crossorigin="anonymous"></script>
<script src="./utils/client.js"></script>