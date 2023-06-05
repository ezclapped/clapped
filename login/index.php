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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo WEBSITE_NAME ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO?>">
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <link rel="stylesheet" href="tailwind.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="../assets/fontwesome/css/font-awesome.min.css">
</head>
<body>
<main>
<div class="fixed w-full h-full bg-center bg-cover bg-no-repeat bg-theme-background-mobile sm:bg-theme-background"></div>
<div class="relative w-full h-full table">
    <div class="table-cell align-middle px-5 py-14">
        <div class="bg-main border-1 border-stone-500/5 rounded-md shadow-4xl dexoboxregister text-center mx-auto p-10 animate-slide-up sm:w-1/2 md:w-1/3 lg:w-1/4">
                <a href="<?php echo URL ?>" title="Home">
                    <img id="skull" src="<?php echo LOGO ?>" alt="logo" class="w-36 h-36 mx-auto mb-5">
                </a>

                <form action="login.php" method="post" class="w-full">
                    <div class="mb-3 w-full text-left input-container">
                        <label for="username">Username</label>
                        <div class="flex w-full rounded-md overflow-hidden">
                            <div class="bg-gray-200 w-16 text-center py-4">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <input type="text" name="username" placeholder="Username" class="bg-secondary text-gray-400 w-full p-4 focus:bg-primary focus:ring-0" style="background-color: #f2f2f2;">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>

                    </div>

                    <div class="mb-3 w-full text-left input-container">
                        <label for="password">Password</label>
                        <div class="flex w-full rounded-md overflow-hidden">
                            <div class="bg-gray-200 w-16 text-center py-4">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" placeholder="Password" class="bg-secondary text-gray-400 w-full p-4 focus:bg-primary focus:ring-0">
                        </div>
                        <?php if (!empty($password_err)): ?>
                            <p class="text-red-500"><?php echo $password_err; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-center my-4">
                        <div class="h-captcha" data-theme="dark" data-size="normal" data-sitekey="<?php echo $site_key; ?>"></div>

                    </div>

                    <div class="my-4">
                        <button class="btn-submit button" type="submit" title="Register">Register</button>
                    </div>
                    <div class="mx-auto my-6 max-w-xs">
                        <p style="color: gray">
                        Don't have an account? <a href="../register">Sign up now</a>.
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>
</body>
</html>