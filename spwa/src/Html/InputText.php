<?php

namespace Spwa\Html;

use Spwa\Nodes\HtmlContentNode;
use Spwa\Nodes\HtmlNode;

class InputText extends HtmlContentNode
{


    public function __construct(
        mixed        $key = null,
        ?string      $class = null,
        ?string      $id = null,
        ?array       $style = null,
        ?array       $data = null,
        ?MouseEvents $mouse = null,

        ?string      $value = null,
        ?onChange    $onChange = null,
        ?onInput     $onInput = null,
    )
    {
        parent::__construct($key, $class, $id, $style, $data, $mouse);
        $this->attrs['value'] = $value;
    }

    function tag(): string
    {
        return "input";
    }
}