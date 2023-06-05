<?php
require_once "../config.php";

// Don't touch this
$secret_key = HCAPTCHA_SECRET;
$site_key = HCAPTCHA_SITEKEY;

$username = $password = $confirm_password = $licence_key = "";;
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

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
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

        .btn-primary {
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

        .btn-primary:hover {
            background-color: #6f15b0;
        }

        .btn-secondary {
            background-color: #444;
            border: none;
            border-radius: 25px;
            padding: 16px 32px;
            text-decoration: none;
            margin: 4px 2px;
            cursor: pointer;
            transition: background-color 250ms;
        }

        .btn-secondary:hover {
            background-color: #555;
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
<body class="back-row-toggle splat-toggle">
<div class="container">
    <div class="box">
        <h2>Sign Up</h2>
        <p>Please fill out this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Licence Key</label>
                <input type="text" name="licence_key" class="form-control <?php echo (!empty($licence_key_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $licence_key; ?>">
                <span class="invalid-feedback"><?php echo $licence_key_err; ?></span>
            </div>
            <div class="form-group">
                <div class="h-captcha" data-sitekey="<?php echo $site_key; ?>"></div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p class="login-link">Already have an account? <a href="login">Login here</a>.</p>
        </form>
    </div>
</div>
</body>
</html>