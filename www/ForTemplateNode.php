<?php

class ForTemplateNode extends TemplateNode
{
    public ?array $array;
    /**
     * @var $callback callback
     */
    public $callback;
    /**
     * @var $keyCallback callback
     */
    public $keyCallback;

    /**
     * @var TemplateNode[] $children
     */
    public array $children = [];

    /**
     * @param array|null $array $array
     * @param callback $callback
     */
    public function __construct(?array $array, callable $callback, callable $keyCallback = null)
    {
        $this->array = $array;
        $this->callback = $callback;
        if ($this->array != null)
            $this->children = array_map($callback, $array, array_keys($array));

        // use the provided key or use the default md5 on the serialized data
        $this->keyCallback = $keyCallback ?? fn($item) => md5(json_encode($item));
    }

    function compare(ForTemplateNode $other, &$list)
    {
        // hash each value and compare: delete, insert, move
        $prevHash = array_map($this->keyCallback, $this->array);
        $nextHash = array_map($other->keyCallback, $other->array);

        $diff = lavenshteinDiff($prevHash, $nextHash);
        foreach ($diff as $action) {

            if ($action->action == SKIP || $action->action == REPLACE) {
                // although the key is the same, there might still be differences
                // check for potential updates
                $this->children[$action->i]->compare($other->children[$action->j], $list);
                continue;
            }
            if ($action->action == DELETE) {
                // update index for all children greater than $index
                for ($i = $action->i + 1; $i < count($this->children); $i++) {
                    $this->children[$i]->index--;
                }
                $list[] = ['type' => DELETE_NODE, 'index' => $action->i, 'path' => $this->children[$action->i]->path];
            }
            if ($action->action == INSERT) {
                for ($i = $action->i + 1; $i < count($this->children); $i++) {
                    $this->children[$i]->index++;
                }

                $list[] = ['type' => INSERT_NODE, 'index' => $action->j, 'value' => $other->children[$action->j]->serialize(), 'path' => $this->path];
            }
        }
    }

    function resolve(ResolvedNode $parent): void
    {
        foreach ($this->children as $index => $child) {
            $child->resolve($parent);
        }
    }
}