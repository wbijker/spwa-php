<?php

namespace Spwa;

use Spwa\Dom\HtmlNode;
use Spwa\Template\NodePath;

class Patch
{

    static function delete(NodePath $path): array
    {
        return self::pack(self::DELETE, $path, null);
    }

    static function replace(NodePath $path, HtmlNode $with): array
    {
        return self::pack(self::REPLACE, $path, $with->render());
    }

    static function insert(NodePath $path, HtmlNode $node): array
    {
        return self::pack(self::INSERT, $path, $node->render());
    }

    static function text(NodePath $path, string $text): array
    {
        return self::pack(self::TEXT, $path, $text);
    }

    private const DELETE = 0;
    private const REPLACE = 1;
    private const INSERT = 2;
    private const TEXT = 3;

    private static function pack(int $type, NodePath $path, $data): array
    {
        return [$type, $path->path, $data];
    }

}