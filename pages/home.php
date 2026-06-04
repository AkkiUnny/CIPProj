<?php

function readMeta($filePath) {

    $meta = [
        "Title" => basename($filePath),
        "Authors" => "",
        "Department" => "",
        "Adviser" => "",
        "Year" => ""
    ];

    $metaFile = $filePath . ".meta";

    if (file_exists($metaFile)) {
        $lines = file($metaFile, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $line) {
            [$key, $value] = explode("=", $line, 2);
            $meta[$key] = $value;
        }
    }

    return $meta;
}

$targetDir = dirname(__DIR__) . '/FileFolder/';
$fileEntries = [];
if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        if (str_ends_with($item, '.meta')) {
            continue;
        }

        $path = $targetDir . $item;
        if (!is_file($path)) {
            continue;
        }

        $meta = readMeta($path);
        $fileEntries[] = [
            'name' => $meta['Title'] ?: $item,
            'file' => $item,
            'uploaded' => filemtime($path),
        ];
    }

    usort($fileEntries, function ($a, $b) {
        return $b['uploaded'] <=> $a['uploaded'];
    });
}

?>

<div class="panel">
    <div class="panel-title">Welcome to the Library</div>
    <!-- <p>Recent uploads</p> -->

    <?php if (!empty($fileEntries)): ?>
        <table class="file-table">
            <thead>
                <tr>
                    <th>Research name</th>
                    <th>Date of upload</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fileEntries as $entry): ?>
                    <tr>
                        <td><a href="../FileFolder/<?= urlencode($entry['file']) ?>" download="<?= htmlspecialchars($entry['file']) ?>"><?= htmlspecialchars($entry['name']) ?></a></td>
                        <td><?= date('F j, Y, g:i A', $entry['uploaded']) ?></td>
                        <td>
                            <a class="download-btn" href="../FileFolder/<?= urlencode($entry['file']) ?>" download="<?= htmlspecialchars($entry['file']) ?>">Download</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No files found in FileFolder.</p>
    <?php endif; ?>

    <style>
        .file-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .file-table th,
        .file-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 13px;
        }

        .file-table th {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 11px;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.95);
        }

        .file-table tr:hover {
            background: rgba(16, 112, 168, 0.05);
        }

        .file-table a {
            color: var(--accent);
            text-decoration: none;
        }

        .file-table a:hover {
            text-decoration: underline;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 4px;
            border: none;
            background: rgba(16, 112, 168, 0.1);
            color: var(--accent);
            font-size: 12px;
            text-decoration: none;
            transition: background 0.15s;
        }

        .download-btn:hover {
            background: rgba(16, 112, 168, 0.18);
        }
    </style>
</div>
