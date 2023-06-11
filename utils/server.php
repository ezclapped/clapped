<?php
header('Content-Type: application/json');

function getFolderSize($folder) {
    $totalSize = 0;
    $files = scandir($folder);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $folder . '/' . $file;

        if (is_file($filePath)) {
            $totalSize += filesize($filePath);
        } elseif (is_dir($filePath)) {
            $totalSize += getFolderSize($filePath);
        }
    }

    return $totalSize;
}

$folderPath = '../media/';
$folderSize = getFolderSize($folderPath);

$response = array(
    'folderSize' => $folderSize
);

echo json_encode($response);
?>
