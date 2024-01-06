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

class TagData extends NodeData
{
    function serialize(ResolvedNode $owner): array
    {
        return [
            'type' => 0,
            'tag' => $this->tag,
            'attributes' => $this->attributes,
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
        $this->attributes = $attributes ?? [];
    }

    function render(ResolvedNode $owner)
    {
        echo "<" . $this->tag;
        $this->attributes['path'] = json_encode($owner->path);

        foreach ($this->attributes as $key => $value) {

            // check for event handlers
            if ($key == 'click') {
                // click value injected a function
                if (is_callable($value)) {
                    echo " onclick='eventHandler(event, " . json_encode($owner->path) . ")'";
                    continue;
                }
                continue;
            }

            if ($key == 'bound') {
                echo " oninput=\"handleInput(event, '$value')\"";
                continue;
            }

            echo " $key=\"$value\"";
        }

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
