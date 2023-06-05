<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../config.php";

// Don't touch this
$secret_key = HCAPTCHA_SECRET;
$site_key = HCAPTCHA_SITEKEY;

$username = $password = $confirm_password = $licence_key = "";
$username_err = $password_err = $confirm_password_err = $licence_key_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
        $captcha_err = "Please complete the captcha.";
        exit;
    }

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $param_username = trim($_POST["username"]);
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = $param_username;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["password_confirmation"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["password_confirmation"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $apikey = generateRandomKey(22);
        $folder_name = generateRandom(10); // Generate a random folder name
        $sql = "INSERT INTO users (username, password, apikey, folder) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt) {
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $apikey, $folder_name);

            if (mysqli_stmt_execute($stmt)) {
                session_start();

                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = mysqli_insert_id($link);
                $_SESSION["username"] = $username;

                // Create the user's folder
                $user_folder = "../media/" . $folder_name;
                if (!mkdir($user_folder, 0777)) {
                    echo "Oops! Something went wrong. Please try again later.";
                    exit;
                }

                header("location: ../dash");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}

function generateRandomKey($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = 'clapped-';
    $character_count = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[random_int(0, $character_count - 1)];
    }

    return $key;
}

function generateRandom($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = '';
    $character_count = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[random_int(0, $character_count - 1)];
    }

    return $key;
}
function showErrorMessage($message, $timeout = 5) {
    echo '<div id="error-box" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #FF6B6B; color: #FFFFFF; padding: 10px; border-radius: 5px; z-index: 9999;">';
    echo $message;
    echo '</div>';

    echo '<script>';
    echo 'setTimeout(function() {';
    echo 'var errorBox = document.getElementById("error-box");';
    echo 'if (errorBox) { errorBox.remove(); }';
    echo '}, ' . ($timeout * 1000) . ');';
    echo '</script>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            <div class="bg-main border-1 border-stone-500/5 rounded-md shadow-4xl max-w-md w-full text-center mx-auto p-10 animate-slide-up">
                <a href="https://clapped.rip/" title="Home">
                    <img id="skull" src="https://cdn.discordapp.com/attachments/1022531334536699934/1115141783396044830/skull.png" alt="logo">
                </a>

                <form action="index.php" method="post">
                    <div class="mb-5 w-full text-left input-container">
                        <label for="username">Username</label>
                        <div class="flex w-full rounded-md overflow-hidden">
                            <div class="bg-gray-200 w-16 text-center py-4">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <input type="text" name="username" placeholder="Username" class="bg-secondary text-gray-400 w-full p-4 focus:bg-primary focus:ring-0" style="background-color: #f2f2f2;">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>

                    </div>

                    <div class="mb-5 w-full text-left input-container">
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

                    <div class="mb-5 w-full text-left input-container">
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

                    <div class="mb-5 w-full text-left input-container">
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