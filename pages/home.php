<?php

$selectedDepartment = $_GET['dept'] ?? 'ALL';
$searchTerm = trim($_GET['search'] ?? '');
$selectedCategory = $_GET['category'] ?? 'All';

$departmentOptions = ['ALL', 'CBA', 'CENG', 'CCSS', 'CAS', 'CFAD', 'LAW', 'DENT', 'GRAD'];

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

//  set up the folder path
$targetDir = dirname(__DIR__) . '/FileFolder/';
$fileEntries = [];
$categoryOptions = [];

if (is_dir($targetDir)) {
    // loop through all files in the folder
    foreach (scandir($targetDir) as $item) {
        // skip hidden system files and metadata files
        if ($item === '.' || $item === '..' || str_ends_with($item, '.meta')) {
            continue;
        }

        $path = $targetDir . $item;
        
        if (is_file($path)) {
            $meta = readMeta($path);
            $deptCode = $meta['Department'] ?: 'Unknown';
            $category = $meta['Category'] ?: 'Research';
            $title = $meta['Title'] ?: $item;
            $authors = $meta['Authors'] ?: 'Unknown';

            // gather all available categories for the dropdown menu
            if ($category !== '') {
                $categoryOptions[$category] = true;
            }

            // check department filter
            if ($selectedDepartment !== 'ALL' && strtolower($deptCode) !== strtolower($selectedDepartment)) {
                continue; 
            }

            // check category filter
            if ($selectedCategory !== 'All' && strtolower($category) !== strtolower($selectedCategory)) {
                continue;
            }

            // check search bar filter
            if ($searchTerm !== '') {
                $searchFor = strtolower($searchTerm);
                $textToSearch = strtolower($title . ' ' . $authors . ' ' . $category);
                if (!str_contains($textToSearch, $searchFor)) {
                    continue; 
                }
            }

            // 4save matching files into our array
            $fileEntries[] = [
                'name' => $title,
                'authors' => $authors,
                'department' => $deptCode,
                'department_code' => $deptCode,
                'category' => $category,
                'file' => $item,
                'uploaded' => filemtime($path),
            ];
        }
    }

    // 5. sort the files so newest uploads appear first
    usort($fileEntries, function ($a, $b) {
        return $b['uploaded'] <=> $a['uploaded'];
    });
}

// sort the unique dropdown categories alphabetically
ksort($categoryOptions);
$categoryOptions = array_keys($categoryOptions);
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
            <option value="All" <?= $selectedCategory === 'All' ? 'selected' : '' ?>>All categories</option>
            <?php foreach ($categoryOptions as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="dept-button" type="submit">Filter</button>
    </form>

    <?php if ($searchTerm !== '' || $selectedCategory !== 'All'): ?>
        <p class="dept-note">Active filters: <?= htmlspecialchars(trim(($searchTerm !== '' ? 'Search: ' . $searchTerm : '') . ($selectedCategory !== 'All' ? ' Category: ' . $selectedCategory : ''), ' ')) ?></p>
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
