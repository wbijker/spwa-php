<?php

namespace Spwa\Dom;


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
        return $this->path->render() . htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
    }
}