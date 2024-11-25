<?php


require 'vendor/autoload.php';

class PatchBuilder
{
    function replace(Node $old, Node $new): void
    {
        echo "Replace " . $old->pathStr() . " with " . $new->pathStr() . "\n";
    }

    function text(Node $node, string $text): void
    {
        echo "Text changed at " . $node->pathStr() . " to " . $text . "\n";
    }

    function updateAttr(Node $node, string $attr, ?string $value): void
    {
        echo "Attribute changed at " . $node->pathStr() . " to " . $attr . " = " . $value . "\n";
    }

    function delete()
    {

    }

    function insert()
    {

    }
}

class StateManager
{
    // key dictionary contain state
    private array $state = [];

    function unserialize($data): void
    {
        if ($data == null)
            return;

        $this->state = json_decode($data, true);
    }

    function serialize(): string
    {
        return json_encode($this->state);
    }

    function restoreState(string $key)
    {
        return $this->state[$key] ?? null;
    }

    function saveState(string $key, $state): void
    {
        $this->state[$key] = $state;
    }
}

class PathInfo
{
    public function __construct(public int $domIndex, public string|int|bool|null $key)
    {
    }

    function set(Node $node, ?Node $parent, bool $add): void
    {
        // root node
        if ($parent == null) {
            $node->path = [];
            $node->key = [$this->key];
            return;
        }
        // real dom
        if ($add) {
            $node->path = array_merge($parent->path, [$this->domIndex]);
            $node->key = array_merge($parent->key, [$this->key]);
            return;
        }
        // node is a virtual node only, such as ForNode, IfNode
        $node->path = $parent->path;
        $node->key = array_merge($parent->key, [$this->key]);
    }
}

abstract class Node
{
    public array $path = [];
    public array $key = [];

    function pathStr(): string
    {
        return implode("|", $this->path);
    }

    function keyStr(): string
    {
        return implode("|", $this->key);
    }

    abstract function compare(Node $node, PatchBuilder $patch): void;

    abstract function renderHtml(): string;

    abstract function initialize(?Node $parent, PathInfo $path, StateManager $manager): void;

    abstract function finalize(StateManager $manager): void;
}


class HtmlText extends Node
{
    public function __construct(private string $text)
    {
    }

    function compare(Node $node, PatchBuilder $patch): void
    {
        if (!$node instanceof HtmlText) {
            // nodes are not the same replace whole node
            $patch->replace($this, $node);
            return;
        }
        if ($this->text != $node->text) {
            $patch->text($this, $node->text);
        }
    }

    function renderHtml(): string
    {
        return '(' . $this->pathStr() . ') ' . $this->text;
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, true);
    }

    function finalize(StateManager $manager): void
    {
    }
}

class ForNode extends Node
{
    /**
     * @template T
     * @param array<T> $list Array of items of type T
     * @param callable(T $item, int $index): (string|int|bool) $keyCallback A callback that generates a key for each item
     * @param callable(T $item, int $index): Node $renderCallback A callback that renders a Node for each item
     */
    public function __construct(private array $list, private $keyCallback, private $renderCallback)
    {
    }

    function compare(Node $node, PatchBuilder $patch): void
    {
        // TODO: Implement compare() method.
    }

    function renderHtml(): string
    {
        $list = array_map(fn($node) => $node[1]->renderHtml(), $this->getNode());
        return implode("", $list);
    }

    // [$key string|int|bool, Node $node][]
    private ?array $node = null;

    public function getNode(): array
    {
        if ($this->node != null)
            return $this->node;

        $this->node = [];
        foreach ($this->list as $i => $item) {
            $key = ($this->keyCallback)($item, $i);
            $node = ($this->renderCallback)($item, $i);
            $this->node[] = [$key, $node];
        }
        return $this->node;
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, false);

        foreach ($this->getNode() as $index => [$key, $node]) {
            $node->initialize($this, new PathInfo($path->domIndex + $index, $key), $manager);
        }
    }

    function finalize(StateManager $manager): void
    {
    }
}

