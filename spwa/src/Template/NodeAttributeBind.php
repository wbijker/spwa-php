<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlElement;
use Spwa\Js\JsFunction;
use Spwa\Js\JsVar;

class NodeAttributeBind extends NodeAttribute
{
    private string $value;

    public function __construct(string &$value)
    {
        $this->value = &$value;
    }

    function set($value)
    {
        $this->value = $value;
    }

    function bind(HtmlElement $element, NodePath $path, PathState $state): void
    {
        $state->set($path)->binding = $this;
        $function = new JsFunction("handleInput", new JsVar("event"));
        $element->addAttribute(new NodeAttributeText("onInput", $function->dump()));
        $element->addAttribute(new NodeAttributeText("value", $this->value));
    }

}