<?php

namespace Spwa\Template;

use Spwa\Dom\HtmlElement;
use Spwa\Js\JsFunction;
use Spwa\Js\JsLiteral;

class NodeAttributeEvent extends NodeAttribute
{
    public string $name;
    /**
     * @var callable
     */
    public $handler;

    public function __construct(string $name, callable $handler)
    {
        $this->name = $name;
        $this->handler = $handler;
    }

    function bind(HtmlElement $element, NodePath $path, PathState $state): void
    {
        $state->get($path)->addEvent($this->name, $this->handler);
        $function = new JsFunction("handleEvent", $this->name, new JsLiteral("event"));
        $element->addAttribute(new NodeAttributeText("on" . $this->name, $function->dump()));
    }

}