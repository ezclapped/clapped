<?php
session_start();
require_once "../config.php";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login");
    exit;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}


$sql = "SELECT id FROM users";
$result = mysqli_query($link, $sql);
$totalUsers = mysqli_num_rows($result);
$userName = $_SESSION["username"];
// Get API Key
$sql = "SELECT apikey FROM users WHERE username = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $apiKey);
    mysqli_stmt_fetch($stmt);

    mysqli_stmt_close($stmt);
}


$myfile = fopen($userName . ".sxcu", "w") or die("Unable to open file!");

$fileconten = '{
    "Version": "14.1.0",
    "DestinationType": "clapped",
    "RequestMethod": "POST",
    "RequestURL": "https://' . $_SERVER['HTTP_HOST'] . '/x/upload.php",
    "Body": "MultipartFormData",
    "Arguments": {
      "key": "' . $apiKey . '"
    },
    "FileFormName": "sharex"
  }';

fwrite($myfile, $fileconten);
fclose($myfile);

// Count users uploads
// Get Folder name
$sql = "SELECT folder FROM users WHERE username = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $folderName);
    mysqli_stmt_fetch($stmt);

    mysqli_stmt_close($stmt);
}

$folder = '../media/' . $folderName;
$count = 0;

function countFiles($folder) {
    global $count;
    $files = glob($folder . '/*');

    foreach ($files as $file) {
        if (is_file($file) && !endsWith($file, ['.html', '.css'])) {
            $count++;
        } elseif (is_dir($file)) {
            countFiles($file);
        }
    }
}

function endsWith($haystack, $needles) {
    foreach ($needles as $needle) {
        if (substr($haystack, -strlen($needle)) === $needle) {
            return true;
        }
    }

    return false;
}

countFiles($folder);
$freeSpace = disk_free_space($folder);

// Get newest folders

$folders = array_diff(scandir($folder), array('..', '.'));

usort($folders, function($a, $b) use ($folder) {
    $pathA = $folder . '/' . $a;
    $pathB = $folder . '/' . $b;
    return filemtime($pathB) - filemtime($pathA);
});

$newestFolders = array_slice($folders, 0, 3);

$index1 = '';
$index2 = '';
$index3 = '';

foreach ($newestFolders as $index => $folder) {
    $directory = $folder; // Annahme: Der Ordnername enthält den vollständigen Pfad zur index.html-Datei
    $filePath = $directory . '/index.html';

    if (is_file($filePath)) {
        if ($index === 0) {
            $index1 = [
                'path' => $filePath,
                'size' => formatSize(disk_total_space($directory))
            ];
        } elseif ($index === 1) {
            $index2 = [
                'path' => $filePath,
                'size' => formatSize(disk_total_space($directory))
            ];
        } elseif ($index === 2) {
            $index3 = [
                'path' => $filePath,
                'size' => formatSize(disk_total_space($directory))
            ];
        }
    }
}

// Funktion zur Formatierung der Dateigröße
function formatSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?php echo WEBSITE_NAME ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO?>">
    <link rel="stylesheet" href="../assets/css/PureSnow.css">
    <link rel="stylesheet" href="../assets/fontwesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/Dashboard/index.css">
