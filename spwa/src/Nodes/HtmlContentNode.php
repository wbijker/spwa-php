<?php

namespace Spwa\Nodes;

use Spwa\Html\MouseEvents;
use Spwa\Js\JS;

/*
 * DOM elements that falls into the flow content category in HTML specifications.
 * These elements accept global attributes and can be placed within the body of a document.
 * Attributes such as class, id, style, data, and mouse events are common to all flow content elements.
 */

abstract class HtmlContentNode extends HtmlNode
{
    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        ?array       $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
    )
    {
        $this->key = $key;

        $list = [
            "class" => $class,
            "id" => $id,
            "style" => $style,
            "data" => $data
        ];
        $this->setAttrs($list);

        if ($mouse?->onClick != null) {
            $this->setEvents(["onClick" => $mouse->onClick]);
        }
    }

}