<?php

namespace Spwa\Template;

class PathState
{
    /**
     * @var PathData[]
     */
    private array $dic = [];

    function get(NodePath $path): PathData
    {
        $key = $path->render();
        if (!isset($this->dic[$key])) {
            $this->dic[$key] = new PathData();
        }
        return $this->dic[$key];
    }

}