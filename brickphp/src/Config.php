<?php

namespace BrickPHP;

/**
 * Project configuration as static members. Set the values here (or assign
 * the statics during bootstrap, before Brick::run) and the framework reads
 * them via Config::$member. Defaults are production-safe (development off).
 */
class Config
{
    /**
     * When true the page emits the HMR long-poll, the ctrl+click
     * open-in-editor inspector, the "w" wireframe keybind, and the
     * ?wireframe view. Set false in production.
     */
    public static bool $development = true;

    /**
     * Root of hmr.php's source-change walk. Relative paths resolve
     * against the entry script's directory; '..' means the project root.
     */
    public static string $sourceDir = '..';

    /**
     * Basenames pruned from that walk — matches directory and file names.
     * @var string[]
     */
    public static array $sourceExclude = ['vendor', 'node_modules', '.git'];

    /**
     * Editor jump-to-source URL template for ctrl+click, interpolated
     * with {file}, {line}, {col}. Empty disables it. Examples:
     *   phpstorm://open?file={file}&line={line}
     *   vscode://file/{file}:{line}:{col}
     *   cursor://file/{file}:{line}:{col}
     */
    public static string $editorUrl = 'phpstorm://open?file={file}&line={line}';

    /**
     * Host-side absolute path of this project, used to rewrite
     * server-captured paths so the editor link resolves when PHP runs
     * in a container/VM. Null = no rewrite (auto-detected server root).
     */
    public static ?string $editorHostRoot = '/Users/willembijker/projects/brickphp';
}
