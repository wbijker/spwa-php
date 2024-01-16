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
     * @param string $tag
     * @param array|null $attributes
     * @param null|TemplateNode[] $children
     */
    public function __construct(string $tag, ?array $attributes, ?array $children)
    {
        $this->tag = $tag;
        $this->attributes = $attributes ?? [];
        $this->children = $children;
    }

    public function compareAttrs(?array $other, &$list)
    {
        foreach ($this->attributes as $key => $value) {

            if ($key === 'bound') {
                $list[] = ['type' => 6, 'path' => $this->resolved->path];
                continue;
            }
            if ($key === 'click') {
                continue;
            }

            if (!isset($other[$key])) {
                $list[] = ['type' => DELETE_ATTR, 'attr' => $key, 'path' => $this->resolved->path];
                continue;
            }
            if ($other[$key] != $value) {
                // change
                $list[] = ['type' => UPDATE_ATTR, 'attr' => $key, 'value' => $value, 'path' => $this->resolved->path];
            }
        }
        foreach ($other as $key => $value) {
            if (!isset($this->attributes[$key])) {
                // insert
                $list[] = ['type' => UPDATE_ATTR, 'attr' => $key, 'value' => $value, 'path' => $this->resolved->path];
            }
        }
    }

    public function compare(HtmlTemplateNode $other, &$list)
    {
//        $this->compareAttrs($other->attributes, $list);

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
