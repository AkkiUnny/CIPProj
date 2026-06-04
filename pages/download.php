<?php
$dept = $_GET['dept'] ?? '';
$file = $_GET['file'] ?? '';

// Sanitize — no directory traversal
$dept_safe = preg_replace('/[^a-zA-Z0-9_]/', '_', $dept);
$file_safe = basename($file);

if (!$dept_safe || !$file_safe) {
    http_response_code(400);
    echo 'Invalid request.';
    exit;
}

$path = __DIR__ . '/../uploads/' . $dept_safe . '/' . $file_safe;

if (!file_exists($path)) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>File Not Found</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 3rem; text-align: center; background: #f5f3ee; color: #1a1a1a; }
            a { color: #2c4a7c; }
        </style>
    </head>
    <body>
        <h2>File not found.</h2>
        <p>The requested file does not exist or has been removed.</p>
        <a href="index.php">Return to Archive</a>
    </body>
    </html>
    <?php
    exit;
}

// Serve the file
$mime_map = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];
$ext  = strtolower(pathinfo($file_safe, PATHINFO_EXTENSION));
$mime = $mime_map[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $file_safe . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
