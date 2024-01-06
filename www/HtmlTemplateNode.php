<?php

class HtmlTemplateNode extends TemplateNode
{
    public string $tag;
    public ?array $attributes;

    /**
     * @var (TemplateNode|string)[]
     */
    public ?array $children = [];

    /**
     * @param string $tag
     * @param array|null $attributes
     * @param null|TemplateNode[] $children
     */
    public function __construct(string $tag, ?array $attributes, ?array $children)
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    public function compare(HtmlTemplateNode $other, &$list)
    {
        for ($i = 0; $i < count($this->children); $i++) {
            compare($this->children[$i], $other->children[$i], $list);
        }
    }

    function resolve(ResolvedNode $parent): void
    {
        $entry = $parent->addChild(new TagData($this->tag, $this->attributes));
        foreach ($this->children as $index => $child) {
            $child->resolve($entry);
        }
    }
}
