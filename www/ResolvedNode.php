<?php

class TagData
{
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
        $this->children = $children ?? [];
    }
}

class TextData
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
    // @var TagNode|TextData $data;
    public $data;
    /**
     * @var ResolvedNode[] $children
     */
    public array $children = [];


    function __construct(?ResolvedNode $parent, int $index, $data)
    {
        $this->parent = $parent;
        $this->index = $index;
        $this->path = $data == null
            ? []
            : array_merge($parent == null ? [] : $parent->path, [$index]);
        $this->data = $data;
    }


    /**
     * @param TagData|TextData $data
     * @return ResolvedNode
     */
    function addChild($data): ResolvedNode
    {
        $child = new ResolvedNode($this, count($this->children), $data);
        $this->children[] = $child;
        return $child;
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
        if ($this->data instanceof TextData) {
            echo htmlentities($this->data->text);
            //.json_encode($this->path);
            return;
        }
        if ($this->data instanceof TagData) {
            echo "<" . $this->data->tag;
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
            foreach ($this->children as $child) {
                $child->render();
            }
            echo "</".$this->data->tag.">";
            return;
        }

        // $data is null for the root
        foreach ($this->children as $child) {
            $child->render();
        }

    }
}
