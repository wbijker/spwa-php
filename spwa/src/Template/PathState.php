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

    function saveComponents(): array
    {
        // specifically save the state of the instances (components)
        $ret = [];
        foreach ($this->dic as $key => $data) {
            if ($data->component)
                $ret[] = ['path' => json_decode($key), 'class' => get_class($data->component),  'state' => $data->component->saveState()];
        }
        return $ret;
    }

    function restoreComponents(array $arr)
    {
        foreach ($arr as $value) {
            $component = new $value['class']();
            $component->restoreState($value['state']);
            $this->get(new NodePath($value['path']))->component = $component;
        }
    }
}