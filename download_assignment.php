<?php
if (!isset($_GET['file'])) {
    http_response_code(400);
    echo 'File is required.';
    exit;
}

$file = basename($_GET['file']);
$baseDir = __DIR__ . '/Assignments';
$filePath = $baseDir . '/' . $file;

if (!is_file($filePath)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$contentType = finfo_file($finfo, $filePath);
finfo_close($finfo);

header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
