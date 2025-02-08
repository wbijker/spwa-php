<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlNode;


class Div extends HtmlNode
{

    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        ?array       $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,
        ?array       $children = null)
    {
        $list = [
            "key" => $key,
            "class" => $class,
            "id" => $id,
            "style" => $style,
            "data" => $data
        ];
        if ($mouse != null) {
            $list = array_merge($list, $mouse->flatAttr($this));
        }
        $this->setAttrs($list);

        if ($mouse?->onClick != null) {
            $this->setEvents(["onClick" => $mouse->onClick]);
        }

        $this->children = $children ?? [];
    }

    function tag(): string
    {
        return "div";
    }
}

// have to be imported.
//function div(?string      $class = null,
//             ?string      $id = null,
//             ?array       $style = null,
//             ?array       $data = null,
//             ?MouseEvents $mouse = null,
//             ?array       $children = null): Div
//{
//    return new Div($class, $id, $style, $data, $mouse, $children);
//}
