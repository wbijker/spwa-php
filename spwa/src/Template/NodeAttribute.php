<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlElement;

abstract class NodeAttribute
{
    abstract function bind(HtmlElement $element, NodePath $path, PathState $state): void;
}

