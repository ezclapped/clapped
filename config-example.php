<?php
// General
define('WEBSITE_NAME', '');
define('LOGO', '');
define('DISCORD_INV', '');
define('URL', '');

$mediafolder = 'media';
$sitename = $_SERVER['SERVER_NAME'];

//it works because it works, dont touch because it works
$length = "5";

// MySQL Stuff
define('DB_SERVER', '');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_NAME', '');

// HCaptcha Secret Key
define('HCAPTCHA_SECRET', '');
define('HCAPTCHA_SITEKEY', '');

// Development
define('DEV_MODE', false); // false or true


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

require_once(__DIR__."/x/CloudFirewall.php");
use CF\CloudFirewall;

$firewall = new CloudFirewall('[CLOUDFLAREEMAIL]', '[CLOUDFLAREKEY]', '[CLOUDFLAREZONE]');

$firewall->sqlInjectionBlock(false);
$firewall->xssInjectionBlock(false);
$firewall->cookieStealBlock(false);
//$firewall->antiFlood(5, 20, 5, false)

// if($_SERVER["SCRIPT_NAME"] != "/x/functions.php"){
// 	if($_SERVER['HTTP_HOST']=="127.0.0.1"){ header("HTTP/1.0 404 Not Found"); die(); }
// }