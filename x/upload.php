<?php

include "functions.php";
include "../config.php";

if (isset($_POST["key"])) {
    $apiKey = $_POST["key"];
    $sql = "SELECT folder FROM users WHERE apikey = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $apiKey);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $folder);
        mysqli_stmt_fetch($stmt);
        
        $userFolder = "../media/" . $folder;
        
        // Check if the user folder already exists
        if (!file_exists($userFolder)) {
            if (mkdir($userFolder, 0777, true)) {
                $result = GeneratePage($length, $_FILES, $userFolder);

                if ($result !== false) {
                    echo 'https://' . $_SERVER['SERVER_NAME'] . "/$mediafolder" . "/" . $folder . "/$result/";
                } else {
                    echo "Error: Couldn't Move File. Please make sure the uploaded file is supported.";
                }
            } else {
                echo "Error: Couldn't create user folder.";
            }
        } else {
            $result = GeneratePage($length, $_FILES, $userFolder);

            if ($result !== false) {
                echo 'https://' . $_SERVER['SERVER_NAME'] . "/$mediafolder" . "/" . $folder . "/$result/";
            } else {
                echo "Error: Couldn't Move File. Please make sure the uploaded file is supported.";
            }
        }
        
    } else {
        echo "Error: Invalid API Key.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Error: API Key not set.";
}

mysqli_close($link);
?>
