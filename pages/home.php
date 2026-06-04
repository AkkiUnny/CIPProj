<?php
$targetDir = dirname(__DIR__) . '/FileFolder/';
$files = [];
if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        if (is_file($targetDir . $item)) {
            $files[] = $item;
        }
    }
}
?>

<div class="panel">
    <div class="panel-title">Welcome to Blue Archive</div>
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

    <!-- <div class="tenor-gif-embed" data-postid="8741534806163341101" data-share-method="host" data-aspect-ratio="1.55245" data-width="100%"><a href="https://tenor.com/view/spinning-banana-banana-donkey-kong-gif-8741534806163341101">Spinning Banana Donkey Kong Sticker</a>from <a href="https://tenor.com/search/spinning+banana-stickers">Spinning Banana Stickers</a></div> <script type="text/javascript" async src="https://tenor.com/embed.js"></script> -->
</div>