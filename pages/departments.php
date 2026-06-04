<?php

$selectedDepartment = $_GET['dept'] ?? 'ALL';
$searchTerm = trim($_GET['search'] ?? '');
$selectedCategory = $_GET['category'] ?? 'All';

$departmentOptions = [
    'ALL',
    'CBA',
    'CENG',
    'CCSS',
    'CAS',
    'CFAD',
    'LAW',
    'DENT',
    'GRAD',
];

function readMeta($filePath) {
    $meta = [
        'Title' => basename($filePath),
        'Authors' => '',
        'Department' => '',
        'Category' => '',
        'Year' => ''
    ];

    $metaFile = $filePath . '.meta';

    if (file_exists($metaFile)) {
        $lines = file($metaFile, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $line) {
            [$key, $value] = explode('=', $line, 2);
            $meta[$key] = $value;
        }
    }

    return $meta;
}

$targetDir = dirname(__DIR__) . '/FileFolder/';
$fileEntries = [];

if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $item) {
        if ($item === '.' || $item === '..' || str_ends_with($item, '.meta')) {
            continue;
        }

        $path = $targetDir . $item;
        if (!is_file($path)) {
            continue;
        }

        $meta = readMeta($path);
        $departmentCode = $meta['Department'] ?: 'Unknown';
        $fileEntries[] = [
            'name' => $meta['Title'] ?: $item,
            'authors' => $meta['Authors'] ?: 'Unknown',
            'department' => $departmentCode,
            'department_code' => $departmentCode,
            'category' => $meta['Category'] ?: 'Research',
            'file' => $item,
            'uploaded' => filemtime($path),
        ];
    }

    usort($fileEntries, function ($a, $b) {
        return $b['uploaded'] <=> $a['uploaded'];
    });
}

$allEntries = $fileEntries;

if ($selectedDepartment !== 'ALL') {
    $fileEntries = array_values(array_filter($allEntries, function ($entry) use ($selectedDepartment) {
        return strcasecmp($entry['department_code'], $selectedDepartment) === 0;
    }));
} else {
    $fileEntries = $allEntries;
}

if ($searchTerm !== '') {
    $needle = mb_strtolower($searchTerm, 'UTF-8');
    $fileEntries = array_values(array_filter($fileEntries, function ($entry) use ($needle) {
        $haystack = mb_strtolower($entry['name'] . ' ' . $entry['authors'] . ' ' . $entry['category'], 'UTF-8');
        return str_contains($haystack, $needle);
    }));
}

if ($selectedCategory !== 'All') {
    $fileEntries = array_values(array_filter($fileEntries, function ($entry) use ($selectedCategory) {
        return strcasecmp($entry['category'], $selectedCategory) === 0;
    }));
}

$categoryOptions = [
    'Case study',
    'Survey',
    'Literature review',
    'Experimental',
    'Theoretical',
    'Design project',
];

?>

<div class="panel">
    <div class="panel-title">Departments</div>
    <p>Browse research papers by department.</p>

    <div class="dept-tabs">
        <?php foreach ($departmentOptions as $code): ?>
            <a class="dept-tab <?= $selectedDepartment === $code ? 'active' : '' ?>" href="dashboard.php?page=departments&dept=<?= urlencode($code) ?>"><?= htmlspecialchars($code) ?></a>
        <?php endforeach; ?>
    </div>

    <p class="dept-note">Showing research papers for <strong><?= htmlspecialchars($selectedDepartment) ?></strong>.</p>

    <form class="dept-controls" method="get" action="dashboard.php">
        <input type="hidden" name="page" value="departments" />
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
                        <td><a class="download-btn" href="../FileFolder/<?= urlencode($entry['file']) ?>" download="<?= htmlspecialchars($entry['file']) ?>">Download</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No research papers found for this department.</p>
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
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid var(--border);
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