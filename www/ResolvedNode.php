<?php

class ResolvedNodeHtml
{
    public string $tag;
    public array $attributes;
    /**
     * @var (string|ResolvedNode)[]
     */
    public array $children = [];

    /**
     * @param string $tag
     * @param array|null $attributes
     * @param ResolvedNode[]|string[] $children
     */
    public function __construct(string $tag, ?array $attributes, ?array $children)
    {
        $this->tag = $tag;
        $this->attributes = $attributes ?? [];
        $this->children = $children ?? [];
    }


}

class ResolvedNodeText
{
    public string $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }


}

// Resolved node refer to a node that has been resolved to represent an actual node in the DOM
class ResolvedNode
{
    public ?ResolvedNode $parent = null;
    // the dom position relative to the parent
    public int $index;
    public array $path;

    // @var ResolvedNodeHtml|ResolvedNodeText $data;
    public $data;

    public function __construct(?ResolvedNode $parent, int $index, $data)
    {
        $this->parent = $parent;
        $this->index = $index;
        $this->path = array_merge($parent == null ? [] : $parent->path, [$index]);
        $this->data = $data;
    }

    public static function createHtml(?ResolvedNode $parent, int $index, string $tag, ?array $attributes, array $children): ResolvedNode
    {
        return new ResolvedNode($parent, $index, new ResolvedNodeHtml($tag, $attributes, $children));
    }

    public static function createText(?ResolvedNode $parent, int $index, string $text): ResolvedNode
    {
        return new ResolvedNode($parent, $index, new ResolvedNodeText($text));
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

    public function render()
    {
        if ($this->data instanceof ResolvedNodeText) {
            echo htmlentities($this->data->text);
            return;
        }
        echo "<$this->data->tag";
        $this->data->attributes['path'] = json_encode($this->path);

        foreach ($this->data->attributes as $key => $value) {

            // check for event handlers
            if ($key == 'click') {
                // click value injected a function
                if (is_callable($value)) {
                    echo " onclick='eventHandler(event, " . json_encode($this->path) . ")'";
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
        if (in_array($this->data->tag, self::$selfClosingTags)) {
            echo "/>";
            return;
        }

        echo ">";
        foreach ($this->data->children as $child) {
            $child->render();
        }
        echo "</$this->data->tag>";
    }
}
