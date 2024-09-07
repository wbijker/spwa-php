<?php

namespace Spwa\Dom;


use Spwa\Patch;
use Spwa\Template\Node;
use Spwa\Template\NodePath;

class HtmlText extends HtmlNode
{
    private string $text;

    public function __construct(Node $owner, NodePath $path, string $text)
    {
        parent::__construct($owner, $path);
        $this->text = $text;
    }

    function render(): string
    {
        return htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
    }

    static function compare(HtmlText $prev, HtmlText $next, array &$patches): void
    {
        if ($prev->text !== $next->text) {
            $patches[] = Patch::text($prev->path, $next->text);
        }
    }
}