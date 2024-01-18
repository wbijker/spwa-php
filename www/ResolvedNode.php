<?php

abstract class NodeData
{
    function render(ResolvedNode $owner)
    {
        foreach ($owner->children as $child) {
            $child->render();
        }
    }

    function serialize(ResolvedNode $owner): array
    {
        return [];
    }
}

class RootData extends NodeData
{
}

function flattenAttributes(array $attrs): array
{
    return $attrs;
}

class TagData extends NodeData
{
    function serialize(ResolvedNode $owner): array
    {
        return [
            'type' => 0,
            'tag' => $this->tag,
            'attributes' => $this->attributes['static'] ?? [],
            'events' => array_keys($this->attributes['events'] ?? []),
            'children' => array_map(fn($child) => $child->serialize(), $owner->children)
        ];
    }

    static private array $selfClosingTags = [
        "area",
        "base",
        "br",
        "col",
        "embed",
        "hr",
        "img",
        "input",
        "link",
        "meta",
        "param",
        "source",
        "track",
        "wbr"
    ];

    public string $tag;
    public array $attributes;

    /**
     * @param string $tag
     * @param array|null $attributes
     */
    public function __construct(string $tag, ?array $attributes)
    {
        $this->tag = $tag;
        $this->attributes = flattenAttributes($attributes ?? []);
    }

    function render(ResolvedNode $owner)
    {
        echo "<" . $this->tag;
        $list = $this->attributes['attrs'] ?? [];

        $events = $this->attributes['events'] ?? [];
        foreach ($events as $key => $value) {
            // events injected a function
            if (is_callable($value)) {
                $list['on' . $key] = "eventHandler('$key', event)";
            }
        }

        $bound = $this->attributes['bound'];
        if (isset($bound)) {
            $list['oninput'] = "handleInput(event, " . str_replace("\"", "'", json_encode($bound)) .")";
        }

        echo " " . implode(" ", array_map(fn($key) => "$key=\"$list[$key]\"", array_keys($list)));

        // self closing tags
        if (in_array($this->tag, self::$selfClosingTags)) {
            echo "/>";
            return;
        }

        echo ">";
        parent::render($owner);
        echo "</" . $this->tag . ">";
    }
}

class MarkerData extends NodeData
{
    function serialize(ResolvedNode $owner): array
    {
        return ['type' => 2, 'text' => $this->marker];
    }

    public string $marker;

    /**
     * @param string $marker
     */
    public function __construct(string $marker)
    {
        $this->marker = $marker;
    }

    function render(ResolvedNode $owner)
    {
        echo "<!--$this->marker-->";
    }
}

class TextData extends NodeData
{

    function serialize(ResolvedNode $owner): array
    {
        return ['type' => 1, 'text' => $this->text];
    }

    public string $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    function render(ResolvedNode $owner)
    {
        echo htmlentities($this->text);
    }
}

// Resolved node refer to a node that has been resolved to represent an actual node in the DOM
class ResolvedNode
{
    public ?ResolvedNode $parent = null;
    public array $path;
    public NodeData $data;
    /**
     * @var ResolvedNode[] $children
     */
    public array $children = [];


    function __construct(?ResolvedNode $parent, NodeData $data)
    {
        $this->parent = $parent;
        $this->path = $data instanceof RootData
            ? []
            : array_merge($parent == null ? [] : $parent->path, [count($parent->children)]);
        $this->data = $data;
    }


    /**
     * @param NodeData $data
     * @return ResolvedNode
     */
    function addChild(NodeData $data): ResolvedNode
    {
        $child = new ResolvedNode($this, $data);
        $this->children[] = $child;
        return $child;
    }

    public function render()
    {
        $this->data->render($this);
    }

    public function serialize(): array
    {
        return $this->data->serialize($this);
    }
}
