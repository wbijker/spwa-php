<?php

use Spwa\Spwa;

require 'vendor/autoload.php';

ignore_user_abort(false);
@set_time_limit(70);
while (ob_get_level() > 0) ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-store');
header('X-Accel-Buffering: no');

$root = dirname(__DIR__);
$deadline = microtime(true) + 60;

$baseline = Spwa::sourceHash($root);

while (microtime(true) < $deadline) {
    clearstatcache();
    if (Spwa::sourceHash($root) !== $baseline) {
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
