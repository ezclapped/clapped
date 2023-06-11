<?php
function count_png_files($folder_path) {
    $count = 0;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder_path));
    foreach($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif', 'mp4'], true)) {
            $count++;
        }
    }

    return $count;
}

$folder_path = '../media/';
$file_count = count_png_files($folder_path);

header('Content-Type: application/json');
echo json_encode($file_count);
?>
