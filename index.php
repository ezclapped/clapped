<?php

require_once "config.php";
$sql = "SELECT id FROM users";
$result = mysqli_query($link, $sql);
$totalUsers = mysqli_num_rows($result);

$sql = "SELECT news FROM general";
$result = mysqli_query($link, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $newsText = $row['news'];
}
?>

<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" style="width: auto">
<head>
    <title><?php  echo WEBSITE_NAME?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:image" content="<?php echo LOGO?>"/>
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO?>">
    <meta property="og:description" content="Upload your images to the web!"/>
    <meta property="og:url" content="<?php echo DISCORD_INV?>"/>
    <meta property="og:title" content="CDN Upload"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/PureSnow.css">
    <link rel="stylesheet" href="./assets/css/Index/index.css">
    <style>
        @font-face {
            font-family: gilroy_bold;
            src: url(/assets/fonts/Gilroy-Bold.ttf)
        }

        @font-face {
            font-family: gilroy_medium;
            src: url(/assets/fonts/Gilroy-Medium.ttf)
        }

        @font-face {
            font-family: gilroy_light;
            src: url(/assets/fonts/Gilroy-Light.ttf)
        }



        .buffer-1 {
            height: 50px;
        }

        .bg-ps {
            background-color: #1e1e1e;
            border: 1px solid #7371fc
        }

        .text-force {
            font-family: "gilroy_medium";
            color: white !important;
        }

        .text-small-mid {
            width: 70%;
        }


        .wave {
            width: 100%;
            filter: drop-shadow(0px 0px 0px rgba(0,0,0,1));
        }

        .gray {
            filter: drop-shadow(0px 0px 0px rgba(70,70,70,1));
        }

        .wave_black {
            background-color: #0d0d0d;
        }

        .wave_gray {
            background-color: #161616;
        }

        @-moz-document url-prefix() {
            .wave_black {
                background-color: #050505;
            }

            .wave_gray {
                background-color: #0e0e0e;
            }
        }

        .text-small {
            font-family: "gilroy_light";
            color: #BCBCBC;
        }

        .text-white {
            color: white !important;
        }

        .ps-color {
            color: #7371fc !important;
            font-family: "gilroy_bold";
        }

        .mobile_fix {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        @media (max-width : 1024px) {
            .mobile_fix {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .mobile-text {
                text-align: center;
            }

            .col-2 {
                width: 100% !important;
                margin-bottom: 20px;
            }

            .mobile-offset {
                margin-left: 0 !important;
            }

        }

        .team {
            border-radius: 20px;
        }

        .avatar {
            border-radius: 50%;
            object-fit: cover;


        }


        .text_animation {
            background: linear-gradient(to right,#8785fb,#3836b8,#8785fb,#3836b8,#8785fb);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: rainbow_animation 6s ease-in-out infinite;
            background-size: 400% 100%;
        }

        @keyframes rainbow_animation {
            0%,100% {
                background-position: 0 0
            }

            50% {
                background-position: 100% 0
            }
        }

    </style>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center">
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


        <div class="wave-container">
            <img src="https://beta.print-screen.dev/assets/images/welcome/wave_gray.png" class="wave gray">
        </div>

    </div>
    <div class="content-container">
        <div class="row">
            <div class="col-12 row mx-0">
                <div class="col-12 row">
                    <div class="col-2 offset-2 mobile-offset">
                        <center>
                            <img src="https://beta.print-screen.dev/assets/icons/numbers/1.png">
                            <h3 class="gilroy_bold text-white">Simple Interface</h3>
                            <div class="text-force text-small-mid text-small">
                                We provide a modern and intuitive platform for you to manage your uploads.
                            </div>
                        </center>
                    </div>
                    <div class="col-2">
                        <center>
                            <img src="https://beta.print-screen.dev/assets/icons/numbers/2.png">
                            <h3 class="gilroy_bold text-white">Secure Storing</h3>
                            <div class="text-force text-small-mid text-small">
                                Your data is safely stored and encrypted to protect your privacy.
                            </div>
                        </center>
                    </div>
                    <div class="col-2">
                        <center>
                            <img src="https://beta.print-screen.dev/assets/icons/numbers/3.png">
                            <h3 class="gilroy_bold text-white">Reliable Support</h3>
                            <div class="text-force text-small-mid text-small">
                                Our support team is looking forward to help you and will respond as quickly as possible.
                            </div>
                        </center>
                    </div>
                    <div class="col-2">
                        <center>
                            <img src="https://beta.print-screen.dev/assets/icons/numbers/4.png">
                            <h3 class="gilroy_bold text-white">Availability</h3>
                            <div class="text-force text-small-mid text-small">
                                Our service is online 24/7 to ensure you have access to what you need, at all times.
                            </div>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <img src="https://beta.print-screen.dev/assets/images/welcome/wave_black.png" class="mt-1 wave">
        <div class="wave_black">
            <div class="row">
                <div class="col-8 offset-2 row">
                    <h1 class="text-force ps-color">Neuigkeiten</h1>
                    <span class="gilroy_bold"><?php echo $newsText ?></span>
                </div>
            </div
        </div>
        <div class="buffer-1"></div>
        <img src="https://beta.print-screen.dev/assets/images/welcome/wave_gray.png" class="wave gray">
        <div class="wave_gray">
            <!-- Todo: sort it left not in the center -->
            <div class="row">
                <div class="col-8 offset-2 row">
                    <h1 class="text-force ps-color mobile_fix">Wer wir sind - Das Team.</h1>
                    <br>
                    <br>
                    <div class="row mobile_fix justify-content-start">
                        <div class="col-4 p-4 mobile_fix">
                            <div class="mobile_fix bg-ps p-2 team">
                                <div class="text-left">
                                    <img class="avatar" height="150px" src="https://cdn.discordapp.com/attachments/1116121458020720680/1120473882038640651/fedox.png">
                                </div>
                                <div class="mobile-text" style="line-height: 100%;">
                                    <h1>Fedox</h1>
                                    <h4>
                                        <span class="text_animation">Owner</span>
                                    </h4>
                                    <p class="center-text">fedox</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 p-4 mobile_fix">
                            <div class="mobile_fix bg-ps p-2 team">
                                <div class="text-left">
                                    <img class="avatar" height="150px" src="https://cdn.discordapp.com/attachments/1116121458020720680/1120473699963908208/8b6336cc07ae49f47b690c8fb6a7860a.jpg">
                                </div>
                                <div class="mobile-text" style="line-height: 100%;">
                                    <h1>Noritem</h1>
                                    <h4>
                                        <span class="text_animation">Veteran</span>
                                    </h4>
                                    <p class="center-text">Clapped#1655</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


</body>
</html>

<script src="./assets/js/PureSnow.js"></script>
<script src="./utils/client.js"></script>
<script src="./utils/getpngs.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://kit.fontawesome.com/983377ea12.js" crossorigin="anonymous"></script>
<script src="./utils/client.js"></script>
