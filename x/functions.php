<?php
include "../config.php";


function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function rand_color()
{
    return "#" . str_pad(dechex(mt_rand(0, 0xffffff)), 6, "0", STR_PAD_LEFT);
}

function remove_exif($in, $out)
{
    $buffer_len = 4096;
    $fd_in = fopen($in, 'rb');
    $fd_out = fopen($out, 'wb');
    while ($buffer = fread($fd_in, $buffer_len)) {
        //  \xFF\xE1\xHH\xLLExif\x00\x00 - Exif
        //  \xFF\xE1\xHH\xLLhttp://      - XMP
        //  \xFF\xE2\xHH\xLLICC_PROFILE  - ICC
        //  \xFF\xED\xHH\xLLPhotoshop    - PH
        while (
            preg_match(
                '/\xFF[\xE1\xE2\xED\xEE](.)(.)(exif|photoshop|http:|icc_profile|adobe)/si',
                $buffer,
                $match,
                PREG_OFFSET_CAPTURE
            )
        ) {
            $len = ord($match[1][0]) * 256 + ord($match[2][0]);
            fwrite($fd_out, substr($buffer, 0, $match[0][1]));
            $filepos = $match[0][1] + 2 + $len - strlen($buffer);
            fseek($fd_in, $filepos, SEEK_CUR);
            $buffer = fread($fd_in, $buffer_len);
        }
        fwrite($fd_out, $buffer, strlen($buffer));
    }
    fclose($fd_out);
    fclose($fd_in);
}

function GeneratePage($length, $file, $userFolder)
{
    include '../config.php';
    $generatedSymbols = generateRandomString();
    $tmpName = $file['sharex']['tmp_name'];
    $fileSize = $file['sharex']['size'] >> 10;
    $originalName = $file['sharex']['name'];
    date_default_timezone_set('Europe/Vienna');
    $uploadTime = date('l\, F jS\, Y, H:i');
    $encEmojis = urlencode($generatedSymbols);
    $target_file = $file['sharex']['name'];
    $color = rand_color();

    $sql = "SELECT folder FROM users WHERE apikey = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_POST["key"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $folder);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $userFolder = "../media/" . $folder;


    $directoryPath = "{$userFolder}/{$generatedSymbols}";

    if (!file_exists($directoryPath)) {
        if (!mkdir($directoryPath, 0777, true)) {
            echo "Couldn't create directory.";
            return;
        }
        chmod($directoryPath, 0777);
    }

    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
    $originalFileType = $fileType;

    // Select the appropriate template file based on the file type
    if ($fileType == 'mp4' || $fileType == 'webm' || $fileType == 'ogg') {
        $templateFile = '../structures/video.html';
    } elseif (
        $fileType == 'mov' ||
        $fileType == 'wmv' ||
        $fileType == 'avi' ||
        $fileType == 'avchd' ||
        $fileType == 'mkv'
    ) {
        $templateFile = '../structures/others.html';
        $fileType = 'mp4'; // Set the file type to mp4 as HTML does not support playback of e.g. mov video format. $originalFileType still contains the original file type e.g. for download.
    } elseif (
        $fileType == 'jpeg' ||
        $fileType == 'png' ||
        $fileType == 'gif' ||
        $fileType == 'tiff' ||
        $fileType == 'jpg' ||
        $fileType == 'jfif'
    ) {
        $templateFile = '../structures/image.html';
    } else {
        echo "Unsupported file type.";
        return;
    }

    // Read the template file
    $template = file_get_contents($templateFile);

    // Replace the placeholders with dynamic values
    $template = str_replace('%color%', $color, $template);
    //replace the user folder
    $template = str_replace('%userfolder%', $folder, $template);
    $template = str_replace('%originalName%', $originalName, $template);
    $template = str_replace('%fileSize%', $fileSize, $template);
    $template = str_replace('%fileType%', $fileType, $template);
    $template = str_replace('%uploadTime%', $uploadTime, $template);
    $template = str_replace('%imgurl%', "{$directoryPath}/image.{$fileType}", $template);
    $template = str_replace('%originalType%', $originalFileType, $template);
    $template = str_replace('%generatedSymbols%', $generatedSymbols, $template);
    $template = str_replace('%author%', $author, $template);
    $template = str_replace('%mediafolder%', $mediafolder, $template);
    $template = str_replace('%sitename%', $sitename, $template);

    if (move_uploaded_file($tmpName, "{$directoryPath}/old.{$fileType}")) {
        remove_exif("{$directoryPath}/old.{$fileType}", "{$directoryPath}/image.{$fileType}");
        unlink("{$directoryPath}/old.{$fileType}");
        file_put_contents("{$directoryPath}/index.html", $template);
        chmod($directoryPath, 0755);
        return $generatedSymbols;
    } else {
        echo "Couldn't Move File";
        return;
    }
}
