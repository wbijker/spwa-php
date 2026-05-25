<?php

return [
    // When true, the page emits the HMR long-poll that watches /hmr.php and
    // reloads on a detected source change. Set to false in production.
    'development' => true,

    // Sources watched for the HMR change signal and the /style.css cache-
    // buster (computed as <newest-mtime>:<file-count> by Spwa::sourceHash).
    'source' => [
        // Root of the walk. Relative paths resolve against this config file's
        // directory; '..' here means the project root (parent of www/).
        'dir' => '..',

        // Basenames to skip — matches both directory names (prunes the
        // subtree) and file names. Keep this list minimal: the more you
        // exclude, the more changes HMR / cache-busting will miss.
        'exclude' => ['vendor', 'node_modules', '.git'],
    ],
];
