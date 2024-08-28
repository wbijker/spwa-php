<?php

namespace Spwa\Template;

class PathState
{
    /**
     * @var PathData[]
     */
    private array $dic = [];


    function set(NodePath $path): PathData
    {
        $key = $path->render();
        if (!isset($this->dic[$key])) {
            $this->dic[$key] = new PathData([], null);
        }
        return $this->dic[$key];
    }

    function get(NodePath $path): ?PathData
    {
        return $this->dic[$path->render()];
    }

    function getEvent(NodePath $path, string $event): ?callable
    {
        $data = $this->get(new NodePath([0, 2, 0]));
        if ($data)
            return $data->getEvent('click');
        return null;
    }
}