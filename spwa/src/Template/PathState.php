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
            $this->dic[$key] = new PathData();
        }
        return $this->dic[$key];
    }

    function get(NodePath $path): ?PathData
    {
        return $this->dic[$path->render()];
    }

    function getEvent(NodePath $path, string $event): ?callable
    {
        $data = $this->get($path);
        if ($data)
            return $data->getEvent($event);
        return null;
    }
}