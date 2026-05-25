<?php

use Spwa\Spwa;

require 'vendor/autoload.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');
header('X-Accel-Buffering: no');

// Production short-circuit. The frontend already gates the poll on
// window.__SPWA_DEV, but a direct hit (curl, stale tab) shouldn't burn
// 60s of worker time when HMR is off.
if (!Spwa::isDevelopment()) {
    echo json_encode(['changed' => false]);
    exit;
}

ignore_user_abort(false);
@set_time_limit(70);
while (ob_get_level() > 0) ob_end_clean();

$deadline = microtime(true) + 60;
$baseline = Spwa::sourceHash();

while (microtime(true) < $deadline) {
    clearstatcache();
    if (Spwa::sourceHash() !== $baseline) {
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
