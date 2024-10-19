<?php

namespace Spwa\Template;


use Spwa\Dom\HtmlElement;

class NodeAttributeText extends NodeAttribute
{
    public string $name;
    public ?string $value;

    public function __construct(string $name, ?string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    function bind(HtmlElement $element, NodePath $path, PathState $state): void
    {
        $element->addAttribute($this);
    }

    public function render(): string
    {
        return self::build($this->name, $this->value);
    }

    static function build(string $name, ?string $value): string
    {
        $escapedName = htmlspecialchars($name, ENT_COMPAT, 'UTF-8');
        if ($value == null)
            return $escapedName;

        $escapedValue = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
        return "$escapedName=\"$escapedValue\"";
    }
}

