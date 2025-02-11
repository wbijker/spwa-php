<?php

namespace Spwa\Nodes;

class RenderContext
{
    /**
     * @template T
     * @param T $state
     * @return T
     */
    public function createState($state)
    {
        return $state;
    }

//    $saved = $manager->restoreState($this->path->keyStr());
//    if ($saved != null) {
//    $this->restoreState($saved);
//    }
}