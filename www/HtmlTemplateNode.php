<?php

class HtmlTemplateNode extends TemplateNode
{
    public string $tag;
    public array $attributes;

    /**
     * @var (TemplateNode|string)[]
     */
    public ?array $children = [];
    private ResolvedNode $resolved;
    /**
     * @var mixed
     */
    private $bound;

    /**
     * @param string $tag
     * @param array|null $attributes
     * @param null|TemplateNode[] $children
     */
    public function __construct(string $tag, ?array $attributes, ?array $children)
    {
        $this->tag = $tag;
        $this->attributes = $attributes ?? [];
        // create copy of bound
        // removing the potential reference to the model
        $this->bound = $this->attributes['bound'];
        $this->children = $children;
    }

    public function compareAttrs(?array $other, &$list)
    {
        $prev = $this->attributes['attrs'] ?? [];
        $next = $other['attrs'] ?? [];

        foreach ($prev as $key => $value) {
            if (!isset($next[$key])) {
                $list[] = ['type' => DELETE_ATTR, 'attr' => $key, 'path' => $this->resolved->path];
                continue;
            }
            if ($next[$key] != $value) {
                $list[] = ['type' => UPDATE_ATTR, 'attr' => $key, 'value' => $next[$key], 'path' => $this->resolved->path];
            }
        }
        foreach ($next as $key => $value) {
            if (!isset($prev[$key])) {
                // insert
                $list[] = ['type' => UPDATE_ATTR, 'attr' => $key, 'value' => $value, 'path' => $this->resolved->path];
            }
        }
    }

    public function compare(HtmlTemplateNode $other, &$list)
    {
        // compare resolved node' attributes
        if ($this->bound != $other->bound) {
            $list[] = [
                'type' => UPDATE_ATTR,
                'attr' => 'value',
                'value' => $other->bound,
                'path' => $this->resolved->path
            ];
        }

        $this->compareAttrs($other->attributes, $list);

        for ($i = 0; $i < count($this->children); $i++) {
            compare($this->children[$i], $other->children[$i], $list);
        }
    }

    function resolve(ResolvedNode $parent): void
    {
        $this->resolved = $parent->addChild(new TagData($this->tag, $this->attributes));
        foreach ($this->children as $child) {
            $child->resolve($this->resolved);
        }
    }
}
