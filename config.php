<?php
// General
define('WEBSITE_NAME', 'Clapped.Rip');
define('LOGO', 'https://www.google.com/images/branding/googlelogo/2x/googlelogo_light_color_92x30dp.png');
define('DISCORD_INV', 'https://discord.gg/clapped');

$mediafolder = 'media';
$sitename = $_SERVER['SERVER_NAME'];

//it works because it works, dont touch because it works
$length = "5";

// MySQL Stuff
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'fedox');
define('DB_PASSWORD', 'asdasdasd');
define('DB_NAME', 'clapped');
define('URL', 'localhost');

// HCaptcha Secret Key
define('HCAPTCHA_SECRET', 'asgfas');
define('HCAPTCHA_SITEKEY', 'asg');

// Development
define('DEV_MODE', true); // false or true


// Implementation
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}


$sql = "SELECT apikey FROM users";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $apikey);

    $apikeys = array();

    while (mysqli_stmt_fetch($stmt)) {
        array_push($apikeys, $apikey);
    }

    mysqli_stmt_close($stmt);

    $tokens = $apikeys;
} else {
    $tokens = null;
}

// Set the API key to retrieve the author
$apikey = isset($_POST['key']) ? $_POST['key'] : null;

// Retrieve the author based on the API key
$author = null;
if ($stmt = mysqli_prepare($link, "SELECT username FROM users WHERE apikey = ?")) {
    mysqli_stmt_bind_param($stmt, "s", $apikey);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $author);

    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        mysqli_stmt_close($stmt);
        $author = null;
    }
}
