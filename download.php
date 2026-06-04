<?php

// A secure download proxy for files stored in FileFolder/.
// Accepts a file query parameter and forces the file to be downloaded.

$targetDir = __DIR__ . '/FileFolder/';
$fileName = $_GET['file'] ?? '';
$fileName = trim($fileName);

if ($fileName === '') {
    http_response_code(400);
    echo 'Missing file name.';
    exit;
}

// Prevent directory traversal by resolving the real path and confirming it is inside FileFolder.
$requestedPath = realpath($targetDir . $fileName);
$rootPath = realpath($targetDir);

if ($requestedPath === false || $rootPath === false || strncmp($requestedPath, $rootPath, strlen($rootPath)) !== 0) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

if (!is_file($requestedPath) || !is_readable($requestedPath)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

$downloadName = basename($requestedPath);
$contentType = mime_content_type($requestedPath) ?: 'application/octet-stream';

header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($requestedPath));

// Clean output buffers and stream the file.
if (ob_get_level()) {
    ob_end_clean();
}

readfile($requestedPath);
exit;
