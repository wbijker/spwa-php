<?php

namespace Spwa\Template;

class PathState
{
    /**
     * @var PathData[]
     */
    private array $dic = [];

    /*
     * Components and their states
     * key is the path of the first node of the component
     * value is the state of the component
     */
    public array $states = [];

    function fillComponent(NodePath $path, $state): void
    {
        $this->states[] = [$path, $state];
    }

    function findState(NodePath $componentPath)
    {
        $max = 0;
        $ret = null;
        foreach ($this->states as $state) {
            [$path, $state] = $state;
            $c = count($path->path);
            if ($componentPath->startsWith($path) && $c > $max) {
                $ret = $state;
                $max = $c;
            }
        }
        return $ret;
    }

    function get(NodePath $path): PathData
    {
        $key = $path->render();
        if (!isset($this->dic[$key])) {
            $this->dic[$key] = new PathData();
        }
        return $this->dic[$key];
    }

}