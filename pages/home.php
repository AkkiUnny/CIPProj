<?php

function readMeta($filePath) {

    $meta = [
        "Title" => $filePath,
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
$files = [];
if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $item) {

    if ($item === '.' || $item === '..') {
        continue;
    }

    if (str_ends_with($item, '.meta')) {
        continue;
    }

    if (is_file($targetDir . $item)) {
        $files[] = $item;
    }
}
}

?>

<div class="library-shell">
    <aside class="library-sidebar">
        <h3>Filters</h3>
        <label class="filter-label" for="deptFilter">Department</label>
        <select id="deptFilter" class="library-search" name="department">
            <option value="">All Departments</option>
            <option value="CBA">CBA</option>
            <option value="CENG">CENG</option>
            <option value="CCSS">CCSS</option>
            <option value="CAS">CAS</option>
            <option value="CFAD">CFAD</option>
            <option value="LAW">LAW</option>
            <option value="DENT">DENT</option>
            <option value="GRAD">GRAD</option>
        </select>
        <input class="library-search" type="search" placeholder="Search title or author" />
    </aside>

    <section class="panel">
        <div class="panel-title">Welcome to the Library</div>
        <p>Recent</p>

        <?php if (!empty($files)): ?>
            <ul class="file-list">
                <?php foreach ($files as $file): ?>
                    <li><a href="../FileFolder/<?= urlencode($file) ?>"><?= htmlspecialchars($file) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No files found in FileFolder.</p>
        <?php endif; ?>
    </section>
</div>

    <!-- <div class="tenor-gif-embed" data-postid="8741534806163341101" data-share-method="host" data-aspect-ratio="1.55245" data-width="100%"><a href="https://tenor.com/view/spinning-banana-banana-donkey-kong-gif-8741534806163341101">Spinning Banana Donkey Kong Sticker</a>from <a href="https://tenor.com/search/spinning+banana-stickers">Spinning Banana Stickers</a></div> <script type="text/javascript" async src="https://tenor.com/embed.js"></script> -->