<?php

return [
    // When true, the page emits the HMR long-poll that watches /hmr.php and
    // reloads on a detected source change. Also gates the ctrl+click "open
    // in editor" inspector, the "w" wireframe-toggle keybind, and the
    // ?wireframe=true view itself. Set to false in production.
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

    // Editor jump-to-source. In dev mode, ctrl/cmd-clicking any element
    // navigates to the URL produced by interpolating this template with
    // {file}, {line}, {col}. Every major editor registers a custom URL
    // scheme the OS hands off to the app.
    //
    //   PHPStorm/IntelliJ : phpstorm://open?file={file}&line={line}
    //                       idea://open?file={file}&line={line}
    //   VS Code           : vscode://file/{file}:{line}:{col}
    //   VS Code Insiders  : vscode-insiders://file/{file}:{line}:{col}
    //   Cursor            : cursor://file/{file}:{line}:{col}
    //   Sublime Text      : subl://open?url=file://{file}&line={line}
    //   TextMate          : txmt://open/?url=file://{file}&line={line}
    //   Atom              : atom://core/open/file?filename={file}&line={line}
    //   Emacs (emacsclient
    //     via emacs://)   : emacs://open?file={file}&line={line}
    //   Nova              : nova://open?path={file}&line={line}
    //
    // First click may pop a "Open in <editor>?" prompt that you can
    // remember. JetBrains needs URL handlers enabled in
    // Settings → Tools → URL Handlers.
    'editor' => [
        'url' => 'phpstorm://open?file={file}&line={line}',

        // Absolute host-side path of this project on your machine. Used to
        // rewrite the server-captured paths so the editor jumps to the
        // right file when PHP runs inside a container or VM. In a plain
        // local setup this matches the auto-detected server path and the
        // rewrite is a no-op — leave it as the project root either way.
        'host_root' => '/Users/willembijker/projects/spwa-php',
    ],
];
