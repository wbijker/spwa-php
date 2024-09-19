<?php

namespace Spwa\Dom;


use Spwa\Patch;
use Spwa\Template\Node;
use Spwa\Template\NodePath;

function padText(string $text): string
{
    return empty($text)
        ? " "
        : $text;
}

class HtmlText extends HtmlNode
{
    private string $text;
    private bool $escape;

    public function __construct(Node $owner, NodePath $path, string $text, bool $escape = true)
    {
        parent::__construct($owner, $path);
        $this->text = $text;
        $this->escape = $escape;
    }

    function render(): string
    {
        // need to pad text to prevent text node from being removed by the browser
        return padText($this->escape ? htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8') : $this->text);
    }

    static function compare(HtmlText $prev, HtmlText $next, array &$patches): void
    {
        if ($prev->text !== $next->text) {
            $patches[] = Patch::text($prev->path, $next->text);
        }
    }
}