<?php

namespace Spwa\Nodes;

use Spwa\Html\MouseEvents;
use Spwa\Js\JS;

/*
 * DOM elements that falls into the flow content category in HTML specifications.
 * These elements accept global attributes and can be placed within the body of a document.
 * Attributes such as class, id, style, data, and mouse events are common to all flow content elements.
 */

function convertStyle($style): string
{
    if (is_string($style))
        return $style;

    if (is_array($style))
        return implode('; ', array_map(
                fn($key, $value) => "{$key}: {$value}",
                array_keys($style),
                $style
            )) . ';';

    return "";
}

abstract class HtmlContentNode extends HtmlNode
{
    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        mixed        $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
    )
    {
        $this->key = $key;

        $list = [
            "class" => $class,
            "id" => $id,
            "style" => convertStyle($style),
            "data" => $data
        ];
        $this->setAttrs($list);
        $mouse?->setEvents($this);
    }

}