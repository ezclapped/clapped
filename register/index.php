<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../config.php";
$secret_key = HCAPTCHA_SECRET;
$site_key = HCAPTCHA_SITEKEY;

$username = $password = $confirm_password = $licence_key = $captcha = "";
$username_err = $password_err = $confirm_password_err = $licence_key_err = $captcha_err = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | <?php echo WEBSITE_NAME ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO ?>">
    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <link rel="stylesheet" href="tailwind.css">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="../assets/fontwesome/css/font-awesome.min.css">

</head>
<body>
<main>
<div class="fixed w-full h-full bg-center bg-cover bg-no-repeat bg-theme-background-mobile sm:bg-theme-background"></div>
<div class="relative w-full h-full table">
    <div class="table-cell align-middle px-5 py-14">
        <div class="bg-main border-1 border-stone-500/5 rounded-md shadow-4xl dexoboxregister text-center mx-auto p-10 animate-slide-up sm:w-1/2 md:w-1/3 lg:w-1/4">
            <a href="<?php echo URL ?>" title="Home">
                <svg xmlns="http://www.w3.org/2000/svg" width="85" height="85" viewBox="0 0 512 512" class="fill-theme drop-shadow-theme button mx-auto mb-8"><title>ionicons-v5-l</title><path d="M256,16C141.31,16,48,109.31,48,224V378.83l82,32.81L146.88,496H192V432h32v64h16V432h32v64h16V432h32v64h45.12L382,411.64l82-32.81V224C464,109.31,370.69,16,256,16ZM168,336a56,56,0,1,1,56-56A56.06,56.06,0,0,1,168,336Zm51.51,64L244,320h24l24.49,80ZM344,336a56,56,0,1,1,56-56A56.06,56.06,0,0,1,344,336Zm104,32h0Z"/></svg>
            </a>

                <form action="regs.php" method="post">
                    <div class="mb-3 w-full text-left input-container">
                        <label for="username">Username</label>
                        <div class="flex w-full rounded-md overflow-hidden">
                            <div class="bg-gray-200 w-16 text-center py-4">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <input type="text" name="username" placeholder="Username" class="bg-secondary text-gray-400 w-full p-4 focus:bg-primary focus:ring-0" style="background-color: #f2f2f2;">
                        </div>
                        <?php
                        if(isset($_SESSION['username_err'])) {
                            echo '<p name="username-error" id="username-error" class="text-sm text-red-500 text-left mt-1.5">'. $_SESSION['username_err'] .'</p>';
                            unset($_SESSION['username_err']);
                        }
                        ?>
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

                    <div class="mb-3 w-full text-left input-container">
                        <label for="username">Password Confirmation</label>
                        <div class="flex w-full rounded-md overflow-hidden">
                            <div class="bg-gray-200 w-16 text-center py-4">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="bg-secondary text-gray-400 w-full p-4 focus:bg-primary focus:ring-0">
                        </div>
                        <?php if (!empty($confirm_password_err)): ?>
                            <p class="text-red-500"><?php echo $confirm_password_err; ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3 w-full text-left input-container">
                        <label for="license_key">License Key</label>
                        <div class="flex w-full rounded-md overflow-hidden">
                            <div class="bg-gray-200 w-16 text-center py-4">
                                <i class="fas fa-key"></i>
                            </div>
                            <input type="text" name="license_key" placeholder="License Key" class="bg-secondary text-gray-400 w-full p-4 focus:bg-primary focus:ring-0">

                        </div>
                    </div>

                    <div class="flex justify-center my-4">
                        <div class="h-captcha" data-theme="dark" data-size="normal" data-sitekey="<?php echo $site_key; ?>"></div>
                        <span class="invalid-feedback"><?php echo $captcha_err; ?></span>
                    </div>

                    <div class="mx-auto my-6 max-w-xs">
                        <p style="color: gray">
                            By registering, you agree to our
                            <a href="https://clapped.rip/legal" title="Terms of Service" target="_blank" >Terms of Service</a>
                            and
                            <a href="https://clapped.rip/legal" title="Privacy Policy" target="_blank" >Privacy Policy</a>.
                        </p>
                    </div>

                    <div class="my-4">
                        <button class="btn-submit button" type="submit" title="Register">Register</button>
                    </div>
                    <div class="mx-auto my-6 max-w-xs">
                        <p style="color: gray">
                            Already have an account? <a href="../login">Login here</a>.
                        </p>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>
</body>
</html>