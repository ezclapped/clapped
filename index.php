<?php

require_once "config.php";

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
    <script src="https://kit.fontawesome.com/983377ea12.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./assets/css/PureSnow.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300&display=swap');
        body {
            background-color: #191919;
            color: white;
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            overflow: hidden;

        }

        .mainContent {
            padding: 210px 0px;

        }

        input[type=button],
        input[type=submit],
        input[type=reset] {
            background-color: #e74c3c;
            border: none;
            color: black; 
            padding: 16px 32px;
            text-decoration: none;
            margin: 4px 2px;
            cursor: pointer;
            transition: background-color 250ms;
        }

        input[type=button]:hover,
        input[type=submit]:hover,
        input[type=reset]:hover {
            background-color: #ff6b81;
        }

        input[type=file] {
            display: none;
        }

        .loginButton {
            padding: 10px 25px;
            background-color: #444;
            font-size: 16px;
            margin-left: 10px;
            font-weight: bold;
            border-radius: 10px;
            color: white;
            text-decoration: none;
            transition: background-color 500ms;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .loginButtonLight {
            padding: 10px 25px;
            background-color: #6c5ce7;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            color: white;
            text-decoration: none;
            transition: background-color 500ms;
            box-shadow: 0 0 25px #6c5ce7;
            margin-top: 20px;
        }

        .loginButtonLight:hover {
            box-shadow: 0 0 50px #6c5ce7;
        }

        .loginButton:hover {
            background-color: #555;
            transform: scale(1.05);
        }


        img {
            max-width: 200px;
        }

        /* Social Buttons */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh;
            width: 100%;
            margin-top: 10%
            
        }

        #github,
        #discord {
            font-size: 4em;
            background-color: #18191f;
            color: #fff;
            box-shadow: 2px 2px 2px #00000080, 10px 1px 12px #00000080,
            2px 2px 10px #00000080, 2px 2px 3px #00000080, inset 2px 2px 10px #00000080,
            inset 2px 2px 10px #00000080, inset 2px 2px 10px #00000080,
            inset 2px 2px 10px #00000080;
            border-radius: 29px;
            padding: 11px 19px;
            margin: 0 40px;
            animation: animate 3s linear infinite;
            text-shadow: 0 0 50px #0072ff, 0 0 100px #0072ff, 0 0 150px #0072ff,
            0 0 200px #0072ff;
            outline: none;
        }

        #github {
            animation-delay: 0.1s;
            margin-left: 10%
            
        }

        #discord {
            animation-delay: 0.1s;
            margin-right: 10%
        }

        @keyframes animate {
            from {
                filter: hue-rotate(0deg);
            }
            to {
                filter: hue-rotate(360deg);
            }
        }


        #snowcontainer {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 100vh;
            z-index: -1;
        }

        .clapped-text {
            font-size: 48px;
            font-weight: bold;
            font-family: Rubik, sans-serif;
            color: white;
        }

        .rip-text {
            font-size: 48px;
            font-weight: bold;
            font-family: Rubik, sans-serif;
            color: #6c5ce7;
            animation: rip-glow 2s infinite;
        }

        .description-text {
            font-size: 20px;
            font-family: Rubik, sans-serif;
            color: white;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        @keyframes rip-glow {
            0% {
                text-shadow: 0 0 5px #6c5ce7, 0 0 10px #6c5ce7, 0 0 15px #6c5ce7, 0 0 20px #6c5ce7;
            }
            50% {
                text-shadow: none;
            }
            100% {
                text-shadow: 0 0 5px #6c5ce7, 0 0 10px #6c5ce7, 0 0 15px #6c5ce7, 0 0 20px #6c5ce7;
            }
        }


    </style>



</head>
<?php

$sql = "SELECT id FROM users";
$result = mysqli_query($link, $sql);
$totalUsers = mysqli_num_rows($result);
?>

<body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<!--<img src="<?php echo LOGO?>" alt="Logo">-->

<div class="mainContent">

    <h1 class="clapped-text">CLAPPED.<span class="rip-text">RIP</span></h1>
    <p class="description-text">Another image/file uploader like any other, but this one is special because of the<br>domain and because of the best support team, your experience will be incredible.<br><br><b>TOTAL USERS: <?php echo $totalUsers; ?><br><br></p>
    <a href="login" class="loginButtonLight">Login</a>
    <a href="register" class="loginButton">Register</a>

    <div class="container">

        <button class="fa-brands fa-github" id="github"></button>
        <button class="fa-brands fa-discord" id="discord"></button>
    </div>

</div>

<div id="snowcontainer">

    <div id="snow"></div>

</div>

</body>
<script src="./assets/js/PureSnow.js"></script>
<script>
    let discordbutton = document.getElementById('discord');

    discordbutton.addEventListener('click', () => {
        window.open('https://discord.gg/moxxcc', '_blank');
    });

    let githubbutton = document.getElementById('github');

    githubbutton.addEventListener('click', () => {
        window.open('https://github.com/Fedox-die-Ente/Uploader', '_blank');
    });
</script>
</html>