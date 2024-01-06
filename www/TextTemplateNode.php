<?php

class TextTemplateNode extends TemplateNode
{
    public string $value;
    private ResolvedNode $resolved;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    function compare(TextTemplateNode $other, &$list): void
    {
        if ($this->value != $other->value) {
            $list[] = ['type' => UPDATE_TEXT, 'value' => $other->value, 'path' => $this->resolved->path];
        }
    }

    function resolve(ResolvedNode $parent): void
    {
        $this->resolved = $parent->addChild(new TextData($this->value));
    }
}