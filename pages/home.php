<?php

$query = trim($_GET['q'] ?? '');

// readMeta() loads metadata from a .meta file for a given document.
function readMeta($filePath) {

    // Default metadata values if no .meta file exists.
    $meta = [
        "Title" => basename($filePath),
        "Authors" => "",
        "Department" => "",
        "Adviser" => "",
        "Year" => ""
    ];

    // The metadata file uses the uploaded filename plus .meta extension.
    $metaFile = $filePath . ".meta";

    if (file_exists($metaFile)) {
        $lines = file($metaFile, FILE_IGNORE_NEW_LINES);

        // Parse each line in the form key=value.
        foreach ($lines as $line) {
            [$key, $value] = explode("=", $line, 2);
            $meta[$key] = $value;
        }
    }

    return $meta;
}

// Build the list of uploaded files from the FileFolder directory.
$targetDir = dirname(__DIR__) . '/FileFolder/';
$fileEntries = [];
if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $item) {
        if ($item === '.' || $item === '..') {
            continue; // skip current / parent directory entries
        }

        if (str_ends_with($item, '.meta')) {
            continue; // skip metadata files themselves
        }

        $path = $targetDir . $item;
        if (!is_file($path)) {
            continue; // skip directories or invalid entries
        }

        $meta = readMeta($path);
        $fileEntries[] = [
            'name' => $meta['Title'] ?: $item,
            'authors' => $meta['Authors'] ?: 'Unknown',
            'department' => $meta['Department'] ?: 'Unknown',
            'category' => $meta['Category'] ?: 'Unknown',
            'file' => $item,
            'uploaded' => filemtime($path),
        ];
    }

    // Sort files newest first by upload timestamp.
    usort($fileEntries, function ($a, $b) {
        return $b['uploaded'] <=> $a['uploaded'];
    });

    if ($query !== '') {
        $query = mb_strtolower($query, 'UTF-8');
        $fileEntries = array_values(array_filter($fileEntries, function ($entry) use ($query) {
            $haystack = mb_strtolower($entry['name'] . ' ' . $entry['department'] . ' ' . $entry['file'], 'UTF-8');
            return str_contains($haystack, $query);
        }));
    }
}

?>

<div class="panel">
    <div class="panel-title">Welcome to the Library</div>

    <form class="library-search-form" method="get" action="dashboard.php?page=home">
        <input class="library-search-input" type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search by title, department, or file name" />
        <button class="library-search-button" type="submit">Search</button>
    </form>

    <?php if ($query !== ''): ?>
        <p class="search-summary">Showing results for “<?= htmlspecialchars($query) ?>”.</p>
    <?php endif; ?>

    <?php if (!empty($fileEntries)): ?>
        <!-- Table shows uploaded research files, department, upload date, and download link -->
        <table class="file-table">
            <thead>
                <tr>
                    <th>Research name</th>
                    <th>Authors</th>
                    <th>Department</th>
                    <th>Category</th>
                    <th>Date of upload</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fileEntries as $entry): ?>
                    <tr>
                        <td><a href="../FileFolder/<?= urlencode($entry['file']) ?>" download="<?= htmlspecialchars($entry['file']) ?>"><?= htmlspecialchars($entry['name']) ?></a></td>
                        <td><?= htmlspecialchars($entry['authors']) ?></td>
                        <td><?= htmlspecialchars($entry['department']) ?></td>
                        <td><?= htmlspecialchars($entry['category']) ?></td>
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
        .library-search-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .library-search-input {
            flex: 1 1 280px;
            min-width: 220px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.8);
            font-family: 'DM Mono', monospace;
            font-size: 12px;
        }

        .library-search-button {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--accent);
            color: #fff;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .library-search-button:hover {
            background: var(--accent-light);
        }

        .search-summary {
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 12px;
        }

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