abstract class HtmlNode extends Node
{
    function compare(Node $node, PatchBuilder $patch): void
    {
        if (!$node instanceof HtmlNode) {
            $patch->replace($this, $node);
            return;
        }

        if (count($this->children) != count($node->children)) {
            $patch->replace($this, $node);
            return;
        }

        // compare attributes
        foreach ($this->attrs as $key => $value) {
            $old = $node->attrs[$key] ?? null;
            if ($old != $value) {
                $patch->updateAttr($this, $key, $value);
            }
            // compare children
            foreach ($this->children as $i => $child) {
                $child->compare($node->children[$i], $patch);
            }
        }
    }

    public array $attrs = [];
    /**
     * @var Node[] $children
     */
    public array $children = [];

    abstract function tag(): string;

    function renderHtml(): string
    {
        $tag = $this->tag();
        $ret = "<$tag";

        $copy = $this->attrs;
        $copy['path'] = $this->pathStr();
        $copy['key'] = $this->keyStr();

        foreach ($copy as $key => $value) {
            $ret .= " $key=\"$value\"";
        }
        $ret .= ">";
        foreach ($this->children as $child) {
            $ret .= $child->renderHtml();
        }
        $ret .= "</$tag>";
        return $ret;
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, true);
        foreach ($this->children as $i => $child) {
            $child->initialize($this, new PathInfo($i, $i), $manager);
        }
    }

    function finalize(StateManager $manager): void
    {
        foreach ($this->children as $child) {
            $child->finalize($manager);
        }
    }

}

class Div extends HtmlNode
{
    public function __construct($attrs = [], $children = [])
    {
        $this->attrs = $attrs;
        $this->children = $children;
    }

    function tag(): string
    {
        return "div";
    }
}


abstract class Components extends Node
{

    function compare(Node $node, PatchBuilder $patch): void
    {
        if (!$node instanceof Components || get_class($node) != get_class($this)) {
            $patch->replace($this, $node);
            return;
        }

        $this->getNode()->compare($node->getNode(), $patch);
    }

    protected object $state;

    function renderHtml(): string
    {
        return $this->getNode()->renderHtml();
    }

    function initialize(?Node $parent, PathInfo $path, StateManager $manager): void
    {
        $path->set($this, $parent, true);

        $saved = $manager->restoreState($this->keyStr());
        if ($saved != null) {
            $this->restoreState($saved);
        }
        $this->getNode()->initialize($this, new PathInfo(0, get_class($this)), $manager);
    }

    function finalize(StateManager $manager): void
    {
        $manager->saveState($this->keyStr(), $this->saveState());
        $this->getNode()->finalize($manager);
    }

    private Node $node;

    public function getNode(): Node
    {
        $this->node ??= $this->render();
        return $this->node;
    }

    public function saveState(): array
    {
        return get_object_vars($this->state);
    }

    public function restoreState(array $saved): void
    {
        foreach ($saved as $key => $value) {
            $this->state->$key = $value;
        }
    }

    abstract function render(): Node;
}


class HomeComponent extends Components
{
    public function __construct()
    {
        $this->state = new class {
            public string $text = "Vetty nice";
            public bool $active = false;
            public int $counter = 0;

            function inc(): void
            {
                $this->counter += 1;
            }
        };
    }

    function tick(): void
    {
        $this->state->counter = intval(date("s"));
        $this->state->active = $this->state->counter % 2 == 0;
    }

    function render(): Node
    {

        return new Div(["class" => $this->state->active ? "bg-red-200" : "bg-red-800"], [
            new Div(["class" => "text-2xl"], [
                new HtmlText($this->state->text),
            ]),
            new Div(["class" => "font-bold"], [
                new HtmlText("Counter:" . $this->state->counter),
            ]),

            new ForNode(["Coffee", "Code", "Pizza"], fn($item, $index) => $item, fn($item, $index) => new Div([], [new HtmlText("Item: $item")])
            )
        ]);
    }
}

session_start();

$data = $_SESSION['state'] ?? null;

$manager = new StateManager();
$manager->unserialize($data);

$app = new HomeComponent();
$app->initialize(null, new PathInfo(0, get_class($app)), $manager);

$node = $app->getNode();
echo $node->renderHtml();

$app->tick();
$new = $app->render();
$node->compare($new, new PatchBuilder());


$app->finalize($manager);

$_SESSION['state'] = $manager->serialize();

?>

<script lang="js">
    function resolveNode(path) {
        return path.reduce((acc, cur) => acc.childNodes[cur], document.body);
    }
</script>


