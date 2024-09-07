<?php

ini_set('memory_limit', '24M');

$filePath = __DIR__ . '/photos.zip';

if (!file_exists($filePath)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File not found']);
    exit;
}

if (ob_get_level()) {
    ob_end_clean(); // shouldn't be used if the file isn't large
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

$file = fopen($filePath, 'rb');
if ($file) {
    while (!feof($file)) {
        echo fread($file, 10240 * 1024);
        flush();
    }
    fclose($file);
}
