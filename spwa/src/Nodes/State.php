<?php

namespace Spwa\Nodes;

use Attribute;

interface State
{
    function fromJson(array $json): void;
    function toJson(): array;
}