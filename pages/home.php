<?php

$selectedDepartment = $_GET['dept'] ?? 'ALL';
$searchTerm = trim($_GET['search'] ?? '');
$selectedCategory = $_GET['category'] ?? '';

$departmentOptions = ['ALL', 'CBA', 'CENG', 'CCSS', 'CAS', 'CFAD', 'LAW', 'DENT', 'GRAD'];
$categoryOptions = [];

// readMeta() loads metadata from a .meta file for a given document.
function readMeta($filePath) {

    // Default metadata values if no .meta file exists.
    $meta = [
        "Title" => basename($filePath),
        "Authors" => "",
        "Department" => "",
        "Adviser" => "",
        "Year" => "",
        "Category" => ""
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

// scanAndBuildFileEntries() scans the FileFolder directory and builds the initial file list.
// Collects metadata for each file and tracks available categories.
function scanAndBuildFileEntries(&$categoryOptions) {
    $targetDir = dirname(__DIR__) . '/FileFolder/';
    $fileEntries = [];

    if (is_dir($targetDir)) {
        foreach (scandir($targetDir) as $item) {
            if ($item === '.' || $item === '..' || str_ends_with($item, '.meta')) {
                continue;
            }

            $path = $targetDir . $item;
            
            if (is_file($path)) {
                $meta = readMeta($path);
                $departmentCode = $meta['Department'] ?: 'Unknown';
                $categoryValue = $meta['Category'] ?: 'Research';

                if (!in_array($categoryValue, $categoryOptions, true)) {
                    $categoryOptions[] = $categoryValue;
                }

                $fileEntries[] = [
                    'name' => $meta['Title'] ?: $item,
                    'authors' => $meta['Authors'] ?: 'Unknown',
                    'department' => $departmentCode,
                    'department_code' => $departmentCode,
                    'category' => $categoryValue,
                    'file' => $item,
                    'uploaded' => filemtime($path),
                ];
            }
        }
    }

    return $fileEntries;
}

// sortFilesByDate() sorts files by upload timestamp, newest first.
function sortFilesByDate(&$fileEntries) {
    usort($fileEntries, function ($a, $b) {
        return $b['uploaded'] <=> $a['uploaded'];
    });
}

// filterByDepartment() removes entries that don't match the selected department.
function filterByDepartment(&$fileEntries, $selectedDepartment) {
    if ($selectedDepartment !== 'ALL') {
        $fileEntries = array_values(array_filter($fileEntries, function ($entry) use ($selectedDepartment) {
            return $entry['department_code'] === $selectedDepartment;
        }));
    }
}

// filterByCategory() removes entries that don't match the selected category.
function filterByCategory(&$fileEntries, $selectedCategory) {
    if ($selectedCategory !== '') {
        $fileEntries = array_values(array_filter($fileEntries, function ($entry) use ($selectedCategory) {
            return $entry['category'] === $selectedCategory;
        }));
    }
}

// filterBySearchTerm() searches title, authors, department, category, and filename.
function filterBySearchTerm(&$fileEntries, $searchTerm) {
    if ($searchTerm !== '') {
        $searchTerm = mb_strtolower($searchTerm, 'UTF-8');
        $fileEntries = array_values(array_filter($fileEntries, function ($entry) use ($searchTerm) {
            $haystack = mb_strtolower(
                $entry['name'] . ' ' . 
                $entry['authors'] . ' ' . 
                $entry['department'] . ' ' . 
                $entry['category'] . ' ' . 
                $entry['file'], 
                'UTF-8'
            );
            return str_contains($haystack, $searchTerm);
        }));
    }
}

// Build and filter the file list.
$fileEntries = scanAndBuildFileEntries($categoryOptions);
sortFilesByDate($fileEntries);
filterByDepartment($fileEntries, $selectedDepartment);
filterByCategory($fileEntries, $selectedCategory);
filterBySearchTerm($fileEntries, $searchTerm);

$categoryOptions = array_unique($categoryOptions);
sort($categoryOptions, SORT_STRING);

?>

<div class="panel">
    <div class="panel-title">Library</div>
    <p>Browse research papers by department with category filtering.</p>

    <div class="dept-tabs">
        <?php foreach ($departmentOptions as $code): ?>
            <a class="dept-tab <?= $selectedDepartment === $code ? 'active' : '' ?>"
               href="dashboard.php?page=home&dept=<?= urlencode($code) ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>"><?= htmlspecialchars($code) ?></a>
        <?php endforeach; ?>
    </div>

    <form class="dept-controls" method="get" action="dashboard.php">
        <input type="hidden" name="page" value="home" />
        <input type="hidden" name="dept" value="<?= htmlspecialchars($selectedDepartment) ?>" />
        <input class="dept-search" type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search title, author, or category" />
        <select class="dept-filter" name="category">
            <option value="">All</option>
            <?php foreach ($categoryOptions as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="dept-button" type="submit">Filter</button>
    </form>

    <?php if ($searchTerm !== '' || $selectedCategory !== ''): ?>
        <p class="dept-note">Active filters: <?= htmlspecialchars(trim(($searchTerm !== '' ? 'Search: ' . $searchTerm : '') . ($selectedCategory !== '' ? ' Category: ' . $selectedCategory : ''), ' ')) ?></p>
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
        .dept-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 14px 0;
        }

        .dept-tab {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 999px;
            text-decoration: none;
            color: var(--text);
            background: rgba(255, 255, 255, 0.65);
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .dept-tab.active,
        .dept-tab:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .dept-note {
            color: var(--text-muted);
            font-size: 12px;
            margin-bottom: 10px;
        }

        .dept-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .dept-search,
        .dept-filter {
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.8);
            font-family: 'DM Mono', monospace;
            font-size: 12px;
        }

        .dept-search {
            flex: 1 1 260px;
            min-width: 220px;
        }

        .dept-filter {
            min-width: 180px;
        }

        .dept-button {
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

        .dept-button:hover {
            background: var(--accent-light);
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
    