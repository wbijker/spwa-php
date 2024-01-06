<?php

class TextTemplateNode extends TemplateNode
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    function compare(TextTemplateNode $other, &$list): void
    {
        if ($this->value != $other->value) {
            $list[] = ['type' => UPDATE_TEXT, 'value' => $other->value, 'path' => $this->path];
        }
    }

    function resolve(ResolvedNode $parent): void
    {
        $parent->addChild(new TextData($this->value));
    }
}