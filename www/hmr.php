<?php

ignore_user_abort(false);
@set_time_limit(70);
while (ob_get_level() > 0) ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-store');
header('X-Accel-Buffering: no');

$root = dirname(__DIR__);
$deadline = microtime(true) + 60;

function spwa_hmr_snapshot(string $root): string
{
    $h = hash_init('sha1');
    $skip = ['vendor' => 1, 'node_modules' => 1, '.git' => 1];
    $dir = new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS);
    $filter = new RecursiveCallbackFilterIterator($dir, function ($f) use ($skip) {
        return $f->isFile()
            ? $f->getExtension() === 'php'
            : !isset($skip[$f->getFilename()]);
    });
    foreach (new RecursiveIteratorIterator($filter) as $f) {
        hash_update($h, $f->getPathname() . ':' . $f->getMTime() . "\n");
    }
    return hash_final($h);
}

$baseline = spwa_hmr_snapshot($root);

while (microtime(true) < $deadline) {
    clearstatcache();
    if (spwa_hmr_snapshot($root) !== $baseline) {
        echo json_encode(['changed' => true]);
        exit;
    }
    echo ' ';
    flush();
    if (connection_aborted()) {
        exit;
    }
    usleep(300_000);
}

echo json_encode(['changed' => false]);