</head>
<div id="bg"></div>
<body>
    <div class="mainContent">
        <h1 class="clapped-text">CLAPPED.<span class="rip-text">RIP</span></h1>
        <div class="box-container">
            <div class="infobox">
                <h3><?php echo $totalUsers ?></h3>
                <p>Total Users</p>
            </div>
            <div class="infobox">
                <h3 id="fileCount"><?php echo $count?></h3>
                <p>Your Uploads</p>
            </div>
            <div class="infobox">
                <h3 id="folder-size"><?php echo formatBytes($freeSpace) ?></h3>

                <p>Your Storage</p>
            </div>
        </div>
        <div class="control-container">
            <h2 class="welcometext my-5">Hey <b id="username"><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, welcome to your dashboard</h2>
            <div class="button-container">
                <a class="button" href="../index.php">HOME</a>
                <a class="button" href="./settings.php">SETTINGS</a>
                <a class="button" href="../tos.php">TOS</a>
            </div>
            <a href="<?php echo $userName . '.sxcu'; ?>" download class="big-button">Download ShareX Config</a>
            <div class="separator"></div>
            <div class="box-container">
                <div class="box">
                    <div class="icon">
                        <svg height="64px" width="64px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-51.2 -51.2 614.40 614.40" xml:space="preserve" fill="#000000" stroke="#000000" stroke-width="0.00512"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#FFFFFF;" d="M304.35,165.234c11.087,0,20.117,9.03,20.117,20.129c0,11.087-9.03,20.117-20.117,20.117 c-11.098,0-20.129-9.03-20.129-20.117C284.221,174.264,293.251,165.234,304.35,165.234z"></path> <path style="fill:#5f006b;" d="M512,67.461v377.078c0,12.466-10.138,22.604-22.604,22.604H22.604 C10.138,467.143,0,457.005,0,444.539V67.461c0-12.466,10.138-22.604,22.604-22.604h466.792C501.862,44.857,512,54.995,512,67.461z M489.396,444.539V67.461H22.604v377.078c0,0.011,0,0.011,0,0.011L489.396,444.539z"></path> <path style="fill:#FFFFFF;" d="M489.396,67.461v377.078L22.604,444.55c0,0,0,0,0-0.011V67.461H489.396z M458.271,359.684V109.877 c0-6.239-5.052-11.302-11.302-11.302H65.031c-6.25,0-11.302,5.063-11.302,11.302v249.807c0,6.239,5.052,11.302,11.302,11.302 h381.937C453.219,370.986,458.271,365.923,458.271,359.684z M251.988,407.762c0-6.25-5.052-11.302-11.302-11.302h-25.78 c-6.239,0-11.302,5.052-11.302,11.302c0,6.239,5.063,11.302,11.302,11.302h25.78C246.936,419.064,251.988,414.001,251.988,407.762z M174.637,407.762c0-6.25-5.063-11.302-11.302-11.302H65.031c-6.25,0-11.302,5.052-11.302,11.302 c0,6.239,5.052,11.302,11.302,11.302h98.304C169.574,419.064,174.637,414.001,174.637,407.762z"></path> <path style="fill:#5f006b;" d="M458.271,109.877v249.807c0,6.239-5.052,11.302-11.302,11.302H65.031 c-6.25,0-11.302-5.063-11.302-11.302V109.877c0-6.239,5.052-11.302,11.302-11.302h381.937 C453.219,98.575,458.271,103.639,458.271,109.877z M435.667,348.382v-4.566c-0.927-0.509-1.808-1.142-2.588-1.933l-76.627-76.616 l-35.432,35.432l47.683,47.683H435.667z M435.667,312.521V121.179H76.333v177.135l113.166-113.155 c4.408-4.408,11.573-4.408,15.981,0l99.559,99.559l43.422-43.422c4.419-4.408,11.573-4.408,15.981,0L435.667,312.521z M336.73,348.382L197.49,209.131L76.333,330.276v18.106H336.73z"></path> <path style="fill:#9B8CCC;" d="M435.667,343.816v4.566h-66.964l-47.683-47.683l35.432-35.432l76.627,76.616 C433.858,342.675,434.74,343.307,435.667,343.816z"></path> <path style="fill:#f099d9;" d="M435.667,121.179v191.342l-71.225-71.225c-4.408-4.408-11.562-4.408-15.981,0l-43.422,43.422 L205.48,185.16c-4.408-4.408-11.573-4.408-15.981,0L76.333,298.314V121.179H435.667z M347.071,185.363 c0-23.565-19.168-42.733-42.721-42.733c-23.565,0-42.733,19.168-42.733,42.733c0,23.553,19.168,42.721,42.733,42.721 C327.903,228.084,347.071,208.916,347.071,185.363z"></path> <polygon style="fill:#9B8CCC;" points="197.49,209.131 336.73,348.382 76.333,348.382 76.333,330.276 "></polygon> <g> <path style="fill:#5f006b;" d="M304.35,142.63c23.553,0,42.721,19.168,42.721,42.733c0,23.553-19.168,42.721-42.721,42.721 c-23.565,0-42.733-19.168-42.733-42.721C261.617,161.798,280.785,142.63,304.35,142.63z M324.467,185.363 c0-11.098-9.03-20.129-20.117-20.129c-11.098,0-20.129,9.03-20.129,20.129c0,11.087,9.03,20.117,20.129,20.117 C315.437,205.48,324.467,196.45,324.467,185.363z"></path> <path style="fill:#5f006b;" d="M240.686,396.46c6.25,0,11.302,5.052,11.302,11.302c0,6.239-5.052,11.302-11.302,11.302h-25.78 c-6.239,0-11.302-5.063-11.302-11.302c0-6.25,5.063-11.302,11.302-11.302H240.686z"></path> <path style="fill:#5f006b;" d="M163.335,396.46c6.239,0,11.302,5.052,11.302,11.302c0,6.239-5.063,11.302-11.302,11.302H65.031 c-6.25,0-11.302-5.063-11.302-11.302c0-6.25,5.052-11.302,11.302-11.302H163.335z"></path> </g> </g></svg>
                    </div>
                    <div class="content">
                        <h3 class="name">
                            <?php if (isset($folders[0])) {
                                echo $folders[0];
                            } else {
                                echo "????";
                            } ?>
                        </h3>
                        <p class="size">(?? MB)</p>
                    </div>
                    <a class="button" href="#">Todo</a>
                </div>
                <div class="box">
                    <div class="icon">
                        <svg height="64px" width="64px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-51.2 -51.2 614.40 614.40" xml:space="preserve" fill="#000000" stroke="#000000" stroke-width="0.00512"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#FFFFFF;" d="M304.35,165.234c11.087,0,20.117,9.03,20.117,20.129c0,11.087-9.03,20.117-20.117,20.117 c-11.098,0-20.129-9.03-20.129-20.117C284.221,174.264,293.251,165.234,304.35,165.234z"></path> <path style="fill:#5f006b;" d="M512,67.461v377.078c0,12.466-10.138,22.604-22.604,22.604H22.604 C10.138,467.143,0,457.005,0,444.539V67.461c0-12.466,10.138-22.604,22.604-22.604h466.792C501.862,44.857,512,54.995,512,67.461z M489.396,444.539V67.461H22.604v377.078c0,0.011,0,0.011,0,0.011L489.396,444.539z"></path> <path style="fill:#FFFFFF;" d="M489.396,67.461v377.078L22.604,444.55c0,0,0,0,0-0.011V67.461H489.396z M458.271,359.684V109.877 c0-6.239-5.052-11.302-11.302-11.302H65.031c-6.25,0-11.302,5.063-11.302,11.302v249.807c0,6.239,5.052,11.302,11.302,11.302 h381.937C453.219,370.986,458.271,365.923,458.271,359.684z M251.988,407.762c0-6.25-5.052-11.302-11.302-11.302h-25.78 c-6.239,0-11.302,5.052-11.302,11.302c0,6.239,5.063,11.302,11.302,11.302h25.78C246.936,419.064,251.988,414.001,251.988,407.762z M174.637,407.762c0-6.25-5.063-11.302-11.302-11.302H65.031c-6.25,0-11.302,5.052-11.302,11.302 c0,6.239,5.052,11.302,11.302,11.302h98.304C169.574,419.064,174.637,414.001,174.637,407.762z"></path> <path style="fill:#5f006b;" d="M458.271,109.877v249.807c0,6.239-5.052,11.302-11.302,11.302H65.031 c-6.25,0-11.302-5.063-11.302-11.302V109.877c0-6.239,5.052-11.302,11.302-11.302h381.937 C453.219,98.575,458.271,103.639,458.271,109.877z M435.667,348.382v-4.566c-0.927-0.509-1.808-1.142-2.588-1.933l-76.627-76.616 l-35.432,35.432l47.683,47.683H435.667z M435.667,312.521V121.179H76.333v177.135l113.166-113.155 c4.408-4.408,11.573-4.408,15.981,0l99.559,99.559l43.422-43.422c4.419-4.408,11.573-4.408,15.981,0L435.667,312.521z M336.73,348.382L197.49,209.131L76.333,330.276v18.106H336.73z"></path> <path style="fill:#9B8CCC;" d="M435.667,343.816v4.566h-66.964l-47.683-47.683l35.432-35.432l76.627,76.616 C433.858,342.675,434.74,343.307,435.667,343.816z"></path> <path style="fill:#f099d9;" d="M435.667,121.179v191.342l-71.225-71.225c-4.408-4.408-11.562-4.408-15.981,0l-43.422,43.422 L205.48,185.16c-4.408-4.408-11.573-4.408-15.981,0L76.333,298.314V121.179H435.667z M347.071,185.363 c0-23.565-19.168-42.733-42.721-42.733c-23.565,0-42.733,19.168-42.733,42.733c0,23.553,19.168,42.721,42.733,42.721 C327.903,228.084,347.071,208.916,347.071,185.363z"></path> <polygon style="fill:#9B8CCC;" points="197.49,209.131 336.73,348.382 76.333,348.382 76.333,330.276 "></polygon> <g> <path style="fill:#5f006b;" d="M304.35,142.63c23.553,0,42.721,19.168,42.721,42.733c0,23.553-19.168,42.721-42.721,42.721 c-23.565,0-42.733-19.168-42.733-42.721C261.617,161.798,280.785,142.63,304.35,142.63z M324.467,185.363 c0-11.098-9.03-20.129-20.117-20.129c-11.098,0-20.129,9.03-20.129,20.129c0,11.087,9.03,20.117,20.129,20.117 C315.437,205.48,324.467,196.45,324.467,185.363z"></path> <path style="fill:#5f006b;" d="M240.686,396.46c6.25,0,11.302,5.052,11.302,11.302c0,6.239-5.052,11.302-11.302,11.302h-25.78 c-6.239,0-11.302-5.063-11.302-11.302c0-6.25,5.063-11.302,11.302-11.302H240.686z"></path> <path style="fill:#5f006b;" d="M163.335,396.46c6.239,0,11.302,5.052,11.302,11.302c0,6.239-5.063,11.302-11.302,11.302H65.031 c-6.25,0-11.302-5.063-11.302-11.302c0-6.25,5.052-11.302,11.302-11.302H163.335z"></path> </g> </g></svg>
                    </div>
                    <div class="content">
                        <h3 class="name">
                            <?php if (isset($folders[1])) {
                                echo $folders[1];
                            } else {
                                echo "????";
                            } ?>
                        </h3>
                        <p class="size">(?? MB)</p>
                    </div>
                    <a class="button" href="#">Todo</a>
                </div>
                <div class="box">
                    <div class="icon">
                        <svg height="64px" width="64px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-51.2 -51.2 614.40 614.40" xml:space="preserve" fill="#000000" stroke="#000000" stroke-width="0.00512"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#FFFFFF;" d="M304.35,165.234c11.087,0,20.117,9.03,20.117,20.129c0,11.087-9.03,20.117-20.117,20.117 c-11.098,0-20.129-9.03-20.129-20.117C284.221,174.264,293.251,165.234,304.35,165.234z"></path> <path style="fill:#5f006b;" d="M512,67.461v377.078c0,12.466-10.138,22.604-22.604,22.604H22.604 C10.138,467.143,0,457.005,0,444.539V67.461c0-12.466,10.138-22.604,22.604-22.604h466.792C501.862,44.857,512,54.995,512,67.461z M489.396,444.539V67.461H22.604v377.078c0,0.011,0,0.011,0,0.011L489.396,444.539z"></path> <path style="fill:#FFFFFF;" d="M489.396,67.461v377.078L22.604,444.55c0,0,0,0,0-0.011V67.461H489.396z M458.271,359.684V109.877 c0-6.239-5.052-11.302-11.302-11.302H65.031c-6.25,0-11.302,5.063-11.302,11.302v249.807c0,6.239,5.052,11.302,11.302,11.302 h381.937C453.219,370.986,458.271,365.923,458.271,359.684z M251.988,407.762c0-6.25-5.052-11.302-11.302-11.302h-25.78 c-6.239,0-11.302,5.052-11.302,11.302c0,6.239,5.063,11.302,11.302,11.302h25.78C246.936,419.064,251.988,414.001,251.988,407.762z M174.637,407.762c0-6.25-5.063-11.302-11.302-11.302H65.031c-6.25,0-11.302,5.052-11.302,11.302 c0,6.239,5.052,11.302,11.302,11.302h98.304C169.574,419.064,174.637,414.001,174.637,407.762z"></path> <path style="fill:#5f006b;" d="M458.271,109.877v249.807c0,6.239-5.052,11.302-11.302,11.302H65.031 c-6.25,0-11.302-5.063-11.302-11.302V109.877c0-6.239,5.052-11.302,11.302-11.302h381.937 C453.219,98.575,458.271,103.639,458.271,109.877z M435.667,348.382v-4.566c-0.927-0.509-1.808-1.142-2.588-1.933l-76.627-76.616 l-35.432,35.432l47.683,47.683H435.667z M435.667,312.521V121.179H76.333v177.135l113.166-113.155 c4.408-4.408,11.573-4.408,15.981,0l99.559,99.559l43.422-43.422c4.419-4.408,11.573-4.408,15.981,0L435.667,312.521z M336.73,348.382L197.49,209.131L76.333,330.276v18.106H336.73z"></path> <path style="fill:#9B8CCC;" d="M435.667,343.816v4.566h-66.964l-47.683-47.683l35.432-35.432l76.627,76.616 C433.858,342.675,434.74,343.307,435.667,343.816z"></path> <path style="fill:#f099d9;" d="M435.667,121.179v191.342l-71.225-71.225c-4.408-4.408-11.562-4.408-15.981,0l-43.422,43.422 L205.48,185.16c-4.408-4.408-11.573-4.408-15.981,0L76.333,298.314V121.179H435.667z M347.071,185.363 c0-23.565-19.168-42.733-42.721-42.733c-23.565,0-42.733,19.168-42.733,42.733c0,23.553,19.168,42.721,42.733,42.721 C327.903,228.084,347.071,208.916,347.071,185.363z"></path> <polygon style="fill:#9B8CCC;" points="197.49,209.131 336.73,348.382 76.333,348.382 76.333,330.276 "></polygon> <g> <path style="fill:#5f006b;" d="M304.35,142.63c23.553,0,42.721,19.168,42.721,42.733c0,23.553-19.168,42.721-42.721,42.721 c-23.565,0-42.733-19.168-42.733-42.721C261.617,161.798,280.785,142.63,304.35,142.63z M324.467,185.363 c0-11.098-9.03-20.129-20.117-20.129c-11.098,0-20.129,9.03-20.129,20.129c0,11.087,9.03,20.117,20.129,20.117 C315.437,205.48,324.467,196.45,324.467,185.363z"></path> <path style="fill:#5f006b;" d="M240.686,396.46c6.25,0,11.302,5.052,11.302,11.302c0,6.239-5.052,11.302-11.302,11.302h-25.78 c-6.239,0-11.302-5.063-11.302-11.302c0-6.25,5.063-11.302,11.302-11.302H240.686z"></path> <path style="fill:#5f006b;" d="M163.335,396.46c6.239,0,11.302,5.052,11.302,11.302c0,6.239-5.063,11.302-11.302,11.302H65.031 c-6.25,0-11.302-5.063-11.302-11.302c0-6.25,5.052-11.302,11.302-11.302H163.335z"></path> </g> </g></svg>
                    </div>
                    <div class="content">
                        <h3 class="name">
                            <?php if (isset($folders[2])) {
                                echo $folders[2];
                            } else {
                                echo "????";
                            } ?>
                        </h3>
                        <p class="size">(?? MB)</p>
                    </div>
                    <a class="button" href="#">Todo</a>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-container">
                <div class="box">
                    <div class="content">
                        <h3 class="name">Invites</h3>
                        <p class="size">0 Invites left</p>
                    </div>
                    <a class="button" href="#">Create</a>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
<script src="../assets/js/Dashboard/index.js"></script>