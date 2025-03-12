<?php

namespace Spwa\Nodes;

abstract class StatelessComponent extends Component
{
    function hasState(): bool
    {
        return false;
    }
}